<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Config;

class FilamentTimezoneServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // This ensures that when Filament renders, it uses the 'app.display_timezone' config
        // which is set by our Middleware based on user preference.
        // If not set, it falls back to 'app.timezone' (UTC).

        TextColumn::configureUsing(function (TextColumn $column) {
            $column->timezone(Config::get('app.display_timezone', Config::get('app.timezone')));
        });

        DateTimePicker::configureUsing(function (DateTimePicker $component) {
            $component->timezone(Config::get('app.display_timezone', Config::get('app.timezone')));
        });

        TextEntry::configureUsing(function (TextEntry $entry) {
            $entry->timezone(Config::get('app.display_timezone', Config::get('app.timezone')));
        });
    }
}
