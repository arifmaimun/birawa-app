<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_doctor_id',
        'patient_id',
        'target_clinic_name',
        'notes',
        'access_token',
        'valid_until',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
    ];

    public function sourceDoctor()
    {
        return $this->belongsTo(User::class, 'source_doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function getIsValidAttribute()
    {
        return $this->valid_until->isFuture();
    }
}
