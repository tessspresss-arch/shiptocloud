<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultatExamen extends Model
{
    use HasFactory;

    protected $table = 'resultats_examens';

    protected $fillable = [
        'examen_id',
        'parametre',
        'valeur',
        'unite',
        'valeur_normale',
        'interpretation',
        'notes'
    ];

    // RELATIONS
    public function examen()
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    // ACCESSORS
    public function getIsAnormalAttribute()
    {
        return $this->interpretation !== 'normal';
    }

    public function getIsCritiqueAttribute()
    {
        return $this->interpretation === 'critique';
    }
}
