<?php

use App\Http\Controllers\Manual\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Manual Application Routes
|--------------------------------------------------------------------------
|
| These routes serve the manual replacement for the Filament Admin Panel.
| They are prefixed with config('migration.route_prefix') usually 'app'.
|
*/

Route::middleware(['web', 'auth'])
    ->prefix(config('migration.route_prefix', 'app'))
    ->name('manual.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Inventory Module
        if (config('migration.modules.inventory', false)) {
            Route::resource('products', \App\Http\Controllers\Manual\Inventory\ProductController::class);
            Route::resource('inventory', \App\Http\Controllers\Manual\Inventory\DoctorInventoryController::class);
            Route::resource('storage-locations', \App\Http\Controllers\Manual\Inventory\StorageLocationController::class);
        }

        // Clinical Module
        if (config('migration.modules.clinical', false)) {
            Route::resource('clients', \App\Http\Controllers\Manual\Clinical\ClientController::class);
            Route::resource('patients', \App\Http\Controllers\Manual\Clinical\PatientController::class);
            Route::resource('visits', \App\Http\Controllers\Manual\Clinical\VisitController::class);
            Route::resource('medical-records', \App\Http\Controllers\Manual\Clinical\MedicalRecordController::class);
        }

        // Finance Module
        if (config('migration.modules.finance', false)) {
            Route::resource('invoices', \App\Http\Controllers\Manual\Finance\InvoiceController::class);
        }

        // Settings & Profile
        if (config('migration.modules.settings', false)) {
            Route::get('profile', [\App\Http\Controllers\Manual\ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('profile', [\App\Http\Controllers\Manual\ProfileController::class, 'update'])->name('profile.update');

            // Master Data
            Route::resource('diagnoses', \App\Http\Controllers\Manual\Settings\DiagnosisController::class);
            Route::resource('consent-templates', \App\Http\Controllers\Manual\Settings\ConsentTemplateController::class);
            Route::resource('message-templates', \App\Http\Controllers\Manual\Settings\MessageTemplateController::class);
            Route::resource('visit-statuses', \App\Http\Controllers\Manual\Settings\VisitStatusController::class);
            Route::resource('vital-sign-settings', \App\Http\Controllers\Manual\Settings\VitalSignSettingController::class);

            // Audit Logs (Read Only)
            Route::get('audit-logs', [\App\Http\Controllers\Manual\Settings\AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('audit-logs/{auditLog}', [\App\Http\Controllers\Manual\Settings\AuditLogController::class, 'show'])->name('audit-logs.show');
        }

        // Management Module (Admin)
        if (config('migration.modules.management', false)) {
            Route::resource('users', \App\Http\Controllers\Manual\Management\UserController::class);
        }

    });
