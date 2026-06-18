# Booking Refunds & Notifications Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add queued email notifications (confirmation + cancellation, to guest and admin), guest self-service refunds (full refund when ≥7 days before check-in), admin refunds (any time, from an in-app booking screen), and Stripe `charge.refunded` reconciliation.

**Architecture:** Refunds flow through one shared service method (`BookingService::cancelAndRefund`) used by both the guest manage-page and the admin detail screen; a separate `reconcileRefund` records refunds initiated directly in the Stripe dashboard (via the `charge.refunded` webhook). All emails are queued Mailables dispatched by event listeners on `BookingConfirmed` / `BookingCancelled`, mirroring the existing `ContactUsMail` pattern.

**Tech Stack:** Laravel 13, Livewire 4, Pest 4, `stripe/stripe-php`, Tailwind v4, `database` queue.

---

## Context the implementer needs

**Divergences from the spec doc (`docs/superpowers/specs/2026-06-15-booking-system-design.md`) — the CODE is the source of truth:**
- **No cleaning fee, no priced add-ons.** Add-ons are *request-only* checkboxes (sauna/jacuzzi, baby crib) snapshotted into `booking_addon` but **never charged**. `grand_total` = nights only. Emails must therefore **list requested add-ons as follow-up items**, not as charges.
- Capacity model is independent caps (`person_count` = max adults, `children_count` = max children).

**Existing pieces to reuse / mirror:**
- `app/Mail/ContactUsMail.php` — Mailable using `Queueable` + `SerializesModels`, with an `envelope()` + `content()` and a Blade view in `resources/views/mail/`. Mirror this, but **also implement `ShouldQueue`** so mail is queued on the `database` connection (`QUEUE_CONNECTION=database`).
- `app/Events/BookingConfirmed.php` — already exists (`public Booking $booking`), already dispatched by `BookingService::confirm()`. It currently has **no listeners** — emails are silent until this plan adds them.
- `app/Services/BookingService.php` — constructor injects `AvailabilityService` + `PricingService`. We will add `StripeService`.
- `app/Services/StripeService.php` — nullable `?StripeClient $client` (null when `STRIPE_SECRET` unset, e.g. tests). Methods guard null with `RuntimeException`.
- `app/Exceptions/BookingException.php` — static factories returning Latvian `__()` messages.
- `app/Enums/BookingStatus.php` — `Pending|Confirmed|Expired|Cancelled` (all already defined).
- `bookings` table already has every refund column: `cancelled_at`, `cancellation_reason`, `refunded_at`, `refund_amount`, `stripe_refund_id`, `notes`, `management_token` (uuid, unique). **No migration is needed.**
- Admin Livewire pattern: `app/Livewire/Admin/Product/ProductList.php` — `boot()` injects services (`FlashMessageService`, `ErrorLogService`), `render()` returns `view(...)->layout('layouts.admin.app')`. Admin routes live in the `auth` + `dashboard` prefix group in `routes/web.php`, registered with `Route::livewire('/path', Component::class)->name('dashboard.x')`.
- `app/Services/FlashMessageService.php` — `success()/error()/info()/warning()` flash to session.
- i18n convention: **Latvian text is the source key in `__()`, `lang/en.json` maps Latvian→English.** Every new Latvian string introduced must get an `en.json` entry in the same task.
- Public booking routes (`booking.success`, `booking.cancel`) are registered **after** `auth.php` and **before** the catch-all `Route::get('/{product}', ...)` in `routes/web.php`. New public booking routes go in the same place. Admin booking routes go inside the `dashboard` group.
- Run tests with `php artisan test --compact --filter=...`. Run `vendor/bin/pint --dirty --format agent` after PHP changes.

**Money formatting:** amounts are integer cents. Format for humans as `number_format($cents / 100, 2, ',', ' ').' €'` (Latvian: comma decimal, space thousands → e.g. `540,00 €`).

---

## File Structure

**Create:**
- `config/booking.php` — admin notification recipient.
- `app/Events/BookingCancelled.php` — event carrying the booking + a `refunded` flag.
- `app/Listeners/SendBookingConfirmationNotifications.php` — sends customer + admin confirmation mail.
- `app/Listeners/SendBookingCancellationNotifications.php` — sends customer + admin cancellation mail.
- `app/Mail/BookingConfirmedCustomerMail.php` + `resources/views/mail/booking/confirmed-customer.blade.php`
- `app/Mail/BookingConfirmedAdminMail.php` + `resources/views/mail/booking/confirmed-admin.blade.php`
- `app/Mail/BookingCancelledCustomerMail.php` + `resources/views/mail/booking/cancelled-customer.blade.php`
- `app/Mail/BookingCancelledAdminMail.php` + `resources/views/mail/booking/cancelled-admin.blade.php`
- `app/Livewire/Booking/ManageBooking.php` + `resources/views/livewire/booking/manage-booking.blade.php`
- `resources/views/booking/manage.blade.php` — public page embedding the Livewire component.
- `app/Livewire/Admin/Booking/BookingList.php` + `resources/views/livewire/admin/booking/booking-list.blade.php`
- `app/Livewire/Admin/Booking/BookingDetail.php` + `resources/views/livewire/admin/booking/booking-detail.blade.php`
- Test files under `tests/Feature/Booking/`.

**Modify:**
- `app/Models/Booking.php` — add `formattedTotal()`, `formattedRefund()`, `isRefundableByGuest()`.
- `app/Services/StripeService.php` — add `createRefund()`.
- `app/Services/BookingService.php` — inject `StripeService`; add `cancelAndRefund()` + `reconcileRefund()`.
- `app/Http/Controllers/StripeWebhookController.php` — handle `charge.refunded`.
- `app/Http/Controllers/BookingController.php` — add `manage()`.
- `app/Providers/AppServiceProvider.php` — register the two event listeners.
- `routes/web.php` — add `booking.manage` (public) + `dashboard.bookings` / `dashboard.booking.detail` (admin).
- `resources/views/layouts/admin/app.blade.php` (or the admin sidebar partial it includes) — add a "Rezervācijas" nav link.
- `lang/en.json` — translations for every new Latvian string.

---

### Task 1: Admin-recipient config

**Files:**
- Create: `config/booking.php`
- Test: `tests/Feature/Booking/BookingConfigTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

it('exposes a booking admin email from config', function () {
    config()->set('booking.admin_email', 'ops@example.com');

    expect(config('booking.admin_email'))->toBe('ops@example.com');
});

it('falls back to the default admin email', function () {
    expect(config('booking.admin_email'))->not->toBeEmpty();
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=BookingConfigTest`
Expected: FAIL — `booking.admin_email` is null (config file missing).

- [ ] **Step 3: Create the config file**

