<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormOption extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category)->where('is_active', true)->orderBy('value');
    }
}
