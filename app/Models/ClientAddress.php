<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'street',
        'additional_info',
        'city',
        'province',
        'postal_code',
        'country',
        'parking_type',
        'address_type',
        'coordinates',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
