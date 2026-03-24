<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\Feature\AddFeature;
use App\Livewire\Admin\Feature\EditFeature;
use App\Livewire\Admin\Feature\FeatureList;
use App\Livewire\Admin\Gallery\AddGallery;
use App\Livewire\Admin\Gallery\EditGallery;
use App\Livewire\Admin\Gallery\GalleryImages;
use App\Livewire\Admin\Gallery\GalleryList;
use App\Livewire\Admin\NewsletterList;
use App\Livewire\Admin\Product\AddProduct;
use App\Livewire\Admin\Product\EditProduct;
use App\Livewire\Admin\Product\ProductFeatures;
use App\Livewire\Admin\Product\ProductImages;
use App\Livewire\Admin\Product\ProductRules;
use App\Livewire\Admin\Rule\AddRule;
use App\Livewire\Admin\Rule\EditRule;
use App\Livewire\Admin\Rule\RuleList;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [
            'localeSessionRedirect',
            'localizationRedirect',
            'localeViewPath',
        ],
    ],
    function () {
        Livewire::setUpdateRoute(static function ($handle, $path) {
            return Route::post($path, $handle);
        });

        Livewire::setScriptRoute(static function ($handle, $path) {
            return Route::get($path, $handle);
        });

        Route::get('/', HomeController::class)->name('home');

        Route::get('/booking', static function () {
            return redirect('https://www.booking.com/hotel/lv/siguldas-skati-sigulda.en-gb.html?label=gen173rf-10CAsoigFCFnNpZ3VsZGFzLXNrYXRpLXNpZ3VsZGFIGlgDaIoBiAEBmAEzuAEHyAEN2AED6AEB-AEBiAIBogIQc2lndWxkYXNza2F0aS5sdqgCAbgC8P6-xwbAAgHSAiQxYWQ0YmE0Mi0yN2YwLTQxYWItYjQ0Yi0xM2U3YzNkZDJjNDDYAgHgAgE&sid=86ee30aeab7a65423e93b60cdc1321d2&dist=0&group_adults=2&group_children=0&keep_landing=1&no_rooms=1&sb_price_type=total&type=total&');
        });

        Route::get('/dizaina-majas-sauna-un-dzakuzis', [ProductController::class, 'index'])->name('products');

        Route::get('/noteikumi', function () {
            return view('terms');
        })->name('terms');

        Route::get('/buj', function () {
            return view('faq');
        })->name('faq');

        Route::get('/kontakti', function () {
            return view('contacts');
        })->name('contacts');

        Route::get('/privatuma-politika', function () {
            return view('privacy-policy');
        })->name('privacy-policy');

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            Route::prefix('dashboard')->group(function () {
                Route::get('/', function () {
                    return view('admin.dashboard');
                })->name('dashboard');

                Route::livewire('/product/add', AddProduct::class)->name('product.add');
                Route::livewire('/product/{product:id}/edit', EditProduct::class)->name('product.edit');
                Route::livewire('/product/{product:id}/images', ProductImages::class)->name('product.images');
                Route::livewire('/product/{product:id}/features', ProductFeatures::class)->name('product.features');
                Route::livewire('/product/{product:id}/rules', ProductRules::class)->name('product.rules');

                Route::livewire('/features', FeatureList::class)->name('dashboard.features');
                Route::livewire('/feature/add', AddFeature::class)->name('dashboard.feature.add');
                Route::livewire('/feature/{feature}/edit', EditFeature::class)->name('dashboard.feature.edit');

                Route::livewire('/rules', RuleList::class)->name('dashboard.rules');
                Route::livewire('/rule/add', AddRule::class)->name('dashboard.rule.add');
                Route::livewire('/rule/{rule}/edit', EditRule::class)->name('dashboard.rule.edit');

                Route::livewire('/galleries', GalleryList::class)->name('dashboard.galleries');
                Route::livewire('/gallery/add', AddGallery::class)->name('dashboard.gallery.add');
                Route::livewire('/gallery/{gallery}/edit', EditGallery::class)->name('dashboard.gallery.edit');
                Route::livewire('/gallery/{gallery:id}/images', GalleryImages::class)->name('dashboard.gallery.images');

                Route::livewire('/newsletter', NewsletterList::class)->name('newsletter.list');
            });
        });

        require __DIR__.'/auth.php';

        Route::get('/{product}', [ProductController::class, 'show'])->name('product');
    }
);
