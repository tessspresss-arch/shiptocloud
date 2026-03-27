<?php

namespace Tests\Feature\Medisys;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientModuleScenarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_list_page_loads_with_primary_actions_and_row_controls(): void
    {
        $this->signInWithPatientsAccess();

        $patient = Patient::factory()->create([
            'nom' => 'Bennani',
            'prenom' => 'Karim',
            'cin' => 'QA100001',
            'telephone' => '61122334',
            'email' => 'karim.bennani@medisys.test',
        ]);

        $response = $this->get(route('patients.index'));

        $response
            ->assertOk()
            ->assertSeeText('Gestion des Patients')
            ->assertSeeText('Liste des patients')
            ->assertSeeText('Nouveau Patient')
            ->assertSeeText('Exporter CSV')
            ->assertSeeText('Mode tableau')
            ->assertSeeText('Mode compact')
            ->assertSeeText('Mode cartes')
            ->assertSeeText('BENNANI')
            ->assertSeeText('Karim')
            ->assertSee('data-label="Actions"', false)
            ->assertSee('aria-label="Voir le dossier de ' . $patient->nom_complet . '"', false)
            ->assertSee('aria-label="Modifier le dossier de ' . $patient->nom_complet . '"', false)
            ->assertSee('aria-label="Archiver le dossier de ' . $patient->nom_complet . '"', false);
    }

    public function test_patient_creation_flow_stores_record_and_returns_success_feedback(): void
    {
        $this->signInWithPatientsAccess();

        $createPage = $this->get(route('patients.create'));

        $createPage
            ->assertOk()
            ->assertSeeText('Ajouter un Patient')
            ->assertSeeText('Information importante')
            ->assertSeeText('Enregistrer')
            ->assertSeeText('Ouvrir le guide');

        $payload = [
            'nom' => 'Zerouali',
            'prenom' => 'Leila',
            'date_naissance' => '1991-03-18',
            'genre' => 'F',
            'cin' => 'QA200002',
            'telephone' => '63344556',
            'email' => 'leila.zerouali@medisys.test',
            'adresse' => '18 Boulevard Zerktouni',
            'ville_selection' => 'Casablanca',
            'code_postal' => '20000',
            'groupe_sanguin' => 'A+',
            'assurance_medicale' => 'CNSS',
            'antecedents_medicaux' => 'Asthme leger',
            'allergies' => 'Aucune',
        ];

        $response = $this->from(route('patients.create'))->post(route('patients.store'), $payload);

        $response
            ->assertRedirect(route('patients.index'))
            ->assertSessionHas('success', 'Patient cree avec succes');

        $patient = Patient::query()->where('cin', 'QA200002')->first();

        $this->assertNotNull($patient);

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'nom' => 'Zerouali',
            'prenom' => 'Leila',
            'telephone' => '63344556',
            'email' => 'leila.zerouali@medisys.test',
            'ville' => 'Casablanca',
            'assurance' => 'CNSS',
            'antecedents' => 'Asthme leger',
        ]);

        $listResponse = $this->get(route('patients.index', ['search' => 'Zerouali']));

        $listResponse
            ->assertOk()
            ->assertSeeText('ZEROUALI')
            ->assertSeeText('Leila')
            ->assertSeeText($patient->numero_dossier);
    }

    public function test_patient_creation_validation_blocks_missing_required_fields_invalid_email_and_invalid_phone(): void
    {
        $this->signInWithPatientsAccess();

        $response = $this->from(route('patients.create'))->post(route('patients.store'), [
            'nom' => '',
            'prenom' => '',
            'date_naissance' => '',
            'genre' => '',
            'telephone' => '1234567',
            'email' => 'email-invalide',
        ]);

        $response
            ->assertRedirect(route('patients.create'))
            ->assertSessionHasErrors([
                'nom',
                'prenom',
                'date_naissance',
                'genre',
                'telephone',
                'email',
            ]);

        $this->assertDatabaseCount('patients', 0);
    }

    public function test_patient_show_and_update_flow_persist_patient_changes(): void
    {
        $this->signInWithPatientsAccess();

        $patient = Patient::factory()->create([
            'nom' => 'Lahlou',
            'prenom' => 'Imane',
            'cin' => 'QA300003',
            'date_naissance' => '1989-07-11',
            'genre' => 'F',
            'telephone' => '67788990',
            'email' => 'imane.lahlou@medisys.test',
            'ville' => 'Rabat',
            'adresse' => 'Rue Al Atlas',
            'groupe_sanguin' => 'B+',
            'assurance' => 'CNOPS',
            'antecedents' => 'Migraine',
            'notes' => 'Premiere version du dossier',
        ]);

        $showResponse = $this->get(route('patients.show', $patient));

        $showResponse
            ->assertOk()
            ->assertSeeText('Dossier patient')
            ->assertSeeText('Informations principales')
            ->assertSeeText('Statistiques')
            ->assertSeeText('Actions rapides')
            ->assertSeeText('Historique')
            ->assertSeeText($patient->telephone)
            ->assertSeeText($patient->email)
            ->assertSeeText($patient->ville);

        $editResponse = $this->get(route('patients.edit', $patient));

        $editResponse
            ->assertOk()
            ->assertSeeText('Modifier Patient')
            ->assertSeeText('Voir fiche')
            ->assertSeeText('Informations Personnelles')
            ->assertSeeText('Contact');

        $updateResponse = $this->from(route('patients.edit', $patient))->put(route('patients.update', $patient), [
            'nom' => 'Lahlou',
            'prenom' => 'Imane QA',
            'cin' => 'QA300003',
            'date_naissance' => '1989-07-11',
            'genre' => 'F',
            'etat_civil' => 'marie',
            'adresse' => '45 Avenue Mohammed V',
            'ville_selection' => 'Agadir',
            'code_postal' => '80000',
            'telephone' => '67788990',
            'email' => 'imane.qa@medisys.test',
            'contact_urgence' => 'Sara Lahlou',
            'telephone_urgence' => '67788991',
            'groupe_sanguin' => 'AB+',
            'assurance_medicale' => 'Autre',
            'assurance_autre' => 'Wafa Pro Sante',
            'allergies' => 'Aspirine',
            'antecedents' => 'Diabete type 2',
            'traitements' => 'Metformine',
            'notes' => 'Suivi trimestriel renforce',
        ]);

        $updateResponse
            ->assertRedirect(route('patients.show', $patient))
            ->assertSessionHas('success', 'Patient modifie avec succes!');

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'prenom' => 'Imane QA',
            'email' => 'imane.qa@medisys.test',
            'ville' => 'Agadir',
            'telephone_urgence' => '67788991',
            'assurance' => 'Wafa Pro Sante',
            'allergies' => 'Aspirine',
            'antecedents' => 'Diabete type 2',
            'traitements' => 'Metformine',
            'notes' => 'Suivi trimestriel renforce',
        ]);

        $updatedShowResponse = $this->get(route('patients.show', $patient));

        $updatedShowResponse
            ->assertOk()
            ->assertSeeText('IMANE QA')
            ->assertSeeText('LAHLOU')
            ->assertSeeText('Agadir')
            ->assertSeeText('Wafa Pro Sante')
            ->assertSeeText('Suivi trimestriel renforce');
    }

    public function test_patient_search_by_name_and_phone_and_delete_flow_behave_as_expected(): void
    {
        $this->signInWithPatientsAccess();

        $matchingPatient = Patient::factory()->create([
            'nom' => 'Berrada',
            'prenom' => 'Mina',
            'cin' => 'QA400004',
            'telephone' => '61234123',
            'email' => 'mina.berrada@medisys.test',
        ]);

        $noisePatient = Patient::factory()->create([
            'nom' => 'Filali',
            'prenom' => 'Omar',
            'cin' => 'QA500005',
            'telephone' => '69999888',
            'email' => 'omar.filali@medisys.test',
        ]);

        $nameSearchResponse = $this->get(route('patients.index', ['search' => 'Berrada']));

        $nameSearchResponse
            ->assertOk()
            ->assertSeeText('BERRADA')
            ->assertSeeText('Mina')
            ->assertDontSeeText('FILALI');

        $phoneSearchResponse = $this->get(route('patients.index', ['search' => '61234123']));

        $phoneSearchResponse
            ->assertOk()
            ->assertSeeText('BERRADA')
            ->assertDontSeeText('FILALI');

        $deleteResponse = $this->delete(route('patients.destroy', $matchingPatient));

        $deleteResponse
            ->assertRedirect(route('patients.index'))
            ->assertSessionHas('success', 'Patient supprime avec succes.');

        $this->assertDatabaseMissing('patients', [
            'id' => $matchingPatient->id,
        ]);

        $this->assertDatabaseHas('patients', [
            'id' => $noisePatient->id,
        ]);
    }

    private function signInWithPatientsAccess(): User
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $this->actingAs($user);

        return $user;
    }
}
