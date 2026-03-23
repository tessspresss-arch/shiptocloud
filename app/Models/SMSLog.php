<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSLog extends Model
{
    use HasFactory;

    protected $table = 'sms_logs';

    protected $fillable = [
        'patient_id',
        'telephone',
        'message',
        'type',
        'statut',
        'provider',
        'provider_message_id',
        'code_erreur',
        'erreur_details',
        'created_by'
    ];

    // RELATIONS
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // SCOPES
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeRecents($query, $jours = 30)
    {
        return $query->whereBetween('created_at', [
            now()->subDays($jours),
            now()
        ]);
    }

    public function scopeEchoues($query)
    {
        return $query->where('statut', 'echec');
    }

    // ACCESSORS
    public function getIsSuccessAttribute()
    {
        return $this->statut === 'envoye' || $this->statut === 'delivre';
    }
}
