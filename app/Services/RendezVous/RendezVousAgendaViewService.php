<?php

namespace App\Services\RendezVous;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\User;
use App\Services\Security\ClinicalAuthorizationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RendezVousAgendaViewService
{
    private const STATUS_A_VENIR = 'a_venir';
    private const STATUS_EN_ATTENTE = 'en_attente';
    private const STATUS_EN_SOINS = 'en_soins';
    private const STATUS_VU = 'vu';
    private const STATUS_ABSENT = 'absent';
    private const STATUS_ANNULE = 'annule';

    public function __construct(private readonly ClinicalAuthorizationService $access)
    {
    }

    public function build(string $currentView, string $weekLayout, Carbon $selectedDate, mixed $selectedMedecinId, ?string $selectedStatut, string $searchTerm, ?User $user = null): array
    {
        $applyFilters = function ($query) use ($selectedMedecinId, $selectedStatut, $searchTerm, $user) {
            if ($user) {
                $this->access->scopeRendezVous($query, $user);
            }

            if ($selectedMedecinId && $selectedMedecinId !== 'all') {
                $query->where('medecin_id', $selectedMedecinId);
            }

            if ($selectedStatut && $selectedStatut !== 'all') {
                $query->byStatut($selectedStatut);
            }

            if ($searchTerm !== '') {
                $query->whereHas('patient', function ($patientQuery) use ($searchTerm) {
                    $patientQuery
                        ->where('nom', 'like', "%{$searchTerm}%")
                        ->orWhere('prenom', 'like', "%{$searchTerm}%")
                        ->orWhere('telephone', 'like', "%{$searchTerm}%");
                });
            }
        };

        $dayStart = $selectedDate->copy()->startOfDay();
        $dayEnd = $selectedDate->copy()->endOfDay();

        $viewRangeStart = match ($currentView) {
            'week' => $selectedDate->copy()->startOfWeek(Carbon::MONDAY),
            'month' => $selectedDate->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY),
            default => $dayStart->copy(),
        };
        $viewRangeEnd = match ($currentView) {
            'week' => $selectedDate->copy()->endOfWeek(Carbon::SUNDAY),
            'month' => $selectedDate->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY),
            default => $dayEnd->copy(),
        };

        $dayQuery = $this->agendaAppointmentsQuery()->whereBetween('date_heure', [$dayStart, $dayEnd]);
        $applyFilters($dayQuery);
        $todayAppointments = $dayQuery->orderBy('date_heure')->get();

        if ($currentView === 'day') {
            $appointmentsInView = $todayAppointments;
        } else {
            $viewQuery = $this->agendaAppointmentsQuery()->whereBetween('date_heure', [
                $viewRangeStart->copy()->startOfDay(),
                $viewRangeEnd->copy()->endOfDay(),
            ]);
            $applyFilters($viewQuery);
            $appointmentsInView = $viewQuery->orderBy('date_heure')->get();
        }

        $upcomingQuery = $this->agendaAppointmentsQuery()
            ->where('date_heure', '>', $dayEnd)
            ->where('date_heure', '<=', $selectedDate->copy()->addDays(7)->endOfDay());
        $applyFilters($upcomingQuery);
        $upcomingAppointments = $upcomingQuery->orderBy('date_heure')->limit(5)->get();
        $this->decorateUpcomingAppointments($upcomingAppointments);

        $appointmentsByHour = $todayAppointments->groupBy(static fn (RendezVous $rdv) => $rdv->date_heure->format('H'));
        $appointmentsByDate = $appointmentsInView->groupBy(static fn (RendezVous $rdv) => $rdv->date_heure->format('Y-m-d'));

        $weekDays = collect();
        if ($currentView === 'week') {
            for ($date = $viewRangeStart->copy(); $date <= $viewRangeEnd; $date->addDay()) {
                $weekDays->push($date->copy());
            }
        }

        $startOfMonth = $selectedDate->copy()->startOfMonth()->startOfWeek();
        $endOfMonth = $selectedDate->copy()->endOfMonth()->endOfWeek();

        $monthQuery = RendezVous::query()->whereBetween('date_heure', [
            $startOfMonth->copy()->startOfDay(),
            $endOfMonth->copy()->endOfDay(),
        ]);
        $applyFilters($monthQuery);
        $daysWithAppointments = $monthQuery
            ->selectRaw('DATE(date_heure) as day_date')
            ->groupBy('day_date')
            ->pluck('day_date')
            ->map(static fn ($date) => (string) $date)
            ->toArray();

        $calendarDays = collect();
        $daysLookup = array_flip($daysWithAppointments);
        for ($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $calendarDays->push((object) [
                'date' => $date->copy(),
                'isToday' => $date->isToday(),
                'hasAppointments' => isset($daysLookup[$dateKey]),
                'isCurrentMonth' => $date->month === $selectedDate->month,
            ]);
        }

        $medecinsQuery = Medecin::select('id', 'nom', 'prenom', 'specialite')->orderBy('nom');

        if ($user?->hasRole('medecin')) {
            $medecinsQuery->whereKey($this->access->currentMedecinId($user) ?? 0);
        }

        $medecins = $medecinsQuery->get();
        $statusOptions = [
            self::STATUS_A_VENIR => 'A venir',
            self::STATUS_EN_ATTENTE => 'En attente',
            self::STATUS_EN_SOINS => 'En soins',
            self::STATUS_VU => 'Vu',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_ANNULE => 'Annule',
        ];

        $confirmedCount = $todayAppointments->whereIn('statut', [self::STATUS_A_VENIR, self::STATUS_EN_SOINS, self::STATUS_EN_ATTENTE])->count();
        $urgentCount = $todayAppointments->filter(static fn (RendezVous $rdv) => stripos((string) $rdv->type, 'urgence') !== false)->count();

        [$startHour, $endHour] = $this->resolveHoursWindow($todayAppointments);
        $occupiedSlots = $todayAppointments->count();
        $availableSlots = max(($endHour - $startHour) - $occupiedSlots, 0);
        $displaySummaryLabel = match ($currentView) {
            'week' => 'RDV de la semaine',
            'month' => 'RDV du mois',
            default => 'RDV du jour',
        };
        $displayDescription = match ($currentView) {
            'week' => $weekLayout === 'dense' ? 'Vue dense hebdomadaire pour le pilotage medical multi-actes' : 'Vue hebdomadaire des rendez-vous et disponibilites du cabinet',
            'month' => 'Vue mensuelle des rendez-vous et disponibilites du cabinet',
            default => 'Gerez les rendez-vous et disponibilites de votre cabinet',
        };

        $displayAppointments = $appointmentsInView instanceof Collection ? $appointmentsInView : collect($appointmentsInView);
        $normalizedDisplayStatuses = $displayAppointments->mapWithKeys(fn (RendezVous $appointment) => [
            $appointment->id => RendezVous::normalizeStatus((string) $appointment->statut) ?? self::STATUS_A_VENIR,
        ]);
        $waitingCount = $normalizedDisplayStatuses->filter(fn ($status) => $status === self::STATUS_EN_ATTENTE)->count();
        $inProgressCount = $normalizedDisplayStatuses->filter(fn ($status) => $status === self::STATUS_EN_SOINS)->count();
        $completedTodayCount = $normalizedDisplayStatuses->filter(fn ($status) => $status === self::STATUS_VU)->count();
        $absentTodayCount = $normalizedDisplayStatuses->filter(fn ($status) => $status === self::STATUS_ABSENT)->count();
        $averageDurationToday = (int) round($displayAppointments->filter(fn (RendezVous $appointment) => in_array($normalizedDisplayStatuses->get($appointment->id), [self::STATUS_EN_SOINS, self::STATUS_VU], true))->avg('duree') ?? 0);
        $delayedCount = $displayAppointments->filter(function (RendezVous $appointment) use ($normalizedDisplayStatuses) {
            $status = $normalizedDisplayStatuses->get($appointment->id) ?? self::STATUS_A_VENIR;
            return !in_array($status, [self::STATUS_VU, self::STATUS_ABSENT, self::STATUS_ANNULE], true) && $appointment->date_heure->isPast();
        })->count();
        $snapshotPrimaryLabel = match ($currentView) {
            'week' => $weekLayout === 'dense' ? 'Blocs semaine' : 'RDV semaine',
            'month' => 'RDV mois',
            default => 'RDV du jour',
        };

        $denseAppointmentsByDate = $appointmentsByDate->map(function ($appointments) {
            return $appointments->map(function (RendezVous $rdv) {
                $normalizedStatus = RendezVous::normalizeStatus((string) $rdv->statut) ?? self::STATUS_A_VENIR;
                $patientName = trim((string) optional($rdv->patient)->prenom . ' ' . (string) optional($rdv->patient)->nom);
                $doctorName = trim((string) optional($rdv->medecin)->prenom . ' ' . (string) optional($rdv->medecin)->nom);
                $rawAct = trim((string) ($rdv->type ?: $rdv->motif));
                $rawActLower = mb_strtolower(trim((string) ($rdv->type . ' ' . $rdv->motif)), 'UTF-8');

                [$denseTypeClass, $denseTypeLabel] = $this->resolveDenseActPresentation($rawActLower, $normalizedStatus, $rawAct);
                [$denseStatusClass, $denseStatusLabel] = $this->resolveDenseStatusPresentation($normalizedStatus);

                $rdv->dense_patient_name = $patientName !== '' ? $patientName : 'Patient inconnu';
                $rdv->dense_doctor_name = $doctorName !== '' ? 'Dr. ' . $doctorName : 'Medecin inconnu';
                $rdv->dense_type_class = $denseTypeClass;
                $rdv->dense_type_label = $denseTypeLabel;
                $rdv->dense_status_class = $denseStatusClass;
                $rdv->dense_status_label = $denseStatusLabel;
                $rdv->dense_presence_class = ($rdv->arrived_at || in_array($normalizedStatus, [self::STATUS_EN_ATTENTE, self::STATUS_EN_SOINS, self::STATUS_VU], true)) ? 'is-present' : 'is-pending';
                $rdv->dense_presence_icon = ($rdv->arrived_at || in_array($normalizedStatus, [self::STATUS_EN_ATTENTE, self::STATUS_EN_SOINS, self::STATUS_VU], true)) ? 'fas fa-user-check' : 'fas fa-user-clock';
                $rdv->dense_presence_label = ($rdv->arrived_at || in_array($normalizedStatus, [self::STATUS_EN_ATTENTE, self::STATUS_EN_SOINS, self::STATUS_VU], true)) ? 'Presence confirmee' : 'Presence non confirmee';
                $rdv->dense_open_url = $rdv->patient_id ? route('patients.show', $rdv->patient_id) : route('rendezvous.edit', $rdv);
                $rdv->dense_edit_url = route('rendezvous.edit', $rdv);
                $rdv->dense_sms_url = route('sms.create', ['rendezvous_id' => $rdv->id]);
                $rdv->dense_facture_url = $rdv->patient_id ? route('factures.create', ['patient_id' => $rdv->patient_id]) : null;
                $rdv->dense_ordonnance_url = $rdv->patient_id ? route('ordonnances.create', ['patient_id' => $rdv->patient_id, 'medecin_id' => $rdv->medecin_id]) : null;
                $rdv->dense_consultation_url = $rdv->patient_id ? route('consultations.create', ['patient_id' => $rdv->patient_id, 'medecin_id' => $rdv->medecin_id, 'rendez_vous_id' => $rdv->id]) : null;
                return $rdv;
            })->sortBy('date_heure')->values();
        });

        return compact('todayAppointments', 'appointmentsInView', 'displayAppointments', 'upcomingAppointments', 'appointmentsByHour', 'appointmentsByDate', 'denseAppointmentsByDate', 'weekDays', 'calendarDays', 'medecins', 'currentView', 'weekLayout', 'selectedDate', 'selectedMedecinId', 'selectedStatut', 'searchTerm', 'statusOptions', 'daysWithAppointments', 'confirmedCount', 'urgentCount', 'availableSlots', 'waitingCount', 'inProgressCount', 'completedTodayCount', 'absentTodayCount', 'averageDurationToday', 'delayedCount', 'snapshotPrimaryLabel', 'startHour', 'endHour', 'displaySummaryLabel', 'displayDescription');
    }

    private function decorateUpcomingAppointments(Collection $upcomingAppointments): void
    {
        $upcomingAppointments->transform(function (RendezVous $upcoming) {
            $upType = mb_strtolower((string) $upcoming->type, 'UTF-8');
            $upMotif = trim((string) ($upcoming->type ?: $upcoming->motif));
            $upcoming->display_class = str_contains($upType, 'urgence') ? 'urgent' : 'confirmed';
            $upPatient = trim((string) optional($upcoming->patient)->prenom . ' ' . (string) optional($upcoming->patient)->nom);
            $upcoming->display_patient = $upPatient !== '' ? $upPatient : 'Patient inconnu';
            $upDoctor = trim((string) optional($upcoming->medecin)->prenom . ' ' . (string) optional($upcoming->medecin)->nom);
            $upcoming->display_doctor = $upDoctor !== '' ? 'Dr. ' . $upDoctor : __('messages.common.doctor_unknown');
            $upcoming->display_motif = $upMotif;
            $upcoming->display_photo = $this->resolvePatientPhotoUrl($upcoming->patient);
            $upInitials = trim(mb_substr((string) optional($upcoming->patient)->prenom, 0, 1, 'UTF-8') . mb_substr((string) optional($upcoming->patient)->nom, 0, 1, 'UTF-8'));
            $upcoming->display_initials = $upInitials !== '' ? mb_strtoupper($upInitials, 'UTF-8') : 'PT';
            return $upcoming;
        });
    }

    private function resolveHoursWindow(Collection $todayAppointments): array
    {
        $startHour = 8;
        $endHour = 19;
        if ($todayAppointments->isNotEmpty()) {
            $earliestHour = (int) $todayAppointments->min(static fn (RendezVous $rdv) => (int) $rdv->date_heure->format('H'));
            $latestHour = (int) $todayAppointments->max(static function (RendezVous $rdv) {
                $end = $rdv->date_heure->copy()->addMinutes((int) $rdv->duree);
                return (int) $end->format('H') + ($end->minute > 0 ? 1 : 0);
            });
            $startHour = max(6, min($startHour, $earliestHour));
            $endHour = min(22, max($endHour, $latestHour));
            if ($endHour <= $startHour) {
                $endHour = min(22, $startHour + 1);
            }
        }
        return [$startHour, $endHour];
    }

    private function resolvePatientPhotoUrl(?Patient $patient): ?string
    {
        $photoPath = trim((string) ($patient?->photo ?? ''));
        if ($photoPath === '') {
            return null;
        }
        if (str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://')) {
            return $photoPath;
        }
        return asset('storage/' . ltrim($photoPath, '/'));
    }

    private function resolveDenseStatusPresentation(string $status): array
    {
        return match ($status) {
            self::STATUS_EN_ATTENTE => ['dense-status-waiting', 'En attente'],
            self::STATUS_EN_SOINS => ['dense-status-active', 'En consultation'],
            self::STATUS_VU => ['dense-status-done', 'Termine'],
            self::STATUS_ABSENT => ['dense-status-absent', 'Absent'],
            self::STATUS_ANNULE => ['dense-status-cancelled', 'Annule'],
            default => ['dense-status-upcoming', 'A venir'],
        };
    }

    private function resolveDenseActPresentation(string $rawActLower, string $status, string $fallbackLabel): array
    {
        if ($status === self::STATUS_ABSENT) {
            return ['dense-act-absence', 'Absence'];
        }
        if (str_contains($rawActLower, 'premiere consultation') || str_contains($rawActLower, 'première consultation')) {
            return ['dense-act-first', 'Premiere consultation'];
        }
        if (str_contains($rawActLower, 'bilan')) {
            return ['dense-act-bilan', 'Bilan'];
        }
        if (str_contains($rawActLower, 'suivi')) {
            return ['dense-act-followup', 'Suivi'];
        }
        if (str_contains($rawActLower, 'injection')) {
            return ['dense-act-injection', 'Injection'];
        }
        if (str_contains($rawActLower, 'chimio')) {
            return ['dense-act-chimio', 'Chimio'];
        }
        if (str_contains($rawActLower, 'scan')) {
            return ['dense-act-scan', 'Scan'];
        }
        if (str_contains($rawActLower, 'consult')) {
            return ['dense-act-consultation', 'Consultation'];
        }
        return ['dense-act-consultation', $fallbackLabel !== '' ? $fallbackLabel : 'Consultation'];
    }

    private function agendaAppointmentsQuery()
    {
        return RendezVous::query()
            ->select(['id', 'patient_id', 'medecin_id', 'date_heure', 'duree', 'type', 'motif', 'statut', 'arrived_at', 'consultation_started_at'])
            ->with(['patient:id,nom,prenom,photo,numero_dossier', 'medecin:id,nom,prenom,specialite']);
    }
}