`config/booking.php`:

```php
<?php

return [
    /*
    | Recipient for booking operations emails (new booking, cancellation/refund).
    | Not hardcoded into mailables so it can be changed per environment.
    */
    'admin_email' => env('BOOKING_ADMIN_EMAIL', 'siguldasskati@gmail.com'),
];
```

- [ ] **Step 4: Run the test**

Run: `php artisan test --compact --filter=BookingConfigTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add config/booking.php tests/Feature/Booking/BookingConfigTest.php
git commit -m "feat(booking): add admin notification email config"
```

---

### Task 2: Booking model helpers (money + refund window)

**Files:**
- Modify: `app/Models/Booking.php`
- Test: `tests/Feature/Booking/BookingRefundHelpersTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Booking;
use Carbon\Carbon;

it('formats the grand total in euros', function () {
    $booking = Booking::factory()->make(['grand_total' => 54000]);

    expect($booking->formattedTotal())->toBe('540,00 €');
});

it('formats the refund amount in euros, or a dash when none', function () {
    expect(Booking::factory()->make(['refund_amount' => 12050])->formattedRefund())->toBe('120,50 €')
        ->and(Booking::factory()->make(['refund_amount' => null])->formattedRefund())->toBe('—');
});

it('is guest-refundable only when confirmed and at least 7 days before check-in', function () {
    Carbon::setTestNow('2026-07-01 10:00:00');

    // exactly 7 days out -> allowed
    expect(Booking::factory()->make([
        'status' => \App\Enums\BookingStatus::Confirmed, 'check_in' => '2026-07-08',
    ])->isRefundableByGuest())->toBeTrue();

    // 6 days out -> blocked
    expect(Booking::factory()->make([
        'status' => \App\Enums\BookingStatus::Confirmed, 'check_in' => '2026-07-07',
    ])->isRefundableByGuest())->toBeFalse();

    // not confirmed -> blocked
    expect(Booking::factory()->make([
        'status' => \App\Enums\BookingStatus::Pending, 'check_in' => '2026-08-01',
    ])->isRefundableByGuest())->toBeFalse();

    Carbon::setTestNow();
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=BookingRefundHelpersTest`
Expected: FAIL — `formattedTotal()` undefined.

- [ ] **Step 3: Add the helpers to `app/Models/Booking.php`**

Add `use App\Enums\BookingStatus;` (already imported) and these methods to the class:

```php
public function formattedTotal(): string
{
    return $this->formatCents($this->grand_total);
}

public function formattedRefund(): string
{
    return $this->refund_amount === null ? '—' : $this->formatCents($this->refund_amount);
}

public function isRefundableByGuest(): bool
{
    return $this->status === BookingStatus::Confirmed
        && now()->startOfDay()->lte($this->check_in->copy()->subDays(7)->startOfDay());
}

private function formatCents(int $cents): string
{
    return number_format($cents / 100, 2, ',', ' ').' €';
}
```

- [ ] **Step 4: Run the test**

Run: `php artisan test --compact --filter=BookingRefundHelpersTest`
Expected: PASS

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Models/Booking.php tests/Feature/Booking/BookingRefundHelpersTest.php
git commit -m "feat(booking): add money + guest-refund-window helpers to Booking"
```

---

### Task 3: `BookingCancelled` event

**Files:**
- Create: `app/Events/BookingCancelled.php`
- Test: `tests/Feature/Booking/BookingCancelledEventTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Events\BookingCancelled;
use App\Models\Booking;

it('carries the booking and refunded flag', function () {
    $booking = Booking::factory()->create();

    $event = new BookingCancelled($booking, refunded: true);

    expect($event->booking->is($booking))->toBeTrue()
        ->and($event->refunded)->toBeTrue();
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=BookingCancelledEventTest`
Expected: FAIL — class not found.

- [ ] **Step 3: Create `app/Events/BookingCancelled.php`**

```php
<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCancelled
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Booking $booking,
        public bool $refunded = false,
    ) {}
}
```

- [ ] **Step 4: Run the test**

Run: `php artisan test --compact --filter=BookingCancelledEventTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Events/BookingCancelled.php tests/Feature/Booking/BookingCancelledEventTest.php
git commit -m "feat(booking): add BookingCancelled event"
```

---

### Task 4: `StripeService::createRefund`

**Files:**
- Modify: `app/Services/StripeService.php`
- Test: `tests/Feature/Booking/StripeRefundTest.php`

- [ ] **Step 1: Write the failing test**

(In tests `STRIPE_SECRET` is unset, so `$client` is null — we can only assert the guard here. The real API call is covered by manual/integration testing, and `cancelAndRefund` tests mock this method.)

```php
<?php

use App\Models\Booking;
use App\Services\StripeService;

