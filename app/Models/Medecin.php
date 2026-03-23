<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medecin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'matricule',
        'civilite',
        'nom',
        'prenom',
        'specialite',
        'telephone',
        'email',
        'adresse_cabinet',
        'ville',
        'code_postal',
        'numero_ordre',
        'signature_path',
        'photo_path',
        'horaires_travail',
        'jours_conges',
        'tarif_consultation',
        'notes',
        'statut',
        'date_embauche',
        'date_depart',
    ];

    protected $casts = [
        'horaires_travail' => 'array',
        'jours_conges' => 'array',
        'tarif_consultation' => 'decimal:2',
        'date_embauche' => 'date',
        'date_depart' => 'date',
    ];

    public function getNomCompletAttribute()
    {
        return "{$this->civilite} {$this->prenom} {$this->nom}";
    }

    public function getSpecialiteFormateeAttribute()
    {
        return $this->specialite ?: 'Medecin Generaliste';
    }

    public function getAvatarUrlAttribute()
    {
        return $this->photo_path
            ? asset('storage/' . $this->photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->nom . ' ' . $this->prenom) . '&color=7F9CF5&background=EBF4FF';
    }

    public static function generateMatricule()
    {
        $year = date('Y');
        $lastMedecin = self::latest()->first();
        $number = $lastMedecin ? intval(substr($lastMedecin->matricule, -4)) + 1 : 1;

        return "MED-{$year}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function rendezvous()
    {
        return $this->hasMany(RendezVous::class);
    }

    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeSpecialite($query, $specialite)
    {
        return $query->where('specialite', 'LIKE', "%{$specialite}%");
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('nom', 'LIKE', "%{$search}%")
                     ->orWhere('prenom', 'LIKE', "%{$search}%")
                     ->orWhere('matricule', 'LIKE', "%{$search}%")
                     ->orWhere('specialite', 'LIKE', "%{$search}%");
    }

    public function estDisponible($date, $heure)
    {
        if ($this->jours_conges && in_array($date, $this->jours_conges)) {
            return false;
        }

        $jourSemaine = strtolower(date('l', strtotime($date)));
        $horaires = $this->horaires_travail[$jourSemaine] ?? null;

        if (!$horaires) {
            return false;
        }

        $heureDebut = strtotime($horaires['debut'] ?? '09:00');
        $heureFin = strtotime($horaires['fin'] ?? '17:00');
        $heureDemandee = strtotime($heure);

        return $heureDemandee >= $heureDebut && $heureDemandee <= $heureFin;
    }

    public function getStatistiquesAttribute()
    {
        return [
            'ordonnances' => $this->ordonnances()->count(),
            'consultations' => $this->consultations()->count(),
            'rendezvous' => $this->rendezvous()->count(),
            'patients_uniques' => $this->consultations()->distinct('patient_id')->count('patient_id'),
        ];
    }
}
