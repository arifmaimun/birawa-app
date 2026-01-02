<?php

namespace App\Http\Controllers;

use App\Models\DoctorInventory;
use App\Models\Expense;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DoctorInventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $locationId = $request->input('location_id');

        // Get user's locations
        $locations = \App\Models\StorageLocation::where('user_id', Auth::id())->get();
        
        // If no locations exist (shouldn't happen due to migration), create default
        if ($locations->isEmpty()) {
            $defaultLocation = \App\Models\StorageLocation::create([
                'user_id' => Auth::id(),
                'name' => 'Main Warehouse',
                'type' => 'warehouse',
                'is_default' => true,
            ]);
            $locations = collect([$defaultLocation]);
        }

        // Determine active location
        $activeLocation = null;
        if ($locationId) {
            $activeLocation = $locations->firstWhere('id', $locationId);
        }
        
        if (!$activeLocation) {
            $activeLocation = $locations->firstWhere('is_default', true) ?? $locations->first();
        }

        $items = DoctorInventory::where('user_id', Auth::id())
            ->where('storage_location_id', $activeLocation->id)
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('item_name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('item_name')
            ->paginate(10);
            
        return view('inventory.index', compact('items', 'search', 'locations', 'activeLocation'));
    }

    public function searchItems(Request $request)
    {
        $search = $request->input('q');
        
        $items = DoctorInventory::where('user_id', Auth::id())
            ->where('item_name', 'like', "%{$search}%")
            ->orderBy('item_name')
            ->limit(10)
            ->get();
            
        return response()->json($items);
    }

    public function expiryReport(Request $request)
    {
        $batches = \App\Models\DoctorInventoryBatch::whereHas('inventory', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->paginate(20);

        return view('inventory.expiry-report', compact('batches'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get(); // For selection
        return view('inventory.create', compact('products'));
    }

    public function store(Request $request)
    {
        $locationId = $request->storage_location_id;
        
        // Find default location if not specified
        if (!$locationId) {
             $defaultLocation = \App\Models\StorageLocation::where('user_id', Auth::id())
                ->where('is_default', true)
                ->first();
             if (!$defaultLocation) {
                 $defaultLocation = \App\Models\StorageLocation::create([
                    'user_id' => Auth::id(),
                    'name' => 'Main Warehouse',
                    'type' => 'warehouse',
                    'is_default' => true,
                 ]);
             }
             $locationId = $defaultLocation->id;
        }

        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required_without:product_id|string|max:255',
            'category' => 'nullable|string|max:100',
            'base_unit' => 'required|string',
            'purchase_unit' => 'required|string',
            'conversion_ratio' => 'required|integer|min:1',
            'min_stock_alert' => 'required|integer|min:0',
            'sku' => [
                'nullable',
                Rule::unique('doctor_inventories')->where(function ($query) use ($locationId) {
                    return $query->where('user_id', Auth::id())
                                 ->where('storage_location_id', $locationId);
                }),
            ],
        ]);

        $productId = $request->product_id;
        $sku = $request->sku;
        $itemName = $request->item_name;

        // If existing product selected, use its details
        if ($productId) {
            $product = Product::find($productId);
            $itemName = $product->name;
            $sku = $product->sku;
            // Check if user already has this item in this location
            $existing = DoctorInventory::where('user_id', Auth::id())
                ->where('storage_location_id', $locationId)
                ->where('product_id', $productId)
                ->first();
            
            if ($existing) {
                return redirect()->route('inventory.index', ['location_id' => $locationId])
                    ->with('error', 'Item already exists in this location.');
            }
        } else {
            // New Product Creation Logic
            if (empty($sku)) {
                // Auto-generate SKU
                $cat = strtoupper(substr($request->category ?? 'GEN', 0, 3));
                $sku = $cat . '-' . now()->format('ymd') . '-' . Str::random(4);
            }
            
            // Check if product exists by SKU
            $existingProduct = \App\Models\Product::where('sku', $sku)->first();

            if ($existingProduct) {
                $product = $existingProduct;
            } else {
                // Create Product first
                $product = Product::create([
                    'name' => $itemName,
                    'sku' => $sku,
                    'category' => $request->category ?? 'General',
                    'type' => 'goods',
                    'cost' => 0, // Will be updated via purchases
                    'price' => $request->selling_price ?? 0,
                    'stock' => 0,
                ]);
            }
            $productId = $product->id;
        }

        DoctorInventory::create([
            'user_id' => Auth::id(),
            'storage_location_id' => $locationId,
            'product_id' => $productId,
            'item_name' => $itemName,
            'category' => $request->category ?? ($product->category ?? 'General'),
            'sku' => $sku,
            'base_unit' => $request->base_unit,
            'purchase_unit' => $request->purchase_unit,
            'conversion_ratio' => $request->conversion_ratio,
            'unit' => $request->base_unit,
            'stock_qty' => 0,
            'alert_threshold' => $request->min_stock_alert,
            'selling_price' => $request->selling_price ?? $product->price,
            'is_sold' => $request->has('is_sold'),
        ]);

        return redirect()->route('inventory.index', ['location_id' => $locationId])->with('success', 'Item added successfully.');
    }

    public function edit(DoctorInventory $doctorInventory)
    {
        // Check ownership
        if ($doctorInventory->user_id !== Auth::id()) {
            abort(403);
        }
        return view('inventory.edit', compact('doctorInventory'));
    }

    public function update(Request $request, DoctorInventory $doctorInventory)
    {
        if ($doctorInventory->user_id !== Auth::id()) {
            abort(403);
        }

        $locationId = $doctorInventory->storage_location_id;

        $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'sku' => ['nullable', 'string', 'max:100', 
                 Rule::unique('doctor_inventories')->ignore($doctorInventory->id)->where(function ($query) use ($locationId) {
                     return $query->where('storage_location_id', $locationId);
                 })
            ],
            'base_unit' => 'required|string',
            'purchase_unit' => 'required|string',
            'conversion_ratio' => 'required|integer|min:1',
            'alert_threshold' => 'required|integer|min:0',
        ]);

        $doctorInventory->update([
            'item_name' => $request->item_name,
            'category' => $request->category,
            'sku' => $request->sku,
            'base_unit' => $request->base_unit,
            'purchase_unit' => $request->purchase_unit,
            'conversion_ratio' => $request->conversion_ratio,
            'alert_threshold' => $request->alert_threshold,
            'selling_price' => $request->selling_price ?? 0,
            'is_sold' => $request->has('is_sold'),
        ]);

        return redirect()->route('inventory.index', ['location_id' => $locationId])->with('success', 'Item updated successfully.');
    }

    public function restockForm(DoctorInventory $doctorInventory)
    {
        if ($doctorInventory->user_id !== Auth::id()) {
            abort(403);
        }
        return view('inventory.restock', compact('doctorInventory'));
    }

    public function restock(Request $request, DoctorInventory $doctorInventory)
    {
        if ($doctorInventory->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantity_purchase_unit' => 'required|numeric|min:0.1',
            'cost_per_purchase_unit' => 'required|numeric|min:0',
            'batch_number' => 'required|string|max:100',
            'expiry_date' => 'required|date|after:today',
        ]);

        DB::transaction(function () use ($request, $doctorInventory) {
            $qtyPurchase = $request->quantity_purchase_unit;
            $costPerUnit = $request->cost_per_purchase_unit;
            
            $qtyBase = $qtyPurchase * $doctorInventory->conversion_ratio;
            $totalCost = $qtyPurchase * $costPerUnit;

            // Update Average Cost Price (Weighted Average)
            $oldStock = $doctorInventory->stock_qty;
            $oldTotalValue = $oldStock * $doctorInventory->average_cost_price;
            $newTotalValue = $oldTotalValue + $totalCost;
            $newTotalStock = $oldStock + $qtyBase;
            
            $newAvgCost = $newTotalStock > 0 ? $newTotalValue / $newTotalStock : 0;

            // Create Expense
            $expense = Expense::create([
                'user_id' => Auth::id(),
                'type' => 'OPEX',
                'category' => 'Medicine Restock',
                'amount' => $totalCost,
                'notes' => "Restock {$doctorInventory->item_name}: {$qtyPurchase} {$doctorInventory->purchase_unit}",
                'transaction_date' => now(),
            ]);

            // Create Transaction
            InventoryTransaction::create([
                'doctor_inventory_id' => $doctorInventory->id,
                'type' => 'IN',
                'quantity_change' => $qtyBase,
                'related_expense_id' => $expense->id,
                'notes' => "Restock via purchase. Batch: {$request->batch_number}",
            ]);

            // Create Batch Record
            \App\Models\DoctorInventoryBatch::create([
                'doctor_inventory_id' => $doctorInventory->id,
                'batch_number' => $request->batch_number,
                'expiry_date' => $request->expiry_date,
                'quantity' => $qtyBase,
            ]);

            // Update Inventory
            $doctorInventory->update([
                'stock_qty' => $newTotalStock,
                'average_cost_price' => $newAvgCost,
            ]);
        });

        return redirect()->route('inventory.index')->with('success', 'Restock successful.');
    }

    public function adjustForm(DoctorInventory $doctorInventory)
    {
        if ($doctorInventory->user_id !== Auth::id()) {
            abort(403);
        }
        return view('inventory.adjust', compact('doctorInventory'));
    }

    public function adjust(Request $request, DoctorInventory $doctorInventory)
    {
        if ($doctorInventory->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'actual_stock' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $doctorInventory) {
            $currentStock = $doctorInventory->stock_qty;
            $actualStock = $request->actual_stock;
            $diff = $actualStock - $currentStock;

            if ($diff == 0) {
                return; // No change
            }

            // Create Transaction
            $transaction = InventoryTransaction::create([
                'doctor_inventory_id' => $doctorInventory->id,
                'type' => 'ADJUSTMENT',
                'quantity_change' => $diff,
                'notes' => "Adjustment: {$request->reason}. " . $request->notes,
            ]);

            // If stock decreased (Loss), create Expense (Loss)
            if ($diff < 0) {
                $lossAmount = abs($diff) * $doctorInventory->average_cost_price;
                
                $expense = Expense::create([
                    'user_id' => Auth::id(),
                    'type' => 'OPEX', // Loss is an operational expense
                    'category' => 'Inventory Loss/Adjustment',
                    'amount' => $lossAmount,
                    'notes' => "Loss adjustment for {$doctorInventory->item_name} ({$diff} {$doctorInventory->base_unit}). Reason: {$request->reason}",
                    'transaction_date' => now(),
                ]);
                
                $transaction->update(['related_expense_id' => $expense->id]);
            }

            // Update Inventory
            $doctorInventory->update([
                'stock_qty' => $actualStock,
            ]);

            if ($actualStock <= $doctorInventory->alert_threshold) {
                $doctorInventory->user->notify(new \App\Notifications\LowStockAlert($doctorInventory));
            }
        });

        return redirect()->route('inventory.index')->with('success', 'Stock adjustment successful.');
    }
}
