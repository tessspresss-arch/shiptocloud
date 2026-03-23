<?php

namespace App\Http\Requests;

use App\Models\ConsultationAiGeneration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateConsultationAiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                Rule::in([
                    ConsultationAiGeneration::ACTION_SUMMARY,
                    ConsultationAiGeneration::ACTION_MEDICAL_REPORT,
                ]),
            ],
            'notes' => ['nullable', 'string', 'max:40000'],
            'summary_text' => ['nullable', 'string', 'max:30000'],
            'preferred_target' => ['nullable', 'string', Rule::in([
                'symptomes',
                'examen_clinique',
                'diagnostic',
                'traitement_prescrit',
                'recommandations',
            ])],
            'field_values' => ['nullable', 'array'],
            'field_values.symptomes' => ['nullable', 'string', 'max:30000'],
            'field_values.examen_clinique' => ['nullable', 'string', 'max:30000'],
            'field_values.diagnostic' => ['nullable', 'string', 'max:30000'],
            'field_values.traitement_prescrit' => ['nullable', 'string', 'max:30000'],
            'field_values.recommandations' => ['nullable', 'string', 'max:30000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'action' => 'type de generation',
            'notes' => 'notes cliniques',
            'summary_text' => 'resume IA',
            'preferred_target' => 'champ cible',
            'field_values' => 'contenu de consultation',
            'field_values.symptomes' => 'symptomes',
            'field_values.examen_clinique' => 'examen clinique',
            'field_values.diagnostic' => 'diagnostic',
            'field_values.traitement_prescrit' => 'traitement prescrit',
            'field_values.recommandations' => 'recommandations',
        ];
    }
}
