# Booking Admin Tooling (Phase 4) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give admins per-house tooling to set nightly pricing (base + min-nights + per-date overrides via a calendar), manage request-only add-ons, and block date ranges — the operational pieces that let the booking system actually take bookings.

**Architecture:** Three new per-product admin sub-pages (`ProductPricing`, `ProductAddons`, `BlockedDates`) following the existing `Admin/Product/*` Livewire pattern (`boot()` service injection, `mount($product)` via `ProductServices::getProductById`, `render()->layout('layouts.admin.app')`), each reachable from an action link on the product list. The pricing calendar reuses vanilla-calendar-pro (already integrated for the booking widget). All money is integer cents in storage; admins type euros.

**Tech Stack:** Laravel 13, Livewire 4, Pest 4, Tailwind v4, Alpine.js 3, vanilla-calendar-pro 3.

---

## Context the implementer needs

**Why this phase exists:** Phases 1–3 built the booking domain (pricing/availability services, Stripe checkout, refunds, notifications, customer + admin booking screens). But there is **no admin UI** to set `base_price`, `min_nights`, per-date prices, add-ons, or blocked dates — today they can only be set via the DB. And the product page **hides the reservation widget when `base_price == 0`**, so no house is bookable until an admin sets a price here. This phase closes that gap. No new domain logic — it drives existing models/services.

**Models already exist (do not create migrations):**
- `App\Models\ProductPrice` — `$fillable = ['product_id','date','price']`; casts `date`→date, `price`→integer (cents). Table `product_prices` has a **unique index on `(product_id, date)`**, so `updateOrCreate(['product_id'=>..,'date'=>..], ['price'=>..])` is the correct upsert.
- `App\Models\Addon` — `HasTranslations`, `$translatable = ['name','description']`; `$fillable = ['product_id','name','description','price','pricing_type','is_active','order']`; casts `name`/`description`→array, `price`→integer, `pricing_type`→`App\Enums\AddonPricingType`, `is_active`→boolean, `order`→integer. A **global scope orders by `order`**. Translatable arrays are stored as `['lv'=>'…','en'=>'…']`; read back with `$addon->getTranslation('name','lv')`.
- `App\Models\BlockedDate` — `$fillable = ['product_id','start_date','end_date','reason']`; casts `start_date`/`end_date`→date.
- `App\Enums\AddonPricingType` — cases `PerStay = 'per_stay'`, `PerNight = 'per_night'`.
- `App\Models\Product` relations: `prices()` (hasMany ProductPrice), `addons()` (hasMany Addon), `blockedDates()` (hasMany BlockedDate). `Product` columns include `base_price` (int cents, default 0) and `min_nights` (int, default 1), both cast to integer.

**Add-ons are REQUEST-ONLY** (never charged). So the add-on UI exposes only `name`, `description`, `is_active`. `price` and `pricing_type` are non-null columns we still must set — default them to `0` and `AddonPricingType::PerStay`. Do NOT add price/pricing-type inputs.

**The existing admin sub-page pattern to mirror** (read `app/Livewire/Admin/Product/ProductRules.php`):
```php
class ProductRules extends Component
{
    public $product;
    private ProductServices $productServices;
    private FlashMessageService $flashMessageService;

    public function boot(ProductServices $productServices, FlashMessageService $flashMessageService /*, … */): void
    {
        $this->productServices = $productServices;
        $this->flashMessageService = $flashMessageService;
    }

    public function mount($product): void
    {
        $this->product = $this->productServices->getProductById($product);
        if (! $this->product) {
            $this->flashMessageService->error(__('Produkts nav atrasts.'));
            $this->redirect(route('dashboard.products'));

            return;
        }
        // … init component state from $this->product
    }

    public function render(): View
    {
        return view('livewire.admin.product.product-rules', [/* … */])->layout('layouts.admin.app');
    }
}
```
- `ProductServices::getProductById($product)` returns the `Product` or null. (In tests, the component is mounted with the product **id**: `Livewire::test(ProductPricing::class, ['product' => $product->id])`.)
- `FlashMessageService` has `success(string)` / `error(string)` (flashes session keys `message` / `error`).
- Admin routes live in the `auth` → `dashboard` prefix group in `routes/web.php`, registered with `Route::livewire('/path', Component::class)->name('…')`. The existing per-product sub-page routes are e.g. `Route::livewire('/product/{product:id}/rules', ProductRules::class)->name('product.rules');`. Component classes are imported at the top of `routes/web.php` (e.g. `use App\Livewire\Admin\Product\ProductRules;`).
- Action links on the product list live in `resources/views/livewire/admin/product/product-list.blade.php` (each is an `<a href="{{ route('product.xxx', $product) }}" class="text-bg-ss-600 inline-flex items-center rounded-md p-2 transition-colors duration-200 hover:bg-gray-50 hover:text-gray-900">` wrapping a 16×16 SVG).

**Test pattern** (read `tests/Feature/Product/ProductImagesTest.php`): top of file `uses(RefreshDatabase::class);` then `beforeEach(fn () => $this->actingAs(User::factory()->create()));`, then `Livewire::test(Component::class, ['product' => $product->id])->set(...)->call(...)->assertHasNoErrors()`. Assert DB state with model queries. Admin routes are behind `auth` only — any authenticated user works. Tests run on SQLite `:memory:` (production MySQL) — keep queries driver-portable (the model casts and `updateOrCreate` used here are portable).

**Money convention:** stored as integer cents; admins type euros. Convert euros→cents with `(int) round($euros * 100)`; display cents→euros with `number_format($cents / 100, 2)`.

