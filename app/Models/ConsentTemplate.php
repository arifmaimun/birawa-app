<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'title', 'body_content'];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
