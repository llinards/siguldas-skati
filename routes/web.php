<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


Route::group(
    [
        'prefix'     => LaravelLocalization::setLocale(),
        'middleware' => [
            'localeSessionRedirect',
            'localizationRedirect',
            'localeViewPath',
        ],
    ],
    function () {
        Route::get('/', HomeController::class)->name('home');

        Route::get('/dizaina-majas-un-sauna', [ProductController::class, 'index'])->name('products');

        Route::get('/kontakti', function () {
            return view('contacts');
        })->name('contacts');

        Route::get('/privatuma-politika', function () {
            return view('privacy-policy');
        })->name('privacy-policy');

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->middleware(['auth', 'verified'])->name('dashboard');

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