**i18n:** Latvian source string is the `__()` key; add an English value to `lang/en.json` for every new Latvian string in the same task. Validate JSON after editing: `php -r 'json_decode(file_get_contents("lang/en.json"), true, 512, JSON_THROW_ON_ERROR); echo "valid\n";'`

**Pint:** after PHP changes run `vendor/bin/pint --dirty --format agent` (NOT `--test`). The repo has pre-existing whole-repo Pint drift in untouched files — ignore it; only your changed files must be clean.

**Commit trailer:** end every commit body with `Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>`. Branch is `development` (NOT master); commit directly.

---

## File Structure

**Create:**
- `app/Livewire/Admin/Product/ProductPricing.php` — base price, min-nights, per-date override management.
- `resources/views/livewire/admin/product/product-pricing.blade.php`
- `app/Livewire/Admin/Product/ProductAddons.php` — request-only add-on CRUD.
- `resources/views/livewire/admin/product/product-addons.blade.php`
- `app/Livewire/Admin/Product/BlockedDates.php` — blocked date-range CRUD.
- `resources/views/livewire/admin/product/blocked-dates.blade.php`
- Test files under `tests/Feature/Product/`.

**Modify:**
- `routes/web.php` — three new `Route::livewire(...)` lines + imports.
- `resources/views/livewire/admin/product/product-list.blade.php` — three action links.
- `resources/js/app.js` — add a `pricingCalendar` Alpine component.
- `lang/en.json` — new UI strings.

---

### Task 1: Pricing page — base price + minimum nights

**Files:**
- Create: `app/Livewire/Admin/Product/ProductPricing.php`
- Create: `resources/views/livewire/admin/product/product-pricing.blade.php`
- Modify: `routes/web.php`, `resources/views/livewire/admin/product/product-list.blade.php`, `lang/en.json`
- Test: `tests/Feature/Product/ProductPricingTest.php`

- [ ] **Step 1: Write the failing test** — `tests/Feature/Product/ProductPricingTest.php`:

```php
<?php

use App\Livewire\Admin\Product\ProductPricing;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAs(User::factory()->create()));

it('renders with the product and shows current base price in euros', function () {
    $product = Product::factory()->create(['base_price' => 15000, 'min_nights' => 2]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->assertStatus(200)
        ->assertSet('product.id', $product->id)
        ->assertSet('basePrice', 150.0)
        ->assertSet('minNights', 2);
});

it('saves the base price (euros) as cents and the minimum nights', function () {
    $product = Product::factory()->create(['base_price' => 0, 'min_nights' => 1]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('basePrice', 149.50)
        ->set('minNights', 3)
        ->call('saveBaseSettings')
        ->assertHasNoErrors();

    $product->refresh();
    expect($product->base_price)->toBe(14950)
        ->and($product->min_nights)->toBe(3);
});

it('rejects a base price below zero and minimum nights below one', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('basePrice', -5)
        ->set('minNights', 0)
        ->call('saveBaseSettings')
        ->assertHasErrors(['basePrice', 'minNights']);
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=ProductPricingTest`
Expected: FAIL — component class not found.

- [ ] **Step 3: Create `app/Livewire/Admin/Product/ProductPricing.php`:**

```php
<?php

namespace App\Livewire\Admin\Product;

use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Component;

class ProductPricing extends Component
{
    public $product;

    public ?float $basePrice = null;

    public int $minNights = 1;

    private ProductServices $productServices;

    private FlashMessageService $flashMessageService;

    public function boot(ProductServices $productServices, FlashMessageService $flashMessageService): void
    {
        $this->productServices = $productServices;
        $this->flashMessageService = $flashMessageService;
    }

    public function mount($product): void
    {
        $this->product = $this->productServices->getProductById($product);

        if (! $this->product) {
            $this->flashMessageService->error(__('Produkts nav atrasts.'));
            $this->redirect(route('dashboard.products'));

            return;
        }

        $this->basePrice = $this->product->base_price / 100;
        $this->minNights = $this->product->min_nights;
    }

    public function saveBaseSettings(): void
    {
        $this->validate([
            'basePrice' => 'required|numeric|min:0',
            'minNights' => 'required|integer|min:1',
        ]);

        $this->product->update([
            'base_price' => (int) round($this->basePrice * 100),
            'min_nights' => $this->minNights,
        ]);

        $this->flashMessageService->success(__('Cenas iestatījumi saglabāti.'));
    }

    public function render(): View
    {
        return view('livewire.admin.product.product-pricing')
            ->layout('layouts.admin.app');
    }
}
```

- [ ] **Step 4: Create `resources/views/livewire/admin/product/product-pricing.blade.php`:**

```blade
<div class="max-w-2xl space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ __('Cenas') }}</h1>
        <p class="text-neutral-600">{{ $product->getTranslation('title', app()->getLocale()) }}</p>
    </div>

    <div class="space-y-4">
        <div>
            <label class="mb-1 block font-medium" for="basePrice">{{ __('Pamatcena (EUR/nakts)') }}</label>
            <input id="basePrice" type="number" step="0.01" min="0" wire:model="basePrice"
                class="w-full rounded-lg border p-2" />
            <x-input-error :messages="$errors->get('basePrice')" class="mt-1" />
        </div>

        <div>
            <label class="mb-1 block font-medium" for="minNights">{{ __('Minimālais nakšu skaits') }}</label>
            <input id="minNights" type="number" min="1" wire:model="minNights"
                class="w-full rounded-lg border p-2" />
            <x-input-error :messages="$errors->get('minNights')" class="mt-1" />
        </div>

        <button type="button" wire:click="saveBaseSettings"
            class="rounded-full bg-ss-dark px-6 py-2 text-white">{{ __('Saglabāt') }}</button>
    </div>
</div>
```

