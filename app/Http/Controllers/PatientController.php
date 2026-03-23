<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Medecin;
use App\Models\Medicament;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\PatientsCsvExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = max(10, min(100, (int) $request->integer('per_page', 15)));

        $patients = Patient::query()
            ->select([
                'id',
                'numero_dossier',
                'nom',
                'prenom',
                'telephone',
                'email',
                'cin',
                'date_naissance',
                'genre',
                'photo',
                'created_at',
            ])
            ->when($request->filled('search'), function($query) use ($request) {
                $search = $request->search;
                return $query->where(function($q) use ($search) {
                    $q->where('nom', 'LIKE', '%' . $search . '%')
                      ->orWhere('prenom', 'LIKE', '%' . $search . '%')
                      ->orWhere('telephone', 'LIKE', '%' . $search . '%')
                      ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($request->filled('gender'), function($query) use ($request) {
                return $query->where('genre', $request->gender);
            })
            ->when($request->filled('status'), function($query) use ($request) {
                if ($request->status == 'actif') {
                    return $query->where('is_draft', false);
                } elseif ($request->status == 'archive') {
                    return $query->where('is_draft', true);
                }
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $totalActivePatients = Patient::query()
            ->where('is_draft', false)
            ->count();

        $todayAppointments = DB::table('rendez_vous')
            ->where('date_heure', '>=', now()->startOfDay())
            ->where('date_heure', '<', now()->endOfDay())
            ->whereNotIn('statut', ['annule', 'annulee'])
            ->count();

        $medicalRecords = DB::table('dossier_medicals')->count();

        $medicalAlerts = Patient::query()
            ->whereNotNull('allergies')
            ->where('allergies', '!=', '')
            ->count();

        return view('patients.index', compact(
            'patients',
            'totalActivePatients',
            'todayAppointments',
            'medicalRecords',
            'medicalAlerts',
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Regles de validation avec valeurs par defaut pour champs non obligatoires
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'genre' => 'required|in:M,F',
            'cin' => 'nullable|string|max:20|unique:patients,cin',
            'telephone' => 'required|string|max:20|unique:patients,telephone',
            'email' => 'nullable|email|unique:patients,email',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:20',
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'assurance_medicale' => 'nullable|string|max:255',
            'assurance_autre' => 'required_if:assurance_medicale,Autre|nullable|string|max:255',
            'antecedents_medicaux' => 'nullable|string',
            'allergies' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // CORRECTION : Utiliser des valeurs par defaut pour les champs NULL
        $validated['adresse'] = $validated['adresse'] ?? '';
        $validated['ville'] = $validated['ville'] ?? '';
        $validated['code_postal'] = $validated['code_postal'] ?? '';
        $validated['assurance_medicale'] = $validated['assurance_medicale'] ?? '';
        $validated['antecedents_medicaux'] = $validated['antecedents_medicaux'] ?? '';
        $validated['allergies'] = $validated['allergies'] ?? '';
        $validated['antecedents'] = $validated['antecedents_medicaux'];

        $finalAssurance = ($validated['assurance_medicale'] ?? '') === 'Autre'
            ? trim((string) ($validated['assurance_autre'] ?? ''))
            : trim((string) ($validated['assurance_medicale'] ?? ''));

        $validated['assurance'] = $finalAssurance;

        // CORRECTION : Verifier que le nom a au moins 3 caracteres
        if (strlen($validated['nom']) >= 3) {
            $prefix = strtoupper(substr($validated['nom'], 0, 3));
        } else {
            $prefix = strtoupper(str_pad($validated['nom'], 3, 'X'));
        }

        $date = date('YmdHis');
        $validated['numero_dossier'] = 'PAT-' . $prefix . '-' . $date;

        // CORRECTION : hasFile('photo') pas hasfile['photo']
        if ($request->hasFile('photo')) { // CORRECTION ICI
            $photoPath = $request->file('photo')->store('patients', 'public'); // CORRECTION ICI
            $validated['photo'] = $photoPath;
        }

        // Gerer le mode brouillon
        $validated['is_draft'] = $request->has('is_draft') ? true : false;

        // CORRECTION : Assurer que toutes les colonnes necessaires sont presentes
        $patientData = array_merge([
            'adresse' => '',
            'ville' => '',
            'code_postal' => '',
            'groupe_sanguin' => null,
            'assurance' => '',
            'antecedents' => '',
            'allergies' => '',
            'photo' => null,
            'is_draft' => false,
        ], $validated);

        unset($patientData['assurance_medicale'], $patientData['assurance_autre'], $patientData['antecedents_medicaux']);

        // CORRECTION : Patient::create($patientData) pas $validated
        $patient = Patient::create($patientData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Patient cree avec succes',
                'patient' => $patient
            ]);
        }

        return redirect()->route('patients.index')
            ->with('success', 'Patient cree avec succes');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $patient->loadCount([
            'consultations',
            'ordonnances',
            'rendezvous as upcoming_rendezvous_count' => function ($query) {
                $query->where('date_heure', '>=', now()->startOfDay())
                    ->whereNotIn('statut', ['annule', 'annulee', 'annulee', 'termine', 'terminee']);
            },
        ]);

        $lastConsultationAt = $patient->consultations()
            ->whereNotNull('date_consultation')
            ->latest('date_consultation')
            ->value('date_consultation');

        $birthDate = $this->parseBirthDate($patient->date_naissance);
        $age = $birthDate?->age;
        $genreLabel = $patient->genre === 'M' ? 'Masculin' : 'Féminin';
        $consultationsCount = (int) ($patient->consultations_count ?? 0);
        $upcomingRendezVousCount = (int) ($patient->upcoming_rendezvous_count ?? 0);
        $prescriptionsCount = (int) ($patient->ordonnances_count ?? 0);
        $profileCompletion = $this->calculateProfileCompletion([
            $patient->cin,
            $birthDate,
            $patient->telephone,
            $patient->email,
            $patient->adresse,
            $patient->ville,
            $patient->code_postal,
            $patient->groupe_sanguin,
            $patient->assurance,
            $patient->antecedents,
        ]);
        $medecins = Medecin::query()
            ->select(['id', 'civilite', 'nom', 'prenom', 'specialite', 'email'])
            ->orderBy('prenom')
            ->orderBy('nom')
            ->get();
        $currentMedecin = $this->resolveCurrentMedecin($medecins);
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

        return view('patients.show', compact(
            'patient',
            'lastConsultationAt',
            'birthDate',
            'age',
            'genreLabel',
            'consultationsCount',
            'upcomingRendezVousCount',
            'prescriptionsCount',
            'profileCompletion',
            'medecins',
            'currentMedecin',
            'medicamentCatalogData',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $patient->loadCount('dossiers');

        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'cin' => 'nullable|string|max:20|unique:patients,cin,' . $patient->id,
            'date_naissance' => 'required|date',
            'genre' => 'required|in:M,F',
            'etat_civil' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:20',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100|unique:patients,email,' . $patient->id,
            'contact_urgence' => 'nullable|string|max:100',
            'telephone_urgence' => 'nullable|string|max:20',
            'groupe_sanguin' => 'nullable|string|max:10',
            'assurance_medicale' => 'nullable|string|max:255',
            'assurance_autre' => 'required_if:assurance_medicale,Autre|nullable|string|max:255',
            'allergies' => 'nullable|string',
            'antecedents' => 'nullable|string',
            'traitements' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $validated['assurance'] = ($validated['assurance_medicale'] ?? '') === 'Autre'
            ? trim((string) ($validated['assurance_autre'] ?? ''))
            : trim((string) ($validated['assurance_medicale'] ?? ''));

        unset($validated['assurance_medicale'], $validated['assurance_autre']);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient->id)
            ->with('success', 'Patient modifie avec succes!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        DB::transaction(function () use ($patient) {
            $archive = $patient->archive;

            if ($archive) {
                foreach ($archive->documents as $document) {
                    if ($document->chemin_fichier) {
                        Storage::disk('public')->delete($document->chemin_fichier);
                    }

                    $document->delete();
                }

                $archive->delete();
            }

            if ($patient->photo) {
                Storage::disk('public')->delete($patient->photo);
            }

            Examen::where('patient_id', $patient->id)->delete();

            $patient->delete();
        });

        return redirect()->route('patients.index')
            ->with('success', 'Patient supprime avec succes.');
    }

    /**
     * Export patients to CSV
     */
    public function export(Request $request, PatientsCsvExportService $csvService)
    {
        $patients = Patient::query()
            ->select([
                'id',
                'numero_dossier',
                'nom',
                'prenom',
                'telephone',
                'email',
                'cin',
                'date_naissance',
                'genre',
                'is_draft',
            ])
            ->when($request->filled('search'), function($query) use ($request) {
                $search = $request->search;
                return $query->where(function($q) use ($search) {
                    $q->where('nom', 'LIKE', '%' . $search . '%')
                      ->orWhere('prenom', 'LIKE', '%' . $search . '%')
                      ->orWhere('telephone', 'LIKE', '%' . $search . '%')
                      ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($request->filled('gender'), function($query) use ($request) {
                return $query->where('genre', $request->gender);
            })
            ->when($request->filled('status'), function($query) use ($request) {
                if ($request->status == 'actif') {
                    return $query->where('is_draft', false);
                } elseif ($request->status == 'archive') {
                    return $query->where('is_draft', true);
                }
            })
            ->latest()
            ->get();

        $delimiter = $csvService->delimiter();
        $filename = 'patients-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($patients, $csvService, $delimiter) {
            $file = fopen('php://output', 'w');

            if ($file === false) {
                return;
            }

            fwrite($file, "\xEF\xBB\xBF");
            $csvService->writeRows($file, $patients, $delimiter);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function parseBirthDate($date): ?\Carbon\Carbon
    {
        if (blank($date)) {
            return null;
        }

        try {
            return $date instanceof \Carbon\Carbon
                ? $date
                : \Carbon\Carbon::parse($date);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function calculateProfileCompletion(array $fields): int
    {
        $filled = collect($fields)->filter(fn ($value) => filled($value))->count();

        return (int) round(($filled / max(1, count($fields))) * 100);
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

