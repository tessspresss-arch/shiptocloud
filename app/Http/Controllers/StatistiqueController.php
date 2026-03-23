<?php

namespace App\Http\Controllers;

use App\Exports\GenericTableExport;
use App\Models\Consultation;
use App\Models\Facture;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StatistiqueController extends Controller
{
    public function index(Request $request)
    {
        extract($this->buildStatisticsPayload((int) $request->get('periode', 30)));

        return view('statistiques.index', compact(
            'totalPatients',
            'nouveauxPatients',
            'totalConsultations',
            'consultationsPeriode',
            'totalRendezVous',
            'rendezVousPeriode',
            'totalFactures',
            'facturesPayees',
            'facturesPeriode',
            'statsMedecins',
            'evolutionPatients',
            'evolutionConsultations',
            'evolutionRevenus',
            'rapportSynthese',
            'kpis',
            'periode'
        ));
    }

    public function rapport(Request $request)
    {
        extract($this->buildStatisticsPayload((int) $request->get('periode', 30)));

        return view('statistiques.rapport', compact(
            'totalPatients',
            'nouveauxPatients',
            'totalConsultations',
            'consultationsPeriode',
            'totalRendezVous',
            'rendezVousPeriode',
            'totalFactures',
            'facturesPayees',
            'facturesPeriode',
            'statsMedecins',
            'rapportSynthese',
            'periode'
        ));
    }

    public function export(Request $request)
    {
        $payload = $this->buildStatisticsPayload((int) $request->get('periode', 30));

        $rows = [
            ['Periode analysee', $payload['periode'] . ' jours'],
            ['Total patients', $payload['totalPatients']],
            ['Nouveaux patients', $payload['nouveauxPatients']],
            ['Total consultations', $payload['totalConsultations']],
            ['Consultations sur la periode', $payload['consultationsPeriode']],
            ['Total rendez-vous', $payload['totalRendezVous']],
            ['Rendez-vous sur la periode', $payload['rendezVousPeriode']],
            ['Montant total facture', round((float) $payload['totalFactures'], 2)],
            ['Montant facture payee', round((float) $payload['facturesPayees'], 2)],
            ['Montant facture sur la periode', round((float) $payload['facturesPeriode'], 2)],
        ];

        foreach ($payload['statsMedecins'] as $medecin) {
            $rows[] = [
                'Consultations praticien - ' . trim($medecin->prenom . ' ' . $medecin->nom),
                (int) $medecin->consultations_count,
            ];
        }

        return Excel::download(
            new GenericTableExport($rows, ['Indicateur', 'Valeur']),
            'statistiques_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    private function buildStatisticsPayload(int $periode): array
    {
        $dateDebut = now()->subDays($periode);

        $totalPatients = Patient::count();
        $nouveauxPatients = Patient::where('created_at', '>=', $dateDebut)->count();

        $totalConsultations = Consultation::count();
        $consultationsPeriode = Consultation::where('created_at', '>=', $dateDebut)->count();

        $totalRendezVous = RendezVous::count();
        $rendezVousPeriode = RendezVous::where('created_at', '>=', $dateDebut)->count();

        $totalFactures = Facture::sum('montant_total');
        $facturesPayees = $this->paidFacturesQuery()->sum('montant_total');
        $facturesPeriode = Facture::whereDate('date_facture', '>=', $dateDebut->toDateString())->sum('montant_total');

        $statsMedecins = Medecin::withCount(['consultations' => function ($query) use ($dateDebut) {
            $query->where('created_at', '>=', $dateDebut);
        }])->get();

        return [
            'periode' => $periode,
            'totalPatients' => $totalPatients,
            'nouveauxPatients' => $nouveauxPatients,
            'totalConsultations' => $totalConsultations,
            'consultationsPeriode' => $consultationsPeriode,
            'totalRendezVous' => $totalRendezVous,
            'rendezVousPeriode' => $rendezVousPeriode,
            'totalFactures' => $totalFactures,
            'facturesPayees' => $facturesPayees,
            'facturesPeriode' => $facturesPeriode,
            'statsMedecins' => $statsMedecins,
            'evolutionPatients' => $this->getEvolutionPatients($periode),
            'evolutionConsultations' => $this->getEvolutionConsultations($periode),
            'evolutionRevenus' => $this->getEvolutionRevenus($periode),
            'rapportSynthese' => $this->getRapportSynthese(),
            'kpis' => $this->getKPIs(),
        ];
    }

    private function getEvolutionPatients(int $periode): array
    {
        $data = [];

        for ($i = $periode; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'count' => Patient::whereDate('created_at', $date->toDateString())->count(),
            ];
        }

        return $data;
    }

    private function getEvolutionConsultations(int $periode): array
    {
        $data = [];

        for ($i = $periode; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'count' => Consultation::whereDate('created_at', $date->toDateString())->count(),
            ];
        }

        return $data;
    }

    private function getEvolutionRevenus(int $periode): array
    {
        $data = [];

        for ($i = $periode; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'montant' => $this->paidFacturesQuery()
                    ->whereDate('date_facture', $date->toDateString())
                    ->sum('montant_total'),
            ];
        }

        return $data;
    }

    private function getRapportSynthese(): array
    {
        $moisCourant = now()->month;
        $anneeCourante = now()->year;
        $moisPrecedent = now()->subMonth()->month;
        $anneePrecedente = now()->subMonth()->year;

        $patientsCourant = Patient::whereMonth('created_at', $moisCourant)
            ->whereYear('created_at', $anneeCourante)
            ->count();
        $patientsPrecedent = Patient::whereMonth('created_at', $moisPrecedent)
            ->whereYear('created_at', $anneePrecedente)
            ->count();

        $consultationsCourant = Consultation::whereMonth('created_at', $moisCourant)
            ->whereYear('created_at', $anneeCourante)
            ->count();
        $consultationsPrecedent = Consultation::whereMonth('created_at', $moisPrecedent)
            ->whereYear('created_at', $anneePrecedente)
            ->count();

        $revenusCourant = $this->paidFacturesQuery()
            ->whereMonth('date_facture', $moisCourant)
            ->whereYear('date_facture', $anneeCourante)
            ->sum('montant_total');
        $revenusPrecedent = $this->paidFacturesQuery()
            ->whereMonth('date_facture', $moisPrecedent)
            ->whereYear('date_facture', $anneePrecedente)
            ->sum('montant_total');

        return [
            [
                'metric' => 'Nouveaux patients',
                'courant' => $patientsCourant,
                'precedent' => $patientsPrecedent,
                'variation' => $patientsPrecedent > 0 ? round((($patientsCourant - $patientsPrecedent) / $patientsPrecedent) * 100, 1) : 0,
                'variation_color' => $patientsCourant >= $patientsPrecedent ? 'success' : 'danger',
                'tendance' => $patientsCourant >= $patientsPrecedent ? 'up' : 'down',
            ],
            [
                'metric' => 'Consultations',
                'courant' => $consultationsCourant,
                'precedent' => $consultationsPrecedent,
                'variation' => $consultationsPrecedent > 0 ? round((($consultationsCourant - $consultationsPrecedent) / $consultationsPrecedent) * 100, 1) : 0,
                'variation_color' => $consultationsCourant >= $consultationsPrecedent ? 'success' : 'danger',
                'tendance' => $consultationsCourant >= $consultationsPrecedent ? 'up' : 'down',
            ],
            [
                'metric' => 'Revenus (DH)',
                'courant' => number_format($revenusCourant, 0),
                'precedent' => number_format($revenusPrecedent, 0),
                'variation' => $revenusPrecedent > 0 ? round((($revenusCourant - $revenusPrecedent) / $revenusPrecedent) * 100, 1) : 0,
                'variation_color' => $revenusCourant >= $revenusPrecedent ? 'success' : 'danger',
                'tendance' => $revenusCourant >= $revenusPrecedent ? 'up' : 'down',
            ],
        ];
    }

    private function getKPIs(): array
    {
        $totalPatients = Patient::count();
        $totalConsultations = Consultation::count();
        $totalRevenus = $this->paidFacturesQuery()->sum('montant_total');
        $consultationsMois = Consultation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            [
                'label' => 'Total Patients',
                'value' => number_format($totalPatients),
                'color' => 'primary',
                'trend' => '+12%',
                'trend_color' => 'success',
                'trend_icon' => 'arrow-up',
            ],
            [
                'label' => 'Consultations',
                'value' => number_format($totalConsultations),
                'color' => 'success',
                'trend' => '+8%',
                'trend_color' => 'success',
                'trend_icon' => 'arrow-up',
            ],
            [
                'label' => 'Revenus (DH)',
                'value' => number_format($totalRevenus, 0),
                'color' => 'warning',
                'trend' => '+15%',
                'trend_color' => 'success',
                'trend_icon' => 'arrow-up',
            ],
            [
                'label' => 'Ce mois',
                'value' => number_format($consultationsMois),
                'color' => 'info',
                'trend' => '+5%',
                'trend_color' => 'success',
                'trend_icon' => 'arrow-up',
            ],
        ];
    }

    private function paidFacturesQuery()
    {
        return Facture::query()->whereIn('statut', $this->paidStatusVariants());
    }

    private function paidStatusVariants(): array
    {
        return [
            'payee',
            "pay\u{00E9}e",
            'paye',
            "pay\u{00E9}",
            'reglee',
            "r\u{00E9}gl\u{00E9}e",
            'regle',
            "r\u{00E9}gl\u{00E9}",
        ];
    }
}
