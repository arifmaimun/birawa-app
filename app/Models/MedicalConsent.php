<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'consent_body_snapshot',
        'client_signature_path',
        'doctor_signature_path',
        'pdf_path',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
