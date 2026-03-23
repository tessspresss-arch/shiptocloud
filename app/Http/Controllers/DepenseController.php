<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Services\Depenses\DepenseStatisticsService;
use App\Services\Exports\Utf8CsvExporter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
class DepenseController extends Controller
{
    public function __construct(private readonly DepenseStatisticsService $depenseStatisticsService)
    {
    }

    private ?array $depensesColumnsCache = null;
    private ?string $depensesCategoriesTableCache = null;
    private ?array $depensesCategoriesColumnsCache = null;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        $orderColumn = $this->hasDepenseColumn('date_depense')
            ? 'date_depense'
            : ($this->hasDepenseColumn('created_at') ? 'created_at' : 'id');

        $depenses = $query->orderBy($orderColumn, 'desc')->with('user')->paginate(15);

        if ($this->hasDepenseColumn('date_depense')) {
            $monthStart = now()->startOfMonth();
            $monthEnd = now()->endOfMonth();
            $yearStart = now()->startOfYear();
            $yearEnd = now()->endOfYear();

            $totalMois = Depense::whereBetween('date_depense', [$monthStart, $monthEnd])->sum('montant');
            $totalAnnee = Depense::whereBetween('date_depense', [$yearStart, $yearEnd])->sum('montant');
        } else {
            $totalMois = 0;
            $totalAnnee = 0;
        }

        return view('depenses.index', compact('depenses', 'totalMois', 'totalAnnee'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('depenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'details' => 'nullable|string',
            'montant' => 'required|numeric|min:0.01',
            'date_depense' => 'required|date',
            'categorie' => 'required|in:fournitures,medicaments,loyer,personnel,utilites,maintenance,formation,autre',
            'beneficiaire' => 'nullable|string|max:255',
            'statut' => 'required|in:enregistre,payee,en_attente',
            'facture_numero' => 'nullable|string|max:100',
            'mode_paiement' => 'nullable|string|max:100',
            'date_paiement' => 'nullable|date',
        ]);

        $validated['date_depense'] = Carbon::parse((string) $request->date_depense)->startOfDay();
        if (!empty($validated['date_paiement'])) {
            $validated['date_paiement'] = Carbon::parse((string) $validated['date_paiement'])->startOfDay();
        }

        Depense::create($this->buildPersistablePayload($validated, false));

        return redirect()->route('depenses.index')->with('success', 'Depense creee avec succes');
    }

