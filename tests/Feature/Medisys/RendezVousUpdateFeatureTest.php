<?php

namespace Tests\Feature\Medisys;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\RendezVousStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RendezVousUpdateFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function planningUser(): User
    {
        return User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => [
                'planning' => true,
                'patients' => true,
            ],
        ]);
    }

    public function test_edit_form_status_change_updates_waiting_room_timestamps_and_history(): void
    {
        $user = $this->planningUser();
        $rdv = RendezVous::factory()->create([
            'statut' => 'a_venir',
            'arrived_at' => null,
            'consultation_started_at' => null,
            'consultation_finished_at' => null,
        ]);

        $this->actingAs($user)
            ->put(route('rendezvous.update', $rdv->id), [
                'patient_id' => $rdv->patient_id,
                'medecin_id' => $rdv->medecin_id,
                'date_heure' => $rdv->date_heure->format('Y-m-d\TH:i'),
                'duree' => 30,
                'type' => 'Suivi',
                'motif' => 'Passage en attente',
                'statut' => 'en_attente',
                'notes' => 'Depuis edition',
            ])
            ->assertRedirect(route('rendezvous.show', $rdv->id));

        $rdv->refresh();

        $this->assertSame('en_attente', $rdv->statut);
        $this->assertNotNull($rdv->arrived_at);
        $this->assertSame(1, RendezVousStatusHistory::query()->where('rendez_vous_id', $rdv->id)->count());
    }

    public function test_edit_form_blocks_conflicting_slot_for_same_doctor(): void
    {
        $user = $this->planningUser();
        $medecin = Medecin::factory()->create();
        $firstPatient = Patient::factory()->create();
        $secondPatient = Patient::factory()->create();
        $slot = now()->addDays(3)->startOfDay()->setTime(10, 0);

        RendezVous::factory()->create([
            'patient_id' => $firstPatient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $slot,
            'duree' => 30,
            'statut' => 'a_venir',
        ]);

        $toUpdate = RendezVous::factory()->create([
            'patient_id' => $secondPatient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $slot->copy()->addHour(),
            'duree' => 30,
            'statut' => 'a_venir',
        ]);

        $this->actingAs($user)
            ->from(route('rendezvous.edit', $toUpdate->id))
            ->put(route('rendezvous.update', $toUpdate->id), [
                'patient_id' => $secondPatient->id,
                'medecin_id' => $medecin->id,
                'date_heure' => $slot->format('Y-m-d\TH:i'),
                'duree' => 30,
                'type' => 'Suivi',
                'motif' => 'Conflit potentiel',
                'statut' => 'a_venir',
                'notes' => 'Ne devrait pas passer',
            ])
            ->assertRedirect(route('rendezvous.edit', $toUpdate->id))
            ->assertSessionHasErrors('date_heure');

        $toUpdate->refresh();

        $this->assertSame($slot->copy()->addHour()->format('Y-m-d H:i:s'), $toUpdate->date_heure?->format('Y-m-d H:i:s'));
    }
}