it('throws when Stripe is not configured', function () {
    $booking = Booking::factory()->create(['stripe_payment_intent_id' => 'pi_123']);

    expect(fn () => app(StripeService::class)->createRefund($booking))
        ->toThrow(RuntimeException::class);
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=StripeRefundTest`
Expected: FAIL — `createRefund()` undefined.

- [ ] **Step 3: Add `createRefund` to `app/Services/StripeService.php`**

Add `use Stripe\Refund;` at the top, then this method:

```php
/**
 * Refund a booking's payment. Passing null refunds the full charge.
 */
public function createRefund(Booking $booking, ?int $amount = null): Refund
{
    if ($this->client === null) {
        throw new \RuntimeException('Stripe is not configured (missing STRIPE_SECRET).');
    }

    $params = ['payment_intent' => (string) $booking->stripe_payment_intent_id];

    if ($amount !== null) {
        $params['amount'] = $amount;
    }

    return $this->client->refunds->create($params, [
        'idempotency_key' => 'refund_'.$booking->reference,
    ]);
}
```

- [ ] **Step 4: Run the test**

Run: `php artisan test --compact --filter=StripeRefundTest`
Expected: PASS

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/StripeService.php tests/Feature/Booking/StripeRefundTest.php
git commit -m "feat(booking): add StripeService::createRefund"
```

---

### Task 5: `BookingService::cancelAndRefund`

**Files:**
- Modify: `app/Services/BookingService.php`
- Test: `tests/Feature/Booking/CancelAndRefundTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Events\BookingCancelled;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\StripeService;
use Illuminate\Support\Facades\Event;
use Stripe\Refund;

it('refunds, cancels, records refund fields, and fires BookingCancelled', function () {
    Event::fake([BookingCancelled::class]);

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_abc',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->once()
            ->andReturn(Refund::constructFrom(['id' => 're_1', 'amount' => 54000]));
    });

    app(BookingService::class)->cancelAndRefund($booking, amount: null, reason: 'Guest request');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled)
        ->and($booking->refund_amount)->toBe(54000)
        ->and($booking->stripe_refund_id)->toBe('re_1')
        ->and($booking->cancellation_reason)->toBe('Guest request')
        ->and($booking->cancelled_at)->not->toBeNull()
        ->and($booking->refunded_at)->not->toBeNull();

    Event::assertDispatched(BookingCancelled::class, fn ($e) => $e->refunded === true);
});

it('is idempotent — a second call does not refund again', function () {
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Cancelled,
        'stripe_payment_intent_id' => 'pi_abc',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->never();
    });

    app(BookingService::class)->cancelAndRefund($booking);

    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=CancelAndRefundTest`
Expected: FAIL — `cancelAndRefund()` undefined.

- [ ] **Step 3: Inject `StripeService` and add the method to `app/Services/BookingService.php`**

Add imports: `use App\Events\BookingCancelled;`. Update the constructor:

```php
public function __construct(
    private readonly AvailabilityService $availability,
    private readonly PricingService $pricing,
    private readonly StripeService $stripe,
) {}
```

Add the method:

```php
/**
 * Refund and cancel a booking. Passing a null amount issues a full refund.
 * Idempotent: a booking already cancelled is left untouched.
 */
public function cancelAndRefund(Booking $booking, ?int $amount = null, ?string $reason = null): void
{
    if ($booking->status === BookingStatus::Cancelled) {
        return;
    }

    $refund = $this->stripe->createRefund($booking, $amount);

    $booking->update([
        'status' => BookingStatus::Cancelled,
        'cancelled_at' => now(),
        'cancellation_reason' => $reason,
        'refunded_at' => now(),
        'refund_amount' => $refund->amount ?? ($amount ?? $booking->grand_total),
        'stripe_refund_id' => $refund->id,
    ]);

    BookingCancelled::dispatch($booking->fresh(), true);
}
```

- [ ] **Step 4: Run the test**

Run: `php artisan test --compact --filter=CancelAndRefundTest`
Expected: PASS

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/BookingService.php tests/Feature/Booking/CancelAndRefundTest.php
git commit -m "feat(booking): add BookingService::cancelAndRefund"
```

---

### Task 6: `BookingService::reconcileRefund` (dashboard-initiated refunds)

**Files:**
- Modify: `app/Services/BookingService.php`
- Test: `tests/Feature/Booking/ReconcileRefundTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Events\BookingCancelled;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\StripeService;
use Illuminate\Support\Facades\Event;

it('records refund state from a webhook without calling Stripe and fires BookingCancelled', function () {
    Event::fake([BookingCancelled::class]);

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_xyz',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->never();
    });

    app(BookingService::class)->reconcileRefund($booking, 54000, 're_dash_1');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled)
        ->and($booking->refund_amount)->toBe(54000)
        ->and($booking->stripe_refund_id)->toBe('re_dash_1');

    Event::assertDispatched(BookingCancelled::class, fn ($e) => $e->refunded === true);
});

it('is idempotent on a redelivered charge.refunded', function () {
    Event::fake([BookingCancelled::class]);

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Cancelled,
        'stripe_payment_intent_id' => 'pi_xyz',
    ]);

    app(BookingService::class)->reconcileRefund($booking, 54000, 're_dash_1');

    Event::assertNotDispatched(BookingCancelled::class);
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=ReconcileRefundTest`
Expected: FAIL — `reconcileRefund()` undefined.

- [ ] **Step 3: Add the method to `app/Services/BookingService.php`**

```php
/**
 * Record a refund that already happened on Stripe's side (e.g. a dashboard
 * refund surfaced via the charge.refunded webhook). Idempotent.
 */
public function reconcileRefund(Booking $booking, int $amountRefunded, ?string $refundId = null): void
{
    if ($booking->status === BookingStatus::Cancelled) {
        return;
    }

    $booking->update([
        'status' => BookingStatus::Cancelled,
        'cancelled_at' => now(),
        'refunded_at' => now(),
        'refund_amount' => $amountRefunded,
        'stripe_refund_id' => $refundId,
    ]);

    BookingCancelled::dispatch($booking->fresh(), true);
}
```

- [ ] **Step 4: Run the test**

Run: `php artisan test --compact --filter=ReconcileRefundTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Services/BookingService.php tests/Feature/Booking/ReconcileRefundTest.php
git commit -m "feat(booking): add BookingService::reconcileRefund for dashboard refunds"
```

---

### Task 7: Confirmation emails (customer + admin) on `BookingConfirmed`

**Files:**
- Create: `app/Mail/BookingConfirmedCustomerMail.php`, `app/Mail/BookingConfirmedAdminMail.php`
- Create: `resources/views/mail/booking/confirmed-customer.blade.php`, `resources/views/mail/booking/confirmed-admin.blade.php`
- Create: `app/Listeners/SendBookingConfirmationNotifications.php`
- Modify: `app/Providers/AppServiceProvider.php`, `lang/en.json`
- Test: `tests/Feature/Booking/BookingConfirmationMailTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Events\BookingConfirmed;
use App\Mail\BookingConfirmedAdminMail;
use App\Mail\BookingConfirmedCustomerMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => config()->set('booking.admin_email', 'ops@example.com'));

it('queues a customer and an admin email when a booking is confirmed', function () {
    Mail::fake();

    $booking = Booking::factory()->create(['guest_email' => 'guest@example.com']);

    event(new BookingConfirmed($booking));

    Mail::assertQueued(BookingConfirmedCustomerMail::class, fn ($mail) => $mail->hasTo('guest@example.com'));
    Mail::assertQueued(BookingConfirmedAdminMail::class, fn ($mail) => $mail->hasTo('ops@example.com'));
});

it('renders the customer confirmation with reference and manage link', function () {
    $booking = Booking::factory()->create(['reference' => 'SS-CONF1']);

    $rendered = (new BookingConfirmedCustomerMail($booking))->render();

    expect($rendered)->toContain('SS-CONF1')
        ->and($rendered)->toContain($booking->management_token);
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=BookingConfirmationMailTest`
Expected: FAIL — mailable classes not found.

- [ ] **Step 3: Create `app/Mail/BookingConfirmedCustomerMail.php`**

```php
<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->booking->guest_email,
            subject: __('Rezervācija apstiprināta').' — '.$this->booking->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking.confirmed-customer',
            with: ['booking' => $this->booking->loadMissing(['product', 'addons'])],
        );
    }
}
```

- [ ] **Step 4: Create `app/Mail/BookingConfirmedAdminMail.php`**

```php
<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: (string) config('booking.admin_email'),
            replyTo: $this->booking->guest_email,
            subject: __('Jauna rezervācija').' — '.$this->booking->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking.confirmed-admin',
            with: ['booking' => $this->booking->loadMissing(['product', 'addons'])],
        );
    }
}
```

- [ ] **Step 5: Create the Blade views**

`resources/views/mail/booking/confirmed-customer.blade.php`:

```blade
<x-mail::message>
# {{ __('Paldies! Rezervācija apstiprināta.') }}

