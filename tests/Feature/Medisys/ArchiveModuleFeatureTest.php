<?php

namespace Tests\Feature\Medisys;

use App\Models\CategorieDocument;
use App\Models\DocumentMedical;
use App\Models\Patient;
use App\Models\PatientArchive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveModuleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_archives_page_uses_controller_data_and_shows_patient_archive(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $patient = Patient::factory()->create([
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
        ]);

        $archive = PatientArchive::create([
            'patient_id' => $patient->id,
        ]);

        $category = CategorieDocument::query()->firstOrCreate([
            'nom' => 'Compte rendu medical',
        ], [
            'nom' => 'Compte rendu medical',
            'description' => 'Compte rendu',
            'couleur' => '#2563eb',
            'icone' => 'fas fa-file-medical',
            'duree_conservation_ans' => 10,
            'confidentiel' => false,
            'actif' => true,
            'ordre' => 1,
            'est_document_patient' => true,
        ]);

        DocumentMedical::create([
            'patient_archive_id' => $archive->id,
            'categorie_document_id' => $category->id,
            'nom_fichier' => 'compte-rendu.pdf',
            'nom_original' => 'compte-rendu.pdf',
            'chemin_fichier' => 'documents/test/compte-rendu.pdf',
            'mime_type' => 'application/pdf',
            'taille_fichier' => 1024,
            'extension' => 'pdf',
            'description' => 'Document archive',
            'hash_fichier' => str_repeat('a', 64),
        ]);

        $response = $this->actingAs($user)->get(route('archives.index', [
            'q' => 'Bennani',
        ]));

        $response
            ->assertOk()
            ->assertSee('Archives des Patients')
            ->assertSee('Bennani')
            ->assertSee('Archive');
    }
}



