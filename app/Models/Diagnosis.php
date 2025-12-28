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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereNull('user_id')->orWhere('user_id', $userId);
    }
}
