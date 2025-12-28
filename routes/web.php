<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Routes (No Auth Required)
Route::get('/r/{token}', [App\Http\Controllers\ReferralController::class, 'showPublic'])->name('referrals.public');
Route::get('/i/{token}', [App\Http\Controllers\InvoiceController::class, 'showPublic'])->name('invoices.public');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Profile & Settings
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // Shift Management
    Route::resource('shifts', App\Http\Controllers\DoctorShiftController::class)->only(['index', 'store', 'update', 'destroy']);

    // Template Management
    Route::resource('consent-templates', App\Http\Controllers\ConsentTemplateController::class);

    // Referrals
    Route::resource('referrals', App\Http\Controllers\ReferralController::class);

    Route::resource('owners', OwnerController::class);
    Route::resource('patients', PatientController::class);
    
    Route::get('visits/calendar-events', [VisitController::class, 'calendarEvents'])->name('visits.calendar-events');
    Route::resource('visits', VisitController::class);
    Route::patch('/visits/{visit}/status', [VisitController::class, 'updateStatus'])->name('visits.update-status');

    Route::resource('products', ProductController::class);

    // Medical Records
    Route::get('/visits/{visit}/medical-record/create', [MedicalRecordController::class, 'create'])->name('medical-records.create');
    Route::post('/visits/{visit}/medical-record', [MedicalRecordController::class, 'store'])->name('medical-records.store');
    Route::get('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'show'])->name('medical-records.show');
    Route::post('/medical-records/{medicalRecord}/request-access', [MedicalRecordController::class, 'requestAccess'])->name('medical-records.request-access');
    Route::patch('/access-requests/{accessRequest}/approve', [MedicalRecordController::class, 'approveAccess'])->name('access-requests.approve');

    // Onboarding
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/check', [OnboardingController::class, 'checkOrRegister'])->name('onboarding.check');

    // Inventory
    Route::resource('inventory', \App\Http\Controllers\DoctorInventoryController::class);
    Route::get('inventory/{doctorInventory}/restock', [\App\Http\Controllers\DoctorInventoryController::class, 'restockForm'])->name('inventory.restock');
    Route::post('inventory/{doctorInventory}/restock', [\App\Http\Controllers\DoctorInventoryController::class, 'restock'])->name('inventory.restock.store');

    // Expenses
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    
    // Finance Dashboard
    Route::get('/finance', [\App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');

    // Invoices
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
    Route::post('visits/{visit}/create-invoice', [\App\Http\Controllers\InvoiceController::class, 'createFromVisit'])->name('invoices.createFromVisit');
    Route::patch('invoices/{invoice}/mark-paid', [\App\Http\Controllers\InvoiceController::class, 'markPaid'])->name('invoices.markPaid');
});
