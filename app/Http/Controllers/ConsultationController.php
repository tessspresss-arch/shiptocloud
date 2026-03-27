<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationAiGeneration;
use App\Models\Medecin;
use App\Models\Medicament;
use App\Models\Ordonnance;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Services\Exports\Utf8CsvExporter;
use App\Services\Security\ClinicalAuthorizationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConsultationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Consultation::class, 'consultation');
    }

    public function index(Request $request)
    {
        $perPage = max(10, min(100, (int) $request->integer('per_page', 10)));
        $selectedPeriod = $request->input('period');
        $selectedMedecin = $request->input('medecin');
        $currentPerPage = $perPage;
        $hasFilters = $request->hasAny(['search', 'period', 'medecin', 'per_page']);
        $periodLabels = [
            'today' => "Aujourd'hui",
            'week' => 'Cette semaine',
            'month' => 'Ce mois',
            'year' => 'Cette annee',
        ];

        $baseQuery = $this->buildIndexQuery($request);

        $consultations = (clone $baseQuery)
            ->orderByDesc('date_consultation')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        foreach ($consultations as $consultation) {
            [$statusClass, $statusLabel] = $this->resolveConsultationStatus($consultation);
            $consultation->status_class = $statusClass;
            $consultation->status_label = $statusLabel;
            $consultation->display_date = $consultation->date_consultation ? Carbon::parse($consultation->date_consultation) : null;
        }

        [$todayStart, $todayEnd] = $this->dayBounds(Carbon::today());
        $totalConsultations = (clone $baseQuery)->count();
        $todayConsultations = (clone $baseQuery)->whereBetween('date_consultation', [$todayStart, $todayEnd])->count();
        $upcomingConsultations = (clone $baseQuery)
            ->whereBetween('date_consultation', [$todayEnd->copy()->addSecond(), $todayEnd->copy()->addDays(7)])
            ->count();
        $activeMedecins = Medecin::actif()->count();
        $medecins = Medecin::actif()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get(['id', 'nom', 'prenom']);

        $selectedMedecinLabel = null;
        if (filled($selectedMedecin)) {
            $selectedMedecinModel = $medecins->firstWhere('id', (int) $selectedMedecin);
            $selectedMedecinLabel = $selectedMedecinModel
                ? trim(($selectedMedecinModel->prenom ?? '') . ' ' . ($selectedMedecinModel->nom ?? ''))
                : null;
        }

        return view('consultations.index', compact(
            'consultations',
            'totalConsultations',
            'todayConsultations',
            'upcomingConsultations',
            'activeMedecins',
            'medecins',
            'selectedPeriod',
            'selectedMedecin',
            'currentPerPage',
            'hasFilters',
            'periodLabels',
            'selectedMedecinLabel'
        ));
    }

    public function export(Request $request, Utf8CsvExporter $csvExporter): StreamedResponse
    {
        $this->authorize('export', Consultation::class);

        $consultations = $this->buildIndexQuery($request)
            ->orderByDesc('date_consultation')
            ->orderByDesc('id')
            ->get();

        $rows = $consultations->map(function ($consultation) {
            [, $statusLabel] = $this->resolveConsultationStatus($consultation);

            return [
                (string) $consultation->id,
                $consultation->date_consultation ? Carbon::parse($consultation->date_consultation)->format('Y-m-d H:i') : '',
                $statusLabel,
                $consultation->patient ? trim(((string) $consultation->patient->prenom) . ' ' . ((string) $consultation->patient->nom)) : '',
                $consultation->medecin ? trim(((string) $consultation->medecin->prenom) . ' ' . ((string) $consultation->medecin->nom)) : '',
                (string) ($consultation->diagnostic ?? ''),
                $consultation->rendezvous ? (string) $consultation->rendezvous->id : '',
                $consultation->created_at ? Carbon::parse($consultation->created_at)->format('Y-m-d H:i') : '',
            ];
        });

        return $csvExporter->download(
            'consultations-' . now()->format('Y-m-d-His') . '.csv',
            ['ID', 'Date consultation', 'Statut', 'Patient', 'Medecin', 'Diagnostic', 'Rendez-vous lie', 'Cree le'],
            $rows
        );
    }

    private function buildIndexQuery(Request $request)
    {
        $query = Consultation::query()
            ->select([
                'id',
                'patient_id',
                'medecin_id',
                'date_consultation',
                'diagnostic',
                'created_at',
            ])
            ->with([
                'patient:id,nom,prenom,photo',
                'medecin:id,nom,prenom',
                'rendezvous:id',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));

                $query->where(function ($inner) use ($search) {
                    $inner->where('diagnostic', 'like', '%' . $search . '%')
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery
                                ->where('nom', 'like', '%' . $search . '%')
                                ->orWhere('prenom', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('medecin', function ($medecinQuery) use ($search) {
                            $medecinQuery
                                ->where('nom', 'like', '%' . $search . '%')
                                ->orWhere('prenom', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($request->filled('medecin'), function ($query) use ($request) {
                $query->where('medecin_id', (int) $request->input('medecin'));
            })
            ->when($request->filled('period'), function ($query) use ($request) {
                $period = (string) $request->input('period');
                $today = Carbon::today();

                if ($period === 'today') {
                    $query->whereBetween('date_consultation', $this->dayBounds($today));
                } elseif ($period === 'week') {
                    $query->whereBetween('date_consultation', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
                } elseif ($period === 'month') {
                    $query->whereBetween('date_consultation', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()]);
                } elseif ($period === 'year') {
                    $query->whereBetween('date_consultation', [$today->copy()->startOfYear(), $today->copy()->endOfYear()]);
                }
            });

        if ($request->user()) {
            app(ClinicalAuthorizationService::class)->scopeConsultations($query, $request->user());
        }

        return $query;
    }

    private function resolveConsultationStatus(Consultation $consultation): array
    {
        if (!$consultation->date_consultation) {
            return ['pending', 'En attente'];
        }

        $date = Carbon::parse($consultation->date_consultation);

        if ($date->isPast()) {
            if (!empty($consultation->diagnostic)) {
                return ['completed', 'Terminee'];
            }

            return ['pending', 'En attente'];
        }

        if ($date->isFuture()) {
            return ['scheduled', 'Planifiee'];
        }

        return ['pending', 'En attente'];
    }

    public function create(Request $request)
    {
        $patients = $this->patientSelectionQuery()->get();
        $medecins = $this->medecinSelectionQuery()->get();

        $selectedRendezVous = null;
        $selectedRendezVousId = $request->filled('rendez_vous_id')
            ? $request->integer('rendez_vous_id')
            : null;

        if ($selectedRendezVousId) {
            $selectedRendezVous = RendezVous::with(['patient:id,nom,prenom', 'medecin:id,nom,prenom'])
                ->find($selectedRendezVousId);
        }

        $selectedPatientId = $request->input('patient_id', $selectedRendezVous?->patient_id);
        $selectedMedecinId = $request->input('medecin_id', $selectedRendezVous?->medecin_id);
        $selectedDateConsultation = old(
            'date_consultation',
            optional($selectedRendezVous?->date_heure)->format('Y-m-d\TH:i') ?: now()->format('Y-m-d\TH:i')
        );

        return view('consultations.create', compact(
            'patients',
            'medecins',
            'selectedPatientId',
            'selectedMedecinId',
            'selectedRendezVousId',
            'selectedDateConsultation',
            'selectedRendezVous'
        ));
    }

    public function store(Request $request)
    {
        if (!$request->filled('rendez_vous_id') || (int) $request->input('rendez_vous_id') <= 0) {
            $request->merge(['rendez_vous_id' => null]);
        }

        $validated = $request->validate([
            'rendez_vous_id' => 'nullable|exists:rendez_vous,id',
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'date_consultation' => 'required|date',
            'symptomes' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'poids' => 'nullable|numeric',
            'taille' => 'nullable|numeric',
            'tension_arterielle_systolique' => 'nullable|integer',
            'tension_arterielle_diastolique' => 'nullable|integer',
            'temperature' => 'nullable|numeric',
            'examen_clinique' => 'nullable|string',
            'traitement_prescrit' => 'nullable|string',
            'recommandations' => 'nullable|string',
            'date_prochaine_visite' => 'nullable|date',
        ]);

        $consultation = Consultation::create($validated);

        if (!empty($validated['rendez_vous_id'])) {
            $rendezVous = RendezVous::find($validated['rendez_vous_id']);
            if ($rendezVous) {
                $rendezVous->statut = 'en_soins';
                if ($rendezVous->consultation_started_at === null) {
                    $rendezVous->consultation_started_at = now();
                }
                $rendezVous->save();
            }
        }

        if (!empty($validated['rendez_vous_id'])) {
            return redirect()->route('consultations.edit', $consultation)
                ->with('success', 'Consultation creee avec succes. Vous pouvez poursuivre avec l assistant IA.');
        }

        return redirect()->route('consultations.index')
            ->with('success', 'Consultation creee avec succes.');
    }

    public function show(Consultation $consultation)
    {
        $consultation->load(['patient', 'medecin', 'rendezvous', 'prescriptions']);

        $consultationDate = $consultation->date_consultation ? Carbon::parse($consultation->date_consultation) : null;
        $nextVisitDate = $consultation->date_prochaine_visite ? Carbon::parse($consultation->date_prochaine_visite) : null;
        $patient = $consultation->patient;
        $medecin = $consultation->medecin;
        if ($patient) {
            $patient->loadCount('ordonnances');
        }
        $patientName = $patient ? trim(strtoupper((string) $patient->nom) . ' ' . (string) $patient->prenom) : 'Patient non renseigne';
        $medecinName = $medecin ? trim(((string) ($medecin->civilite ?? 'Dr.')) . ' ' . (string) $medecin->prenom . ' ' . (string) $medecin->nom) : 'Medecin non renseigne';
        $patientAge = $patient && $patient->date_naissance ? Carbon::parse($patient->date_naissance)->age : null;
        $temperature = is_numeric($consultation->temperature) ? number_format((float) $consultation->temperature, 1, '.', '') : null;
        $poids = is_numeric($consultation->poids) ? number_format((float) $consultation->poids, 2, '.', '') : null;
        $taille = is_numeric($consultation->taille) ? number_format((float) $consultation->taille, 2, '.', '') : null;
        $tension = ($consultation->tension_arterielle_systolique && $consultation->tension_arterielle_diastolique)
            ? $consultation->tension_arterielle_systolique . '/' . $consultation->tension_arterielle_diastolique
            : null;
        $poidsFloat = is_numeric($consultation->poids) ? (float) $consultation->poids : null;
        $tailleFloat = is_numeric($consultation->taille) ? (float) $consultation->taille : null;
        $tailleMetre = $tailleFloat ? ($tailleFloat > 10 ? $tailleFloat / 100 : $tailleFloat) : null;
        $imc = ($poidsFloat && $tailleMetre && $tailleMetre > 0)
            ? number_format($poidsFloat / ($tailleMetre * $tailleMetre), 1, '.', '')
            : null;
        $hasClinicalConclusion = filled($consultation->diagnostic) || filled($consultation->traitement_prescrit);
        $statusClass = 'pending';
        $statusLabel = 'En attente';

        if ($consultationDate) {
            if ($consultationDate->isFuture()) {
                $statusClass = 'scheduled';
                $statusLabel = 'Planifiee';
            } elseif ($consultationDate->isToday() && !$hasClinicalConclusion) {
                $statusClass = 'in-progress';
                $statusLabel = 'En cours';
            } elseif ($hasClinicalConclusion) {
                $statusClass = 'completed';
                $statusLabel = 'Terminee';
            }
        }

        $consultation->prescriptions->transform(function ($prescription) {
            $prescription->display_date = $prescription->date_prescription ? Carbon::parse($prescription->date_prescription) : null;
            $statut = strtolower((string) ($prescription->statut ?? 'active'));
            $prescription->display_status = ucfirst($statut ?: 'active');
            $prescription->status_pill_class = str_contains($statut, 'termine')
                ? 'success'
                : (str_contains($statut, 'annule') ? 'warning' : '');
            $prescription->med_count = is_array($prescription->medicaments) ? count($prescription->medicaments) : 0;

            return $prescription;
        });

        $cabinetName = config('app.name', 'Cabinet Medical');
        $prescriptionsCount = $consultation->prescriptions->count();
        $medecins = Medecin::query()
            ->select(['id', 'civilite', 'nom', 'prenom', 'specialite', 'email'])
            ->orderBy('prenom')
            ->orderBy('nom')
            ->get();
        $currentMedecin = $medecin ?: $this->resolveCurrentMedecin($medecins);
        $medicamentCatalogData = Medicament::query()
            ->select([
                'id',
                'nom_commercial',
                'dci',
                'presentation',
                'posologie',
                'voie_administration',
                'classe_therapeutique',
            ])
            ->orderBy('nom_commercial')
            ->get()
            ->map(function (Medicament $medicament): array {
                return [
                    'id' => $medicament->id,
                    'label' => trim($medicament->nom_commercial . ' ' . ($medicament->presentation ? '(' . $medicament->presentation . ')' : '')),
                    'search' => Str::lower(implode(' ', array_filter([
                        $medicament->nom_commercial,
                        $medicament->dci,
                        $medicament->presentation,
                        $medicament->classe_therapeutique,
                    ]))),
                    'nom_commercial' => $medicament->nom_commercial,
                    'dci' => $medicament->dci,
                    'presentation' => $medicament->presentation,
                    'posologie' => $medicament->posologie,
                    'voie_administration' => $medicament->voie_administration,
                    'classe_therapeutique' => $medicament->classe_therapeutique,
                ];
            })
            ->values();
        $consultationOrdonnancesCount = Ordonnance::query()
            ->where('consultation_id', $consultation->id)
            ->count();
        $latestConsultationOrdonnance = Ordonnance::query()
            ->where('consultation_id', $consultation->id)
            ->latest('date_prescription')
            ->latest('id')
            ->first();
        $aiGenerations = $consultation->aiGenerations()
            ->with('user:id,name')
            ->latest()
            ->limit((int) config('medical_ai.history_limit', 8))
            ->get();
        $aiGenerationsCount = $consultation->aiGenerations()->count();

        return view('consultations.show', compact(
            'consultation',
            'consultationDate',
            'nextVisitDate',
            'patient',
            'medecin',
            'patientName',
            'medecinName',
            'patientAge',
            'temperature',
            'poids',
            'taille',
            'tension',
            'imc',
            'statusClass',
            'statusLabel',
            'cabinetName',
            'prescriptionsCount',
            'medecins',
            'currentMedecin',
            'medicamentCatalogData',
            'consultationOrdonnancesCount',
            'latestConsultationOrdonnance',
            'aiGenerations',
            'aiGenerationsCount'
        ));
    }

    public function edit(Consultation $consultation)
    {
        $consultation->load(['patient', 'medecin', 'rendezvous']);
        $patients = $this->patientSelectionQuery()->get();
        $medecins = $this->medecinSelectionQuery()->get();

        $consultationDate = $consultation->date_consultation ? Carbon::parse($consultation->date_consultation) : null;
        $statusClass = 'pending';
        $statusLabel = 'En attente';

        if ($consultationDate) {
            if ($consultationDate->isFuture()) {
                $statusClass = 'scheduled';
                $statusLabel = 'Planifiee';
            } elseif (filled($consultation->diagnostic)) {
                $statusClass = 'completed';
                $statusLabel = 'Terminee';
            }
        }

        $aiGenerationsCount = $consultation->aiGenerations()->count();

        return view('consultations.edit', compact(
            'consultation',
            'patients',
            'medecins',
            'consultationDate',
            'statusClass',
            'statusLabel',
            'aiGenerationsCount'
        ));
    }

    public function update(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'date_consultation' => 'required|date',
            'symptomes' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'poids' => 'nullable|numeric',
            'taille' => 'nullable|numeric',
            'tension_arterielle_systolique' => 'nullable|integer',
            'tension_arterielle_diastolique' => 'nullable|integer',
            'temperature' => 'nullable|numeric',
            'examen_clinique' => 'nullable|string',
            'traitement_prescrit' => 'nullable|string',
            'recommandations' => 'nullable|string',
            'date_prochaine_visite' => 'nullable|date',
        ]);

        $consultation->update($validated);

        if ($request->boolean('redirect_to_show')) {
            return redirect()->route('consultations.show', $consultation)
                ->with('success', 'Consultation mise a jour avec succes.');
        }

        return redirect()->route('consultations.index')
            ->with('success', 'Consultation mise a jour avec succes.');
    }

    public function destroy(Consultation $consultation)
    {
        $consultation->delete();

        return redirect()->route('consultations.index')
            ->with('success', 'Consultation supprimee avec succes.');
    }

    private function dayBounds(Carbon $date): array
    {
        return [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
    }

    private function patientSelectionQuery()
    {
        return Patient::query()
            ->select(['id', 'nom', 'prenom', 'cin', 'date_naissance'])
            ->orderBy('nom')
            ->orderBy('prenom');
    }

    private function medecinSelectionQuery()
    {
        return Medecin::actif()
            ->select(['id', 'nom', 'prenom', 'specialite'])
            ->orderBy('nom')
            ->orderBy('prenom');
    }

    private function resolveCurrentMedecin($medecins): ?Medecin
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        $medecinId = $user->getAttribute('medecin_id');
        if ($medecinId) {
            $direct = $medecins->firstWhere('id', (int) $medecinId);
            if ($direct) {
                return $direct;
            }
        }

        $emailMatch = $medecins->firstWhere('email', $user->email);
        if ($emailMatch) {
            return $emailMatch;
        }

        $parts = preg_split('/\s+/', trim((string) $user->name)) ?: [];
        if (count($parts) >= 2) {
            $prenom = $parts[0];
            $nom = $parts[count($parts) - 1];

            return $medecins->first(function (Medecin $medecin) use ($prenom, $nom): bool {
                return strcasecmp((string) $medecin->prenom, (string) $prenom) === 0
                    && strcasecmp((string) $medecin->nom, (string) $nom) === 0;
            });
        }

        return null;
    }
}
