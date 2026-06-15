# Booking System — Plan 1: Foundation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the database schema, Eloquent models, factories, and pure domain services (pricing + availability) for the booking system — no UI, no Stripe yet.

**Architecture:** New tables hang off the existing `Product` model. Money is stored as integer cents throughout. Two stateless services encapsulate the core logic: `PricingService` computes a `BookingQuote` from per-date prices + cleaning fee + add-ons; `AvailabilityService` answers whether a date range is bookable given confirmed bookings, live pending holds, and admin-blocked dates. Everything here is unit-testable with factories and no external calls.

**Tech Stack:** Laravel 13, PHP 8.3, Pest 4, Eloquent, Carbon. Backed enums for status/pricing-type.

**Source spec:** `docs/superpowers/specs/2026-06-15-booking-system-design.md`

---

## File Structure

**Enums (create):**
- `app/Enums/BookingStatus.php` — `Pending`, `Confirmed`, `Expired`, `Cancelled`.
- `app/Enums/AddonPricingType.php` — `PerStay`, `PerNight`.

**Migrations (create):**
- `..._add_booking_columns_to_products_table.php`
- `..._create_product_prices_table.php`
- `..._create_addons_table.php`
- `..._create_blocked_dates_table.php`
- `..._create_bookings_table.php`
- `..._create_booking_addon_table.php`

**Models (create):** `ProductPrice`, `Addon`, `BlockedDate`, `Booking`. **Modify:** `app/Models/Product.php` (add relations + new casts).

**Factories (create):** `ProductPriceFactory`, `AddonFactory`, `BlockedDateFactory`, `BookingFactory`.

**Services (create):**
- `app/Support/BookingQuote.php` — readonly DTO.
- `app/Services/PricingService.php`
- `app/Services/AvailabilityService.php`

**Tests (create):** under `tests/Feature/Booking/` so `RefreshDatabase` applies automatically.

---

## Task 1: Backed enums

**Files:**
- Create: `app/Enums/BookingStatus.php`
- Create: `app/Enums/AddonPricingType.php`
- Test: `tests/Feature/Booking/EnumsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\AddonPricingType;
use App\Enums\BookingStatus;

it('exposes the expected booking statuses', function () {
    expect(BookingStatus::Pending->value)->toBe('pending')
        ->and(BookingStatus::Confirmed->value)->toBe('confirmed')
        ->and(BookingStatus::Expired->value)->toBe('expired')
        ->and(BookingStatus::Cancelled->value)->toBe('cancelled');
});

it('exposes the expected add-on pricing types', function () {
    expect(AddonPricingType::PerStay->value)->toBe('per_stay')
        ->and(AddonPricingType::PerNight->value)->toBe('per_night');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=EnumsTest`
Expected: FAIL — `Class "App\Enums\BookingStatus" not found`.

- [ ] **Step 3: Write the enums**

`app/Enums/BookingStatus.php`:
```php
<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
}
```

`app/Enums/AddonPricingType.php`:
```php
<?php

namespace App\Enums;

enum AddonPricingType: string
{
    case PerStay = 'per_stay';
    case PerNight = 'per_night';
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=EnumsTest`
Expected: PASS (2 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Enums tests/Feature/Booking/EnumsTest.php
git commit -m "feat(booking): add BookingStatus and AddonPricingType enums"
```

---

## Task 2: Add booking columns to `products`

**Files:**
- Create: `database/migrations/2026_06_15_000001_add_booking_columns_to_products_table.php`
- Modify: `app/Models/Product.php`
- Test: `tests/Feature/Booking/ProductBookingColumnsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Product;

it('has booking pricing columns with sensible defaults', function () {
    $product = Product::factory()->create();

    expect($product->base_price)->toBe(0)
        ->and($product->cleaning_fee)->toBe(0)
        ->and($product->min_nights)->toBe(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ProductBookingColumnsTest`
Expected: FAIL — column `base_price` does not exist.

- [ ] **Step 3: Write the migration**

`database/migrations/2026_06_15_000001_add_booking_columns_to_products_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('base_price')->default(0)->after('person_count');
            $table->unsignedInteger('cleaning_fee')->default(0)->after('base_price');
            $table->unsignedInteger('min_nights')->default(1)->after('cleaning_fee');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'cleaning_fee', 'min_nights']);
        });
    }
};
```

Then add casts in `app/Models/Product.php` — extend the existing `$casts` array with:
```php
        'base_price' => 'integer',
        'cleaning_fee' => 'integer',
        'min_nights' => 'integer',
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=ProductBookingColumnsTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add database/migrations app/Models/Product.php tests/Feature/Booking/ProductBookingColumnsTest.php
git commit -m "feat(booking): add base_price, cleaning_fee, min_nights to products"
```

---

## Task 3: `product_prices` table, model, factory, relation

**Files:**
- Create: `database/migrations/2026_06_15_000002_create_product_prices_table.php`
- Create: `app/Models/ProductPrice.php`
- Create: `database/factories/ProductPriceFactory.php`
- Modify: `app/Models/Product.php` (add `prices()` relation)
- Test: `tests/Feature/Booking/ProductPriceTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Product;
use App\Models\ProductPrice;

