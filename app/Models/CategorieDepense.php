<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategorieDepense extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories_depenses';

    protected $fillable = [
        'nom',
        'description',
        'icone',
        'couleur',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // RELATIONS
    public function depenses()
    {
        return $this->hasMany(Depense::class, 'categorie_id');
    }

    // SCOPES
    public function scopeActives($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('nom', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%");
    }

    // ACCESSORS
    public function getTotalDepensesAttribute()
    {
        return $this->depenses()->sum('montant');
    }
}
