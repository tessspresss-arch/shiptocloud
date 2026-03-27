<?php

namespace App\Http\Controllers;

use App\Models\DossierMedical;
use App\Models\Patient;
use App\Services\Security\ClinicalAuthorizationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DossierMedicalController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DossierMedical::class, 'dossier');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = max(10, min(100, (int) $request->integer('per_page', 10)));

        $baseQuery = $this->buildIndexQuery($request);

        $dossiers = (clone $baseQuery)
            ->with('patient')
            ->latest('updated_at')
            ->paginate($perPage)
            ->appends($request->query());

        $dossiers->getCollection()->transform(function (DossierMedical $dossier) {
            $typeValue = mb_strtolower((string) ($dossier->type ?? 'general'));
            $dossier->display_type_class = str_contains($typeValue, 'urg')
                ? 'is-urgence'
                : (str_contains($typeValue, 'spec') ? 'is-specialise' : 'is-general');
            $dossier->display_status_class = ($dossier->statut ?? '') === 'archive' ? 'is-archive' : 'is-active';
            $dossier->display_numero_dossier = $dossier->numero_dossier ?: 'Numero non renseigne';
            $dossier->display_reference_meta = 'Reference de suivi du dossier';
            $dossier->display_type_label = ucfirst((string) ($dossier->type ?? 'General'));
            $dossier->display_open_date = $dossier->date_ouverture?->format('d/m/Y') ?: 'Non specifiee';
            $dossier->display_open_date_human = $dossier->date_ouverture?->diffForHumans() ?: 'Date indisponible';

            return $dossier;
        });

        $statsCollection = (clone $baseQuery)
            ->withCount('consultations')
            ->get(['id', 'patient_id', 'statut', 'diagnostic', 'observations']);

        $stats = [
            'actifs' => $statsCollection->filter(fn ($dossier) => ($dossier->statut ?? null) !== 'archive')->count(),
            'patients' => $statsCollection->pluck('patient_id')->filter()->unique()->count(),
            'consultations' => (int) $statsCollection->sum('consultations_count'),
            'urgents' => $statsCollection->filter(function ($dossier) {
                $diagnostic = mb_strtolower((string) ($dossier->diagnostic ?? ''));
                $observations = mb_strtolower((string) ($dossier->observations ?? ''));

                return str_contains($diagnostic, 'urgent') || str_contains($observations, 'urgent');
            })->count(),
        ];

        $typeOptions = DossierMedical::query()
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->orderBy('type')
            ->pluck('type')
            ->unique()
            ->values();

        return view('dossiers.index', compact('dossiers', 'stats', 'typeOptions'));
    }

    private function buildIndexQuery(Request $request): Builder
    {
        $query = DossierMedical::query()
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $search = trim((string) $request->input('search'));

                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('numero_dossier', 'like', '%' . $search . '%')
                        ->orWhere('diagnostic', 'like', '%' . $search . '%')
                        ->orWhere('observations', 'like', '%' . $search . '%')
                        ->orWhere('traitement', 'like', '%' . $search . '%')
                        ->orWhere('prescriptions', 'like', '%' . $search . '%')
                        ->orWhereHas('patient', function (Builder $patientQuery) use ($search) {
                            $patientQuery->where('nom', 'like', '%' . $search . '%')
                                ->orWhere('prenom', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($request->filled('type'), function (Builder $query) use ($request) {
                $query->where('type', (string) $request->input('type'));
            });

        if ($request->user()) {
            $query->whereHas('patient', function (Builder $patientQuery) use ($request) {
                app(ClinicalAuthorizationService::class)->scopePatients($patientQuery, $request->user());
            });
        }

        return $query;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patientsQuery = Patient::query();
        if (auth()->check()) {
            app(ClinicalAuthorizationService::class)->scopePatients($patientsQuery, auth()->user());
        }

        $patients = $patientsQuery->get();
        $statsQuery = DossierMedical::query();
        if (auth()->check()) {
            $statsQuery->whereHas('patient', function (Builder $patientQuery) {
                app(ClinicalAuthorizationService::class)->scopePatients($patientQuery, auth()->user());
            });
        }

        $createStats = [
            'patients' => $patients->count(),
            'actifs' => (clone $statsQuery)->where('statut', '!=', 'archive')->count(),
            'archives' => (clone $statsQuery)->where('statut', 'archive')->count(),
        ];

        $typeOptions = DossierMedical::query()
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->orderBy('type')
            ->pluck('type')
            ->unique()
            ->values();

        return view('dossiers.create', compact('patients', 'createStats', 'typeOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'numero_dossier' => 'required|string|max:50|unique:dossiers_medicaux,numero_dossier',
            'type' => 'nullable|string|max:50',
            'date_ouverture' => 'nullable|date',
            'observations' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'traitement' => 'nullable|string',
            'prescriptions' => 'nullable|string',
            'statut' => 'required|in:actif,archive',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        // Set defaults
        $validated['type'] = $validated['type'] ?? 'général';
        $patient = Patient::query()->findOrFail($validated['patient_id']);
        abort_unless(app(ClinicalAuthorizationService::class)->canAccessPatient($request->user(), $patient), 403);
        $validated['statut'] = $validated['statut'] ?? 'actif';

        // Handle file uploads
        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents/dossiers', $filename, 'local');
                $documentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
        }
        $validated['documents'] = $documentPaths;

        try {
            DossierMedical::create($validated);

            return redirect()->route('dossiers.index')
                ->with('success', 'Dossier médical créé avec succès !');

        } catch (\Exception $e) {
            Log::error('Medical record creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la creation du dossier.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DossierMedical $dossier)
    {
        $dossier->load('patient', 'consultations', 'ordonnances');
        return view('dossiers.show', compact('dossier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DossierMedical $dossier)
    {
        $dossier->loadCount(['consultations', 'ordonnances']);

        $patientsQuery = Patient::query();
        if (auth()->check()) {
            app(ClinicalAuthorizationService::class)->scopePatients($patientsQuery, auth()->user());
        }

        $patients = $patientsQuery->get();
        $editStats = [
            'consultations' => (int) ($dossier->consultations_count ?? 0),
            'ordonnances' => (int) ($dossier->ordonnances_count ?? 0),
            'documents' => count($dossier->documents ?? []),
        ];

        return view('dossiers.edit', compact('dossier', 'patients', 'editStats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DossierMedical $dossier)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'numero_dossier' => 'required|string|max:50|unique:dossiers_medicaux,numero_dossier,' . $dossier->id,
            'type' => 'nullable|string|max:50',
            'date_ouverture' => 'nullable|date',
            'observations' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'traitement' => 'nullable|string',
            'prescriptions' => 'nullable|string',
            'statut' => 'required|in:actif,archive',
            'documents' => 'nullable|array',
        ]);

        $validated['type'] = $validated['type'] ?? 'général';

        try {
            $patient = Patient::query()->findOrFail($validated['patient_id']);
            abort_unless(app(ClinicalAuthorizationService::class)->canAccessPatient($request->user(), $patient), 403);
            $dossier->update($validated);

            return redirect()->route('dossiers.show', $dossier->id)
                ->with('success', 'Dossier médical modifié avec succès !');

        } catch (\Exception $e) {
            Log::error('Medical record update failed', [
                'error' => $e->getMessage(),
                'dossier_id' => $dossier->id ?? null,
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification du dossier.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DossierMedical $dossier)
    {
        try {
            $dossier->delete();

            return redirect()->route('dossiers.index')
                ->with('success', 'Dossier médical supprimé avec succès !');

        } catch (\Exception $e) {
            Log::error('Medical record delete failed', [
                'error' => $e->getMessage(),
                'dossier_id' => $dossier->id ?? null,
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du dossier.');
        }
    }

    /**
     * Afficher les archives des dossiers médicaux
     */
    public function archives(Request $request)
    {
        $this->authorize('viewAny', DossierMedical::class);

        $perPage = max(10, min(100, (int) $request->integer('per_page', 20)));

        $baseQuery = $this->buildArchivesQuery($request);

        $dossiersArchives = (clone $baseQuery)
            ->with('patient')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        $statsCollection = (clone $baseQuery)
            ->get(['id', 'patient_id', 'date_ouverture', 'updated_at']);

        $oldestArchive = $statsCollection
            ->filter(fn ($dossier) => filled($dossier->date_ouverture ?? $dossier->updated_at))
            ->sortBy(fn ($dossier) => $dossier->date_ouverture ?? $dossier->updated_at)
            ->first();

        $stats = [
            'archives' => $statsCollection->count(),
            'patients' => $statsCollection->pluck('patient_id')->filter()->unique()->count(),
            'anciennete' => $oldestArchive
                ? now()->diffInDays($oldestArchive->date_ouverture ?? $oldestArchive->updated_at)
                : 0,
        ];

        return view('dossiers.archives', compact('dossiersArchives', 'stats'));
    }

    /**
     * Archiver un dossier médical (statut = archive)
     */
    public function archive(DossierMedical $dossier)
    {
        $this->authorize('update', $dossier);

        if ($dossier->statut === 'archive') {
            return redirect()->route('dossiers.show', $dossier->id)
                ->with('info', 'Le dossier est deja archive.');
        }

        $dossier->statut = 'archive';
        $dossier->save();

        return redirect()->route('dossiers.show', $dossier->id)
            ->with('success', 'Le dossier a ete archive avec succes.');
    }

    private function buildArchivesQuery(Request $request): Builder
    {
        $query = DossierMedical::query()
            ->where('statut', 'archive')
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $search = trim((string) $request->input('search'));

                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('numero_dossier', 'like', '%' . $search . '%')
                        ->orWhere('diagnostic', 'like', '%' . $search . '%')
                        ->orWhere('observations', 'like', '%' . $search . '%')
                        ->orWhereHas('patient', function (Builder $patientQuery) use ($search) {
                            $patientQuery->where('nom', 'like', '%' . $search . '%')
                                ->orWhere('prenom', 'like', '%' . $search . '%');
                        });
                });
            });

        if ($request->user()) {
            $query->whereHas('patient', function (Builder $patientQuery) use ($request) {
                app(ClinicalAuthorizationService::class)->scopePatients($patientQuery, $request->user());
            });
        }

        return $query;
    }
}
