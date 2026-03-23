<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificatMedical extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certificats_medicaux';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'consultation_id',
        'type',
        'date_emission',
        'date_debut',
        'date_fin',
        'nombre_jours',
        'motif',
        'observations',
        'recommendations',
        'est_transmis',
        'date_transmission',
        'fichier_pdf'
    ];

    protected $casts = [
        'date_emission' => 'datetime',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'date_transmission' => 'datetime',
        'est_transmis' => 'boolean',
    ];

    // RELATIONS
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

    // SCOPES
    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeTransmis($query)
    {
        return $query->where('est_transmis', true);
    }

    public function scopeNonTransmis($query)
    {
        return $query->where('est_transmis', false);
    }

    public function scopeRecents($query, $jours = 30)
    {
        return $query->whereBetween('date_emission', [
            now()->subDays($jours),
            now()
        ]);
    }

    // ACCESSORS
    public function getEstActifAttribute()
    {
        return $this->date_fin >= now();
    }

    public function getDureeFormatteeAttribute()
    {
        if ($this->nombre_jours) {
            return $this->nombre_jours . ' jour(s)';
        }
        return $this->date_debut->diffInDays($this->date_fin) . ' jour(s)';
    }

    public function getDateEmissionFormatteeAttribute()
    {
        return $this->date_emission->format('d/m/Y');
    }
}
