<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id', 'patient_id', 'medecin_id', 'date_prescription',
        'numero_prescription', 'type_prescription', 'medicaments', 'examens_demandes',
        'soins_prescrits', 'recommandations', 'est_renouvelable', 'nombre_renouvellements',
        'duree_validite_jours', 'statut', 'signature_medecin', 'date_signature',
        'pharmacie_nom', 'pharmacie_adresse', 'date_delivrance'
    ];

    protected $casts = [
        'date_prescription' => 'datetime',
        'date_signature' => 'datetime',
        'date_delivrance' => 'date',
        'medicaments' => 'array',
        'examens_demandes' => 'array',
        'est_renouvelable' => 'boolean'
    ];

    // RELATIONS
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    // ACCESSORS
    public function getEstValideAttribute()
    {
        return $this->date_prescription->addDays($this->duree_validite_jours ?? 30) >= now();
    }

    public function getMedicamentsListeAttribute()
    {
        if (empty($this->medicaments)) {
            return [];
        }

        return array_map(function($med) {
            return $med['nom'] . ' - ' . ($med['posologie'] ?? '');
        }, $this->medicaments);
    }
}
