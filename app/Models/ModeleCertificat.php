<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModeleCertificat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modele_certificats';

    protected $fillable = [
        'nom',
        'contenu_html',
        'type',
        'medecin_id',
        'est_template_general',
        'is_actif'
    ];

    protected $casts = [
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

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
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
        
        return $query->where('nom', 'like', "%{$search}%")
                     ->orWhere('type', 'like', "%{$search}%");
    }
}
