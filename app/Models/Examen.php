<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examen extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'consultation_id',
        'nom_examen',
        'description',
        'type',
        'statut',
        'date_demande',
        'date_realisation',
        'lieu_realisation',
        'observations',
        'document_resultat',
        'resultats',
        'recommandations',
        'localisation',
        'appareil',
        'cout',
        'payee',
        'fichier_examen',
        'created_by',
    ];

    protected $casts = [
        'date_demande' => 'datetime',
        'date_realisation' => 'datetime',
        'payee' => 'boolean',
        'cout' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function resultatsExamens()
    {
        return $this->hasMany(ResultatExamen::class, 'examen_id');
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function getTypeExamenAttribute(): ?string
    {
        return $this->attributes['nom_examen']
            ?? $this->attributes['type']
            ?? null;
    }

    public function getDateExamenAttribute()
    {
        return $this->attributes['date_demande'] ?? null;
    }

    public function getLocalisationAttribute(): ?string
    {
        return $this->attributes['lieu_realisation']
            ?? $this->attributes['localisation']
            ?? null;
    }
}
