<?php

namespace Tests\Feature\Medisys;

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CriticalModuleFixesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_and_report_routes_resolve_to_their_dedicated_actions(): void
    {
        $router = app('router');

        $this->assertSame(
            'contacts.export',
            $router->getRoutes()->match(Request::create('/contacts/export', 'GET'))->getName()
        );
        $this->assertSame(
            'depenses.export',
            $router->getRoutes()->match(Request::create('/depenses/export', 'GET'))->getName()
        );
        $this->assertSame(
            'depenses.statistiques',
            $router->getRoutes()->match(Request::create('/depenses/statistiques', 'GET'))->getName()
        );
        $this->assertSame(
            'medicaments.reports',
            $router->getRoutes()->match(Request::create('/medicaments/reports', 'GET'))->getName()
        );
        $this->assertSame(
            'certificats.export',
            $router->getRoutes()->match(Request::create('/certificats/export', 'GET'))->getName()
        );
    }

    public function test_planning_route_redirects_to_the_real_agenda_workflow(): void
    {
        $user = User::factory()->create([
            'role' => 'medecin',
            'module_permissions' => ['planning' => true],
        ]);

        $response = $this->actingAs($user)->get(route('planning.index', [
            'date' => '2026-03-14',
            'view' => 'week',
        ]));

        $response->assertRedirect(route('agenda.index', [
            'date' => '2026-03-14',
            'view' => 'week',
        ]));
    }

    public function test_facture_update_persists_header_and_line_items(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['facturation' => true],
        ]);

        $patient = Patient::factory()->create();
        $newPatient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();

        $facture = Facture::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'montant_total' => 100,
            'remise' => 0,
            'notes' => 'Ancienne note',
        ]);

        LigneFacture::create([
            'facture_id' => $facture->id,
            'description' => 'Ancienne ligne',
            'quantite' => 1,
            'prix_unitaire' => 100,
            'total_ligne' => 100,
            'type' => 'prestation',
        ]);

        $response = $this->actingAs($user)->put(route('factures.update', $facture), [
            'patient_id' => $newPatient->id,
            'medecin_id' => $medecin->id,
            'date_facture' => now()->toDateString(),
            'date_echeance' => now()->addDays(10)->toDateString(),
            'remise' => 15,
            'notes' => 'Note mise a jour',
            'prestations' => [
                [
                    'description' => 'Consultation specialisee',
                    'quantite' => 2,
                    'prix_unitaire' => 150,
                ],
                [
                    'description' => 'Acte complementaire',
                    'quantite' => 1,
                    'prix_unitaire' => 50,
                ],
            ],
        ]);

        $response->assertRedirect(route('factures.show', $facture));

        $this->assertDatabaseHas('factures', [
            'id' => $facture->id,
            'patient_id' => $newPatient->id,
            'montant_total' => 350,
            'remise' => 15,
            'notes' => 'Note mise a jour',
        ]);

        $this->assertDatabaseMissing('ligne_factures', [
            'facture_id' => $facture->id,
            'description' => 'Ancienne ligne',
        ]);

        $this->assertDatabaseHas('ligne_factures', [
            'facture_id' => $facture->id,
            'description' => 'Consultation specialisee',
            'quantite' => 2,
            'prix_unitaire' => 150,
            'total_ligne' => 300,
        ]);
    }

    public function test_depenses_export_and_statistiques_routes_are_operational(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['depenses' => true],
        ]);

        $this->actingAs($user)->post(route('depenses.store'), [
            'description' => 'Fournitures cabinet',
            'details' => 'Papeterie',
            'montant' => 120.50,
            'date_depense' => now()->toDateString(),
            'categorie' => 'fournitures',
            'beneficiaire' => 'Librairie',
            'statut' => 'payee',
            'facture_numero' => 'DEP-001',
            'mode_paiement' => 'especes',
        ])->assertRedirect(route('depenses.index'));

        $this->actingAs($user)->post(route('depenses.store'), [
            'description' => 'Maintenance',
            'details' => 'Imprimante',
            'montant' => 80,
            'date_depense' => now()->toDateString(),
            'categorie' => 'maintenance',
            'beneficiaire' => 'Prestataire IT',
            'statut' => 'en_attente',
            'facture_numero' => 'DEP-002',
            'mode_paiement' => 'virement',
        ])->assertRedirect(route('depenses.index'));

        $exportResponse = $this->actingAs($user)->get(route('depenses.export'));
        $exportResponse->assertOk();
        $this->assertStringContainsString('text/csv', (string) $exportResponse->headers->get('content-type'));
        $this->assertStringContainsString('Fournitures cabinet', $exportResponse->streamedContent());

        $statsResponse = $this->actingAs($user)->getJson(route('depenses.statistiques'));
        $statsResponse
            ->assertOk()
            ->assertJsonPath('total_depenses', 2)
            ->assertJsonPath('montant_total', 200.5);
    }

    public function test_depenses_statistiques_route_renders_an_html_page_for_browser_navigation(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['depenses' => true],
        ]);

        $response = $this->actingAs($user)->get(route('depenses.statistiques'));

        $response
            ->assertOk()
            ->assertSee('Repartition par statut', false)
            ->assertSee('Repartition par categorie', false);
    }

    public function test_dashboard_urgent_consultations_widget_endpoint_returns_only_todays_urgent_items(): void
    {
        $user = User::factory()->create([
            'role' => 'medecin',
            'module_permissions' => ['planning' => true, 'patients' => true],
        ]);

        $patient = Patient::factory()->create([
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
        ]);

        $medecin = Medecin::factory()->create([
            'nom' => 'Zarrik',
            'prenom' => 'Mohammed',
        ]);

        RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => now()->setTime(11, 0),
            'type' => 'urgence',
            'motif' => 'Urgence',
            'statut' => 'en_attente',
        ]);

        RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => now()->addDay()->setTime(14, 0),
            'type' => 'urgence',
            'motif' => 'Urgence demain',
            'statut' => 'a_venir',
        ]);

        RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => now()->setTime(9, 30),
            'type' => 'consultation',
            'motif' => 'Controle',
            'statut' => 'en_attente',
        ]);

        $response = $this->actingAs($user)->getJson(route('dashboard.urgent-consultations'));

        $response
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('items.0.patient_name', 'Ahmed Bennani')
            ->assertJsonPath('items.0.time', '11:00');

        $this->assertStringContainsString('type=urgence', (string) $response->json('all_url'));
    }
}



