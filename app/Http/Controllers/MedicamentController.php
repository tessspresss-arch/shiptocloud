<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MedicamentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Medicament::with(['createdBy', 'updatedBy']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nom_commercial', 'like', '%' . $request->search . '%')
                  ->orWhere('dci', 'like', '%' . $request->search . '%')
                  ->orWhere('code_cip', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->lowStock();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
            }
        }

        // Apply sorting with whitelist to prevent SQL injection via column names.
        $allowedSorts = [
            'nom_commercial',
            'dci',
            'code_cip',
            'categorie',
            'type',
            'quantite_stock',
            'prix_achat',
            'prix_vente',
            'date_peremption',
            'statut',
            'created_at',
        ];
        $sortBy = $request->get('sort_by', 'nom_commercial');
        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'nom_commercial';
        }

        $sortDirection = strtolower((string) $request->get('sort_direction', 'asc')) === 'desc'
            ? 'desc'
            : 'asc';

        $query->orderBy($sortBy, $sortDirection);

        $medicaments = $query->paginate(15)->appends($request->query());
        $medicaments->getCollection()->transform(function (Medicament $medicament) {
            $medicament->display_stock_class = $medicament->stock_status === 'rupture'
                ? 'pill-red'
                : ($medicament->stock_status === 'faible' ? 'pill-amber' : 'pill-green');
            $medicament->display_expiration_class = $medicament->expiration_status === 'expire'
                ? 'pill-red'
                : ($medicament->expiration_status === 'bientot_expire' ? 'pill-amber' : 'pill-green');

            return $medicament;
        });

        // Get unique categories for filter dropdown
        $categories = Medicament::whereNotNull('categorie')
            ->distinct()
            ->pluck('categorie')
            ->sort()
            ->values();

        // Calculate statistics
        $stats = [
            'total' => Medicament::count(),
            'actifs' => Medicament::active()->count(),
            'stock_faible' => Medicament::lowStock()->count(),
            'expires' => Medicament::expired()->count(),
            'expire_bientot' => Medicament::expiringSoon()->count(),
            'valeur_stock' => Medicament::sum('prix_achat'),
        ];
        $resultCount = method_exists($medicaments, 'total') ? $medicaments->total() : $medicaments->count();
        $activeFilterCount = collect([
            $request->input('search'),
            $request->input('categorie'),
            $request->input('type'),
            $request->input('statut'),
            $request->input('stock_status'),
        ])->filter(fn ($value) => filled($value))->count();

        return view('medicaments.index', compact('medicaments', 'categories', 'stats', 'resultCount', 'activeFilterCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('medicaments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_commercial' => 'required|string|max:255',
            'dci' => 'nullable|string|max:255',
            'code_cip' => 'nullable|string|max:255',
            'code_medicament' => 'nullable|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'classe_therapeutique' => 'nullable|string|max:255',
            'laboratoire' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'quantite_stock' => 'required|integer|min:0',
            'quantite_seuil' => 'required|integer|min:0',
            'quantite_ideale' => 'required|integer|min:0',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'prix_remboursement' => 'nullable|numeric|min:0',
            'taux_remboursement' => 'nullable|numeric|min:0|max:100',
            'date_peremption' => 'nullable|date|after:today',
            'date_fabrication' => 'nullable|date|before_or_equal:today',
            'numero_lot' => 'nullable|string|max:255',
            'fournisseur' => 'nullable|string|max:255',
            'presentation' => 'nullable|string',
            'voie_administration' => 'nullable|string|max:255',
            'posologie' => 'nullable|string',
            'contre_indications' => 'nullable|string',
            'effets_secondaires' => 'nullable|string',
            'interactions' => 'nullable|string',
            'precautions' => 'nullable|string',
            'conservation' => 'nullable|string',
            'statut' => 'required|in:actif,inactif',
            'generique' => 'boolean',
            'remboursable' => 'boolean',
            'composants' => 'nullable|array',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Medicament::create($validated);

        return redirect()->route('medicaments.index')
            ->with('success', 'Médicament créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicament $medicament)
    {
        $medicament->load(['mouvementStocks', 'createdBy', 'updatedBy']);
        return view('medicaments.show', compact('medicament'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medicament $medicament)
    {
        return view('medicaments.edit', compact('medicament'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicament $medicament)
    {
        $validated = $request->validate([
            'nom_commercial' => 'required|string|max:255',
            'dci' => 'nullable|string|max:255',
            'code_cip' => 'nullable|string|max:255',
            'code_medicament' => 'nullable|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'classe_therapeutique' => 'nullable|string|max:255',
            'laboratoire' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'quantite_stock' => 'required|integer|min:0',
            'quantite_seuil' => 'required|integer|min:0',
            'quantite_ideale' => 'required|integer|min:0',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'prix_remboursement' => 'nullable|numeric|min:0',
            'taux_remboursement' => 'nullable|numeric|min:0|max:100',
            'date_peremption' => 'nullable|date',
            'date_fabrication' => 'nullable|date|before_or_equal:today',
            'numero_lot' => 'nullable|string|max:255',
            'fournisseur' => 'nullable|string|max:255',
            'presentation' => 'nullable|string',
            'voie_administration' => 'nullable|string|max:255',
            'posologie' => 'nullable|string',
            'contre_indications' => 'nullable|string',
            'effets_secondaires' => 'nullable|string',
            'interactions' => 'nullable|string',
            'precautions' => 'nullable|string',
            'conservation' => 'nullable|string',
            'statut' => 'required|in:actif,inactif',
            'generique' => 'boolean',
            'remboursable' => 'boolean',
            'composants' => 'nullable|array',
        ]);

        $validated['updated_by'] = Auth::id();

        $medicament->update($validated);

        return redirect()->route('medicaments.show', $medicament)
            ->with('success', 'Médicament mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicament $medicament)
    {
        $medicament->delete();

        return redirect()->route('medicaments.index')
            ->with('success', 'Médicament supprimé avec succès.');
    }

    /**
     * Display reports for medicaments.
     */
    public function reports(Request $request)
    {
        $dateDebut = Carbon::parse($request->input('date_debut', now()->startOfMonth()->toDateString()))->startOfDay();
        $dateFin = Carbon::parse($request->input('date_fin', now()->toDateString()))->endOfDay();

        $medicamentsStock = Medicament::query()
            ->orderByRaw('quantite_stock = 0 desc')
            ->orderByRaw('quantite_stock <= quantite_seuil desc')
            ->orderBy('nom_commercial')
            ->get();

        $mouvements = MouvementStock::query()
            ->with(['medicament', 'user'])
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->latest('created_at')
            ->get();

        $medicamentsExpires = Medicament::query()
            ->whereNotNull('date_peremption')
            ->where('date_peremption', '<=', now()->addDays(30))
            ->orderBy('date_peremption')
            ->get();

        $categories = Medicament::query()
            ->get()
            ->groupBy(fn (Medicament $medicament) => $medicament->categorie ?: 'Non classé')
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->toArray();

        $typeLabels = [
            'prescription' => 'Prescription',
            'otc' => 'OTC',
            'controlled' => 'Contrôlé',
        ];

        $types = Medicament::query()
            ->get()
            ->groupBy(fn (Medicament $medicament) => $typeLabels[$medicament->type] ?? ucfirst((string) $medicament->type))
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->toArray();

        $stats = [
            'total_medicaments' => Medicament::count(),
            'medicaments_actifs' => Medicament::active()->count(),
            'stock_faible' => Medicament::lowStock()->count(),
            'expires' => Medicament::expired()->count(),
            'perimes' => Medicament::expiringSoon()->count(),
            'valeur_totale_stock' => Medicament::query()->get()->sum(fn (Medicament $medicament) => $medicament->valeur_stock),
            'total_mouvements_mois' => $mouvements->count(),
            'categories' => $categories,
            'types' => $types,
        ];

        return view('medicaments.reports', compact('stats', 'medicamentsStock', 'mouvements', 'medicamentsExpires'));
    }
}
