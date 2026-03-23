<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_facture',
        'patient_id',
        'consultation_id',
        'medecin_id',
        'date_facture',
        'date_echeance',
        'montant_total',
        'remise',
        'statut',
        'mode_paiement',
        'date_paiement',
        'notes',
    ];

    protected $casts = [
        'date_facture' => 'date',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
        'montant_total' => 'decimal:2',
        'remise' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function ligneFactures()
    {
        return $this->hasMany(LigneFacture::class);
    }

    public static function generateNumero(): string
    {
        $last = self::latest()->first();
        $numero = $last ? intval(substr($last->numero_facture, -6)) + 1 : 1;
        return 'FAC-' . date('Y') . '-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function getMontantNetAttribute()
    {
        return $this->montant_total - $this->remise;
    }
}
