# Booking System — Plan 2: Booking + Stripe Checkout Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Turn the foundation into a working booking flow: a guest picks dates/guests/add-ons on the house page, is sent to Stripe-hosted Checkout, pays, and the booking is confirmed by webhook — with a pending-hold that auto-expires.

**Architecture:** A Livewire `BookingWidget` collects input and shows a live quote (reusing `PricingService` + `AvailabilityService`). On submit, `BookingService::createPendingBooking()` validates availability/capacity/min-nights inside a locked transaction and writes a `pending` booking with a 20-minute hold; `StripeService` creates a Checkout Session and the guest is redirected to Stripe. Stripe's webhook (`checkout.session.completed`) drives confirmation via `BookingService::confirm()` — never the browser redirect. A scheduled command releases expired holds. Confirmation fires a `BookingConfirmed` event (listeners/emails land in Plan 3).

**Tech Stack:** Laravel 13, PHP 8.3, Livewire 4, Pest 4, `stripe/stripe-php` (latest), Stripe Checkout (hosted), dynamic payment methods.

**Source spec:** `docs/superpowers/specs/2026-06-15-booking-system-design.md` (§4 booking flow, §3 data model). Plan 1 (foundation) is complete and merged on `development`.

**Stripe best-practices baked in:** use Checkout Sessions (hosted), do not hardcode `payment_method_types` (rely on dashboard dynamic payment methods), verify webhook signatures, use an idempotency key on session creation, treat the webhook (not the redirect) as the source of truth for "paid".

---

## File Structure

**Create:**
- `app/Exceptions/BookingException.php` — domain exception (unavailable / capacity / min-nights).
- `app/Services/BookingService.php` — create-pending + confirm orchestration.
- `app/Services/StripeService.php` — Checkout Session + webhook verification + line-item builder.
- `app/Events/BookingConfirmed.php` — fired on confirmation (consumed in Plan 3).
- `app/Http/Controllers/StripeWebhookController.php` — webhook endpoint.
- `app/Http/Controllers/BookingController.php` — `success` / `cancel` pages.
- `app/Console/Commands/ReleaseExpiredBookings.php` — `bookings:release-expired`.
- `app/Livewire/Booking/BookingWidget.php` + `resources/views/livewire/booking/booking-widget.blade.php`.
- `resources/views/booking/success.blade.php`, `resources/views/booking/cancel.blade.php`.

**Modify:**
- `config/services.php` (stripe block), `.env`, `.env.example`.
- `app/Models/Booking.php` (reference generator helper).
- `routes/web.php` (webhook + success/cancel routes), `routes/console.php` (schedule), `bootstrap/app.php` (CSRF exempt webhook).
- `resources/views/product.blade.php` (mount the widget).

**Tests:** under `tests/Feature/Booking/`.

---

## Task 1: Add Stripe SDK + config

**Files:**
- Modify: `config/services.php`, `.env`, `.env.example`
- Command: `composer require stripe/stripe-php`

- [ ] **Step 1: Install the SDK**

Run: `composer require stripe/stripe-php`
Expected: package installed, `composer.json` + lock updated.

- [ ] **Step 2: Add the stripe config block**

In `config/services.php`, add before the closing `];`:
```php
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
```

- [ ] **Step 3: Add env keys**

Append to `.env` and `.env.example` (use real test keys only in `.env`, leave `.env.example` blank):
```
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
```

- [ ] **Step 4: Verify config resolves**

Run: `php artisan config:show services.stripe`
Expected: shows `key`, `secret`, `webhook_secret` entries (values may be null locally).

- [ ] **Step 5: Commit**

```bash
git add composer.json composer.lock config/services.php .env.example
git commit -m "chore(booking): add stripe/stripe-php and stripe config

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```
(Do not commit `.env`.)

---

## Task 2: `BookingException` + `Booking::generateReference()`

**Files:**
- Create: `app/Exceptions/BookingException.php`
- Modify: `app/Models/Booking.php`
- Test: `tests/Feature/Booking/BookingReferenceTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Booking;

it('generates a unique SS-prefixed reference', function () {
    $ref = Booking::generateReference();

    expect($ref)->toStartWith('SS-')
        ->and(strlen($ref))->toBe(8); // SS- + 5 chars
});

it('does not collide with an existing reference', function () {
    $existing = Booking::factory()->create(['reference' => 'SS-AAAAA']);

    // Force the first random draw to equal the existing one, then succeed on retry.
    $ref = Booking::generateReference();

    expect($ref)->not->toBe($existing->reference)
        ->and(Booking::where('reference', $ref)->exists())->toBeFalse();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=BookingReferenceTest`
Expected: FAIL — `Call to undefined method ...generateReference()`.

- [ ] **Step 3: Implement the exception and helper**

`app/Exceptions/BookingException.php`:
```php
<?php

namespace App\Exceptions;

use RuntimeException;

class BookingException extends RuntimeException
{
    public static function datesUnavailable(): self
    {
        return new self('The selected dates are no longer available.');
    }

    public static function exceedsCapacity(int $max): self
    {
        return new self("This house accommodates up to {$max} guests.");
    }

    public static function belowMinimumNights(int $min): self
    {
        return new self("The minimum stay is {$min} night(s).");
    }
}
```