it('belongs to a product and stores a per-date price in cents', function () {
    $product = Product::factory()->create();

    $price = ProductPrice::factory()->for($product)->create([
        'date' => '2026-07-01',
        'price' => 18000,
    ]);

    expect($price->product->is($product))->toBeTrue()
        ->and($price->price)->toBe(18000)
        ->and($product->fresh()->prices)->toHaveCount(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ProductPriceTest`
Expected: FAIL — `Class "App\Models\ProductPrice" not found`.

- [ ] **Step 3: Write the migration, model, factory, and relation**

`database/migrations/2026_06_15_000002_create_product_prices_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->unsignedInteger('price');
            $table->timestamps();

            $table->unique(['product_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
```

`app/Models/ProductPrice.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'date', 'price'];

    protected $casts = [
        'date' => 'date',
        'price' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

`database/factories/ProductPriceFactory.php`:
```php
<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'date' => $this->faker->unique()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'price' => $this->faker->numberBetween(10000, 30000),
        ];
    }
}
```

Add to `app/Models/Product.php` (relation method, and import `HasMany` if not already imported — it is):
```php
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=ProductPriceTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add database/migrations database/factories/ProductPriceFactory.php app/Models tests/Feature/Booking/ProductPriceTest.php
git commit -m "feat(booking): add product_prices table, model, factory, relation"
```

---

## Task 4: `addons` table, model, factory, relation

**Files:**
- Create: `database/migrations/2026_06_15_000003_create_addons_table.php`
- Create: `app/Models/Addon.php`
- Create: `database/factories/AddonFactory.php`
- Modify: `app/Models/Product.php` (add `addons()` relation)
- Test: `tests/Feature/Booking/AddonTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Product;

it('belongs to a product, is translatable, and casts pricing type', function () {
    $product = Product::factory()->create();

    $addon = Addon::factory()->for($product)->create([
        'name' => ['lv' => 'Pirts', 'en' => 'Sauna'],
        'price' => 7000,
        'pricing_type' => AddonPricingType::PerStay,
    ]);

    expect($addon->product->is($product))->toBeTrue()
        ->and($addon->pricing_type)->toBe(AddonPricingType::PerStay)
        ->and($addon->getTranslation('name', 'en'))->toBe('Sauna')
        ->and($product->fresh()->addons)->toHaveCount(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=AddonTest`
Expected: FAIL — `Class "App\Models\Addon" not found`.

- [ ] **Step 3: Write the migration, model, factory, and relation**

`database/migrations/2026_06_15_000003_create_addons_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->json('name');
            $table->unsignedInteger('price');
            $table->string('pricing_type')->default('per_stay');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
```

`app/Models/Addon.php`:
```php
<?php

namespace App\Models;

use App\Enums\AddonPricingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Addon extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = ['product_id', 'name', 'price', 'pricing_type', 'is_active', 'order'];

    protected $casts = [
        'name' => 'array',
        'price' => 'integer',
        'pricing_type' => AddonPricingType::class,
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('order', static function (Builder $builder) {
            $builder->orderBy('order');
        });
    }
}
```

`database/factories/AddonFactory.php`:
```php
<?php

namespace Database\Factories;

use App\Enums\AddonPricingType;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Addon>
 */
class AddonFactory extends Factory
{
    public function definition(): array
    {
        $word = $this->faker->unique()->word();

        return [
            'product_id' => Product::factory(),
            'name' => ['lv' => ucfirst($word), 'en' => ucfirst($word)],
            'price' => $this->faker->numberBetween(1000, 8000),
            'pricing_type' => $this->faker->randomElement(AddonPricingType::cases()),
            'is_active' => true,
            'order' => 0,
        ];
    }
}
```

Add to `app/Models/Product.php`:
```php
    public function addons(): HasMany
    {
        return $this->hasMany(Addon::class);
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=AddonTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add database/migrations database/factories/AddonFactory.php app/Models tests/Feature/Booking/AddonTest.php
git commit -m "feat(booking): add addons table, model, factory, relation"
```

---

## Task 5: `blocked_dates` table, model, factory, relation

**Files:**
- Create: `database/migrations/2026_06_15_000004_create_blocked_dates_table.php`
- Create: `app/Models/BlockedDate.php`
- Create: `database/factories/BlockedDateFactory.php`
- Modify: `app/Models/Product.php` (add `blockedDates()` relation)
- Test: `tests/Feature/Booking/BlockedDateTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\BlockedDate;
use App\Models\Product;

it('belongs to a product and stores an inclusive date range', function () {
    $product = Product::factory()->create();

    $blocked = BlockedDate::factory()->for($product)->create([
        'start_date' => '2026-07-10',
        'end_date' => '2026-07-12',
        'reason' => 'Maintenance',
    ]);

    expect($blocked->product->is($product))->toBeTrue()
        ->and($blocked->start_date->toDateString())->toBe('2026-07-10')
        ->and($product->fresh()->blockedDates)->toHaveCount(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=BlockedDateTest`
Expected: FAIL — `Class "App\Models\BlockedDate" not found`.

- [ ] **Step 3: Write the migration, model, factory, and relation**

`database/migrations/2026_06_15_000004_create_blocked_dates_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_dates');
    }
};
```

`app/Models/BlockedDate.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedDate extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'start_date', 'end_date', 'reason'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

`database/factories/BlockedDateFactory.php`:
```php
<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockedDate>
 */
class BlockedDateFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+6 months');
        $end = (clone $start)->modify('+2 days');

        return [
            'product_id' => Product::factory(),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'reason' => $this->faker->optional()->sentence(3),
        ];
    }
}
```

Add to `app/Models/Product.php`:
```php
    public function blockedDates(): HasMany
    {
        return $this->hasMany(BlockedDate::class);
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=BlockedDateTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add database/migrations database/factories/BlockedDateFactory.php app/Models tests/Feature/Booking/BlockedDateTest.php
git commit -m "feat(booking): add blocked_dates table, model, factory, relation"
```

---

## Task 6: `bookings` table, model, factory, relation

**Files:**
- Create: `database/migrations/2026_06_15_000005_create_bookings_table.php`
- Create: `app/Models/Booking.php`
- Create: `database/factories/BookingFactory.php`
- Modify: `app/Models/Product.php` (add `bookings()` relation)
- Test: `tests/Feature/Booking/BookingTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Product;

it('persists a booking with casts and product relation', function () {
    $product = Product::factory()->create();

    $booking = Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-07-01',
        'check_out' => '2026-07-05',
        'grand_total' => 72000,
    ]);

    expect($booking->product->is($product))->toBeTrue()
        ->and($booking->status)->toBe(BookingStatus::Confirmed)
        ->and($booking->grand_total)->toBe(72000)
        ->and($booking->check_in->toDateString())->toBe('2026-07-01')
        ->and($booking->reference)->not->toBeEmpty()
        ->and($booking->management_token)->not->toBeEmpty();
});

it('defaults a generated reference and management token via factory', function () {
    $a = Booking::factory()->create();
    $b = Booking::factory()->create();

    expect($a->reference)->not->toBe($b->reference)
        ->and($a->management_token)->not->toBe($b->management_token);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=BookingTest`
Expected: FAIL — `Class "App\Models\Booking" not found`.

- [ ] **Step 3: Write the migration, model, factory, and relation**

`database/migrations/2026_06_15_000005_create_bookings_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->string('reference')->unique();
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->unsignedInteger('nights_total')->default(0);
            $table->unsignedInteger('cleaning_fee')->default(0);
            $table->unsignedInteger('addons_total')->default(0);
            $table->unsignedInteger('grand_total')->default(0);
            $table->string('currency', 3)->default('eur');
            $table->string('status')->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->uuid('management_token')->unique();
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->unsignedInteger('refund_amount')->nullable();
            $table->string('stripe_refund_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status', 'check_in', 'check_out']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
```

`app/Models/Booking.php`:
```php
<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'adults' => 'integer',
        'children' => 'integer',
        'nights_total' => 'integer',
        'cleaning_fee' => 'integer',
        'addons_total' => 'integer',
        'grand_total' => 'integer',
        'status' => BookingStatus::class,
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refund_amount' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

`database/factories/BookingFactory.php`:
```php
<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('now', '+3 months');
        $checkOut = (clone $checkIn)->modify('+3 days');

        return [
            'product_id' => Product::factory(),
            'reference' => 'SS-'.Str::upper(Str::random(5)),
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'guest_name' => $this->faker->name(),
            'guest_email' => $this->faker->safeEmail(),
            'guest_phone' => $this->faker->phoneNumber(),
            'nights_total' => 54000,
            'cleaning_fee' => 0,
            'addons_total' => 0,
            'grand_total' => 54000,
            'currency' => 'eur',
            'status' => BookingStatus::Confirmed,
            'management_token' => (string) Str::uuid(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Pending,
            'expires_at' => now()->addMinutes(20),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Pending,
            'expires_at' => now()->subMinute(),
        ]);
    }
}
```

Add to `app/Models/Product.php`:
```php
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=BookingTest`
Expected: PASS (2 tests).

- [ ] **Step 5: Commit**

```bash
git add database/migrations database/factories/BookingFactory.php app/Models tests/Feature/Booking/BookingTest.php
git commit -m "feat(booking): add bookings table, model, factory, relation"
```

---

## Task 7: `booking_addon` snapshot pivot

**Files:**
- Create: `database/migrations/2026_06_15_000006_create_booking_addon_table.php`
- Modify: `app/Models/Booking.php` (add `addons()` belongsToMany with snapshot pivot columns)
- Test: `tests/Feature/Booking/BookingAddonTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Booking;

it('snapshots add-on name and price onto the booking pivot', function () {
    $booking = Booking::factory()->create();
    $addon = Addon::factory()->create([
        'name' => ['lv' => 'Pirts', 'en' => 'Sauna'],
        'price' => 7000,
        'pricing_type' => AddonPricingType::PerStay,
    ]);

    $booking->addons()->attach($addon->id, [
        'name' => 'Sauna',
        'price' => 7000,
        'pricing_type' => AddonPricingType::PerStay->value,
        'quantity' => 1,
    ]);

    $pivot = $booking->fresh()->addons->first()->pivot;

    expect($booking->fresh()->addons)->toHaveCount(1)
        ->and($pivot->name)->toBe('Sauna')
        ->and((int) $pivot->price)->toBe(7000)
        ->and((int) $pivot->quantity)->toBe(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=BookingAddonTest`
Expected: FAIL — table `booking_addon` does not exist / `addons()` undefined.

- [ ] **Step 3: Write the migration and relation**

`database/migrations/2026_06_15_000006_create_booking_addon_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_addon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('addon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('price');
            $table->string('pricing_type');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_addon');
    }
};
```

Add to `app/Models/Booking.php` — import `BelongsToMany` and add the relation:
```php
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
```
```php
    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class)
            ->withPivot(['name', 'price', 'pricing_type', 'quantity'])
            ->withTimestamps();
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=BookingAddonTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add database/migrations app/Models/Booking.php tests/Feature/Booking/BookingAddonTest.php
git commit -m "feat(booking): add booking_addon snapshot pivot"
```

---

## Task 8: `PricingService` + `BookingQuote` DTO

`PricingService::quote()` sums per-night prices (each night uses its `product_prices` row, falling back to `product.base_price`), adds the cleaning fee, and adds add-ons (`per_stay` = price × qty; `per_night` = price × qty × nights). The number of nights is `check_out − check_in` (half-open: the checkout date is not charged).

**Files:**
- Create: `app/Support/BookingQuote.php`
- Create: `app/Services/PricingService.php`
- Test: `tests/Feature/Booking/PricingServiceTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\PricingService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = app(PricingService::class);
});

it('uses base_price for nights without an override', function () {
    $product = Product::factory()->create(['base_price' => 15000, 'cleaning_fee' => 0]);

    $quote = $this->service->quote($product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04'));

    // 3 nights x 15000
    expect($quote->nights)->toBe(3)
        ->and($quote->nightsTotal)->toBe(45000)
        ->and($quote->grandTotal)->toBe(45000);
});

it('uses per-date overrides where present and base_price elsewhere', function () {
    $product = Product::factory()->create(['base_price' => 15000, 'cleaning_fee' => 0]);
    ProductPrice::factory()->for($product)->create(['date' => '2026-07-02', 'price' => 20000]);

    // nights: 07-01 (15000) + 07-02 (20000) + 07-03 (15000) = 50000
    $quote = $this->service->quote($product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04'));

    expect($quote->nightsTotal)->toBe(50000)
        ->and($quote->grandTotal)->toBe(50000);
});

it('adds the cleaning fee and add-ons (per_stay and per_night)', function () {
    $product = Product::factory()->create(['base_price' => 10000, 'cleaning_fee' => 3000]);
    $sauna = Addon::factory()->for($product)->create(['price' => 7000, 'pricing_type' => AddonPricingType::PerStay]);
    $crib = Addon::factory()->for($product)->create(['price' => 500, 'pricing_type' => AddonPricingType::PerNight]);

    // 2 nights x 10000 = 20000; cleaning 3000; sauna 7000; crib 500 x 2 = 1000 => 31000
    $quote = $this->service->quote(
        $product,
        Carbon::parse('2026-07-01'),
        Carbon::parse('2026-07-03'),
        [['addon' => $sauna, 'quantity' => 1], ['addon' => $crib, 'quantity' => 1]],
    );

    expect($quote->nightsTotal)->toBe(20000)
        ->and($quote->cleaningFee)->toBe(3000)
        ->and($quote->addonsTotal)->toBe(8000)
        ->and($quote->grandTotal)->toBe(31000);
});

it('throws when check_out is not after check_in', function () {
    $product = Product::factory()->create(['base_price' => 10000]);

    expect(fn () => $this->service->quote($product, Carbon::parse('2026-07-05'), Carbon::parse('2026-07-05')))
        ->toThrow(InvalidArgumentException::class);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=PricingServiceTest`
Expected: FAIL — `Class "App\Services\PricingService" not found`.

- [ ] **Step 3: Write the DTO and service**

`app/Support/BookingQuote.php`:
```php
<?php

namespace App\Support;

/**
 * @phpstan-type AddonLine array{addon_id: int, name: string, price: int, pricing_type: string, quantity: int, line_total: int}
 */
readonly class BookingQuote
{
    /**
     * @param  array<int, array{addon_id: int, name: string, price: int, pricing_type: string, quantity: int, line_total: int}>  $addonLines
     */
    public function __construct(
        public int $nights,
        public int $nightsTotal,
        public int $cleaningFee,
        public int $addonsTotal,
        public int $grandTotal,
        public array $addonLines = [],
    ) {}
}
```

`app/Services/PricingService.php`:
```php
<?php

namespace App\Services;

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Product;
use App\Support\BookingQuote;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use InvalidArgumentException;

class PricingService
{
    /**
     * Build a price quote for a stay.
     *
     * @param  array<int, array{addon: Addon, quantity: int}>  $addonSelections
     */
    public function quote(
        Product $product,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        array $addonSelections = [],
    ): BookingQuote {
        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            throw new InvalidArgumentException('Check-out must be after check-in.');
        }

        $nights = $checkIn->diffInDays($checkOut);

        $overrides = $product->prices()
            ->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()])
            ->get()
            ->keyBy(fn ($price) => $price->date->toDateString());

        $nightsTotal = 0;
        foreach (CarbonPeriod::create($checkIn, $checkOut->copy()->subDay()) as $night) {
            $key = $night->toDateString();
            $nightsTotal += $overrides->has($key) ? $overrides[$key]->price : $product->base_price;
        }

        $addonsTotal = 0;
        $addonLines = [];
        foreach ($addonSelections as $selection) {
            /** @var Addon $addon */
            $addon = $selection['addon'];
            $quantity = max(1, (int) ($selection['quantity'] ?? 1));
            $multiplier = $addon->pricing_type === AddonPricingType::PerNight ? $nights : 1;
            $lineTotal = $addon->price * $quantity * $multiplier;
            $addonsTotal += $lineTotal;

            $addonLines[] = [
                'addon_id' => $addon->id,
                'name' => $addon->getTranslation('name', app()->getLocale()),
                'price' => $addon->price,
                'pricing_type' => $addon->pricing_type->value,
                'quantity' => $quantity,
                'line_total' => $lineTotal,
            ];
        }

        $cleaningFee = (int) $product->cleaning_fee;
        $grandTotal = $nightsTotal + $cleaningFee + $addonsTotal;

        return new BookingQuote(
            nights: $nights,
            nightsTotal: $nightsTotal,
            cleaningFee: $cleaningFee,
            addonsTotal: $addonsTotal,
            grandTotal: $grandTotal,
            addonLines: $addonLines,
        );
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=PricingServiceTest`
Expected: PASS (4 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Support/BookingQuote.php app/Services/PricingService.php tests/Feature/Booking/PricingServiceTest.php
git commit -m "feat(booking): add PricingService and BookingQuote DTO"
```

---

## Task 9: `AvailabilityService`

`AvailabilityService::isAvailable()` returns false if the requested half-open range `[checkIn, checkOut)` overlaps any (a) confirmed booking, (b) pending booking with `expires_at` in the future, or (c) blocked-date range. An optional `$ignoreBookingId` lets a booking ignore itself.

Overlap test for two half-open booking ranges `[s1,e1)` and `[s2,e2)`: they overlap iff `s1 < e2 && e1 > s2`.

For `blocked_dates` the range is **inclusive** (`start_date`..`end_date` are all blocked nights). A stay occupies nights `checkIn`..`checkOut − 1`. These overlap iff `start_date < checkOut AND end_date >= checkIn` — pure column comparisons, no date arithmetic, so it works identically on SQLite (test) and MySQL (production).

**Files:**
- Create: `app/Services/AvailabilityService.php`
- Test: `tests/Feature/Booking/AvailabilityServiceTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Product;
use App\Services\AvailabilityService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = app(AvailabilityService::class);
    $this->product = Product::factory()->create();
});

it('is available when nothing overlaps', function () {
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04')))
        ->toBeTrue();
});

it('is unavailable when a confirmed booking overlaps', function () {
    Booking::factory()->for($this->product)->create([
        'check_in' => '2026-07-03', 'check_out' => '2026-07-06',
    ]);

    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04')))
        ->toBeFalse();
});

it('allows a back-to-back stay starting on a checkout day', function () {
    Booking::factory()->for($this->product)->create([
        'check_in' => '2026-07-01', 'check_out' => '2026-07-04',
    ]);

    // new stay starts exactly when the previous checks out
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-04'), Carbon::parse('2026-07-06')))
        ->toBeTrue();
});

it('ignores expired pending holds but blocks live ones', function () {
    Booking::factory()->for($this->product)->expired()->create([
        'check_in' => '2026-07-01', 'check_out' => '2026-07-04',
    ]);
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04')))
        ->toBeTrue();

    Booking::factory()->for($this->product)->pending()->create([
        'check_in' => '2026-07-01', 'check_out' => '2026-07-04',
    ]);
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04')))
        ->toBeFalse();
});

it('is unavailable when a blocked range overlaps (inclusive end)', function () {
    BlockedDate::factory()->for($this->product)->create([
        'start_date' => '2026-07-05', 'end_date' => '2026-07-05',
    ]);

    // stay 07-04 -> 07-06 covers the night of 07-05, which is blocked
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-04'), Carbon::parse('2026-07-06')))
        ->toBeFalse();
});

it('can ignore a given booking id', function () {
    $booking = Booking::factory()->for($this->product)->create([
        'check_in' => '2026-07-01', 'check_out' => '2026-07-04',
    ]);

    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04'), $booking->id))
        ->toBeTrue();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=AvailabilityServiceTest`
Expected: FAIL — `Class "App\Services\AvailabilityService" not found`.

- [ ] **Step 3: Write the service**

`app/Services/AvailabilityService.php`:
```php
<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Product;
use Carbon\CarbonInterface;

class AvailabilityService
{
    /**
     * Whether the half-open range [checkIn, checkOut) is bookable.
     */
    public function isAvailable(
        Product $product,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        ?int $ignoreBookingId = null,
    ): bool {
        $checkInDate = $checkIn->toDateString();
        $checkOutDate = $checkOut->toDateString();

        $bookingClash = $product->bookings()
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->where(function ($query) {
                $query->where('status', BookingStatus::Confirmed)
                    ->orWhere(function ($pending) {
                        $pending->where('status', BookingStatus::Pending)
                            ->where('expires_at', '>', now());
                    });
            })
            ->where('check_in', '<', $checkOutDate)
            ->where('check_out', '>', $checkInDate)
            ->exists();

        if ($bookingClash) {
            return false;
        }

        // blocked_dates are inclusive (start_date..end_date are blocked nights).
        // Overlap with stay nights [checkIn, checkOut): start_date < checkOut AND end_date >= checkIn.
        // Pure column comparisons — driver-agnostic (SQLite test + MySQL prod).
        $blockedClash = $product->blockedDates()
            ->where('start_date', '<', $checkOutDate)
            ->where('end_date', '>=', $checkInDate)
            ->exists();

        return ! $blockedClash;
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=AvailabilityServiceTest`
Expected: PASS (6 assertions across cases).

- [ ] **Step 5: Commit**

```bash
git add app/Services/AvailabilityService.php tests/Feature/Booking/AvailabilityServiceTest.php
git commit -m "feat(booking): add AvailabilityService"
```

---

## Task 10: Pint + full suite

- [ ] **Step 1: Format**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean / fixes applied.

- [ ] **Step 2: Run the booking suite**

Run: `php artisan test --compact tests/Feature/Booking`
Expected: all PASS.

- [ ] **Step 3: Run the full suite (no regressions)**

Run: `php artisan test --compact`
Expected: all PASS.

- [ ] **Step 4: Commit any formatting changes**

```bash
git add -A
git commit -m "style(booking): apply pint formatting"
```

---

## Self-Review Notes

- **Spec coverage:** Data model §3 (products columns, product_prices, addons, bookings, booking_addon, blocked_dates), availability rule §3.7, and pricing logic §3/§4 quote are all covered. Stripe, controllers, Livewire, refunds, notifications, admin, and the scheduled command are intentionally deferred to Plans 2–4.
- **Type consistency:** `BookingQuote` properties (`nights`, `nightsTotal`, `cleaningFee`, `addonsTotal`, `grandTotal`, `addonLines`) are produced by `PricingService::quote()` and asserted in tests with matching names. `AvailabilityService::isAvailable()` signature matches its tests. Enum cases (`BookingStatus`, `AddonPricingType`) referenced consistently across models, factories, and services.
- **DB driver safety:** tests run on SQLite (`:memory:`) while production is MySQL. All queries use plain column comparisons — no raw date arithmetic — so behaviour is identical across both drivers.
```
