<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_doctor_id');
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'target_medical_record_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_doctor_id');
    }
}
