<?php

namespace App\Http\Controllers;

use App\Models\DoctorInventory;
use App\Models\InventoryTransaction;
use App\Models\StorageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternalTransferController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:doctor_inventories,id',
            'target_location_id' => 'required|exists:storage_locations,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $sourceItem = DoctorInventory::findOrFail($request->inventory_id);
        
        if ($sourceItem->user_id !== Auth::id()) {
            abort(403);
        }

        $targetLocation = StorageLocation::where('id', $request->target_location_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($sourceItem->storage_location_id === $targetLocation->id) {
            return back()->with('error', 'Cannot transfer to the same location.');
        }

        if ($sourceItem->stock_qty < $request->quantity) {
            return back()->with('error', 'Insufficient stock.');
        }

        DB::transaction(function () use ($sourceItem, $targetLocation, $request) {
            // Decrement source
            $sourceItem->decrement('stock_qty', $request->quantity);

            // Create Transaction for Source
            InventoryTransaction::create([
                'doctor_inventory_id' => $sourceItem->id,
                'type' => 'OUT',
                'quantity_change' => -$request->quantity,
                'notes' => "Internal transfer to {$targetLocation->name}",
            ]);

            // Find or Create Target Item
            $targetItem = DoctorInventory::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'storage_location_id' => $targetLocation->id,
                    'sku' => $sourceItem->sku,
                ],
                [
                    'item_name' => $sourceItem->item_name,
                    'category' => $sourceItem->category,
                    'unit' => $sourceItem->unit,
                    'base_unit' => $sourceItem->base_unit,
                    'purchase_unit' => $sourceItem->purchase_unit,
                    'conversion_ratio' => $sourceItem->conversion_ratio,
                    'selling_price' => $sourceItem->selling_price,
                    'alert_threshold' => $sourceItem->alert_threshold,
                    'stock_qty' => 0,
                ]
            );

            // Increment Target
            $targetItem->increment('stock_qty', $request->quantity);

             // Create Transaction for Target
             InventoryTransaction::create([
                'doctor_inventory_id' => $targetItem->id,
                'type' => 'IN',
                'quantity_change' => $request->quantity,
                'notes' => "Internal transfer from {$sourceItem->storageLocation->name}",
            ]);
        });

        return back()->with('success', 'Stock transferred successfully.');
    }
}
