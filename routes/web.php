<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\Feature\AddFeature;
use App\Livewire\Admin\Feature\EditFeature;
use App\Livewire\Admin\Feature\FeatureList;
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
        Route::get('/', HomeController::class)->name('home');

        Route::get('/dizaina-majas-sauna-un-dzakuzis', [ProductController::class, 'index'])->name('products');

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

                Route::get('/product/add', AddProduct::class)->name('product.add');
                Route::get('/product/{product:id}/edit', EditProduct::class)->name('product.edit');
                Route::get('/product/{product:id}/images', ProductImages::class)->name('product.images');
                Route::get('/product/{product:id}/features', ProductFeatures::class)->name('product.features');
                Route::get('/product/{product:id}/rules', ProductRules::class)->name('product.rules');

                Route::get('/features', FeatureList::class)->name('dashboard.features');
                Route::get('/feature/add', AddFeature::class)->name('dashboard.feature.add');
                Route::get('/feature/{feature}/edit', EditFeature::class)->name('dashboard.feature.edit');

                Route::get('/rules', RuleList::class)->name('dashboard.rules');
                Route::get('/rule/add', AddRule::class)->name('dashboard.rule.add');
                Route::get('/rule/{rule}/edit', EditRule::class)->name('dashboard.rule.edit');

                Route::get('/newsletter', NewsletterList::class)->name('newsletter.list');
            });
        });

        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle);
        });

        Livewire::setScriptRoute(function ($handle) {
            return Route::get('/livewire/livewire.js', $handle);
        });

        require __DIR__.'/auth.php';

        Route::get('/{product}', [ProductController::class, 'show'])->name('product');
    }
);
