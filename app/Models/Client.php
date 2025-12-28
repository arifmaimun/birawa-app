<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'address', 'phone',
        'first_name', 'last_name',
        'is_business', 'business_name', 'contact_person',
        'id_type', 'id_number', 'gender', 'occupation',
        'dob', 'ethnicity', 'religion', 'marital_status'
    ];
    
    protected $casts = [
        'is_business' => 'boolean',
        'dob' => 'date',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(ClientAddress::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
