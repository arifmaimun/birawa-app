<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    protected $guarded = ['id'];

    public function medicalRecords()
    {
        return $this->belongsToMany(MedicalRecord::class);
    }
}
