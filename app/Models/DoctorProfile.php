<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bank_account_details' => 'array',
        'timezone' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the doctor's timezone or default to application timezone.
     */
    public function getTimezoneAttribute($value)
    {
        return $value ?: config('app.timezone');
    }
}
