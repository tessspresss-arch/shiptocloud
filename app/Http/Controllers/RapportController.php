<?php

namespace App\Http\Controllers;

use App\Exports\GenericTableExport;
use App\Models\Consultation;
use App\Models\Facture;
use App\Models\Medecin;
use App\Models\Medicament;
use App\Models\MouvementStock;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\Report;
use App\Services\Pdf\PdfBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
class RapportController extends Controller
{
    private const FACTURE_STATUT_PAYEE = [
        'payee',
        "pay\u{00E9}e",
        "pay\u{00C3}\u{00A9}e",
        "pay\u{00C3}\u{0192}\u{00C2}\u{00A9}e",
        'reglee',
        "r\u{00E9}gl\u{00E9}e",
    ];

    private const FACTURE_STATUT_IMPAYEE = [
        'impayee',
        "impay\u{00E9}e",
        "impay\u{00C3}\u{00A9}e",
        "impay\u{00C3}\u{0192}\u{00C2}\u{00A9}e",
        'en_attente',
        'brouillon',
    ];

    private const RDV_STATUT_CONFIRME = [
        'en_soins',
        "\u{00E0}_venir",
        'a_venir',
        'en_attente',
        'confirme',
        "confirm\u{00E9}",
        "confirm\u{00C3}\u{00A9}",
        "confirm\u{00C3}\u{0192}\u{00C2}\u{00A9}",
    ];

    public function index()
    {
        $reports = Report::with('user')->latest()->take(10)->get();

        return view('rapports.index', compact('reports'));
    }

