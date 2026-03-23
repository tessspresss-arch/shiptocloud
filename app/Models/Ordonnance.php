<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ordonnance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ordonnances';

    protected $fillable = [
        'numero_ordonnance',
        'patient_id',
        'medecin_id',
        'consultation_id',
        'date_prescription',
        'date_expiration',
        'diagnostic',
        'observations',
        'instructions',
        'medicaments',
        'statut',
        'imprimee',
    ];

    protected $casts = [
        'medicaments' => 'array',
        'date_prescription' => 'date',
        'date_expiration' => 'date',
        'imprimee' => 'boolean',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class, 'medecin_id');
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function ligneOrdonnances()
    {
        return $this->hasMany(LigneOrdonnance::class);
    }
}
