<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Services\Exports\Utf8CsvExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedecinController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(10, min(100, (int) $request->integer('per_page', 10)));
        $statusOptions = $this->getStatusOptions();
        $selectedStatus = $request->input('status');
        $selectedSpecialite = $request->input('specialite');
        $currentPerPage = $perPage;
        $hasFilters = $request->hasAny(['search', 'status', 'specialite', 'per_page']);
        $statusLabel = null;

        if (filled($selectedStatus)) {
            $statusLabel = $statusOptions[$this->normalizeStatus((string) $selectedStatus)] ?? null;
        }

        $baseQuery = $this->buildIndexQuery($request);

        $medecins = (clone $baseQuery)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate($perPage)
            ->appends($request->query());

        $medecins->getCollection()->transform(function (Medecin $medecin) use ($statusOptions) {
            $statusKey = $this->normalizeStatus((string) ($medecin->statut ?? 'inactif'));
            $medecin->status_key = $statusKey;
            $medecin->status_label = $statusOptions[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));

            return $medecin;
        });

        $statsCollection = (clone $baseQuery)
            ->withCount('rendezvous')
            ->get(['id', 'statut', 'specialite']);

        $stats = [
            'actifs' => $statsCollection->where('statut', 'actif')->count(),
            'specialites' => $statsCollection
                ->pluck('specialite')
                ->filter(fn ($specialite) => filled($specialite))
                ->unique()
                ->count(),
            'rendezvous' => (int) $statsCollection->sum('rendezvous_count'),
            'inactifs' => $statsCollection->filter(fn ($medecin) => ($medecin->statut ?? null) !== 'actif')->count(),
        ];

        $specialites = Medecin::withTrashed()
            ->whereNotNull('specialite')
            ->where('specialite', '!=', '')
            ->orderBy('specialite')
            ->pluck('specialite')
            ->unique()
            ->values();

        return view('medecins.index', compact(
            'medecins',
            'stats',
            'specialites',
            'statusOptions',
            'selectedStatus',
            'selectedSpecialite',
            'currentPerPage',
            'hasFilters',
            'statusLabel'
        ));
    }

    public function export(Request $request, Utf8CsvExporter $csvExporter): StreamedResponse
    {
        $medecins = $this->buildIndexQuery($request)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        $rows = $medecins->map(fn ($medecin) => [
            (string) ($medecin->matricule ?? ''),
            trim(((string) ($medecin->civilite ?? '')) . ' ' . ((string) ($medecin->prenom ?? '')) . ' ' . ((string) ($medecin->nom ?? ''))),
            (string) ($medecin->specialite ?? ''),
            (string) ($medecin->telephone ?? ''),
            (string) ($medecin->email ?? ''),
            (string) ($medecin->numero_ordre ?? ''),
            (string) ($medecin->statut ?? ''),
            optional($medecin->date_embauche)->format('Y-m-d'),
        ]);

        return $csvExporter->download(
            'medecins-' . now()->format('Y-m-d-His') . '.csv',
            ['Matricule', 'Nom complet', 'Specialite', 'Telephone', 'Email', 'Numero ordre', 'Statut', 'Date embauche'],
            $rows
        );
    }

    private function buildIndexQuery(Request $request)
    {
        return Medecin::withTrashed()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));

                $query->where(function ($inner) use ($search) {
                    $inner->where('nom', 'like', '%' . $search . '%')
                        ->orWhere('prenom', 'like', '%' . $search . '%')
                        ->orWhere('matricule', 'like', '%' . $search . '%')
                        ->orWhere('specialite', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('telephone', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('statut', $this->normalizeStatus((string) $request->input('status')));
            })
            ->when($request->filled('specialite'), function ($query) use ($request) {
                $query->where('specialite', (string) $request->input('specialite'));
            });
    }

    public function create()
    {
        return view('medecins.create', [
            'specialites' => $this->getSpecialites(),
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateMedecin($request);
        $validated['matricule'] = Medecin::generateMatricule();

        $medecin = Medecin::create($validated);
        $this->storeUploads($request, $medecin);

        return redirect()
            ->route('medecins.index')
            ->with('success', 'Medecin cree avec succes.');
    }

    public function show(Medecin $medecin)
    {
        $medecin->loadCount(['consultations', 'rendezvous', 'ordonnances']);

        $statusKey = $this->normalizeStatus((string) ($medecin->statut ?? 'inactif'));
        $statusOptions = $this->getStatusOptions();
        $statusClass = 'status-' . $statusKey;
        $statusLabel = $statusOptions[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));

        return view('medecins.show', compact('medecin', 'statusClass', 'statusLabel'));
    }

    public function edit(Medecin $medecin)
    {
        $medecin->statut = $this->normalizeStatus((string) ($medecin->statut ?? 'inactif'));
        $statusClass = 'status-' . $medecin->statut;

        return view('medecins.edit', [
            'medecin' => $medecin,
            'specialites' => $this->getSpecialites(),
            'statusOptions' => $this->getStatusOptions(),
            'statusClass' => $statusClass,
        ]);
    }

    public function update(Request $request, Medecin $medecin)
    {
        $validated = $this->validateMedecin($request, $medecin);

        $medecin->update($validated);
        $this->storeUploads($request, $medecin);

        return redirect()
            ->route('medecins.show', $medecin)
            ->with('success', 'Medecin mis a jour avec succes.');
    }

    public function destroy(Medecin $medecin)
    {
        $medecin->delete();

        return redirect()
            ->route('medecins.index')
            ->with('success', 'Medecin supprime avec succes.');
    }

    private function validateMedecin(Request $request, ?Medecin $medecin = null): array
    {
        $validated = $request->validate([
            'civilite' => ['required', 'string', 'max:20'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'specialite' => ['nullable', 'string', 'max:255'],
            'numero_ordre' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('medecins', 'numero_ordre')->ignore($medecin?->id),
            ],
            'telephone' => ['nullable', 'string', 'max:30'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('medecins', 'email')->ignore($medecin?->id),
            ],
            'adresse_cabinet' => ['nullable', 'string'],
            'ville' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'statut' => ['required', Rule::in(['actif', 'inactif', 'conge', 'en_conge', 'retraite'])],
            'tarif_consultation' => ['nullable', 'numeric', 'min:0'],
            'date_embauche' => ['nullable', 'date'],
            'date_depart' => ['nullable', 'date', 'after_or_equal:date_embauche'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'signature' => ['nullable', 'image', 'mimes:png', 'max:2048'],
        ]);

        if (($validated['statut'] ?? null) === 'conge') {
            $validated['statut'] = 'en_conge';
        }

        unset($validated['photo'], $validated['signature']);

        return $validated;
    }

    private function storeUploads(Request $request, Medecin $medecin): void
    {
        if ($request->hasFile('photo')) {
            if ($medecin->photo_path) {
                Storage::disk('public')->delete($medecin->photo_path);
            }

            $medecin->photo_path = $request->file('photo')->store('medecins/photos', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($medecin->signature_path) {
                Storage::disk('public')->delete($medecin->signature_path);
            }

            $medecin->signature_path = $request->file('signature')->store('medecins/signatures', 'public');
        }

        if ($medecin->isDirty(['photo_path', 'signature_path'])) {
            $medecin->save();
        }
    }

    private function getSpecialites(): array
    {
        return [
            'Medecine generale',
            'Cardiologie',
            'Dermatologie',
            'Hematologie',
            'Oncologie medicale',
            'Pediatrie',
            'Neurologie',
            'Psychiatrie',
            'Ophtalmologie',
            'Traumatologie',
            'Urologie',
            'Gynecologie',
            'Parodontologie',
            'Radiologie',
            'Nutrition',
        ];
    }

    private function getStatusOptions(): array
    {
        return [
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'en_conge' => 'En conge',
            'retraite' => 'Retraite',
        ];
    }

    private function normalizeStatus(string $status): string
    {
        return $status === 'conge' ? 'en_conge' : $status;
    }
}