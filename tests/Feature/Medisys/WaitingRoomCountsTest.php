<?php

namespace Tests\Feature\Medisys;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaitingRoomCountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_waiting_room_counts_are_calculated_per_status_without_duplication(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['planning' => true],
        ]);

        $medecin = Medecin::factory()->create();
        $today = now()->startOfDay();

        RendezVous::factory()->create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(9, 0),
            'statut' => 'a_venir',
        ]);

        RendezVous::factory()->create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(10, 0),
            'statut' => 'en_attente',
        ]);

        RendezVous::factory()->create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(11, 0),
            'statut' => 'en_soins',
        ]);

        RendezVous::factory()->create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(12, 0),
            'statut' => 'vu',
        ]);

        RendezVous::factory()->create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(13, 0),
            'statut' => 'absent',
        ]);

        $response = $this->actingAs($user)->getJson(route('agenda.waiting_room.data', [
            'date' => $today->toDateString(),
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('counts.a_venir', 1)
            ->assertJsonPath('counts.en_attente', 1)
            ->assertJsonPath('counts.en_soins', 1)
            ->assertJsonPath('counts.vu', 1)
            ->assertJsonPath('counts.absent', 1);

        $json = $response->json();
        $this->assertCount(1, $json['lists']['a_venir']);
        $this->assertCount(1, $json['lists']['en_attente']);
        $this->assertCount(1, $json['lists']['en_soins']);
        $this->assertCount(1, $json['lists']['vu']);
        $this->assertCount(1, $json['lists']['absent']);
    }

    public function test_same_patient_is_shown_only_once_using_highest_priority_status(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['planning' => true],
        ]);

        $medecin = Medecin::factory()->create();
        $patient = Patient::factory()->create([
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
        ]);

        $today = now()->startOfDay();

        RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(9, 0),
            'statut' => 'en_soins',
            'motif' => 'Consultation generale',
        ]);

        RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(11, 0),
            'statut' => 'vu',
            'motif' => 'Urgence',
        ]);

        $response = $this->actingAs($user)->getJson(route('agenda.waiting_room.data', [
            'date' => $today->toDateString(),
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('counts.en_soins', 1)
            ->assertJsonPath('counts.vu', 0);

        $json = $response->json();

        $this->assertCount(1, $json['lists']['en_soins']);
        $this->assertCount(0, $json['lists']['vu']);
        $this->assertSame('Consultation generale', $json['lists']['en_soins'][0]['motif']);
    }
}



