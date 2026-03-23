<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Medicament extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom_commercial',
        'dci',
        'code_cip',
        'code_medicament',
        'categorie',
        'classe_therapeutique',
        'laboratoire',
        'type',
        'quantite_stock',
        'quantite_seuil',
        'quantite_ideale',
        'prix_achat',
        'prix_vente',
        'prix_remboursement',
        'taux_remboursement',
        'date_peremption',
        'date_fabrication',
        'numero_lot',
        'fournisseur',
        'presentation',
        'voie_administration',
        'posologie',
        'contre_indications',
        'effets_secondaires',
        'interactions',
        'precautions',
        'conservation',
        'statut',
        'generique',
        'remboursable',
        'composants',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_peremption' => 'date',
        'date_fabrication' => 'date',
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'prix_remboursement' => 'decimal:2',
        'generique' => 'boolean',
        'remboursable' => 'boolean',
        'composants' => 'array',
    ];

    // Relationships
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function mouvementStocks(): HasMany
    {
        return $this->hasMany(MouvementStock::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantite_stock <= quantite_seuil');
    }

    public function scopeExpired($query)
    {
        return $query->where('date_peremption', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('date_peremption', '<=', now()->addDays($days))
                    ->where('date_peremption', '>', now());
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('categorie', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByLaboratory($query, $laboratory)
    {
        return $query->where('laboratoire', $laboratory);
    }

    public function scopeGenerics($query)
    {
        return $query->where('generique', true);
    }

    public function scopeReimbursable($query)
    {
        return $query->where('remboursable', true);
    }

    // Accessors
    public function getStockStatusAttribute()
    {
        if ($this->quantite_stock <= 0) {
            return 'rupture';
        } elseif ($this->quantite_stock <= $this->quantite_seuil) {
            return 'faible';
        } elseif ($this->quantite_stock >= $this->quantite_ideale) {
            return 'optimal';
        } else {
            return 'normal';
        }
    }

    public function getExpirationStatusAttribute()
    {
        if (!$this->date_peremption) {
            return 'non_defini';
        }

        $daysUntilExpiration = now()->diffInDays($this->date_peremption, false);

        if ($daysUntilExpiration < 0) {
            return 'expire';
        } elseif ($daysUntilExpiration <= 30) {
            return 'bientot_expire';
        } else {
            return 'valide';
        }
    }

    public function getPrixRemboursementCalculeAttribute()
    {
        if (!$this->remboursable || !$this->prix_vente || !$this->taux_remboursement) {
            return 0;
        }

        return round($this->prix_vente * ($this->taux_remboursement / 100), 2);
    }

    public function getValeurStockAttribute()
    {
        return round($this->quantite_stock * $this->prix_achat, 2);
    }

    public function getJoursRestantsAttribute()
    {
        if (!$this->date_peremption) {
            return null;
        }

        return now()->diffInDays($this->date_peremption, false);
    }

    // Methods
    public function addStock(int $quantity, string $motif = null, string $reference = null, User $user = null): bool
    {
        $oldStock = $this->quantite_stock;
        $this->quantite_stock += $quantity;
        $this->updated_by = $user ? $user->id : auth()->id();
        $this->save();

        // Créer le mouvement de stock
        $this->mouvementStocks()->create([
            'type_mouvement' => 'entree',
            'quantite' => $quantity,
            'quantite_avant' => $oldStock,
            'quantite_apres' => $this->quantite_stock,
            'prix_unitaire' => $this->prix_achat,
            'valeur_totale' => $quantity * $this->prix_achat,
            'motif' => $motif,
            'reference' => $reference,
            'date_mouvement' => now()->toDateString(),
            'heure_mouvement' => now()->toTimeString(),
            'user_id' => $user ? $user->id : auth()->id(),
        ]);

        return true;
    }

    public function removeStock(int $quantity, string $motif = null, string $reference = null, User $user = null): bool
    {
        if ($this->quantite_stock < $quantity) {
            return false; // Stock insuffisant
        }

        $oldStock = $this->quantite_stock;
        $this->quantite_stock -= $quantity;
        $this->updated_by = $user ? $user->id : auth()->id();
        $this->save();

        // Créer le mouvement de stock
        $this->mouvementStocks()->create([
            'type_mouvement' => 'sortie',
            'quantite' => -$quantity, // Quantité négative pour les sorties
            'quantite_avant' => $oldStock,
            'quantite_apres' => $this->quantite_stock,
            'prix_unitaire' => $this->prix_vente,
            'valeur_totale' => $quantity * $this->prix_vente,
            'motif' => $motif,
            'reference' => $reference,
            'date_mouvement' => now()->toDateString(),
            'heure_mouvement' => now()->toTimeString(),
            'user_id' => $user ? $user->id : auth()->id(),
        ]);

        return true;
    }

    public function adjustStock(int $newQuantity, string $motif = null, User $user = null): bool
    {
        $oldStock = $this->quantite_stock;
        $difference = $newQuantity - $oldStock;

        $this->quantite_stock = $newQuantity;
        $this->updated_by = $user ? $user->id : auth()->id();
        $this->save();

        // Créer le mouvement de stock
        $this->mouvementStocks()->create([
            'type_mouvement' => 'ajustement',
            'quantite' => $difference,
            'quantite_avant' => $oldStock,
            'quantite_apres' => $this->quantite_stock,
            'motif' => $motif,
            'date_mouvement' => now()->toDateString(),
            'heure_mouvement' => now()->toTimeString(),
            'user_id' => $user ? $user->id : auth()->id(),
        ]);

        return true;
    }

    public function isExpired(): bool
    {
        return $this->date_peremption && $this->date_peremption->isPast();
    }

    public function isLowStock(): bool
    {
        return $this->quantite_stock <= $this->quantite_seuil;
    }

    public function needsRestock(): bool
    {
        return $this->quantite_stock < $this->quantite_ideale;
    }
}
