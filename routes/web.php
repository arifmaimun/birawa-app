<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
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
Route::get('/react-demo', function () {
    return view('react_demo');
})->name('react.demo');
Route::get('/r/{token}', [App\Http\Controllers\ReferralController::class, 'showPublic'])->name('referrals.public');
Route::get('/i/{token}', [App\Http\Controllers\InvoiceController::class, 'showPublic'])->name('invoices.public');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Profile & Settings
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::put('/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Application Settings Hub
    Route::view('/settings', 'settings.index')->name('settings.index');
    
    // Shift Management
    Route::resource('shifts', App\Http\Controllers\DoctorShiftController::class)->only(['index', 'store', 'update', 'destroy']);

    // Template Management
    Route::resource('consent-templates', App\Http\Controllers\ConsentTemplateController::class);
    Route::resource('message-templates', App\Http\Controllers\MessageTemplateController::class);

    // Referrals
    Route::resource('referrals', App\Http\Controllers\ReferralController::class);

    Route::resource('clients', ClientController::class);
    Route::resource('patients', PatientController::class);
    
    Route::get('visits/calendar-events', [VisitController::class, 'calendarEvents'])->name('visits.calendar-events');
    Route::get('visits/calendar', [VisitController::class, 'calendar'])->name('visits.calendar');
    Route::get('visits/recommend-route', [VisitController::class, 'recommendRoute'])->name('visits.recommend-route');
    Route::post('visits/{visit}/start-trip', [VisitController::class, 'startTrip'])->name('visits.start-trip');
    Route::post('visits/{visit}/end-trip', [VisitController::class, 'endTrip'])->name('visits.end-trip');
    Route::resource('visits', VisitController::class);
    Route::patch('/visits/{visit}/status', [VisitController::class, 'updateStatus'])->name('visits.update-status');

    // Settings
    Route::resource('visit-statuses', App\Http\Controllers\VisitStatusController::class);
    Route::resource('vital-sign-settings', App\Http\Controllers\VitalSignSettingController::class);

    
    Route::get('products/check-sku', [ProductController::class, 'checkSku'])->name('products.check-sku');
    Route::resource('products', ProductController::class);

    // Medical Records
    Route::post('/diagnoses', [App\Http\Controllers\DiagnosisController::class, 'store'])->name('diagnoses.store');
    Route::get('/visits/{visit}/medical-record/create', [MedicalRecordController::class, 'create'])->name('medical-records.create');
    Route::post('/visits/{visit}/medical-record', [MedicalRecordController::class, 'store'])->name('medical-records.store');
    Route::get('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'show'])->name('medical-records.show');
    Route::post('/medical-records/{medicalRecord}/request-access', [MedicalRecordController::class, 'requestAccess'])->name('medical-records.request-access');
    Route::patch('/access-requests/{accessRequest}/approve', [MedicalRecordController::class, 'approveAccess'])->name('access-requests.approve');

    // Onboarding
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/check', [OnboardingController::class, 'checkOrRegister'])->name('onboarding.check');

    // Inventory
    Route::get('inventory/items/search', [\App\Http\Controllers\DoctorInventoryController::class, 'searchItems'])->name('inventory.items.search');
    Route::get('inventory/expiry-report', [\App\Http\Controllers\DoctorInventoryController::class, 'expiryReport'])->name('inventory.expiry-report');
    Route::get('inventory/transactions', [\App\Http\Controllers\InventoryTransactionController::class, 'index'])->name('inventory.transactions.index');
    Route::resource('storage-locations', \App\Http\Controllers\StorageLocationController::class); // Added
    Route::resource('inventory', \App\Http\Controllers\DoctorInventoryController::class);
    Route::get('inventory/{doctorInventory}/restock', [\App\Http\Controllers\DoctorInventoryController::class, 'restockForm'])->name('inventory.restock');
    Route::post('inventory/{doctorInventory}/restock', [\App\Http\Controllers\DoctorInventoryController::class, 'restock'])->name('inventory.restock.store');

    // Inventory Transfers
    Route::resource('inventory-transfers', \App\Http\Controllers\InventoryTransferController::class);
    Route::post('internal-transfers', [\App\Http\Controllers\InternalTransferController::class, 'store'])->name('internal-transfers.store');
    Route::patch('inventory-transfers/{inventoryTransfer}/approve', [\App\Http\Controllers\InventoryTransferController::class, 'approve'])->name('inventory-transfers.approve');
    Route::patch('inventory-transfers/{inventoryTransfer}/reject', [\App\Http\Controllers\InventoryTransferController::class, 'reject'])->name('inventory-transfers.reject');

    // Stock Opname
    Route::resource('stock-opnames', \App\Http\Controllers\StockOpnameController::class);
    Route::post('stock-opnames/{stockOpname}/complete', [\App\Http\Controllers\StockOpnameController::class, 'complete'])->name('stock-opnames.complete');

    // Social Features (Friendships & Chat)
    Route::get('/friends', [App\Http\Controllers\FriendshipController::class, 'index'])->name('friends.index');
    Route::get('/friends/search', [App\Http\Controllers\FriendshipController::class, 'search'])->name('friends.search');
    Route::post('/friends/request', [App\Http\Controllers\FriendshipController::class, 'sendRequest'])->name('friends.request');
    Route::patch('/friends/{friendship}/accept', [App\Http\Controllers\FriendshipController::class, 'acceptRequest'])->name('friends.accept');

    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/messages/{user}', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');

    Route::get('stock-opnames/{stockOpname}/export', [\App\Http\Controllers\StockOpnameController::class, 'export'])->name('stock-opnames.export');
    Route::post('stock-opnames/{stockOpname}/items/{item}', [\App\Http\Controllers\StockOpnameController::class, 'updateItem'])->name('stock-opnames.items.update');

    // Services
    Route::resource('services', \App\Http\Controllers\DoctorServiceController::class);

    // Expenses
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    
    // Finance Dashboard
    Route::get('/finance', [\App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');

    // Invoices
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
    Route::post('visits/{visit}/create-invoice', [\App\Http\Controllers\InvoiceController::class, 'createFromVisit'])->name('invoices.createFromVisit');
    Route::post('invoices/{invoice}/payments', [\App\Http\Controllers\InvoiceController::class, 'storePayment'])->name('invoices.payments.store');
    Route::delete('invoices/{invoice}/payments/{payment}', [\App\Http\Controllers\InvoiceController::class, 'destroyPayment'])->name('invoices.payments.destroy');

    // Social Features (Friends & Chat)
    // Friends
    Route::get('/friends', [App\Http\Controllers\FriendshipController::class, 'index'])->name('friends.index');
    Route::get('/friends/search', [App\Http\Controllers\FriendshipController::class, 'search'])->name('friends.search');
    Route::post('/friends/request', [App\Http\Controllers\FriendshipController::class, 'sendRequest'])->name('friends.request');
    Route::post('/friends/{friendship}/accept', [App\Http\Controllers\FriendshipController::class, 'acceptRequest'])->name('friends.accept');
    Route::delete('/friends/{friendship}', [App\Http\Controllers\FriendshipController::class, 'destroy'])->name('friends.destroy');
    
    // Chat UI
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/messages/{user}', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');

    // Legacy/API Message Routes (optional, keeping for compatibility if needed)
    Route::get('/messages/unread-count', [App\Http\Controllers\MessageController::class, 'unreadCount'])->name('messages.unread-count');
    Route::patch('/messages/{message}/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.mark-read');

    // Admin Panel
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::get('audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    });

});

require __DIR__.'/manual.php';
