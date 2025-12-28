<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diagnosis;

class DiagnosisSeeder extends Seeder
{
    public function run()
    {
        $diagnoses = [
            // Skin
            ['code' => 'D001', 'name' => 'Dermatitis', 'category' => 'Skin'],
            ['code' => 'D002', 'name' => 'Fungal Infection', 'category' => 'Skin'],
            ['code' => 'D003', 'name' => 'Scabies', 'category' => 'Skin'],
            
            // Digestive
            ['code' => 'G001', 'name' => 'Gastroenteritis', 'category' => 'Digestive'],
            ['code' => 'G002', 'name' => 'Diarrhea', 'category' => 'Digestive'],
            ['code' => 'G003', 'name' => 'Vomiting', 'category' => 'Digestive'],
            ['code' => 'G004', 'name' => 'Parvovirus', 'category' => 'Digestive'],
            
            // Respiratory
            ['code' => 'R001', 'name' => 'Upper Respiratory Infection', 'category' => 'Respiratory'],
            ['code' => 'R002', 'name' => 'Pneumonia', 'category' => 'Respiratory'],
            ['code' => 'R003', 'name' => 'Feline Calicivirus', 'category' => 'Respiratory'],

            // Others
            ['code' => 'O001', 'name' => 'Otitis Externa', 'category' => 'Ear'],
            ['code' => 'E001', 'name' => 'Conjunctivitis', 'category' => 'Eye'],
            ['code' => 'T001', 'name' => 'Trauma / Wound', 'category' => 'Injury'],
            ['code' => 'V001', 'name' => 'Vaccination', 'category' => 'Preventive'],
        ];

        foreach ($diagnoses as $diagnosis) {
            Diagnosis::updateOrCreate(['code' => $diagnosis['code']], $diagnosis);
        }
    }
}
