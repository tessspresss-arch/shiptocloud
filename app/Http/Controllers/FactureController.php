<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Consultation;
use App\Models\LigneFacture;
use App\Models\Patient;
use App\Models\Medecin;
use App\Services\Billing\FactureMailService;
use App\Services\Billing\FacturePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FactureController extends Controller
{
    /**
     * Afficher la liste des factures
     */
    public function index(Request $request)
    {
        $perPage = max(10, min(100, (int) $request->integer('per_page', 15)));
        $statusCatalog = $this->statusCatalog();
        $statusOptions = [
            '' => 'Tous les statuts',
            'payee' => "Pay\u{00E9}e",
            'impayee' => "Impay\u{00E9}e",
            'en_attente' => 'En attente',
            'partiellement_payee' => "Partiellement pay\u{00E9}e",
            'annulee' => "Annul\u{00E9}e",
            'brouillon' => 'Brouillon',
        ];

        $baseQuery = Facture::query()
            ->select([
                'id',
                'numero_facture',
                'patient_id',
                'date_facture',
                'date_echeance',
                'montant_total',
                'remise',
                'statut',
                'created_at',
            ])
            ->with(['patient:id,nom,prenom'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));

                $query->where(function ($inner) use ($search) {
                    $inner->where('numero_facture', 'like', '%' . $search . '%')
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery
                                ->where('nom', 'like', '%' . $search . '%')
                                ->orWhere('prenom', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($request->filled('period'), function ($query) use ($request) {
                $period = $request->input('period');

                if ($period === 'month') {
                    $query->whereBetween('date_facture', [now()->startOfMonth(), now()->endOfMonth()]);
                } elseif ($period === 'quarter') {
                    $query->whereBetween('date_facture', [now()->startOfQuarter(), now()->endOfQuarter()]);
                } elseif ($period === 'year') {
                    $query->whereBetween('date_facture', [now()->startOfYear(), now()->endOfYear()]);
                }
            });

        $facturesQuery = (clone $baseQuery)
            ->when($request->filled('status'), function ($query) use ($request, $statusCatalog) {
                $statusKey = $this->normalizeStatus((string) $request->input('status'));
                $variants = $statusCatalog[$statusKey]['variants'] ?? [(string) $request->input('status')];
                $query->whereIn('statut', $variants);
            })
            ->orderByDesc('date_facture')
            ->orderByDesc('id');

        $factures = $facturesQuery
            ->paginate($perPage)
            ->appends($request->query());

        foreach ($factures as $facture) {
            $statusKey = $this->resolveStatusKey((string) $facture->statut, $statusCatalog);
            $facture->status_class = $statusCatalog[$statusKey]['class'] ?? 'brouillon';
            $facture->status_label = $statusCatalog[$statusKey]['label'] ?? 'Brouillon';
            $facture->status_icon = match ($facture->status_class) {
                'payee' => 'fa-circle-check',
                'impayee' => 'fa-clock',
                'annulee' => 'fa-ban',
                default => 'fa-circle-info',
            };
            $factureDate = $facture->date_facture ? \Carbon\Carbon::parse($facture->date_facture) : null;
            $dueDate = $facture->date_echeance ? \Carbon\Carbon::parse($facture->date_echeance) : null;
            $facture->is_paid_invoice = $facture->status_class === 'payee';
            $facture->display_patient_name = $facture->patient
                ? trim(strtoupper((string) $facture->patient->nom) . ' ' . ($facture->patient->prenom ?? ''))
                : 'Patient inconnu';
            $facture->display_date_facture = $factureDate?->format('d/m/Y') ?? 'Non definie';
            $facture->display_date_facture_human = $factureDate?->diffForHumans() ?? 'Date indisponible';
            $facture->display_date_facture_muted = $factureDate ? '' : 'muted';
            $facture->display_date_echeance = $dueDate?->format('d/m/Y') ?? 'Non definie';
            $facture->display_date_echeance_human = $dueDate?->diffForHumans() ?? 'Aucune echeance';
            $facture->display_date_echeance_muted = $dueDate ? '' : 'muted';
            $facture->is_overdue = $dueDate?->isPast() && !$facture->is_paid_invoice;
        }

        $totalFactures = (clone $baseQuery)->count();
        $paidFactures = $this->countByStatuses(clone $baseQuery, $statusCatalog['payee']['variants']);
        $unpaidFactures = $this->countByStatuses(
            clone $baseQuery,
            array_merge(
                $statusCatalog['impayee']['variants'],
                $statusCatalog['en_attente']['variants'],
                $statusCatalog['partiellement_payee']['variants']
            )
        );
        $totalAmount = (float) ((clone $baseQuery)->sum('montant_total') ?? 0);
        $selectedStatus = $request->input('status');
        $selectedPeriod = $request->input('period');
        $currentPerPage = $perPage;
        $hasFilters = $request->hasAny(['search', 'status', 'period', 'per_page']);
        $statusLabel = $selectedStatus && isset($statusOptions[$selectedStatus]) ? $statusOptions[$selectedStatus] : null;
        $periodLabels = [
            'month' => 'Ce mois',
            'quarter' => 'Ce trimestre',
            'year' => 'Cette annee',
        ];
        $selectedPeriodLabel = $selectedPeriod && isset($periodLabels[$selectedPeriod]) ? $periodLabels[$selectedPeriod] : null;

        return view('factures.index', [
            'factures' => $factures,
            'totalFactures' => $totalFactures,
            'paidFactures' => $paidFactures,
            'unpaidFactures' => $unpaidFactures,
            'totalAmount' => $totalAmount,
            'statusOptions' => $statusOptions,
            'selectedStatus' => $selectedStatus,
            'selectedPeriod' => $selectedPeriod,
            'currentPerPage' => $currentPerPage,
            'hasFilters' => $hasFilters,
            'statusLabel' => $statusLabel,
            'selectedPeriodLabel' => $selectedPeriodLabel,
        ]);
    }

    private function statusCatalog(): array
    {
        return [
            'payee' => [
                'label' => "Pay\u{00E9}e",
                'class' => 'payee',
                'variants' => ['payee', "pay\u{00E9}e", 'paye', "pay\u{00E9}", 'reglee', "r\u{00E9}gl\u{00E9}e", 'regle', "r\u{00E9}gl\u{00E9}"],
            ],
            'impayee' => [
                'label' => "Impay\u{00E9}e",
                'class' => 'impayee',
                'variants' => ['impayee', "impay\u{00E9}e", 'non_payee', "non_pay\u{00E9}e", 'en_attente', 'en attente', 'attente'],
            ],
            'en_attente' => [
                'label' => 'En attente',
                'class' => 'partiellement_payee',
                'variants' => ['en_attente', 'en attente', 'attente'],
            ],
            'partiellement_payee' => [
                'label' => "Partiellement pay\u{00E9}e",
                'class' => 'partiellement_payee',
                'variants' => ['partiellement_payee', "partiellement_pay\u{00E9}e", 'partielle'],
            ],
            'annulee' => [
                'label' => "Annul\u{00E9}e",
                'class' => 'annulee',
                'variants' => ['annulee', "annul\u{00E9}e", 'annule', "annul\u{00E9}"],
            ],
            'brouillon' => [
                'label' => 'Brouillon',
                'class' => 'brouillon',
                'variants' => ['brouillon', 'draft'],
            ],
        ];
    }

    private function normalizeStatus(?string $status): string
    {
        return Str::of((string) $status)
            ->lower()
            ->ascii()
            ->replace(['-', ' '], '_')
            ->trim('_')
            ->value();
    }

    private function resolveStatusKey(string $status, array $statusCatalog): string
    {
        $normalized = $this->normalizeStatus($status);

        foreach ($statusCatalog as $key => $meta) {
            foreach ($meta['variants'] as $variant) {
                if ($normalized === $this->normalizeStatus($variant)) {
                    return $key;
                }
            }
        }

        return 'brouillon';
    }

    private function countByStatuses($query, array $statuses): int
    {
        return (int) $query->whereIn('statut', $statuses)->count();
    }

    private function isPaidStatus(?string $status): bool
    {
        $normalized = $this->normalizeStatus($status);

        return in_array($normalized, [
            'payee',
            'paye',
            'reglee',
            'regle',
            'paid',
        ], true);
    }

    private function findSelectedConsultation(?int $consultationId): ?Consultation
    {
        if (!$consultationId) {
            return null;
        }

        return Consultation::query()
            ->with(['patient:id,nom,prenom', 'medecin:id,nom,prenom'])
            ->find($consultationId);
    }

    /**
     * Afficher le formulaire de crÃƒÆ’Ã‚Â©ation
     */
    public function create(Request $request)
    {
        $patients = $this->patientSelectionQuery()->get();
        $medecins = $this->medecinSelectionQuery()->get();
        $selectedConsultation = $this->findSelectedConsultation($request->integer('consultation_id'));
        $selectedPatientId = old('patient_id', $request->input('patient_id', $selectedConsultation?->patient_id));
        $selectedMedecinId = old('medecin_id', $request->input('medecin_id', $selectedConsultation?->medecin_id));
        $defaultPrestationDescription = old(
            'prestations.0.description',
            $selectedConsultation ? 'Consultation liee #' . $selectedConsultation->id : 'Consultation generale'
        );
        $defaultFactureNotes = old(
            'notes',
            $selectedConsultation ? 'Facture generee depuis la consultation #' . $selectedConsultation->id : ''
        );

        return view('factures.create', compact(
            'patients',
            'medecins',
            'selectedConsultation',
            'selectedPatientId',
            'selectedMedecinId',
            'defaultPrestationDescription',
            'defaultFactureNotes'
        ));
    }

    /**
     * Enregistrer une nouvelle facture
     */
    public function store(Request $request)
    {
        // Validation des donnÃƒÆ’Ã‚Â©es
        $request->validate([
            'consultation_id' => 'nullable|exists:consultations,id',
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'nullable|exists:medecins,id',
            'prestations' => 'required|array|min:1',
            'prestations.*.description' => 'required|string|max:255',
            'prestations.*.quantite' => 'required|integer|min:1',
            'prestations.*.prix_unitaire' => 'required|numeric|min:0',
            'date_facture' => 'required|date',
            'date_echeance' => 'nullable|date',
            'remise' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'action' => 'required|in:brouillon,en_attente'
        ]);

        $selectedConsultation = $this->findSelectedConsultation($request->integer('consultation_id'));
        if ($selectedConsultation && (int) $selectedConsultation->patient_id !== (int) $request->integer('patient_id')) {
            return back()
                ->withErrors(['consultation_id' => 'La consultation selectionnee ne correspond pas au patient choisi.'])
                ->withInput();
        }

        $medecinId = $request->filled('medecin_id')
            ? $request->integer('medecin_id')
            : $selectedConsultation?->medecin_id;

        // Calcul du montant total
        $montantTotal = 0;
        foreach ($request->prestations as $prestation) {
            $montantTotal += $prestation['quantite'] * $prestation['prix_unitaire'];
        }

        // CrÃƒÆ’Ã‚Â©ation de la facture
        $facture = Facture::create([
            'numero_facture' => Facture::generateNumero(),
            'patient_id' => $request->patient_id,
            'consultation_id' => $selectedConsultation?->id,
            'medecin_id' => $medecinId,
            'date_facture' => $request->date_facture,
            'date_echeance' => $request->date_echeance,
            'montant_total' => $montantTotal,
            'remise' => $request->remise ?? 0,
            'statut' => $request->action === 'brouillon' ? 'brouillon' : 'en_attente',
            'notes' => $request->notes,
        ]);

        // CrÃƒÆ’Ã‚Â©ation des lignes de facture
        foreach ($request->prestations as $prestation) {
            $totalLigne = $prestation['quantite'] * $prestation['prix_unitaire'];

            LigneFacture::create([
                'facture_id' => $facture->id,
                'description' => $prestation['description'],
                'quantite' => $prestation['quantite'],
                'prix_unitaire' => $prestation['prix_unitaire'],
                'total_ligne' => $totalLigne,
                'type' => 'prestation', // Type par dÃƒÆ’Ã‚Â©faut
            ]);
        }

        // Message de succÃƒÆ’Ã‚Â¨s selon l'action
        $message = $request->action === 'brouillon'
            ? 'Brouillon de facture enregistrÃƒÆ’Ã‚Â© avec succÃƒÆ’Ã‚Â¨s!'
            : 'Facture crÃƒÆ’Ã‚Â©ÃƒÆ’Ã‚Â©e avec succÃƒÆ’Ã‚Â¨s!';

        return redirect()->route('factures.index')
            ->with('success', $message);
    }

    /**
     * Afficher une facture spÃƒÆ’Ã‚Â©cifique
     */
    public function show($id)
    {
        $facture = Facture::with(['ligneFactures', 'patient', 'medecin', 'consultation.patient'])->find($id);
        
        if (!$facture) {
            return redirect()->route('factures.index')
                ->with('error', 'Facture non trouvÃƒÆ’Ã‚Â©e');
        }
        
        return view('factures.show', compact('facture'));
    }

    /**
     * Afficher le formulaire d'ÃƒÆ’Ã‚Â©dition
     */
    public function edit($id)
    {
        $facture = Facture::with(['ligneFactures', 'patient', 'medecin', 'consultation.patient'])->find($id);
        
        if (!$facture) {
            return redirect()->route('factures.index')
                ->with('error', 'Facture non trouvÃƒÆ’Ã‚Â©e');
        }
        
        $patients = $this->patientSelectionQuery()->get();
        $medecins = $this->medecinSelectionQuery()->get();
        
        return view('factures.edit', compact('facture', 'patients', 'medecins'));
    }

    private function patientSelectionQuery()
    {
        return Patient::query()
            ->select(['id', 'nom', 'prenom', 'telephone'])
            ->orderBy('nom')
            ->orderBy('prenom');
    }

    private function medecinSelectionQuery()
    {
        return Medecin::query()
            ->select(['id', 'nom', 'prenom'])
            ->orderBy('nom')
            ->orderBy('prenom');
    }

    /**
     * Mettre ÃƒÆ’Ã‚Â  jour une facture
     */
    public function update(Request $request, $id)
    {
        $facture = Facture::with('ligneFactures')->findOrFail($id);

        $validated = $request->validate([
            'consultation_id' => 'nullable|exists:consultations,id',
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'nullable|exists:medecins,id',
            'prestations' => 'required|array|min:1',
            'prestations.*.description' => 'required|string|max:255',
            'prestations.*.quantite' => 'required|integer|min:1',
            'prestations.*.prix_unitaire' => 'required|numeric|min:0',
            'date_facture' => 'required|date',
            'date_echeance' => 'nullable|date',
            'remise' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $selectedConsultation = $this->findSelectedConsultation((int) ($validated['consultation_id'] ?? 0));
        if ($selectedConsultation && (int) $selectedConsultation->patient_id !== (int) $validated['patient_id']) {
            return back()
                ->withErrors(['consultation_id' => 'La consultation selectionnee ne correspond pas au patient choisi.'])
                ->withInput();
        }

        $medecinId = !empty($validated['medecin_id'])
            ? (int) $validated['medecin_id']
            : $selectedConsultation?->medecin_id;

        $montantTotal = 0;
        foreach ($validated['prestations'] as $prestation) {
            $montantTotal += ((int) $prestation['quantite']) * ((float) $prestation['prix_unitaire']);
        }

        DB::transaction(function () use ($facture, $validated, $montantTotal, $selectedConsultation, $medecinId) {
            $facture->update([
                'consultation_id' => $selectedConsultation?->id,
                'patient_id' => $validated['patient_id'],
                'medecin_id' => $medecinId,
                'date_facture' => $validated['date_facture'],
                'date_echeance' => $validated['date_echeance'] ?? null,
                'montant_total' => $montantTotal,
                'remise' => $validated['remise'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            $facture->ligneFactures()->delete();

            foreach ($validated['prestations'] as $prestation) {
                $quantite = (int) $prestation['quantite'];
                $prixUnitaire = (float) $prestation['prix_unitaire'];

                LigneFacture::create([
                    'facture_id' => $facture->id,
                    'description' => $prestation['description'],
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'total_ligne' => $quantite * $prixUnitaire,
                    'type' => 'prestation',
                ]);
            }
        });

        return redirect()->route('factures.show', $facture)
            ->with('success', 'Facture mise a jour avec succes.');
    }

    /**
     * Supprimer une facture
     */
    public function destroy($id)
    {
        $facture = Facture::with('ligneFactures')->findOrFail($id);

        if ($this->isPaidStatus((string) $facture->statut)) {
            $message = "Action impossible. Cette facture a d\u{00E9}j\u{00E0} \u{00E9}t\u{00E9} r\u{00E9}gl\u{00E9}e. La suppression n'est pas autoris\u{00E9}e.";

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->route('factures.index')->with('error', $message);
        }

        try {
            DB::transaction(function () use ($facture) {
                $facture->delete();
            });

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture supprimÃƒÆ’Ã‚Â©e avec succÃƒÆ’Ã‚Â¨s.',
                ]);
            }

            return redirect()->route('factures.index')
                ->with('success', 'Facture supprimÃƒÆ’Ã‚Â©e avec succÃƒÆ’Ã‚Â¨s.');
        } catch (\Throwable $e) {
            Log::error('Invoice delete failed', [
                'facture_id' => $facture->id ?? null,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossible de supprimer la facture pour le moment.",
                ], 500);
            }

            return redirect()->route('factures.index')
                ->with('error', "Impossible de supprimer la facture pour le moment.");
        }
    }
    /**
     * GÃƒÆ’Ã‚Â©nÃƒÆ’Ã‚Â©rer et tÃƒÆ’Ã‚Â©lÃƒÆ’Ã‚Â©charger le PDF de la facture
     */
    public function generatePdf(int $id, FacturePdfService $pdfService)
    {
        $facture = Facture::with(['ligneFactures', 'patient', 'medecin'])->findOrFail($id);
        
        // GÃƒÆ’Ã‚Â©nÃƒÆ’Ã‚Â©rer le PDF
        $pdf = Pdf::loadView('factures.pdf', compact('facture'));
        
        // TÃƒÆ’Ã‚Â©lÃƒÆ’Ã‚Â©charger le PDF
        return $pdf->download('facture-' . $facture->numero_facture . '.pdf');
    }

    /**
     * Envoyer la facture par email
     */
    public function envoyer($id, FactureMailService $mailService)
    {
        $facture = Facture::with(['ligneFactures', 'patient', 'medecin'])->findOrFail($id);
        
        // VÃƒÆ’Ã‚Â©rifier que le patient a une adresse email
        if (!$facture->patient->email) {
            return redirect()->route('factures.show', $facture)
                ->with('error', 'Le patient n\'a pas d\'adresse email enregistrÃƒÆ’Ã‚Â©e.');
        }

        try {
            $mailService->sendToPatient($facture);

            return redirect()->route('factures.show', $facture)
                ->with('success', 'Facture envoyÃƒÆ’Ã‚Â©e par email avec succÃƒÆ’Ã‚Â¨s ÃƒÆ’Ã‚Â  ' . $facture->patient->email);
        } catch (\Exception $e) {
            Log::error('Invoice email send failed', [
                'error' => $e->getMessage(),
                'facture_id' => $facture->id ?? null,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('factures.show', $facture)
                ->with('error', 'Erreur lors de l\'envoi de l\'email.');
        }
    }

    /**
     * Mettre ÃƒÆ’Ã‚Â  jour le statut de la facture
     */
    public function updateStatut(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);

        $statusMap = [
            'brouillon' => 'brouillon',
            'en_attente' => 'en_attente',
            'attente' => 'en_attente',
            'payee' => 'payÃƒÆ’Ã‚Â©e',
            'paye' => 'payÃƒÆ’Ã‚Â©e',
            'reglee' => 'payÃƒÆ’Ã‚Â©e',
            'regle' => 'payÃƒÆ’Ã‚Â©e',
            'annulee' => 'annulÃƒÆ’Ã‚Â©e',
            'annule' => 'annulÃƒÆ’Ã‚Â©e',
        ];

        $requestedStatus = $this->normalizeStatus((string) $request->input('statut'));
        if (!array_key_exists($requestedStatus, $statusMap)) {
            return back()->withErrors(['statut' => 'Le statut selectionne est invalide.']);
        }

        $facture->statut = $statusMap[$requestedStatus];
        $facture->date_paiement = $facture->statut === 'payÃƒÆ’Ã‚Â©e' ? now() : null;
        
        $facture->save();

        return redirect()->route('factures.show', $facture)
            ->with('success', 'Statut de la facture mis ÃƒÆ’Ã‚Â  jour avec succÃƒÆ’Ã‚Â¨s.');
    }
}


