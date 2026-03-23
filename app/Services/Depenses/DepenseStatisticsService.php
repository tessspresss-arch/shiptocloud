<?php

namespace App\Services\Depenses;

use App\Models\Depense;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DepenseStatisticsService
{
    public function buildPayload(Collection $depenses, Collection $comparisonDepenses, array $period, array $filters, Builder $baseQuery): array
    {
        $totalAmount = (float) $depenses->sum('montant');
        $comparisonAmount = (float) $comparisonDepenses->sum('montant');
        $totalCount = $depenses->count();
        $comparisonCount = $comparisonDepenses->count();
        $paidAmount = (float) $depenses->filter(fn (Depense $depense) => (string) ($depense->statut ?? '') === 'payee')->sum('montant');
        $pendingAmount = (float) $depenses->filter(fn (Depense $depense) => (string) ($depense->statut ?? '') === 'en_attente')->sum('montant');
        $topExpense = $depenses->sortByDesc('montant')->first();
        $statusBreakdown = $this->buildStatusBreakdown($depenses, $totalAmount);
        $categoryBreakdown = $this->buildCategoryBreakdown($depenses, $totalAmount);
        $trendMonths = $period['period'] === 'year' || $period['period'] === 'all' ? 12 : 6;

        $statistics = [
            'total_depenses' => $totalCount,
            'montant_total' => $totalAmount,
            'ticket_moyen' => $totalCount > 0 ? round($totalAmount / $totalCount, 2) : 0.0,
            'montant_paye' => $paidAmount,
            'montant_en_attente' => $pendingAmount,
            'taux_paiement' => $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100, 1) : 0.0,
            'variation_montant' => $this->buildVariationPayload($totalAmount, $comparisonAmount, $period['comparison_label']),
            'variation_volume' => $this->buildVariationPayload((float) $totalCount, (float) $comparisonCount, $period['comparison_label']),
            'par_statut' => $statusBreakdown,
            'par_categorie' => $categoryBreakdown,
            'top_categorie' => $categoryBreakdown[0] ?? null,
            'plus_grosse_depense' => $topExpense ? [
                'description' => (string) ($topExpense->description ?? 'Depense sans description'),
                'montant' => (float) $topExpense->montant,
                'date' => optional($topExpense->date_depense)->format('d/m/Y'),
                'statut' => (string) ($topExpense->statut ?? 'enregistre'),
            ] : null,
            'tendance_mensuelle' => $this->buildMonthlyTrend($baseQuery, $period['trend_end'], $trendMonths),
            'top_depenses' => $depenses->sortByDesc('montant')->take(5)->map(fn (Depense $depense) => [
                'description' => (string) ($depense->description ?? 'Depense sans description'),
                'categorie' => (string) ($depense->categorie ?? 'autre'),
                'statut' => (string) ($depense->statut ?? 'enregistre'),
                'montant' => (float) $depense->montant,
                'date' => optional($depense->date_depense)->format('d/m/Y'),
                'beneficiaire' => (string) ($depense->beneficiaire ?? ''),
            ])->values(),
            'periode' => [
                'key' => $period['period'],
                'label' => $period['label'],
                'comparison_label' => $period['comparison_label'],
            ],
            'filtres' => $filters,
        ];

        $currency = fn ($value) => number_format((float) $value, 2, ',', ' ') . ' DH';
        $signedCurrency = function ($value) {
            $prefix = (float) $value > 0 ? '+' : '';
            return $prefix . number_format((float) $value, 2, ',', ' ') . ' DH';
        };
        $signedPercent = function ($value) {
            if ($value === null) {
                return '--';
            }
            $prefix = (float) $value > 0 ? '+' : '';
            return $prefix . number_format((float) $value, 1, ',', ' ') . '%';
        };

        $monthOptions = [1 => 'Janvier', 2 => 'Fevrier', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Aout', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Decembre'];
        $selectedPeriod = $statistics['filtres']['period'] ?? 'month';
        $selectedMonth = (int) ($statistics['filtres']['month'] ?? now()->month);
        $selectedYear = (int) ($statistics['filtres']['year'] ?? now()->year);
        $selectedCategorie = (string) ($statistics['filtres']['selected_categorie'] ?? '');
        $selectedStatut = (string) ($statistics['filtres']['selected_statut'] ?? '');
        $selectedSearch = (string) ($statistics['filtres']['selected_search'] ?? '');
        $selectedDateFrom = (string) ($statistics['filtres']['date_from'] ?? '');
        $selectedDateTo = (string) ($statistics['filtres']['date_to'] ?? '');
        $trendSeries = collect($statistics['tendance_mensuelle'] ?? []);
        $trendMax = max((float) ($trendSeries->max('montant') ?? 0), 1);
        $trendWidth = 100;
        $trendHeight = 58;
        $trendBaseY = 48;
        $trendTopOffset = 6;
        $trendSidePadding = 8;
        $trendStep = $trendSeries->count() > 1 ? (($trendWidth - ($trendSidePadding * 2)) / ($trendSeries->count() - 1)) : 0;
        $trendPointCoordinates = $trendSeries->values()->map(function ($point, $index) use ($trendBaseY, $trendHeight, $trendMax, $trendSidePadding, $trendStep, $trendTopOffset) {
            $x = $trendSidePadding + ($index * $trendStep);
            $normalized = ((float) ($point['montant'] ?? 0) / $trendMax);
            $y = $trendBaseY - ($normalized * ($trendHeight - ($trendTopOffset * 2) - 8));
            return ['x' => round($x, 2), 'y' => round($y, 2)];
        });
        $trendPoints = $trendPointCoordinates->map(fn (array $point) => $point['x'] . ',' . $point['y'])->implode(' ');
        $trendAreaPoints = trim('0,' . $trendBaseY . ' ' . $trendPoints . ' ' . $trendWidth . ',' . $trendBaseY);
        $topCategorie = $statistics['top_categorie'] ?? null;
        $largestExpense = $statistics['plus_grosse_depense'] ?? null;
        $showMonthFields = in_array($selectedPeriod, ['month', 'year'], true);
        $showCustomFields = $selectedPeriod === 'custom';

        return [
            'statistics' => $statistics,
            'view' => [
                'stats' => $statistics,
                'currency' => $currency,
                'signedCurrency' => $signedCurrency,
                'signedPercent' => $signedPercent,
                'statusLabels' => $this->statusLabels(),
                'categoryLabels' => $this->categoryLabels(),
                'monthOptions' => $monthOptions,
                'selectedPeriod' => $selectedPeriod,
                'selectedMonth' => $selectedMonth,
                'selectedYear' => $selectedYear,
                'selectedCategorie' => $selectedCategorie,
                'selectedStatut' => $selectedStatut,
                'selectedSearch' => $selectedSearch,
                'selectedDateFrom' => $selectedDateFrom,
                'selectedDateTo' => $selectedDateTo,
                'trendSeries' => $trendSeries,
                'trendPoints' => $trendPoints,
                'trendAreaPoints' => $trendAreaPoints,
                'trendPointCoordinates' => $trendPointCoordinates,
                'topCategorie' => $topCategorie,
                'largestExpense' => $largestExpense,
                'showMonthFields' => $showMonthFields,
                'showCustomFields' => $showCustomFields,
            ],
        ];
    }

    private function buildStatusBreakdown(Collection $depenses, float $totalAmount): array
    {
        $labels = $this->statusLabels();
        return $depenses->groupBy(fn (Depense $depense) => (string) ($depense->statut ?? 'inconnu'))->map(function ($items, $statut) use ($labels, $totalAmount) {
            $amount = (float) collect($items)->sum('montant');
            return [
                'key' => $statut,
                'label' => $labels[$statut] ?? ucfirst(str_replace('_', ' ', $statut)),
                'total' => count($items),
                'montant' => $amount,
                'share' => $totalAmount > 0 ? round(($amount / $totalAmount) * 100, 1) : 0.0,
            ];
        })->sortByDesc('montant')->values()->all();
    }

    private function buildCategoryBreakdown(Collection $depenses, float $totalAmount): array
    {
        $labels = $this->categoryLabels();
        return $depenses->groupBy(fn (Depense $depense) => (string) ($depense->categorie ?? 'autre'))->map(function ($items, $categorie) use ($labels, $totalAmount) {
            $amount = (float) collect($items)->sum('montant');
            return [
                'key' => $categorie,
                'label' => $labels[$categorie] ?? ucfirst(str_replace('_', ' ', $categorie)),
                'total' => count($items),
                'montant' => $amount,
                'share' => $totalAmount > 0 ? round(($amount / $totalAmount) * 100, 1) : 0.0,
            ];
        })->sortByDesc('montant')->values()->all();
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
            $monthQuery->where('date_depense', '>=', $monthStart)->where('date_depense', '<=', $monthEnd);
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

    private function statusLabels(): array
    {
        return ['enregistre' => 'Enregistrees', 'payee' => 'Payees', 'en_attente' => 'En attente'];
    }

    private function categoryLabels(): array
    {
        return ['fournitures' => 'Fournitures', 'medicaments' => 'Medicaments', 'loyer' => 'Loyer', 'personnel' => 'Personnel', 'utilites' => 'Utilites', 'maintenance' => 'Maintenance', 'formation' => 'Formation', 'autre' => 'Autre'];
    }

    private function monthName(int $month): string
    {
        return match ($month) {
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
            default => 'Decembre',
        };
    }
}
