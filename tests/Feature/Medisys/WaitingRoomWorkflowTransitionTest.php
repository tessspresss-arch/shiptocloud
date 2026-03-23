<?php

namespace Tests\Feature\Medisys;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\RendezVousStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaitingRoomWorkflowTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_status_actions_update_status_and_transition_timestamps(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['planning' => true],
        ]);

        $patient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();

        $rdv = RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'statut' => 'a_venir',
            'arrived_at' => null,
            'consultation_started_at' => null,
            'consultation_finished_at' => null,
        ]);

        $this->actingAs($user)
            ->postJson(route('rendezvous.update_status', $rdv->id), ['statut' => 'en_attente'])
            ->assertOk()
            ->assertJsonPath('rendezvous.statut', 'en_attente');

        $this->actingAs($user)
            ->postJson(route('rendezvous.update_status', $rdv->id), ['statut' => 'en_soins'])
            ->assertOk()
            ->assertJsonPath('rendezvous.statut', 'en_soins');

        $this->actingAs($user)
            ->postJson(route('rendezvous.update_status', $rdv->id), ['statut' => 'vu'])
            ->assertOk()
            ->assertJsonPath('rendezvous.statut', 'vu');

        $rdv->refresh();

        $this->assertNotNull($rdv->arrived_at);
        $this->assertNotNull($rdv->consultation_started_at);
        $this->assertNotNull($rdv->consultation_finished_at);

        $this->assertSame(3, RendezVousStatusHistory::query()->where('rendez_vous_id', $rdv->id)->count());
    }
}



