<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierMedical extends Model
{
    use HasFactory;
    
    // Nom personnalisé de la table (optionnel, Laravel utilise le pluriel par défaut)
    protected $table = 'dossiers_medicaux';
    
    protected $fillable = [
        'patient_id',
        'numero_dossier',
        'type',
        'date_ouverture',
        'observations',
        'diagnostic',
        'traitement',
        'prescriptions',
        'statut',
        'documents'
    ];
    
    protected $casts = [
        'date_ouverture' => 'date',
        'documents' => 'array'
    ];
    
    // Relation avec le patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
    // Relation avec les consultations
    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id', 'patient_id');
    }

    // Relation avec les ordonnances via le patient
    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class, 'patient_id', 'patient_id');
    }
}
