<?php

namespace App\Http\Controllers;

use App\Models\CategorieDocument;
use App\Models\DocumentMedical;
use App\Models\Patient;
use App\Models\PatientArchive;
use App\Services\Security\ClinicalAuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DocumentMedical::class, 'document');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        CategorieDocument::ensureDefaultCatalog();

        $documentsQuery = DocumentMedical::with(['categorie', 'archive.patient']);
        if (auth()->check()) {
            $access = app(ClinicalAuthorizationService::class);
            $documentsQuery->whereHas('archive.patient', function ($patientQuery) use ($access) {
                $access->scopePatients($patientQuery, auth()->user());
            });
        }

        $documents = $documentsQuery
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalDocuments = (clone $documentsQuery)->count();
        $totalCategories = CategorieDocument::query()
            ->actives()
            ->count();
        $usedCategories = DocumentMedical::whereNotNull('categorie_document_id')
            ->distinct('categorie_document_id')
            ->count('categorie_document_id');
        $totalBytes = (int) DocumentMedical::sum('taille_fichier');
        $documents->getCollection()->transform(function (DocumentMedical $document) {
            $patient = $document->archive?->patient;
            $document->display_patient_name = $patient
                ? trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? ''))
                : null;
            $document->display_patient_dossier = $patient?->numero_dossier;
            $document->display_category_name = $document->categorie->nom ?? 'Non classe';
            $document->display_source_label = ($document->source_document ?? 'telechargement') === 'scan_cabinet'
                ? 'Scan cabinet'
                : 'Televersement';
            $document->display_extension = strtoupper((string) ($document->extension ?? pathinfo($document->nom_original, PATHINFO_EXTENSION)));
            $document->display_size_label = $this->formatBytes($document->taille_fichier);

            return $document;
        });
        $totalBytesLabel = $this->formatBytes($totalBytes);

        return view('documents.index', compact(
            'documents',
            'totalDocuments',
            'totalCategories',
            'usedCategories',
            'totalBytes',
            'totalBytesLabel'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        CategorieDocument::ensureDefaultCatalog();

        $categoriesActive = CategorieDocument::query()
            ->actives()
            ->ordered()
            ->get();

        $documentsByCategory = DocumentMedical::query()
            ->selectRaw('categorie_document_id, COUNT(*) as total')
            ->whereNotNull('categorie_document_id')
            ->groupBy('categorie_document_id')
            ->pluck('total', 'categorie_document_id');

        $patientsQuery = Patient::query()
            ->select('id', 'nom', 'prenom', 'numero_dossier')
            ->orderBy('nom')
            ->orderBy('prenom');

        if ($request->user()) {
            app(ClinicalAuthorizationService::class)->scopePatients($patientsQuery, $request->user());
        }

        $patients = $patientsQuery->get();

        $selectedPatient = null;
        if ($request->filled('patient_id')) {
            $selectedPatient = $patients->firstWhere('id', (int) $request->input('patient_id'));
        }
        $displayPatient = $patients->firstWhere('id', (int) old('patient_id', $selectedPatient?->id));

        $activeCategoriesCount = $categoriesActive->count();
        $patientCategoryIds = $categoriesActive
            ->where('est_document_patient', true)
            ->pluck('id')
            ->all();
        $categoriesActive->transform(function (CategorieDocument $categorie) use ($documentsByCategory) {
            $categorie->display_color = $categorie->couleur ?: '#3b82f6';
            $categorie->display_icon = $categorie->icone ?: 'fas fa-folder';
            $categorie->display_documents_count = (int) ($documentsByCategory[$categorie->id] ?? 0);

            return $categorie;
        });

        return view('documents.upload', compact(
            'categoriesActive',
            'documentsByCategory',
            'patients',
            'selectedPatient',
            'displayPatient',
            'activeCategoriesCount',
            'patientCategoryIds',
        ));
    }

    private function formatBytes($bytes): string
    {
        $bytes = (int) $bytes;

        if ($bytes <= 0) {
            return '0 MB';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'categorie_document_id' => 'required|exists:categorie_documents,id',
            'fichier' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png,gif,zip'
                . '|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                . ',application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain,image/jpeg,image/png,image/gif,application/zip,application/x-zip-compressed',
            'description' => 'nullable|string|max:1000',
            'source_document' => 'nullable|in:telechargement,scan_cabinet',
        ]);

        try {
            $file = $request->file('fichier');
            $extension = strtolower((string) $file->getClientOriginalExtension());
            $fileName = (string) Str::uuid() . '.' . $extension;
            $patient = Patient::query()->findOrFail($validated['patient_id']);
            abort_unless(app(ClinicalAuthorizationService::class)->canAccessPatient($request->user(), $patient), 403);
            $category = CategorieDocument::query()->findOrFail($validated['categorie_document_id']);
            $archive = PatientArchive::query()->firstOrCreate(
                ['patient_id' => $patient->id],
                ['donnees' => null]
            );

            $folder = Str::slug((string) $category->nom, '-');
            $filePath = $file->storeAs('documents/' . $patient->id . '/' . $folder, $fileName, 'local');

            DocumentMedical::create([
                'patient_archive_id' => $archive->id,
                'categorie_document_id' => $validated['categorie_document_id'],
                'nom_fichier' => $fileName,
                'nom_original' => $file->getClientOriginalName(),
                'chemin_fichier' => $filePath,
                'mime_type' => $file->getMimeType(),
                'taille_fichier' => $file->getSize(),
                'extension' => $extension,
                'description' => $validated['description'] ?? null,
                'auteur' => optional(auth()->user())->name,
                'date_document' => now(),
                'hash_fichier' => hash_file('sha256', $file->getRealPath()),
                'source_document' => $validated['source_document'] ?? 'telechargement',
            ]);

            return redirect()
                ->route('documents.index')
                ->with('success', 'Document telecharge et associe au dossier patient avec succes.');
        } catch (\Exception $e) {
            Log::error('Document upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', 'Erreur lors du telechargement du document.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentMedical $document)
    {
        $disk = $this->resolveStorageDisk($document->chemin_fichier);

        if (!$disk) {
            abort(404);
        }

        return $disk->download($document->chemin_fichier, $document->nom_original);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentMedical $document)
    {
        try {
            $disk = $this->resolveStorageDisk($document->chemin_fichier);
            if ($disk) {
                $disk->delete($document->chemin_fichier);
            }

            $document->delete();

            return redirect()->route('documents.index')->with('success', 'Document supprime avec succes.');
        } catch (\Exception $e) {
            Log::error('Document delete failed', [
                'error' => $e->getMessage(),
                'document_id' => $document->id,
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', 'Erreur lors de la suppression du document.');
        }
    }

    private function resolveStorageDisk(string $path): ?\Illuminate\Contracts\Filesystem\Filesystem
    {
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local');
        }

        // Backward compatibility for documents stored on public disk.
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public');
        }

        return null;
    }
}
