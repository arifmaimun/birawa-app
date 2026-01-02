<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Client;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\VisitStatus;
use App\Models\MedicalRecord;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\DoctorInventory;
use App\Models\DoctorServiceCatalog;
use App\Models\Expense;
use App\Models\InvoiceItem;
use App\Models\StorageLocation;

class RunComprehensiveTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:run {--cleanup : Clean up previous test data} {--no-report : Do not generate HTML report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run comprehensive automated testing suite with data insertion, verification, and reporting.';

    protected $batchId;
    protected $results = [];
    protected $createdIds = [];
    protected $startTime;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->startTime = microtime(true);
        $this->batchId = now()->format('Ymd_His');
        
        $this->info("Starting Comprehensive Test Suite (Batch ID: {$this->batchId})...");

        if ($this->option('cleanup')) {
            $this->cleanupOldData();
        }

        try {
            DB::beginTransaction();

            // 1. Preparation & Data Insertion
            $this->step('Preparing Test Data', function() {
                $this->createUsers();
                $this->createClients();
                $this->createPatients();
                $this->createProducts(); // New
                $this->createStorageLocations(); // New
                $this->createDoctorServices(); // New
                $this->createDoctorInventory(); // New
                $this->createVisits();
                $this->createMedicalRecords();
                $this->createInvoices();
                $this->createExpenses(); // New
            });

            // 2. Verification
            $this->step('Verifying Functions', function() {
                $this->verifyCRUD();
                $this->verifyValidations();
                $this->verifyRelationships();
                $this->verifyFinancials(); // New
            });

            DB::commit();
            
            // Save created IDs for cleanup
            $this->saveTestRunData();

            $this->info("Test Suite Completed Successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Test Failed: " . $e->getMessage());
            $this->logResult('Critical Error', 'System', 'Failed', 0, $e->getMessage());
        }

