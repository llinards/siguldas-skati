<?php

namespace App\Providers;

use App\Events\BookingConfirmed;
use App\Listeners\SendBookingConfirmationNotifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    use LoadsTranslatedCachedRoutes;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::automaticallyEagerLoadRelationships();
        DB::prohibitDestructiveCommands(app()->isProduction());
        RouteServiceProvider::loadCachedRoutesUsing(fn () => $this->loadCachedRoutes());
        Translatable::fallback(config('app.fallback_locale'));
        Event::listen(BookingConfirmed::class, SendBookingConfirmationNotifications::class);
    }
}
