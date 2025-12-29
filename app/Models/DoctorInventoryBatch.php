<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorInventoryBatch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
    ];

    public function inventory()
    {
        return $this->belongsTo(DoctorInventory::class, 'doctor_inventory_id');
    }
}