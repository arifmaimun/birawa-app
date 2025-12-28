<?php

namespace App\Http\Controllers;

use App\Models\DoctorInventory;
use App\Models\Expense;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorInventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = DoctorInventory::where('user_id', Auth::id())
            ->when($search, function ($query) use ($search) {
                $query->where('item_name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderBy('item_name')
            ->paginate(10);
            
        return view('inventory.index', compact('items', 'search'));
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

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'base_unit' => 'required|string',
            'purchase_unit' => 'required|string',
            'conversion_ratio' => 'required|integer|min:1',
            'min_stock_alert' => 'required|integer|min:0',
        ]);

        DoctorInventory::create([
            'user_id' => Auth::id(),
            'item_name' => $request->item_name,
            'sku' => $request->sku,
            'base_unit' => $request->base_unit,
            'purchase_unit' => $request->purchase_unit,
            'conversion_ratio' => $request->conversion_ratio,
            'unit' => $request->base_unit, // Fallback/Legacy
            'stock_qty' => 0,
            'alert_threshold' => $request->min_stock_alert,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Item added successfully.');
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

        $request->validate([
            'item_name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'base_unit' => 'required|string',
            'purchase_unit' => 'required|string',
            'conversion_ratio' => 'required|integer|min:1',
            'alert_threshold' => 'required|integer|min:0',
        ]);

        $doctorInventory->update($request->all());

        return redirect()->route('inventory.index')->with('success', 'Item updated successfully.');
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
                'notes' => "Restock via purchase",
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
        });

        return redirect()->route('inventory.index')->with('success', 'Stock adjustment successful.');
    }
}
