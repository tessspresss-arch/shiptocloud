<?php

namespace App\Services;

use App\Models\Depense;
use App\Models\Facture;
use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Ordonnance;
use App\Models\Patient;
use App\Models\ResultatExamen;
use App\Models\RendezVous;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private const FACTURE_STATUT_PAYEE = [
        'payee',
        'payée',
        "pay\u{00C3}\u{00A9}e",
        "pay\u{00C3}\u{0192}\u{00C2}\u{00A9}e",
    ];
    private const RDV_STATUT_ANNULE = [
        'annule',
        'annulé',
        "annul\u{00C3}\u{00A9}",
        "annul\u{00C3}\u{0192}\u{00C2}\u{00A9}",
    ];

    /**
     * Get key statistics.
     */
    public static function getStatistics(): array
    {
        $today = today();
        $cacheKey = 'dashboard:statistics:' . $today->toDateString();

        return Cache::remember($cacheKey, now()->addMinutes(3), static function () use ($today): array {
            $monthStart = $today->copy()->startOfMonth();
            $monthEnd = $today->copy()->endOfMonth();
            $year = (int) $today->year;

            try {
                $patientStats = Patient::query()
                    ->selectRaw('COUNT(*) as total')
                    ->selectRaw('SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as nouveaux_mois', [
                        $monthStart->copy()->startOfDay(),
                        $monthEnd->copy()->endOfDay(),
                    ])
                    ->first();

                $rdvStats = RendezVous::query()
                    ->whereBetween('date_heure', [$today->copy()->startOfDay(), $today->copy()->addWeek()->endOfDay()])
                    ->selectRaw('COUNT(*) as semaine')
                    ->selectRaw('SUM(CASE WHEN DATE(date_heure) = ? THEN 1 ELSE 0 END) as aujourd_hui', [$today->toDateString()])
                    ->first();

                $rdvToday = RendezVous::query()
                    ->whereBetween('date_heure', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                    ->get(['statut', 'consultation_started_at', 'consultation_finished_at']);

                $consultationsTerminees = $rdvToday->filter(function (RendezVous $rdv) {
                    return $rdv->statut === 'vu' || $rdv->consultation_finished_at !== null;
                })->count();

                $waitingRoomCount = $rdvToday->where('statut', 'en_attente')->count();
                $absentCount = $rdvToday->where('statut', 'absent')->count();

                $averageConsultationMinutes = (int) round(
                    $rdvToday
                        ->filter(fn (RendezVous $rdv) => $rdv->consultation_started_at && $rdv->consultation_finished_at)
                        ->map(fn (RendezVous $rdv) => $rdv->consultation_started_at->diffInMinutes($rdv->consultation_finished_at))
                        ->average() ?? 0
                );

                $factureStats = Facture::query()
                    ->whereYear('date_facture', $year)
                    ->selectRaw('SUM(CASE WHEN date_facture BETWEEN ? AND ? THEN montant_total ELSE 0 END) as revenus_mois', [
                        $monthStart->toDateString(),
                        $monthEnd->toDateString(),
                    ])
                    ->selectRaw('SUM(montant_total) as revenus_annee')
                    ->first();

                $depenseStats = self::safeDepenseAggregate($monthStart, $monthEnd, $year);

                return [
                    'patients_total' => (int) ($patientStats->total ?? 0),
                    'patients_nouveaux_mois' => (int) ($patientStats->nouveaux_mois ?? 0),
                    'rdv_aujourd_hui' => (int) ($rdvStats->aujourd_hui ?? 0),
                    'rdv_semaine' => (int) ($rdvStats->semaine ?? 0),
                    'patients_salle_attente' => $waitingRoomCount,
                    'consultations_terminees_aujourdhui' => $consultationsTerminees,
                    'patients_absents_aujourdhui' => $absentCount,
                    'temps_moyen_consultation' => $averageConsultationMinutes,
                    'revenus_mois' => (float) ($factureStats->revenus_mois ?? 0),
                    'revenus_annee' => (float) ($factureStats->revenus_annee ?? 0),
                    'depenses_mois' => $depenseStats['depenses_mois'],
                    'depenses_annee' => $depenseStats['depenses_annee'],
                ];
            } catch (\Throwable $e) {
                return [
                    'patients_total' => 0,
                    'patients_nouveaux_mois' => 0,
                    'rdv_aujourd_hui' => 0,
                    'rdv_semaine' => 0,
                    'patients_salle_attente' => 0,
                    'consultations_terminees_aujourdhui' => 0,
                    'patients_absents_aujourdhui' => 0,
                    'temps_moyen_consultation' => 0,
                    'revenus_mois' => 0,
                    'revenus_annee' => 0,
                    'depenses_mois' => 0,
                    'depenses_annee' => 0,
                ];
            }
        });
    }

    /**
     * Safely query depenses table.
     */
    private static function safeDepenseQuery($month = null, $year = null, $field = 'montant')
    {
        try {
            $query = Depense::query();

            if ($month && $year) {
                $query->whereMonth('date_depense', $month)
                    ->whereYear('date_depense', $year);
            } elseif ($year) {
                $query->whereYear('date_depense', $year);
            }

            return (float) $query->sum($field);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Return month/year expenses using a single SQL aggregate query.
     */
    private static function safeDepenseAggregate(Carbon $monthStart, Carbon $monthEnd, int $year): array
    {
        try {
            $row = Depense::query()
                ->whereYear('date_depense', $year)
                ->selectRaw('SUM(CASE WHEN date_depense BETWEEN ? AND ? THEN montant ELSE 0 END) as depenses_mois', [
                    $monthStart->copy()->startOfDay(),
                    $monthEnd->copy()->endOfDay(),
                ])
                ->selectRaw('SUM(montant) as depenses_annee')
                ->first();

            return [
                'depenses_mois' => (float) ($row->depenses_mois ?? 0),
                'depenses_annee' => (float) ($row->depenses_annee ?? 0),
            ];
        } catch (\Throwable $e) {
            return [
                'depenses_mois' => 0,
                'depenses_annee' => 0,
            ];
        }
    }

    /**
     * Get appointments for today.
     */
    public static function getRDVToday()
    {
        $today = today()->toDateString();

        return Cache::remember("dashboard:rdv-today:{$today}", now()->addMinutes(1), static function () use ($today) {
            return RendezVous::with(['patient:id,nom,prenom,telephone', 'medecin:id,nom,prenom,specialite'])
                ->whereDate('date_heure', $today)
                ->orderBy('date_heure')
                ->get();
        });
    }

    /**
     * Get upcoming appointments.
     */
    public static function getUpcomingRDV($days = 7)
    {
        $start = today()->startOfDay();
        $end = today()->addDays((int) $days)->endOfDay();
        $cacheKey = sprintf('dashboard:upcoming-rdv:%s:%s:%d', $start->toDateString(), $end->toDateString(), (int) $days);

        return Cache::remember($cacheKey, now()->addMinutes(1), static function () use ($start, $end) {
            return RendezVous::with(['patient:id,nom,prenom,telephone', 'medecin:id,nom,prenom,specialite'])
                ->whereBetween('date_heure', [$start, $end])
                ->orderBy('date_heure')
                ->limit(10)
                ->get();
        });
    }

    /**
     * Get recent patients.
     */
    public static function getRecentPatients($limit = 5)
    {
        return Patient::query()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get financial summary.
     */
    public static function getFinancialSummary($month = null, $year = null): array
    {
        $month = (int) ($month ?? today()->month);
        $year = (int) ($year ?? today()->year);
        $cacheKey = "dashboard:financial-summary:{$year}:{$month}";

        return Cache::remember($cacheKey, now()->addMinutes(3), static function () use ($month, $year): array {
            try {
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                $depenses = self::safeDepenseQuery($month, $year, 'montant');

                $groupedByStatus = Facture::query()
                    ->whereBetween('date_facture', [$startDate, $endDate])
                    ->selectRaw('statut, SUM(montant_total) as total')
                    ->groupBy('statut')
                    ->get();

                $revenus = (float) $groupedByStatus->sum('total');
                $impayees = (float) $groupedByStatus
                    ->reject(fn ($row) => in_array((string) $row->statut, self::FACTURE_STATUT_PAYEE, true))
                    ->sum('total');

                return [
                    'revenus' => $revenus,
                    'depenses' => $depenses,
                    'benefice' => $revenus - $depenses,
                    'factures_impayees' => $impayees,
                ];
            } catch (\Throwable $e) {
                return [
                    'revenus' => 0,
                    'depenses' => 0,
                    'benefice' => 0,
                    'factures_impayees' => 0,
                ];
            }
        });
    }

    /**
     * Get monthly revenue chart data.
     */
    public static function getMonthlyRevenueChart($year = null): array
    {
        $year = (int) ($year ?? today()->year);
        $cacheKey = "dashboard:monthly-revenue:{$year}";

        return Cache::remember($cacheKey, now()->addMinutes(5), static function () use ($year): array {
            try {
                $monthlyTotals = Facture::query()
                    ->whereYear('date_facture', $year)
                    ->selectRaw('MONTH(date_facture) as month, SUM(montant_total) as total')
                    ->groupByRaw('MONTH(date_facture)')
                    ->pluck('total', 'month');

                $data = [];
                for ($month = 1; $month <= 12; $month++) {
                    $data[] = [
                        'mois' => Carbon::createFromDate($year, $month, 1)->format('M'),
                        'montant' => (float) ($monthlyTotals[$month] ?? 0),
                    ];
                }

                return $data;
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    /**
     * Get monthly expenses chart data.
     */
    public static function getMonthlyExpensesChart($year = null): array
    {
        $year = (int) ($year ?? today()->year);
        $cacheKey = "dashboard:monthly-expenses:{$year}";

        return Cache::remember($cacheKey, now()->addMinutes(5), static function () use ($year): array {
            try {
                $monthlyTotals = Depense::query()
                    ->whereYear('date_depense', $year)
                    ->selectRaw('MONTH(date_depense) as month, SUM(montant) as total')
                    ->groupByRaw('MONTH(date_depense)')
                    ->pluck('total', 'month');

                $data = [];
                for ($month = 1; $month <= 12; $month++) {
                    $data[] = [
                        'mois' => Carbon::createFromDate($year, $month, 1)->format('M'),
                        'montant' => (float) ($monthlyTotals[$month] ?? 0),
                    ];
                }

                return $data;
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    /**
     * Get alerts and notifications.
     */
    public static function getAlerts(): array
    {
        $cacheKey = 'dashboard:alerts:' . today()->toDateString();

        return Cache::remember($cacheKey, now()->addMinutes(2), static function (): array {
            try {
                $alerts = [];

                $latePatients = RendezVous::query()
                    ->whereDate('date_heure', today())
                    ->where('date_heure', '<', now()->subMinutes(15))
                    ->whereIn('statut', ['a_venir', 'en_attente'])
                    ->count();

                if ($latePatients > 0) {
                    $alerts[] = [
                        'type' => 'warning',
                        'message' => $latePatients . ' ' . ($latePatients > 1 ? "patients en retard en salle d'attente" : "patient en retard en salle d'attente"),
                        'icon' => 'fa-user-clock',
                        'color' => 'warning',
                        'route' => route('agenda.waiting_room'),
                    ];
                }

                $impayees = Facture::query()
                    ->whereNotIn('statut', self::FACTURE_STATUT_PAYEE)
                    ->count();

                if ($impayees > 0) {
                    $alerts[] = [
                        'type' => 'warning',
                        'message' => $impayees . ' ' . ($impayees > 1 ? "factures impay\u{00E9}es" : "facture impay\u{00E9}e"),
                        'icon' => 'fa-file-invoice',
                        'color' => 'warning',
                        'route' => route('factures.index'),
                    ];
                }

                $annules = RendezVous::query()
                    ->whereDate('date_heure', '>=', today())
                    ->whereIn('statut', self::RDV_STATUT_ANNULE)
                    ->count();

                if ($annules > 0) {
                    $alerts[] = [
                        'type' => 'info',
                        'message' => $annules . ' ' . ($annules > 1 ? "rendez-vous annul\u{00E9}s" : "rendez-vous annul\u{00E9}"),
                        'icon' => 'fa-calendar',
                        'color' => 'info',
                        'route' => route('rendezvous.index'),
                    ];
                }

                $labResults = ResultatExamen::query()
                    ->whereDate('created_at', today())
                    ->count();

                if ($labResults > 0) {
                    $alerts[] = [
                        'type' => 'info',
                        'message' => $labResults . ' ' . ($labResults > 1 ? "r\u{00E9}sultats de laboratoire re\u{00E7}us aujourd'hui" : "r\u{00E9}sultat de laboratoire re\u{00E7}u aujourd'hui"),
                        'icon' => 'fa-flask-vial',
                        'color' => 'info',
                        'route' => route('examens.index'),
                    ];
                }

                $expiredOrdonnances = Ordonnance::query()
                    ->whereNotNull('date_expiration')
                    ->whereDate('date_expiration', '<=', today())
                    ->count();

                if ($expiredOrdonnances > 0) {
                    $alerts[] = [
                        'type' => 'warning',
                        'message' => $expiredOrdonnances . ' ' . ($expiredOrdonnances > 1 ? "ordonnances expir\u{00E9}es" : "ordonnance expir\u{00E9}e"),
                        'icon' => 'fa-file-prescription',
                        'color' => 'warning',
                        'route' => route('ordonnances.index'),
                    ];
                }

                return $alerts;
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public static function getConsultationDailyChart(int $days = 7): array
    {
        $days = max(5, $days);
        $start = today()->subDays($days - 1)->startOfDay();
        $cacheKey = 'dashboard:consultation-daily:' . $start->toDateString() . ':' . $days;

        return Cache::remember($cacheKey, now()->addMinutes(5), static function () use ($days, $start): array {
            try {
                $counts = Consultation::query()
                    ->whereBetween('date_consultation', [$start, today()->endOfDay()])
                    ->selectRaw('DATE(date_consultation) as day, COUNT(*) as total')
                    ->groupByRaw('DATE(date_consultation)')
                    ->pluck('total', 'day');

                $data = [];
                for ($offset = 0; $offset < $days; $offset++) {
                    $date = $start->copy()->addDays($offset);
                    $key = $date->toDateString();
                    $data[] = [
                        'label' => $date->translatedFormat('D'),
                        'value' => (int) ($counts[$key] ?? 0),
                    ];
                }

                return $data;
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public static function getPatientEvolutionChart(int $months = 6): array
    {
        $months = max(3, $months);
        $start = today()->copy()->subMonths($months - 1)->startOfMonth();
        $cacheKey = 'dashboard:patient-evolution:' . $start->toDateString() . ':' . $months;

        return Cache::remember($cacheKey, now()->addMinutes(5), static function () use ($months, $start): array {
            try {
                $counts = Patient::query()
                    ->where('created_at', '>=', $start)
                    ->selectRaw('YEAR(created_at) as year_value, MONTH(created_at) as month_value, COUNT(*) as total')
                    ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                    ->get()
                    ->mapWithKeys(function ($row) {
                        return [sprintf('%04d-%02d', $row->year_value, $row->month_value) => (int) $row->total];
                    });

                $data = [];
                for ($offset = 0; $offset < $months; $offset++) {
                    $date = $start->copy()->addMonths($offset);
                    $key = $date->format('Y-m');
                    $data[] = [
                        'label' => $date->translatedFormat('M'),
                        'value' => (int) ($counts[$key] ?? 0),
                    ];
                }

                return $data;
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public static function getMedecinActivity(): array
    {
        $cacheKey = 'dashboard:medecin-activity:' . today()->toDateString();

        return Cache::remember($cacheKey, now()->addMinutes(2), static function (): array {
            try {
                $medecins = Medecin::query()
                    ->orderBy('nom')
                    ->orderBy('prenom')
                    ->get(['id', 'civilite', 'nom', 'prenom', 'specialite', 'statut', 'photo_path']);

                $todayRdv = RendezVous::query()
                    ->whereDate('date_heure', today())
                    ->get(['medecin_id', 'statut', 'date_heure'])
                    ->groupBy('medecin_id');

                $items = $medecins->map(function (Medecin $medecin) use ($todayRdv) {
                    $rdv = $todayRdv->get($medecin->id, collect());
                    $status = 'disponible';

                    if (($medecin->statut ?? '') !== 'actif') {
                        $status = 'absent';
                    } elseif ($rdv->contains(fn (RendezVous $item) => $item->statut === 'en_soins')) {
                        $status = 'en_consultation';
                    }

                    $nextSlot = optional($rdv->sortBy('date_heure')->first())->date_heure?->format('H:i');

                    return [
                        'id' => $medecin->id,
                        'name' => trim(($medecin->prenom ?? '') . ' ' . ($medecin->nom ?? '')),
                        'specialite' => $medecin->specialite_formatee,
                        'avatar_url' => $medecin->avatar_url,
                        'status' => $status,
                        'next_slot' => $nextSlot,
                    ];
                })->values();

                return [
                    'disponible' => $items->where('status', 'disponible')->count(),
                    'en_consultation' => $items->where('status', 'en_consultation')->count(),
                    'absent' => $items->where('status', 'absent')->count(),
                    'items' => $items->take(6),
                ];
            } catch (\Throwable $e) {
                return [
                    'disponible' => 0,
                    'en_consultation' => 0,
                    'absent' => 0,
                    'items' => collect(),
                ];
            }
        });
    }
}