    public function generateMonthlyReport(Request $request)
    {
        try {
            if (!auth()->check()) {
                \Log::error('User not authenticated for monthly report');

                return response()->json(['error' => 'Utilisateur non authentifie'], 401);
            }

            $dateDebut = $request->get('date_debut', now()->startOfMonth()->toDateString());
            $dateFin = $request->get('date_fin', now()->endOfMonth()->toDateString());
            $format = $request->get('format', 'pdf');

            \Log::info('Starting monthly report generation', [
                'user_id' => auth()->id(),
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'format' => $format,
            ]);

            $rdvRows = RendezVous::query()
                ->whereBetween('date_heure', [$dateDebut, $dateFin])
                ->selectRaw('statut, COUNT(*) as total_count')
                ->groupBy('statut')
                ->get();

            $rdvTotal = (int) $rdvRows->sum('total_count');
            $rdvConfirmes = (int) $rdvRows->whereIn('statut', self::RDV_STATUT_CONFIRME)->sum('total_count');

            $data = [
                'periode' => Carbon::parse($dateDebut)->format('F Y'),
                'consultations' => [
                    'total' => Consultation::whereBetween('date_consultation', [$dateDebut, $dateFin])->count(),
                    'par_medecin' => DB::table('consultations')
                        ->join('medecins', 'consultations.medecin_id', '=', 'medecins.id')
                        ->whereBetween('consultations.date_consultation', [$dateDebut, $dateFin])
                        ->selectRaw('medecins.nom, medecins.prenom, COUNT(*) as count')
                        ->groupBy('medecins.id', 'medecins.nom', 'medecins.prenom')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'medecin' => ['nom' => $item->nom, 'prenom' => $item->prenom],
                                'count' => $item->count,
                            ];
                        }),
                ],
                'patients' => [
                    'nouveaux' => Patient::whereBetween('created_at', [$dateDebut, $dateFin])->count(),
                    'total_actifs' => Patient::whereHas('consultations', function ($q) use ($dateDebut, $dateFin) {
                        $q->whereBetween('date_consultation', [$dateDebut, $dateFin]);
                    })->count(),
                ],
                'rendez_vous' => [
                    'total' => $rdvTotal,
                    'confirme' => $rdvConfirmes,
                ],
                'revenus' => [
                    'total' => Facture::whereIn('statut', self::FACTURE_STATUT_PAYEE)
                        ->whereBetween('date_facture', [$dateDebut, $dateFin])
                        ->sum('montant_total'),
                    'moyenne_par_consultation' => 0,
                ],
            ];

            if ($data['consultations']['total'] > 0) {
                $data['revenus']['moyenne_par_consultation'] =
                    $data['revenus']['total'] / $data['consultations']['total'];
            }

            return $this->generateReport('monthly', $data, $dateDebut, $dateFin, $format);
        } catch (\Exception $e) {
            \Log::error('Erreur generation rapport mensuel: ' . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de la generation du rapport'], 500);
        }
    }

    public function generateFinancialReport(Request $request)
    {
        try {
            if (!auth()->check()) {
                \Log::error('User not authenticated for financial report');

                return response()->json(['error' => 'Utilisateur non authentifie'], 401);
            }

            $dateDebut = $request->get('date_debut', now()->startOfMonth()->toDateString());
            $dateFin = $request->get('date_fin', now()->endOfMonth()->toDateString());
            $format = $request->get('format', 'pdf');

            \Log::info('Starting financial report generation', [
                'user_id' => auth()->id(),
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'format' => $format,
            ]);

            $factureRows = Facture::query()
                ->whereBetween('date_facture', [$dateDebut, $dateFin])
                ->selectRaw('statut, COUNT(*) as total_count, SUM(montant_total) as total_amount')
                ->groupBy('statut')
                ->get();

            $facturesTotal = (int) $factureRows->sum('total_count');
            $facturesPayees = (int) $factureRows->whereIn('statut', self::FACTURE_STATUT_PAYEE)->sum('total_count');
            $facturesImpayees = (int) $factureRows->whereIn('statut', self::FACTURE_STATUT_IMPAYEE)->sum('total_count');

            $revenusTotal = (float) $factureRows->whereIn('statut', self::FACTURE_STATUT_PAYEE)->sum('total_amount');
            $impayesTotal = (float) $factureRows->whereIn('statut', self::FACTURE_STATUT_IMPAYEE)->sum('total_amount');
            $revenuMoyen = $facturesPayees > 0 ? $revenusTotal / $facturesPayees : 0;

            $anciensImpayes = Facture::query()
                ->whereIn('statut', self::FACTURE_STATUT_IMPAYEE)
                ->where('date_echeance', '<', now())
                ->whereBetween('date_facture', [$dateDebut, $dateFin])
                ->sum('montant_total');

            $data = [
                'periode' => Carbon::parse($dateDebut)->format('F Y'),
                'factures' => [
                    'total' => $facturesTotal,
                    'payees' => $facturesPayees,
                    'impayees' => $facturesImpayees,
                ],
                'revenus' => [
                    'total' => $revenusTotal,
                    'moyenne' => $revenuMoyen,
                ],
                'impayes' => [
                    'montant_total' => $impayesTotal,
                    'anciens' => $anciensImpayes,
                ],
                'evolution_mensuelle' => $this->getMonthlyRevenueEvolution($dateDebut, $dateFin),
            ];

            return $this->generateReport('financial', $data, $dateDebut, $dateFin, $format);
        } catch (\Exception $e) {
            \Log::error('Erreur generation rapport financier: ' . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de la generation du rapport'], 500);
        }
    }

    public function generatePatientReport(Request $request)
    {
        try {
            if (!auth()->check()) {
                \Log::error('User not authenticated for patient report');

                return response()->json(['error' => 'Utilisateur non authentifie'], 401);
            }

            $dateDebut = $request->get('date_debut', now()->startOfMonth()->toDateString());
            $dateFin = $request->get('date_fin', now()->endOfMonth()->toDateString());
            $format = $request->get('format', 'pdf');

            \Log::info('Starting patient report generation', [
                'user_id' => auth()->id(),
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'format' => $format,
            ]);

            $data = [
                'periode' => Carbon::parse($dateDebut)->format('F Y'),
                'demographie' => [
                    'total_patients' => Patient::count(),
                    'nouveaux_patients' => Patient::whereBetween('created_at', [$dateDebut, $dateFin])->count(),
                    'par_genre' => Patient::selectRaw('genre, COUNT(*) as count')
                        ->groupBy('genre')
                        ->get()
                        ->pluck('count', 'genre'),
                    'par_age' => $this->getPatientsByAgeGroup(),
                ],
                'activite' => [
                    'consultations_total' => Consultation::whereBetween('date_consultation', [$dateDebut, $dateFin])->count(),
                    'patients_actifs' => Patient::whereHas('consultations', function ($q) use ($dateDebut, $dateFin) {
                        $q->whereBetween('date_consultation', [$dateDebut, $dateFin]);
                    })->count(),
                    'moyenne_consultations_par_patient' => 0,
                ],
                'top_pathologies' => $this->getTopPathologies($dateDebut, $dateFin),
            ];

            if ($data['activite']['patients_actifs'] > 0) {
                $data['activite']['moyenne_consultations_par_patient'] =
                    $data['activite']['consultations_total'] / $data['activite']['patients_actifs'];
            }

            return $this->generateReport('patient', $data, $dateDebut, $dateFin, $format);
        } catch (\Exception $e) {
            \Log::error('Erreur generation rapport patients: ' . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de la generation du rapport'], 500);
        }
    }

    public function generateMedicamentReport(Request $request)
    {
        try {
            if (!auth()->check()) {
                \Log::error('User not authenticated for medicament report');

                return response()->json(['error' => 'Utilisateur non authentifie'], 401);
            }

            $dateDebut = $request->get('date_debut', now()->startOfMonth()->toDateString());
            $dateFin = $request->get('date_fin', now()->endOfMonth()->toDateString());
            $format = $request->get('format', 'pdf');

            \Log::info('Starting medicament report generation', [
                'user_id' => auth()->id(),
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'format' => $format,
            ]);

            $data = [
                'periode' => Carbon::parse($dateDebut)->format('F Y'),
                'stock' => [
                    'total_medicaments' => Medicament::count(),
                    'stock_faible' => Medicament::whereRaw('quantite_stock <= quantite_seuil')->count(),
                    'rupture_stock' => Medicament::where('quantite_stock', 0)->count(),
                    'valeur_totale_stock' => Medicament::sum(DB::raw('quantite_stock * prix_achat')),
                ],
                'mouvements' => [
                    'entrees' => MouvementStock::where('type_mouvement', 'entree')
                        ->whereBetween('date_mouvement', [$dateDebut, $dateFin])
                        ->sum('quantite'),
                    'sorties' => MouvementStock::where('type_mouvement', 'sortie')
                        ->whereBetween('date_mouvement', [$dateDebut, $dateFin])
                        ->sum('quantite'),
                    'valeur_entrees' => MouvementStock::where('type_mouvement', 'entree')
                        ->whereBetween('date_mouvement', [$dateDebut, $dateFin])
                        ->sum('valeur_totale'),
                    'valeur_sorties' => MouvementStock::where('type_mouvement', 'sortie')
                        ->whereBetween('date_mouvement', [$dateDebut, $dateFin])
                        ->sum('valeur_totale'),
                ],
                'top_medicaments' => $this->getTopMedicaments($dateDebut, $dateFin),
                'alertes' => [
                    'perimes' => Medicament::where('date_peremption', '<', now())->count(),
                    'peremption_proche' => Medicament::where('date_peremption', '<=', now()->addDays(30))
                        ->where('date_peremption', '>', now())->count(),
                ],
            ];

            return $this->generateReport('medicament', $data, $dateDebut, $dateFin, $format);
        } catch (\Exception $e) {
            \Log::error('Erreur generation rapport medicaments: ' . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de la generation du rapport'], 500);
        }
    }

    private function generateReport($type, $data, $dateDebut, $dateFin, $format)
    {
        try {
            $report = Report::create([
                'type' => $type,
                'periode' => $data['periode'],
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'format' => $format,
                'generated_by' => auth()->id(),
                'parameters' => request()->all(),
            ]);

            if ($format === 'pdf') {
                return $this->generatePdf($type, $data, $report);
            }

            if ($format === 'excel') {
                return $this->generateExcel($type, $data, $report);
            }

            return response()->json(['message' => 'Format non supporte'], 400);
        } catch (\Exception $e) {
            \Log::error('Erreur creation rapport: ' . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de la generation du rapport'], 500);
        }
    }

    private function generatePdf($type, $data, $report)
    {
        try {
            $pdf = app(PdfBuilder::class)->fromView("rapports.pdf.{$type}", compact('data', 'report'));
            $filename = "rapport_{$type}_{$report->id}.pdf";

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Erreur generation PDF: ' . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de la generation du PDF'], 500);
        }
    }

    private function generateExcel($type, $data, $report)
    {
        $filename = "rapport_{$type}_{$report->id}.xlsx";

        return Excel::download(
            new GenericTableExport($this->flattenForSpreadsheet($data), ['Indicateur', 'Valeur']),
            $filename
        );
    }

    private function flattenForSpreadsheet(array $data, string $prefix = ''): array
    {
        $rows = [];

        foreach ($data as $key => $value) {
            $label = trim($prefix . ' ' . str_replace('_', ' ', (string) $key));
            $label = ucwords($label);

            if ($value instanceof Collection) {
                $rows = array_merge($rows, $this->flattenForSpreadsheet($value->toArray(), $label));
                continue;
            }

            if (is_array($value)) {
                if ($this->isList($value)) {
                    foreach ($value as $index => $item) {
                        if (is_array($item)) {
                            $rows = array_merge($rows, $this->flattenForSpreadsheet($item, $label . ' ' . ($index + 1)));
                        } else {
                            $rows[] = [$label . ' ' . ($index + 1), $item];
                        }
                    }
                } else {
                    $rows = array_merge($rows, $this->flattenForSpreadsheet($value, $label));
                }

                continue;
            }

            $rows[] = [$label, is_bool($value) ? ($value ? 'Oui' : 'Non') : $value];
        }

        return $rows;
    }

    private function isList(array $value): bool
    {
        return array_keys($value) === range(0, count($value) - 1);
    }

    private function getMonthlyRevenueEvolution($dateDebut, $dateFin)
    {
        $start = Carbon::parse($dateDebut)->startOfMonth();
        $end = Carbon::parse($dateFin)->endOfMonth();

        $rows = Facture::query()
            ->whereIn('statut', self::FACTURE_STATUT_PAYEE)
            ->whereBetween('date_facture', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('DATE_FORMAT(date_facture, "%Y-%m") as ym, SUM(montant_total) as total')
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $data = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $key = $cursor->format('Y-m');
            $data[] = [
                'mois' => $cursor->format('M Y'),
                'revenus' => (float) ($rows[$key] ?? 0),
            ];

            $cursor->addMonth();
        }

        return $data;
    }

    private function getPatientsByAgeGroup()
    {
        return Patient::selectRaw("
            CASE
                WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 18 THEN '0-17'
                WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 18 AND 35 THEN '18-35'
                WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 36 AND 55 THEN '36-55'
                WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 56 AND 75 THEN '56-75'
                ELSE '75+'
            END as age_group,
            COUNT(*) as count
        ")
            ->whereNotNull('date_naissance')
            ->groupBy('age_group')
            ->get()
            ->pluck('count', 'age_group');
    }

    private function getTopPathologies($dateDebut, $dateFin)
    {
        return Consultation::selectRaw('diagnostic, COUNT(*) as count')
            ->whereBetween('date_consultation', [$dateDebut, $dateFin])
            ->whereNotNull('diagnostic')
            ->groupBy('diagnostic')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();
    }

    private function getTopMedicaments($dateDebut, $dateFin)
    {
        return MouvementStock::with('medicament')
            ->selectRaw('medicament_id, SUM(quantite) as total_quantite')
            ->where('type_mouvement', 'sortie')
            ->whereBetween('date_mouvement', [$dateDebut, $dateFin])
            ->groupBy('medicament_id')
            ->orderBy('total_quantite', 'desc')
            ->take(10)
            ->get()
            ->map(function ($mouvement) {
                return [
                    'medicament' => $mouvement->medicament->nom_commercial ?? 'N/A',
                    'quantite' => $mouvement->total_quantite,
                ];
            });
    }
}
