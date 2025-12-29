<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorInventory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'stock_qty' => 'decimal:2',
        'average_cost_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function batches()
    {
        return $this->hasMany(DoctorInventoryBatch::class);
    }
}
