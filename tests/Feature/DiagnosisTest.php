<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Diagnosis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiagnosisTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_add_private_diagnosis()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $otherDoctor = User::factory()->create(['role' => 'veterinarian']);

        $response = $this->actingAs($doctor)->postJson(route('diagnoses.store'), [
            'code' => 'TEST001',
            'name' => 'Test Diagnosis',
            'category' => 'General'
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('diagnoses', [
            'code' => 'TEST001',
            'user_id' => $doctor->id
        ]);

        // Verify other doctor cannot see it (via scope)
        $this->assertEquals(0, Diagnosis::forUser($otherDoctor->id)->where('code', 'TEST001')->count());
        $this->assertEquals(1, Diagnosis::forUser($doctor->id)->where('code', 'TEST001')->count());
    }

    public function test_global_diagnosis_visibility()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        
        $globalDiagnosis = Diagnosis::create([
            'code' => 'GLOBAL001',
            'name' => 'Global Diagnosis',
            'category' => 'General',
            'user_id' => null
        ]);

        $this->assertEquals(1, Diagnosis::forUser($doctor->id)->where('code', 'GLOBAL001')->count());
    }

    public function test_cannot_add_duplicate_diagnosis()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        
        Diagnosis::create([
            'code' => 'EXISTING',
            'name' => 'Existing',
            'category' => 'General',
            'user_id' => $doctor->id
        ]);

        $response = $this->actingAs($doctor)->postJson(route('diagnoses.store'), [
            'code' => 'EXISTING',
            'name' => 'Duplicate',
            'category' => 'General'
        ]);

        $response->assertStatus(422);
    }
}
