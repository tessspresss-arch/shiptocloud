<?php

namespace App\Services\MedicalAi;

use App\Models\Consultation;
use App\Models\ConsultationAiGeneration;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ConsultationAiAssistantService
{
    private const LOCAL_PROVIDER = 'local_fallback';
    private const SECTION_FALLBACKS = [
        'motif' => 'Information insuffisante dans les notes pour preciser clairement le motif.',
        'resume_clinique' => 'Information insuffisante dans les notes pour etablir un resume clinique complet.',
        'observations' => 'Information insuffisante dans les notes pour documenter des observations complementaires.',
        'conclusion' => 'Conclusion prudente : informations insuffisantes pour formuler une conclusion clinique definitive.',
        'conduite_a_tenir' => 'Conduite a tenir a confirmer par le medecin selon l evaluation clinique complete.',
    ];

    public function generate(
        Consultation $consultation,
        User $user,
        string $action,
        ?string $notes,
        array $fieldValues = [],
        ?string $preferredTarget = null,
        ?string $summaryText = null
    ): array {
        if (!in_array($action, [ConsultationAiGeneration::ACTION_SUMMARY, ConsultationAiGeneration::ACTION_MEDICAL_REPORT], true)) {
            throw new \DomainException('Seules la generation du resume IA et du compte rendu IA sont disponibles dans cette version.');
        }

        $cleanFields = $this->sanitizeFieldValues($fieldValues);
        $sourceText = $this->composeSourceText($notes, $cleanFields);
        $summaryText = trim((string) $summaryText);

        if ($sourceText === '' && $summaryText === '') {
            throw new \InvalidArgumentException('Aucun contenu clinique exploitable n a ete fourni.');
        }

        $generation = match ($action) {
            ConsultationAiGeneration::ACTION_SUMMARY => $this->generateSummary($consultation, $sourceText, $cleanFields),
            ConsultationAiGeneration::ACTION_MEDICAL_REPORT => $this->generateMedicalReport($consultation, $sourceText, $cleanFields, $summaryText),
        };
        $generatedText = match ($action) {
            ConsultationAiGeneration::ACTION_SUMMARY => $this->formatSummaryDocument($generation['structured_payload']),
            ConsultationAiGeneration::ACTION_MEDICAL_REPORT => $this->formatMedicalReportDocument($generation['structured_payload']),
        };

        $history = ConsultationAiGeneration::create([
            'consultation_id' => $consultation->id,
            'user_id' => $user->id,
            'action_type' => $action,
            'source_text' => $sourceText,
            'generated_text' => $generatedText,
            'suggested_target' => $preferredTarget ?: $this->defaultTargetForAction($action),
            'context_payload' => [
                'engine' => config('medical_ai.engine', 'openai_responses'),
                'provider' => $generation['provider'],
                'model' => $generation['model'],
                'used_fallback' => $generation['used_fallback'],
                'fallback_reason' => $generation['fallback_reason'],
                'field_values' => $cleanFields,
                'summary_text' => $summaryText,
                'patient_id' => $consultation->patient_id,
                'medecin_id' => $consultation->medecin_id,
                'structured_payload' => $generation['structured_payload'],
            ],
        ]);

        $history->setRelation('user', $user);

        return [
            'history' => $history,
            'generated_text' => $generatedText,
            'suggested_target' => $history->suggested_target,
            'used_fallback' => $generation['used_fallback'],
            'provider' => $generation['provider'],
            'fallback_reason' => $generation['fallback_reason'],
        ];
    }

    private function generateSummary(Consultation $consultation, string $sourceText, array $fieldValues): array
    {
        try {
            $structured = $this->generateSummaryWithOpenAi($consultation, $sourceText, $fieldValues);

            return [
                'structured_payload' => $structured,
                'provider' => 'openai',
                'model' => (string) config('medical_ai.openai.model', 'gpt-5-mini'),
                'used_fallback' => false,
                'fallback_reason' => null,
            ];
        } catch (\RuntimeException $exception) {
            if (!config('medical_ai.fallback.enabled', true)) {
                throw $exception;
            }

            return [
                'structured_payload' => $this->buildLocalSummary($consultation, $fieldValues, $sourceText),
                'provider' => self::LOCAL_PROVIDER,
                'model' => 'local_clinical_assistant',
                'used_fallback' => true,
                'fallback_reason' => $exception->getMessage(),
            ];
        }
    }

    private function generateMedicalReport(Consultation $consultation, string $sourceText, array $fieldValues, string $summaryText): array
    {
        try {
            $structured = $this->generateMedicalReportWithOpenAi($consultation, $sourceText, $fieldValues, $summaryText);

            return [
                'structured_payload' => $structured,
                'provider' => 'openai',
                'model' => (string) config('medical_ai.openai.model', 'gpt-5-mini'),
                'used_fallback' => false,
                'fallback_reason' => null,
            ];
        } catch (\RuntimeException $exception) {
            if (!config('medical_ai.fallback.enabled', true)) {
                throw $exception;
            }

            return [
                'structured_payload' => $this->buildLocalMedicalReport($consultation, $fieldValues, $sourceText, $summaryText),
                'provider' => self::LOCAL_PROVIDER,
                'model' => 'local_clinical_assistant',
                'used_fallback' => true,
                'fallback_reason' => $exception->getMessage(),
            ];
        }
    }

    private function generateSummaryWithOpenAi(Consultation $consultation, string $sourceText, array $fieldValues): array
    {
        $apiKey = trim((string) config('medical_ai.openai.api_key'));

        if ($apiKey === '') {
            throw new \RuntimeException('La configuration OpenAI est incomplete. Ajoutez OPENAI_API_KEY avant d utiliser le resume IA.');
        }

        try {
            $response = $this->openAiClient($apiKey)->post('/responses', [
                'model' => (string) config('medical_ai.openai.model', 'gpt-5-mini'),
                'store' => false,
                'reasoning' => [
                    'effort' => 'low',
                ],
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => 'consultation_summary',
                        'strict' => true,
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'motif' => [
                                    'type' => 'string',
                                    'description' => 'Motif principal de consultation, formule en une phrase courte et prudente.',
                                ],
                                'resume_clinique' => [
                                    'type' => 'string',
                                    'description' => 'Synthese clinique concise, factuelle, sans information inventee.',
                                ],
                                'observations' => [
                                    'type' => 'string',
                                    'description' => 'Observations utiles ou element de surveillance, avec mention d information insuffisante si besoin.',
                                ],
                                'conclusion' => [
                                    'type' => 'string',
                                    'description' => 'Conclusion professionnelle et prudente, sans certitude excessive si les notes sont insuffisantes.',
                                ],
                                'conduite_a_tenir' => [
                                    'type' => 'string',
                                    'description' => 'Conduite a tenir ou suivi propose, ou mention explicite que l information doit etre completee.',
                                ],
                            ],
                            'required' => [
                                'motif',
                                'resume_clinique',
                                'observations',
                                'conclusion',
                                'conduite_a_tenir',
                            ],
                            'additionalProperties' => false,
                        ],
                    ],
                ],
                'input' => [
                    [
                        'role' => 'system',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $this->systemPrompt(),
                            ],
                        ],
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $this->userPrompt($consultation, $sourceText, $fieldValues),
                            ],
                        ],
                    ],
                ],
            ]);

            $payload = $response->throw()->json();
        } catch (ConnectionException $exception) {
            throw new \RuntimeException('Le service IA ne repond pas actuellement. Reessayez dans quelques instants.', previous: $exception);
        } catch (\Throwable $exception) {
            $message = data_get(($response ?? null)?->json(), 'error.message')
                ?: 'La generation du resume IA a echoue.';

            throw new \RuntimeException($message, previous: $exception);
        }

        $rawText = $this->extractOpenAiText($payload);

        if ($rawText === '') {
            throw new \RuntimeException('Le service IA n a retourne aucun contenu exploitable.');
        }

        try {
            $decoded = json_decode($rawText, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \RuntimeException('Le resume IA a ete recu dans un format invalide.', previous: $exception);
        }

        return $this->normalizeStructuredSummary($decoded, $sourceText, $fieldValues);
    }

    private function generateMedicalReportWithOpenAi(
        Consultation $consultation,
        string $sourceText,
        array $fieldValues,
        string $summaryText
    ): array {
        $apiKey = trim((string) config('medical_ai.openai.api_key'));

        if ($apiKey === '') {
            throw new \RuntimeException('La configuration OpenAI est incomplete. Ajoutez OPENAI_API_KEY avant d utiliser le compte rendu IA.');
        }

        try {
            $response = $this->openAiClient($apiKey)->post('/responses', [
                'model' => (string) config('medical_ai.openai.model', 'gpt-5-mini'),
                'store' => false,
                'reasoning' => [
                    'effort' => 'low',
                ],
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => 'consultation_medical_report',
                        'strict' => true,
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'informations_patient' => [
                                    'type' => 'string',
                                    'description' => 'Identite patient et contexte administratif utile, de facon sobre.',
                                ],
                                'motif_consultation' => [
                                    'type' => 'string',
                                    'description' => 'Motif de consultation en une phrase claire et prudente.',
                                ],
                                'anamnese_contexte' => [
                                    'type' => 'string',
                                    'description' => 'Contexte ou anamnese issue strictement des notes disponibles.',
                                ],
                                'examen_clinique' => [
                                    'type' => 'string',
                                    'description' => 'Elements d examen clinique ou mention d information insuffisante.',
                                ],
                                'diagnostic_hypothese' => [
                                    'type' => 'string',
                                    'description' => 'Diagnostic ou hypothese formulee avec prudence si necessaire.',
                                ],
                                'conduite_a_tenir' => [
                                    'type' => 'string',
                                    'description' => 'Conduite a tenir proposee, sans inventer d actes absents.',
                                ],
                                'recommandations' => [
                                    'type' => 'string',
                                    'description' => 'Conseils, surveillance ou recommandations de suivi.',
                                ],
                            ],
                            'required' => [
                                'informations_patient',
                                'motif_consultation',
                                'anamnese_contexte',
                                'examen_clinique',
                                'diagnostic_hypothese',
                                'conduite_a_tenir',
                                'recommandations',
                            ],
                            'additionalProperties' => false,
                        ],
                    ],
                ],
                'input' => [
                    [
                        'role' => 'system',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $this->medicalReportSystemPrompt(),
                            ],
                        ],
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $this->medicalReportUserPrompt($consultation, $sourceText, $fieldValues, $summaryText),
                            ],
                        ],
                    ],
                ],
            ]);

            $payload = $response->throw()->json();
        } catch (ConnectionException $exception) {
            throw new \RuntimeException('Le service IA ne repond pas actuellement. Reessayez dans quelques instants.', previous: $exception);
        } catch (\Throwable $exception) {
            $message = data_get(($response ?? null)?->json(), 'error.message')
                ?: 'La generation du compte rendu IA a echoue.';

            throw new \RuntimeException($message, previous: $exception);
        }

        $rawText = $this->extractOpenAiText($payload);

        if ($rawText === '') {
            throw new \RuntimeException('Le service IA n a retourne aucun contenu exploitable pour le compte rendu.');
        }

        try {
            $decoded = json_decode($rawText, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \RuntimeException('Le compte rendu IA a ete recu dans un format invalide.', previous: $exception);
        }

        return $this->normalizeMedicalReport($decoded, $consultation, $sourceText, $fieldValues, $summaryText);
    }

    private function buildLocalSummary(Consultation $consultation, array $fieldValues, string $sourceText): array
    {
        $motif = $this->bestAvailable($fieldValues, ['symptomes'], $this->extractSentence($sourceText, 'Motif de consultation a preciser.'));
        $resumeClinique = $this->bestAvailable(
            $fieldValues,
            ['diagnostic', 'examen_clinique', 'symptomes'],
            $this->extractSentence($sourceText, 'Resume clinique a completer.')
        );
        $observations = $this->bestAvailable(
            $fieldValues,
            ['examen_clinique'],
            $this->extractSentence($sourceText, 'Observations cliniques a completer.')
        );
        $conclusion = $this->bestAvailable(
            $fieldValues,
            ['diagnostic'],
            'Conclusion clinique preliminaire a valider par le medecin.'
        );
        $conduite = $this->bestAvailable(
            $fieldValues,
            ['traitement_prescrit', 'recommandations'],
            'Conduite a tenir et suivi a preciser lors de la validation medicale.'
        );

        return $this->normalizeStructuredSummary([
            'motif' => $motif,
            'resume_clinique' => $resumeClinique,
            'observations' => $observations,
            'conclusion' => $conclusion,
            'conduite_a_tenir' => $conduite,
        ], $sourceText, $fieldValues);
    }

    private function buildLocalMedicalReport(
        Consultation $consultation,
        array $fieldValues,
        string $sourceText,
        string $summaryText
    ): array {
        return $this->normalizeMedicalReport([
            'informations_patient' => sprintf(
                '%s consulte le %s avec %s.',
                $this->patientLabel($consultation),
                optional($consultation->date_consultation)->format('d/m/Y') ?: now()->format('d/m/Y'),
                $this->doctorLabel($consultation)
            ),
            'motif_consultation' => $this->bestAvailable(
                $fieldValues,
                ['symptomes'],
                $this->extractSentence($summaryText !== '' ? $summaryText : $sourceText, 'Information insuffisante pour preciser le motif de consultation.')
            ),
            'anamnese_contexte' => $summaryText !== ''
                ? $this->extractSentence($summaryText, 'Information insuffisante dans les notes pour preciser l anamnese.')
                : $this->extractSentence($sourceText, 'Information insuffisante dans les notes pour preciser l anamnese.'),
            'examen_clinique' => $this->bestAvailable(
                $fieldValues,
                ['examen_clinique'],
                'Information insuffisante dans les notes pour decrire l examen clinique.'
            ),
            'diagnostic_hypothese' => $this->bestAvailable(
                $fieldValues,
                ['diagnostic'],
                'Hypothese diagnostique a confirmer par le medecin selon l evaluation clinique complete.'
            ),
            'conduite_a_tenir' => $this->bestAvailable(
                $fieldValues,
                ['traitement_prescrit'],
                'Conduite a tenir a definir ou confirmer par le medecin.'
            ),
            'recommandations' => $this->bestAvailable(
                $fieldValues,
                ['recommandations'],
                'Recommandations de suivi a preciser selon l evolution clinique.'
            ),
        ], $consultation, $sourceText, $fieldValues, $summaryText);
    }

    private function openAiClient(string $apiKey)
    {
        $client = Http::baseUrl((string) config('medical_ai.openai.base_url', 'https://api.openai.com/v1'))
            ->acceptJson()
            ->withToken($apiKey)
            ->timeout((int) config('medical_ai.openai.timeout', 30));

        $organization = trim((string) config('medical_ai.openai.organization'));
        $project = trim((string) config('medical_ai.openai.project'));

        if ($organization !== '') {
            $client = $client->withHeaders(['OpenAI-Organization' => $organization]);
        }

        if ($project !== '') {
            $client = $client->withHeaders(['OpenAI-Project' => $project]);
        }

        return $client;
    }

    private function normalizeStructuredSummary(array $decoded, string $sourceText, array $fieldValues): array
    {
        $fields = self::SECTION_FALLBACKS;
        $isSparse = $this->isSparseInput($sourceText, $fieldValues);

        $normalized = [];

        foreach ($fields as $key => $fallback) {
            $value = trim((string) ($decoded[$key] ?? ''));
            $value = $this->stripSectionPrefix($key, $value);
            $value = $this->sanitizeGeneratedSection($value);

            if ($value === '' || $this->looksInsufficient($value)) {
                $normalized[$key] = $fallback;
                continue;
            }

            if ($isSparse && $this->shouldForcePrudence($key, $fieldValues)) {
                $normalized[$key] = $fallback;
                continue;
            }

            $normalized[$key] = $this->limitSectionLength($this->toSentence($value));
        }

        return $normalized;
    }

    private function normalizeMedicalReport(
        array $decoded,
        Consultation $consultation,
        string $sourceText,
        array $fieldValues,
        string $summaryText
    ): array {
        $fields = [
            'informations_patient' => sprintf(
                '%s consulte le %s avec %s.',
                $this->patientLabel($consultation),
                optional($consultation->date_consultation)->format('d/m/Y') ?: now()->format('d/m/Y'),
                $this->doctorLabel($consultation)
            ),
            'motif_consultation' => 'Information insuffisante dans les notes pour preciser le motif de consultation.',
            'anamnese_contexte' => 'Information insuffisante dans les notes pour preciser l anamnese ou le contexte.',
            'examen_clinique' => 'Information insuffisante dans les notes pour decrire l examen clinique.',
            'diagnostic_hypothese' => 'Diagnostic ou hypothese a confirmer par le medecin selon l evaluation clinique complete.',
            'conduite_a_tenir' => 'Conduite a tenir a definir ou confirmer par le medecin.',
            'recommandations' => 'Recommandations de suivi a preciser selon l evolution clinique.',
        ];

        $normalized = [];
        $isSparse = $this->isSparseInput($sourceText . "\n" . $summaryText, $fieldValues);

        foreach ($fields as $key => $fallback) {
            $value = trim((string) ($decoded[$key] ?? ''));
            $value = $this->stripMedicalReportPrefix($key, $value);
            $value = $this->sanitizeGeneratedSection($value);

            if ($value === '' || $this->looksInsufficient($value)) {
                $normalized[$key] = $fallback;
                continue;
            }

            if ($isSparse && $this->shouldForceMedicalReportPrudence($key, $fieldValues, $summaryText)) {
                $normalized[$key] = $fallback;
                continue;
            }

            $normalized[$key] = $this->limitSectionLength($this->toSentence($value));
        }

        return $normalized;
    }

    private function extractOpenAiText(array $payload): string
    {
        $outputText = trim((string) data_get($payload, 'output_text', ''));

        if ($outputText !== '') {
            return $outputText;
        }

        $contentItems = data_get($payload, 'output', []);

        if (!is_array($contentItems)) {
            return '';
        }

        foreach ($contentItems as $item) {
            foreach ((array) data_get($item, 'content', []) as $content) {
                $text = trim((string) data_get($content, 'text', ''));
                if ($text !== '') {
                    return $text;
                }
            }
        }

        return '';
    }

    private function extractSentence(string $text, string $fallback): string
    {
        $segments = preg_split('/(?:\n{2,}|\n|(?<=[\.\!\?])\s+)/u', trim($text)) ?: [];

        foreach ($segments as $segment) {
            $segment = trim((string) $segment, " \t\n\r\0\x0B-•");
            if ($segment !== '') {
                return $segment;
            }
        }

        return $fallback;
    }

    private function sanitizeGeneratedSection(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        if ($value === '') {
            return '';
        }

        return trim($value, " \t\n\r\0\x0B-•:");
    }

    private function stripSectionPrefix(string $key, string $value): string
    {
        $labels = [
            'motif' => ['motif'],
            'resume_clinique' => ['resume clinique', 'résumé clinique'],
            'observations' => ['observations', 'observation'],
            'conclusion' => ['conclusion'],
            'conduite_a_tenir' => ['conduite a tenir', 'conduite à tenir'],
        ];

        foreach ($labels[$key] ?? [] as $label) {
            $pattern = '/^' . preg_quote($label, '/') . '\s*:\s*/iu';
            $value = preg_replace($pattern, '', $value) ?? $value;
        }

        return trim($value);
    }

    private function stripMedicalReportPrefix(string $key, string $value): string
    {
        $labels = [
            'informations_patient' => ['informations patient', 'information patient'],
            'motif_consultation' => ['motif de consultation', 'motif consultation'],
            'anamnese_contexte' => ['anamnese / contexte', 'anamnese', 'contexte'],
            'examen_clinique' => ['examen clinique'],
            'diagnostic_hypothese' => ['diagnostic ou hypothese', 'diagnostic', 'hypothese'],
            'conduite_a_tenir' => ['conduite a tenir', 'conduite à tenir'],
            'recommandations' => ['recommandations', 'recommandation'],
        ];

        foreach ($labels[$key] ?? [] as $label) {
            $pattern = '/^' . preg_quote($label, '/') . '\s*:\s*/iu';
            $value = preg_replace($pattern, '', $value) ?? $value;
        }

        return trim($value);
    }

    private function limitSectionLength(string $value): string
    {
        return Str::limit($value, 260, '...');
    }

    private function looksInsufficient(string $value): bool
    {
        $normalized = Str::lower($value);
        $needles = [
            'non renseign',
            'non precise',
            'non précisé',
            'n/a',
            'aucune information',
            'information insuffisante',
            'insuffisant',
            'inconnu',
            'a completer',
            'à compléter',
        ];

        foreach ($needles as $needle) {
            if (str_contains($normalized, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function isSparseInput(string $sourceText, array $fieldValues): bool
    {
        $filledFields = count(array_filter($fieldValues, fn (string $value) => trim($value) !== ''));
        $sentenceCount = count(preg_split('/(?:\n{2,}|\n|(?<=[\.\!\?])\s+)/u', trim($sourceText)) ?: []);
        $length = mb_strlen(trim($sourceText));

        return $filledFields <= 1 || $sentenceCount <= 2 || $length < 120;
    }

    private function shouldForcePrudence(string $key, array $fieldValues): bool
    {
        return match ($key) {
            'observations' => trim((string) ($fieldValues['examen_clinique'] ?? '')) === '',
            'conclusion' => trim((string) ($fieldValues['diagnostic'] ?? '')) === '',
            'conduite_a_tenir' => trim((string) ($fieldValues['traitement_prescrit'] ?? '')) === ''
                && trim((string) ($fieldValues['recommandations'] ?? '')) === '',
            default => false,
        };
    }

    private function shouldForceMedicalReportPrudence(string $key, array $fieldValues, string $summaryText): bool
    {
        return match ($key) {
            'anamnese_contexte' => trim($summaryText) === '',
            'examen_clinique' => trim((string) ($fieldValues['examen_clinique'] ?? '')) === '',
            'diagnostic_hypothese' => trim((string) ($fieldValues['diagnostic'] ?? '')) === '',
            'conduite_a_tenir' => trim((string) ($fieldValues['traitement_prescrit'] ?? '')) === '',
            'recommandations' => trim((string) ($fieldValues['recommandations'] ?? '')) === '',
            default => false,
        };
    }

    private function formatSummaryDocument(array $summary): string
    {
        return trim(implode("\n\n", [
            'Motif',
            $summary['motif'],
            'Resume clinique',
            $summary['resume_clinique'],
            'Observations',
            $summary['observations'],
            'Conclusion',
            $summary['conclusion'],
            'Conduite a tenir',
            $summary['conduite_a_tenir'],
        ]));
    }

    private function formatMedicalReportDocument(array $report): string
    {
        return trim(implode("\n\n", [
            'Informations patient',
            $report['informations_patient'],
            'Motif de consultation',
            $report['motif_consultation'],
            'Anamnese / contexte',
            $report['anamnese_contexte'],
            'Examen clinique',
            $report['examen_clinique'],
            'Diagnostic ou hypothese',
            $report['diagnostic_hypothese'],
            'Conduite a tenir',
            $report['conduite_a_tenir'],
            'Recommandations',
            $report['recommandations'],
        ]));
    }

    private function systemPrompt(): string
    {
        return implode(' ', [
            'Tu es un assistant de redaction clinique pour un cabinet medical.',
            'Tu rediges exclusivement en francais professionnel, clair, concis et factuel.',
            'Tu ne crees jamais d informations absentes des notes source.',
            'Si une information est incertaine, formule-la avec prudence sans inventer.',
            'Si les notes sont insuffisantes, indique explicitement que l information est insuffisante au lieu de supposer.',
            'Chaque section doit rester breve, exploitable, et ne jamais remplacer la validation du medecin.',
            'Retourne uniquement un JSON conforme au schema fourni.',
        ]);
    }

    private function medicalReportSystemPrompt(): string
    {
        return implode(' ', [
            'Tu es un assistant de redaction de compte rendu medical pour un cabinet medical.',
            'Tu rediges exclusivement en francais professionnel, structure, clair et factuel.',
            'Tu ne crees jamais d informations absentes des notes source ni du resume fourni.',
            'Si une information manque, tu l indiques explicitement avec prudence.',
            'Le compte rendu ne remplace jamais la validation du medecin.',
            'Retourne uniquement un JSON conforme au schema fourni.',
        ]);
    }

    private function userPrompt(Consultation $consultation, string $sourceText, array $fieldValues): string
    {
        $context = [
            'Patient: ' . $this->patientLabel($consultation),
            'Medecin: ' . $this->doctorLabel($consultation),
            'Date: ' . (optional($consultation->date_consultation)->format('d/m/Y') ?: now()->format('d/m/Y')),
        ];

        if ($fieldValues !== []) {
            $context[] = 'Champs de consultation disponibles:';

            foreach ($fieldValues as $field => $value) {
                $context[] = '- ' . $this->labelForField($field) . ': ' . $value;
            }
        }

        return implode("\n", [
            'Genere un resume de consultation structure selon les sections demandees.',
            'Le ton doit etre medical, sobre, professionnel, clair et concis.',
            'Ne jamais inventer de symptomes, diagnostics, examens ou conduites a tenir absents des notes.',
            'Si une section n est pas suffisamment documentee, ecris une formulation prudente signalant une information insuffisante.',
            'Chaque valeur doit contenir une a deux phrases maximum, sans titre ni puce.',
            implode("\n", $context),
            '',
            'Notes source:',
            $sourceText,
        ]);
    }

    private function medicalReportUserPrompt(
        Consultation $consultation,
        string $sourceText,
        array $fieldValues,
        string $summaryText
    ): string {
        $context = [
            'Patient: ' . $this->patientLabel($consultation),
            'Medecin: ' . $this->doctorLabel($consultation),
            'Date: ' . (optional($consultation->date_consultation)->format('d/m/Y') ?: now()->format('d/m/Y')),
        ];

        if ($fieldValues !== []) {
            $context[] = 'Champs de consultation disponibles:';

            foreach ($fieldValues as $field => $value) {
                $context[] = '- ' . $this->labelForField($field) . ': ' . $value;
            }
        }

        return implode("\n", [
            'Genere un compte rendu medical structure selon les sections demandees.',
            'Le ton doit etre medical, professionnel, clair et concis.',
            'Chaque valeur doit contenir une a trois phrases maximum, sans titre ni puce.',
            'Ne jamais inventer de donnees. Si une section est insuffisamment documentee, indiquer explicitement que l information est insuffisante.',
            implode("\n", $context),
            '',
            'Resume IA disponible:',
            $summaryText !== '' ? $summaryText : 'Aucun resume IA fourni.',
            '',
            'Notes source:',
            $sourceText !== '' ? $sourceText : 'Aucune note libre supplementaire.',
        ]);
    }

    private function sanitizeFieldValues(array $fieldValues): array
    {
        $allowed = ['symptomes', 'examen_clinique', 'diagnostic', 'traitement_prescrit', 'recommandations'];
        $result = [];

        foreach ($allowed as $field) {
            $value = trim((string) Arr::get($fieldValues, $field, ''));
            if ($value !== '') {
                $result[$field] = preg_replace("/\r\n|\r/u", "\n", $value);
            }
        }

        return $result;
    }

    private function composeSourceText(?string $notes, array $fieldValues): string
    {
        $notes = trim((string) $notes);
        if ($notes !== '') {
            return preg_replace("/\r\n|\r/u", "\n", $notes);
        }

        $parts = [];
        foreach ($fieldValues as $field => $value) {
            $parts[] = $this->labelForField($field) . ":\n" . $value;
        }

        return trim(implode("\n\n", $parts));
    }

    private function labelForField(string $field): string
    {
        return match ($field) {
            'symptomes' => 'Symptomes',
            'examen_clinique' => 'Examen clinique',
            'diagnostic' => 'Diagnostic',
            'traitement_prescrit' => 'Traitement prescrit',
            'recommandations' => 'Recommandations',
            default => ucfirst(str_replace('_', ' ', $field)),
        };
    }

    private function bestAvailable(array $fieldValues, array $priorityKeys, string $fallback): string
    {
        foreach ($priorityKeys as $key) {
            $value = trim((string) ($fieldValues[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    private function defaultTargetForAction(string $action): string
    {
        return match ($action) {
            ConsultationAiGeneration::ACTION_SUMMARY => 'recommandations',
            ConsultationAiGeneration::ACTION_MEDICAL_REPORT => 'diagnostic',
            default => 'recommandations',
        };
    }

    private function patientLabel(Consultation $consultation): string
    {
        $patient = trim(($consultation->patient->prenom ?? '') . ' ' . ($consultation->patient->nom ?? ''));
        return $patient !== '' ? $patient : 'Patient #' . $consultation->patient_id;
    }

    private function doctorLabel(Consultation $consultation): string
    {
        $doctor = trim(($consultation->medecin->prenom ?? '') . ' ' . ($consultation->medecin->nom ?? ''));
        return $doctor !== '' ? 'Dr. ' . $doctor : 'Medecin #' . $consultation->medecin_id;
    }

    private function toSentence(string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
        if ($text === '') {
            return '';
        }

        $text = Str::ucfirst($text);

        return preg_match('/[.!?]$/u', $text) ? $text : $text . '.';
    }
}
