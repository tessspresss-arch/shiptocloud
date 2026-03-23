<?php
// app/Models/LigneFacture.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneFacture extends Model
{
    use HasFactory;
    
    protected $table = 'ligne_factures';
    
    protected $fillable = [
        'facture_id',
        'description',
        'quantite',
        'prix_unitaire',
        'total_ligne',
        'type'
    ];
    
    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'total_ligne' => 'decimal:2',
    ];
    
    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }
}
