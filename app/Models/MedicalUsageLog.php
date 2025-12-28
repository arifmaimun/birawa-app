<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalUsageLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function doctorInventory()
    {
        return $this->belongsTo(DoctorInventory::class, 'doctor_inventory_id');
    }
}