Add to `app/Models/Booking.php` (import `Illuminate\Support\Str;`):
```php
    public static function generateReference(): string
    {
        do {
            $reference = 'SS-'.Str::upper(Str::random(5));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=BookingReferenceTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Exceptions/BookingException.php app/Models/Booking.php tests/Feature/Booking/BookingReferenceTest.php
git commit -m "feat(booking): add BookingException and unique reference generator

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Task 3: `BookingService::createPendingBooking()`

Validates availability (via `AvailabilityService`), capacity (`adults + children <= product.person_count`, `adults >= 1`), and minimum nights (`nights >= product.min_nights`), all inside a `DB::transaction` that locks the product row to serialize concurrent attempts. Computes the quote with `PricingService`, writes a `pending` booking with `expires_at = now()+20min`, a unique reference and `management_token`, and snapshots the selected add-ons into the pivot.

**Files:**
- Create: `app/Services/BookingService.php`
- Test: `tests/Feature/Booking/CreatePendingBookingTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\AddonPricingType;
use App\Enums\BookingStatus;
use App\Exceptions\BookingException;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Product;
use App\Services\BookingService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = app(BookingService::class);
    $this->product = Product::factory()->create([
        'base_price' => 10000,
        'cleaning_fee' => 3000,
        'min_nights' => 2,
        'person_count' => 4,
    ]);
    $this->guest = ['name' => 'Jane Guest', 'email' => 'jane@example.com', 'phone' => '+37120000000'];
});

it('creates a pending booking with a hold, reference, token and totals', function () {
    $booking = $this->service->createPendingBooking(
        $this->product,
        Carbon::parse('2026-08-01'),
        Carbon::parse('2026-08-04'),
        2, 1, [], $this->guest,
    );

    expect($booking->status)->toBe(BookingStatus::Pending)
        ->and($booking->expires_at)->not->toBeNull()
        ->and($booking->expires_at->isFuture())->toBeTrue()
        ->and($booking->reference)->toStartWith('SS-')
        ->and($booking->management_token)->not->toBeEmpty()
        ->and($booking->nights_total)->toBe(30000) // 3 nights x 10000
        ->and($booking->cleaning_fee)->toBe(3000)
        ->and($booking->grand_total)->toBe(33000)
        ->and($booking->guest_email)->toBe('jane@example.com');
});

it('snapshots selected add-ons onto the booking', function () {
    $sauna = Addon::factory()->for($this->product)->create([
        'name' => ['lv' => 'Pirts', 'en' => 'Sauna'], 'price' => 7000, 'pricing_type' => AddonPricingType::PerStay,
    ]);

    $booking = $this->service->createPendingBooking(
        $this->product,
        Carbon::parse('2026-08-01'),
        Carbon::parse('2026-08-03'),
        2, 0,
        [['addon' => $sauna, 'quantity' => 1]],
        $this->guest,
    );

    expect($booking->addons_total)->toBe(7000)
        ->and($booking->grand_total)->toBe(27000) // 2x10000 + 3000 + 7000
        ->and($booking->addons)->toHaveCount(1)
        ->and($booking->addons->first()->pivot->name)->toBe('Sauna');
});

it('rejects unavailable dates', function () {
    Booking::factory()->for($this->product)->create([
        'status' => BookingStatus::Confirmed, 'check_in' => '2026-08-01', 'check_out' => '2026-08-05',
    ]);

    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-02'), Carbon::parse('2026-08-04'), 2, 0, [], $this->guest,
    );
})->throws(BookingException::class);

it('rejects stays below the minimum nights', function () {
    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-02'), 2, 0, [], $this->guest,
    );
})->throws(BookingException::class);

