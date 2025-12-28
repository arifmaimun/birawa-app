<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity_change' => 'decimal:2',
    ];

    public function inventory()
    {
        return $this->belongsTo(DoctorInventory::class, 'doctor_inventory_id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'related_expense_id');
    }
}
