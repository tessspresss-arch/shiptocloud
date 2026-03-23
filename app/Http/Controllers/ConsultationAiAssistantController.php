<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateConsultationAiRequest;
use App\Models\Consultation;
use App\Services\MedicalAi\ConsultationAiAssistantService;
use App\Services\Pdf\PdfBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
class ConsultationAiAssistantController extends Controller
{
    public function generate(
        GenerateConsultationAiRequest $request,
        Consultation $consultation,
        ConsultationAiAssistantService $assistantService
    ): JsonResponse {
        $consultation->loadMissing([
            'patient:id,nom,prenom',
            'medecin:id,nom,prenom',
        ]);

        $notes = trim((string) $request->validated('notes', ''));
        $summaryText = trim((string) $request->validated('summary_text', ''));
        $fieldValues = (array) $request->validated('field_values', []);

        if ($notes === '' && $summaryText === '' && collect($fieldValues)->filter(fn ($value) => trim((string) $value) !== '')->isEmpty()) {
            return response()->json([
                'message' => 'Ajoutez des notes ou chargez le contenu de la consultation avant de lancer l assistant IA.',
            ], 422);
        }

        try {
            $result = $assistantService->generate(
                $consultation,
                $request->user(),
                (string) $request->validated('action'),
                $notes,
                $fieldValues,
                $request->validated('preferred_target'),
                $summaryText
            );
        } catch (\DomainException|\InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'notes' => $exception->getMessage(),
            ]);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $this->userFacingRuntimeMessage($exception),
            ], 503);
        }

        $history = $result['history'];

        $action = (string) $request->validated('action');
        $label = $action === \App\Models\ConsultationAiGeneration::ACTION_MEDICAL_REPORT ? 'Compte rendu IA' : 'Resume';
        $message = $result['used_fallback']
            ? $label . ' genere en mode secours local. Le contenu reste a relire et valider par le medecin.'
            : 'Generation IA effectuee. Le contenu reste a valider par le medecin.';

        return response()->json([
            'message' => $message,
            'generation' => [
                'id' => $history->id,
                'action_type' => $history->action_type,
                'action_label' => $history->action_label,
                'generated_text' => $result['generated_text'],
                'suggested_target' => $result['suggested_target'],
                'provider' => $result['provider'],
                'used_fallback' => $result['used_fallback'],
                'fallback_reason' => $result['fallback_reason'],
                'created_at' => optional($history->created_at)->format('d/m/Y H:i'),
                'user_name' => $history->user?->name ?? 'Utilisateur',
                'preview' => \Illuminate\Support\Str::limit($result['generated_text'], 140),
            ],
        ]);
    }

    public function exportMedicalReportPdf(Request $request, Consultation $consultation, PdfBuilder $pdfBuilder): Response
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:30000'],
        ], [], [
            'content' => 'compte rendu IA',
        ]);

        $consultation->loadMissing([
            'patient:id,nom,prenom,date_naissance,telephone,email',
            'medecin:id,nom,prenom,specialite',
        ]);

        $pdf = $pdfBuilder->fromView('consultations.pdf.ai_medical_report', [
            'consultation' => $consultation,
            'content' => trim((string) $validated['content']),
        ])->setPaper('a4');

        return $pdf->download('compte-rendu-ia-consultation-' . $consultation->id . '.pdf');
    }

    private function userFacingRuntimeMessage(\RuntimeException $exception): string
    {
        $message = trim($exception->getMessage());
        $messageLower = mb_strtolower($message);

        if (str_contains($message, 'OPENAI_API_KEY')) {
            return 'La configuration OpenAI est incomplete. Ajoutez OPENAI_API_KEY avant d utiliser le resume IA.';
        }

        if (str_contains($messageLower, 'quota') || str_contains($messageLower, 'billing')) {
            return 'Le service IA est temporairement indisponible pour ce compte. Verifiez le quota API ou reessayez plus tard.';
        }

        if (str_contains($messageLower, 'ne repond pas')) {
            return 'Le service IA ne repond pas actuellement. Reessayez dans quelques instants.';
        }

        if (str_contains($messageLower, 'format invalide') || str_contains($messageLower, 'aucun contenu exploitable')) {
            return 'Le contenu genere par le service IA est inutilisable pour le moment. Reessayez ou utilisez le mode secours.';
        }

        return 'La generation IA est momentanement indisponible. Reessayez dans quelques instants.';
    }
}