it('rejects guest counts above capacity', function () {
    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 4, 2, [], $this->guest,
    );
})->throws(BookingException::class);
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=CreatePendingBookingTest`
Expected: FAIL — `Class "App\Services\BookingService" not found`.

- [ ] **Step 3: Implement the service**

`app/Services/BookingService.php`:
```php
<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Events\BookingConfirmed;
use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Product;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Minutes a pending hold is kept before it is released.
     */
    public const HOLD_MINUTES = 20;

    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly PricingService $pricing,
    ) {}

    /**
     * Create a pending booking that holds the dates while the guest pays.
     *
     * @param  array<int, array{addon: \App\Models\Addon, quantity: int}>  $addonSelections
     * @param  array{name: string, email: string, phone: string}  $guest
     */
    public function createPendingBooking(
        Product $product,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        int $adults,
        int $children,
        array $addonSelections,
        array $guest,
    ): Booking {
        return DB::transaction(function () use ($product, $checkIn, $checkOut, $adults, $children, $addonSelections, $guest) {
            // Lock the product row to serialize concurrent booking attempts for this house.
            $product = Product::whereKey($product->getKey())->lockForUpdate()->firstOrFail();

            $this->assertGuestCount($product, $adults, $children);

            if (! $this->availability->isAvailable($product, $checkIn, $checkOut)) {
                throw BookingException::datesUnavailable();
            }

            $quote = $this->pricing->quote($product, $checkIn, $checkOut, $addonSelections);

            if ($quote->nights < $product->min_nights) {
                throw BookingException::belowMinimumNights($product->min_nights);
            }

            $booking = $product->bookings()->create([
                'reference' => Booking::generateReference(),
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'adults' => $adults,
                'children' => $children,
                'guest_name' => $guest['name'],
                'guest_email' => $guest['email'],
                'guest_phone' => $guest['phone'],
                'nights_total' => $quote->nightsTotal,
                'cleaning_fee' => $quote->cleaningFee,
                'addons_total' => $quote->addonsTotal,
                'grand_total' => $quote->grandTotal,
                'currency' => 'eur',
                'status' => BookingStatus::Pending,
                'expires_at' => now()->addMinutes(self::HOLD_MINUTES),
                'management_token' => (string) Str::uuid(),
            ]);

            foreach ($quote->addonLines as $line) {
                $booking->addons()->attach($line['addon_id'], [
                    'name' => $line['name'],
                    'price' => $line['price'],
                    'pricing_type' => $line['pricing_type'],
                    'quantity' => $line['quantity'],
                ]);
            }

            return $booking;
        });
    }

    /**
     * Confirm a booking once payment has succeeded. Idempotent.
     */
    public function confirm(Booking $booking, ?string $paymentIntentId = null): void
    {
        if ($booking->status === BookingStatus::Confirmed) {
            return;
        }

        $booking->update([
            'status' => BookingStatus::Confirmed,
            'stripe_payment_intent_id' => $paymentIntentId,
            'expires_at' => null,
        ]);

        BookingConfirmed::dispatch($booking->fresh());
    }

    private function assertGuestCount(Product $product, int $adults, int $children): void
    {
        if ($adults < 1 || ($adults + $children) > $product->person_count) {
            throw BookingException::exceedsCapacity($product->person_count);
        }
    }
}
```

Note: `BookingConfirmed` is created in Task 4 — implement that before running this task's test, or temporarily comment the dispatch. The plan orders Task 4 right after; if running strictly in order, add a minimal `BookingConfirmed` stub now. To keep tasks independently runnable, create the event in **this** task as a stub (full version is identical in Task 4):

`app/Events/BookingConfirmed.php`:
```php
<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Booking $booking) {}
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=CreatePendingBookingTest`
Expected: PASS (5 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Services/BookingService.php app/Events/BookingConfirmed.php tests/Feature/Booking/CreatePendingBookingTest.php
git commit -m "feat(booking): add BookingService create-pending with hold and validation

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Task 4: `BookingService::confirm()` idempotency + `BookingConfirmed` event

`BookingConfirmed` already exists (Task 3 stub). This task proves `confirm()` is idempotent and fires the event exactly once on the pending→confirmed transition.

**Files:**
- Test: `tests/Feature/Booking/ConfirmBookingTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Events\BookingConfirmed;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->service = app(BookingService::class);
});

it('confirms a pending booking and fires BookingConfirmed once', function () {
    Event::fake([BookingConfirmed::class]);
    $booking = Booking::factory()->pending()->create();

    $this->service->confirm($booking, 'pi_test_123');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Confirmed)
        ->and($booking->stripe_payment_intent_id)->toBe('pi_test_123')
        ->and($booking->expires_at)->toBeNull();

    Event::assertDispatchedTimes(BookingConfirmed::class, 1);
});

