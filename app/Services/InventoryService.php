<?php

namespace App\Services;

use App\Models\DoctorInventory;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Deduct stock based on usage and unit type.
     * Handles conversion from lower units to higher units if necessary.
     * 
     * @param int $inventoryId
     * @param float $quantity
     * @param string $unitType 'box', 'package', 'unit' (tablet/ml)
     * @return void
     */
    public function deductStock(int $inventoryId, float $quantity, string $unitType = 'unit')
    {
        $inventory = DoctorInventory::where('id', $inventoryId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Normalize everything to 'unit' (smallest unit)
        $quantityInUnits = $this->convertToUnits($inventory, $quantity, $unitType);

        DB::transaction(function () use ($inventory, $quantityInUnits, $quantity, $unitType) {
            // Deduct from stock_qty (decimal)
            
            if ($inventory->stock_qty < $quantityInUnits) {
                throw new \Exception("Insufficient stock for {$inventory->item_name}. Available: {$inventory->stock_qty}, Required: {$quantityInUnits}");
            }

            $inventory->decrement('stock_qty', $quantityInUnits);

            // Log Transaction
            InventoryTransaction::create([
                'doctor_inventory_id' => $inventory->id,
                'type' => 'OUT',
                'quantity_change' => -1 * $quantityInUnits,
                'notes' => "Used {$quantity} {$unitType} (calculated as {$quantityInUnits} base units)",
            ]);
        });
    }

    /**
     * Convert input quantity to base units.
     */
    private function convertToUnits(DoctorInventory $inventory, float $quantity, string $unitType): float
    {
        // Assuming unitType match inventory->unit_type (box) or inventory->sub_unit_type (tablet/ml)
        // If input is 'box', multiply by ratio.
        // If input is 'unit', keep as is.
        
        // Simple logic check (names might vary, relying on flag or string match)
        // For now, if unitType is 'box' or 'pack', multiply.
        
        $ratio = $inventory->conversion_ratio ?? 1;

        if (stripos($unitType, 'box') !== false || stripos($unitType, 'pack') !== false) {
            return $quantity * $ratio;
        }

        return $quantity;
    }
}
