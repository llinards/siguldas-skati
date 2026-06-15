# Booking System with Stripe Payments — Design

**Date:** 2026-06-15
**Status:** Approved (design phase)
**Scope:** v1 — on-site direct booking of houses (`Product`) with full upfront payment via Stripe Checkout, guest checkout (no account), per-date pricing, priced add-ons, refunds, and email notifications.

---

## 1. Summary

Guests book a house directly on the site, pay the full amount upfront through Stripe's hosted Checkout, and receive instant confirmation. The on-site database is the single source of truth for availability. Bookings can be refunded: guests self-refund up to 7 days before check-in; admins refund at any time. All key events trigger queued email notifications to both the customer and the admin.

This replaces the current behaviour where the "booking" link redirects to an external booking.com page.

### Locked decisions

- **Payment:** full amount upfront, instant confirmation.
- **Availability source of truth:** on-site only (+ admin date-blocking). No external/iCal sync in v1.
- **Pricing:** per-date pricing (seasonal/weekend) with a per-house `base_price` fallback; optional per-house `cleaning_fee`; per-house `min_nights`.
- **Add-ons:** priced, charged at checkout.
- **Guest identity:** guest checkout, no account. Guests manage a booking via a tokenised link.
- **Stripe integration:** Stripe Checkout (hosted redirect) via the official `stripe/stripe-php` SDK. **Not** Laravel Cashier (subscription-oriented, wrong fit).
- **Double-booking safety:** pending-hold + webhook confirmation (Approach A).
- **Refunds:** customer self-refund (full) only when `today <= check_in - 7 days`; admin refund any time. Auto-refund instantly within the window (no admin approval step).
- **Notifications:** email only, sent as queued Mailables (`database` queue), via Laravel events → listeners.
- **Entry point (v1):** product-page booking widget only. The standalone 3-step wizard is deferred.

### Out of scope (v1)

Standalone 3-step booking wizard; SMS/other notification channels; channel-manager / iCal sync with booking.com; guest user accounts and "my bookings" history.

---

## 2. Existing context

- Laravel 13, Livewire 4, Tailwind v4, multilingual via `mcamara/laravel-localization` + `spatie/laravel-translatable`.
- `Product` model = the houses. Has `person_count`, `children_count`, translatable `pricelist` (marketing text — **unrelated** to booking prices, left untouched), `features`, `rules`, `images`.
- Admin area is a set of Livewire CRUD components under `app/Livewire/Admin/**` behind `auth` middleware, following a consistent List/Add/Edit pattern.
- `QUEUE_CONNECTION=database`, `MAIL_MAILER=smtp`, `MAIL_FROM_ADDRESS=info@siguldasskati.lv`. Existing `app/Mail/ContactUsMail.php` uses the `Queueable` trait — the pattern to mirror.
- Routes are wrapped in a localization group in `routes/web.php`; the catch-all `/{product}` show route is registered **last** — new booking routes must be registered before it to avoid being swallowed.
- No Stripe or Cashier currently installed.

---

## 3. Data model

All monetary values are stored as **integer cents** (avoids float rounding; Stripe expects the smallest currency unit). Currency is `eur`.

### 3.1 `products` (new columns)

| Column | Type | Notes |
|---|---|---|
| `base_price` | unsigned int (cents) | Fallback nightly price for any date without an explicit `product_prices` row. |
| `cleaning_fee` | unsigned int (cents), default `0` | Flat per-booking fee. |
| `min_nights` | unsigned int, default `1` | Minimum stay length. |

### 3.2 `product_prices`

Per-date overrides; only stored for dates that differ from `base_price`.

| Column | Type | Notes |
|---|---|---|
| `id` | id | |
| `product_id` | FK → products | cascade on delete |
| `date` | date | unique together with `product_id` |
| `price` | unsigned int (cents) | nightly price for that date |
| timestamps | | |

### 3.3 `addons`

Per-house priced extras (sauna/jacuzzi, baby crib, …).

| Column | Type | Notes |
|---|---|---|
| `id` | id | |
| `product_id` | FK → products | cascade on delete |
| `name` | json (translatable) | |
| `price` | unsigned int (cents) | |
| `pricing_type` | string enum: `per_stay` \| `per_night` | |
| `is_active` | bool, default `true` | |
| `order` | int, default `0` | |
| timestamps | | |

### 3.4 `bookings`