- [ ] **Step 5: Register the route in `routes/web.php`.** Add the import next to the other `use App\Livewire\Admin\Product\…;` lines:

```php
use App\Livewire\Admin\Product\ProductPricing;
```

Inside the `dashboard` prefix group, next to `Route::livewire('/product/{product:id}/rules', ProductRules::class)->name('product.rules');`, add:

```php
Route::livewire('/product/{product:id}/pricing', ProductPricing::class)->name('product.pricing');
```

- [ ] **Step 6: Add the action link in `resources/views/livewire/admin/product/product-list.blade.php`.** Immediately AFTER the `product.rules` link block (the `<a href="{{ route('product.rules', $product) }}" …>…</a>`), add:

```blade
                            <a href="{{ route('product.pricing', $product) }}"
                                class="text-bg-ss-600 inline-flex items-center rounded-md p-2 transition-colors duration-200 hover:bg-gray-50 hover:text-gray-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.5 9.5a2.5 2.5 0 0 0-2.5-1.5c-1.5 0-2.5 1-2.5 2s1 1.8 2.5 2 2.5.9 2.5 2-1 2-2.5 2a2.5 2.5 0 0 1-2.5-1.5M12 6.5v11" />
                                </svg>
                            </a>
```

- [ ] **Step 7: Add `lang/en.json` keys.** (`Saglabāt`, `Produkts nav atrasts.` already exist — do NOT re-add. Check first.) Add:

```json
"Cenas iestatījumi saglabāti.": "Pricing settings saved.",
"Pamatcena (EUR/nakts)": "Base price (EUR/night)",
"Minimālais nakšu skaits": "Minimum nights"
```

(`Cenas` already exists as "Prices"/"Pricing" — verify; if missing add `"Cenas": "Pricing"`.)

- [ ] **Step 8: Run the test**

Run: `php artisan test --compact --filter=ProductPricingTest`
Expected: PASS (3 tests).

- [ ] **Step 9: Pint + validate JSON + commit**

```bash
vendor/bin/pint --dirty --format agent
php -r 'json_decode(file_get_contents("lang/en.json"), true, 512, JSON_THROW_ON_ERROR); echo "valid\n";'
git add app/Livewire/Admin/Product/ProductPricing.php resources/views/livewire/admin/product/product-pricing.blade.php routes/web.php resources/views/livewire/admin/product/product-list.blade.php lang/en.json tests/Feature/Product/ProductPricingTest.php
git commit -m "feat(admin): product pricing page with base price and min nights"
```

---

### Task 2: Pricing page — per-date overrides (calendar)

**Files:**
- Modify: `app/Livewire/Admin/Product/ProductPricing.php`
- Modify: `resources/views/livewire/admin/product/product-pricing.blade.php`
- Modify: `resources/js/app.js`, `lang/en.json`
- Test: `tests/Feature/Product/ProductPricingTest.php` (add cases)

- [ ] **Step 1: Add failing tests** to `tests/Feature/Product/ProductPricingTest.php`:

```php
it('applies an override price (euros) to each selected date as cents', function () {
    $product = Product::factory()->create(['base_price' => 15000]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('selectedDates', ['2026-07-12', '2026-07-13'])
        ->set('overridePrice', 180)
        ->call('applyPriceToSelected')
        ->assertHasNoErrors()
        ->assertSet('selectedDates', [])
        ->assertSet('overridePrice', null);

    expect(\App\Models\ProductPrice::where('product_id', $product->id)->count())->toBe(2)
        ->and(\App\Models\ProductPrice::where('product_id', $product->id)->where('date', '2026-07-12')->value('price'))->toBe(18000);
});

it('overwrites the price for a date that already has an override', function () {
    $product = Product::factory()->create();
    \App\Models\ProductPrice::create(['product_id' => $product->id, 'date' => '2026-07-12', 'price' => 18000]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('selectedDates', ['2026-07-12'])
        ->set('overridePrice', 200)
        ->call('applyPriceToSelected')
        ->assertHasNoErrors();

    expect(\App\Models\ProductPrice::where('product_id', $product->id)->count())->toBe(1)
        ->and(\App\Models\ProductPrice::where('product_id', $product->id)->where('date', '2026-07-12')->value('price'))->toBe(20000);
});

it('rejects applying with no dates selected or a non-positive price', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('selectedDates', [])
        ->set('overridePrice', 0)
        ->call('applyPriceToSelected')
        ->assertHasErrors(['selectedDates', 'overridePrice']);
});

it('removes a single date override', function () {
    $product = Product::factory()->create();
    $override = \App\Models\ProductPrice::create(['product_id' => $product->id, 'date' => '2026-07-12', 'price' => 18000]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->call('removeOverride', $override->id)
        ->assertHasNoErrors();

    expect(\App\Models\ProductPrice::find($override->id))->toBeNull();
});
```

- [ ] **Step 2: Run to confirm new cases fail**

Run: `php artisan test --compact --filter=ProductPricingTest`
Expected: FAIL — `applyPriceToSelected`/`removeOverride` undefined.

- [ ] **Step 3: Extend `app/Livewire/Admin/Product/ProductPricing.php`.** Add `use App\Models\ProductPrice;` at the top. Add two properties after `$minNights`:

```php
    /** @var array<int, string> 'Y-m-d' dates selected in the calendar */
    public array $selectedDates = [];

    public ?float $overridePrice = null;
```

Add these methods (after `saveBaseSettings`):

