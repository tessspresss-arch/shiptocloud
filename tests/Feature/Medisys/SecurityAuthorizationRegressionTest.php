<?php

namespace Tests\Feature\Medisys;

use App\Models\Consultation;
use App\Models\DossierMedical;
use App\Models\Facture;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAuthorizationRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_medecin_cannot_access_another_doctors_facture_resources(): void
    {
        $currentMedecin = Medecin::factory()->create([
            'email' => 'medecin.scope@medisys.test',
        ]);

        $otherMedecin = Medecin::factory()->create();
        $patient = Patient::factory()->create();
        $facture = Facture::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $otherMedecin->id,
        ]);

        $user = User::factory()->create([
            'role' => 'medecin',
            'email' => $currentMedecin->email,
            'module_permissions' => ['facturation' => true],
        ]);

        $this->actingAs($user)
            ->get(route('factures.show', $facture))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('factures.pdf', $facture))
            ->assertForbidden();
    }

    public function test_medecin_only_sees_his_own_agenda_entries(): void
    {
        $currentMedecin = Medecin::factory()->create([
            'email' => 'agenda.scope@medisys.test',
        ]);

        $otherMedecin = Medecin::factory()->create();
        $visiblePatient = Patient::factory()->create([
            'prenom' => 'Nadia',
            'nom' => 'Visible',
        ]);
        $hiddenPatient = Patient::factory()->create([
            'prenom' => 'Samir',
            'nom' => 'Cache',
        ]);

        $visibleRendezVous = RendezVous::factory()->create([
            'patient_id' => $visiblePatient->id,
            'medecin_id' => $currentMedecin->id,
        ]);

        $hiddenRendezVous = RendezVous::factory()->create([
            'patient_id' => $hiddenPatient->id,
            'medecin_id' => $otherMedecin->id,
        ]);

        $user = User::factory()->create([
            'role' => 'medecin',
            'email' => $currentMedecin->email,
            'module_permissions' => ['planning' => true],
        ]);

        $this->actingAs($user)
            ->get(route('rendezvous.index'))
            ->assertOk()
            ->assertSee('Nadia')
            ->assertDontSee('Samir');

        $eventsResponse = $this->actingAs($user)
            ->getJson('/api/rendezvous?date=' . $visibleRendezVous->date_heure->format('Y-m-d'));

        $eventsResponse->assertOk();
        $eventIds = collect($eventsResponse->json())->pluck('id')->map(fn ($id) => (int) $id)->all();
        $this->assertSame([$visibleRendezVous->id], $eventIds);

        $this->actingAs($user)
            ->get(route('rendezvous.show', $hiddenRendezVous))
            ->assertNotFound();
    }

    public function test_medecin_cannot_create_or_preview_ordonnance_for_another_doctors_consultation(): void
    {
        $currentMedecin = Medecin::factory()->create([
            'email' => 'ordonnance.scope@medisys.test',
        ]);

        $otherMedecin = Medecin::factory()->create();
        $patient = Patient::factory()->create();
        $consultation = Consultation::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $otherMedecin->id,
        ]);

        $user = User::factory()->create([
            'role' => 'medecin',
            'email' => $currentMedecin->email,
            'module_permissions' => ['pharmacie' => true],
        ]);

        $payload = [
            'patient_id' => $patient->id,
            'medecin_id' => $otherMedecin->id,
            'consultation_id' => $consultation->id,
            'date_prescription' => '2026-03-24',
            'diagnostic' => 'Controle',
            'instructions' => 'Repos',
            'statut' => 'active',
            'medicaments' => [[
                'medicament_label' => 'Paracetamol',
                'posologie' => '1 comprime',
                'duree' => '5 jours',
                'quantite' => '10',
                'instructions' => 'Apres repas',
            ]],
        ];

        if (!Schema::hasColumn('ordonnances', 'medecin_id')) {
            unset($payload['medecin_id']);
        }

        $this->actingAs($user)
            ->post(route('ordonnances.store.quick'), $payload)
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('ordonnances.preview-pdf'), $payload)
            ->assertForbidden();
    }

    public function test_dossier_uploaded_documents_are_written_to_private_local_storage(): void
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['patients' => true],
        ]);

        $patient = Patient::factory()->create();
        $document = UploadedFile::fake()->create('compte-rendu.pdf', 120, 'application/pdf');

        $this->actingAs($user)
            ->post(route('dossiers.store'), [
                'patient_id' => $patient->id,
                'numero_dossier' => 'DOS-SEC-0001',
                'type' => 'general',
                'date_ouverture' => '2026-03-24',
                'statut' => 'actif',
                'documents' => [$document],
            ])
            ->assertRedirect(route('dossiers.index'));

        $dossier = DossierMedical::query()->latest('id')->firstOrFail();
        $storedDocument = $dossier->documents[0] ?? null;

        $this->assertIsArray($storedDocument);
        $this->assertStringStartsWith('documents/dossiers/', (string) ($storedDocument['path'] ?? ''));
        Storage::disk('local')->assertExists((string) $storedDocument['path']);
    }

    public function test_patient_and_medecin_fallback_avatars_are_generated_locally(): void
    {
        $patient = Patient::factory()->make([
            'photo' => null,
            'prenom' => 'Leila',
            'nom' => 'Amrani',
        ]);

        $medecin = Medecin::factory()->make([
            'photo_path' => null,
            'prenom' => 'Youssef',
            'nom' => 'Bennani',
        ]);

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $patient->avatar_url);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $medecin->avatar_url);
        $this->assertStringNotContainsString('ui-avatars.com', $patient->avatar_url);
        $this->assertStringNotContainsString('ui-avatars.com', $medecin->avatar_url);
    }
}
