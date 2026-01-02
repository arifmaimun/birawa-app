<?php

namespace App\DTOs;

class MedicalRecordDTO
{
    public function __construct(
        public readonly string $subjective,
        public readonly string $objective,
        public readonly ?string $assessment,
        public readonly ?string $plan_diagnostic,
        public readonly string $plan_treatment,
        public readonly ?string $plan_recipe,
        public readonly ?array $diagnoses,
        public readonly ?array $inventory_items,
        public readonly ?array $service_items,
        public readonly ?array $vital_signs,
        public readonly ?array $custom_vital_signs,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            subjective: $request->validated('subjective'),
            objective: $request->validated('objective'),
            assessment: $request->validated('assessment'),
            plan_diagnostic: $request->validated('plan_diagnostic'),
            plan_treatment: $request->validated('plan_treatment'),
            plan_recipe: $request->validated('plan_recipe'),
            diagnoses: $request->validated('diagnoses'),
            inventory_items: $request->validated('inventory_items'),
            service_items: $request->validated('service_items'),
            vital_signs: [
                'temperature' => $request->input('temperature'),
                'weight' => $request->input('weight'),
                'heart_rate' => $request->input('heart_rate'),
            ],
            custom_vital_signs: $request->input('custom_vital_signs'),
        );
    }
}