{{ __('Rezervācijas numurs') }}: **{{ $booking->reference }}**

- {{ __('Māja') }}: {{ $booking->product?->getTranslation('title', app()->getLocale()) }}
- {{ __('Reģistrēšanās') }}: {{ $booking->check_in->format('d.m.Y') }}
- {{ __('Izrakstīšanās') }}: {{ $booking->check_out->format('d.m.Y') }}
- {{ __('Viesi') }}: {{ $booking->adults }} + {{ $booking->children }}
- {{ __('Kopā') }}: {{ $booking->formattedTotal() }}

@if ($booking->addons->isNotEmpty())
{{ __('Pieprasītie papildinājumi (sazināsimies par detaļām)') }}:
@foreach ($booking->addons as $addon)
- {{ $addon->pivot->name }}
@endforeach
@endif

<x-mail::button :url="route('booking.manage', ['booking' => $booking->reference, 'token' => $booking->management_token])">
{{ __('Apskatīt rezervāciju') }}
</x-mail::button>

{{ __('Ar cieņu') }},<br>
{{ config('app.name') }}
</x-mail::message>
```

`resources/views/mail/booking/confirmed-admin.blade.php`:

```blade
<x-mail::message>
# {{ __('Jauna rezervācija') }} — {{ $booking->reference }}

- {{ __('Māja') }}: {{ $booking->product?->getTranslation('title', app()->getLocale()) }}
- {{ __('Reģistrēšanās') }}: {{ $booking->check_in->format('d.m.Y') }}
- {{ __('Izrakstīšanās') }}: {{ $booking->check_out->format('d.m.Y') }}
- {{ __('Viesi') }}: {{ $booking->adults }} + {{ $booking->children }}
- {{ __('Kopā') }}: {{ $booking->formattedTotal() }}

{{ __('Viesis') }}: {{ $booking->guest_name }} · {{ $booking->guest_email }} · {{ $booking->guest_phone }}

@if ($booking->addons->isNotEmpty())
{{ __('Pieprasītie papildinājumi') }}:
@foreach ($booking->addons as $addon)
- {{ $addon->pivot->name }}
@endforeach
@endif
</x-mail::message>
```

> **Note on `product->title`:** confirm the `Product` translatable attribute name used for the house name (check `app/Models/Product.php` `$translatable`). If it is not `title`, use the correct key (e.g. `name`). Adjust both Blade views.

- [ ] **Step 6: Create `app/Listeners/SendBookingConfirmationNotifications.php`**

```php
<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Mail\BookingConfirmedAdminMail;
use App\Mail\BookingConfirmedCustomerMail;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationNotifications
{
    public function handle(BookingConfirmed $event): void
    {
        Mail::send(new BookingConfirmedCustomerMail($event->booking));
        Mail::send(new BookingConfirmedAdminMail($event->booking));
    }
}
```

- [ ] **Step 7: Register the listener in `app/Providers/AppServiceProvider.php`**

Add imports and register inside `boot()`:

```php
use App\Events\BookingConfirmed;
use App\Listeners\SendBookingConfirmationNotifications;
use Illuminate\Support\Facades\Event;

// inside boot():
Event::listen(BookingConfirmed::class, SendBookingConfirmationNotifications::class);
```

- [ ] **Step 8: Add `lang/en.json` keys**

Add (merge, keep file valid JSON — don't duplicate existing keys like `Rezervācijas numurs`, `Reģistrēšanās`, `Izrakstīšanās`, `Viesi`, `Kopā`, `Māja`):

```json
"Rezervācija apstiprināta": "Booking confirmed",
"Jauna rezervācija": "New booking",
"Pieprasītie papildinājumi (sazināsimies par detaļām)": "Requested add-ons (we'll be in touch about the details)",
"Pieprasītie papildinājumi": "Requested add-ons",
"Apskatīt rezervāciju": "View booking",
"Ar cieņu": "Kind regards",
"Viesis": "Guest"
```

- [ ] **Step 9: Run the test**

Run: `php artisan test --compact --filter=BookingConfirmationMailTest`
Expected: PASS

- [ ] **Step 10: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Mail/BookingConfirmed*.php resources/views/mail/booking/confirmed-*.blade.php app/Listeners/SendBookingConfirmationNotifications.php app/Providers/AppServiceProvider.php lang/en.json tests/Feature/Booking/BookingConfirmationMailTest.php
git commit -m "feat(booking): queue customer + admin confirmation emails"
```

---

### Task 8: Cancellation emails (customer + admin) on `BookingCancelled`

**Files:**
- Create: `app/Mail/BookingCancelledCustomerMail.php`, `app/Mail/BookingCancelledAdminMail.php`
- Create: `resources/views/mail/booking/cancelled-customer.blade.php`, `resources/views/mail/booking/cancelled-admin.blade.php`
- Create: `app/Listeners/SendBookingCancellationNotifications.php`
- Modify: `app/Providers/AppServiceProvider.php`, `lang/en.json`
- Test: `tests/Feature/Booking/BookingCancellationMailTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Events\BookingCancelled;
use App\Mail\BookingCancelledAdminMail;
use App\Mail\BookingCancelledCustomerMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => config()->set('booking.admin_email', 'ops@example.com'));

it('queues a customer and an admin email when a booking is cancelled', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'guest_email' => 'guest@example.com',
        'refund_amount' => 54000,
    ]);

    event(new BookingCancelled($booking, refunded: true));

    Mail::assertQueued(BookingCancelledCustomerMail::class, fn ($mail) => $mail->hasTo('guest@example.com'));
    Mail::assertQueued(BookingCancelledAdminMail::class, fn ($mail) => $mail->hasTo('ops@example.com'));
});

it('shows the refunded amount in the customer cancellation email', function () {
    $booking = Booking::factory()->create(['reference' => 'SS-CXL1', 'refund_amount' => 54000]);

    $rendered = (new BookingCancelledCustomerMail($booking, refunded: true))->render();

    expect($rendered)->toContain('SS-CXL1')->and($rendered)->toContain('540,00 €');
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=BookingCancellationMailTest`
Expected: FAIL — mailable classes not found.

- [ ] **Step 3: Create `app/Mail/BookingCancelledCustomerMail.php`**

