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

    public function test_patient_show_page_exposes_quick_ordonnance_modal(): void
    {
        $medecin = Medecin::factory()->create([
            'civilite' => 'Dr.',
            'prenom' => 'Imane',
            'nom' => 'Rami',
            'email' => 'imane.rami@medisys.test',
        ]);

        $user = User::factory()->create([
            'name' => 'Imane Rami',
            'email' => 'imane.rami@medisys.test',
            'role' => 'admin',
        ]);

        $patient = Patient::factory()->create([
            'prenom' => 'Samir',
            'nom' => 'Tazi',
            'allergies' => 'Aucune',
        ]);

        Medicament::create([
            'nom_commercial' => 'Nurofen',
            'dci' => 'Ibuprofene',
            'code_cip' => '3400000000100',
            'code_medicament' => 'MED-NUR-01',
            'presentation' => '400 mg',
            'posologie' => '1 comprime apres repas',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('patients.show', $patient));

        $response->assertOk();
        $response->assertSee('id="modal-ordonnance"', false);
        $response->assertSee('patientOrdonnanceModalForm', false);
        $response->assertSee('patientOrdonnanceModalPayload', false);
        $response->assertSee(route('ordonnances.store.quick'), false);
        $response->assertDontSee(route('ordonnances.create', ['patient_id' => $patient->id]), false);
        $response->assertSee('Samir Tazi');
    }

    public function test_consultation_show_page_exposes_quick_ordonnance_modal_with_consultation_context(): void
    {
        $medecin = Medecin::factory()->create([
            'civilite' => 'Dr.',
            'prenom' => 'Sara',
            'nom' => 'Lahlou',
            'email' => 'sara.lahlou@medisys.test',
        ]);

        $user = User::factory()->create([
            'name' => 'Sara Lahlou',
            'email' => 'sara.lahlou@medisys.test',
            'role' => 'admin',
        ]);

        $patient = Patient::factory()->create([
            'prenom' => 'Yassine',
            'nom' => 'Berrada',
            'allergies' => 'Iode',
        ]);

        $consultation = Consultation::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
        ]);

        $medicament = Medicament::create([
            'nom_commercial' => 'Spasfon',
            'dci' => 'Phloroglucinol',
            'code_cip' => '3400000000200',
            'code_medicament' => 'MED-SPA-02',
            'presentation' => '80 mg',
            'posologie' => '1 comprime si douleur',
        ]);

        $ordonnancePayload = [
            'numero_ordonnance' => 'ORD-CONSULT-001',
            'patient_id' => $patient->id,
            'consultation_id' => $consultation->id,
            'date_prescription' => '2026-03-23',
            'instructions' => 'Hydratation renforcee',
            'medicaments' => [[
                'medicament_id' => $medicament->id,
                'posologie' => '80mg - si douleur',
                'duree' => '5 jours',
                'quantite' => '80mg',
                'instructions' => 'si douleur',
            ]],
        ];

        if (Schema::hasColumn('ordonnances', 'medecin_id')) {
            $ordonnancePayload['medecin_id'] = $medecin->id;
        }

        if (Schema::hasColumn('ordonnances', 'statut')) {
            $ordonnancePayload['statut'] = 'active';
        }

        Ordonnance::create($ordonnancePayload);

        $response = $this
            ->actingAs($user)
            ->get(route('consultations.show', $consultation));

        $response->assertOk();
        $response->assertSee('id="modal-ordonnance"', false);
        $response->assertSee('patientOrdonnanceModalForm', false);
        $response->assertSee('patientOrdonnanceModalPayload', false);
        $response->assertSee('name="consultation_id" value="' . $consultation->id . '"', false);
        $response->assertSee(route('ordonnances.store.quick'), false);
        $response->assertDontSee(route('ordonnances.create', ['consultation_id' => $consultation->id]), false);
        $response->assertSee('Hydratation renforcee');
        $response->assertSee('data-consultation-ordonnances-count', false);
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

    public function test_quick_store_route_returns_json_for_ajax_modal_submission(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $patient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();
        $medicament = Medicament::create([
            'nom_commercial' => 'Azithro',
            'dci' => 'Azithromycine',
            'code_cip' => '3400000000101',
            'code_medicament' => 'MED-AZI-01',
            'presentation' => '500 mg',
            'posologie' => '1 comprime par jour',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('ordonnances.store.quick'), [
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'date_prescription' => '2026-03-23',
                'instructions' => 'Boire beaucoup d eau',
                'medicaments' => [
                    [
                        'medicament_id' => $medicament->id,
                        'medicament_label' => 'Azithro (500 mg)',
                        'posologie' => '500mg - 1x/jour',
                        'duree' => '3 jours',
                        'quantite' => '500mg',
                        'instructions' => '1x/jour',
                    ],
                ],
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'patient_id' => $patient->id,
            'ordonnances_count' => 1,
            'prescriptions_count' => 1,
        ]);

        $ordonnance = Ordonnance::query()->latest('id')->first();

        $this->assertNotNull($ordonnance);
        $this->assertSame($patient->id, $ordonnance->patient_id);
        $this->assertSame('500mg - 1x/jour', $ordonnance->medicaments[0]['posologie']);
        $this->assertSame('500mg', $ordonnance->medicaments[0]['quantite']);
        $this->assertSame('1x/jour', $ordonnance->medicaments[0]['instructions']);
    }

    public function test_quick_store_route_returns_patient_and_consultation_counts_for_consultation_modal_submission(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $patient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();
        $consultation = Consultation::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
        ]);
        $medicament = Medicament::create([
            'nom_commercial' => 'Doliprane',
            'dci' => 'Paracetamol',
            'code_cip' => '3400000000102',
            'code_medicament' => 'MED-DOL-03',
            'presentation' => '1 g',
            'posologie' => '1 comprime matin et soir',
        ]);

        $existingOrdonnance = [
            'numero_ordonnance' => 'ORD-COUNT-001',
            'patient_id' => $patient->id,
            'consultation_id' => null,
            'date_prescription' => '2026-03-22',
            'instructions' => 'Ordonnance precedente',
            'medicaments' => [],
        ];

        if (Schema::hasColumn('ordonnances', 'medecin_id')) {
            $existingOrdonnance['medecin_id'] = $medecin->id;
        }

        if (Schema::hasColumn('ordonnances', 'statut')) {
            $existingOrdonnance['statut'] = 'active';
        }

        Ordonnance::create($existingOrdonnance);

        $response = $this
            ->actingAs($user)
            ->post(route('ordonnances.store.quick'), [
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'consultation_id' => $consultation->id,
                'date_prescription' => '2026-03-23',
                'instructions' => 'Nouvelle ordonnance consultation',
                'medicaments' => [
                    [
                        'medicament_id' => $medicament->id,
                        'medicament_label' => 'Doliprane (1 g)',
                        'posologie' => '1g - 2x/jour',
                        'duree' => '4 jours',
                        'quantite' => '1g',
                        'instructions' => '2x/jour',
                    ],
                ],
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'patient_id' => $patient->id,
            'consultation_id' => $consultation->id,
            'patient_ordonnances_count' => 2,
            'consultation_ordonnances_count' => 1,
        ]);
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
