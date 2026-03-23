<?php

namespace Tests\Feature\Medisys;

use App\Models\Consultation;
use App\Models\ConsultationAiGeneration;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConsultationAiAssistantFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_consultation_summary_and_stores_history(): void
    {
        config([
            'medical_ai.openai.api_key' => 'test-openai-key',
            'medical_ai.openai.model' => 'gpt-5-mini',
            'medical_ai.fallback.enabled' => true,
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'id' => 'resp_test_123',
                'output_text' => json_encode([
                    'motif' => 'Toux et fatigue depuis trois jours',
                    'resume_clinique' => 'Patient sans detresse respiratoire, infection virale probable',
                    'observations' => 'Surveillance de la tolerance respiratoire et reevaluation si aggravation',
                    'conclusion' => 'Tableau clinique rassurant a ce stade',
                    'conduite_a_tenir' => 'Repos, hydratation et controle a 48 heures si persistance',
                ], JSON_UNESCAPED_UNICODE),
            ], 200),
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $patient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();
        $consultation = Consultation::create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date_consultation' => now()->toDateString(),
            'symptomes' => 'Toux seche, fatigue et gene thoracique legere.',
            'examen_clinique' => 'Auscultation correcte, pas de detresse respiratoire.',
            'diagnostic' => 'Infection virale probable.',
            'traitement_prescrit' => 'Hydratation, repos, paracetamol si besoin.',
            'recommandations' => 'Surveillance pendant 48 heures.',
        ]);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_SUMMARY,
            'notes' => "Patient fatigue depuis 3 jours.\nPas de fievre importante.\nA reevaluer si aggravation.",
            'preferred_target' => 'recommandations',
            'field_values' => [
                'symptomes' => $consultation->symptomes,
                'diagnostic' => $consultation->diagnostic,
                'recommandations' => $consultation->recommandations,
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('generation.action_type', ConsultationAiGeneration::ACTION_SUMMARY)
            ->assertJsonPath('generation.suggested_target', 'recommandations')
            ->assertJsonPath('generation.action_label', 'Resume automatique');

        $this->assertDatabaseHas('consultation_ai_generations', [
            'consultation_id' => $consultation->id,
            'user_id' => $user->id,
            'action_type' => ConsultationAiGeneration::ACTION_SUMMARY,
            'suggested_target' => 'recommandations',
        ]);

        $this->assertDatabaseHas('consultation_ai_generations', [
            'consultation_id' => $consultation->id,
            'user_id' => $user->id,
            'action_type' => ConsultationAiGeneration::ACTION_SUMMARY,
        ]);

        $history = ConsultationAiGeneration::firstOrFail();
        $this->assertStringContainsString('Motif', $history->generated_text);
        $this->assertStringContainsString('Conduite a tenir', $history->generated_text);
        $this->assertSame('openai', data_get($history->context_payload, 'provider'));
        $this->assertSame('gpt-5-mini', data_get($history->context_payload, 'model'));
        $this->assertFalse((bool) data_get($history->context_payload, 'used_fallback'));

        Http::assertSent(function ($request) {
            $payload = $request->data();
            $systemText = data_get($payload, 'input.0.content.0.text', '');
            $userText = data_get($payload, 'input.1.content.0.text', '');

            return $request->url() === 'https://api.openai.com/v1/responses'
                && data_get($payload, 'model') === 'gpt-5-mini'
                && data_get($payload, 'text.format.type') === 'json_schema'
                && data_get($payload, 'reasoning.effort') === 'low'
                && str_contains($systemText, 'Tu ne crees jamais d informations absentes des notes source.')
                && str_contains($systemText, 'Si les notes sont insuffisantes')
                && str_contains($userText, 'Chaque valeur doit contenir une a deux phrases maximum');
        });
    }

    public function test_it_generates_medical_report_and_stores_history(): void
    {
        config([
            'medical_ai.openai.api_key' => 'test-openai-key',
            'medical_ai.openai.model' => 'gpt-5-mini',
            'medical_ai.fallback.enabled' => true,
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'id' => 'resp_test_report_123',
                'output_text' => json_encode([
                    'informations_patient' => 'Ahmed Bennani, consultation realisee avec le Dr Test le 14/03/2026',
                    'motif_consultation' => 'Toux persistante et fatigue',
                    'anamnese_contexte' => 'Symptomes evoluant depuis cinq jours sans signe de gravite immediate',
                    'examen_clinique' => 'Auscultation rassurante, pas de detresse respiratoire',
                    'diagnostic_hypothese' => 'Infection virale probable',
                    'conduite_a_tenir' => 'Traitement symptomatique et surveillance de l evolution',
                    'recommandations' => 'Reevaluation si aggravation ou persistance des symptomes',
                ], JSON_UNESCAPED_UNICODE),
            ], 200),
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create(['nom' => 'Bennani', 'prenom' => 'Ahmed'])->id,
            'medecin_id' => Medecin::factory()->create(['nom' => 'Test', 'prenom' => 'Docteur'])->id,
            'date_consultation' => now()->toDateString(),
            'symptomes' => 'Toux et fatigue.',
            'examen_clinique' => 'Auscultation rassurante.',
            'diagnostic' => 'Infection virale probable.',
            'traitement_prescrit' => 'Traitement symptomatique.',
            'recommandations' => 'Surveillance 48h.',
        ]);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_MEDICAL_REPORT,
            'notes' => 'Patient fatigue depuis cinq jours, toux persistante, sans detresse respiratoire.',
            'summary_text' => "Motif\nToux persistante.\n\nResume clinique\nSuspicion virale simple.",
            'preferred_target' => 'diagnostic',
            'field_values' => [
                'symptomes' => $consultation->symptomes,
                'examen_clinique' => $consultation->examen_clinique,
                'diagnostic' => $consultation->diagnostic,
                'traitement_prescrit' => $consultation->traitement_prescrit,
                'recommandations' => $consultation->recommandations,
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('generation.action_type', ConsultationAiGeneration::ACTION_MEDICAL_REPORT)
            ->assertJsonPath('generation.suggested_target', 'diagnostic')
            ->assertJsonPath('generation.action_label', 'Compte rendu medical')
            ->assertJsonPath('generation.used_fallback', false);

        $history = ConsultationAiGeneration::query()->where('action_type', ConsultationAiGeneration::ACTION_MEDICAL_REPORT)->firstOrFail();
        $this->assertStringContainsString('Informations patient', $history->generated_text);
        $this->assertStringContainsString('Diagnostic ou hypothese', $history->generated_text);
        $this->assertSame('openai', data_get($history->context_payload, 'provider'));
        $this->assertSame('diagnostic', $history->suggested_target);

        Http::assertSent(function ($request) {
            $payload = $request->data();
            return data_get($payload, 'text.format.name') === 'consultation_medical_report'
                && str_contains((string) data_get($payload, 'input.0.content.0.text', ''), 'compte rendu medical')
                && str_contains((string) data_get($payload, 'input.1.content.0.text', ''), 'Resume IA disponible');
        });
    }

    public function test_it_rejects_empty_generation_requests(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_SUMMARY,
            'notes' => '',
            'field_values' => [],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('consultation_ai_generations', 0);
    }

    public function test_it_falls_back_to_local_summary_when_openai_is_not_configured(): void
    {
        config([
            'medical_ai.openai.api_key' => null,
            'medical_ai.fallback.enabled' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
            'symptomes' => 'Douleur thoracique moderee.',
        ]);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_SUMMARY,
            'notes' => 'Douleur depuis ce matin, sans dyspnee.',
            'field_values' => [
                'symptomes' => 'Douleur thoracique moderee.',
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('generation.used_fallback', true)
            ->assertJsonPath('generation.provider', 'local_fallback')
            ->assertJsonPath('message', 'Resume genere en mode secours local. Le contenu reste a relire et valider par le medecin.');

        $history = ConsultationAiGeneration::firstOrFail();
        $this->assertSame('local_fallback', data_get($history->context_payload, 'provider'));
        $this->assertTrue((bool) data_get($history->context_payload, 'used_fallback'));
        $this->assertStringContainsString('OPENAI_API_KEY', (string) data_get($history->context_payload, 'fallback_reason'));
        $this->assertStringContainsString('Motif', $history->generated_text);
    }

    public function test_it_falls_back_to_local_summary_when_openai_quota_is_exceeded(): void
    {
        config([
            'medical_ai.openai.api_key' => 'test-openai-key',
            'medical_ai.fallback.enabled' => true,
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'error' => [
                    'message' => 'You exceeded your current quota, please check your plan and billing details.',
                ],
            ], 429),
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
            'symptomes' => 'Cephalees et fatigue.',
            'diagnostic' => 'Syndrome viral simple.',
            'recommandations' => 'Repos et hydratation.',
        ]);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_SUMMARY,
            'notes' => 'Patient fatigue, sans signe de gravite immediate.',
            'field_values' => [
                'symptomes' => $consultation->symptomes,
                'diagnostic' => $consultation->diagnostic,
                'recommandations' => $consultation->recommandations,
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('generation.used_fallback', true)
            ->assertJsonPath('generation.provider', 'local_fallback')
            ->assertJsonPath('message', 'Resume genere en mode secours local. Le contenu reste a relire et valider par le medecin.');

        $history = ConsultationAiGeneration::firstOrFail();
        $this->assertSame('local_fallback', data_get($history->context_payload, 'provider'));
        $this->assertTrue((bool) data_get($history->context_payload, 'used_fallback'));
        $this->assertStringContainsString('quota', strtolower((string) data_get($history->context_payload, 'fallback_reason')));
    }

    public function test_it_falls_back_to_local_medical_report_when_openai_quota_is_exceeded(): void
    {
        config([
            'medical_ai.openai.api_key' => 'test-openai-key',
            'medical_ai.fallback.enabled' => true,
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'error' => [
                    'message' => 'You exceeded your current quota, please check your plan and billing details.',
                ],
            ], 429),
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
            'diagnostic' => 'Bronchite simple.',
            'recommandations' => 'Repos, surveillance.',
        ]);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_MEDICAL_REPORT,
            'summary_text' => "Motif\nToux.\n\nConclusion\nBronchite simple.",
            'field_values' => [
                'diagnostic' => 'Bronchite simple.',
                'recommandations' => 'Repos, surveillance.',
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('generation.used_fallback', true)
            ->assertJsonPath('generation.provider', 'local_fallback');

        $history = ConsultationAiGeneration::query()->where('action_type', ConsultationAiGeneration::ACTION_MEDICAL_REPORT)->firstOrFail();
        $this->assertStringContainsString('Informations patient', $history->generated_text);
        $this->assertStringContainsString('Recommandations', $history->generated_text);
        $this->assertSame('local_fallback', data_get($history->context_payload, 'provider'));
    }

    public function test_it_returns_prudent_sections_for_short_incomplete_notes(): void
    {
        config([
            'medical_ai.openai.api_key' => null,
            'medical_ai.fallback.enabled' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_SUMMARY,
            'notes' => 'Fatigue depuis hier.',
            'field_values' => [],
        ]);

        $response->assertOk()
            ->assertJsonPath('generation.used_fallback', true);

        $generated = (string) data_get($response->json(), 'generation.generated_text', '');
        $this->assertStringContainsString('Motif', $generated);
        $this->assertStringContainsString('Information insuffisante dans les notes', $generated);
        $this->assertStringContainsString('Conclusion prudente', $generated);
    }

    public function test_it_handles_long_and_disorganized_notes_without_crashing(): void
    {
        config([
            'medical_ai.openai.api_key' => null,
            'medical_ai.fallback.enabled' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
            'diagnostic' => 'Rhinopharyngite probable.',
            'recommandations' => 'Hydratation, lavage de nez, surveillance 48h.',
        ]);

        $notes = "Patient encombre.\n\nToux ++ depuis 5 jours ???\nPas de detresse.\nNotes melangees, examen incomplet.\nA revoir si fievre ou aggravation.\nSommeil perturbe.";

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_SUMMARY,
            'notes' => $notes,
            'field_values' => [
                'diagnostic' => $consultation->diagnostic,
                'recommandations' => $consultation->recommandations,
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('generation.used_fallback', true);

        $generated = (string) data_get($response->json(), 'generation.generated_text', '');
        $this->assertStringContainsString('Resume clinique', $generated);
        $this->assertStringContainsString('Rhinopharyngite probable.', $generated);
        $this->assertStringContainsString('Hydratation, lavage de nez, surveillance 48h.', $generated);
    }

    public function test_it_keeps_returning_an_error_if_fallback_is_disabled(): void
    {
        config([
            'medical_ai.openai.api_key' => null,
            'medical_ai.fallback.enabled' => false,
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
            'symptomes' => 'Palpitations breves.',
        ]);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_SUMMARY,
            'notes' => 'Episodes brefs sans malaise.',
            'field_values' => [
                'symptomes' => 'Palpitations breves.',
            ],
        ]);

        $response->assertStatus(503)
            ->assertJsonPath('message', 'La configuration OpenAI est incomplete. Ajoutez OPENAI_API_KEY avant d utiliser le resume IA.');

        $this->assertDatabaseCount('consultation_ai_generations', 0);
    }

    public function test_it_returns_a_generic_provider_error_when_fallback_is_disabled_and_openai_fails(): void
    {
        config([
            'medical_ai.openai.api_key' => 'test-openai-key',
            'medical_ai.fallback.enabled' => false,
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'error' => [
                    'message' => 'You exceeded your current quota, please check your plan and billing details.',
                ],
            ], 429),
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
            'symptomes' => 'Toux seche.',
        ]);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_SUMMARY,
            'notes' => 'Toux seche depuis 3 jours.',
            'field_values' => [
                'symptomes' => 'Toux seche.',
            ],
        ]);

        $response->assertStatus(503)
            ->assertJsonPath('message', 'Le service IA est temporairement indisponible pour ce compte. Verifiez le quota API ou reessayez plus tard.');
    }

    public function test_it_exports_medical_report_pdf(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->post(route('consultations.ai.export-medical-report', $consultation), [
            'content' => "Informations patient\nPatient test.\n\nMotif de consultation\nDouleur thoracique.",
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_it_accepts_large_diagnostic_field_values_for_medical_report_generation(): void
    {
        config([
            'medical_ai.openai.api_key' => null,
            'medical_ai.fallback.enabled' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['consultations' => true],
        ]);

        $consultation = Consultation::create([
            'patient_id' => Patient::factory()->create()->id,
            'medecin_id' => Medecin::factory()->create()->id,
            'date_consultation' => now()->toDateString(),
        ]);

        $longDiagnostic = str_repeat('Diagnostic detaille. ', 450);

        $response = $this->actingAs($user)->postJson(route('consultations.ai.generate', $consultation), [
            'action' => ConsultationAiGeneration::ACTION_MEDICAL_REPORT,
            'summary_text' => "Motif\nControle.\n\nConclusion\nSituation stable.",
            'field_values' => [
                'diagnostic' => $longDiagnostic,
                'recommandations' => 'Surveillance clinique.',
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('generation.action_type', ConsultationAiGeneration::ACTION_MEDICAL_REPORT);
    }
}



