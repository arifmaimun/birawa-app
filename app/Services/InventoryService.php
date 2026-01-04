<?php

namespace App\Services;

use App\Models\DoctorInventory;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Reserve stock for a potential sale/usage.
     * Moves stock from 'Available' to 'Reserved'.
     *
     * @return void
     */
    public function reserveStock(int $inventoryId, float $quantity, string $unitType = 'unit')
    {
        DB::transaction(function () use ($inventoryId, $quantity, $unitType) {
            $inventory = DoctorInventory::where('id', $inventoryId)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            $quantityInUnits = $this->convertToUnits($inventory, $quantity, $unitType);

            // Check availability: (Stock - Reserved) >= Requested
            $availableStock = $inventory->stock_qty - $inventory->reserved_qty;

            if ($availableStock < $quantityInUnits) {
                throw new \Exception("Insufficient available stock for {$inventory->item_name}. Available: {$availableStock}, Required: {$quantityInUnits}");
            }

            $inventory->increment('reserved_qty', $quantityInUnits);

            // Log Transaction (Optional for reservation, but good for tracking)
            InventoryTransaction::create([
                'doctor_inventory_id' => $inventory->id,
                'type' => 'RESERVATION',
                'quantity_change' => 0, // No physical change yet
                'notes' => "Reserved {$quantity} {$unitType} (calculated as {$quantityInUnits} base units)",
            ]);
        });
    }

    /**
     * Commit reserved stock (finalize sale).
     * Deducts from 'Reserved' and 'Stock Qty'.
     *
     * @return void
     */
    public function commitStock(int $inventoryId, float $quantity, string $unitType = 'unit')
    {
        DB::transaction(function () use ($inventoryId, $quantity, $unitType) {
            $inventory = DoctorInventory::where('id', $inventoryId)
                // ->where('user_id', Auth::id()) // Might be triggered by system/cashier not the doctor
                ->lockForUpdate()
                ->firstOrFail();

            $quantityInUnits = $this->convertToUnits($inventory, $quantity, $unitType);

            // Decrease reserved and actual stock
            // We assume this was reserved previously.
            // Ideally we should check if reserved_qty >= quantityInUnits, but sometimes immediate sale happens.

            if ($inventory->reserved_qty >= $quantityInUnits) {
                $inventory->decrement('reserved_qty', $quantityInUnits);
            } else {
                // If not enough reserved (maybe direct sale without reservation), just deduct stock
                // But we should verify stock exists
                if ($inventory->stock_qty < $quantityInUnits) {
                    throw new \Exception("Insufficient stock for {$inventory->item_name}.");
                }
            }

            $inventory->decrement('stock_qty', $quantityInUnits);
            $inventory->update(['is_sold' => true]);

            // Log Transaction
            InventoryTransaction::create([
                'doctor_inventory_id' => $inventory->id,
                'type' => 'OUT',
                'quantity_change' => -1 * $quantityInUnits,
                'notes' => "Sold/Used {$quantity} {$unitType}",
            ]);

            // Check threshold
            if ($inventory->stock_qty <= $inventory->alert_threshold) {
                DB::afterCommit(function () use ($inventory) {
                    $inventory->user->notify(new \App\Notifications\LowStockAlert($inventory));
                });
            }
        });
    }

    /**
     * Release reserved stock (cancel).
     * Moves stock from 'Reserved' back to 'Available'.
     */
    public function releaseStock(int $inventoryId, float $quantity, string $unitType = 'unit')
    {
        DB::transaction(function () use ($inventoryId, $quantity, $unitType) {
            $inventory = DoctorInventory::find($inventoryId);
            if (! $inventory) {
                return;
            }

            $quantityInUnits = $this->convertToUnits($inventory, $quantity, $unitType);

            if ($inventory->reserved_qty >= $quantityInUnits) {
                $inventory->decrement('reserved_qty', $quantityInUnits);

                InventoryTransaction::create([
                    'doctor_inventory_id' => $inventory->id,
                    'type' => 'CANCEL_RESERVATION',
                    'quantity_change' => 0,
                    'notes' => "Released {$quantity} {$unitType}",
                ]);
            }
        });
    }

    /**
     * Legacy method for immediate deduction.
     * Re-implemented to use commitStock directly (assuming no prior reservation or implicit reservation).
     */
    public function deductStock(int $inventoryId, float $quantity, string $unitType = 'unit')
    {
        // For backward compatibility or immediate usage (e.g. broken item), we can just commit directly.
        // But if we want to follow the "Reservation" flow strictly, maybe we should reserve then commit.
        // Let's just treat it as immediate commit.

        $this->commitStock($inventoryId, $quantity, $unitType);
    }

    /**
     * Convert input quantity to base units.
     */
    private function convertToUnits(DoctorInventory $inventory, float $quantity, string $unitType): float
    {
        $ratio = $inventory->conversion_ratio ?? 1;

        if (stripos($unitType, 'box') !== false || stripos($unitType, 'pack') !== false) {
            return $quantity * $ratio;
        }

        return $quantity;
    }
}
