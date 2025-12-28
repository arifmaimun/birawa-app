<?php

namespace App\Http\Controllers;

use App\Models\MedicalConsent;
use App\Models\MedicalRecord;
use App\Models\ConsentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MedicalConsentController extends Controller
{
    public function create(MedicalRecord $medicalRecord)
    {
        if ($medicalRecord->doctor_id !== Auth::id()) {
            abort(403);
        }
        
        $templates = ConsentTemplate::where('doctor_id', Auth::id())->get();

        return view('medical_consents.create', compact('medicalRecord', 'templates'));
    }

    public function store(Request $request, MedicalRecord $medicalRecord)
    {
        if ($medicalRecord->doctor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'consent_body' => 'required|string',
            'client_signature' => 'required|string', // Base64
            'doctor_signature' => 'required|string', // Base64
        ]);

        // Process Signatures
        $clientSigPath = $this->saveSignature($request->client_signature, 'client');
        $doctorSigPath = $this->saveSignature($request->doctor_signature, 'doctor');

        MedicalConsent::create([
            'medical_record_id' => $medicalRecord->id,
            'consent_body_snapshot' => $request->consent_body,
            'client_signature_path' => $clientSigPath,
            'doctor_signature_path' => $doctorSigPath,
        ]);

        return redirect()->route('medical-records.show', $medicalRecord)->with('success', 'Consent form signed and saved.');
    }

    private function saveSignature($base64String, $prefix)
    {
        // Remove header data:image/png;base64,
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $type = strtolower($type[1]); // png, jpg, etc
            
            if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                throw new \Exception('Invalid image type');
            }
            
            $base64String = base64_decode($base64String);
            
            if ($base64String === false) {
                throw new \Exception('Base64 decode failed');
            }
        } else {
            throw new \Exception('Did not match data URI with image data');
        }

        $fileName = 'signatures/' . $prefix . '_' . Str::uuid() . '.' . $type;
        Storage::disk('public')->put($fileName, $base64String);
        
        return $fileName;
    }
    
    public function show(MedicalConsent $medicalConsent)
    {
        // Check access via medical record
        $record = $medicalConsent->medicalRecord;
        if ($record->doctor_id !== Auth::id() && !$record->hasAccessGranted(Auth::id())) {
            abort(403);
        }

        return view('medical_consents.show', compact('medicalConsent'));
    }
}
