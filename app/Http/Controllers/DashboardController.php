<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\RendezVous;
use App\Models\Consultation;
use App\Models\Facture;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get total patients count
     */
    public function patients_count()
    {
        return Patient::count();
    }

    /**
     * Get today's appointments count
     */
    public function rdv_today_count()
    {
        return RendezVous::whereDate('date_rdv', today())->count();
    }

    /**
     * Get active doctors count
     */
    public function medecins_count()
    {
        return Medecin::count();
    }

    /**
     * Get current month revenues
     */
    public function revenus_mois()
    {
        try {
            return Facture::whereMonth('date_facture', now()->month)
                ->whereYear('date_facture', now()->year)
                ->sum('montant_total');
        } catch (\Exception $e) {
            return 0; // Return 0 if table doesn't exist or query fails
        }
    }

    /**
     * Get unpaid bills count
     */
    public function factures_impayees()
    {
        try {
            return Facture::where('statut', '!=', 'payée')
                ->whereMonth('date_facture', now()->month)
                ->whereYear('date_facture', now()->year)
                ->count();
        } catch (\Exception $e) {
            return 0; // Return 0 if table doesn't exist or query fails
        }
    }

    public function index()
    {
        $aujourdhui = Carbon::today()->toDateString();
        $moisCourant = now()->month;
        $anneeCourante = now()->year;

        // Métriques de base using optimized methods
        $totalMedecins = $this->medecins_count();
        $totalPatients = $this->patients_count();

        // Nouveaux patients ce mois
        $nouveauxPatientsMois = Patient::whereMonth('created_at', $moisCourant)
            ->whereYear('created_at', $anneeCourante)
            ->count();

        // Rendez-vous aujourd'hui
        $rdvAujourdhui = $this->rdv_today_count();

        // Rendez-vous annulés ce mois
        $rdvAnnulesMois = RendezVous::where('statut', 'annule')
            ->whereMonth('date_rdv', $moisCourant)
            ->whereYear('date_rdv', $anneeCourante)
            ->count();

        // Consultations ce mois
        $consultationsMois = Consultation::whereMonth('date_consultation', $moisCourant)
            ->whereYear('date_consultation', $anneeCourante)
            ->count();

        // Données de facturation
        $revenusMois = $this->revenus_mois();

        $facturesImpayees = $this->factures_impayees();

        // Statistiques de croissance
        $moisPrecedent = now()->subMonth()->month;
        $anneePrecedente = now()->subMonth()->year;

        $patientsMoisPrecedent = Patient::whereMonth('created_at', $moisPrecedent)
            ->whereYear('created_at', $anneePrecedente)
            ->count();

        $consultationsMoisPrecedent = Consultation::whereMonth('date_consultation', $moisPrecedent)
            ->whereYear('date_consultation', $anneePrecedente)
            ->count();

        $croissancePatients = $patientsMoisPrecedent > 0
            ? (($nouveauxPatientsMois - $patientsMoisPrecedent) / $patientsMoisPrecedent) * 100
            : 0;

        $croissanceConsultations = $consultationsMoisPrecedent > 0
            ? (($consultationsMois - $consultationsMoisPrecedent) / $consultationsMoisPrecedent) * 100
            : 0;

        // Données pour les sections du dashboard
        $derniersMedecins = Medecin::latest()->take(3)->get();
        $derniersPatients = Patient::latest()->take(5)->get();
        $tousMedecins = Medecin::orderBy('nom')->get();

        $prochainsRdv = RendezVous::with(['patient', 'medecin'])
            ->whereDate('date_heure', '>=', $aujourdhui)
            ->where('statut', '!=', 'annule')
            ->orderBy('date_heure')
            ->take(5)
            ->get();

        $rdvToday = RendezVous::with(['patient', 'medecin'])
            ->whereDate('date_heure', $aujourdhui)
            ->orderBy('date_heure')
            ->get();

        // Consultations récentes
        $consultationsRecentes = Consultation::with(['patient', 'medecin'])
            ->latest()
            ->take(5)
            ->get();

        // Alertes et notifications
        $alertes = [];

        // Alerte pour rendez-vous aujourd'hui
        if ($rdvAujourdhui > 0) {
            $alertes[] = [
                'type' => 'info',
                'icon' => 'calendar',
                'message' => "Vous avez {$rdvAujourdhui} rendez-vous programmé(s) aujourd'hui.",
                'action' => route('agenda.index')
            ];
        }

        // Alerte pour rendez-vous dans les prochaines 24h
        $rdvProchaines24h = RendezVous::whereBetween('date_heure', [now(), now()->addHours(24)])
            ->where('statut', '!=', 'annule')
            ->count();
        if ($rdvProchaines24h > 0) {
            $alertes[] = [
                'type' => 'primary',
                'icon' => 'clock',
                'message' => "{$rdvProchaines24h} rendez-vous dans les prochaines 24 heures.",
                'action' => route('agenda.index')
            ];
        }

        // Alerte pour factures impayées
        if ($facturesImpayees > 0) {
            $alertes[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'message' => "{$facturesImpayees} facture(s) en attente de paiement ce mois.",
                'action' => route('factures.index')
            ];
        }

        // Alerte pour factures en retard (> 30 jours)
        try {
            $facturesEnRetard = Facture::where('statut', '!=', 'payée')
                ->where('date_facture', '<', now()->subDays(30))
                ->count();
            if ($facturesEnRetard > 0) {
                $alertes[] = [
                    'type' => 'danger',
                    'icon' => 'exclamation-circle',
                    'message' => "{$facturesEnRetard} facture(s) en retard de paiement (> 30 jours).",
                    'action' => route('factures.index')
                ];
            }
        } catch (\Exception $e) {
            // Skip if table doesn't exist
        }

        // Alerte pour rendez-vous annulés
        if ($rdvAnnulesMois > 0) {
            $alertes[] = [
                'type' => 'danger',
                'icon' => 'times-circle',
                'message' => "{$rdvAnnulesMois} rendez-vous annulé(s) ce mois.",
                'action' => route('rendezvous.index')
            ];
        }

        // Alerte pour nouveaux patients aujourd'hui
        $nouveauxPatientsAujourdhui = Patient::whereDate('created_at', $aujourdhui)->count();
        if ($nouveauxPatientsAujourdhui > 0) {
            $alertes[] = [
                'type' => 'success',
                'icon' => 'user-plus',
                'message' => "{$nouveauxPatientsAujourdhui} nouveau(x) patient(s) enregistré(s) aujourd'hui.",
                'action' => route('patients.index')
            ];
        }

        // Alerte pour médecins sans rendez-vous aujourd'hui
        $medecinsSansRdv = Medecin::whereDoesntHave('rendezVous', function($query) use ($aujourdhui) {
            $query->whereDate('date_heure', $aujourdhui)
                  ->where('statut', '!=', 'annule');
        })->count();
        if ($medecinsSansRdv > 0 && $totalMedecins > 0) {
            $alertes[] = [
                'type' => 'secondary',
                'icon' => 'user-md',
                'message' => "{$medecinsSansRdv} médecin(s) sans rendez-vous aujourd'hui.",
                'action' => route('medecins.index')
            ];
        }

        // Données pour les graphiques
        $patientGrowthData = $this->getPatientGrowthData();
        $revenueData = $this->getRevenueData();
        $appointmentStats = $this->getAppointmentStats();

        // Préparer les données
        $data = [
            // Métriques principales
            'total_patients' => $totalPatients,
            'total_medecins' => $totalMedecins,
            'total_consultations_mois' => $consultationsMois,
            'rdv_aujourdhui' => $rdvAujourdhui,
            'nouveaux_patients_mois' => $nouveauxPatientsMois,
            'rdv_annules_mois' => $rdvAnnulesMois,
            'revenus_mois' => $revenusMois,
            'factures_impayees' => $facturesImpayees,

            // Statistiques de croissance
            'croissance_patients' => round($croissancePatients, 1),
            'croissance_consultations' => round($croissanceConsultations, 1),

            // Données temporelles
            'date_aujourdhui' => $aujourdhui,
            'mois_courant' => now()->format('F Y'),

            // Données pour les graphiques
            'patient_growth_data' => $patientGrowthData,
            'revenue_data' => $revenueData,
            'appointment_stats' => $appointmentStats,

            // Listes et données détaillées
            'derniers_patients' => $derniersPatients,
            'derniers_medecins' => $derniersMedecins,
            'tous_medecins' => $tousMedecins,
            'prochains_rdv' => $prochainsRdv,
            'rdv_today' => $rdvToday,
            'consultations_recentes' => $consultationsRecentes,
            'alertes' => $alertes,
        ];

        return view('dashboard.index', $data);
    }

    /**
     * Get patient growth data for charts (last 6 months)
     */
    public function getPatientGrowthData()
    {
        $data = Patient::where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        $result = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->month;
            $result[] = $data[$month] ?? 0;
        }

        return $result;
    }

    /**
     * Get revenue data for charts (current year monthly)
     */
    public function getRevenueData()
    {
        try {
            $data = Facture::whereYear('created_at', date('Y'))
                ->where('statut', 'payée')
                ->selectRaw('MONTH(created_at) as month, SUM(montant_total) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('total', 'month')
                ->toArray();
        } catch (\Exception $e) {
            $data = []; // Return empty array if table doesn't exist
        }

        // Fill missing months with 0
        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $result[] = $data[$month] ?? 0;
        }

        return $result;
    }

    /**
     * Get appointment statistics for pie chart
     */
    public function getAppointmentStats()
    {
        $stats = RendezVous::whereYear('created_at', date('Y'))
            ->selectRaw('statut, COUNT(*) as count')
            ->groupBy('statut')
            ->get()
            ->pluck('count', 'statut')
            ->toArray();

        return [
            'confirmé' => $stats['confirmé'] ?? 0,
            'programmé' => $stats['programmé'] ?? 0,
            'annulé' => $stats['annulé'] ?? 0,
            'terminé' => $stats['terminé'] ?? 0,
        ];
    }

    /**
     * Afficher le tableau de bord administrateur
     */
    public function admin()
    {
        // Statistiques spécifiques à l'admin
        $stats = [
            'total_patients' => Patient::count(),
            'total_medecins' => Medecin::count(),
            'total_rendezvous' => RendezVous::count(),
            'rendezvous_aujourdhui' => RendezVous::whereDate('date_rdv', today())->count(),
            'total_consultations' => Consultation::count(),
            'consultations_mois' => Consultation::whereMonth('date_consultation', now()->month)->count(),
            'revenus_mois' => $this->revenus_mois(),
        ];

        return redirect()->route('admin.dashboard');
    }
}
