<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Visit extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('doctor_access', function (Builder $builder) {
            if (Auth::check() && Auth::user()->role === 'veterinarian') {
                $builder->where('user_id', Auth::id());
            }
        });
    }

    protected $fillable = [
        'patient_id',
        'user_id',
        'scheduled_at',
        'visit_status_id',
        'complaint',
        'transport_fee',
        'latitude',
        'longitude',
        'distance_km',
        'actual_travel_minutes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'distance_km' => 'float',
        'actual_travel_minutes' => 'integer',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitStatus(): BelongsTo
    {
        return $this->belongsTo(VisitStatus::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