| Column | Type | Notes |
|---|---|---|
| `id` | id | |
| `product_id` | FK → products | restrict on delete |
| `reference` | string, unique | human code, e.g. `SS-7K2P9` |
| `check_in` | date | inclusive |
| `check_out` | date | exclusive (guest departs that morning) |
| `adults` | unsigned int | validated vs `product.person_count` |
| `children` | unsigned int, default `0` | validated vs `product.children_count` |
| `guest_name` | string | |
| `guest_email` | string | |
| `guest_phone` | string | |
| `nights_total` | unsigned int (cents) | sum of per-night prices |
| `cleaning_fee` | unsigned int (cents) | snapshot of product fee at booking time |
| `addons_total` | unsigned int (cents) | sum of selected add-ons |
| `grand_total` | unsigned int (cents) | `nights_total + cleaning_fee + addons_total` |
| `currency` | string, default `eur` | |
| `status` | string enum: `pending` \| `confirmed` \| `expired` \| `cancelled` | |
| `expires_at` | timestamp, nullable | hold deadline while `pending` |
| `management_token` | uuid, unique | guest access without an account |
| `stripe_session_id` | string, nullable | |
| `stripe_payment_intent_id` | string, nullable | |
| `cancelled_at` | timestamp, nullable | |
| `cancellation_reason` | string, nullable | |
| `refunded_at` | timestamp, nullable | |
| `refund_amount` | unsigned int (cents), nullable | |
| `stripe_refund_id` | string, nullable | |
| `notes` | text, nullable | admin notes |
| timestamps | | |

**Status semantics:** a `cancelled` booking may or may not carry a refund — refund state is tracked by its own (`refunded_at`, `refund_amount`, `stripe_refund_id`) fields, not overloaded onto `status`.

### 3.5 `booking_addon` (snapshot pivot)

Snapshots price/name at booking time so later add-on edits never rewrite history.

| Column | Type | Notes |
|---|---|---|
| `booking_id` | FK → bookings | cascade on delete |
| `addon_id` | FK → addons | nullable on delete (set null) |
| `name` | string | snapshot |
| `price` | unsigned int (cents) | snapshot |
| `pricing_type` | string | snapshot |
| `quantity` | unsigned int, default `1` | |

### 3.6 `blocked_dates`

Admin-blocked ranges (maintenance, off-site bookings).

| Column | Type | Notes |
|---|---|---|
| `id` | id | |
| `product_id` | FK → products | cascade on delete |
| `start_date` | date | inclusive |
| `end_date` | date | inclusive |
| `reason` | string, nullable | |
| timestamps | | |

### 3.7 Availability rule

A date range `[check_in, check_out)` is available for a product when **none** of the following overlap it:
- a `confirmed` booking,
- a `pending` booking whose `expires_at` is in the future,
- a `blocked_dates` range.

Overlap test for two half-open ranges: `existing.check_in < requested.check_out AND existing.check_out > requested.check_in`. `blocked_dates` are inclusive ranges and are converted to the half-open convention when compared.

---

## 4. Booking flow

### 4.1 Happy path

1. **Booking widget** (Livewire, on the house page): guest selects check-in/check-out, guest counts, and add-ons. The widget computes a live quote (per-date nights + cleaning fee + add-ons) and validates availability, capacity, and `min_nights` reactively.
2. Guest enters name / email / phone and clicks **Rezervēt**.
3. Server, inside a DB transaction with row locking on the relevant product rows:
   - re-validate availability, capacity, and min-nights (never trust the client),
   - recompute the quote server-side,
   - create a `pending` booking with `expires_at = now + 20 minutes`, a unique `reference`, and a `management_token`,
   - snapshot selected add-ons into `booking_addon`,
   - create a Stripe **Checkout Session** with one line item per cost component (nights, cleaning fee, each add-on), `metadata.booking_id`, `success_url`, `cancel_url`, and an `expires_at` aligned with the hold,
   - redirect the guest to the Stripe-hosted page.
4. Guest pays on Stripe's hosted Checkout page (Stripe handles cards, Apple/Google Pay, 3DS/SCA).
5. **Webhook** `checkout.session.completed` (idempotent): mark the booking `confirmed`, store `stripe_payment_intent_id`, fire `BookingConfirmed`. Listeners queue the customer confirmation email (reference, dates, total, manage link) and the admin "new booking" email.
6. `success_url` lands on a confirmation page that renders from the **booking status in the DB** — it never trusts the redirect itself as proof of payment.
7. Abandonment / expiry: the `bookings:release-expired` scheduled command (every minute) flips `pending` bookings past `expires_at` to `expired`, freeing the dates. The Stripe session also expires on its side.

### 4.2 Concurrency / double-booking

The pending-hold reserves the dates the instant the guest is sent to Stripe, so a second guest cannot reach Checkout for the same range. Final confirmation is driven solely by the webhook — the browser redirect is never the source of truth. The availability re-check inside the locked transaction (step 3) closes the race between two simultaneous "Rezervēt" clicks.

