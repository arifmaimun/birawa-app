<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransferItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_approved' => 'decimal:2',
    ];

    public function transfer()
    {
        return $this->belongsTo(InventoryTransfer::class, 'inventory_transfer_id');
    }
}
