<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'categorie_id',
        'description',
        'details',
        'montant',
        'date_depense',
        'categorie',
        'beneficiaire',
        'statut',
        'facture_numero',
        'mode_paiement',
        'date_paiement',
        'user_id',
        'methode_paiement',
        'reference_paiement',
        'notes',
        'piece_jointe',
        'is_documentee',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'categorie_id' => 'integer',
        'date_depense' => 'datetime',
        'date_paiement' => 'datetime',
        'montant' => 'decimal:2',
        'is_documentee' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur qui a créé la dépense
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes pour les statistiques
     */
    public function scopeTotalByMonth($query, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        $start = now()->setYear($year)->setMonth($month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return $query->whereBetween('date_depense', [$start, $end])->sum('montant');
    }

    public function scopeTotalByYear($query, $year = null)
    {
        $year = $year ?? now()->year;
        $start = now()->setYear($year)->startOfYear();
        $end = $start->copy()->endOfYear();

        return $query->whereBetween('date_depense', [$start, $end])->sum('montant');
    }

    public function scopeTotalByCategory($query, $category)
    {
        return $query->where('categorie', $category)->sum('montant');
    }
}
