<?php

namespace Tests\Feature\Medisys;

use App\Models\Examen;
use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Ordonnance;
use App\Models\Patient;
use App\Models\PatientArchive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CoreCrudRegressionFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_medecin_show_update_and_destroy_workflow_is_operational(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['medecins' => true],
        ]);

        $medecin = Medecin::factory()->create([
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
            'statut' => 'actif',
        ]);

        $patient = Patient::factory()->create();

        $consultation = Consultation::query()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_consultation' => '2026-03-24',
            'diagnostic' => 'Controle annuel',
        ]);

        $ordonnancePayload = [
            'numero_ordonnance' => 'ORD-SHOW-TEST-0001',
            'patient_id' => $patient->id,
            'consultation_id' => $consultation->id,
            'date_prescription' => '2026-03-24',
            'observations' => 'Ordonnance liee a la fiche medecin.',
            'instructions' => 'Apres le repas.',
            'medicaments' => [
                [
                    'medicament_label' => 'Paracetamol',
                    'posologie' => '1 comprime',
                    'duree' => '5 jours',
                ],
            ],
        ];

        if (Schema::hasColumn('ordonnances', 'medecin_id')) {
            $ordonnancePayload['medecin_id'] = $medecin->id;
        }

        if (Schema::hasColumn('ordonnances', 'date_expiration')) {
            $ordonnancePayload['date_expiration'] = '2026-04-24';
        }

        if (Schema::hasColumn('ordonnances', 'statut')) {
            $ordonnancePayload['statut'] = 'active';
        }

        Ordonnance::query()->create($ordonnancePayload);

        $this->actingAs($user)
            ->get(route('medecins.show', $medecin))
            ->assertOk()
            ->assertSee('Ahmed')
            ->assertSee('Bennani')
            ->assertViewHas('medecin', function (Medecin $loadedMedecin): bool {
                return (int) ($loadedMedecin->consultations_count ?? 0) === 1
                    && (int) ($loadedMedecin->ordonnances_count ?? 0) === 1;
            });

        $this->actingAs($user)
            ->put(route('medecins.update', $medecin), [
                'civilite' => 'Dr.',
                'nom' => 'Bennani',
                'prenom' => 'Youssef',
                'specialite' => 'Cardiologie',
                'numero_ordre' => $medecin->numero_ordre,
                'telephone' => '+212612345678',
                'email' => 'youssef.bennani@example.test',
                'adresse_cabinet' => 'Boulevard Hassan II',
                'ville' => 'Casablanca',
                'code_postal' => '20000',
                'statut' => 'en_conge',
                'tarif_consultation' => 350,
                'date_embauche' => '2024-01-01',
                'date_depart' => null,
                'notes' => 'Disponible sur rendez-vous.',
            ])
            ->assertRedirect(route('medecins.show', $medecin));

        $this->assertDatabaseHas('medecins', [
            'id' => $medecin->id,
            'prenom' => 'Youssef',
            'specialite' => 'Cardiologie',
            'statut' => 'en_conge',
            'email' => 'youssef.bennani@example.test',
        ]);

        $this->actingAs($user)
            ->delete(route('medecins.destroy', $medecin))
            ->assertRedirect(route('medecins.index'));

        $this->assertSoftDeleted('medecins', [
            'id' => $medecin->id,
        ]);
    }

    public function test_patient_destroy_removes_patient_and_archive_when_no_related_records_exist(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['patients' => true],
        ]);

        $patient = Patient::factory()->create();
        $archive = PatientArchive::create([
            'patient_id' => $patient->id,
            'donnees' => ['source' => 'test'],
        ]);

        $this->actingAs($user)
            ->delete(route('patients.destroy', $patient))
            ->assertRedirect(route('patients.index'));

        $this->assertDatabaseMissing('patients', [
            'id' => $patient->id,
        ]);

        $this->assertDatabaseMissing('patient_archives', [
            'id' => $archive->id,
        ]);
    }

    public function test_examens_store_update_and_show_support_the_current_form_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => [
                'examens' => true,
                'patients' => true,
            ],
        ]);

        $patient = Patient::factory()->create([
            'nom' => 'Idrissi',
            'prenom' => 'Fatima',
        ]);

        $medecin = Medecin::factory()->create();

        $this->actingAs($user)
            ->post(route('examens.store'), [
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'type_examen' => 'Analyse de sang',
                'date_examen' => '2026-03-15',
                'description' => 'Controle biologique',
                'localisation' => 'Laboratoire central',
                'observations' => 'Prelevement a jeun.',
                'statut' => 'demande',
            ])
            ->assertRedirect(route('patients.show', $patient));

        /** @var Examen $examen */
        $examen = Examen::query()->latest('id')->firstOrFail();

        $this->assertSame('Analyse de sang', $examen->nom_examen);
        $this->assertSame('biologie', $examen->type);
        $this->assertSame('Laboratoire central', $examen->lieu_realisation);

        $this->actingAs($user)
            ->get(route('examens.show', $examen))
            ->assertOk()
            ->assertSee('Analyse de sang')
            ->assertSee('Fatima Idrissi');

        $this->actingAs($user)
            ->put(route('examens.update', $examen), [
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'type_examen' => 'Radiographie',
                'date_examen' => '2026-03-20',
                'description' => 'Controle radiologique',
                'localisation' => 'Imagerie A',
                'observations' => 'Urgent.',
                'statut' => 'en_cours',
            ])
            ->assertRedirect(route('examens.show', $examen));

        $this->assertDatabaseHas('examens', [
            'id' => $examen->id,
            'nom_examen' => 'Radiographie',
            'type' => 'imagerie',
            'lieu_realisation' => 'Imagerie A',
            'statut' => 'en_attente',
        ]);
    }
}
