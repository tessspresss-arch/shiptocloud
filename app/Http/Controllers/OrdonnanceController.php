<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Medicament;
use App\Models\ModeleOrdonnance;
use App\Models\Ordonnance;
use App\Models\Patient;
use App\Services\Pdf\PdfBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class OrdonnanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordonnances = Ordonnance::with(['patient', 'medecin', 'consultation.medecin'])->paginate(10);

        $currentPageItems = $ordonnances->getCollection()->map(function (Ordonnance $ordonnance) {
            $linkedMedecin = optional($ordonnance->consultation)->medecin ?? $ordonnance->medecin;
            $expirationDate = $ordonnance->date_expiration ? \Carbon\Carbon::parse($ordonnance->date_expiration) : null;
            $daysUntilExpiry = $expirationDate?->diffInDays(now(), false);
            $statutValue = (string) ($ordonnance->statut ?? 'active');

            $ordonnance->display_medecin_name = $linkedMedecin
                ? 'Dr. ' . trim(($linkedMedecin->nom ?? '') . ' ' . ($linkedMedecin->prenom ?? ''))
                : null;
            $ordonnance->display_medecin_specialite = $linkedMedecin?->specialite ?: 'Specialite non renseignee';
            $ordonnance->display_date_prescription = $ordonnance->date_prescription
                ? \Carbon\Carbon::parse($ordonnance->date_prescription)->format('d/m/Y')
                : '-';
            $ordonnance->display_date_expiration = $expirationDate?->format('d/m/Y');
            $ordonnance->display_expiration_note = match (true) {
                $expirationDate === null => "Aucune date d'expiration",
                $daysUntilExpiry < 0 => 'Expire dans ' . abs((int) $daysUntilExpiry) . ' jour(s)',
                $daysUntilExpiry === 0 => "Expire aujourd'hui",
                default => 'Expiree',
            };
            $ordonnance->display_expiration_tone = match (true) {
                $expirationDate === null => '',
                $daysUntilExpiry < 0 => 'danger',
                $daysUntilExpiry === 0 => 'warning',
                default => 'success',
            };
            $ordonnance->display_statut_class = in_array($statutValue, ['active', 'expiree', 'annulee'], true)
                ? 'ord-status-' . $statutValue
                : 'ord-status-default';
            $ordonnance->display_statut_text = ucfirst($statutValue);

            return $ordonnance;
        });

        $ordonnances->setCollection($currentPageItems);
        $activeCount = $currentPageItems->where('statut', 'active')->count();
        $expiredCount = $currentPageItems->where('statut', 'expiree')->count();
        $cancelledCount = $currentPageItems->where('statut', 'annulee')->count();

        return view('ordonnances.index', compact('ordonnances', 'activeCount', 'expiredCount', 'cancelledCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return $this->renderForm($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $supportsMedecinColumn = $this->supportsOrdonnanceColumn('medecin_id');
        $validated = $request->validate($this->ordonnanceRules($supportsMedecinColumn));
        $validated['medicaments'] = $this->normalizeMedicationRows($validated['medicaments'] ?? []);
        $this->ensureConsultationMatchesContext($validated, $supportsMedecinColumn);

        $payload = [
            'numero_ordonnance' => $this->generateNumeroOrdonnance(),
            'patient_id' => $validated['patient_id'],
            'consultation_id' => $validated['consultation_id'] ?? null,
            'date_prescription' => $validated['date_prescription'],
            'diagnostic' => $validated['diagnostic'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'medicaments' => $validated['medicaments'],
            'statut' => $validated['statut'] ?? 'active',
            'imprimee' => (bool) ($validated['imprimee'] ?? false),
        ];

        if ($supportsMedecinColumn) {
            $payload['medecin_id'] = $validated['medecin_id'];
        }

        $ordonnance = Ordonnance::create($this->filterExistingOrdonnanceColumns($payload));

        if ($request->expectsJson()) {
            $patientOrdonnancesCount = Ordonnance::query()
                ->where('patient_id', $validated['patient_id'])
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Ordonnance creee avec succes.',
                'ordonnance' => [
                    'id' => $ordonnance->id,
                    'numero_ordonnance' => $ordonnance->numero_ordonnance,
                ],
                'patient_id' => (int) $validated['patient_id'],
                'ordonnances_count' => $patientOrdonnancesCount,
                'prescriptions_count' => $patientOrdonnancesCount,
            ]);
        }

        if ($request->boolean('print_after_save')) {
            return redirect()->route('ordonnances.show', ['ordonnance' => $ordonnance->id, 'print' => 1]);
        }

        return redirect()
            ->route('ordonnances.index')
            ->with('success', 'Ordonnance creee avec succes.');
    }

    /**
     * Preview a prescription as PDF without saving it.
     */
    public function previewPdf(Request $request, PdfBuilder $pdfBuilder)
    {
        $supportsMedecinColumn = $this->supportsOrdonnanceColumn('medecin_id');
        $validated = $request->validate($this->ordonnanceRules($supportsMedecinColumn));
        $validated['medicaments'] = $this->normalizeMedicationRows($validated['medicaments'] ?? []);
        $this->ensureConsultationMatchesContext($validated, $supportsMedecinColumn);

        $patient = Patient::query()->findOrFail($validated['patient_id']);
        $medecin = ($supportsMedecinColumn && !empty($validated['medecin_id']))
            ? Medecin::query()->find($validated['medecin_id'])
            : null;

        $medicationRows = $this->hydrateMedicationRows($validated['medicaments']);

        $pdf = $pdfBuilder->fromView('ordonnances.pdf', [
            'ordonnanceNumber' => 'Apercu provisoire',
            'patientName' => trim($patient->prenom . ' ' . $patient->nom) ?: 'Patient non renseigne',
            'patientIdentifier' => $patient->numero_dossier ?: ('PAT-' . $patient->id),
            'doctorName' => $medecin
                ? trim(($medecin->civilite ?: 'Dr.') . ' ' . $medecin->prenom . ' ' . $medecin->nom)
                : 'Medecin a confirmer',
            'doctorSpeciality' => $medecin?->specialite,
            'datePrescription' => $validated['date_prescription'],
            'diagnostic' => $validated['diagnostic'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'medicationRows' => $medicationRows,
            'isPreview' => true,
        ]);

        return $pdf->download('ordonnance-apercu-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Show the form for creating or editing a prescription.
     */
    private function renderForm(Request $request, ?Ordonnance $ordonnance = null)
    {
        $ordonnance?->loadMissing(['patient', 'medecin', 'consultation.patient', 'consultation.medecin']);

        $selectedPatientIdFromContext = $ordonnance?->patient_id ?: $request->input('patient_id');

        $patients = Patient::query()
            ->select([
                'id',
                'numero_dossier',
                'nom',
                'prenom',
                'date_naissance',
                'telephone',
                'email',
                'allergies',
                'traitements',
                'notes',
            ])
            ->orderBy('prenom')
            ->orderBy('nom')
            ->get();

        $medecins = Medecin::query()
            ->select(['id', 'civilite', 'nom', 'prenom', 'specialite', 'email', 'telephone'])
            ->orderBy('prenom')
            ->orderBy('nom')
            ->get();

        $medicaments = Medicament::query()
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
            ->get();

        if ($medicaments->isEmpty()) {
            $medicaments = collect($this->fallbackMedicationCatalog())->map(fn (array $item) => new Medicament($item));
        }

        $consultations = Consultation::with([
            'patient:id,numero_dossier,nom,prenom',
            'medecin:id,civilite,nom,prenom,specialite',
        ])
            ->when(
                $selectedPatientIdFromContext,
                fn ($query) => $query->where('patient_id', $selectedPatientIdFromContext),
                fn ($query) => $query
            )
            ->latest('date_consultation')
            ->take($selectedPatientIdFromContext ? 50 : 20)
            ->get();

        $selectedConsultation = null;
        if ($request->filled('consultation_id') || $ordonnance?->consultation_id) {
            $selectedConsultation = Consultation::with([
                'patient:id,numero_dossier,nom,prenom,date_naissance,allergies,traitements',
                'medecin:id,civilite,nom,prenom,specialite,email,telephone',
            ])->find($request->integer('consultation_id', $ordonnance?->consultation_id));
        }

        if ($selectedConsultation && !$consultations->contains('id', $selectedConsultation->id)) {
            $consultations->prepend($selectedConsultation);
        }

        if (!$selectedPatientIdFromContext && $ordonnance?->patient_id) {
            $selectedPatientIdFromContext = $ordonnance->patient_id;
        }

        $currentMedecin = $this->resolveCurrentMedecin($medecins);
        $defaultPatientId = old('patient_id', $selectedConsultation?->patient_id ?: $selectedPatientIdFromContext);
        $defaultMedecinId = old(
            'medecin_id',
            $ordonnance?->medecin_id
                ?: $currentMedecin?->id
                ?: $selectedConsultation?->medecin_id
                ?: $request->input('medecin_id')
        );
        $defaultConsultationId = old('consultation_id', $selectedConsultation?->id ?: $ordonnance?->consultation_id);

        $ordonnanceTemplates = $this->availableTemplatesFor($currentMedecin);

        $patientDirectoryData = $patients->map(function (Patient $patient): array {
            $allergies = trim((string) $patient->allergies);
            $traitements = trim((string) $patient->traitements);
            $notes = trim((string) $patient->notes);

            return [
                'id' => $patient->id,
                'label' => trim($patient->prenom . ' ' . $patient->nom),
                'search' => Str::lower(implode(' ', array_filter([
                    $patient->prenom,
                    $patient->nom,
                    $patient->numero_dossier,
                    $patient->telephone,
                ]))),
                'numero_dossier' => $patient->numero_dossier ?: 'Sans dossier',
                'age' => $patient->date_naissance?->age,
                'allergies' => $allergies !== '' ? $allergies : null,
                'traitements' => $traitements !== '' ? $traitements : null,
                'notes' => $notes !== '' ? Str::limit($notes, 180) : null,
                'telephone' => $patient->telephone,
                'email' => $patient->email,
            ];
        })->values();

        $medicamentCatalogData = $medicaments->map(function (Medicament $medicament): array {
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
        })->values();

        $consultationDirectoryData = $consultations->map(function (Consultation $consultation): array {
            $patient = $consultation->patient;
            $medecin = $consultation->medecin;

            return [
                'id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'medecin_id' => $consultation->medecin_id,
                'label' => trim(implode(' | ', array_filter([
                    optional($consultation->date_consultation)->format('d/m/Y'),
                    $patient ? trim($patient->prenom . ' ' . $patient->nom) : null,
                    $medecin ? trim($medecin->prenom . ' ' . $medecin->nom) : null,
                ]))),
            ];
        })->values();

        $templateCatalogData = $ordonnanceTemplates->map(function (ModeleOrdonnance $template): array {
            $plainText = trim(strip_tags((string) $template->contenu_html));
            $medications = collect($template->medicaments_template ?? [])
                ->map(function (array $row): array {
                    return [
                        'medicament_id' => $row['medicament_id'] ?? null,
                        'medicament_label' => $row['medicament_label'] ?? null,
                        'posologie' => $row['posologie'] ?? '',
                        'duree' => $row['duree'] ?? '',
                        'quantite' => $row['quantite'] ?? '',
                        'instructions' => $row['instructions'] ?? '',
                    ];
                })
                ->values()
                ->all();

            return [
                'id' => $template->id,
                'name' => $template->nom,
                'category' => $template->categorie,
                'diagnostic' => $template->diagnostic_contexte ?: $template->nom,
                'instructions' => $template->instructions_generales ?: $plainText,
                'medications' => $medications,
                'content' => $plainText,
                'preview' => Str::limit($plainText, 180),
                'scope' => $template->est_template_general ? 'general' : 'personnel',
            ];
        })->values();

        $isEditing = (bool) ($ordonnance && $ordonnance->exists);
        $initialDateValue = old('date_prescription', $isEditing && $ordonnance->date_prescription ? $ordonnance->date_prescription->format('Y-m-d') : date('Y-m-d'));
        $initialDiagnostic = old('diagnostic', $isEditing ? ($ordonnance->diagnostic ?? '') : '');
        $initialInstructions = old('instructions', $isEditing ? ($ordonnance->instructions ?? '') : '');
        $selectedPatientId = (string) old('patient_id', $defaultPatientId ?? '');
        $selectedMedecinId = (string) old('medecin_id', $defaultMedecinId ?? '');
        $selectedConsultationId = (string) old('consultation_id', $defaultConsultationId ?? '');
        $selectedPatient = collect($patientDirectoryData)->firstWhere('id', (int) $selectedPatientId);
        $selectedMedecin = $medecins->firstWhere('id', (int) $selectedMedecinId) ?? $currentMedecin;
        $medicamentLabelMap = collect($medicamentCatalogData)->mapWithKeys(fn ($item) => [$item['id'] => $item['label']]);
        $templateCount = $templateCatalogData->count();
        $initialDoctorPayload = $selectedMedecin ? [
            'id' => $selectedMedecin->id,
            'name' => trim(($selectedMedecin->civilite ?? 'Dr.') . ' ' . $selectedMedecin->prenom . ' ' . $selectedMedecin->nom),
            'specialite' => $selectedMedecin->specialite ?: 'Medecin generaliste',
        ] : null;
        $existingMedicationRows = $isEditing && is_array($ordonnance->medicaments ?? null)
            ? $ordonnance->medicaments
            : [[
                'medicament_id' => '',
                'posologie' => '',
                'duree' => '',
                'quantite' => '',
                'instructions' => '',
            ]];
        $prescriptionRows = collect(old('medicaments', $existingMedicationRows))
            ->values()
            ->map(function ($row) use ($medicamentLabelMap) {
                $row = is_array($row) ? $row : [];
                $row['display_label'] = $medicamentLabelMap[(int) ($row['medicament_id'] ?? 0)] ?? ($row['medicament_label'] ?? '');

                return $row;
            });
        $formAction = $isEditing ? route('ordonnances.update', $ordonnance) : route('ordonnances.store');

        return view('ordonnances.create', compact(
            'ordonnance',
            'patients',
            'medecins',
            'medicaments',
            'consultations',
            'defaultPatientId',
            'defaultMedecinId',
            'defaultConsultationId',
            'currentMedecin',
            'ordonnanceTemplates',
            'patientDirectoryData',
            'medicamentCatalogData',
            'consultationDirectoryData',
            'templateCatalogData',
            'isEditing',
            'initialDateValue',
            'initialDiagnostic',
            'initialInstructions',
            'selectedPatientId',
            'selectedMedecinId',
            'selectedConsultationId',
            'selectedPatient',
            'selectedMedecin',
            'medicamentLabelMap',
            'templateCount',
            'initialDoctorPayload',
            'prescriptionRows',
            'formAction'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ordonnance $ordonnance)
    {
        $ordonnance->load(['patient', 'medecin', 'consultation.medecin']);

        $medicamentIds = collect($ordonnance->medicaments ?? [])
            ->pluck('medicament_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $medicamentNames = Medicament::query()
            ->whereIn('id', $medicamentIds)
            ->pluck('nom_commercial', 'id');

        $displayMedecin = $ordonnance->medecin ?: optional($ordonnance->consultation)->medecin;

        return view('ordonnances.show', compact('ordonnance', 'medicamentNames', 'displayMedecin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ordonnance $ordonnance)
    {
        return $this->renderForm(request(), $ordonnance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ordonnance $ordonnance)
    {
        $supportsMedecinColumn = $this->supportsOrdonnanceColumn('medecin_id');

        $validated = $request->validate($this->ordonnanceRules($supportsMedecinColumn));
        $this->ensureConsultationMatchesContext($validated, $supportsMedecinColumn);

        $payload = [
            'patient_id' => $validated['patient_id'],
            'consultation_id' => $validated['consultation_id'] ?? null,
            'date_prescription' => $validated['date_prescription'],
            'diagnostic' => $validated['diagnostic'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'medicaments' => $validated['medicaments'],
            'statut' => $validated['statut'] ?? $ordonnance->statut,
            'imprimee' => (bool) ($validated['imprimee'] ?? false),
        ];

        if ($supportsMedecinColumn) {
            $payload['medecin_id'] = $validated['medecin_id'];
        }

        $ordonnance->update($this->filterExistingOrdonnanceColumns($payload));

        return redirect()
            ->route('ordonnances.index')
            ->with('success', 'Ordonnance mise a jour avec succes.');
    }

    /**
     * Download a saved prescription as PDF.
     */
    public function downloadPdf(Ordonnance $ordonnance, PdfBuilder $pdfBuilder)
    {
        $ordonnance->load(['patient', 'medecin', 'consultation.medecin']);

        $medicationRows = $this->hydrateMedicationRows(is_array($ordonnance->medicaments) ? $ordonnance->medicaments : []);
        $displayMedecin = $ordonnance->medecin ?: optional($ordonnance->consultation)->medecin;

        $pdf = $pdfBuilder->fromView('ordonnances.pdf', [
            'ordonnanceNumber' => $ordonnance->numero_ordonnance,
            'patientName' => trim(optional($ordonnance->patient)->prenom . ' ' . optional($ordonnance->patient)->nom) ?: 'Patient non renseigne',
            'patientIdentifier' => optional($ordonnance->patient)->numero_dossier ?: ('PAT-' . $ordonnance->patient_id),
            'doctorName' => $displayMedecin
                ? trim(($displayMedecin->civilite ?: 'Dr.') . ' ' . $displayMedecin->prenom . ' ' . $displayMedecin->nom)
                : 'Medecin non renseigne',
            'doctorSpeciality' => $displayMedecin?->specialite,
            'datePrescription' => optional($ordonnance->date_prescription)?->format('Y-m-d'),
            'diagnostic' => $ordonnance->diagnostic,
            'instructions' => $ordonnance->instructions,
            'medicationRows' => $medicationRows,
            'isPreview' => false,
        ]);

        return $pdf->download('ordonnance-' . ($ordonnance->numero_ordonnance ?: $ordonnance->id) . '.pdf');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ordonnance $ordonnance)
    {
        $ordonnance->delete();

        return redirect()
            ->route('ordonnances.index')
            ->with('success', 'Ordonnance supprimee avec succes.');
    }

    private function generateNumeroOrdonnance(): string
    {
        do {
            $numero = 'ORD-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(4));
        } while (Ordonnance::withTrashed()->where('numero_ordonnance', $numero)->exists());

        return $numero;
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

    private function availableTemplatesFor(?Medecin $currentMedecin)
    {
        return ModeleOrdonnance::query()
            ->actifs()
            ->when(
                $currentMedecin,
                function ($query) use ($currentMedecin) {
                    $query->where(function ($templateQuery) use ($currentMedecin) {
                        $templateQuery
                            ->where('est_template_general', true)
                            ->orWhere('medecin_id', $currentMedecin->id);
                    });
                },
                function ($query) {
                    $query->where('est_template_general', true);
                }
            )
            ->orderByDesc('est_template_general')
            ->orderBy('nom')
            ->get();
    }

    private function ordonnanceRules(bool $supportsMedecinColumn): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => ($supportsMedecinColumn ? 'required' : 'nullable') . '|exists:medecins,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'date_prescription' => 'required|date',
            'diagnostic' => 'nullable|string',
            'instructions' => 'nullable|string',
            'statut' => 'nullable|string|max:50',
            'imprimee' => 'nullable|boolean',
            'medicaments' => 'required|array|min:1',
            'medicaments.*.medicament_id' => 'nullable|exists:medicaments,id',
            'medicaments.*.medicament_label' => 'nullable|string|max:255',
            'medicaments.*.posologie' => 'required|string',
            'medicaments.*.duree' => 'required|string',
            'medicaments.*.quantite' => 'nullable|string',
            'medicaments.*.instructions' => 'nullable|string',
        ];
    }

    private function normalizeMedicationRows(array $rows): array
    {
        $normalized = collect($rows)
            ->map(function (array $row): array {
                $medicamentId = $row['medicament_id'] ?? null;
                $medicamentLabel = trim((string) ($row['medicament_label'] ?? ''));

                return [
                    'medicament_id' => $medicamentId !== '' ? $medicamentId : null,
                    'medicament_label' => $medicamentLabel !== '' ? $medicamentLabel : null,
                    'posologie' => trim((string) ($row['posologie'] ?? '')),
                    'duree' => trim((string) ($row['duree'] ?? '')),
                    'quantite' => trim((string) ($row['quantite'] ?? '')),
                    'instructions' => trim((string) ($row['instructions'] ?? '')),
                ];
            })
            ->filter(fn (array $row): bool => $row['medicament_id'] !== null || $row['medicament_label'] !== null || $row['posologie'] !== '' || $row['duree'] !== '')
            ->values();

        foreach ($normalized as $index => $row) {
            if ($row['medicament_id'] === null && $row['medicament_label'] === null) {
                throw ValidationException::withMessages([
                    "medicaments.$index.medicament_label" => 'Selectionnez un medicament ou saisissez son libelle.',
                ]);
            }
        }

        return $normalized->all();
    }

    private function ensureConsultationMatchesContext(array $validated, bool $supportsMedecinColumn): void
    {
        if (empty($validated['consultation_id'])) {
            return;
        }

        $consultation = Consultation::query()
            ->select(['id', 'patient_id', 'medecin_id'])
            ->find($validated['consultation_id']);

        if (!$consultation) {
            return;
        }

        $errors = [];

        if ((int) $consultation->patient_id !== (int) $validated['patient_id']) {
            $errors['consultation_id'] = 'La consultation selectionnee ne correspond pas au patient choisi.';
        }

        if (
            $supportsMedecinColumn
            && !empty($validated['medecin_id'])
            && $consultation->medecin_id
            && (int) $consultation->medecin_id !== (int) $validated['medecin_id']
        ) {
            $errors['medecin_id'] = 'Le medecin selectionne ne correspond pas a la consultation choisie.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function hydrateMedicationRows(array $rows): array
    {
        $medicamentIds = collect($rows)
            ->pluck('medicament_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $medicamentNames = Medicament::query()
            ->whereIn('id', $medicamentIds)
            ->pluck('nom_commercial', 'id');

        return collect($rows)
            ->map(function (array $row) use ($medicamentNames): array {
                return [
                    'medicament' => $medicamentNames[$row['medicament_id'] ?? null]
                        ?? ($row['medicament_label'] ?? ('ID: ' . ($row['medicament_id'] ?? '-'))),
                    'posologie' => $row['posologie'] ?? null,
                    'duree' => $row['duree'] ?? null,
                    'quantite' => $row['quantite'] ?? null,
                    'instructions' => $row['instructions'] ?? null,
                ];
            })
            ->all();
    }

    private function fallbackMedicationCatalog(): array
    {
        $csvPath = database_path('data/medicaments_base.csv');
        if (!File::exists($csvPath)) {
            return [];
        }

        $lines = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return [];
        }

        $header = null;
        $rows = [];

        foreach ($lines as $line) {
            $columns = str_getcsv($line, ';');
            if ($header === null) {
                $header = $columns;
                continue;
            }

            $row = array_combine($header, $columns);
            if (!is_array($row)) {
                continue;
            }

            $rows[] = [
                'id' => null,
                'nom_commercial' => trim((string) ($row['nom_commercial'] ?? $row['nom'] ?? '')),
                'dci' => trim((string) ($row['dci'] ?? '')),
                'presentation' => trim((string) ($row['presentation'] ?? '')),
                'posologie' => trim((string) ($row['posologie'] ?? '')),
                'voie_administration' => trim((string) ($row['voie_administration'] ?? '')),
                'classe_therapeutique' => trim((string) ($row['classe_therapeutique'] ?? '')),
            ];
        }

        return array_values(array_filter($rows, fn (array $row): bool => $row['nom_commercial'] !== ''));
    }

    private function filterExistingOrdonnanceColumns(array $payload): array
    {
        return array_filter(
            $payload,
            fn (string $column): bool => $this->supportsOrdonnanceColumn($column),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function supportsOrdonnanceColumn(string $column): bool
    {
        $columns = Cache::rememberForever('schema.columns.ordonnances.v1', function () {
            try {
                return array_flip(Schema::getColumnListing('ordonnances'));
            } catch (\Throwable) {
                return [];
            }
        });

        return isset($columns[$column]);
    }
}
