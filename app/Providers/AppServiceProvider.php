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

        $models = [
            \App\Models\AccessRequest::class,
            \App\Models\Client::class,
            \App\Models\ClientAddress::class,
            \App\Models\ConsentTemplate::class,
            \App\Models\Diagnosis::class,
            \App\Models\DoctorInventory::class,
            \App\Models\DoctorInventoryBatch::class,
            \App\Models\DoctorProfile::class,
            \App\Models\DoctorServiceCatalog::class,
            \App\Models\DoctorShift::class,
            \App\Models\EngagementTask::class,
            \App\Models\Expense::class,
            \App\Models\FormOption::class,
            \App\Models\InventoryTransaction::class,
            \App\Models\InventoryTransfer::class,
            \App\Models\InventoryTransferItem::class,
            \App\Models\Invoice::class,
            \App\Models\InvoiceItem::class,
            \App\Models\InvoicePayment::class,
            \App\Models\MedicalConsent::class,
            \App\Models\MedicalRecord::class,
            \App\Models\MedicalUsageLog::class,
            \App\Models\MessageTemplate::class,
            \App\Models\Patient::class,
            \App\Models\Product::class,
            \App\Models\Referral::class,
            \App\Models\StockOpname::class,
            \App\Models\StockOpnameItem::class,
            \App\Models\StorageLocation::class,
            \App\Models\User::class,
            \App\Models\Visit::class,
            \App\Models\VisitStatus::class,
            \App\Models\VitalSign::class,
            \App\Models\VitalSignSetting::class,
        ];

        foreach ($models as $model) {
            $model::observe(\App\Observers\AuditObserver::class);
        }
    }
}
