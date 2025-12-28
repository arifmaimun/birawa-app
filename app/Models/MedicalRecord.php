<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function usageLogs()
    {
        return $this->hasMany(MedicalUsageLog::class);
    }

    public function diagnoses()
    {
        return $this->belongsToMany(Diagnosis::class, 'diagnosis_medical_record');
    }

    public function hasAccessGranted($userId)
    {
        // Check if there is an approved access request for this user
        return AccessRequest::where('target_medical_record_id', $this->id)
            ->where('requester_doctor_id', $userId)
            ->where('status', 'approved')
            ->exists();
    }
}
