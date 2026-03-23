<?php

namespace Tests\Feature\Medisys;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_update_patient_profile_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $patient = Patient::factory()->create([
            'nom' => 'Bennani',
            'prenom' => 'Karim',
            'cin' => 'AB123456',
            'telephone' => '+212600000001',
            'email' => 'karim.bennani@medisys.test',
            'assurance' => 'CNSS',
        ]);

        $response = $this->actingAs($user)->put(route('patients.update', $patient), [
            'nom' => 'Bennani',
            'prenom' => 'Karim Updated',
            'cin' => 'AB123456',
            'date_naissance' => '1992-04-12',
            'genre' => 'M',
            'etat_civil' => 'Marie',
            'adresse' => '27 Rue Atlas',
            'ville' => 'Casablanca',
            'code_postal' => '20000',
            'telephone' => '+212600000001',
            'email' => 'karim.updated@medisys.test',
            'contact_urgence' => 'Sara Bennani',
            'telephone_urgence' => '+212600000999',
            'groupe_sanguin' => 'A+',
            'assurance_medicale' => 'Autre',
            'assurance_autre' => 'AXA',
            'allergies' => 'Penicilline',
            'antecedents' => 'Asthme',
            'traitements' => 'Inhalateur',
            'notes' => 'Suivi trimestriel',
        ]);

        $response->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'prenom' => 'Karim Updated',
            'email' => 'karim.updated@medisys.test',
            'assurance' => 'AXA',
            'allergies' => 'Penicilline',
            'antecedents' => 'Asthme',
        ]);
    }

    public function test_index_search_and_status_filter_only_returns_matching_patients(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        Patient::factory()->create([
            'nom' => 'El Idrissi',
            'prenom' => 'Amine',
            'telephone' => '+212611111111',
            'email' => 'amine.idrissi@medisys.test',
            'is_draft' => false,
        ]);

        Patient::factory()->create([
            'nom' => 'Benali',
            'prenom' => 'Nora',
            'telephone' => '+212622222222',
            'email' => 'nora.benali@medisys.test',
            'is_draft' => true,
        ]);

        $response = $this->actingAs($user)->get(route('patients.index', [
            'search' => 'Idrissi',
            'status' => 'actif',
        ]));

        $response
            ->assertOk()
            ->assertSee('EL IDRISSI')
            ->assertDontSee('BENALI');
    }
}