it('is idempotent on repeated confirmation (webhook redelivery)', function () {
    Event::fake([BookingConfirmed::class]);
    $booking = Booking::factory()->pending()->create();

    $this->service->confirm($booking, 'pi_test_123');
    $this->service->confirm($booking->fresh(), 'pi_test_123');

    Event::assertDispatchedTimes(BookingConfirmed::class, 1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ConfirmBookingTest`
Expected: FAIL if event/confirm not behaving idempotently. (If Task 3 was implemented exactly, the first test may already pass; the idempotency test guards the behavior — keep both.)

- [ ] **Step 3: Implementation**

Already implemented in Task 3 (`confirm()` early-returns when already `Confirmed`). No new code unless a test fails — if so, align `confirm()` with the spec above.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=ConfirmBookingTest`
Expected: PASS (2 tests).

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/Booking/ConfirmBookingTest.php
git commit -m "test(booking): cover confirm idempotency and BookingConfirmed event

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Task 5: `StripeService`

`buildLineItems()` is pure (no network) and unit-tested: one Stripe line item per cost component (stay, cleaning fee, each add-on), each `quantity: 1` with `unit_amount` equal to that component's total in cents so the Stripe total equals `grand_total` exactly. `createCheckoutSession()` and `constructWebhookEvent()` wrap the SDK and are not unit-tested directly (covered via mocks in Tasks 6–7).

**Files:**
- Create: `app/Services/StripeService.php`
- Test: `tests/Feature/Booking/StripeLineItemsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Booking;
use App\Services\StripeService;

it('builds one line item per cost component summing to grand_total', function () {
    $booking = Booking::factory()->create([
        'nights_total' => 30000, 'cleaning_fee' => 3000, 'addons_total' => 7000, 'grand_total' => 40000,
    ]);
    $addon = Addon::factory()->create(['pricing_type' => AddonPricingType::PerStay]);
    $booking->addons()->attach($addon->id, [
        'name' => 'Sauna', 'price' => 7000, 'pricing_type' => 'per_stay', 'quantity' => 1,
    ]);

    $items = app(StripeService::class)->buildLineItems($booking->fresh());

    $sum = collect($items)->sum(fn ($i) => $i['price_data']['unit_amount'] * $i['quantity']);

    expect($sum)->toBe(40000)
        ->and($items)->toHaveCount(3) // stay + cleaning + sauna
        ->and($items[0]['price_data']['currency'])->toBe('eur');
});

it('omits the cleaning fee line when it is zero', function () {
    $booking = Booking::factory()->create([
        'nights_total' => 20000, 'cleaning_fee' => 0, 'addons_total' => 0, 'grand_total' => 20000,
    ]);

    $items = app(StripeService::class)->buildLineItems($booking->fresh());

    expect($items)->toHaveCount(1)
        ->and($items[0]['price_data']['unit_amount'])->toBe(20000);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=StripeLineItemsTest`
Expected: FAIL — `Class "App\Services\StripeService" not found`.

- [ ] **Step 3: Implement the service**

`app/Services/StripeService.php`:
```php
<?php

namespace App\Services;

use App\Models\Booking;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeService
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient((string) config('services.stripe.secret'));
    }

    /**
     * Build Stripe line items from a booking's stored cost components.
     * One item per component (quantity 1) so the Stripe total equals grand_total.
     *
     * @return array<int, array{price_data: array{currency: string, unit_amount: int, product_data: array{name: string}}, quantity: int}>
     */
    public function buildLineItems(Booking $booking): array
    {
        $currency = $booking->currency;
        $items = [];

        $items[] = $this->lineItem($currency, __('Naktis').' ('.$booking->check_in->toDateString().' – '.$booking->check_out->toDateString().')', $booking->nights_total);

        if ($booking->cleaning_fee > 0) {
            $items[] = $this->lineItem($currency, __('Uzkopšana'), $booking->cleaning_fee);
        }

        foreach ($booking->addons as $addon) {
            $items[] = $this->lineItem(
                $currency,
                $addon->pivot->name,
                $addon->pivot->price * $addon->pivot->quantity,
            );
        }

        return $items;
    }

    public function createCheckoutSession(Booking $booking, string $successUrl, string $cancelUrl): Session
    {
        return $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $this->buildLineItems($booking),
            'customer_email' => $booking->guest_email,
            'client_reference_id' => $booking->reference,
            'metadata' => ['booking_id' => (string) $booking->id],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'expires_at' => $booking->expires_at->getTimestamp(),
        ], [
            'idempotency_key' => 'checkout_'.$booking->reference,
        ]);
    }

    public function constructWebhookEvent(string $payload, ?string $signature): Event
    {
        return Webhook::constructEvent($payload, (string) $signature, (string) config('services.stripe.webhook_secret'));
    }

    /**
     * @return array{price_data: array{currency: string, unit_amount: int, product_data: array{name: string}}, quantity: int}
     */
    private function lineItem(string $currency, string $name, int $amount): array
    {
        return [
            'price_data' => [
                'currency' => $currency,
                'unit_amount' => $amount,
                'product_data' => ['name' => $name],
            ],
            'quantity' => 1,
        ];
    }
}
```

Note on `expires_at`: Stripe requires the session `expires_at` to be at least 30 minutes in the future. Our hold is 20 minutes, which Stripe will reject. Set the **Stripe** session expiry independently to the minimum 30 minutes (the booking hold stays at 20 min and is enforced by our own scheduled command). Change the `'expires_at'` line to:
```php
            'expires_at' => now()->addMinutes(30)->getTimestamp(),
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=StripeLineItemsTest`
Expected: PASS (2 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Services/StripeService.php tests/Feature/Booking/StripeLineItemsTest.php
git commit -m "feat(booking): add StripeService with checkout session and line items

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Task 6: Webhook controller + route (CSRF-exempt)

The webhook verifies the signature (via `StripeService`), and on `checkout.session.completed` confirms the matching booking. Always returns 200 for handled/ignored events, 400 for an invalid signature. Tests mock `StripeService::constructWebhookEvent` to return a hand-built `Stripe\Event` (no network, no real signature).

**Files:**
- Create: `app/Http/Controllers/StripeWebhookController.php`
- Modify: `routes/web.php` (top-level, outside the localization group), `bootstrap/app.php` (CSRF exempt)
- Test: `tests/Feature/Booking/StripeWebhookTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\StripeService;
use Stripe\Event;

it('confirms the booking on checkout.session.completed', function () {
    $booking = Booking::factory()->pending()->create();

    $event = Event::constructFrom([
        'id' => 'evt_1',
        'type' => 'checkout.session.completed',
        'data' => ['object' => [
            'id' => 'cs_test_1',
            'object' => 'checkout.session',
            'payment_intent' => 'pi_test_1',
            'metadata' => ['booking_id' => (string) $booking->id],
        ]],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->once()->andReturn($event);
    });

    $response = $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake']);

    $response->assertOk();
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed)
        ->and($booking->fresh()->stripe_payment_intent_id)->toBe('pi_test_1');
});

it('returns 400 on an invalid signature', function () {
    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('constructWebhookEvent')->andThrow(new \Stripe\Exception\SignatureVerificationException('bad sig'));
    });

    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 'bad'])->assertStatus(400);
});

it('ignores unrelated events with a 200', function () {
    $event = Event::constructFrom([
        'id' => 'evt_2', 'type' => 'payment_intent.created',
        'data' => ['object' => ['id' => 'pi_x']],
    ]);
    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->andReturn($event);
    });

    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 'ok'])->assertOk();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=StripeWebhookTest`
Expected: FAIL — route `/stripe/webhook` not defined (404).

- [ ] **Step 3: Implement the controller, route, and CSRF exemption**

`app/Http/Controllers/StripeWebhookController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeService $stripe, BookingService $bookings): Response
    {
        try {
            $event = $stripe->constructWebhookEvent($request->getContent(), $request->header('Stripe-Signature'));
        } catch (\Throwable $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $booking = Booking::query()
                ->when($session->id ?? null, fn ($q) => $q->orWhere('stripe_session_id', $session->id))
                ->orWhere('id', $session->metadata->booking_id ?? null)
                ->first();

            if ($booking) {
                $paymentIntent = is_string($session->payment_intent ?? null)
                    ? $session->payment_intent
                    : ($session->payment_intent->id ?? null);
                $bookings->confirm($booking, $paymentIntent);
            }
        }

        return response('OK', 200);
    }
}
```

In `routes/web.php`, register the webhook **outside** the localization group (it must not be locale-prefixed). Add near the top, after the `use` statements and before the `Route::group([...])` block:
```php
Route::post('/stripe/webhook', \App\Http\Controllers\StripeWebhookController::class)->name('stripe.webhook');
```

In `bootstrap/app.php`, inside the `->withMiddleware(function (Middleware $middleware) {` closure, add:
```php
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=StripeWebhookTest`
Expected: PASS (3 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/StripeWebhookController.php routes/web.php bootstrap/app.php tests/Feature/Booking/StripeWebhookTest.php
git commit -m "feat(booking): add Stripe webhook endpoint for checkout confirmation

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Task 7: Release-expired command + schedule

**Files:**
- Create: `app/Console/Commands/ReleaseExpiredBookings.php`
- Modify: `routes/console.php`
- Test: `tests/Feature/Booking/ReleaseExpiredBookingsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Models\Booking;

it('expires only pending holds past their deadline', function () {
    $expired = Booking::factory()->expired()->create();          // pending, expires_at in past
    $live = Booking::factory()->pending()->create();             // pending, expires_at in future
    $confirmed = Booking::factory()->create();                   // confirmed

    $this->artisan('bookings:release-expired')->assertSuccessful();

    expect($expired->fresh()->status)->toBe(BookingStatus::Expired)
        ->and($live->fresh()->status)->toBe(BookingStatus::Pending)
        ->and($confirmed->fresh()->status)->toBe(BookingStatus::Confirmed);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ReleaseExpiredBookingsTest`
Expected: FAIL — command `bookings:release-expired` not found.

- [ ] **Step 3: Implement the command and schedule**

`app/Console/Commands/ReleaseExpiredBookings.php`:
```php
<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Console\Command;

class ReleaseExpiredBookings extends Command
{
    protected $signature = 'bookings:release-expired';

    protected $description = 'Release expired pending booking holds so the dates become available again.';

    public function handle(): int
    {
        $released = Booking::query()
            ->where('status', BookingStatus::Pending)
            ->where('expires_at', '<', now())
            ->update(['status' => BookingStatus::Expired]);

        $this->info("Released {$released} expired booking hold(s).");

        return self::SUCCESS;
    }
}
```

Add to `routes/console.php` (import the facade):
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('bookings:release-expired')->everyMinute();
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=ReleaseExpiredBookingsTest`
Expected: PASS.

Verify the schedule is registered: `php artisan schedule:list`
Expected: shows `bookings:release-expired` every minute.

- [ ] **Step 5: Commit**

```bash
git add app/Console/Commands/ReleaseExpiredBookings.php routes/console.php tests/Feature/Booking/ReleaseExpiredBookingsTest.php
git commit -m "feat(booking): add scheduled command to release expired holds

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Task 8: `BookingWidget` Livewire component

A functional booking widget: date inputs, guest counts, add-on checkboxes, a live price summary (recomputed reactively), and a Reserve button. On submit it creates the pending booking and redirects to Stripe. (Visual calendar polish to match the mockup is a later refinement — this version is functional and styled with Tailwind.)

**Files:**
- Create: `app/Livewire/Booking/BookingWidget.php`
- Create: `resources/views/livewire/booking/booking-widget.blade.php`
- Modify: `resources/views/product.blade.php` (mount the widget; remove the booking.com CTA link)
- Test: `tests/Feature/Booking/BookingWidgetTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Addon;
use App\Models\Booking;
use App\Models\Product;
use App\Services\StripeService;
use Livewire\Livewire;

beforeEach(function () {
    $this->product = Product::factory()->create([
        'base_price' => 10000, 'cleaning_fee' => 3000, 'min_nights' => 1, 'person_count' => 4,
    ]);
});

it('renders and computes a live quote for chosen dates', function () {
    Livewire::test(\App\Livewire\Booking\BookingWidget::class, ['product' => $this->product])
        ->set('checkIn', '2026-09-01')
        ->set('checkOut', '2026-09-04')
        ->set('adults', 2)
        ->assertSet('quoteTotal', 33000) // 3x10000 + 3000
        ->assertSee('330'); // formatted euros somewhere in the summary
});

it('creates a pending booking and redirects to Stripe on reserve', function () {
    $fakeSession = new \Stripe\Checkout\Session('cs_test_123');
    $fakeSession->url = 'https://checkout.stripe.test/cs_test_123';

    $this->mock(StripeService::class, function ($mock) use ($fakeSession) {
        $mock->shouldReceive('createCheckoutSession')->once()->andReturn($fakeSession);
    });

    Livewire::test(\App\Livewire\Booking\BookingWidget::class, ['product' => $this->product])
        ->set('checkIn', '2026-09-01')
        ->set('checkOut', '2026-09-04')
        ->set('adults', 2)
        ->set('children', 0)
        ->set('guestName', 'Jane Guest')
        ->set('guestEmail', 'jane@example.com')
        ->set('guestPhone', '+37120000000')
        ->call('reserve')
        ->assertRedirect('https://checkout.stripe.test/cs_test_123');

    expect(Booking::where('product_id', $this->product->id)->where('status', \App\Enums\BookingStatus::Pending)->count())->toBe(1);
});

it('shows an error and does not redirect when dates are unavailable', function () {
    Booking::factory()->for($this->product)->create([
        'status' => \App\Enums\BookingStatus::Confirmed, 'check_in' => '2026-09-01', 'check_out' => '2026-09-10',
    ]);

    Livewire::test(\App\Livewire\Booking\BookingWidget::class, ['product' => $this->product])
        ->set('checkIn', '2026-09-02')
        ->set('checkOut', '2026-09-04')
        ->set('adults', 2)
        ->set('guestName', 'Jane Guest')
        ->set('guestEmail', 'jane@example.com')
        ->set('guestPhone', '+37120000000')
        ->call('reserve')
        ->assertNoRedirect();

    expect(Booking::where('status', \App\Enums\BookingStatus::Pending)->count())->toBe(0);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=BookingWidgetTest`
Expected: FAIL — component class not found.

- [ ] **Step 3: Implement the component, view, and mount it**

`app/Livewire/Booking/BookingWidget.php`:
```php
<?php

namespace App\Livewire\Booking;

use App\Exceptions\BookingException;
use App\Models\Addon;
use App\Models\Product;
use App\Services\BookingService;
use App\Services\PricingService;
use App\Services\StripeService;
use App\Support\BookingQuote;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BookingWidget extends Component
{
    public Product $product;

    #[Validate('required|date|after_or_equal:today')]
    public string $checkIn = '';

    #[Validate('required|date|after:checkIn')]
    public string $checkOut = '';

    #[Validate('required|integer|min:1')]
    public int $adults = 2;

    #[Validate('integer|min:0')]
    public int $children = 0;

    /** @var array<int, bool> addon_id => selected */
    public array $selectedAddons = [];

    #[Validate('required|string|min:2|max:120')]
    public string $guestName = '';

    #[Validate('required|email|max:255')]
    public string $guestEmail = '';

    #[Validate('required|string|min:6|max:40')]
    public string $guestPhone = '';

    public ?string $bookingError = null;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return array<int, array{addon: Addon, quantity: int}>
     */
    private function addonSelections(): array
    {
        if (empty(array_filter($this->selectedAddons))) {
            return [];
        }

        return $this->product->addons()
            ->where('is_active', true)
            ->whereIn('id', array_keys(array_filter($this->selectedAddons)))
            ->get()
            ->map(fn (Addon $addon) => ['addon' => $addon, 'quantity' => 1])
            ->all();
    }

    private function quote(): ?BookingQuote
    {
        if ($this->checkIn === '' || $this->checkOut === '') {
            return null;
        }

        try {
            return app(PricingService::class)->quote(
                $this->product,
                Carbon::parse($this->checkIn),
                Carbon::parse($this->checkOut),
                $this->addonSelections(),
            );
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    public function getQuoteTotalProperty(): int
    {
        return $this->quote()?->grandTotal ?? 0;
    }

    public function reserve(BookingService $bookings, StripeService $stripe)
    {
        $this->bookingError = null;
        $this->validate();

        try {
            $booking = $bookings->createPendingBooking(
                $this->product,
                Carbon::parse($this->checkIn),
                Carbon::parse($this->checkOut),
                $this->adults,
                $this->children,
                $this->addonSelections(),
                ['name' => $this->guestName, 'email' => $this->guestEmail, 'phone' => $this->guestPhone],
            );
        } catch (BookingException $e) {
            $this->bookingError = $e->getMessage();

            return null;
        }

        $session = $stripe->createCheckoutSession(
            $booking,
            route('booking.success', $booking->reference).'?session_id={CHECKOUT_SESSION_ID}',
            route('booking.cancel', $booking->reference),
        );

        $booking->update(['stripe_session_id' => $session->id]);

        return $this->redirect($session->url);
    }

    public function render(): View
    {
        return view('livewire.booking.booking-widget', [
            'quote' => $this->quote(),
            'addons' => $this->product->addons()->where('is_active', true)->get(),
        ]);
    }
}
```

`resources/views/livewire/booking/booking-widget.blade.php` (functional, Tailwind-styled; match the surrounding card look from the mockup). Use `number_format($amount / 100, 2)` for euros:
```blade
<div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
    @if ($bookingError)
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $bookingError }}</div>
    @endif

    <div class="grid grid-cols-2 gap-3">
        <label class="text-sm font-medium text-neutral-700">
            {{ __('Check-in') }}
            <input type="date" wire:model.live="checkIn" class="mt-1 w-full rounded-lg border-neutral-300" />
            @error('checkIn') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </label>
        <label class="text-sm font-medium text-neutral-700">
            {{ __('Check-out') }}
            <input type="date" wire:model.live="checkOut" class="mt-1 w-full rounded-lg border-neutral-300" />
            @error('checkOut') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </label>
    </div>

    <div class="mt-3 grid grid-cols-2 gap-3">
        <label class="text-sm font-medium text-neutral-700">
            {{ __('Adults') }}
            <input type="number" min="1" wire:model.live="adults" class="mt-1 w-full rounded-lg border-neutral-300" />
        </label>
        <label class="text-sm font-medium text-neutral-700">
            {{ __('Children') }}
            <input type="number" min="0" wire:model.live="children" class="mt-1 w-full rounded-lg border-neutral-300" />
        </label>
    </div>

    @if ($addons->isNotEmpty())
        <div class="mt-4 space-y-2">
            @foreach ($addons as $addon)
                <label class="flex items-center gap-2 text-sm text-neutral-700">
                    <input type="checkbox" wire:model.live="selectedAddons.{{ $addon->id }}" />
                    {{ $addon->getTranslation('name', app()->getLocale()) }}
                    <span class="ml-auto">€{{ number_format($addon->price / 100, 2) }}</span>
                </label>
            @endforeach
        </div>
    @endif

    @if ($quote)
        <div class="mt-4 space-y-1 border-t border-neutral-200 pt-4 text-sm">
            <div class="flex justify-between">
                <span>€{{ number_format(($quote->nightsTotal / max($quote->nights, 1)) / 100, 0) }} × {{ $quote->nights }} {{ __('naktis') }}</span>
                <span>€{{ number_format($quote->nightsTotal / 100, 2) }}</span>
            </div>
            @if ($quote->cleaningFee > 0)
                <div class="flex justify-between"><span>{{ __('Uzkopšana') }}</span><span>€{{ number_format($quote->cleaningFee / 100, 2) }}</span></div>
            @endif
            @if ($quote->addonsTotal > 0)
                <div class="flex justify-between"><span>{{ __('Papildservisi') }}</span><span>€{{ number_format($quote->addonsTotal / 100, 2) }}</span></div>
            @endif
            <div class="flex justify-between border-t border-neutral-200 pt-2 font-semibold">
                <span>{{ __('Total') }}</span><span>€{{ number_format($quote->grandTotal / 100, 2) }}</span>
            </div>
        </div>
    @endif

    <div class="mt-4 space-y-2">
        <input type="text" wire:model="guestName" placeholder="{{ __('Vārds, uzvārds') }}" class="w-full rounded-lg border-neutral-300" />
        @error('guestName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        <input type="email" wire:model="guestEmail" placeholder="{{ __('E-pasts') }}" class="w-full rounded-lg border-neutral-300" />
        @error('guestEmail') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        <input type="text" wire:model="guestPhone" placeholder="{{ __('Tālrunis') }}" class="w-full rounded-lg border-neutral-300" />
        @error('guestPhone') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
    </div>

    <button type="button" wire:click="reserve" wire:loading.attr="disabled"
        class="mt-4 w-full rounded-xl bg-[#2f3a1f] px-4 py-3 font-semibold text-white hover:opacity-90">
        {{ __('Rezervēt brīvdienu māju') }}
    </button>
</div>
```

In `resources/views/product.blade.php`, replace the booking.com CTA (around line 20 — the `<x-btn-primary href="https://www.booking.com/...">@lang('Rezervēt')</x-btn-primary>`) with the widget mount, placing it in the booking card/sidebar area:
```blade
@livewire('booking.booking-widget', ['product' => $product])
```
Read the surrounding markup and keep the existing card container/styling so the widget sits where the price box is in the mockup. Leave the gallery/amenities sections untouched.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=BookingWidgetTest`
Expected: PASS (3 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Livewire/Booking/BookingWidget.php resources/views/livewire/booking/booking-widget.blade.php resources/views/product.blade.php tests/Feature/Booking/BookingWidgetTest.php
git commit -m "feat(booking): add BookingWidget livewire component on product page

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Task 9: Success / cancel pages + routes

**Files:**
- Create: `app/Http/Controllers/BookingController.php`
- Create: `resources/views/booking/success.blade.php`, `resources/views/booking/cancel.blade.php`
- Modify: `routes/web.php` (inside the localization group, BEFORE the catch-all `/{product}` route)
- Test: `tests/Feature/Booking/BookingPagesTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Models\Booking;

it('shows the confirmed reference on the success page', function () {
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed, 'reference' => 'SS-ABCDE',
    ]);

    $this->get('/lv/booking/'.$booking->reference.'/success')
        ->assertOk()
        ->assertSee('SS-ABCDE');
});

it('shows a processing state when the booking is still pending', function () {
    $booking = Booking::factory()->pending()->create(['reference' => 'SS-PEND1']);

    $this->get('/lv/booking/'.$booking->reference.'/success')
        ->assertOk()
        ->assertSee(__('We are confirming your payment'));
});

it('renders the cancel page', function () {
    $booking = Booking::factory()->pending()->create();

    $this->get('/lv/booking/'.$booking->reference.'/cancel')->assertOk();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=BookingPagesTest`
Expected: FAIL — routes not defined (404).

- [ ] **Step 3: Implement the controller, views, and routes**

`app/Http/Controllers/BookingController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function success(Booking $booking): View
    {
        return view('booking.success', compact('booking'));
    }

    public function cancel(Booking $booking): View
    {
        return view('booking.cancel', compact('booking'));
    }
}
```

`resources/views/booking/success.blade.php`:
```blade
<x-app-layout>
    <div class="mx-auto max-w-xl px-4 py-20 text-center">
        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            <h1 class="text-2xl font-semibold text-[#2f3a1f]">{{ __('Paldies! Rezervācija apstiprināta.') }}</h1>
            <p class="mt-4 text-neutral-600">{{ __('Rezervācijas numurs') }}: <strong>{{ $booking->reference }}</strong></p>
            <p class="mt-2 text-neutral-600">{{ $booking->check_in->toDateString() }} – {{ $booking->check_out->toDateString() }}</p>
        @else
            <h1 class="text-2xl font-semibold text-[#2f3a1f]">{{ __('We are confirming your payment') }}</h1>
            <p class="mt-4 text-neutral-600">{{ __('Rezervācijas numurs') }}: <strong>{{ $booking->reference }}</strong></p>
        @endif
    </div>
</x-app-layout>
```

`resources/views/booking/cancel.blade.php`:
```blade
<x-app-layout>
    <div class="mx-auto max-w-xl px-4 py-20 text-center">
        <h1 class="text-2xl font-semibold text-[#2f3a1f]">{{ __('Maksājums atcelts') }}</h1>
        <p class="mt-4 text-neutral-600">{{ __('Jūsu rezervācija netika pabeigta. Datumi atkal būs pieejami pēc neilga brīža.') }}</p>
        <a href="{{ route('home') }}" class="mt-6 inline-block text-[#2f3a1f] underline">{{ __('Atpakaļ uz sākumu') }}</a>
    </div>
</x-app-layout>
```

Confirm the layout component name: check whether the site uses `<x-app-layout>` or `@extends('layouts.app')` by reading an existing public view (e.g. `resources/views/contacts.blade.php`). Match whatever the existing public pages use — do not introduce a new layout convention.

In `routes/web.php`, inside the localization group, **before** the final `Route::get('/{product}', ...)` catch-all, add:
```php
        Route::get('/booking/{booking:reference}/success', [\App\Http\Controllers\BookingController::class, 'success'])->name('booking.success');
        Route::get('/booking/{booking:reference}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel'])->name('booking.cancel');
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=BookingPagesTest`
Expected: PASS (3 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/BookingController.php resources/views/booking routes/web.php tests/Feature/Booking/BookingPagesTest.php
git commit -m "feat(booking): add success and cancel pages with routes

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Task 10: Pint + full suite

- [ ] **Step 1: Format** — `vendor/bin/pint --dirty --format agent`
- [ ] **Step 2: Booking suite** — `php artisan test --compact tests/Feature/Booking` (expect all PASS)
- [ ] **Step 3: Full suite** — `php artisan test --compact` (expect no regressions)
- [ ] **Step 4: Commit any formatting**

```bash
git add -A
git commit -m "style(booking): apply pint formatting

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Self-Review Notes

- **Spec coverage (§4 booking flow):** widget selection (Task 8), pending-hold creation in a locked transaction with capacity/min-nights/availability validation (Task 3), Stripe Checkout session + idempotency + dynamic payment methods (Task 5), webhook-driven confirmation as source of truth (Task 6), success/cancel pages reading DB status (Task 9), hold expiry (Task 7), `BookingConfirmed` event for Plan 3 (Tasks 3–4). Carry-forward items `min_nights` and `currency` from Plan 1's review are now addressed (min-nights enforced in Task 3; currency fixed to `eur` on creation).
- **Deferred to Plan 3:** `BookingConfirmed`/`BookingCancelled` listeners and emails, guest manage page + refunds, `charge.refunded` webhook branch.
- **Deferred to Plan 4:** admin pricing calendar, add-ons management, blocked-dates UI, booking management. (Until Plan 4, set `base_price`/`product_prices`/`addons` via tinker or a seeder for manual testing.)
- **Type consistency:** `BookingService::createPendingBooking(Product, CarbonInterface, CarbonInterface, int, int, array, array)` and `confirm(Booking, ?string)` match every call site (widget, webhook) and tests. `StripeService::buildLineItems/createCheckoutSession/constructWebhookEvent` match the webhook + widget usage. `BookingException` factory names (`datesUnavailable`, `exceedsCapacity`, `belowMinimumNights`) used consistently.
- **Stripe expiry caveat:** the booking hold is 20 min but Stripe's session `expires_at` minimum is 30 min — Task 5 sets the Stripe session expiry to 30 min independently while our own scheduled command enforces the 20-min hold. This is intentional and documented in the task.
- **Manual-testing prerequisites** (for the human after this plan): Stripe test API keys in `.env`, a product with `base_price > 0`, and the Stripe CLI forwarding `stripe listen --forward-to <app>/stripe/webhook` to obtain `STRIPE_WEBHOOK_SECRET`.
```
