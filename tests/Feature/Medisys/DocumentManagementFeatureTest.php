<?php

namespace Tests\Feature\Medisys;

use App\Models\CategorieDocument;
use App\Models\Patient;
use App\Models\PatientArchive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_page_bootstraps_default_patient_categories_when_catalog_is_empty(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['documents' => true],
        ]);

        CategorieDocument::query()->delete();
        $this->assertSame(0, CategorieDocument::count());

        $response = $this->actingAs($user)->get(route('documents.upload'));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(10, CategorieDocument::count());
        $this->assertDatabaseHas('categorie_documents', [
            'nom' => 'Ordonnance medicale',
            'actif' => true,
        ]);
    }

    public function test_document_upload_is_attached_to_patient_archive_with_selected_category(): void
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['documents' => true],
        ]);

        $patient = Patient::factory()->create([
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
        ]);

        $category = CategorieDocument::query()
            ->where('nom', "Carte d'identite patient")
            ->firstOrFail();

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'patient_id' => $patient->id,
            'categorie_document_id' => $category->id,
            'source_document' => 'scan_cabinet',
            'description' => 'Carte d identite scannee a l accueil.',
            'fichier' => UploadedFile::fake()->create('carte-identite.pdf', 120, 'application/pdf'),
        ]);

        $response->assertRedirect(route('documents.index'));

        $archive = PatientArchive::query()->where('patient_id', $patient->id)->first();

        $this->assertNotNull($archive);
        $this->assertDatabaseHas('document_medicals', [
            'patient_archive_id' => $archive->id,
            'categorie_document_id' => $category->id,
            'description' => 'Carte d identite scannee a l accueil.',
            'source_document' => 'scan_cabinet',
        ]);
    }

    public function test_document_categories_can_be_created_dynamically_from_categories_module(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['documents' => true],
        ]);

        $response = $this->actingAs($user)->post(route('documents.categories.store'), [
            'nom' => 'Consentement operatoire',
            'description' => 'Documents signes avant intervention.',
            'couleur' => '#0f766e',
            'icone' => 'fas fa-file-circle-check',
            'duree_conservation_ans' => 15,
            'ordre' => 120,
            'actif' => '1',
            'confidentiel' => '1',
            'est_document_patient' => '1',
        ]);

        $response->assertRedirect(route('documents.categories'));

        $this->assertDatabaseHas('categorie_documents', [
            'nom' => 'Consentement operatoire',
            'est_document_patient' => true,
            'confidentiel' => true,
            'actif' => true,
        ]);
    }
}