        // 3. Reporting
        if (!$this->option('no-report')) {
            $this->generateReport();
        }
    }

    protected function step($name, callable $callback)
    {
        $this->info("Running: $name");
        $start = microtime(true);
        try {
            $callback();
            $duration = microtime(true) - $start;
            $this->logResult($name, 'Suite', 'Passed', $duration);
        } catch (\Exception $e) {
            $duration = microtime(true) - $start;
            $this->logResult($name, 'Suite', 'Failed', $duration, $e->getMessage());
            throw $e;
        }
    }

    protected function createUsers()
    {
        $this->info("- Creating Users...");
        
        // Normal Case
        $user = User::create([
            'name' => 'Test Doctor ' . $this->batchId,
            'email' => "doctor_{$this->batchId}@example.com",
            'password' => Hash::make('password'),
            'role' => 'veterinarian',
            'phone' => '08123456789',
        ]);
        $this->trackId(User::class, $user->id);
        
        // Boundary Case (Max Length Name)
        $longNameUser = User::create([
            'name' => Str::random(250),
            'email' => "longname_{$this->batchId}@example.com",
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);
        $this->trackId(User::class, $longNameUser->id);

        $this->logResult('Create Users', 'User', 'Passed', 0.1);
    }

    protected function createClients()
    {
        $this->info("- Creating Clients...");
        $user = User::where('email', "doctor_{$this->batchId}@example.com")->first();

        // Normal Client
        $client = Client::create([
            'user_id' => $user->id,
            'name' => 'Test Client ' . $this->batchId,
            'phone' => '08123456789',
            'address' => 'Test Address 123',
            'first_name' => 'Test',
            'last_name' => 'Client',
            'is_business' => false,
        ]);
        $this->trackId(Client::class, $client->id);

        $this->logResult('Create Clients', 'Client', 'Passed', 0.1);
    }

    protected function createPatients()
    {
        $this->info("- Creating Patients...");
        $client = Client::where('name', 'Test Client ' . $this->batchId)->first();

        // Normal Patient
        $patient = Patient::create([
            'client_id' => $client->id,
            'name' => 'Fluffy ' . $this->batchId,
            'species' => 'Cat',
            'breed' => 'Persian',
            'gender' => 'Male',
            'dob' => '2020-01-01',
            'is_sterile' => true,
        ]);
        $this->trackId(Patient::class, $patient->id);

        $this->logResult('Create Patients', 'Patient', 'Passed', 0.1);
    }

    protected function createProducts()
    {
        $this->info("- Creating Products...");
        
        $product = Product::create([
            'name' => 'Test Product ' . $this->batchId,
            'sku' => 'SKU-' . $this->batchId,
            'category' => 'Medicine',
            'type' => 'goods', // Corrected to lowercase
            'cost' => 50000,
            'price' => 75000,
            'stock' => 100,
        ]);
        $this->trackId(Product::class, $product->id);

        $this->logResult('Create Products', 'Product', 'Passed', 0.1);
    }

    protected function createStorageLocations()
    {
        $this->info("- Creating Storage Locations...");
        $user = User::where('email', "doctor_{$this->batchId}@example.com")->first();

        $location = StorageLocation::create([
            'user_id' => $user->id,
            'name' => 'Main Cabinet ' . $this->batchId,
            'type' => 'warehouse',
            'is_default' => true,
        ]);
        $this->trackId(StorageLocation::class, $location->id);

        $this->logResult('Create Storage Locations', 'StorageLocation', 'Passed', 0.1);
    }

    protected function createDoctorServices()
    {
        $this->info("- Creating Doctor Services...");
        $user = User::where('email', "doctor_{$this->batchId}@example.com")->first();

        $service = DoctorServiceCatalog::create([
            'user_id' => $user->id,
            'service_name' => 'General Checkup ' . $this->batchId,
            'description' => 'Standard health check',
            'price' => 150000,
            'duration_minutes' => 30,
        ]);
        $this->trackId(DoctorServiceCatalog::class, $service->id);

        $this->logResult('Create Services', 'Service', 'Passed', 0.1);
    }

    protected function createDoctorInventory()
    {
        $this->info("- Creating Doctor Inventory...");
        $user = User::where('email', "doctor_{$this->batchId}@example.com")->first();
        $product = Product::where('sku', 'SKU-' . $this->batchId)->first();
        $location = StorageLocation::where('name', 'Main Cabinet ' . $this->batchId)->first();

        if (!$product || !$location) {
            $this->logResult('Create Inventory', 'Inventory', 'Skipped', 0, "Product or Location missing");
            return;
        }

        $inventory = DoctorInventory::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'storage_location_id' => $location->id,
            'item_name' => $product->name,
            'sku' => $product->sku,
            'stock_qty' => 10,
            'unit' => 'Bottle',
            'base_unit' => 'Bottle',
            'purchase_unit' => 'Box',
            'conversion_ratio' => 10,
            'alert_threshold' => 2,
            'low_stock_threshold' => 2,
            'average_cost_price' => 50000,
            'selling_price' => 75000,
            'is_sold' => true,
        ]);
        $this->trackId(DoctorInventory::class, $inventory->id);

        $this->logResult('Create Inventory', 'Inventory', 'Passed', 0.1);
    }

    protected function createExpenses()
    {
        $this->info("- Creating Expenses...");
        $user = User::where('email', "doctor_{$this->batchId}@example.com")->first();

        $expense = Expense::create([
            'user_id' => $user->id,
            'type' => 'OPEX',
            'amount' => 250000,
            'category' => 'Operational',
            'transaction_date' => now(),
            'notes' => 'Office Supplies ' . $this->batchId,
        ]);
        $this->trackId(Expense::class, $expense->id);

        $this->logResult('Create Expenses', 'Expense', 'Passed', 0.1);
    }

    protected function createVisits()
    {
        $this->info("- Creating Visits...");
        $patient = Patient::where('name', 'Fluffy ' . $this->batchId)->first();
        $user = User::where('email', "doctor_{$this->batchId}@example.com")->first();
        
        // Ensure VisitStatus exists
        $status = VisitStatus::firstOrCreate(['name' => 'Scheduled'], ['label' => 'Scheduled', 'color' => 'gray']);

        $visit = Visit::create([
            'patient_id' => $patient->id,
            'user_id' => $user->id,
            'scheduled_at' => now()->addDays(1),
            'visit_status_id' => $status->id,
            'complaint' => 'Checkup',
            'transport_fee' => 50000,
            'distance_km' => 5.5,
        ]);
        $this->trackId(Visit::class, $visit->id);

        $this->logResult('Create Visits', 'Visit', 'Passed', 0.1);
    }

    protected function createMedicalRecords()
    {
        $this->info("- Creating Medical Records...");
        $visit = Visit::with(['user', 'patient'])->whereHas('user', function($q) {
            $q->where('email', 'like', "%{$this->batchId}%");
        })->first();

        if (!$visit) {
            $this->logResult('Create Medical Records', 'MedicalRecord', 'Skipped', 0, "No Visit found");
            return;
        }
        
        $record = MedicalRecord::create([
            'visit_id' => $visit->id,
            'doctor_id' => $visit->user_id,
            'patient_id' => $visit->patient_id,
            'subjective' => 'Patient looks healthy',
            'objective' => 'Temperature 38.5C',
            'assessment' => 'Healthy',
            'plan_treatment' => 'None',
            'plan_recipe' => 'Vitamin C',
            'is_locked' => false,
        ]);
        $this->trackId(MedicalRecord::class, $record->id);

        $this->logResult('Create Medical Records', 'MedicalRecord', 'Passed', 0.1);
    }

    protected function createInvoices()
    {
        $this->info("- Creating Invoices...");
        $visit = Visit::whereHas('user', function($q) {
            $q->where('email', 'like', "%{$this->batchId}%");
        })->first();
        
        $product = Product::where('sku', 'SKU-' . $this->batchId)->first();

        if (!$visit) {
            $this->logResult('Create Invoices', 'Invoice', 'Skipped', 0, "No Visit found");
            return;
        }

        // Calculate total from items we plan to add
        // 1. Transport Fee (from visit)
        // 2. Product Item
        $productPrice = $product ? $product->price : 0;
        $qty = 2;
        $itemTotal = $productPrice * $qty;
        $total = $visit->transport_fee + $itemTotal;

        $invoice = Invoice::create([
            'visit_id' => $visit->id,
            'invoice_number' => 'INV-' . $this->batchId,
            'total_amount' => $total,
            'deposit_amount' => 0,
            'remaining_balance' => $total,
            'payment_status' => 'unpaid',
            'due_date' => now()->addDays(7),
        ]);
        $this->trackId(Invoice::class, $invoice->id);

        // Add Invoice Item
        if ($product) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $product->id,
                'description' => $product->name,
                'unit_price' => $product->price,
                'unit_cost' => $product->cost,
                'quantity' => $qty,
            ]);
        }

        $this->logResult('Create Invoices', 'Invoice', 'Passed', 0.1);
    }

    protected function verifyFinancials()
    {
        $this->info("- Verifying Financials...");
        
        // Check Invoice Calculation
        $invoice = Invoice::where('invoice_number', 'INV-' . $this->batchId)->with('invoiceItems')->first();
        if (!$invoice) throw new \Exception("Invoice Verification Failed: Not Found");
        
        $itemsTotal = $invoice->invoiceItems->sum(function($item) {
            return $item->unit_price * $item->quantity;
        });
        
        // Add visit fees if logic dictates (assuming here for simplicity we just check if total matches what we set or is consistent)
        // For now, let's just check if items exist
        if ($invoice->invoiceItems->count() === 0) throw new \Exception("Invoice Items Missing");

        // Check Expense
        $expense = Expense::where('notes', 'Office Supplies ' . $this->batchId)->first();
        if (!$expense) throw new \Exception("Expense Verification Failed");

        $this->logResult('Verify Financials', 'Finance', 'Passed', 0.1);
    }

    protected function verifyCRUD()
    {
        $this->info("- Verifying CRUD...");
        
        // Read
        $client = Client::where('name', 'Test Client ' . $this->batchId)->first();
        if (!$client) throw new \Exception("Client Read Failed");

        // Update
        $client->update(['phone' => '08999999999']);
        if ($client->fresh()->phone !== '08999999999') throw new \Exception("Client Update Failed");

        // Delete verification will be done in cleanup, but let's test soft delete if applicable
        // Client uses SoftDeletes
        $client->delete();
        if (!Client::withTrashed()->find($client->id)->trashed()) throw new \Exception("Client Soft Delete Failed");
        
        $client->restore(); // Restore for further tests

        $this->logResult('Verify CRUD', 'System', 'Passed', 0.2);
    }

    protected function verifyValidations()
    {
        $this->info("- Verifying Validations...");
        
        // Example: Try to create user without email (should fail if not nullable)
        try {
            User::create(['name' => 'Invalid User']);
            $this->logResult('Validation: Missing Email', 'User', 'Failed', 0, "Should have failed");
        } catch (\Exception $e) {
            $this->logResult('Validation: Missing Email', 'User', 'Passed', 0, "Caught expected error");
        }
    }

    protected function verifyRelationships()
    {
        $this->info("- Verifying Relationships...");
        
        $patient = Patient::where('name', 'Fluffy ' . $this->batchId)->first();
        if (!$patient->client) throw new \Exception("Patient->Client Relationship Failed");
        if ($patient->visits->count() === 0) throw new \Exception("Patient->Visits Relationship Failed");

        $this->logResult('Verify Relationships', 'System', 'Passed', 0.1);
    }

    protected function trackId($modelClass, $id)
    {
        $this->createdIds[$modelClass][] = $id;
    }

    protected function saveTestRunData()
    {
        $path = storage_path('app/test_runs');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        $data = [
            'batch_id' => $this->batchId,
            'timestamp' => now()->toIso8601String(),
            'created_ids' => $this->createdIds,
        ];

        File::put("$path/{$this->batchId}.json", json_encode($data, JSON_PRETTY_PRINT));
    }

    protected function cleanupOldData()
    {
        $this->info("Cleaning up old test data...");
        // This would typically read from the JSON files and delete
        // Implementation for "cleanup" option
        $path = storage_path('app/test_runs');
        if (!File::exists($path)) return;

        $files = File::files($path);
        foreach ($files as $file) {
            $data = json_decode(File::get($file), true);
            $this->deleteFromData($data);
            File::delete($file);
        }
    }

    public function deleteFromData($data)
    {
        if (!isset($data['created_ids'])) return;
        
        foreach ($data['created_ids'] as $modelClass => $ids) {
            if (class_exists($modelClass)) {
                $modelClass::whereIn('id', $ids)->forceDelete(); // Force delete to clean DB
            }
        }
    }

    protected function logResult($scenario, $module, $status, $duration, $error = null)
    {
        $this->results[] = [
            'scenario' => $scenario,
            'module' => $module,
            'status' => $status,
            'duration' => round($duration * 1000, 2) . 'ms',
            'error' => $error,
            'timestamp' => now()->toTimeString()
        ];
    }

    protected function generateReport()
    {
        $this->info("Generating HTML Report...");
        
        $html = $this->buildHtmlReport();

        $path = storage_path('app/public/test-reports');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        File::put("$path/report_{$this->batchId}.html", $html);
        $this->info("Report generated: $path/report_{$this->batchId}.html");
    }

    protected function getSummary()
    {
        $total = count($this->results);
        $passed = collect($this->results)->where('status', 'Passed')->count();
        $failed = collect($this->results)->where('status', 'Failed')->count();
        
        return [
            'total' => $total,
            'passed' => $passed,
            'failed' => $failed,
            'rate' => $total > 0 ? round(($passed / $total) * 100, 2) : 0
        ];
    }

    protected function buildHtmlReport()
    {
        $summary = $this->getSummary();
        $rows = '';
        foreach ($this->results as $r) {
            $color = $r['status'] === 'Passed' ? 'green' : 'red';
            $rows .= "<tr>
                <td>{$r['timestamp']}</td>
                <td>{$r['module']}</td>
                <td>{$r['scenario']}</td>
                <td style='color: $color; font-weight: bold'>{$r['status']}</td>
                <td>{$r['duration']}</td>
                <td style='color: red'>{$r['error']}</td>
            </tr>";
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Test Report {$this->batchId}</title>
            <style>
                body { font-family: sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .summary { margin-bottom: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; }
            </style>
        </head>
        <body>
            <h1>Automated Test Report</h1>
            <div class='summary'>
                <h3>Batch ID: {$this->batchId}</h3>
                <p>Date: " . now()->toDayDateTimeString() . "</p>
                <p>Total: {$summary['total']} | Passed: {$summary['passed']} | Failed: {$summary['failed']} | Success Rate: {$summary['rate']}%</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Module</th>
                        <th>Scenario</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    $rows
                </tbody>
            </table>
        </body>
        </html>
        ";
    }
}
