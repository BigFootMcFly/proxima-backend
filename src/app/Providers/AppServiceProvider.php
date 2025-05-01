<?php

namespace App\Providers;

use App\Validators\SnowflakeValidator;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // load telescope only if not in production
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // setting default color for Infolist textEntry objects
        TextEntry::configureUsing(function (TextEntry $textEntry): void {
            $textEntry->color('info');
        });

        // register custom validator
        SnowflakeValidator::registerValidator();
    }
}
