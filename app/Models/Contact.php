<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'type',
        'email',
        'telephone',
        'telephone_secondaire',
        'adresse',
        'ville',
        'codepostal',
        'entreprise',
        'fonction',
        'notes',
        'is_actif',
        'is_favorite'
    ];

    protected $casts = [
        'is_actif' => 'boolean',
        'is_favorite' => 'boolean',
    ];

    // SCOPES
    public function scopeActifs($query)
    {
        return $query->where('is_actif', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('nom', 'like', "%{$search}%")
                     ->orWhere('prenom', 'like', "%{$search}%")
                     ->orWhere('entreprise', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%")
                     ->orWhere('telephone', 'like', "%{$search}%");
    }

    // ACCESSORS
    public function getNomCompletAttribute()
    {
        $nom = trim($this->nom);
        if ($this->prenom) {
            $nom .= ' ' . trim($this->prenom);
        }
        return $nom;
    }

    public function getTypeFormatteAttribute()
    {
        $types = [
            'patient' => 'Patient',
            'laboratoire' => 'Laboratoire',
            'fournisseur' => 'Fournisseur',
            'hopital' => 'Hôpital',
            'assurance' => 'Assurance',
            'autre' => 'Autre'
        ];
        return $types[$this->type] ?? $this->type;
    }
}