---

## 5. Refund / cancellation flow

### 5.1 Guest self-service

- Manage page at `/booking/{reference}/{token}` (Livewire `Booking/ManageBooking`).
- If the booking is `confirmed` **and** `today <= check_in - 7 days`: a **Request refund** action issues an immediate **full** Stripe refund against the original payment intent, sets `status = cancelled` with `cancelled_at`, populates the refund fields, and fires `BookingCancelled(refunded: true)`.
- Inside the 7-day window: the action is disabled with an explanation (contact the host).

### 5.2 Admin

- From the admin booking detail, a **Refund** action available **at any time** (full or a specified amount), with a logged reason → same internal cancel/refund path.

### 5.3 Webhook sync

- `charge.refunded` (idempotent) reconciles refund state, including refunds an admin performs directly in the Stripe dashboard.

---

## 6. Notifications

Laravel **events → listeners → queued Mailables**. Mailables implement `ShouldQueue` and use the `database` queue, mirroring `app/Mail/ContactUsMail.php`.

| Event | Customer email | Admin email |
|---|---|---|
| `BookingConfirmed` | Confirmation: reference, house, dates, total, manage link | "New booking" notice |
| `BookingCancelled` | Cancellation + refund status | Cancellation/refund notice |
| `BookingExpired` | — (silent housekeeping) | — |

The admin recipient address comes from config / site settings (not hardcoded). All booking-related strings are translatable, consistent with the rest of the site.

---

## 7. Components & files

### Public
- `app/Livewire/Booking/BookingWidget.php` — date/guest/add-on selection, live quote, live availability/capacity/min-nights validation, kicks off checkout.
- `app/Livewire/Booking/ManageBooking.php` — guest cancel/refund via `reference` + `management_token`.
- `app/Http/Controllers/BookingController.php` — `checkout` (create pending booking + Stripe session + redirect), `success`, `cancel`.
- `app/Http/Controllers/StripeWebhookController.php` — verifies signature, dispatches handlers for `checkout.session.completed` and `charge.refunded`.

### Services
- `app/Services/BookingService.php` — availability checks, create-pending, confirm, cancel/refund orchestration.
- `app/Services/PricingService.php` — quote builder from `product_prices` / `base_price` + cleaning fee + add-ons.
- `app/Services/StripeService.php` — thin wrapper over `stripe/stripe-php` (create Checkout Session, create refund, verify webhook). Swappable/fakeable in tests.

### Admin (Livewire, following existing `Admin/**` pattern)
- `Admin/Booking/BookingList.php`, `Admin/Booking/BookingDetail.php` (view, cancel/refund, notes).
- `Admin/Product/ProductPricing.php` — per-date price-calendar editor (sets `base_price` + `product_prices`).
- `Admin/Product/ProductAddons.php` — manage `addons`.
- `Admin/Product/BlockedDates.php` — manage `blocked_dates`.

### Models
- `Booking`, `Addon`, `ProductPrice`, `BlockedDate` (+ relations on `Product`). Factories + seeders for each.

### Console
- `app/Console/Commands/ReleaseExpiredBookings.php` (`bookings:release-expired`), scheduled every minute.

### Config
- `config/services.php` Stripe block; `.env`: `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`.

### Routes
New booking routes registered **before** the catch-all `/{product}` route, inside the existing localization group.

### Dependency
- Add `stripe/stripe-php` via Composer (approved).

---

## 8. Testing (Pest)

- **Availability:** overlap detection across confirmed / non-expired-pending / blocked ranges; two simultaneous pendings cannot both hold the same dates.
- **Pricing:** quote from `base_price` only, from mixed `product_prices` + fallback, plus cleaning fee and add-ons (`per_stay` vs `per_night`).
- **Hold expiry:** `bookings:release-expired` flips only expired pendings and frees dates.
- **Webhook:** `checkout.session.completed` confirms a pending booking and is idempotent on redelivery; ignores unrelated/unknown bookings.
- **Refund window:** guest refund allowed at exactly `check_in - 7 days`, blocked inside the window; admin refund allowed any time.
- **Validation:** capacity (adults/children vs product limits) and `min_nights` enforced server-side.
- **Notifications:** `Mail::fake()` / `Queue::fake()` assert the right queued mailables on confirm/cancel.
- `StripeService` is faked so no network calls occur in tests.

---

## 9. Phasing notes

v1 delivers the product-page widget end-to-end (select → pay → confirm → notify → refund) plus the admin tooling needed to operate it (pricing calendar, add-ons, blocked dates, booking management). The standalone 3-step wizard, SMS, and external calendar sync are explicitly deferred to later iterations and share the same data model and services.
