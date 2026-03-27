<?php

namespace Tests\Feature\Medisys;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\SMSReminder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaViewsFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function planningUser(): User
    {
        return User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['planning' => true, 'patients' => true, 'consultations' => true],
        ]);
    }

    public function test_week_view_renders_appointments_across_the_selected_week(): void
    {
        $user = $this->planningUser();
        $medecin = Medecin::factory()->create(['nom' => 'Zarrik', 'prenom' => 'Mohammed']);
        $patientA = Patient::factory()->create(['nom' => 'Bennani', 'prenom' => 'Ahmed']);
        $patientB = Patient::factory()->create(['nom' => 'Idrissi', 'prenom' => 'Fatima']);

        RendezVous::factory()->create([
            'patient_id' => $patientA->id,
            'medecin_id' => $medecin->id,
            'date_heure' => '2026-03-18 10:00:00',
            'statut' => 'en_attente',
            'type' => 'Consultation',
        ]);

        RendezVous::factory()->create([
            'patient_id' => $patientB->id,
            'medecin_id' => $medecin->id,
            'date_heure' => '2026-03-20 14:30:00',
            'statut' => 'a_venir',
            'type' => 'Controle',
        ]);

        $response = $this->actingAs($user)->get(route('agenda.index', [
            'date' => '2026-03-18',
            'view' => 'week',
        ]));

        $response
            ->assertOk()
            ->assertSee('agenda-week-grid', false)
            ->assertSee('Ahmed Bennani')
            ->assertSee('Fatima Idrissi');
    }

    public function test_month_view_renders_month_grid_with_appointments(): void
    {
        $user = $this->planningUser();
        $medecin = Medecin::factory()->create();
        $patient = Patient::factory()->create(['nom' => 'Bennani', 'prenom' => 'Ahmed']);

        RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => '2026-03-05 11:00:00',
            'statut' => 'en_attente',
            'type' => 'Urgence',
            'motif' => 'Urgence',
        ]);

        $response = $this->actingAs($user)->get(route('agenda.index', [
            'date' => '2026-03-14',
            'view' => 'month',
        ]));

        $response
            ->assertOk()
            ->assertSee('agenda-month-grid', false)
            ->assertSee('Ahmed Bennani')
            ->assertSee('11:00');
    }

    public function test_day_view_extends_visible_hours_when_appointments_are_outside_default_range(): void
    {
        $user = $this->planningUser();
        $medecin = Medecin::factory()->create();
        $patient = Patient::factory()->create();

        RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => '2026-03-14 07:30:00',
            'statut' => 'a_venir',
            'type' => 'Consultation',
        ]);

        RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => '2026-03-14 20:30:00',
            'statut' => 'a_venir',
            'type' => 'Consultation',
        ]);

        $response = $this->actingAs($user)->get(route('agenda.index', [
            'date' => '2026-03-14',
            'view' => 'day',
        ]));

        $response
            ->assertOk()
            ->assertSee('07:00')
            ->assertSee('20:00');
    }

    public function test_agenda_status_actions_submit_statut_and_redirect_back_for_html_requests(): void
    {
        $user = $this->planningUser();
        $medecin = Medecin::factory()->create();
        $patient = Patient::factory()->create(['nom' => 'Bennani', 'prenom' => 'Ahmed']);

        $rdv = RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => '2026-03-14 10:00:00',
            'statut' => 'en_attente',
            'type' => 'Consultation',
        ]);

        $agendaUrl = route('agenda.index', ['date' => '2026-03-14', 'view' => 'day']);

        $this->actingAs($user)
            ->get($agendaUrl)
            ->assertSee('name="statut" value="en_soins"', false);

        $response = $this->actingAs($user)
            ->from($agendaUrl)
            ->post(route('rendezvous.update_status', $rdv->id), [
                'statut' => 'en_soins',
            ]);

        $response->assertRedirect($agendaUrl);
        $this->assertSame('en_soins', $rdv->fresh()->statut);
    }

    public function test_day_view_renders_sms_modal_triggers_for_appointments(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => [
                'planning' => true,
                'patients' => true,
                'consultations' => true,
                'sms' => true,
            ],
        ]);

        $medecin = Medecin::factory()->create(['nom' => 'Zarrik', 'prenom' => 'Mohammed']);
        $patient = Patient::factory()->create([
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
            'telephone' => '0612345678',
        ]);

        RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => '2026-03-24 10:30:00',
            'statut' => 'a_venir',
            'type' => 'Consultation',
        ]);

        $response = $this->actingAs($user)->get(route('agenda.index', [
            'date' => '2026-03-24',
            'view' => 'day',
        ]));

        $response
            ->assertOk()
            ->assertSee('agendaSmsModal', false)
            ->assertSee('data-agenda-sms-trigger', false)
            ->assertSee(route('sms.store'), false);
    }

    public function test_sms_store_returns_json_for_agenda_modal_requests(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => [
                'planning' => true,
                'patients' => true,
                'consultations' => true,
                'sms' => true,
            ],
        ]);

        $medecin = Medecin::factory()->create(['nom' => 'Zarrik', 'prenom' => 'Mohammed']);
        $patient = Patient::factory()->create([
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
            'telephone' => '0612345678',
        ]);

        $rdv = RendezVous::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_heure' => now()->addDay(),
            'statut' => 'a_venir',
            'type' => 'Consultation',
        ]);

        $response = $this->actingAs($user)->postJson(route('sms.store'), [
            'rendezvous_id' => $rdv->id,
            'telephone' => '0612345678',
            'message_template' => 'Bonjour Ahmed, rappel de votre rendez-vous.',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Rappel SMS cree avec succes.',
            ]);

        $this->assertDatabaseHas((new SMSReminder())->getTable(), [
            'rendezvous_id' => $rdv->id,
            'patient_id' => $patient->id,
            'telephone' => '0612345678',
            'statut' => 'planifie',
        ]);
    }
}
