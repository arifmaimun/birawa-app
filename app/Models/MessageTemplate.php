<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'type', 'title', 'content_pattern'];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
