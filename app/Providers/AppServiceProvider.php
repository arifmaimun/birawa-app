<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        Paginator::defaultView('vendor.pagination.simple-tailwind');

        \App\Models\DoctorInventory::observe(\App\Observers\AuditObserver::class);
        \App\Models\InventoryTransfer::observe(\App\Observers\AuditObserver::class);
        \App\Models\Product::observe(\App\Observers\AuditObserver::class);
        \App\Models\DoctorInventoryBatch::observe(\App\Observers\AuditObserver::class);
    }
}
