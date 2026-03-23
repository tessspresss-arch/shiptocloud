<?php

namespace Tests\Feature\Medisys;

use App\Models\Medecin;
use App\Models\Medicament;
use App\Models\Ordonnance;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrdonnanceCreateExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_prefills_connected_doctor_when_email_matches(): void
    {
        $medecin = Medecin::factory()->create([
            'civilite' => 'Dr.',
            'prenom' => 'Salma',
            'nom' => 'Alaoui',
            'email' => 'salma.alaoui@medisys.test',
            'specialite' => 'cardiologie',
        ]);

        $user = User::factory()->create([
            'name' => 'Salma Alaoui',
            'email' => 'salma.alaoui@medisys.test',
            'role' => 'admin',
        ]);

        $patient = Patient::factory()->create([
            'prenom' => 'Nadia',
            'nom' => 'Bennani',
            'allergies' => 'Penicilline',
            'traitements' => 'Aspirine',
        ]);

        Medicament::create([
            'nom_commercial' => 'Doliprane',
            'dci' => 'Paracetamol',
            'code_cip' => '3400000000001',
            'code_medicament' => 'MED-DOLI-01',
            'presentation' => '500 mg',
            'posologie' => '1 comprime matin et soir',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('ordonnances.create', ['patient_id' => $patient->id]));

        $response->assertOk();
        $response->assertSee('Salma Alaoui');
        $response->assertSee('cardiologie');
        $response->assertSee('Nadia Bennani');
        $response->assertSee('Penicilline');
        $response->assertSee('value="' . $medecin->id . '"', false);
    }

    public function test_store_persists_doctor_and_medications(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $patient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();
        $medicament = Medicament::create([
            'nom_commercial' => 'Augmentin',
            'dci' => 'Amoxicilline',
            'code_cip' => '3400000000002',
            'code_medicament' => 'MED-AUG-01',
            'presentation' => '1 g',
            'posologie' => '1 comprime matin et soir',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('ordonnances.store'), [
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'date_prescription' => '2026-03-14',
                'diagnostic' => 'Angine',
                'instructions' => 'Hydratation et repos',
                'statut' => 'active',
                'medicaments' => [
                    [
                        'medicament_id' => $medicament->id,
                        'posologie' => '1 comprime matin et soir',
                        'duree' => '7 jours',
                        'quantite' => '14',
                        'instructions' => 'Apres repas',
                    ],
                ],
            ]);

        $response->assertRedirect(route('ordonnances.index'));

        $ordonnance = Ordonnance::query()->latest('id')->first();

        $this->assertNotNull($ordonnance);
        $this->assertSame($patient->id, $ordonnance->patient_id);
        $this->assertSame('Angine', $ordonnance->diagnostic);
        $this->assertSame('Hydratation et repos', $ordonnance->instructions);
        $this->assertSame($medicament->id, $ordonnance->medicaments[0]['medicament_id']);

        if (Schema::hasColumn('ordonnances', 'medecin_id')) {
            $this->assertSame($medecin->id, $ordonnance->medecin_id);
        }
    }

    public function test_edit_page_reuses_form_and_update_persists_existing_ordonnance(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $patient = Patient::factory()->create([
            'prenom' => 'Leila',
            'nom' => 'Fassi',
        ]);
        $medecin = Medecin::factory()->create([
            'civilite' => 'Dr.',
            'prenom' => 'Nora',
            'nom' => 'Idrissi',
        ]);
        $medicament = Medicament::create([
            'nom_commercial' => 'Spasfon',
            'dci' => 'Phloroglucinol',
            'code_cip' => '3400000000003',
            'code_medicament' => 'MED-SPAS-01',
            'presentation' => '80 mg',
            'posologie' => '1 comprime si douleur',
        ]);

        $payload = [
            'numero_ordonnance' => 'ORD-TEST-001',
            'patient_id' => $patient->id,
            'date_prescription' => '2026-03-15',
            'diagnostic' => 'Douleur abdominale',
            'instructions' => 'Repos',
            'medicaments' => [[
                'medicament_id' => $medicament->id,
                'posologie' => '1 comprime si douleur',
                'duree' => '3 jours',
                'quantite' => '6',
                'instructions' => 'Apres repas',
            ]],
        ];

        if (Schema::hasColumn('ordonnances', 'medecin_id')) {
            $payload['medecin_id'] = $medecin->id;
        }
        if (Schema::hasColumn('ordonnances', 'statut')) {
            $payload['statut'] = 'active';
        }

        $ordonnance = Ordonnance::create($payload);

        $this->actingAs($user)
            ->get(route('ordonnances.edit', $ordonnance))
            ->assertOk()
            ->assertSee('Mettre a jour')
            ->assertSee('Douleur abdominale');

        $response = $this->actingAs($user)->put(route('ordonnances.update', $ordonnance), [
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_prescription' => '2026-03-16',
            'diagnostic' => 'Douleur abdominale resolue',
            'instructions' => 'Hydratation',
            'statut' => 'active',
            'medicaments' => [[
                'medicament_id' => $medicament->id,
                'posologie' => '1 comprime matin',
                'duree' => '2 jours',
                'quantite' => '2',
                'instructions' => 'Matin',
            ]],
        ]);

        $response->assertRedirect(route('ordonnances.index'));

        $ordonnance->refresh();
        $this->assertSame('Douleur abdominale resolue', $ordonnance->diagnostic);
        $this->assertSame('Hydratation', $ordonnance->instructions);
        $this->assertSame('2026-03-16', optional($ordonnance->date_prescription)->format('Y-m-d'));
        $this->assertSame('1 comprime matin', $ordonnance->medicaments[0]['posologie']);
    }

    public function test_store_rejects_consultation_that_does_not_belong_to_selected_patient(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $selectedPatient = Patient::factory()->create();
        $otherPatient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();
        $medicament = Medicament::create([
            'nom_commercial' => 'Clamoxyl',
            'dci' => 'Amoxicilline',
            'code_cip' => '3400000000099',
            'code_medicament' => 'MED-CLAM-01',
            'presentation' => '500 mg',
            'posologie' => '1 comprime trois fois par jour',
        ]);

        $consultation = Consultation::factory()->create([
            'patient_id' => $otherPatient->id,
            'medecin_id' => $medecin->id,
        ]);

        $response = $this->actingAs($user)->from(route('ordonnances.create'))->post(route('ordonnances.store'), [
            'patient_id' => $selectedPatient->id,
            'medecin_id' => $medecin->id,
            'consultation_id' => $consultation->id,
            'date_prescription' => '2026-03-15',
            'diagnostic' => 'Infection',
            'instructions' => 'Repos',
            'medicaments' => [[
                'medicament_id' => $medicament->id,
                'posologie' => '1 comprime trois fois par jour',
                'duree' => '5 jours',
                'quantite' => '15',
                'instructions' => 'Apres repas',
            ]],
        ]);

        $response
            ->assertRedirect(route('ordonnances.create'))
            ->assertSessionHasErrors(['consultation_id']);

        $this->assertDatabaseCount('ordonnances', 0);
    }

    public function test_preview_pdf_returns_a_real_pdf_response(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $patient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();
        $medicament = Medicament::create([
            'nom_commercial' => 'Doliprane',
            'dci' => 'Paracetamol',
            'code_cip' => '3400000000004',
            'code_medicament' => 'MED-DOLI-02',
            'presentation' => '1 g',
            'posologie' => '1 comprime matin et soir',
        ]);

        $response = $this->actingAs($user)->post(route('ordonnances.preview-pdf'), [
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_prescription' => '2026-03-15',
            'diagnostic' => 'Fievre',
            'instructions' => 'Hydratation',
            'medicaments' => [[
                'medicament_id' => $medicament->id,
                'posologie' => '1 comprime matin et soir',
                'duree' => '3 jours',
                'quantite' => '6',
                'instructions' => 'Apres repas',
            ]],
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_show_prefers_direct_prescribing_doctor_when_no_consultation_is_linked(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $patient = Patient::factory()->create([
            'prenom' => 'Mina',
            'nom' => 'Zeroual',
        ]);
        $medecin = Medecin::factory()->create([
            'civilite' => 'Dr.',
            'prenom' => 'Youssef',
            'nom' => 'Bennis',
            'specialite' => 'Cardiologie',
        ]);

        $payload = [
            'numero_ordonnance' => 'ORD-TEST-002',
            'patient_id' => $patient->id,
            'consultation_id' => null,
            'date_prescription' => '2026-03-15',
            'diagnostic' => 'Suivi',
            'instructions' => 'Controle',
            'medicaments' => [],
        ];

        if (Schema::hasColumn('ordonnances', 'medecin_id')) {
            $payload['medecin_id'] = $medecin->id;
        }
        if (Schema::hasColumn('ordonnances', 'statut')) {
            $payload['statut'] = 'active';
        }

        $ordonnance = Ordonnance::create($payload);

        $response = $this->actingAs($user)->get(route('ordonnances.show', $ordonnance));

        $response->assertOk();
        if (Schema::hasColumn('ordonnances', 'medecin_id')) {
            $response->assertSee('Youssef Bennis');
            $response->assertSee('Cardiologie');
        }
    }
}
