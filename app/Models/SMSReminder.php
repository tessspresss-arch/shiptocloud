<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSReminder extends Model
{
    use HasFactory;

    protected $table = 'sms_reminders';

    protected $fillable = [
        'rendezvous_id',
        'patient_id',
        'telephone',
        'heures_avant',
        'statut',
        'date_envoi_prevue',
        'date_envoi_reelle',
        'message_template',
        'code_erreur',
        'erreur_message',
        'provider',
        'provider_id'
    ];

    protected $casts = [
        'date_envoi_prevue' => 'datetime',
        'date_envoi_reelle' => 'datetime',
    ];

    // RELATIONS
    public function rendezvous()
    {
        return $this->belongsTo(RendezVous::class, 'rendezvous_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // SCOPES
    public function scopePlanifies($query)
    {
        return $query->where('statut', 'planifie');
    }

    public function scopeAEnvoyer($query)
    {
        return $query->where('statut', 'planifie')
                     ->where('date_envoi_prevue', '<=', now());
    }

    public function scopeRecents($query, $jours = 30)
    {
        return $query->whereBetween('created_at', [
            now()->subDays($jours),
            now()
        ]);
    }

    // ACCESSORS
    public function getIsEnvoyeAttribute()
    {
        return $this->statut === 'envoye' || $this->statut === 'delivre';
    }

    public function getHeuresRestantesAttribute()
    {
        return $this->date_envoi_prevue ? $this->date_envoi_prevue->diffInHours(now()) : null;
    }
}
