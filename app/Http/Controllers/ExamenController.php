<?php

namespace App\Http\Controllers;

use App\Exports\ExamensExport;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\ResultatExamen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExamenController extends Controller
{
    public function index(Request $request)
    {
        $query = Examen::with('patient');

        if ($request->filled('patient')) {
            $query->where('patient_id', $request->patient);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('nom_examen', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $allowedSortBy = ['date_demande', 'created_at', 'statut', 'type'];
        $allowedSortOrder = ['asc', 'desc'];
        $sortBy = in_array($request->get('sort_by'), $allowedSortBy, true) ? $request->get('sort_by') : 'date_demande';
        $sortOrder = in_array($request->get('sort_order'), $allowedSortOrder, true) ? $request->get('sort_order') : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $examens = $query->paginate(20)->appends($request->query());
        $examens->getCollection()->transform(function (Examen $examen) {
            $examen->display_status_class = in_array((string) $examen->statut, ['demande', 'en_cours', 'termine', 'annule', 'en_attente'], true)
                ? 'status-' . $examen->statut
                : 'status-default';
            $examen->display_status_text = ucfirst(str_replace('_', ' ', (string) ($examen->statut ?? 'inconnu')));
            $examen->display_patient_name = trim(($examen->patient->nom ?? '') . ' ' . ($examen->patient->prenom ?? '')) ?: 'N/A';
            $examenDate = $examen->date_examen ? Carbon::parse($examen->date_examen) : null;
            $examen->display_date_examen = $examenDate?->format('d/m/Y H:i') ?? '-';
            $examen->display_date_examen_human = $examenDate?->diffForHumans() ?? 'Date non planifiee';

            return $examen;
        });
        $examensEnAttente = Examen::where('statut', 'demande')->count();
        $examensEnCours = Examen::whereIn('statut', ['en_cours', 'en_attente'])->count();
        $examensTermines = Examen::where('statut', 'termine')->count();
        $patients = Patient::orderBy('nom')->get();
        $types = ['biologie', 'imagerie', 'endoscopie', 'autre'];
        $statuts = ['demande', 'en_attente', 'termine', 'annule'];
        $examensTotal = method_exists($examens, 'total') ? $examens->total() : count($examens ?? []);
        $hasFilters = $request->filled('search') || $request->filled('patient') || $request->filled('statut') || $request->filled('type');
        $selectedPatientId = $request->input('patient');
        $selectedPatientLabel = null;

        if ($selectedPatientId) {
            $selectedPatientModel = $patients->firstWhere('id', (int) $selectedPatientId);
            $selectedPatientLabel = $selectedPatientModel
                ? trim(($selectedPatientModel->nom ?? '') . ' ' . ($selectedPatientModel->prenom ?? ''))
                : null;
        }

        $selectedStatus = $request->input('statut');
        $selectedType = $request->input('type');
        $inProgress = $examensEnCours;

        return view('examens.index', compact(
            'examens',
            'patients',
            'types',
            'statuts',
            'examensEnAttente',
            'examensEnCours',
            'examensTermines',
            'examensTotal',
            'hasFilters',
            'selectedPatientId',
            'selectedPatientLabel',
            'selectedStatus',
            'selectedType',
            'inProgress'
        ));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('nom')->get();
        $medecins = Medecin::orderBy('nom')->get();
        $types = ['biologie' => 'Biologie', 'imagerie' => 'Imagerie', 'endoscopie' => 'Endoscopie', 'autre' => 'Autre'];

        $patient = null;
        if ($request->has('patient_id')) {
            $patient = Patient::find($request->patient_id);
        }

        return view('examens.create', compact('patients', 'medecins', 'types', 'patient'));
    }

    public function results(Request $request)
    {
        $resultStatusOptions = [
            'demande' => 'En attente',
            'en_attente' => 'En cours',
            'termine' => 'Termines',
            'annule' => 'Annules',
        ];

        $typeOptions = [
            'biologie' => 'Biologie',
            'imagerie' => 'Imagerie',
            'endoscopie' => 'Endoscopie',
            'autre' => 'Autre',
        ];

        $query = Examen::query()
            ->with(['patient', 'resultatsExamens'])
            ->withCount('resultatsExamens');

        if ($request->filled('patient')) {
            $query->where('patient_id', $request->integer('patient'));
        }

        if ($request->filled('type')) {
            $query->where('type', (string) $request->input('type'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', (string) $request->input('statut'));
        }

        if ($request->filled('date')) {
            $query->whereDate('date_demande', (string) $request->input('date'));
        }

        $summaryQuery = clone $query;

        $results = $query
            ->orderByDesc('date_demande')
            ->paginate(12)
            ->appends($request->query());

        $resultRows = collect($results->items())->map(function (Examen $examen) {
            $hasStructuredResults = $examen->resultatsExamens->isNotEmpty();
            $hasTextResult = filled($examen->resultats);
            $hasDownload = filled($examen->document_resultat) || filled($examen->fichier_examen);

            $criticalCount = $examen->resultatsExamens->where('interpretation', 'critique')->count();
            $abnormalCount = $examen->resultatsExamens->where('interpretation', 'anormal')->count();

            if ($criticalCount > 0) {
                $resultSummary = $criticalCount . ' parametre' . ($criticalCount > 1 ? 's' : '') . ' critique' . ($criticalCount > 1 ? 's' : '');
                $resultTone = 'critical';
            } elseif ($abnormalCount > 0) {
                $resultSummary = $abnormalCount . ' parametre' . ($abnormalCount > 1 ? 's' : '') . ' anormal' . ($abnormalCount > 1 ? 'ux' : '');
                $resultTone = 'warning';
            } elseif ($hasStructuredResults) {
                $resultSummary = $examen->resultatsExamens_count . ' resultat' . ($examen->resultatsExamens_count > 1 ? 's' : '') . ' enregistre' . ($examen->resultatsExamens_count > 1 ? 's' : '');
                $resultTone = 'success';
            } elseif ($hasTextResult) {
                $resultSummary = 'Compte rendu disponible';
                $resultTone = 'info';
            } elseif ($hasDownload) {
                $resultSummary = 'Document resultat disponible';
                $resultTone = 'info';
            } else {
                $resultSummary = 'Resultat non renseigne';
                $resultTone = 'muted';
            }

            $resultPreview = $hasStructuredResults
                ? $examen->resultatsExamens->take(2)->map(function (ResultatExamen $resultat) {
                    return trim($resultat->parametre . ': ' . $resultat->valeur . ($resultat->unite ? ' ' . $resultat->unite : ''));
                })->implode(' | ')
                : ($hasTextResult ? mb_strimwidth(trim((string) $examen->resultats), 0, 110, '...') : ($hasDownload ? 'PDF ou fichier joint disponible depuis les actions.' : 'Attente de saisie ou d import du resultat.'));

            $statusKey = (string) $examen->statut;
            $examen->results_status_label = match ($statusKey) {
                'demande' => 'En attente',
                'en_attente' => 'En cours',
                'termine' => 'Termine',
                'annule' => 'Annule',
                default => ucfirst(str_replace('_', ' ', $statusKey ?: 'inconnu')),
            };
            $examen->results_status_class = in_array($statusKey, ['demande', 'en_attente', 'termine', 'annule'], true)
                ? $statusKey
                : 'inconnu';

            $filePath = $examen->document_resultat ?: $examen->fichier_examen;
            $examen->results_download_url = null;
            if (filled($filePath)) {
                $normalizedPath = ltrim((string) $filePath, '/');
                if (str_starts_with($normalizedPath, 'http://') || str_starts_with($normalizedPath, 'https://')) {
                    $examen->results_download_url = $normalizedPath;
                } elseif (Storage::exists($normalizedPath)) {
                    $examen->results_download_url = Storage::url($normalizedPath);
                } elseif (str_starts_with($normalizedPath, 'storage/')) {
                    $examen->results_download_url = asset($normalizedPath);
                } else {
                    $examen->results_download_url = asset('storage/' . $normalizedPath);
                }
            }

            $examen->results_summary = $resultSummary;
            $examen->results_tone = $resultTone;
            $examen->results_preview = $resultPreview;
            $examen->display_patient_name = trim(($examen->patient->nom ?? '') . ' ' . ($examen->patient->prenom ?? '')) ?: 'Patient inconnu';
            $examen->display_date_label = optional($examen->date_demande)->format('d/m/Y');
            $examen->display_date_meta = optional($examen->date_demande)->format('H:i');

            return $examen;
        });

        $patients = Patient::query()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        $patients->transform(function (Patient $patient) {
            $patient->display_label = trim(($patient->nom ?? '') . ' ' . ($patient->prenom ?? '')) ?: 'Patient #' . $patient->id;

            return $patient;
        });

        $totalExamens = (clone $summaryQuery)->count();
        $examensEnAttente = (clone $summaryQuery)->where('statut', 'demande')->count();
        $examensEnCours = (clone $summaryQuery)->where('statut', 'en_attente')->count();
        $examensTermines = (clone $summaryQuery)->where('statut', 'termine')->count();

        $selectedPatient = (string) $request->input('patient', '');
        $selectedType = (string) $request->input('type', '');
        $selectedStatut = (string) $request->input('statut', '');
        $selectedDate = (string) $request->input('date', '');
        $selectedDateLabel = $selectedDate !== '' ? Carbon::parse($selectedDate)->format('d/m/Y') : null;

        $hasFilters = $selectedPatient !== '' || $selectedType !== '' || $selectedStatut !== '' || $selectedDate !== '';
        $selectedPatientLabel = $patients->firstWhere('id', (int) $selectedPatient);

        return view('examens.results', [
            'results' => $results,
            'resultRows' => $resultRows,
            'patients' => $patients,
            'types' => $typeOptions,
            'resultStatusOptions' => $resultStatusOptions,
            'totalExamens' => $totalExamens,
            'examensEnAttente' => $examensEnAttente,
            'examensEnCours' => $examensEnCours,
            'examensTermines' => $examensTermines,
            'selectedPatient' => $selectedPatient,
            'selectedType' => $selectedType,
            'selectedStatut' => $selectedStatut,
            'selectedStatutLabel' => $resultStatusOptions[$selectedStatut] ?? null,
            'selectedDate' => $selectedDate,
            'selectedDateLabel' => $selectedDateLabel,
            'hasFilters' => $hasFilters,
            'selectedPatientLabel' => $selectedPatientLabel ? trim(($selectedPatientLabel->nom ?? '') . ' ' . ($selectedPatientLabel->prenom ?? '')) : null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateExamen($request);
        $payload = $this->mapToPersistencePayload($validated);

        Examen::create($payload);

        return redirect()
            ->route('patients.show', $validated['patient_id'])
            ->with('success', 'Examen cree avec succes.');
    }

    public function show(Examen $examen)
    {
        $examen->load('patient', 'medecin', 'consultation', 'resultatsExamens');

        return view('examens.show', compact('examen'));
    }

    public function edit(Examen $examen)
    {
        $patients = Patient::orderBy('nom')->get();
        $medecins = Medecin::orderBy('nom')->get();
        $types = ['biologie' => 'Biologie', 'imagerie' => 'Imagerie', 'endoscopie' => 'Endoscopie', 'autre' => 'Autre'];

        return view('examens.edit', compact('examen', 'patients', 'medecins', 'types'));
    }

    public function update(Request $request, Examen $examen)
    {
        $validated = $this->validateExamen($request);
        $payload = $this->mapToPersistencePayload($validated);

        $examen->update($payload);

        return redirect()
            ->route('examens.show', $examen)
            ->with('success', 'Examen mis a jour avec succes.');
    }

    public function destroy(Examen $examen)
    {
        $patientId = $examen->patient_id;
        $examen->delete();

        return redirect()
            ->route('patients.show', $patientId)
            ->with('success', 'Examen supprime avec succes.');
    }

    public function addResultat(Request $request, Examen $examen)
    {
        $validated = $request->validate([
            'parametre' => 'required|string|max:255',
            'valeur' => 'required|string|max:100',
            'unite' => 'nullable|string|max:50',
            'valeur_normale' => 'nullable|string|max:100',
            'interpretation' => 'required|in:normal,anormal,critique',
            'notes' => 'nullable|string',
        ]);

        ResultatExamen::create([
            'examen_id' => $examen->id,
            ...$validated,
        ]);

        return back()->with('success', 'Resultat ajoute avec succes.');
    }

    public function deleteResultat(ResultatExamen $resultat)
    {
        $resultat->delete();

        return back()->with('success', 'Resultat supprime avec succes.');
    }

    public function export(Request $request)
    {
        $query = Examen::with('patient', 'medecin');

        if ($request->filled('patient')) {
            $query->byPatient($request->patient);
        }

        if ($request->filled('statut')) {
            $query->byStatut($request->statut);
        }

        $examens = $query->orderBy('date_demande', 'desc')->get();

        return Excel::download(new ExamensExport($examens), 'examens.xlsx');
    }

    private function validateExamen(Request $request): array
    {
        return $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'medecin_id' => ['nullable', 'exists:medecins,id'],
            'consultation_id' => ['nullable', 'exists:consultations,id'],
            'type_examen' => ['required', 'string', 'max:255'],
            'date_examen' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'localisation' => ['nullable', 'string', 'max:255'],
            'appareil' => ['nullable', 'string', 'max:255'],
            'resultats' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
            'recommandations' => ['nullable', 'string'],
            'statut' => ['required', 'in:demande,en_attente,en_cours,termine,annule'],
            'cout' => ['nullable', 'numeric', 'min:0'],
            'payee' => ['nullable', 'boolean'],
        ]);
    }

    private function mapToPersistencePayload(array $validated): array
    {
        return [
            'patient_id' => $validated['patient_id'],
            'medecin_id' => $validated['medecin_id'] ?? null,
            'consultation_id' => $validated['consultation_id'] ?? null,
            'nom_examen' => $validated['type_examen'],
            'description' => $validated['description'] ?? null,
            'type' => $this->mapLegacyType($validated['type_examen']),
            'statut' => $this->mapLegacyStatut($validated['statut']),
            'date_demande' => Carbon::parse($validated['date_examen'])->startOfDay(),
            'date_realisation' => null,
            'lieu_realisation' => $validated['localisation'] ?? null,
            'appareil' => $validated['appareil'] ?? null,
            'resultats' => $validated['resultats'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'recommandations' => $validated['recommandations'] ?? null,
            'cout' => $validated['cout'] ?? null,
            'payee' => (bool) ($validated['payee'] ?? false),
            'created_by' => auth()->id(),
        ];
    }

    private function mapLegacyType(string $typeExamen): string
    {
        $normalized = mb_strtolower(trim($typeExamen));

        return match ($normalized) {
            'analyse de sang', 'biologie' => 'biologie',
            'radiographie', 'echographie', 'irm', 'imagerie' => 'imagerie',
            'endoscopie' => 'endoscopie',
            default => 'autre',
        };
    }

    private function mapLegacyStatut(string $statut): string
    {
        return $statut === 'en_cours' ? 'en_attente' : $statut;
    }
}
