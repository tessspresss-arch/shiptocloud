<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'periode',
        'date_debut',
        'date_fin',
        'format',
        'file_path',
        'generated_by',
        'parameters',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'parameters' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'monthly' => 'Rapport Mensuel',
            'financial' => 'Rapport Financier',
            'patient' => 'Rapport Patients',
            'medicament' => 'Rapport Médicaments',
            default => ucfirst($this->type)
        };
    }

    public function getFormatLabelAttribute()
    {
        return match($this->format) {
            'pdf' => 'PDF',
            'excel' => 'Excel',
            'csv' => 'CSV',
            default => strtoupper($this->format)
        };
    }
}
