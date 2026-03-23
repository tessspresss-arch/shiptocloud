<?php

namespace Tests\Feature\Medisys;

use App\Models\Medecin;
use App\Models\Medicament;
use App\Models\ModeleOrdonnance;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdonnanceTemplateManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_toggle_an_ordonnance_template_from_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $medecin = Medecin::factory()->create([
            'prenom' => 'Lina',
            'nom' => 'Berrada',
        ]);

        $medicament = Medicament::create([
            'nom_commercial' => 'Doliprane',
            'dci' => 'Paracetamol',
            'code_cip' => '3400000000010',
            'code_medicament' => 'MED-DOLI-SET-01',
            'presentation' => '500 mg',
            'posologie' => '1 comprime matin et soir',
        ]);

        $response = $this->actingAs($admin)->post(route('parametres.ordonnances.templates.store'), [
            'nom' => 'Douleur simple',
            'categorie' => 'Douleur / antalgique',
            'diagnostic_contexte' => 'Douleur sans signe de gravite.',
            'instructions_generales' => 'Hydratation et recontrole si aggravation.',
            'medecin_id' => $medecin->id,
            'est_template_general' => '0',
            'is_actif' => '1',
            'medicaments_template' => [
                [
                    'medicament_id' => $medicament->id,
                    'posologie' => '1 comprime matin et soir',
                    'duree' => '5 jours',
                    'quantite' => '10',
                    'instructions' => 'Apres repas',
                ],
            ],
        ]);

        $response
            ->assertRedirect(route('parametres.index') . '#ordonnances')
            ->assertSessionHas('success');

        $template = ModeleOrdonnance::query()->latest('id')->first();

        $this->assertNotNull($template);
        $this->assertSame('Douleur simple', $template->nom);
        $this->assertSame('Douleur / antalgique', $template->categorie);
        $this->assertSame($medecin->id, $template->medecin_id);
        $this->assertFalse($template->est_template_general);
        $this->assertTrue($template->is_actif);
        $this->assertSame($medicament->id, $template->medicaments_template[0]['medicament_id']);
        $this->assertStringContainsString('Hydratation et recontrole', (string) $template->contenu_html);

        $toggleResponse = $this->actingAs($admin)->patch(route('parametres.ordonnances.templates.toggle', $template));

        $toggleResponse
            ->assertRedirect(route('parametres.index') . '#ordonnances')
            ->assertSessionHas('success');

        $this->assertFalse($template->fresh()->is_actif);
    }

    public function test_ordonnance_create_page_exposes_structured_template_catalog(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $medecin = Medecin::factory()->create([
            'prenom' => 'Salma',
            'nom' => 'Alaoui',
            'email' => $admin->email,
        ]);

        $patient = Patient::factory()->create();
        $medicament = Medicament::create([
            'nom_commercial' => 'Augmentin',
            'dci' => 'Amoxicilline',
            'code_cip' => '3400000000011',
            'code_medicament' => 'MED-AUG-TPL-01',
            'presentation' => '1 g',
            'posologie' => '1 comprime matin et soir',
        ]);

        ModeleOrdonnance::query()->create([
            'nom' => 'Infection ORL',
            'categorie' => 'Infection / antibiotique',
            'diagnostic_contexte' => 'Infection ORL sans signe de gravite.',
            'instructions_generales' => 'Boire beaucoup d eau et terminer le traitement.',
            'medicaments_template' => [
                [
                    'medicament_id' => $medicament->id,
                    'medicament_label' => 'Augmentin (1 g)',
                    'posologie' => '1 comprime matin et soir',
                    'duree' => '6 jours',
                    'quantite' => '12',
                    'instructions' => 'Apres le repas',
                ],
            ],
            'contenu_html' => 'Infection ORL sans signe de gravite.',
            'medecin_id' => null,
            'est_template_general' => true,
            'is_actif' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('ordonnances.create', ['patient_id' => $patient->id]));

        $response->assertOk();
        $response->assertSee('Infection ORL');
        $response->assertSee('"diagnostic":"Infection ORL sans signe de gravite."', false);
        $response->assertSee('"medications":[{"medicament_id":' . $medicament->id, false);
        $response->assertSee('"instructions":"Boire beaucoup d eau et terminer le traitement."', false);
    }
}
