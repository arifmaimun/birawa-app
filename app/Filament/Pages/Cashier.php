<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\DoctorInventory;
use App\Services\InventoryService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cashier extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static string $view = 'filament.pages.cashier';

    public $barcode = '';
    public $cart = [];
    public $total = 0;
    public $paymentMethod = 'cash';
    public $amountPaid = 0;
    public $customerName = 'Walk-in Customer';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\CashierStats::class,
        ];
    }

    public function mount()
    {
        $this->cart = [];
    }

    public function scan()
    {
        if (empty($this->barcode)) return;

        $product = Product::where('sku', $this->barcode)->first();
        
        if (!$product) {
             Notification::make()->warning()->title('Product not found')->send();
             $this->barcode = '';
             return;
        }
        
        $this->addToCart($product);
        $this->barcode = '';
    }

    public function addToCart(Product $product)
    {
        if (isset($this->cart[$product->id])) {
            $this->cart[$product->id]['qty']++;
        } else {
            $this->cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => 1,
            ];
        }
        $this->calculateTotal();
    }

    public function updateQty($productId, $qty)
    {
        if ($qty <= 0) {
            unset($this->cart[$productId]);
        } else {
            $this->cart[$productId]['qty'] = $qty;
        }
        $this->calculateTotal();
    }

    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['qty'];
        }
    }

    public function checkout(InventoryService $inventoryService)
    {
        if (empty($this->cart)) {
            Notification::make()->warning()->title('Cart is empty')->send();
            return;
        }

        DB::transaction(function () use ($inventoryService) {
            // Create Invoice
            $invoice = Invoice::create([
                'user_id' => Auth::id(), // Cashier
                'patient_id' => null, // Walk-in
                'total_amount' => $this->total,
                'payment_status' => 'paid',
                'stock_committed' => true,
                'status' => 'issued',
                'due_date' => now(),
            ]);

            foreach ($this->cart as $item) {
                // Find inventory for this user to deduct stock
                $inventory = DoctorInventory::where('user_id', Auth::id())
                    ->where('product_id', $item['id'])
                    ->first();
                
                $inventoryId = $inventory ? $inventory->id : null;
                
                $unitCost = 0;
                if ($inventory) {
                    $unitCost = $inventory->average_cost_price;
                } else {
                    $prod = Product::find($item['id']);
                    if ($prod) {
                        $unitCost = $prod->cost;
                    }
                }

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['id'],
                    'description' => $item['name'],
                    'doctor_inventory_id' => $inventoryId,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'unit_cost' => $unitCost,
                ]);

                // Deduct Stock
                if ($inventoryId) {
                    try {
                        $inventoryService->commitStock($inventoryId, $item['qty']);
                    } catch (\Exception $e) {
                        // If insufficient stock, maybe warn? For now we proceed as it is a forced sale
                        Notification::make()->warning()->title("Stock warning for {$item['name']}: " . $e->getMessage())->send();
                    }
                } else {
                    // Deduct from global product stock if no inventory assigned
                    $product = Product::find($item['id']);
                    if ($product) {
                        $product->decrement('stock', $item['qty']);
                    }
                }
            }

            // Record Payment
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $this->total,
                'method' => $this->paymentMethod,
                'paid_at' => now(),
                'notes' => 'POS Sale - ' . $this->customerName,
            ]);
        });

        Notification::make()->success()->title('Sale completed')->send();
        $this->cart = [];
        $this->total = 0;
        $this->amountPaid = 0;
        $this->barcode = '';
    }
}
