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
            'telephone' => '60000001',
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
            'telephone' => '60000001',
            'email' => 'karim.updated@medisys.test',
            'contact_urgence' => 'Sara Bennani',
            'telephone_urgence' => '60000999',
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
            'telephone' => '61111111',
            'email' => 'amine.idrissi@medisys.test',
            'is_draft' => false,
        ]);

        Patient::factory()->create([
            'nom' => 'Benali',
            'prenom' => 'Nora',
            'telephone' => '62222222',
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

    public function test_authorized_user_can_update_patient_city_from_dropdown_selection(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $patient = Patient::factory()->create([
            'nom' => 'Lahlou',
            'prenom' => 'Imane',
            'cin' => 'CD987654',
            'telephone' => '60000077',
            'email' => 'imane.lahlou@medisys.test',
        ]);

        $response = $this->actingAs($user)->put(route('patients.update', $patient), [
            'nom' => 'Lahlou',
            'prenom' => 'Imane',
            'cin' => 'CD987654',
            'date_naissance' => '1991-07-18',
            'genre' => 'F',
            'etat_civil' => 'celibataire',
            'adresse' => '45 Rue Al Atlas',
            'ville_selection' => 'Kenitra',
            'code_postal' => '14000',
            'telephone' => '60000077',
            'email' => 'imane.lahlou@medisys.test',
            'contact_urgence' => 'Yassine Lahlou',
            'telephone_urgence' => '60000078',
            'groupe_sanguin' => 'B+',
            'assurance_medicale' => 'CNSS',
            'allergies' => 'Aucune',
            'antecedents' => 'RAS',
            'traitements' => 'Aucun',
            'notes' => 'Dossier mis a jour via select ville',
        ]);

        $response->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'ville' => 'Kenitra',
            'code_postal' => '14000',
        ]);
    }
}