    /**
     * Display the specified resource.
     */
    public function show(Depense $depense)
    {
        return view('depenses.show', compact('depense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Depense $depense)
    {
        return view('depenses.edit', compact('depense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Depense $depense)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'details' => 'nullable|string',
            'montant' => 'required|numeric|min:0.01',
            'date_depense' => 'required|date',
            'categorie' => 'required|in:fournitures,medicaments,loyer,personnel,utilites,maintenance,formation,autre',
            'beneficiaire' => 'nullable|string|max:255',
            'statut' => 'required|in:enregistre,payee,en_attente',
            'facture_numero' => 'nullable|string|max:100',
            'mode_paiement' => 'nullable|string|max:100',
            'date_paiement' => 'nullable|date',
        ]);

        $validated['date_depense'] = Carbon::parse((string) $request->date_depense)->startOfDay();
        if (!empty($validated['date_paiement'])) {
            $validated['date_paiement'] = Carbon::parse((string) $validated['date_paiement'])->startOfDay();
        }

        $depense->update($this->buildPersistablePayload($validated, true));

        return redirect()->route('depenses.index')->with('success', 'Depense mise a jour avec succes');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Depense $depense)
    {
        $depense->delete();

        return redirect()->route('depenses.index')->with('success', 'Depense supprimee avec succes');
    }

    public function export(Request $request, Utf8CsvExporter $csvExporter): StreamedResponse
    {
        $orderColumn = $this->hasDepenseColumn('date_depense')
            ? 'date_depense'
            : ($this->hasDepenseColumn('created_at') ? 'created_at' : 'id');

        $depenses = $this->buildFilteredQuery($request)
            ->orderBy($orderColumn, 'desc')
            ->get();

        $rows = $depenses->map(fn ($depense) => [
            (string) ($depense->description ?? ''),
            optional($depense->date_depense)->format('Y-m-d'),
            (string) ($depense->categorie ?? ''),
            (string) $depense->montant,
            (string) ($depense->beneficiaire ?? ''),
            (string) ($depense->statut ?? ''),
        ]);

        return $csvExporter->download(
            'depenses-' . now()->format('Y-m-d-His') . '.csv',
            ['Description', 'Date', 'Categorie', 'Montant', 'Beneficiaire', 'Statut'],
            $rows
        );
    }

    public function statistiques(Request $request)
    {
        $period = $this->resolveStatisticsPeriod($request);
        $baseQuery = $this->buildBaseDepensesQuery($request);
        $filteredQuery = clone $baseQuery;
        $this->applyDateRangeToQuery($filteredQuery, $period['start'], $period['end']);

        $comparisonQuery = clone $baseQuery;
        $this->applyDateRangeToQuery($comparisonQuery, $period['comparison_start'], $period['comparison_end']);

        $depenses = $filteredQuery->get();
        $comparisonDepenses = $comparisonQuery->get();
        $payload = $this->depenseStatisticsService->buildPayload(
            $depenses,
            $comparisonDepenses,
            $period,
            [
                'period' => $period['period'],
                'month' => $period['month'],
                'year' => $period['year'],
                'date_from' => optional($period['start'])->format('Y-m-d'),
                'date_to' => optional($period['end'])->format('Y-m-d'),
                'selected_categorie' => $request->input('categorie', ''),
                'selected_statut' => $request->input('statut', ''),
                'selected_search' => trim((string) $request->input('search', '')),
                'categories' => $this->availableCategories(),
                'statuts' => $this->availableStatuses(),
            ],
            $baseQuery
        );
        $statistics = $payload['statistics'];

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json($statistics);
        }

        return view('depenses.statistiques', $payload['view']);
    }

    private function buildFilteredQuery(Request $request)
    {
        return $this->buildBaseDepensesQuery($request);
    }

    private function buildBaseDepensesQuery(Request $request): Builder
    {
        $query = Depense::query();

        if ($request->filled('categorie')) {
            if ($this->hasDepenseColumn('categorie')) {
                $query->where('categorie', $request->categorie);
            } elseif ($this->hasDepenseColumn('categorie_id')) {
                $query->where('categorie_id', $this->resolveCategorieId((string) $request->categorie));
            }
        }

        if ($request->filled('statut') && $this->hasDepenseColumn('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $searchable = array_values(array_filter(
                ['description', 'beneficiaire', 'facture_numero'],
                fn (string $column) => $this->hasDepenseColumn($column)
            ));

            if (!empty($searchable)) {
                $query->where(function ($q) use ($search, $searchable) {
                    foreach ($searchable as $idx => $column) {
                        if ($idx === 0) {
                            $q->where($column, 'LIKE', "%{$search}%");
                        } else {
                            $q->orWhere($column, 'LIKE', "%{$search}%");
                        }
                    }
                });
            }
        }

        return $query;
    }

    private function resolveStatisticsPeriod(Request $request): array
    {
        $allowedPeriods = ['month', 'year', 'custom', 'all'];
        $period = (string) $request->input('period', 'month');

        if (!in_array($period, $allowedPeriods, true)) {
            $period = 'month';
        }

        if (!$this->hasDepenseColumn('date_depense')) {
            return [
                'period' => 'all',
                'label' => 'Toutes les periodes',
                'comparison_label' => 'Periode precedente',
                'start' => null,
                'end' => null,
                'comparison_start' => null,
                'comparison_end' => null,
                'month' => (int) now()->month,
                'year' => (int) now()->year,
                'trend_end' => now()->endOfMonth(),
            ];
        }

        $month = max(1, min(12, (int) $request->input('month', now()->month)));
        $year = max(2020, min(2100, (int) $request->input('year', now()->year)));
        $start = null;
        $end = null;
        $comparisonStart = null;
        $comparisonEnd = null;
        $label = 'Toutes les periodes';
        $comparisonLabel = 'Periode precedente';

        if ($period === 'month') {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $comparisonStart = $start->copy()->subMonthNoOverflow()->startOfMonth();
            $comparisonEnd = $comparisonStart->copy()->endOfMonth();
            $label = $this->monthName($month) . ' ' . $year;
            $comparisonLabel = $this->monthName((int) $comparisonStart->month) . ' ' . $comparisonStart->year;
        } elseif ($period === 'year') {
            $start = Carbon::create($year, 1, 1)->startOfYear();
            $end = $start->copy()->endOfYear();
            $comparisonStart = $start->copy()->subYear()->startOfYear();
            $comparisonEnd = $comparisonStart->copy()->endOfYear();
            $label = 'Annee ' . $year;
            $comparisonLabel = 'Annee ' . $comparisonStart->year;
        } elseif ($period === 'custom') {
            $rawStart = $request->input('date_from');
            $rawEnd = $request->input('date_to');
            $start = $rawStart ? Carbon::parse((string) $rawStart)->startOfDay() : now()->startOfMonth();
            $end = $rawEnd ? Carbon::parse((string) $rawEnd)->endOfDay() : now()->endOfMonth();

            if ($start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            $duration = $start->diffInDays($end) + 1;
            $comparisonEnd = $start->copy()->subDay()->endOfDay();
            $comparisonStart = $comparisonEnd->copy()->subDays(max($duration - 1, 0))->startOfDay();
            $label = $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
            $comparisonLabel = $comparisonStart->format('d/m/Y') . ' - ' . $comparisonEnd->format('d/m/Y');
            $month = (int) $start->month;
            $year = (int) $start->year;
        }

        return [
            'period' => $period,
            'label' => $label,
            'comparison_label' => $comparisonLabel,
            'start' => $start,
            'end' => $end,
            'comparison_start' => $comparisonStart,
            'comparison_end' => $comparisonEnd,
            'month' => $month,
            'year' => $year,
            'trend_end' => $end ? $end->copy() : now()->endOfMonth(),
        ];
    }

    private function applyDateRangeToQuery(Builder $query, ?Carbon $start, ?Carbon $end): void
    {
        if (!$this->hasDepenseColumn('date_depense')) {
            return;
        }

        if ($start) {
            $query->where('date_depense', '>=', $start);
        }

        if ($end) {
            $query->where('date_depense', '<=', $end);
        }
    }

    private function buildStatusBreakdown($depenses, float $totalAmount): array
    {
        $labels = $this->statusLabels();

        return $depenses
            ->groupBy(fn (Depense $depense) => (string) ($depense->statut ?? 'inconnu'))
            ->map(function ($items, $statut) use ($labels, $totalAmount) {
                $amount = (float) collect($items)->sum('montant');

                return [
                    'key' => $statut,
                    'label' => $labels[$statut] ?? ucfirst(str_replace('_', ' ', $statut)),
                    'total' => count($items),
                    'montant' => $amount,
                    'share' => $totalAmount > 0 ? round(($amount / $totalAmount) * 100, 1) : 0.0,
                ];
            })
            ->sortByDesc('montant')
            ->values()
            ->all();
    }

    private function buildCategoryBreakdown($depenses, float $totalAmount): array
    {
        $labels = $this->categoryLabels();

        return $depenses
            ->groupBy(fn (Depense $depense) => (string) ($depense->categorie ?? 'autre'))
            ->map(function ($items, $categorie) use ($labels, $totalAmount) {
                $amount = (float) collect($items)->sum('montant');

                return [
                    'key' => $categorie,
                    'label' => $labels[$categorie] ?? ucfirst(str_replace('_', ' ', $categorie)),
                    'total' => count($items),
                    'montant' => $amount,
                    'share' => $totalAmount > 0 ? round(($amount / $totalAmount) * 100, 1) : 0.0,
                ];
            })
            ->sortByDesc('montant')
            ->values()
            ->all();
    }

    private function buildVariationPayload(float $current, float $previous, string $comparisonLabel): array
    {
        $delta = $current - $previous;
        $percentage = null;

        if ($previous > 0) {
            $percentage = round(($delta / $previous) * 100, 1);
        } elseif ($current > 0) {
            $percentage = 100.0;
        }

        return [
            'current' => $current,
            'previous' => $previous,
            'delta' => round($delta, 2),
            'percentage' => $percentage,
            'direction' => $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat'),
            'comparison_label' => $comparisonLabel,
        ];
    }

    private function buildMonthlyTrend(Builder $baseQuery, Carbon $endDate, int $months): array
    {
        $series = collect();

        for ($index = $months - 1; $index >= 0; $index--) {
            $monthStart = $endDate->copy()->subMonths($index)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $monthQuery = clone $baseQuery;
            $this->applyDateRangeToQuery($monthQuery, $monthStart, $monthEnd);
            $items = $monthQuery->get();
            $amount = (float) $items->sum('montant');

            $series->push([
                'label' => strtoupper(substr($this->monthName((int) $monthStart->month), 0, 3)),
                'full_label' => $this->monthName((int) $monthStart->month) . ' ' . $monthStart->year,
                'montant' => $amount,
                'total' => $items->count(),
            ]);
        }

        return $series->all();
    }

    private function availableCategories(): array
    {
        $defaults = array_keys($this->categoryLabels());

        if (!$this->hasDepenseColumn('categorie')) {
            return $defaults;
        }

        return Depense::query()
            ->whereNotNull('categorie')
            ->distinct()
            ->pluck('categorie')
            ->filter()
            ->merge($defaults)
            ->unique()
            ->values()
            ->all();
    }

    private function availableStatuses(): array
    {
        $defaults = array_keys($this->statusLabels());

        if (!$this->hasDepenseColumn('statut')) {
            return $defaults;
        }

        return Depense::query()
            ->whereNotNull('statut')
            ->distinct()
            ->pluck('statut')
            ->filter()
            ->merge($defaults)
            ->unique()
            ->values()
            ->all();
    }

    private function statusLabels(): array
    {
        return [
            'enregistre' => 'Enregistrees',
            'payee' => 'Payees',
            'en_attente' => 'En attente',
        ];
    }

    private function categoryLabels(): array
    {
        return [
            'fournitures' => 'Fournitures',
            'medicaments' => 'Medicaments',
            'loyer' => 'Loyer',
            'personnel' => 'Personnel',
            'utilites' => 'Utilites',
            'maintenance' => 'Maintenance',
            'formation' => 'Formation',
            'autre' => 'Autre',
        ];
    }

    private function monthName(int $month): string
    {
        return [
            1 => 'Janvier',
            2 => 'Fevrier',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Aout',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Decembre',
        ][$month] ?? 'Periode';
    }

    /**
     * Keep only columns that really exist in current depenses schema.
     */
    private function buildPersistablePayload(array $validated, bool $isUpdate): array
    {
        $payload = [];
        foreach ($validated as $key => $value) {
            if ($this->hasDepenseColumn($key)) {
                $payload[$key] = $value;
            }
        }

        if (!$isUpdate && $this->hasDepenseColumn('user_id')) {
            $payload['user_id'] = Auth::id();
        }

        return array_merge($payload, $this->buildLegacyDepenseColumns($validated, $isUpdate));
    }

    /**
     * Add legacy columns used by older depenses schema.
     */
    private function buildLegacyDepenseColumns(array $validated, bool $isUpdate): array
    {
        $legacy = [];
        $userId = Auth::id();

        if ($this->hasDepenseColumn('categorie_id')) {
            $legacy['categorie_id'] = $this->resolveCategorieId($validated['categorie'] ?? 'autre');
        }

        if ($this->hasDepenseColumn('created_by') && !$isUpdate && $userId) {
            $legacy['created_by'] = $userId;
        }

        if ($this->hasDepenseColumn('updated_by') && $userId) {
            $legacy['updated_by'] = $userId;
        }

        if ($this->hasDepenseColumn('methode_paiement') && !empty($validated['mode_paiement'])) {
            $legacy['methode_paiement'] = $this->normalizePaymentMethod($validated['mode_paiement']);
        }

        if ($this->hasDepenseColumn('reference_paiement') && !empty($validated['facture_numero'])) {
            $legacy['reference_paiement'] = $validated['facture_numero'];
        }

        if ($this->hasDepenseColumn('notes') && !empty($validated['details'])) {
            $legacy['notes'] = $validated['details'];
        }

        return $legacy;
    }

    private function resolveCategorieId(string $categorie): int
    {
        $table = $this->resolveCategoriesTable();
        if (!$table || !$this->categoryTableHasColumn('nom')) {
            return 1;
        }

        $labels = [
            'fournitures' => 'Fournitures',
            'medicaments' => 'Medicaments',
            'loyer' => 'Loyer',
            'personnel' => 'Personnel',
            'utilites' => 'Utilites',
            'maintenance' => 'Maintenance',
            'formation' => 'Formation',
            'autre' => 'Autre',
        ];

        $nom = $labels[$categorie] ?? 'Autre';

        $existing = DB::table($table)->where('nom', $nom)->orderBy('id')->first();
        if ($existing) {
            if (
                $this->categoryTableHasColumn('deleted_at')
                && property_exists($existing, 'deleted_at')
                && $existing->deleted_at !== null
            ) {
                DB::table($table)->where('id', $existing->id)->update(['deleted_at' => null]);
            }
            return (int) $existing->id;
        }

        $insert = ['nom' => $nom];
        if ($this->categoryTableHasColumn('description')) {
            $insert['description'] = 'Categorie synchronisee automatiquement';
        }
        if ($this->categoryTableHasColumn('icone')) {
            $insert['icone'] = 'fa-folder';
        }
        if ($this->categoryTableHasColumn('couleur')) {
            $insert['couleur'] = '#3B82F6';
        }
        if ($this->categoryTableHasColumn('is_active')) {
            $insert['is_active'] = true;
        }
        if ($this->categoryTableHasColumn('created_at')) {
            $insert['created_at'] = now();
        }
        if ($this->categoryTableHasColumn('updated_at')) {
            $insert['updated_at'] = now();
        }

        return (int) DB::table($table)->insertGetId($insert);
    }

    private function normalizePaymentMethod(string $mode): string
    {
        $normalized = mb_strtolower(trim($mode));

        if (str_contains($normalized, 'vir')) {
            return 'virement';
        }

        if (str_contains($normalized, 'cart')) {
            return 'carte';
        }

        if (str_contains($normalized, 'cheq') || str_contains($normalized, 'ch')) {
            return 'cheque';
        }

        return 'especes';
    }

    private function hasDepenseColumn(string $column): bool
    {
        if ($this->depensesColumnsCache === null) {
            $cachedColumns = Cache::rememberForever('schema.columns.depenses.v1', function () {
                try {
                    return Schema::getColumnListing('depenses');
                } catch (\Throwable) {
                    return [];
                }
            });

            if ($cachedColumns === []) {
                $this->depensesColumnsCache = [];
            } else {
                $this->depensesColumnsCache = array_fill_keys($cachedColumns, true);
            }
        }

        return isset($this->depensesColumnsCache[$column]);
    }

    private function resolveCategoriesTable(): ?string
    {
        if ($this->depensesCategoriesTableCache !== null) {
            return $this->depensesCategoriesTableCache;
        }

        $this->depensesCategoriesTableCache = Cache::rememberForever('schema.depenses.categories_table.v1', function () {
            try {
                Schema::getColumnListing('categories_depenses');
                return 'categories_depenses';
            } catch (\Throwable) {
                // Fall through to the legacy table name.
            }

            try {
                Schema::getColumnListing('categorie_depenses');
                return 'categorie_depenses';
            } catch (\Throwable) {
                return null;
            }
        });

        return $this->depensesCategoriesTableCache;
    }

    private function categoryTableHasColumn(string $column): bool
    {
        $table = $this->resolveCategoriesTable();

        if (!$table) {
            return false;
        }

        if ($this->depensesCategoriesColumnsCache === null) {
            $cacheKey = 'schema.columns.' . $table . '.v1';
            $cachedColumns = Cache::rememberForever($cacheKey, fn () => Schema::getColumnListing($table));
            $this->depensesCategoriesColumnsCache = array_fill_keys($cachedColumns, true);
        }

        return isset($this->depensesCategoriesColumnsCache[$column]);
    }
}

