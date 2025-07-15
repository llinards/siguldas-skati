<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\Product\AddProduct;
use App\Livewire\Admin\Product\EditProduct;
use App\Livewire\Admin\Product\ProductFeatures;
use App\Livewire\Admin\Product\ProductImages;
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

        Route::get('/dizaina-majas-un-sauna', [ProductController::class, 'index'])->name('products');

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

                Route::get('/features', App\Livewire\Admin\Feature\FeatureList::class)->name('dashboard.features');
                Route::get('/feature/add', App\Livewire\Admin\Feature\AddFeature::class)->name('dashboard.feature.add');
                Route::get('/feature/{feature}/edit', App\Livewire\Admin\Feature\EditFeature::class)->name('dashboard.feature.edit');
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
