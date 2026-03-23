<?php

namespace Tests\Feature\Medisys;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientCreationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_reception_can_create_patient_from_http_form(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $response = $this->actingAs($user)->post(route('patients.store'), [
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
            'date_naissance' => '1990-05-20',
            'genre' => 'M',
            'cin' => 'QA123456',
            'telephone' => '+212633334444',
            'email' => 'ahmed.bennani@medisys.test',
            'adresse' => 'Boulevard Hassan II',
            'ville' => 'Casablanca',
            'groupe_sanguin' => 'A+',
            'assurance_medicale' => 'CNSS',
            'antecedents_medicaux' => 'RAS',
            'allergies' => 'Aucune',
        ]);

        $response->assertRedirect(route('patients.index'));

        $this->assertDatabaseHas('patients', [
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
            'telephone' => '+212633334444',
            'cin' => 'QA123456',
        ]);
    }

    public function test_creation_persists_medical_history_and_postal_code(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $response = $this->actingAs($user)->post(route('patients.store'), [
            'nom' => 'El Amrani',
            'prenom' => 'Nadia',
            'date_naissance' => '1988-11-21',
            'genre' => 'F',
            'cin' => 'ZA654321',
            'telephone' => '+212699887766',
            'email' => 'nadia.elamrani@medisys.test',
            'adresse' => '14 Rue Atlas',
            'ville' => 'Rabat',
            'code_postal' => '10000',
            'groupe_sanguin' => 'B+',
            'assurance_medicale' => 'Autre',
            'assurance_autre' => 'AssurTest',
            'antecedents_medicaux' => 'Hypertension',
            'allergies' => 'Aucune',
        ]);

        $response->assertRedirect(route('patients.index'));

        $this->assertDatabaseHas('patients', [
            'nom' => 'El Amrani',
            'prenom' => 'Nadia',
            'code_postal' => '10000',
            'assurance' => 'AssurTest',
            'antecedents' => 'Hypertension',
        ]);
    }
}



