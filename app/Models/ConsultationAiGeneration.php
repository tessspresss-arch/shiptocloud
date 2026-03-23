<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationAiGeneration extends Model
{
    use HasFactory;

    public const ACTION_SUMMARY = 'summary';
    public const ACTION_MEDICAL_REPORT = 'medical_report';
    public const ACTION_REWRITE = 'rewrite';

    protected $fillable = [
        'consultation_id',
        'user_id',
        'action_type',
        'source_text',
        'generated_text',
        'suggested_target',
        'context_payload',
    ];

    protected $casts = [
        'context_payload' => 'array',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action_type) {
            self::ACTION_SUMMARY => 'Resume automatique',
            self::ACTION_MEDICAL_REPORT => 'Compte rendu medical',
            self::ACTION_REWRITE => 'Amelioration de redaction',
            default => ucfirst(str_replace('_', ' ', (string) $this->action_type)),
        };
    }
}
