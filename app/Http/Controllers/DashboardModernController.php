<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Facture;
use App\Models\Ordonnance;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardModernController extends Controller
{
    /**
     * Affiche le tableau de bord moderne
     */
    public function index()
    {
        $stats = DashboardService::getStatistics();
        $rdvToday = $this->decorateRendezVousCollection(DashboardService::getRDVToday());
        $urgentConsultations = $this->extractUrgentConsultations($rdvToday);
        $upcomingRDV = $this->decorateRendezVousCollection(DashboardService::getUpcomingRDV(7));
        $financialSummary = DashboardService::getFinancialSummary();
        $monthlyRevenue = DashboardService::getMonthlyRevenueChart();
        $consultationDaily = DashboardService::getConsultationDailyChart();
        $patientEvolution = DashboardService::getPatientEvolutionChart();
        $medecinActivity = $this->prepareMedecinActivity(DashboardService::getMedecinActivity());
        $alerts = $this->prepareAlerts(DashboardService::getAlerts());
        $recentActivities = Cache::remember(
            'dashboard:recent-activities',
            now()->addMinutes(1),
            fn () => $this->buildRecentActivities()
        );
        $actionCenter = Cache::remember(
            'dashboard:action-center:' . (auth()->id() ?? 'guest'),
            now()->addMinutes(1),
            fn () => $this->buildActionCenter()
        );
        $quickActions = $this->buildQuickActions();
        $revenuMois = number_format((float) ($stats['revenus_mois'] ?? 0), 0, ',', ' ');
        $consultationChartMax = max(1, (int) (collect($consultationDaily)->max('value') ?? 0));
        $revenueChartMax = max(1, (int) round((float) (collect($monthlyRevenue)->max('montant') ?? 0)));
        $patientChartMax = max(1, (int) (collect($patientEvolution)->max('value') ?? 0));
        $medecinsActifs = (int) (($medecinActivity['disponible'] ?? 0) + ($medecinActivity['en_consultation'] ?? 0));
        $revenuObjectif = 15000;
        $rdvObjectif = 30;
        $patientsObjectif = 40;
        $revenuProgress = min(100, (int) round(((float) ($stats['revenus_mois'] ?? 0) / max(1, $revenuObjectif)) * 100));
        $rdvProgress = min(100, (int) round(((float) ($stats['rdv_semaine'] ?? 0) / max(1, $rdvObjectif)) * 100));
        $patientsProgress = min(100, (int) round(((float) ($stats['patients_nouveaux_mois'] ?? 0) / max(1, $patientsObjectif)) * 100));

        return view('dashboard.modern', [
            'stats' => $stats,
            'rdvToday' => $rdvToday,
            'urgentConsultations' => $urgentConsultations,
            'upcomingRDV' => $upcomingRDV,
            'financialSummary' => $financialSummary,
            'monthlyRevenue' => $monthlyRevenue,
            'consultationDaily' => $consultationDaily,
            'patientEvolution' => $patientEvolution,
            'medecinActivity' => $medecinActivity,
            'alerts' => $alerts,
            'recentActivities' => $recentActivities,
            'actionCenter' => $actionCenter,
            'quickActions' => $quickActions,
            'revenuMois' => $revenuMois,
            'consultationChartMax' => $consultationChartMax,
            'revenueChartMax' => $revenueChartMax,
            'patientChartMax' => $patientChartMax,
            'medecinsActifs' => $medecinsActifs,
            'revenuObjectif' => $revenuObjectif,
            'rdvObjectif' => $rdvObjectif,
            'patientsObjectif' => $patientsObjectif,
            'revenuProgress' => $revenuProgress,
            'rdvProgress' => $rdvProgress,
            'patientsProgress' => $patientsProgress,
        ]);
    }

    /**
     * Retourne les donnees de statistiques en JSON
     */
    public function getStats()
    {
        return response()->json(DashboardService::getStatistics());
    }

    /**
     * Retourne les donnees du graphique de revenus
     */
    public function getRevenueData(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        if ($year < 2000 || $year > 2100) {
            $year = (int) now()->year;
        }

        return response()->json(DashboardService::getMonthlyRevenueChart($year));
    }

    public function getUrgentConsultations()
    {
        $urgentConsultations = $this->extractUrgentConsultations(
            $this->decorateRendezVousCollection(DashboardService::getRDVToday())
        );

        return response()->json([
            'count' => $urgentConsultations->count(),
            'items' => $urgentConsultations->map(function (RendezVous $rdv) {
                return [
                    'id' => $rdv->id,
                    'patient_name' => $rdv->dashboard_patient_name,
                    'patient_initials' => $rdv->dashboard_patient_initials,
                    'patient_avatar' => $rdv->dashboard_patient_avatar,
                    'time' => optional($rdv->date_rdv ?? $rdv->date_heure)->format('H:i') ?? '-',
                    'medecin' => 'Dr ' . (trim(($rdv->medecin->prenom ?? '') . ' ' . ($rdv->medecin->nom ?? '')) ?: 'Non assigne'),
                    'status' => $rdv->dashboard_status,
                    'patient_url' => $rdv->dashboard_patient_url,
                ];
            })->values(),
            'all_url' => route('rendezvous.index', [
                'date' => now()->toDateString(),
                'type' => 'urgence',
            ]),
        ]);
    }

    private function buildRecentActivities()
    {
        $patients = Patient::query()
            ->latest()
            ->take(5)
            ->get()
            ->map(function (Patient $patient) {
                $name = trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? '')) ?: "Patient #{$patient->id}";

                return [
                    'at' => $patient->created_at,
                    'icon' => 'fa-user-plus',
                    'title' => 'Nouveau patient',
                    'description' => $name,
                    'meta' => $patient->numero_dossier ?? "ID {$patient->id}",
                    'url' => route('patients.show', $patient),
                ];
            });

        $rendezvous = RendezVous::query()
            ->with('patient:id,nom,prenom')
            ->latest()
            ->take(5)
            ->get()
            ->map(function (RendezVous $rdv) {
                $name = trim(($rdv->patient->prenom ?? '') . ' ' . ($rdv->patient->nom ?? '')) ?: "Patient #{$rdv->patient_id}";
                $rdvAt = $rdv->date_heure ? $rdv->date_heure->format('d/m H:i') : '-';

                return [
                    'at' => $rdv->created_at,
                    'icon' => 'fa-calendar-plus',
                    'title' => 'Nouveau rendez-vous',
                    'description' => $name,
                    'meta' => "Prevu le {$rdvAt}",
                    'url' => route('rendezvous.show', $rdv),
                ];
            });

        $factures = Facture::query()
            ->with('patient:id,nom,prenom')
            ->latest()
            ->take(5)
            ->get()
            ->map(function (Facture $facture) {
                $name = trim(($facture->patient->prenom ?? '') . ' ' . ($facture->patient->nom ?? '')) ?: 'Patient inconnu';

                return [
                    'at' => $facture->created_at,
                    'icon' => 'fa-file-invoice-dollar',
                    'title' => 'Nouvelle facture',
                    'description' => $facture->numero_facture ?? "Facture #{$facture->id}",
                    'meta' => $name,
                    'url' => route('factures.show', $facture),
                ];
            });

        $consultations = Consultation::query()
            ->with('patient:id,nom,prenom')
            ->latest()
            ->take(5)
            ->get()
            ->map(function (Consultation $consultation) {
                $name = trim(($consultation->patient->prenom ?? '') . ' ' . ($consultation->patient->nom ?? '')) ?: "Patient #{$consultation->patient_id}";

                return [
                    'at' => $consultation->created_at,
                    'icon' => 'fa-stethoscope',
                    'title' => 'Consultation enregistree',
                    'description' => $name,
                    'meta' => "Ref #{$consultation->id}",
                    'url' => route('consultations.show', $consultation),
                ];
            });

        return $patients
            ->merge($rendezvous)
            ->merge($factures)
            ->merge($consultations)
            ->sortByDesc('at')
            ->take(10)
            ->values()
            ->map(function (array $activity) {
                $activity['time'] = optional($activity['at'])->format('d/m H:i') ?? '-';

                return $activity;
            });
    }

    private function buildActionCenter()
    {
        $unpaidStatuses = ['impayee', 'impayee', "impay\u{00C3}\u{00A9}e", 'en_attente', 'brouillon'];
        $pendingRdvStatuses = ['en_attente', 'programme', 'programme', "programm\u{00C3}\u{00A9}", 'a_confirmer'];

        $impayeesCount = Facture::query()->whereIn('statut', $unpaidStatuses)->count();
        $impayeesMontant = (float) Facture::query()->whereIn('statut', $unpaidStatuses)->sum('montant_total');

        $rdvAConfirmer = RendezVous::query()
            ->whereBetween('date_heure', [now()->startOfDay(), now()->addDays(1)->endOfDay()])
            ->whereIn('statut', $pendingRdvStatuses)
            ->count();

        $ordonnancesARenouveler = Ordonnance::query()
            ->whereNotNull('date_expiration')
            ->whereDate('date_expiration', '<=', now()->addDays(7))
            ->whereDate('date_expiration', '>=', now()->startOfDay())
            ->count();

        $dossiersIncomplets = Patient::query()
            ->where(function ($query) {
                $query->whereNull('telephone')->orWhere('telephone', '');
            })
            ->orWhere(function ($query) {
                $query->whereNull('email')->orWhere('email', '');
            })
            ->count();

        $items = collect([
            [
                'title' => 'Factures a relancer',
                'subtitle' => number_format($impayeesMontant, 0, ',', ' ') . ' DH a recuperer',
                'count' => $impayeesCount,
                'icon' => 'fa-file-invoice-dollar',
                'badge' => 'Urgent',
                'tone' => 'danger',
                'route' => route('factures.index'),
                'module' => 'facturation',
            ],
            [
                'title' => 'RDV a confirmer',
                'subtitle' => 'Prochaines 24h',
                'count' => $rdvAConfirmer,
                'icon' => 'fa-calendar-check',
                'badge' => "Aujourd’hui",
                'tone' => 'info',
                'route' => route('rendezvous.index'),
                'module' => 'planning',
            ],
            [
                'title' => 'Ordonnances a renouveler',
                'subtitle' => 'Expiration sous 7 jours',
                'count' => $ordonnancesARenouveler,
                'icon' => 'fa-file-prescription',
                'badge' => 'Cette semaine',
                'tone' => 'warning',
                'route' => route('ordonnances.index'),
                'module' => 'pharmacie',
            ],
            [
                'title' => 'Dossiers incomplets',
                'subtitle' => 'Telephone ou email manquant',
                'count' => $dossiersIncomplets,
                'icon' => 'fa-user-pen',
                'badge' => 'A verifier',
                'tone' => 'neutral',
                'route' => route('patients.index'),
                'module' => 'patients',
            ],
        ]);

        $user = auth()->user();

        return $items
            ->filter(fn (array $item) => !$user || $user->hasModuleAccess((string) ($item['module'] ?? '')))
            ->values();
    }

    private function buildQuickActions(): array
    {
        $actions = [
            [
                'label' => 'Nouveau patient',
                'icon' => 'fa-user-plus',
                'route' => route('patients.create'),
                'tone' => 'blue',
                'module' => 'patients',
            ],
            [
                'label' => 'Nouvelle consultation',
                'icon' => 'fa-stethoscope',
                'route' => route('consultations.create'),
                'tone' => 'blue',
                'module' => 'consultations',
            ],
            [
                'label' => 'Ouvrir agenda',
                'icon' => 'fa-calendar-days',
                'route' => route('agenda.index'),
                'tone' => 'green',
                'module' => 'planning',
            ],
            [
                'label' => 'Envoyer SMS patient',
                'icon' => 'fa-comment-sms',
                'route' => route('sms.create'),
                'tone' => 'green',
                'module' => 'sms',
            ],
            [
                'label' => 'Ajouter ordonnance',
                'icon' => 'fa-file-prescription',
                'route' => route('ordonnances.create'),
                'tone' => 'purple',
                'module' => 'pharmacie',
            ],
            [
                'label' => 'Ajouter document patient',
                'icon' => 'fa-file-arrow-up',
                'route' => route('documents.upload'),
                'tone' => 'amber',
                'module' => 'documents',
            ],
        ];

        $user = auth()->user();

        return array_values(array_filter($actions, fn (array $action) => !$user || $user->hasModuleAccess((string) ($action['module'] ?? ''))));
    }

    private function decorateRendezVousCollection(Collection $items): Collection
    {
        return $items->map(function (RendezVous $rdv) {
            $fullName = trim(($rdv->patient->prenom ?? '') . ' ' . ($rdv->patient->nom ?? ''));
            $displayName = $fullName !== '' ? $fullName : ($rdv->patient->nom_complet ?? 'Patient');
            $initials = collect(preg_split('/\s+/', $displayName) ?: [])
                ->filter()
                ->take(2)
                ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8'))
                ->implode('');

            $photo = trim((string) ($rdv->patient->photo ?? ''));
            $rdv->dashboard_patient_name = $displayName;
            $rdv->dashboard_patient_initials = $initials !== '' ? $initials : 'PT';
            $rdv->dashboard_patient_avatar = $photo !== '' ? asset('storage/' . ltrim($photo, '/')) : null;
            $rdv->dashboard_status = $this->resolveStatusPresentation($rdv->statut ?? 'a_venir');
            $rdv->dashboard_patient_url = route('patients.show', $rdv->patient_id);
            $rdv->dashboard_sms_url = route('sms.create', ['patient_id' => $rdv->patient_id]);
            $rdv->dashboard_consultation_url = route('consultations.create', ['patient_id' => $rdv->patient_id]);
            $rdv->dashboard_documents_url = route('documents.upload', ['patient_id' => $rdv->patient_id]);
            $rdv->dashboard_ordonnance_url = route('ordonnances.create', ['patient_id' => $rdv->patient_id]);
            $rdv->dashboard_edit_url = route('rendezvous.edit', $rdv);
            $rdv->dashboard_start_url = route('rendezvous.update_status', $rdv->id);

            return $rdv;
        });
    }

    private function extractUrgentConsultations(Collection $items): Collection
    {
        return $items
            ->filter(fn (RendezVous $rdv) => $this->isUrgentRendezVous($rdv))
            ->sortBy('date_heure')
            ->take(5)
            ->values();
    }

    private function isUrgentRendezVous(RendezVous $rdv): bool
    {
        $type = mb_strtolower((string) ($rdv->type ?? ''), 'UTF-8');
        $motif = mb_strtolower((string) ($rdv->motif ?? ''), 'UTF-8');

        return str_contains($type, 'urgence') || str_contains($motif, 'urgence');
    }

    private function resolveStatusPresentation(string $status): array
    {
        return match ($status) {
            'en_attente' => ['label' => 'En attente', 'class' => 'status-waiting'],
            'en_soins' => ['label' => 'En consultation', 'class' => 'status-active'],
            'vu' => ['label' => 'Termine', 'class' => 'status-done'],
            'absent' => ['label' => 'Absent', 'class' => 'status-missed'],
            'annule' => ['label' => 'Annule', 'class' => 'status-neutral'],
            default => ['label' => 'A venir', 'class' => 'status-upcoming'],
        };
    }

    private function prepareMedecinActivity(array $medecinActivity): array
    {
        $items = collect($medecinActivity['items'] ?? [])
            ->map(function (array $doctor) {
                $statusPresentation = $this->resolveDoctorStatusPresentation((string) ($doctor['status'] ?? 'disponible'));
                $initials = collect(preg_split('/\s+/', (string) ($doctor['name'] ?? '')) ?: [])
                    ->filter()
                    ->take(2)
                    ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8'))
                    ->implode('');

                $doctor['display_status'] = $statusPresentation;
                $doctor['display_initials'] = $initials !== '' ? $initials : 'DR';
                $doctor['display_specialite'] = trim((string) ($doctor['specialite'] ?? '')) !== ''
                    ? $doctor['specialite']
                    : 'Generaliste';

                return $doctor;
            })
            ->values();

        $medecinActivity['items'] = $items;
        $medecinActivity['items_count'] = $items->count();

        return $medecinActivity;
    }

    private function prepareAlerts(array $alerts): array
    {
        return collect($alerts)
            ->map(function (array $alert) {
                $type = (string) ($alert['type'] ?? 'info');
                $alert['tile_class'] = $type === 'warning' ? 'alert-warning' : 'alert-info';

                return $alert;
            })
            ->values()
            ->all();
    }

    private function resolveDoctorStatusPresentation(string $status): array
    {
        return match ($status) {
            'en_consultation' => ['label' => 'En consultation', 'class' => 'status-active'],
            'absent' => ['label' => 'Absent', 'class' => 'status-neutral'],
            default => ['label' => 'Disponible', 'class' => 'status-done'],
        };
    }
}