```php
<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public bool $refunded = false) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->booking->guest_email,
            subject: __('Rezervācija atcelta').' — '.$this->booking->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking.cancelled-customer',
            with: ['booking' => $this->booking, 'refunded' => $this->refunded],
        );
    }
}
```

- [ ] **Step 4: Create `app/Mail/BookingCancelledAdminMail.php`**

```php
<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public bool $refunded = false) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: (string) config('booking.admin_email'),
            subject: __('Rezervācija atcelta').' — '.$this->booking->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking.cancelled-admin',
            with: ['booking' => $this->booking, 'refunded' => $this->refunded],
        );
    }
}
```

- [ ] **Step 5: Create the Blade views**

`resources/views/mail/booking/cancelled-customer.blade.php`:

```blade
<x-mail::message>
# {{ __('Rezervācija atcelta') }}

{{ __('Rezervācijas numurs') }}: **{{ $booking->reference }}**

@if ($refunded)
{{ __('Atmaksātā summa') }}: **{{ $booking->formattedRefund() }}**

{{ __('Atmaksa parasti tiek saņemta 5–10 darba dienu laikā.') }}
@else
{{ __('Rezervācija ir atcelta.') }}
@endif

{{ __('Ar cieņu') }},<br>
{{ config('app.name') }}
</x-mail::message>
```

`resources/views/mail/booking/cancelled-admin.blade.php`:

```blade
<x-mail::message>
# {{ __('Rezervācija atcelta') }} — {{ $booking->reference }}

- {{ __('Viesis') }}: {{ $booking->guest_name }} · {{ $booking->guest_email }}
- {{ __('Reģistrēšanās') }}: {{ $booking->check_in->format('d.m.Y') }}
@if ($refunded)
- {{ __('Atmaksātā summa') }}: {{ $booking->formattedRefund() }}
@endif
</x-mail::message>
```

- [ ] **Step 6: Create `app/Listeners/SendBookingCancellationNotifications.php`**

```php
<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Mail\BookingCancelledAdminMail;
use App\Mail\BookingCancelledCustomerMail;
use Illuminate\Support\Facades\Mail;

class SendBookingCancellationNotifications
{
    public function handle(BookingCancelled $event): void
    {
        Mail::send(new BookingCancelledCustomerMail($event->booking, $event->refunded));
        Mail::send(new BookingCancelledAdminMail($event->booking, $event->refunded));
    }
}
```

- [ ] **Step 7: Register the listener in `app/Providers/AppServiceProvider.php` `boot()`**

```php
use App\Events\BookingCancelled;
use App\Listeners\SendBookingCancellationNotifications;

// inside boot():
Event::listen(BookingCancelled::class, SendBookingCancellationNotifications::class);
```

- [ ] **Step 8: Add `lang/en.json` keys**

```json
"Rezervācija atcelta": "Booking cancelled",
"Atmaksātā summa": "Refunded amount",
"Atmaksa parasti tiek saņemta 5–10 darba dienu laikā.": "The refund usually arrives within 5–10 business days.",
"Rezervācija ir atcelta.": "The booking has been cancelled."
```

- [ ] **Step 9: Run the test**

Run: `php artisan test --compact --filter=BookingCancellationMailTest`
Expected: PASS

- [ ] **Step 10: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Mail/BookingCancelled*.php resources/views/mail/booking/cancelled-*.blade.php app/Listeners/SendBookingCancellationNotifications.php app/Providers/AppServiceProvider.php lang/en.json tests/Feature/Booking/BookingCancellationMailTest.php
git commit -m "feat(booking): queue customer + admin cancellation emails"
```

---

### Task 9: `charge.refunded` webhook reconciliation

**Files:**
- Modify: `app/Http/Controllers/StripeWebhookController.php`
- Test: `tests/Feature/Booking/ChargeRefundedWebhookTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\StripeService;
use Stripe\Event;

it('reconciles a dashboard refund on charge.refunded', function () {
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_ref_1',
    ]);

    $event = Event::constructFrom([
        'id' => 'evt_r1',
        'type' => 'charge.refunded',
        'data' => ['object' => [
            'id' => 'ch_1',
            'object' => 'charge',
            'payment_intent' => 'pi_ref_1',
            'amount_refunded' => 54000,
            'refunds' => ['data' => [['id' => 're_hook_1']]],
        ]],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->once()->andReturn($event);
    });

    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake'])->assertOk();

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled)
        ->and($booking->refund_amount)->toBe(54000)
        ->and($booking->stripe_refund_id)->toBe('re_hook_1');
});

