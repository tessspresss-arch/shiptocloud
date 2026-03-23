<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'rendez_vous_id', 'patient_id', 'medecin_id', 'date_consultation',
        'symptomes', 'diagnostic', 'poids', 'taille',
        'tension_arterielle_systolique', 'tension_arterielle_diastolique',
        'temperature', 'examen_clinique', 'traitement_prescrit',
        'recommandations', 'date_prochaine_visite'
    ];

    protected $casts = [
        'date_consultation' => 'date',
        'date_prochaine_visite' => 'date',
        'poids' => 'decimal:2',
        'taille' => 'decimal:2',
        'temperature' => 'decimal:1'
    ];

    // RELATIONS
    public function rendezvous()
    {
        return $this->belongsTo(RendezVous::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function aiGenerations(): HasMany
    {
        return $this->hasMany(ConsultationAiGeneration::class);
    }

    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'patient_id', 'patient_id');
    }

    // ACCESSORS
    public function getImcAttribute()
    {
        if ($this->poids && $this->taille && $this->taille > 0) {
            return round($this->poids / ($this->taille * $this->taille), 1);
        }
        return null;
    }

    public function getTensionAttribute()
    {
        if ($this->tension_arterielle_systolique && $this->tension_arterielle_diastolique) {
            return $this->tension_arterielle_systolique . '/' . $this->tension_arterielle_diastolique;
        }
        return null;
    }
}
