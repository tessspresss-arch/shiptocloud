<?php

namespace App\Services\Security;

use App\Models\CertificatMedical;
use App\Models\Consultation;
use App\Models\DocumentMedical;
use App\Models\DossierMedical;
use App\Models\Examen;
use App\Models\Facture;
use App\Models\Medecin;
use App\Models\Ordonnance;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ClinicalAuthorizationService
{
    /**
     * @var array<int, \App\Models\Medecin|null>
     */
    private array $currentMedecins = [];

    /**
     * @var array<int, bool>
     */
    private array $legacyPermissionFallback = [];

    /**
     * @var array<string, array<string, bool>>
     */
    private array $tableColumns = [];

    public function allows(User $user, string $resource, string $action): bool
    {
        return $this->allowsCode($user, sprintf('%s.%s', $resource, $action));
    }

    public function allowsCode(User $user, string $permissionCode): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->usesLegacyModulePermissions($user)) {
            $resource = explode('.', $permissionCode)[0] ?? '';

            return $resource !== '' && $user->hasModuleAccess($resource);
        }

        return $user->hasPermissionCode($permissionCode);
    }

    public function currentMedecin(?User $user): ?Medecin
    {
        if (!$user) {
            return null;
        }

        $cacheKey = (int) ($user->id ?? 0);
        if (array_key_exists($cacheKey, $this->currentMedecins)) {
            return $this->currentMedecins[$cacheKey];
        }

        $query = Medecin::query()->select(['id', 'nom', 'prenom', 'email']);

        $medecinId = (int) ($user->getAttribute('medecin_id') ?? 0);
        if ($medecinId > 0) {
            $direct = (clone $query)->find($medecinId);
            if ($direct) {
                return $this->currentMedecins[$cacheKey] = $direct;
            }
        }

        $email = trim((string) $user->email);
        if ($email !== '') {
            $emailMatch = (clone $query)->where('email', $email)->first();
            if ($emailMatch) {
                return $this->currentMedecins[$cacheKey] = $emailMatch;
            }
        }

        $parts = preg_split('/\s+/', trim((string) $user->name)) ?: [];
        if (count($parts) >= 2) {
            $prenom = (string) $parts[0];
            $nom = (string) $parts[count($parts) - 1];

            $nameMatch = (clone $query)
                ->whereRaw('LOWER(prenom) = ?', [mb_strtolower($prenom, 'UTF-8')])
                ->whereRaw('LOWER(nom) = ?', [mb_strtolower($nom, 'UTF-8')])
                ->first();

            if ($nameMatch) {
                return $this->currentMedecins[$cacheKey] = $nameMatch;
            }
        }

        return $this->currentMedecins[$cacheKey] = null;
    }

    public function currentMedecinId(?User $user): ?int
    {
        return $this->currentMedecin($user)?->id;
    }

    public function canAccessPatient(User $user, Patient $patient): bool
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return true;
        }

        $currentMedecinId = $this->currentMedecinId($user);
        if (!$currentMedecinId) {
            return false;
        }

        return $this->scopePatients(Patient::query()->whereKey($patient->getKey()), $user)->exists();
    }

    public function canAccessConsultation(User $user, Consultation $consultation): bool
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return true;
        }

        $currentMedecinId = $this->currentMedecinId($user);

        return $currentMedecinId !== null && (int) $consultation->medecin_id === $currentMedecinId;
    }

    public function canAccessRendezVous(User $user, RendezVous $rendezVous): bool
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return true;
        }

        $currentMedecinId = $this->currentMedecinId($user);

        return $currentMedecinId !== null && (int) $rendezVous->medecin_id === $currentMedecinId;
    }

    public function canAccessOrdonnance(User $user, Ordonnance $ordonnance): bool
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return true;
        }

        $currentMedecinId = $this->currentMedecinId($user);
        if ($currentMedecinId === null) {
            return false;
        }

        $ordonnance->loadMissing(['consultation:id,medecin_id,patient_id', 'patient:id']);
        $linkedMedecinId = (int) ($ordonnance->medecin_id ?: $ordonnance->consultation?->medecin_id ?: 0);

        if ($linkedMedecinId > 0) {
            return $linkedMedecinId === $currentMedecinId;
        }

        return $ordonnance->patient ? $this->canAccessPatient($user, $ordonnance->patient) : false;
    }

    public function canAccessFacture(User $user, Facture $facture): bool
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return true;
        }

        $currentMedecinId = $this->currentMedecinId($user);
        if ($currentMedecinId === null) {
            return false;
        }

        $facture->loadMissing(['consultation:id,medecin_id,patient_id', 'patient:id']);
        $linkedMedecinId = (int) ($facture->medecin_id ?: $facture->consultation?->medecin_id ?: 0);

        if ($linkedMedecinId > 0) {
            return $linkedMedecinId === $currentMedecinId;
        }

        return $facture->patient ? $this->canAccessPatient($user, $facture->patient) : false;
    }

    public function canAccessDossier(User $user, DossierMedical $dossier): bool
    {
        $dossier->loadMissing('patient:id');

        return $dossier->patient ? $this->canAccessPatient($user, $dossier->patient) : false;
    }

    public function canAccessDocument(User $user, DocumentMedical $document): bool
    {
        $document->loadMissing('archive.patient:id');

        return $document->archive?->patient
            ? $this->canAccessPatient($user, $document->archive->patient)
            : false;
    }

    public function scopePatients(Builder $query, User $user): Builder
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return $query;
        }

        $currentMedecinId = $this->currentMedecinId($user);
        if ($currentMedecinId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $patientQuery) use ($currentMedecinId) {
            $patientQuery
                ->whereHas('consultations', function (Builder $consultationQuery) use ($currentMedecinId) {
                    $consultationQuery->where('medecin_id', $currentMedecinId);
                })
                ->orWhereHas('rendezvous', function (Builder $rendezVousQuery) use ($currentMedecinId) {
                    $rendezVousQuery->where('medecin_id', $currentMedecinId);
                });

            if ($this->tableHasColumn('ordonnances', 'medecin_id')) {
                $patientQuery->orWhereHas('ordonnances', function (Builder $ordonnanceQuery) use ($currentMedecinId) {
                    $ordonnanceQuery->where('medecin_id', $currentMedecinId);
                });
            } elseif ($this->tableHasColumn('ordonnances', 'consultation_id')) {
                $patientQuery->orWhereHas('ordonnances.consultation', function (Builder $consultationQuery) use ($currentMedecinId) {
                    $consultationQuery->where('medecin_id', $currentMedecinId);
                });
            }

            if ($this->tableHasColumn('factures', 'medecin_id')) {
                $patientQuery->orWhereHas('factures', function (Builder $factureQuery) use ($currentMedecinId) {
                    $factureQuery->where('medecin_id', $currentMedecinId);
                });
            }

            if ($this->tableHasColumn('examens', 'medecin_id')) {
                $patientQuery->orWhereHas('examens', function (Builder $examenQuery) use ($currentMedecinId) {
                    $examenQuery->where('medecin_id', $currentMedecinId);
                });
            }

            if ($this->tableHasColumn('certificats_medicaux', 'medecin_id')) {
                $patientQuery->orWhereHas('certificats', function (Builder $certificatQuery) use ($currentMedecinId) {
                    $certificatQuery->where('medecin_id', $currentMedecinId);
                });
            }

            $patientQuery->orWhere(function (Builder $unassignedQuery) {
                $unassignedQuery
                    ->whereDoesntHave('consultations')
                    ->whereDoesntHave('rendezvous');

                if ($this->tableHasColumn('ordonnances', 'medecin_id')) {
                    $unassignedQuery->whereDoesntHave('ordonnances', function (Builder $ordonnanceQuery) {
                        $ordonnanceQuery->whereNotNull('medecin_id');
                    });
                } elseif ($this->tableHasColumn('ordonnances', 'consultation_id')) {
                    $unassignedQuery->whereDoesntHave('ordonnances', function (Builder $ordonnanceQuery) {
                        $ordonnanceQuery->whereNotNull('consultation_id');
                    });
                } else {
                    $unassignedQuery->whereDoesntHave('ordonnances');
                }

                if ($this->tableHasColumn('factures', 'medecin_id')) {
                    $unassignedQuery->whereDoesntHave('factures', function (Builder $factureQuery) {
                        $factureQuery->whereNotNull('medecin_id');
                    });
                } else {
                    $unassignedQuery->whereDoesntHave('factures');
                }

                if ($this->tableHasColumn('examens', 'medecin_id')) {
                    $unassignedQuery->whereDoesntHave('examens', function (Builder $examenQuery) {
                        $examenQuery->whereNotNull('medecin_id');
                    });
                } else {
                    $unassignedQuery->whereDoesntHave('examens');
                }

                if ($this->tableHasColumn('certificats_medicaux', 'medecin_id')) {
                    $unassignedQuery->whereDoesntHave('certificats', function (Builder $certificatQuery) {
                        $certificatQuery->whereNotNull('medecin_id');
                    });
                } else {
                    $unassignedQuery->whereDoesntHave('certificats');
                }
            });
        });
    }

    public function scopeConsultations(Builder $query, User $user): Builder
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return $query;
        }

        $currentMedecinId = $this->currentMedecinId($user);

        return $currentMedecinId === null
            ? $query->whereRaw('1 = 0')
            : $query->where('medecin_id', $currentMedecinId);
    }

    public function scopeRendezVous(Builder $query, User $user): Builder
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return $query;
        }

        $currentMedecinId = $this->currentMedecinId($user);

        return $currentMedecinId === null
            ? $query->whereRaw('1 = 0')
            : $query->where('medecin_id', $currentMedecinId);
    }

    public function scopeOrdonnances(Builder $query, User $user): Builder
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return $query;
        }

        $currentMedecinId = $this->currentMedecinId($user);
        if ($currentMedecinId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $ordonnanceQuery) use ($currentMedecinId) {
            $hasDirectConstraint = false;

            if ($this->tableHasColumn('ordonnances', 'medecin_id')) {
                $ordonnanceQuery->where('medecin_id', $currentMedecinId);
                $hasDirectConstraint = true;
            }

            if ($this->tableHasColumn('ordonnances', 'consultation_id')) {
                $method = $hasDirectConstraint ? 'orWhereHas' : 'whereHas';

                $ordonnanceQuery->{$method}('consultation', function (Builder $consultationQuery) use ($currentMedecinId) {
                    $consultationQuery->where('medecin_id', $currentMedecinId);
                });

                return;
            }

            if (!$hasDirectConstraint) {
                $ordonnanceQuery->whereRaw('1 = 0');
            }
        });
    }

    public function scopeFactures(Builder $query, User $user): Builder
    {
        if (!$this->shouldRestrictToCurrentMedecin($user)) {
            return $query;
        }

        $currentMedecinId = $this->currentMedecinId($user);
        if ($currentMedecinId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $factureQuery) use ($currentMedecinId) {
            $factureQuery
                ->where('medecin_id', $currentMedecinId)
                ->orWhereHas('consultation', function (Builder $consultationQuery) use ($currentMedecinId) {
                    $consultationQuery->where('medecin_id', $currentMedecinId);
                });
        });
    }

    private function shouldRestrictToCurrentMedecin(User $user): bool
    {
        return $user->hasRole('medecin');
    }

    private function usesLegacyModulePermissions(User $user): bool
    {
        $cacheKey = (int) ($user->id ?? 0);
        if (array_key_exists($cacheKey, $this->legacyPermissionFallback)) {
            return $this->legacyPermissionFallback[$cacheKey];
        }

        try {
            return $this->legacyPermissionFallback[$cacheKey] = !$user->roles()->exists()
                && !$user->permissionOverrides()->exists();
        } catch (Throwable) {
            return $this->legacyPermissionFallback[$cacheKey] = true;
        }
    }

    private function tableHasColumn(string $table, string $column): bool
    {
        if (!isset($this->tableColumns[$table])) {
            try {
                $this->tableColumns[$table] = array_fill_keys(Schema::getColumnListing($table), true);
            } catch (Throwable) {
                $this->tableColumns[$table] = [];
            }
        }

        return isset($this->tableColumns[$table][$column]);
    }
}
