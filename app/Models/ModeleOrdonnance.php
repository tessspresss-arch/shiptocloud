<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModeleOrdonnance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modele_ordonnances';

    protected $fillable = [
        'nom',
        'categorie',
        'diagnostic_contexte',
        'instructions_generales',
        'medicaments_template',
        'contenu_html',
        'medecin_id',
        'est_template_general',
        'is_actif',
    ];

    protected $casts = [
        'medicaments_template' => 'array',
        'est_template_general' => 'boolean',
        'is_actif' => 'boolean',
    ];

    // RELATIONS
    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    // SCOPES
    public function scopeActifs($query)
    {
        return $query->where('is_actif', true);
    }

    public function scopeGeneraux($query)
    {
        return $query->where('est_template_general', true);
    }

    public function scopePersonnels($query, $medecinId)
    {
        return $query->where('medecin_id', $medecinId)
                     ->where('est_template_general', false);
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('nom', 'like', "%{$search}%");
    }
}
