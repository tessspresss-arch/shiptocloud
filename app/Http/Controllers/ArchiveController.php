<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Models\PatientArchive;
use App\Models\DocumentMedical;
use App\Models\CategorieDocument;
use App\Models\LogAccesArchive;
use App\Models\Patient;
use Carbon\Carbon;

class ArchiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,medecin,secretaire,infirmier');
    }

    /**
     * Afficher la liste des archives avec recherche et filtres
     */
    public function index(Request $request)
    {
        $query = PatientArchive::with([
            'patient.dossiers:id,patient_id',
            'documents.categorie',
        ]);

        // Recherche par patient
        $patientSearch = trim((string) ($request->get('patient') ?: $request->get('q')));
        if ($patientSearch !== '') {
            $query->whereHas('patient', function($q) use ($request) {
                $search = trim((string) (request('patient') ?: request('q')));
                $q->where('nom', 'like', '%' . $search . '%')
                  ->orWhere('prenom', 'like', '%' . $search . '%')
                  ->orWhere('numero_dossier', 'like', '%' . $search . '%');
            });
        }

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->whereHas('documents', function($q) use ($request) {
                $q->where('categorie_document_id', $request->categorie);
            });
        }

        // Filtre par date
        $dateStart = $request->get('date_debut') ?: $request->get('date');
        if (!empty($dateStart)) {
            $query->whereHas('documents', function($q) use ($request) {
                $date = request('date_debut') ?: request('date');
                $q->whereDate('date_document', '>=', $date);
            });
        }

        if ($request->filled('date_fin')) {
            $query->whereHas('documents', function($q) use ($request) {
                $q->where('date_document', '<=', $request->date_fin);
            });
        }

        // Recherche par tags/mots-clés
        if ($request->filled('tags')) {
            $query->whereHas('documents', function($q) use ($request) {
                $q->whereJsonContains('tags', $request->tags);
            });
        }

        $archives = $query
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $archives->getCollection()->transform(function (PatientArchive $archive) {
            $patientName = data_get($archive, 'patient.nom_complet')
                ?? data_get($archive, 'patient.name')
                ?? trim((string) (data_get($archive, 'patient.prenom', '') . ' ' . data_get($archive, 'patient.nom', '')))
                ?? 'Patient';

            $archivedAt = data_get($archive, 'archived_at')
                ?? data_get($archive, 'date_archivage')
                ?? data_get($archive, 'updated_at');

            $reason = data_get($archive, 'motif')
                ?? data_get($archive, 'raison')
                ?? data_get($archive, 'notes')
                ?? 'Aucun motif renseigne';

            $statusValue = strtolower((string) (data_get($archive, 'statut') ?? data_get($archive, 'status') ?? 'archive'));

            $archive->display_patient_name = trim((string) $patientName) !== '' ? trim((string) $patientName) : 'Patient';
            $archive->display_archived_at = $archivedAt ? Carbon::parse($archivedAt)->format('d/m/Y') : '—';
            $archive->display_reason = trim((string) $reason) !== '' ? trim((string) $reason) : 'Aucun motif renseigne';
            $archive->display_status_label = match ($statusValue) {
                'restaure', 'restore', 'restored' => 'Restaure',
                'historique', 'history' => 'Historique',
                default => 'Archive',
            };
            $archive->display_status_class = match ($statusValue) {
                'restaure', 'restore', 'restored' => 'status-badge status-restored',
                'historique', 'history' => 'status-badge status-history',
                default => 'status-badge status-archived',
            };

            $archive->display_view_url = optional($archive->patient?->dossiers->first())->id
                ? route('dossiers.show', $archive->patient->dossiers->first()->id)
                : null;

            return $archive;
        });

        // Statistiques pour le dashboard
        $stats = [
            'total_archives' => PatientArchive::count(),
            'total_documents' => DocumentMedical::count(),
            'documents_ce_mois' => DocumentMedical::whereMonth('created_at', now()->month)->count(),
            'espaces_stockage' => $this->calculerEspaceStockage()
        ];

        $categories = CategorieDocument::actives()->ordered()->get();

        // Log d'accès
        $this->logAcces('consultation', 'liste_archives', null, 'Consultation de la liste des archives');

        return view('archives.index', compact('archives', 'stats', 'categories'));
    }

    /**
     * Afficher le formulaire de création d'une nouvelle archive
     */
    public function create()
    {
        $patients = Patient::select('id', 'nom', 'prenom', 'date_naissance')->get();
        $categories = CategorieDocument::actives()->ordered()->get();

        return view('archives.create', compact('patients', 'categories'));
    }

    /**
     * Stocker une nouvelle archive
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'documents' => 'required|array|min:1',
            'documents.*.fichier' => 'required|file|max:51200|mimes:pdf,doc,docx,jpg,jpeg,png,gif,dcm,txt',
            'documents.*.categorie_id' => 'required|exists:categorie_documents,id',
            'documents.*.description' => 'nullable|string|max:500',
            'documents.*.date_document' => 'nullable|date',
            'documents.*.tags' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Créer ou récupérer l'archive patient
            $archive = PatientArchive::firstOrCreate(
                ['patient_id' => $request->patient_id],
                ['date_creation' => now()]
            );

            $uploadedDocuments = [];

            foreach ($request->documents as $docData) {
                $file = $docData['fichier'];
                $categorie = CategorieDocument::findOrFail($docData['categorie_id']);

                // Générer un nom de fichier unique
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = 'archives/' . $archive->patient_id . '/' . $categorie->nom;

                // Stocker le fichier
                $storedPath = $file->storeAs($path, $filename, 'public');

                // Créer l'entrée en base
                $document = DocumentMedical::create([
                    'patient_archive_id' => $archive->id,
                    'categorie_document_id' => $categorie->id,
                    'nom_fichier' => $filename,
                    'nom_original' => $file->getClientOriginalName(),
                    'chemin_fichier' => $storedPath,
                    'mime_type' => $file->getMimeType(),
                    'taille_fichier' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension(),
                    'description' => $docData['description'] ?? null,
                    'date_document' => $docData['date_document'] ?? null,
                    'auteur' => Auth::user()->name,
                    'tags' => $docData['tags'] ? json_decode($docData['tags']) : null,
                    'hash_fichier' => hash_file('sha256', storage_path('app/public/' . $storedPath))
                ]);

                // Chiffrement si nécessaire
                if ($categorie->confidentiel) {
                    $this->chiffrerDocument($document);
                }

                $uploadedDocuments[] = $document;
            }

            DB::commit();

            // Log d'accès
            $this->logAcces('creation', 'documents', $archive->id, 'Upload de ' . count($uploadedDocuments) . ' document(s)');

            return response()->json([
                'success' => true,
                'message' => count($uploadedDocuments) . ' document(s) archivé(s) avec succès',
                'archive_id' => $archive->id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de l\'archivage: ' . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de l\'archivage des documents'], 500);
        }
    }

    /**
     * Afficher les détails d'une archive
     */
    public function show($id)
    {
        $archive = PatientArchive::with(['patient', 'documents.categorie'])->findOrFail($id);

        // Vérifier les permissions d'accès
        $this->verifierAccesPatient($archive->patient_id);

        $documentsParCategorie = $archive->documents->groupBy('categorie.nom');

        // Log d'accès
        $this->logAcces('consultation', 'archive_detaillee', $id, 'Consultation détaillée de l\'archive patient');

        return view('archives.show', compact('archive', 'documentsParCategorie'));
    }

    /**
     * Télécharger un document
     */
    public function download($documentId)
    {
        $document = DocumentMedical::findOrFail($documentId);

        // Vérifier les permissions
        $this->verifierAccesPatient($document->archive->patient_id);

        // Log d'accès
        $this->logAcces('telechargement', 'document', $document->id, 'Téléchargement du document: ' . $document->nom_original);

        // Déchiffrer si nécessaire
        if ($document->chiffre) {
            return $this->dechiffrerEtTelecharger($document);
        }

        return Storage::disk('public')->download($document->chemin_fichier, $document->nom_original);
    }

    /**
     * Prévisualiser un document
     */
    public function preview($documentId)
    {
        $document = DocumentMedical::findOrFail($documentId);

        // Vérifier les permissions
        $this->verifierAccesPatient($document->archive->patient_id);

        // Log d'accès
        $this->logAcces('consultation', 'preview_document', $document->id, 'Prévisualisation du document: ' . $document->nom_original);

        if (in_array($document->mime_type, ['image/jpeg', 'image/png', 'image/gif'])) {
            return response()->file(storage_path('app/public/' . $document->chemin_fichier));
        }

        return response()->json(['error' => 'Type de fichier non supporté pour la prévisualisation'], 400);
    }

    /**
     * Supprimer un document (soft delete)
     */
    public function destroyDocument($documentId)
    {
        $document = DocumentMedical::findOrFail($documentId);

        // Vérifier les permissions
        $this->verifierAccesPatient($document->archive->patient_id);

        DB::beginTransaction();
        try {
            // Marquer comme supprimé
            $document->update([
                'supprime' => true,
                'date_suppression' => now()
            ]);

            // Log d'accès
            $this->logAcces('suppression', 'document', $document->id, 'Suppression du document: ' . $document->nom_original);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Document supprimé avec succès']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }

    /**
     * Recherche avancée
     */
    public function search(Request $request)
    {
        $query = DocumentMedical::with(['archive.patient', 'categorie']);

        // Recherche par contenu du nom de fichier
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nom_original', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%')
                  ->orWhereJsonContains('tags', $request->q);
            });
        }

        // Filtres avancés
        if ($request->filled('categorie_id')) {
            $query->where('categorie_document_id', $request->categorie_id);
        }

        if ($request->filled('patient_id')) {
            $query->whereHas('archive', function($q) use ($request) {
                $q->where('patient_id', $request->patient_id);
            });
        }

        if ($request->filled('date_debut')) {
            $query->where('date_document', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_document', '<=', $request->date_fin);
        }

        if ($request->filled('mime_type')) {
            $query->where('mime_type', 'like', '%' . $request->mime_type . '%');
        }

        $resultats = $query->where('supprime', false)
                          ->orderBy('date_document', 'desc')
                          ->paginate(20);

        // Log d'accès
        $this->logAcces('recherche', 'documents', null, 'Recherche avancée: ' . $request->q);

        return view('archives.search', compact('resultats'));
    }

    /**
     * API pour statistiques
     */
    public function statistiques()
    {
        $stats = [
            'total_archives' => PatientArchive::count(),
            'total_documents' => DocumentMedical::count(),
            'documents_par_categorie' => DocumentMedical::selectRaw('categorie_documents.nom, COUNT(*) as count')
                ->join('categorie_documents', 'document_medicals.categorie_document_id', '=', 'categorie_documents.id')
                ->groupBy('categorie_documents.nom')
                ->pluck('count', 'nom'),
            'evolution_mensuelle' => DocumentMedical::selectRaw('MONTH(created_at) as mois, COUNT(*) as count')
                ->whereYear('created_at', now()->year)
                ->groupBy('mois')
                ->pluck('count', 'mois'),
            'espaces_stockage' => $this->calculerEspaceStockage()
        ];

        return response()->json($stats);
    }

    /**
     * Vérifier l'accès à un patient selon le rôle
     */
    private function verifierAccesPatient($patientId)
    {
        $user = Auth::user();

        // Admin et médecins ont accès à tout
        if (in_array($user->role, ['admin', 'medecin'])) {
            return true;
        }

        // Secrétaires et infirmiers: accès restreint selon les règles métier
        // Ici on pourrait ajouter des règles spécifiques selon les besoins

        return true; // Temporairement ouvert
    }

    /**
     * Logger les accès aux archives
     */
    private function logAcces($action, $typeObjet, $objetId, $description)
    {
        try {
            LogAccesArchive::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'type_objet' => $typeObjet,
                'objet_id' => $objetId,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Throwable $e) {
            Log::warning('Archive access log skipped', [
                'error' => $e->getMessage(),
                'action' => $action,
                'type_objet' => $typeObjet,
                'objet_id' => $objetId,
            ]);
        }
    }

    /**
     * Calculer l'espace de stockage utilisé
     */
    private function calculerEspaceStockage()
    {
        $totalSize = DocumentMedical::where('supprime', false)
            ->sum('taille_fichier');

        return [
            'total_octets' => $totalSize,
            'total_mo' => round($totalSize / 1024 / 1024, 2),
            'total_go' => round($totalSize / 1024 / 1024 / 1024, 2)
        ];
    }

    /**
     * Chiffrer un document confidentiel
     */
    private function chiffrerDocument(DocumentMedical $document)
    {
        // Implémentation du chiffrement AES-256
        // À adapter selon les besoins de sécurité
        $document->update(['chiffre' => true]);
    }

    /**
     * Déchiffrer et télécharger un document
     */
    private function dechiffrerEtTelecharger(DocumentMedical $document)
    {
        // Implémentation du déchiffrement
        // Temporairement retourne le fichier tel quel
        return Storage::disk('public')->download($document->chemin_fichier, $document->nom_original);
    }
}
