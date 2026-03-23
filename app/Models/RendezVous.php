<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    private const STATUS_A_VENIR = 'a_venir';
    private const STATUS_EN_ATTENTE = 'en_attente';
    private const STATUS_EN_SOINS = 'en_soins';
    private const STATUS_VU = 'vu';
    private const STATUS_ABSENT = 'absent';
    private const STATUS_ANNULE = 'annule';

    protected $table = 'rendez_vous';

    protected $fillable = [
        'patient_id', 'medecin_id', 'date_heure', 'date_rdv', 'statut',
        'motif', 'type', 'duree', 'notes',
        'arrived_at', 'consultation_started_at', 'consultation_finished_at', 'absent_marked_at'
    ];

    protected $casts = [
        'date_heure' => 'datetime',
        'date_rdv' => 'datetime',
        'arrived_at' => 'datetime',
        'consultation_started_at' => 'datetime',
        'consultation_finished_at' => 'datetime',
        'absent_marked_at' => 'datetime',
    ];

    // RELATIONS
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
        return $this->hasOne(Consultation::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(RendezVousStatusHistory::class, 'rendez_vous_id');
    }

    // SCOPES
    public function scopeToday($query)
    {
        return $query->whereDate('date_heure', today());
    }

    public function scopeConfirmed($query)
    {
        return $query->where('statut', self::STATUS_EN_SOINS);
    }

    public function scopeFuture($query)
    {
        return $query->where('date_heure', '>', now());
    }

    public function scopeByStatut($query, ?string $statut)
    {
        $normalized = self::normalizeStatus($statut);

        if ($normalized === null || $normalized === '') {
            return $query;
        }

        return $query->where('statut', $normalized);
    }

    // ACCESSORS
    public function getDateAttribute()
    {
        return $this->date_heure->format('d/m/Y');
    }

    public function getHeureAttribute()
    {
        return $this->date_heure->format('H:i');
    }

    public function getEstPasseAttribute()
    {
        return $this->date_heure < now();
    }

    public function getTypeAttribute($value)
    {
        return $this->normalizeEncoding($value);
    }

    public function getMotifAttribute($value)
    {
        return $this->normalizeEncoding($value);
    }

    // MUTATORS
    public function setStatutAttribute($value): void
    {
        $this->attributes['statut'] = self::normalizeStatus($value) ?? self::STATUS_A_VENIR;
    }

    public function setDateHeureAttribute($value): void
    {
        $normalized = $this->normalizeDateTime($value);
        $this->attributes['date_heure'] = $normalized;
        $this->attributes['date_rdv'] = $normalized;
    }

    public function setDateRdvAttribute($value): void
    {
        $normalized = $this->normalizeDateTime($value);
        $this->attributes['date_rdv'] = $normalized;
        $this->attributes['date_heure'] = $normalized;
    }

        public static function normalizeStatus(?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        $value = trim((string) $status);
        if ($value === '') {
            return null;
        }

        // Collapse double-encoded fragments before status normalization.
        $value = str_replace("\u{00C3}\u{0192}\u{00C2}", "\u{00C3}", $value);

        // Normalize common mojibake fragments back to valid UTF-8.
        $value = strtr($value, [
            "\u{00C3}\u{00A0}" => "\u{00E0}",
            "\u{00C3}\u{00A9}" => "\u{00E9}",
            "\u{00C3}\u{00A8}" => "\u{00E8}",
            "\u{00C3}\u{00AA}" => "\u{00EA}",
            "\u{00C3}\u{00B9}" => "\u{00F9}",
            "\u{00C3}\u{00BB}" => "\u{00FB}",
            "\u{00C3}\u{00B4}" => "\u{00F4}",
            "\u{00C3}\u{00AE}" => "\u{00EE}",
            "\u{00C3}\u{00A7}" => "\u{00E7}",
        ]);

        $value = mb_strtolower($value, 'UTF-8');
        $value = str_replace(["\u{00A0}", '-', ' '], '_', $value);
        $value = str_replace(["\u{00E1}", "\u{00E0}", "\u{00E2}"], 'a', $value);
        $value = str_replace(["\u{00E9}", "\u{00E8}", "\u{00EA}"], 'e', $value);

        return match ($value) {
            'a_venir', "programmé", 'programme', 'confirme', "confirmé" => self::STATUS_A_VENIR,
            'attente', 'salle_attente', 'salle_d_attente', 'en_attente' => self::STATUS_EN_ATTENTE,
            'consultation', 'en_consultation', 'salle_soin', 'salle_de_soin', 'en_soins' => self::STATUS_EN_SOINS,
            'vu', 'termine', "terminé", 'terminee', "terminée" => self::STATUS_VU,
            'absent' => self::STATUS_ABSENT,
            'annule', "annulé" => self::STATUS_ANNULE,
            default => null,
        };
    }
    private function normalizeDateTime($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $timezone = config('app.timezone', 'UTC');

        if ($value instanceof CarbonInterface) {
            return $value->copy()->setTimezone($timezone)->format('Y-m-d H:i:s');
        }

        return Carbon::parse((string) $value, $timezone)
            ->setTimezone($timezone)
            ->format('Y-m-d H:i:s');
    }

    private function normalizeEncoding($value)
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        if (
            !str_contains($value, "\u{00C3}")
            && !str_contains($value, "\u{00C2}")
            && !str_contains($value, "\u{00E2}")
        ) {
            return $value;
        }

        // First collapse double-encoded UTF-8 fragments.
        $value = str_replace("\u{00C3}\u{0192}\u{00C2}", "\u{00C3}", $value);

        return strtr($value, [
            "\u{00C3}\u{00A9}" => "\u{00E9}",
            "\u{00C3}\u{00A8}" => "\u{00E8}",
            "\u{00C3}\u{00AA}" => "\u{00EA}",
            "\u{00C3}\u{00AB}" => "\u{00EB}",
            "\u{00C3}\u{00A0}" => "\u{00E0}",
            "\u{00C3}\u{00A2}" => "\u{00E2}",
            "\u{00C3}\u{00AE}" => "\u{00EE}",
            "\u{00C3}\u{00B4}" => "\u{00F4}",
            "\u{00C3}\u{00B9}" => "\u{00F9}",
            "\u{00C3}\u{00BB}" => "\u{00FB}",
            "\u{00C3}\u{00A7}" => "\u{00E7}",
            "\u{00C3}\u{0089}" => "\u{00C9}",
            "\u{00C3}\u{0080}" => "\u{00C0}",
            "\u{00C3}\u{0087}" => "\u{00C7}",
            "\u{00E2}\u{20AC}\u{2122}" => "\u{2019}",
            "\u{00E2}\u{20AC}\u{201C}" => "\u{2013}",
        ]);
    }

    // Accessor pour le statut formaté
    public function getStatusLabelAttribute()
    {
        return match($this->statut) {
            self::STATUS_A_VENIR => 'A venir',
            self::STATUS_EN_ATTENTE => 'En attente',
            self::STATUS_EN_SOINS => 'En soins',
            self::STATUS_VU => 'Vu',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_ANNULE => 'Annule',
            default => 'Inconnu'
        };
    }
    // Accessor pour la priorité (urgence)
    public function getPrioriteAttribute()
    {
        return match($this->type) {
            'urgence' => 'urgent',
            default => 'normal'
        };
    }

    // Accessor pour l'icône du type de consultation
    public function getTypeConsultationIconAttribute()
    {
        return match($this->type) {
            'consultation' => 'fas fa-stethoscope',
            'controle' => 'fas fa-clipboard-check',
            'urgence' => 'fas fa-exclamation-triangle',
            'teleconsultation' => 'fas fa-video',
            default => 'fas fa-calendar-check'
        };
    }

    // Accessor pour l'heure de fin
    public function getHeureFinAttribute()
    {
        return $this->date_heure->copy()->addMinutes($this->duree);
    }
}



