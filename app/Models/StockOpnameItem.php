<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'system_qty' => 'decimal:2',
        'actual_qty' => 'decimal:2',
        'difference' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function doctorInventory()
    {
        return $this->belongsTo(DoctorInventory::class);
    }
}
