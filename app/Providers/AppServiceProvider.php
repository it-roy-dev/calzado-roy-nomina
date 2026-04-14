<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use LaravelLang\Routes\Events\LocaleHasBeenSetEvent;
use App\Services\NominaService;
use Spatie\Backup\Tasks\Backup\DbDumperFactory;
use App\DbDumpers\PostgreSqlWithPassword;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    $this->app->singleton(NominaService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Log::info('AppServiceProvider boot ejecutado');
    
        DbDumperFactory::extend('pgsql', function() {
            \Log::info('PostgreSqlWithPassword instanciado');
            return new PostgreSqlWithPassword();
        });

        // Parche para pg_dump en Windows con WAMP
        DbDumperFactory::extend('pgsql', function() {
            return new PostgreSqlWithPassword();
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Event::listen(static function (LocaleHasBeenSetEvent $event) {
            $lang = $event->locale->code;
            \Illuminate\Support\Facades\Log::info('Locale set to: ' . $lang);
        });
    }
}
