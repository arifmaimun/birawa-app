<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled in Controller/Middleware
    }

    public function rules(): array
    {
        return [
            'subjective' => 'required|string',
            'objective' => 'required|string',
            'assessment' => 'nullable|string',
            'diagnoses' => 'nullable|array',
            'diagnoses.*' => 'exists:diagnoses,id',
            'plan_treatment' => 'required|string',
            'plan_recipe' => 'nullable|string',
            'inventory_items' => 'nullable|array',
            'inventory_items.*.id' => 'exists:doctor_inventories,id',
            'inventory_items.*.qty' => 'numeric|min:0',
            'service_items' => 'nullable|array',
            'service_items.*.id' => 'exists:doctor_service_catalogs,id',
            'service_items.*.qty' => 'numeric|min:0',
            'temperature' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'heart_rate' => 'nullable|numeric',
            'custom_vital_signs' => 'nullable|array',
        ];
    }
}
