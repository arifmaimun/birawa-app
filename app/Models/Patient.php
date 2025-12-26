<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'name', 'species', 'breed', 'gender', 'dob'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
