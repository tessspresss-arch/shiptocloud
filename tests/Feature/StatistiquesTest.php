<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Facture;
use App\Models\Medecin;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatistiquesTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistiques_page_loads()
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/statistiques')
            ->assertStatus(200)
            ->assertSee('Tableau de bord statistiques')
            ->assertSee('Total patients')
            ->assertSee('Consultations');
    }

    public function test_statistics_calculations_are_accurate()
    {
        $patients = Patient::factory(5)->create();
        $medecin = Medecin::factory()->create();

        Consultation::factory(10)->create([
            'patient_id' => $patients->first()->id,
            'medecin_id' => $medecin->id,
            'created_at' => now()->subDays(15),
        ]);

        Facture::factory(3)->create([
            'patient_id' => $patients->first()->id,
            'statut' => "pay\u{00E9}e",
            'montant_total' => 300,
            'date_facture' => now()->subDays(10),
        ]);

        $user = \App\Models\User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)
            ->get('/statistiques?periode=30');

        $response->assertStatus(200)
            ->assertSee('Consultations')
            ->assertSee('Revenus');
    }

    public function test_period_filter_works()
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);

        $periods = [7, 30, 90, 365];

        foreach ($periods as $period) {
            $response = $this->actingAs($user)
                ->get("/statistiques?periode={$period}");

            $response->assertStatus(200);
        }
    }

    public function test_charts_render_correctly()
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)
            ->get('/statistiques');

        $response->assertSee('Consultations par medecin');
        $response->assertSee('Synthese mensuelle');
    }

    public function test_unauthorized_access_is_blocked()
    {
        $user = \App\Models\User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get('/statistiques')
            ->assertStatus(403);
    }

    public function test_export_functionality()
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)
            ->get('/statistiques/export');

        $response->assertStatus(200);
        $this->assertStringContainsString(
            'attachment; filename=statistiques_',
            (string) $response->headers->get('content-disposition')
        );
    }
}