it('ignores charge.refunded with no matching payment intent', function () {
    $event = Event::constructFrom([
        'id' => 'evt_r2',
        'type' => 'charge.refunded',
        'data' => ['object' => [
            'id' => 'ch_2', 'object' => 'charge',
            'payment_intent' => 'pi_unknown', 'amount_refunded' => 1000,
            'refunds' => ['data' => []],
        ]],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->once()->andReturn($event);
    });

    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake'])->assertOk();
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=ChargeRefundedWebhookTest`
Expected: FAIL — booking stays `Confirmed` (event type not handled).

- [ ] **Step 3: Handle `charge.refunded` in `app/Http/Controllers/StripeWebhookController.php`**

Add this block after the existing `checkout.session.completed` `if`, before `return response('OK', 200);`:

```php
if ($event->type === 'charge.refunded') {
    $charge = $event->data->object;

    $paymentIntentId = is_string($charge->payment_intent ?? null)
        ? $charge->payment_intent
        : ($charge->payment_intent->id ?? null);

    if ($paymentIntentId === null) {
        return response('OK', 200);
    }

    $booking = Booking::where('stripe_payment_intent_id', $paymentIntentId)->first();

    if ($booking) {
        $refundId = $charge->refunds->data[0]->id ?? null;
        $bookings->reconcileRefund($booking, (int) $charge->amount_refunded, $refundId);
    }
}
```

- [ ] **Step 4: Run the test**

Run: `php artisan test --compact --filter=ChargeRefundedWebhookTest`
Expected: PASS

- [ ] **Step 5: Run the full webhook suite (no regressions)**

Run: `php artisan test --compact --filter=Webhook`
Expected: PASS

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/StripeWebhookController.php tests/Feature/Booking/ChargeRefundedWebhookTest.php
git commit -m "feat(booking): reconcile dashboard refunds via charge.refunded webhook"
```

---

### Task 10: Guest manage-booking page + self-service refund

**Files:**
- Create: `app/Livewire/Booking/ManageBooking.php`, `resources/views/livewire/booking/manage-booking.blade.php`
- Create: `resources/views/booking/manage.blade.php`
- Modify: `app/Http/Controllers/BookingController.php`, `routes/web.php`, `lang/en.json`
- Test: `tests/Feature/Booking/ManageBookingTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Livewire\Booking\ManageBooking;
use App\Models\Booking;
use App\Services\StripeService;
use Carbon\Carbon;
use Livewire\Livewire;
use Stripe\Refund;

it('aborts with 403 on a bad management token', function () {
    $booking = Booking::factory()->create();

    $this->get('/lv/booking/'.$booking->reference.'/manage/'.\Illuminate\Support\Str::uuid())
        ->assertForbidden();
});

it('renders the manage page with a valid token', function () {
    $booking = Booking::factory()->create(['reference' => 'SS-MNG1']);

    $this->get('/lv/booking/'.$booking->reference.'/manage/'.$booking->management_token)
        ->assertOk()
        ->assertSee('SS-MNG1');
});

it('lets the guest refund when at least 7 days before check-in', function () {
    Carbon::setTestNow('2026-07-01 10:00:00');

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-07-20',
        'check_out' => '2026-07-23',
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_mng',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->once()
            ->andReturn(Refund::constructFrom(['id' => 're_mng', 'amount' => 54000]));
    });

    Livewire::test(ManageBooking::class, ['booking' => $booking, 'token' => $booking->management_token])
        ->call('requestRefund');

    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);

    Carbon::setTestNow();
});

it('blocks a refund inside the 7-day window', function () {
    Carbon::setTestNow('2026-07-01 10:00:00');

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-07-05',
        'check_out' => '2026-07-08',
        'stripe_payment_intent_id' => 'pi_mng2',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->never();
    });

    Livewire::test(ManageBooking::class, ['booking' => $booking, 'token' => $booking->management_token])
        ->call('requestRefund');

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);

    Carbon::setTestNow();
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=ManageBookingTest`
Expected: FAIL — route/component missing.

- [ ] **Step 3: Create `app/Livewire/Booking/ManageBooking.php`**

```php
<?php

namespace App\Livewire\Booking;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\View\View;
use Livewire\Component;

class ManageBooking extends Component
{
    public Booking $booking;

    public string $token = '';

    public ?string $message = null;

    public function mount(Booking $booking, string $token): void
    {
        abort_unless(hash_equals($booking->management_token, $token), 403);

        $this->booking = $booking;
        $this->token = $token;
    }

    public function requestRefund(BookingService $bookings): void
    {
        abort_unless(hash_equals($this->booking->management_token, $this->token), 403);

        if (! $this->booking->isRefundableByGuest()) {
            $this->message = __('Atmaksu vairs nevar veikt tiešsaistē. Lūdzu, sazinieties ar mums.');

            return;
        }

        $bookings->cancelAndRefund($this->booking, amount: null, reason: __('Viesa pieprasījums'));
        $this->booking->refresh();
        $this->message = __('Rezervācija atcelta un atmaksa veikta.');
    }

    public function render(): View
    {
        return view('livewire.booking.manage-booking');
    }
}
```

- [ ] **Step 4: Create `resources/views/livewire/booking/manage-booking.blade.php`**

```blade
<div class="space-y-6">
    <div>
        <p class="text-neutral-600">{{ __('Rezervācijas numurs') }}: <strong>{{ $booking->reference }}</strong></p>
        <p class="text-neutral-600">{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</p>
        <p class="text-neutral-600">{{ __('Kopā') }}: {{ $booking->formattedTotal() }}</p>
        <p class="text-neutral-600">{{ __('Statuss') }}: {{ __(ucfirst($booking->status->value)) }}</p>
    </div>

    @if ($message)
        <p class="rounded-lg bg-neutral-100 p-4 text-sm text-neutral-700">{{ $message }}</p>
    @endif

    @if ($booking->status === \App\Enums\BookingStatus::Cancelled)
        <p class="text-neutral-600">{{ __('Rezervācija ir atcelta.') }}
            @if ($booking->refund_amount){{ __('Atmaksātā summa') }}: {{ $booking->formattedRefund() }}@endif
        </p>
    @elseif ($booking->isRefundableByGuest())
        <button type="button" wire:click="requestRefund" wire:loading.attr="disabled"
            class="rounded-full bg-ss-dark px-6 py-3 text-white">
            {{ __('Atcelt rezervāciju un saņemt atmaksu') }}
        </button>
    @else
        <p class="text-sm text-neutral-500">
            {{ __('Bezmaksas atcelšana iespējama līdz 7 dienām pirms ierašanās. Lūdzu, sazinieties ar mums.') }}
        </p>
    @endif
</div>
```

- [ ] **Step 5: Create `resources/views/booking/manage.blade.php`**

```blade
<x-app-layout>
    <div class="mx-auto max-w-xl px-4 py-20">
        <h1 class="mb-8 text-2xl font-semibold text-[#2f3a1f]">{{ __('Jūsu rezervācija') }}</h1>
        @livewire('booking.manage-booking', ['booking' => $booking, 'token' => $token])
    </div>
</x-app-layout>
```

- [ ] **Step 6: Add the `manage()` controller method to `app/Http/Controllers/BookingController.php`**

```php
public function manage(Booking $booking, string $token): View
{
    abort_unless(hash_equals($booking->management_token, $token), 403);

    return view('booking.manage', compact('booking', 'token'));
}
```

- [ ] **Step 7: Register the route in `routes/web.php`**

Add next to the existing `booking.success` / `booking.cancel` routes (before the `/{product}` catch-all):

```php
Route::get('/booking/{booking:reference}/manage/{token}', [\App\Http\Controllers\BookingController::class, 'manage'])->name('booking.manage');
```

- [ ] **Step 8: Add `lang/en.json` keys**

```json
"Jūsu rezervācija": "Your booking",
"Statuss": "Status",
"Confirmed": "Confirmed",
"Pending": "Pending",
"Cancelled": "Cancelled",
"Expired": "Expired",
"Atcelt rezervāciju un saņemt atmaksu": "Cancel booking and get a refund",
"Bezmaksas atcelšana iespējama līdz 7 dienām pirms ierašanās. Lūdzu, sazinieties ar mums.": "Free cancellation is available up to 7 days before arrival. Please contact us.",
"Atmaksu vairs nevar veikt tiešsaistē. Lūdzu, sazinieties ar mums.": "Online refunds are no longer available. Please contact us.",
"Rezervācija atcelta un atmaksa veikta.": "Booking cancelled and refunded.",
"Viesa pieprasījums": "Guest request"
```

- [ ] **Step 9: Run the test**

Run: `php artisan test --compact --filter=ManageBookingTest`
Expected: PASS

- [ ] **Step 10: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Booking/ManageBooking.php resources/views/livewire/booking/manage-booking.blade.php resources/views/booking/manage.blade.php app/Http/Controllers/BookingController.php routes/web.php lang/en.json tests/Feature/Booking/ManageBookingTest.php
git commit -m "feat(booking): guest manage page with 7-day self-service refund"
```

---

### Task 11: Admin booking list

**Files:**
- Create: `app/Livewire/Admin/Booking/BookingList.php`, `resources/views/livewire/admin/booking/booking-list.blade.php`
- Modify: `routes/web.php`, the admin sidebar nav (`resources/views/layouts/admin/app.blade.php` or the partial it includes)
- Test: `tests/Feature/Booking/AdminBookingListTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Booking;
use App\Models\User;

it('requires authentication', function () {
    $this->get('/lv/dashboard/bookings')->assertRedirect();
});

it('lists bookings for an authenticated admin', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create(['reference' => 'SS-ADM1', 'guest_name' => 'Anna Guest']);

    $this->actingAs($user)->get('/lv/dashboard/bookings')
        ->assertOk()
        ->assertSee('SS-ADM1')
        ->assertSee('Anna Guest');
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=AdminBookingListTest`
Expected: FAIL — route missing.

- [ ] **Step 3: Create `app/Livewire/Admin/Booking/BookingList.php`**

```php
<?php

namespace App\Livewire\Admin\Booking;

use App\Models\Booking;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BookingList extends Component
{
    use WithPagination;

    public function render(): View
    {
        $bookings = Booking::query()
            ->with('product')
            ->latest()
            ->paginate(20);

        return view('livewire.admin.booking.booking-list', compact('bookings'))
            ->layout('layouts.admin.app');
    }
}
```

- [ ] **Step 4: Create `resources/views/livewire/admin/booking/booking-list.blade.php`**

```blade
<div class="p-6">
    <h1 class="mb-6 text-2xl font-semibold">{{ __('Rezervācijas') }}</h1>

    <div class="overflow-x-auto rounded-lg border">
        <table class="min-w-full divide-y text-sm">
            <thead class="bg-neutral-50 text-left">
                <tr>
                    <th class="px-4 py-3">{{ __('Numurs') }}</th>
                    <th class="px-4 py-3">{{ __('Viesis') }}</th>
                    <th class="px-4 py-3">{{ __('Datumi') }}</th>
                    <th class="px-4 py-3">{{ __('Kopā') }}</th>
                    <th class="px-4 py-3">{{ __('Statuss') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $booking->reference }}</td>
                        <td class="px-4 py-3">{{ $booking->guest_name }}</td>
                        <td class="px-4 py-3">{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</td>
                        <td class="px-4 py-3">{{ $booking->formattedTotal() }}</td>
                        <td class="px-4 py-3">{{ __(ucfirst($booking->status->value)) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('dashboard.booking.detail', $booking->id) }}" class="text-blue-600 underline">{{ __('Apskatīt') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-neutral-500">{{ __('Nav rezervāciju.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
</div>
```

- [ ] **Step 5: Register the route in `routes/web.php`**

Inside the `dashboard` prefix group (with the other `Route::livewire(...)` admin routes):

```php
Route::livewire('/bookings', \App\Livewire\Admin\Booking\BookingList::class)->name('dashboard.bookings');
Route::livewire('/booking/{booking:id}', \App\Livewire\Admin\Booking\BookingDetail::class)->name('dashboard.booking.detail');
```

> Both admin booking routes are registered here in Task 11 so the list's "Apskatīt" link resolves; the `BookingDetail` class is created in Task 12. If running tasks out of order, Task 12 must be completed before hitting the detail route.

- [ ] **Step 6: Add a sidebar nav link**

In the admin sidebar (`resources/views/layouts/admin/app.blade.php` — locate the existing nav links such as the one pointing to `route('dashboard.products')` and copy its markup), add:

```blade
<a href="{{ route('dashboard.bookings') }}">{{ __('Rezervācijas') }}</a>
```

Match the exact classes/structure of the sibling nav links.

- [ ] **Step 7: Add `lang/en.json` keys**

```json
"Rezervācijas": "Bookings",
"Numurs": "Number",
"Datumi": "Dates",
"Apskatīt": "View",
"Nav rezervāciju.": "No bookings."
```

- [ ] **Step 8: Run the test**

Run: `php artisan test --compact --filter=AdminBookingListTest`
Expected: PASS

- [ ] **Step 9: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Admin/Booking/BookingList.php resources/views/livewire/admin/booking/booking-list.blade.php routes/web.php resources/views/layouts/admin/app.blade.php lang/en.json tests/Feature/Booking/AdminBookingListTest.php
git commit -m "feat(admin): booking list screen"
```

---

### Task 12: Admin booking detail + refund-any-time + notes

**Files:**
- Create: `app/Livewire/Admin/Booking/BookingDetail.php`, `resources/views/livewire/admin/booking/booking-detail.blade.php`
- Modify: `lang/en.json`
- Test: `tests/Feature/Booking/AdminBookingDetailTest.php`

(The route was already registered in Task 11, Step 5.)

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Livewire\Admin\Booking\BookingDetail;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\User;
use App\Services\StripeService;
use Livewire\Livewire;
use Stripe\Refund;

it('shows booking details and requested add-ons to an admin', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create(['reference' => 'SS-DET1']);
    $addon = Addon::factory()->create();
    $booking->addons()->attach($addon->id, [
        'name' => 'Pirts', 'price' => 0, 'pricing_type' => 'per_stay', 'quantity' => 1,
    ]);

    $this->actingAs($user)->get('/lv/dashboard/booking/'.$booking->id)
        ->assertOk()
        ->assertSee('SS-DET1')
        ->assertSee('Pirts');
});

it('lets an admin refund a confirmed booking any time', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => now()->addDay()->toDateString(), // inside the 7-day window — admin can still refund
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_admin',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->once()
            ->andReturn(Refund::constructFrom(['id' => 're_admin', 'amount' => 54000]));
    });

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->set('refundReason', 'Owner cancelled')
        ->call('refund');

    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled)
        ->and($booking->fresh()->cancellation_reason)->toBe('Owner cancelled');
});

it('saves admin notes', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create();

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->set('notes', 'Called the guest, all good')
        ->call('saveNotes');

    expect($booking->fresh()->notes)->toBe('Called the guest, all good');
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=AdminBookingDetailTest`
Expected: FAIL — component missing.

- [ ] **Step 3: Create `app/Livewire/Admin/Booking/BookingDetail.php`**

```php
<?php

namespace App\Livewire\Admin\Booking;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Component;

class BookingDetail extends Component
{
    public Booking $booking;

    public ?string $refundReason = null;

    public ?int $refundAmount = null;

    public ?string $notes = null;

    public function mount(Booking $booking): void
    {
        $this->booking = $booking->load(['product', 'addons']);
        $this->notes = $booking->notes;
    }

    public function refund(BookingService $bookings, FlashMessageService $flash): void
    {
        // Admins may refund at any time. A null amount refunds in full.
        $amount = $this->refundAmount !== null ? (int) $this->refundAmount : null;

        $bookings->cancelAndRefund($this->booking, $amount, $this->refundReason);
        $this->booking->refresh();

        $flash->success(__('Atmaksa veikta.'));
    }

    public function saveNotes(FlashMessageService $flash): void
    {
        $this->booking->update(['notes' => $this->notes]);

        $flash->success(__('Piezīmes saglabātas.'));
    }

    public function render(): View
    {
        return view('livewire.admin.booking.booking-detail')
            ->layout('layouts.admin.app');
    }
}
```

- [ ] **Step 4: Create `resources/views/livewire/admin/booking/booking-detail.blade.php`**

```blade
<div class="max-w-2xl space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ $booking->reference }}</h1>
        <p class="text-neutral-600">{{ __(ucfirst($booking->status->value)) }}</p>
    </div>

    <dl class="grid grid-cols-2 gap-3 text-sm">
        <dt class="font-medium">{{ __('Viesis') }}</dt>
        <dd>{{ $booking->guest_name }} · {{ $booking->guest_email }} · {{ $booking->guest_phone }}</dd>
        <dt class="font-medium">{{ __('Datumi') }}</dt>
        <dd>{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</dd>
        <dt class="font-medium">{{ __('Viesi') }}</dt>
        <dd>{{ $booking->adults }} + {{ $booking->children }}</dd>
        <dt class="font-medium">{{ __('Kopā') }}</dt>
        <dd>{{ $booking->formattedTotal() }}</dd>
        @if ($booking->refund_amount)
            <dt class="font-medium">{{ __('Atmaksātā summa') }}</dt>
            <dd>{{ $booking->formattedRefund() }}</dd>
        @endif
    </dl>

    @if ($booking->addons->isNotEmpty())
        <div>
            <h2 class="mb-2 font-medium">{{ __('Pieprasītie papildinājumi') }}</h2>
            <ul class="list-disc pl-5 text-sm">
                @foreach ($booking->addons as $addon)
                    <li>{{ $addon->pivot->name }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <h2 class="mb-2 font-medium">{{ __('Piezīmes') }}</h2>
        <textarea wire:model="notes" rows="3" class="w-full rounded-lg border p-2"></textarea>
        <button type="button" wire:click="saveNotes" class="mt-2 rounded-full bg-neutral-800 px-5 py-2 text-white">{{ __('Saglabāt piezīmes') }}</button>
    </div>

    @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
        <div class="rounded-lg border border-red-200 p-4">
            <h2 class="mb-2 font-medium text-red-700">{{ __('Atmaksa') }}</h2>
            <input type="text" wire:model="refundReason" placeholder="{{ __('Iemesls') }}" class="mb-2 w-full rounded-lg border p-2" />
            <button type="button" wire:click="refund" wire:confirm="{{ __('Vai tiešām veikt atmaksu?') }}"
                class="rounded-full bg-red-600 px-5 py-2 text-white">
                {{ __('Veikt pilnu atmaksu') }}
            </button>
        </div>
    @endif
</div>
```

- [ ] **Step 5: Add `lang/en.json` keys**

```json
"Piezīmes": "Notes",
"Saglabāt piezīmes": "Save notes",
"Piezīmes saglabātas.": "Notes saved.",
"Atmaksa": "Refund",
"Iemesls": "Reason",
"Veikt pilnu atmaksu": "Issue full refund",
"Vai tiešām veikt atmaksu?": "Really issue a refund?",
"Atmaksa veikta.": "Refund issued."
```

- [ ] **Step 6: Run the test**

Run: `php artisan test --compact --filter=AdminBookingDetailTest`
Expected: PASS

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Admin/Booking/BookingDetail.php resources/views/livewire/admin/booking/booking-detail.blade.php lang/en.json tests/Feature/Booking/AdminBookingDetailTest.php
git commit -m "feat(admin): booking detail with refund-any-time and notes"
```

---

### Task 13: Full-suite verification

**Files:** none (verification only)

- [ ] **Step 1: Run the entire booking suite**

Run: `php artisan test --compact --filter=Booking`
Expected: PASS (all booking feature tests green).

- [ ] **Step 2: Run the full test suite**

Run: `php artisan test --compact`
Expected: PASS (no regressions across the app).

- [ ] **Step 3: Confirm Pint is clean**

Run: `vendor/bin/pint --dirty --format agent`
Expected: no files changed (already formatted).

- [ ] **Step 4: Validate `lang/en.json` is well-formed**

Run: `php -r 'json_decode(file_get_contents("lang/en.json"), true, 512, JSON_THROW_ON_ERROR); echo "valid\n";'`
Expected: prints `valid`.

---

## Self-Review

**Spec coverage (spec §5 Refund/cancellation, §6 Notifications):**
- §5.1 Guest self-service refund (≥7 days, full, instant, fires `BookingCancelled`) → Task 10 (+ helper Task 2, service Task 5).
- §5.1 Inside-window block → Task 10 (`isRefundableByGuest` false branch).
- §5.2 Admin refund any time, with reason → Task 12 (+ service Task 5).
- §5.3 `charge.refunded` webhook sync (dashboard refunds) → Task 9 (+ service Task 6).
- §6 `BookingConfirmed` → customer + admin email → Task 7.
- §6 `BookingCancelled` → customer (with refund status) + admin email → Task 8.
- §6 Admin recipient from config, strings translatable → Task 1 + per-task `en.json` edits.
- Admin booking UI (list/detail/notes) → Tasks 11–12.

**Divergences honored:** add-ons listed as *requests* (not charges) in Tasks 7/12; `grand_total` = nights only; no cleaning fee referenced anywhere.

**Out of scope (deferred, consistent with spec phasing):** `BookingExpired` email (spec marks it silent housekeeping — no task, intentional); admin pricing-calendar / add-ons / blocked-dates editors (separate future plan); SMS; iCal sync.

**Type/name consistency:** `cancelAndRefund(Booking, ?int $amount, ?string $reason)`, `reconcileRefund(Booking, int $amountRefunded, ?string $refundId)`, `createRefund(Booking, ?int $amount): Refund`, `isRefundableByGuest(): bool`, `formattedTotal()/formattedRefund()`, route names `booking.manage` / `dashboard.bookings` / `dashboard.booking.detail`, config key `booking.admin_email` — all used consistently across tasks.

**Known check for the implementer:** verify the `Product` translatable house-name attribute (`title` vs `name`) before finalizing the mail Blades in Task 7 (noted inline).
