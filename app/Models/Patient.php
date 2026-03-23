<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_dossier', 'nom', 'prenom', 'date_naissance', 'genre',
        'telephone', 'email', 'cin', 'adresse', 'ville', 'code_postal',
        'groupe_sanguin', 'assurance', 'antecedents', 'notes', 'photo', 'is_draft',
        'etat_civil', 'contact_urgence', 'telephone_urgence', 'allergies', 'traitements'
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    // RELATIONS
    public function rendezvous()
    {
        return $this->hasMany(RendezVous::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function dossiers()
    {
        return $this->hasMany(DossierMedical::class);
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }

    public function archive()
    {
        return $this->hasOne(PatientArchive::class);
    }

    // MÉTHODE UTILE
    public function getAgeAttribute()
    {
        return $this->date_naissance->age;
    }

    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getInitialesAttribute()
    {
        $prenomInitial = Str::upper(Str::substr((string) $this->prenom, 0, 1));
        $nomInitial = Str::upper(Str::substr((string) $this->nom, 0, 1));

        return trim($prenomInitial . $nomInitial) ?: 'PT';
    }

    public function getAvatarUrlAttribute()
    {
        return $this->photo
            ? asset('storage/' . ltrim($this->photo, '/'))
            : 'https://ui-avatars.com/api/?name=' . urlencode(trim($this->prenom . ' ' . $this->nom)) . '&color=2563EB&background=EAF2FF';
    }

    public function getTelephoneFormattedAttribute()
    {
        // Format Moroccan phone number: +212 6XX-XXXXXX
        if (preg_match('/^\+212\s6(\d{2})-(\d{6})$/', $this->telephone, $matches)) {
            return '+212 6' . $matches[1] . '-' . $matches[2];
        }
        return $this->telephone;
    }
}