```php
public function applyPriceToSelected(): void
{
    $this->validate([
        'selectedDates' => 'required|array|min:1',
        'selectedDates.*' => 'date',
        'overridePrice' => 'required|numeric|min:0.01',
    ]);

    $cents = (int) round($this->overridePrice * 100);

    foreach ($this->selectedDates as $date) {
        ProductPrice::updateOrCreate(
            ['product_id' => $this->product->id, 'date' => $date],
            ['price' => $cents],
        );
    }

    $this->selectedDates = [];
    $this->overridePrice = null;

    $this->flashMessageService->success(__('Cenas atjauninātas izvēlētajiem datumiem.'));
}

public function removeOverride(int $priceId): void
{
    ProductPrice::where('product_id', $this->product->id)->whereKey($priceId)->delete();

    $this->flashMessageService->success(__('Cenas korekcija dzēsta.'));
}
```

Update `render()` to pass the existing overrides:

```php
public function render(): View
{
    return view('livewire.admin.product.product-pricing', [
        'overrides' => $this->product->prices()->orderBy('date')->get(),
    ])->layout('layouts.admin.app');
}
```

- [ ] **Step 4: Extend the view** `resources/views/livewire/admin/product/product-pricing.blade.php`. Add this block BEFORE the final closing `</div>` (after the base-settings block):

```blade
    <hr class="border-neutral-200" />

    <div class="space-y-4">
        <h2 class="font-medium">{{ __('Cenas pa datumiem') }}</h2>

        <div wire:ignore x-data="pricingCalendar({ minDate: '{{ now()->toDateString() }}' })">
            <div x-ref="calendar"></div>
        </div>

        <div class="flex items-end gap-3">
            <div>
                <label class="mb-1 block text-sm font-medium" for="overridePrice">{{ __('Cena izvēlētajiem (EUR/nakts)') }}</label>
                <input id="overridePrice" type="number" step="0.01" min="0" wire:model="overridePrice"
                    class="w-40 rounded-lg border p-2" />
            </div>
            <button type="button" wire:click="applyPriceToSelected"
                class="rounded-full bg-ss-dark px-5 py-2 text-white">{{ __('Piemērot izvēlētajiem') }}</button>
        </div>
        <x-input-error :messages="$errors->get('overridePrice')" />
        <x-input-error :messages="$errors->get('selectedDates')" />

        @if ($overrides->isNotEmpty())
            <table class="min-w-full divide-y text-sm">
                <thead class="text-left">
                    <tr><th class="py-2">{{ __('Datums') }}</th><th class="py-2">{{ __('Cena') }}</th><th></th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($overrides as $override)
                        <tr>
                            <td class="py-2">{{ $override->date->format('d.m.Y') }}</td>
                            <td class="py-2">{{ number_format($override->price / 100, 2) }} €</td>
                            <td class="py-2 text-right">
                                <button type="button" wire:click="removeOverride({{ $override->id }})"
                                    class="text-red-600 underline">{{ __('Dzēst') }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-sm text-neutral-500">{{ __('Nav cenu korekciju.') }}</p>
        @endif
    </div>
```

- [ ] **Step 5: Add the `pricingCalendar` Alpine component to `resources/js/app.js`.** Inside the existing `document.addEventListener('alpine:init', () => { … })` block (the same one that registers `bookingCalendar`), register a second component:

```js
    window.Alpine.data('pricingCalendar', (config = {}) => ({
        calendar: null,
        init() {
            this.calendar = new Calendar(this.$refs.calendar, {
                type: 'multiple',
                selectedTheme: 'light',
                displayMonthsCount: window.matchMedia('(min-width: 1024px)').matches ? 2 : 1,
                monthsToSwitch: 1,
                selectionDatesMode: 'multiple', // toggle individual dates
                firstWeekday: 1,
                displayDateMin: config.minDate,
                disableDatesPast: true,
                onClickDate: (self) => {
                    const dates = [...(self.context.selectedDates ?? [])].sort();
                    // Defer-sync to Livewire; the value is sent with the next request (the Apply click).
                    this.$wire.set('selectedDates', dates, false);
                },
            });
            this.calendar.init();
        },
        destroy() {
            this.calendar?.destroy?.();
        },
    }));
```

