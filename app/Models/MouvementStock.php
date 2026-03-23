<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvement_stocks';

    protected $fillable = [
        'medicament_id',
        'type_mouvement',
        'quantite',
        'stock_avant',
        'stock_apres',
        'prix_unitaire',
        'valeur_totale',
        'motif',
        'reference',
        'date_mouvement',
        'heure_mouvement',
        'user_id',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'stock_avant' => 'integer',
        'stock_apres' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'valeur_totale' => 'decimal:2',
        'date_mouvement' => 'date',
        'heure_mouvement' => 'datetime:H:i:s',
    ];

    // Relationships
    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeEntrees($query)
    {
        return $query->where('type_mouvement', 'entree');
    }

    public function scopeSorties($query)
    {
        return $query->where('type_mouvement', 'sortie');
    }

    public function scopeAjustements($query)
    {
        return $query->where('type_mouvement', 'ajustement');
    }

    public function scopeByMedicament($query, $medicamentId)
    {
        return $query->where('medicament_id', $medicamentId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_mouvement', [$startDate, $endDate]);
    }

    public function getDateMouvementAttribute($value)
    {
        if ($value) {
            return $this->asDateTime($value);
        }

        return $this->created_at;
    }

    public function getQuantiteAvantAttribute(): ?int
    {
        return $this->stock_avant;
    }

    public function getQuantiteApresAttribute(): ?int
    {
        return $this->stock_apres;
    }

    public function getTypeMouvementLabelAttribute(): string
    {
        return match ($this->type_mouvement) {
            'entree' => 'Entrée',
            'sortie' => 'Sortie',
            'ajustement' => 'Ajustement',
            'retour' => 'Retour',
            default => ucfirst((string) $this->type_mouvement),
        };
    }

    public function getQuantiteFormateeAttribute(): string
    {
        return sprintf('%+d', (int) $this->quantite);
    }
}
