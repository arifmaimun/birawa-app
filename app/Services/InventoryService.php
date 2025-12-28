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
            ->where('doctor_id', Auth::id())
            ->firstOrFail();

        // Normalize everything to 'unit' (smallest unit)
        $quantityInUnits = $this->convertToUnits($inventory, $quantity, $unitType);

        DB::transaction(function () use ($inventory, $quantityInUnits, $quantity, $unitType) {
            // Deduct from total_units (conceptually)
            // But we store: stock_boxes, stock_units
            // Logic: 
            // 1 Box = conversion_ratio Units.
            // Total Units Available = (stock_boxes * conversion_ratio) + stock_units.
            
            $currentTotalUnits = ($inventory->stock_boxes * $inventory->conversion_ratio) + $inventory->stock_units;
            
            if ($currentTotalUnits < $quantityInUnits) {
                throw new \Exception("Insufficient stock for {$inventory->product_name}. Available: {$currentTotalUnits}, Required: {$quantityInUnits}");
            }

            $newTotalUnits = $currentTotalUnits - $quantityInUnits;

            // Convert back to Boxes + Units
            // Usually we keep units < conversion_ratio
            $newBoxes = floor($newTotalUnits / $inventory->conversion_ratio);
            $newUnits = $newTotalUnits % $inventory->conversion_ratio;

            $inventory->update([
                'stock_boxes' => $newBoxes,
                'stock_units' => $newUnits,
            ]);

            // Log Transaction
            InventoryTransaction::create([
                'doctor_inventory_id' => $inventory->id,
                'type' => 'usage',
                'quantity' => $quantity, // Original qty input
                'unit' => $unitType,
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