> This JS is not unit-tested (it's a thin UX layer); the component logic is covered by the PHP tests that set `selectedDates` directly. After editing, the user must run `npm run build` (or `npm run dev`) to see the calendar.

- [ ] **Step 6: Add `lang/en.json` keys:**

```json
"Cenas pa datumiem": "Per-date pricing",
"Cena izvēlētajiem (EUR/nakts)": "Price for selected (EUR/night)",
"Piemērot izvēlētajiem": "Apply to selected",
"Cenas atjauninātas izvēlētajiem datumiem.": "Prices updated for the selected dates.",
"Cenas korekcija dzēsta.": "Price override removed.",
"Datums": "Date",
"Cena": "Price",
"Nav cenu korekciju.": "No price overrides."
```

(`Dzēst` already exists — do not re-add.)

- [ ] **Step 7: Run the test**

Run: `php artisan test --compact --filter=ProductPricingTest`
Expected: PASS (7 tests).

- [ ] **Step 8: Pint + validate JSON + commit**

```bash
vendor/bin/pint --dirty --format agent
php -r 'json_decode(file_get_contents("lang/en.json"), true, 512, JSON_THROW_ON_ERROR); echo "valid\n";'
git add app/Livewire/Admin/Product/ProductPricing.php resources/views/livewire/admin/product/product-pricing.blade.php resources/js/app.js lang/en.json tests/Feature/Product/ProductPricingTest.php
git commit -m "feat(admin): per-date price overrides with calendar editor"
```

---

### Task 3: Add-ons manager

**Files:**
- Create: `app/Livewire/Admin/Product/ProductAddons.php`
- Create: `resources/views/livewire/admin/product/product-addons.blade.php`
- Modify: `routes/web.php`, `resources/views/livewire/admin/product/product-list.blade.php`, `lang/en.json`
- Test: `tests/Feature/Product/ProductAddonsTest.php`

- [ ] **Step 1: Write the failing test** — `tests/Feature/Product/ProductAddonsTest.php`:

```php
<?php

use App\Livewire\Admin\Product\ProductAddons;
use App\Models\Addon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAs(User::factory()->create()));

it('renders with the product', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->assertStatus(200)
        ->assertSet('product.id', $product->id);
});

it('creates a request-only addon with translated name and description', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->set('nameLv', 'Pirts')
        ->set('nameEn', 'Sauna')
        ->set('descLv', 'Pirts par papildu samaksu')
        ->set('descEn', 'Sauna for an extra fee')
        ->set('isActive', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('nameLv', '');

    $addon = Addon::where('product_id', $product->id)->first();
    expect($addon)->not->toBeNull()
        ->and($addon->getTranslation('name', 'lv'))->toBe('Pirts')
        ->and($addon->getTranslation('name', 'en'))->toBe('Sauna')
        ->and($addon->getTranslation('description', 'lv'))->toBe('Pirts par papildu samaksu')
        ->and($addon->price)->toBe(0)
        ->and($addon->pricing_type)->toBe(\App\Enums\AddonPricingType::PerStay)
        ->and($addon->is_active)->toBeTrue();
});

it('requires a Latvian name', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->set('nameLv', '')
        ->call('save')
        ->assertHasErrors(['nameLv']);
});

it('edits an existing addon', function () {
    $product = Product::factory()->create();
    $addon = Addon::factory()->for($product)->create(['name' => ['lv' => 'Old', 'en' => 'Old']]);

    Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->call('edit', $addon->id)
        ->assertSet('nameLv', 'Old')
        ->set('nameLv', 'Jauns')
        ->call('save')
        ->assertHasNoErrors();

    expect($addon->fresh()->getTranslation('name', 'lv'))->toBe('Jauns')
        ->and(Addon::where('product_id', $product->id)->count())->toBe(1);
});

it('toggles active and deletes an addon', function () {
    $product = Product::factory()->create();
    $addon = Addon::factory()->for($product)->create(['is_active' => true]);

    $component = Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->call('toggleActive', $addon->id);
    expect($addon->fresh()->is_active)->toBeFalse();

    $component->call('delete', $addon->id);
    expect(Addon::find($addon->id))->toBeNull();
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=ProductAddonsTest`
Expected: FAIL — component not found.

- [ ] **Step 3: Create `app/Livewire/Admin/Product/ProductAddons.php`:**

```php
<?php

namespace App\Livewire\Admin\Product;

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Component;

class ProductAddons extends Component
{
    public $product;

    public ?int $editingId = null;

    public string $nameLv = '';

    public string $nameEn = '';

    public string $descLv = '';

    public string $descEn = '';

    public bool $isActive = true;

    private ProductServices $productServices;

    private FlashMessageService $flashMessageService;

    public function boot(ProductServices $productServices, FlashMessageService $flashMessageService): void
    {
        $this->productServices = $productServices;
        $this->flashMessageService = $flashMessageService;
    }

    public function mount($product): void
    {
        $this->product = $this->productServices->getProductById($product);

        if (! $this->product) {
            $this->flashMessageService->error(__('Produkts nav atrasts.'));
            $this->redirect(route('dashboard.products'));
        }
    }

    public function save(): void
    {
        $this->validate([
            'nameLv' => 'required|string|max:120',
            'nameEn' => 'nullable|string|max:120',
            'descLv' => 'nullable|string|max:500',
            'descEn' => 'nullable|string|max:500',
        ]);

        $attributes = [
            'name' => ['lv' => $this->nameLv, 'en' => $this->nameEn],
            'description' => ['lv' => $this->descLv, 'en' => $this->descEn],
            'is_active' => $this->isActive,
        ];

        if ($this->editingId !== null) {
            Addon::where('product_id', $this->product->id)->whereKey($this->editingId)->update($attributes);
        } else {
            $this->product->addons()->create([
                ...$attributes,
                'price' => 0,
                'pricing_type' => AddonPricingType::PerStay,
                'order' => (int) $this->product->addons()->max('order') + 1,
            ]);
        }

        $this->resetForm();
        $this->flashMessageService->success(__('Papildinājums saglabāts.'));
    }

    public function edit(int $addonId): void
    {
        $addon = $this->product->addons()->findOrFail($addonId);

        $this->editingId = $addon->id;
        $this->nameLv = $addon->getTranslation('name', 'lv');
        $this->nameEn = $addon->getTranslation('name', 'en');
        $this->descLv = $addon->getTranslation('description', 'lv');
        $this->descEn = $addon->getTranslation('description', 'en');
        $this->isActive = $addon->is_active;
    }

    public function toggleActive(int $addonId): void
    {
        $addon = $this->product->addons()->findOrFail($addonId);
        $addon->update(['is_active' => ! $addon->is_active]);
    }

    public function delete(int $addonId): void
    {
        Addon::where('product_id', $this->product->id)->whereKey($addonId)->delete();
        $this->flashMessageService->success(__('Papildinājums dzēsts.'));
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'nameLv', 'nameEn', 'descLv', 'descEn']);
        $this->isActive = true;
    }

    public function render(): View
    {
        return view('livewire.admin.product.product-addons', [
            'addons' => $this->product->addons()->get(),
        ])->layout('layouts.admin.app');
    }
}
```

> Note: `getTranslation('name', 'en')` returns `''` when the `en` key is absent (Spatie returns an empty string for a missing translation by default in this app's config), so the `edit` test that only sets `lv` still behaves. The `nameEn`/`descLv`/`descEn` validations are `nullable`.

- [ ] **Step 4: Create `resources/views/livewire/admin/product/product-addons.blade.php`:**

```blade
<div class="max-w-2xl space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ __('Papildinājumi') }}</h1>
        <p class="text-neutral-600">{{ $product->getTranslation('title', app()->getLocale()) }}</p>
    </div>

    <div class="space-y-3 rounded-lg border p-4">
        <h2 class="font-medium">{{ $editingId ? __('Rediģēt papildinājumu') : __('Pievienot papildinājumu') }}</h2>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="mb-1 block text-sm">{{ __('Nosaukums (LV)') }}</label>
                <input type="text" wire:model="nameLv" class="w-full rounded-lg border p-2" />
                <x-input-error :messages="$errors->get('nameLv')" class="mt-1" />
            </div>
            <div>
                <label class="mb-1 block text-sm">{{ __('Nosaukums (EN)') }}</label>
                <input type="text" wire:model="nameEn" class="w-full rounded-lg border p-2" />
            </div>
            <div>
                <label class="mb-1 block text-sm">{{ __('Apraksts (LV)') }}</label>
                <textarea wire:model="descLv" rows="2" class="w-full rounded-lg border p-2"></textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm">{{ __('Apraksts (EN)') }}</label>
                <textarea wire:model="descEn" rows="2" class="w-full rounded-lg border p-2"></textarea>
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="isActive" /> {{ __('Aktīvs') }}
        </label>

        <div class="flex gap-2">
            <button type="button" wire:click="save" class="rounded-full bg-ss-dark px-5 py-2 text-white">{{ __('Saglabāt') }}</button>
            @if ($editingId)
                <button type="button" wire:click="resetForm" class="rounded-full border px-5 py-2">{{ __('Atcelt') }}</button>
            @endif
        </div>
    </div>

    <div class="space-y-2">
        @forelse ($addons as $addon)
            <div class="flex items-center justify-between rounded-lg border p-3">
                <div>
                    <p class="font-medium">{{ $addon->getTranslation('name', 'lv') }}</p>
                    <p class="text-sm text-neutral-500">{{ $addon->getTranslation('description', 'lv') }}</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <button type="button" wire:click="toggleActive({{ $addon->id }})"
                        class="{{ $addon->is_active ? 'text-green-700' : 'text-neutral-400' }}">
                        {{ $addon->is_active ? __('Aktīvs') : __('Neaktīvs') }}
                    </button>
                    <button type="button" wire:click="edit({{ $addon->id }})" class="text-blue-600 underline">{{ __('Rediģēt') }}</button>
                    <button type="button" wire:click="delete({{ $addon->id }})"
                        wire:confirm="{{ __('Dzēst šo papildinājumu?') }}" class="text-red-600 underline">{{ __('Dzēst') }}</button>
                </div>
            </div>
        @empty
            <p class="text-sm text-neutral-500">{{ __('Nav papildinājumu.') }}</p>
        @endforelse
    </div>
</div>
```

- [ ] **Step 5: Register the route in `routes/web.php`.** Add import `use App\Livewire\Admin\Product\ProductAddons;` and, in the dashboard group:

```php
Route::livewire('/product/{product:id}/addons', ProductAddons::class)->name('product.addons');
```

- [ ] **Step 6: Add the action link** in `resources/views/livewire/admin/product/product-list.blade.php`, after the `product.pricing` link:

```blade
                            <a href="{{ route('product.addons', $product) }}"
                                class="text-bg-ss-600 inline-flex items-center rounded-md p-2 transition-colors duration-200 hover:bg-gray-50 hover:text-gray-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5a2 2 0 0 1 1.41.59l7 7a2 2 0 0 1 0 2.82l-5.18 5.18a2 2 0 0 1-2.82 0l-7-7A2 2 0 0 1 3 9V4a1 1 0 0 1 1-1z" />
                                </svg>
                            </a>
```

- [ ] **Step 7: Add `lang/en.json` keys** (skip any that already exist — `Saglabāt`, `Dzēst`, `Atcelt`, `Aktīvs` may already exist; check):

```json
"Papildinājumi": "Add-ons",
"Pievienot papildinājumu": "Add an add-on",
"Rediģēt papildinājumu": "Edit add-on",
"Nosaukums (LV)": "Name (LV)",
"Nosaukums (EN)": "Name (EN)",
"Apraksts (LV)": "Description (LV)",
"Apraksts (EN)": "Description (EN)",
"Aktīvs": "Active",
"Neaktīvs": "Inactive",
"Rediģēt": "Edit",
"Papildinājums saglabāts.": "Add-on saved.",
"Papildinājums dzēsts.": "Add-on deleted.",
"Dzēst šo papildinājumu?": "Delete this add-on?",
"Nav papildinājumu.": "No add-ons."
```

- [ ] **Step 8: Run the test**

Run: `php artisan test --compact --filter=ProductAddonsTest`
Expected: PASS (6 tests).

- [ ] **Step 9: Pint + validate JSON + commit**

```bash
vendor/bin/pint --dirty --format agent
php -r 'json_decode(file_get_contents("lang/en.json"), true, 512, JSON_THROW_ON_ERROR); echo "valid\n";'
git add app/Livewire/Admin/Product/ProductAddons.php resources/views/livewire/admin/product/product-addons.blade.php routes/web.php resources/views/livewire/admin/product/product-list.blade.php lang/en.json tests/Feature/Product/ProductAddonsTest.php
git commit -m "feat(admin): request-only add-ons manager"
```

---

### Task 4: Blocked dates manager

**Files:**
- Create: `app/Livewire/Admin/Product/BlockedDates.php`
- Create: `resources/views/livewire/admin/product/blocked-dates.blade.php`
- Modify: `routes/web.php`, `resources/views/livewire/admin/product/product-list.blade.php`, `lang/en.json`
- Test: `tests/Feature/Product/BlockedDatesTest.php`

- [ ] **Step 1: Write the failing test** — `tests/Feature/Product/BlockedDatesTest.php`:

```php
<?php

use App\Livewire\Admin\Product\BlockedDates;
use App\Models\BlockedDate;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAs(User::factory()->create()));

it('renders with the product', function () {
    $product = Product::factory()->create();

    Livewire::test(BlockedDates::class, ['product' => $product->id])
        ->assertStatus(200)
        ->assertSet('product.id', $product->id);
});

it('adds a blocked date range', function () {
    $product = Product::factory()->create();

    Livewire::test(BlockedDates::class, ['product' => $product->id])
        ->set('startDate', '2026-08-01')
        ->set('endDate', '2026-08-05')
        ->set('reason', 'Apkope')
        ->call('addBlock')
        ->assertHasNoErrors()
        ->assertSet('startDate', '');

    $block = BlockedDate::where('product_id', $product->id)->first();
    expect($block)->not->toBeNull()
        ->and($block->start_date->toDateString())->toBe('2026-08-01')
        ->and($block->end_date->toDateString())->toBe('2026-08-05')
        ->and($block->reason)->toBe('Apkope');
});

it('rejects an end date before the start date', function () {
    $product = Product::factory()->create();

    Livewire::test(BlockedDates::class, ['product' => $product->id])
        ->set('startDate', '2026-08-05')
        ->set('endDate', '2026-08-01')
        ->call('addBlock')
        ->assertHasErrors(['endDate']);
});

it('removes a blocked range', function () {
    $product = Product::factory()->create();
    $block = BlockedDate::create([
        'product_id' => $product->id, 'start_date' => '2026-08-01', 'end_date' => '2026-08-05',
    ]);

    Livewire::test(BlockedDates::class, ['product' => $product->id])
        ->call('removeBlock', $block->id)
        ->assertHasNoErrors();

    expect(BlockedDate::find($block->id))->toBeNull();
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter=BlockedDatesTest`
Expected: FAIL — component not found.

- [ ] **Step 3: Create `app/Livewire/Admin/Product/BlockedDates.php`:**

```php
<?php

namespace App\Livewire\Admin\Product;

use App\Models\BlockedDate;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Component;

class BlockedDates extends Component
{
    public $product;

    public string $startDate = '';

    public string $endDate = '';

    public ?string $reason = null;

    private ProductServices $productServices;

    private FlashMessageService $flashMessageService;

    public function boot(ProductServices $productServices, FlashMessageService $flashMessageService): void
    {
        $this->productServices = $productServices;
        $this->flashMessageService = $flashMessageService;
    }

    public function mount($product): void
    {
        $this->product = $this->productServices->getProductById($product);

        if (! $this->product) {
            $this->flashMessageService->error(__('Produkts nav atrasts.'));
            $this->redirect(route('dashboard.products'));
        }
    }

    public function addBlock(): void
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'reason' => 'nullable|string|max:255',
        ]);

        $this->product->blockedDates()->create([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'reason' => $this->reason,
        ]);

        $this->reset(['startDate', 'endDate', 'reason']);
        $this->flashMessageService->success(__('Datumi bloķēti.'));
    }

    public function removeBlock(int $blockId): void
    {
        BlockedDate::where('product_id', $this->product->id)->whereKey($blockId)->delete();
        $this->flashMessageService->success(__('Bloķējums noņemts.'));
    }

    public function render(): View
    {
        return view('livewire.admin.product.blocked-dates', [
            'blocks' => $this->product->blockedDates()->orderBy('start_date')->get(),
        ])->layout('layouts.admin.app');
    }
}
```

- [ ] **Step 4: Create `resources/views/livewire/admin/product/blocked-dates.blade.php`:**

```blade
<div class="max-w-2xl space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ __('Bloķētie datumi') }}</h1>
        <p class="text-neutral-600">{{ $product->getTranslation('title', app()->getLocale()) }}</p>
    </div>

    <div class="space-y-3 rounded-lg border p-4">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="mb-1 block text-sm">{{ __('No') }}</label>
                <input type="date" wire:model="startDate" class="w-full rounded-lg border p-2" />
                <x-input-error :messages="$errors->get('startDate')" class="mt-1" />
            </div>
            <div>
                <label class="mb-1 block text-sm">{{ __('Līdz') }}</label>
                <input type="date" wire:model="endDate" class="w-full rounded-lg border p-2" />
                <x-input-error :messages="$errors->get('endDate')" class="mt-1" />
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm">{{ __('Iemesls') }}</label>
            <input type="text" wire:model="reason" class="w-full rounded-lg border p-2" />
        </div>
        <button type="button" wire:click="addBlock" class="rounded-full bg-ss-dark px-5 py-2 text-white">{{ __('Bloķēt datumus') }}</button>
    </div>

    <div class="space-y-2">
        @forelse ($blocks as $block)
            <div class="flex items-center justify-between rounded-lg border p-3 text-sm">
                <div>
                    <span class="font-medium">{{ $block->start_date->format('d.m.Y') }} – {{ $block->end_date->format('d.m.Y') }}</span>
                    @if ($block->reason)<span class="text-neutral-500"> · {{ $block->reason }}</span>@endif
                </div>
                <button type="button" wire:click="removeBlock({{ $block->id }})"
                    wire:confirm="{{ __('Noņemt šo bloķējumu?') }}" class="text-red-600 underline">{{ __('Dzēst') }}</button>
            </div>
        @empty
            <p class="text-sm text-neutral-500">{{ __('Nav bloķētu datumu.') }}</p>
        @endforelse
    </div>
</div>
```

- [ ] **Step 5: Register the route in `routes/web.php`.** Add import `use App\Livewire\Admin\Product\BlockedDates;` and, in the dashboard group:

```php
Route::livewire('/product/{product:id}/blocked-dates', BlockedDates::class)->name('product.blocked-dates');
```

- [ ] **Step 6: Add the action link** in `resources/views/livewire/admin/product/product-list.blade.php`, after the `product.addons` link:

```blade
                            <a href="{{ route('product.blocked-dates', $product) }}"
                                class="text-bg-ss-600 inline-flex items-center rounded-md p-2 transition-colors duration-200 hover:bg-gray-50 hover:text-gray-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="16" rx="2" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9h18M8 3v4M16 3v4M9 15l6-4M9 11l6 4" />
                                </svg>
                            </a>
```

- [ ] **Step 7: Add `lang/en.json` keys** (skip duplicates — `Iemesls`, `Dzēst` already exist):

```json
"Bloķētie datumi": "Blocked dates",
"No": "From",
"Līdz": "To",
"Bloķēt datumus": "Block dates",
"Datumi bloķēti.": "Dates blocked.",
"Bloķējums noņemts.": "Block removed.",
"Noņemt šo bloķējumu?": "Remove this block?",
"Nav bloķētu datumu.": "No blocked dates."
```

- [ ] **Step 8: Run the test**

Run: `php artisan test --compact --filter=BlockedDatesTest`
Expected: PASS (4 tests).

- [ ] **Step 9: Pint + validate JSON + commit**

```bash
vendor/bin/pint --dirty --format agent
php -r 'json_decode(file_get_contents("lang/en.json"), true, 512, JSON_THROW_ON_ERROR); echo "valid\n";'
git add app/Livewire/Admin/Product/BlockedDates.php resources/views/livewire/admin/product/blocked-dates.blade.php routes/web.php resources/views/livewire/admin/product/product-list.blade.php lang/en.json tests/Feature/Product/BlockedDatesTest.php
git commit -m "feat(admin): blocked dates manager"
```

---

### Task 5: Full-suite verification

**Files:** none (verification only)

- [ ] **Step 1: Run the new admin tooling tests**

Run: `php artisan test --compact --filter="ProductPricing|ProductAddons|BlockedDates"`
Expected: PASS (all green).

- [ ] **Step 2: Run the full test suite**

Run: `php artisan test --compact`
Expected: PASS (no regressions).

- [ ] **Step 3: Confirm Pint is clean on changed files**

Run: `vendor/bin/pint --dirty --format agent`
Expected: no changes (already formatted).

- [ ] **Step 4: Validate `lang/en.json`**

Run: `php -r 'json_decode(file_get_contents("lang/en.json"), true, 512, JSON_THROW_ON_ERROR); echo "valid\n";'`
Expected: prints `valid`.

- [ ] **Step 5: Build the frontend** (for the pricing calendar JS)

Run: `npm run build`
Expected: builds without error. (Manual UI check: visit a product's pricing page from the admin product list, pick dates, set a price.)

---

## Self-Review

**Scope coverage (user-approved Phase 4 scope):**
- Pricing editor — base price + min nights → Task 1; per-date overrides via calendar → Task 2. ✅
- Add-ons manager (request-only CRUD) → Task 3. ✅
- Blocked dates manager → Task 4. ✅
- Partial admin refunds → **explicitly out of scope** (deferred per the scoping decision). Not included — intentional.

**End-to-end effect:** after Task 1, an admin can set `base_price > 0`, which un-hides the product-page reservation widget (the Phase-3 gate) — so this phase makes houses bookable. The override calendar feeds `PricingService` (reads `product_prices`), add-ons feed the request-only checkboxes, blocked dates feed `AvailabilityService` (reads `blocked_dates`) — all consumers already exist from Phases 1–3.

**Placeholder scan:** no TBD/TODO; every code step shows complete code; every test step shows the test body.

**Type/name consistency:** component class names (`ProductPricing`, `ProductAddons`, `BlockedDates`), route names (`product.pricing`, `product.addons`, `product.blocked-dates`), method names (`saveBaseSettings`, `applyPriceToSelected`, `removeOverride`, `save`, `edit`, `toggleActive`, `delete`, `resetForm`, `addBlock`, `removeBlock`), and property names (`basePrice`, `minNights`, `selectedDates`, `overridePrice`, `nameLv/nameEn/descLv/descEn/isActive/editingId`, `startDate/endDate/reason`) are used consistently across tasks. Money helpers (`(int) round($e*100)` / `number_format($c/100,2)`), `AddonPricingType::PerStay`, and `updateOrCreate(['product_id','date'],['price'])` against the unique index are all consistent with the verified models.

**Known check for the implementer:** mirror `ProductRules`'s `mount($product)` + `ProductServices::getProductById($product)` exactly (the existing, working convention — tests mount with the product **id**). Do not switch to route-model-binding of a `Product` into `mount`.
