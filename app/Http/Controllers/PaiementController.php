<?php

namespace App\Http\Controllers;

use App\Services\Billing\PaiementLedgerService;
use App\Services\Exports\Utf8CsvExporter;
use App\Services\Pdf\PdfBuilder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaiementController extends Controller
{
    public function index(Request $request, PaiementLedgerService $ledgerService)
    {
        $ledger = $ledgerService->build($request);
        $perPage = max(10, min(100, (int) $request->integer('per_page', 15)));
        $currentPage = max(1, (int) $request->integer('page', 1));
        $items = $ledger->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator(
            $items,
            $ledger->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'encaissements' => (float) $ledger->filter(fn (array $row) => $row['montant'] > 0)->sum('montant'),
            'decaissements' => abs((float) $ledger->filter(fn (array $row) => $row['montant'] < 0)->sum('montant')),
            'operations' => $ledger->count(),
        ];

        $modeOptions = $ledger
            ->pluck('mode_paiement')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->mapWithKeys(fn (string $mode) => [(string) Str::of($mode)->lower()->ascii()->replace([' ', '/', '_'], '-') => $mode])
            ->all();

        $statusOptions = $ledger
            ->pluck('statut')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->mapWithKeys(fn (string $status) => [(string) Str::of($status)->lower()->ascii()->replace([' ', '/', '_'], '-') => ucfirst(str_replace('_', ' ', $status))])
            ->all();

        $sourceBreakdown = $ledger
            ->groupBy('source_label')
            ->map(fn ($rows, string $label) => [
                'label' => $label,
                'count' => $rows->count(),
                'amount' => (float) $rows->sum('montant'),
            ])
            ->sortByDesc('count')
            ->values();

        $modeBreakdown = $ledger
            ->groupBy(fn (array $row) => $row['mode_paiement'] ?: 'Non defini')
            ->map(fn ($rows, string $label) => [
                'label' => $label,
                'count' => $rows->count(),
                'amount' => (float) $rows->sum('montant'),
            ])
            ->sortByDesc('count')
            ->values();

        return view('paiements.index', [
            'entries' => $paginated,
            'stats' => $stats,
            'selectedSource' => (string) $request->input('source', ''),
            'selectedMode' => (string) $request->input('mode', ''),
            'selectedStatus' => (string) $request->input('statut', ''),
            'selectedDateFrom' => (string) $request->input('date_from', ''),
            'selectedDateTo' => (string) $request->input('date_to', ''),
            'modeOptions' => $modeOptions,
            'statusOptions' => $statusOptions,
            'sourceBreakdown' => $sourceBreakdown,
            'modeBreakdown' => $modeBreakdown,
            'search' => trim((string) $request->input('search', '')),
        ]);
    }

    public function show(string $source, int $id, PaiementLedgerService $ledgerService)
    {
        $entry = $ledgerService->find($source, $id);

        abort_if($entry === null, 404);

        return view('paiements.show', [
            'entry' => $entry,
        ]);
    }

    public function export(Request $request, PaiementLedgerService $ledgerService, Utf8CsvExporter $csvExporter): StreamedResponse
    {
        $rows = $ledgerService->build($request)->map(fn (array $row) => [
            $row['source_label'],
            $row['reference'],
            $row['patient'] ?? '',
            $row['tiers'] ?? '',
            $row['medecin'] ?? '',
            optional($row['date_operation'])->format('Y-m-d H:i'),
            $row['mode_paiement'],
            $row['statut'],
            number_format((float) $row['montant'], 2, '.', ''),
        ]);

        return $csvExporter->download(
            'paiements-' . now()->format('Y-m-d-His') . '.csv',
            ['Source', 'Reference', 'Patient', 'Tiers', 'Medecin', 'Date operation', 'Mode paiement', 'Statut', 'Montant'],
            $rows
        );
    }

    public function exportPdf(Request $request, PaiementLedgerService $ledgerService, PdfBuilder $pdfBuilder)
    {
        $ledger = $ledgerService->build($request)->values();
        $stats = [
            'encaissements' => (float) $ledger->filter(fn (array $row) => $row['montant'] > 0)->sum('montant'),
            'decaissements' => abs((float) $ledger->filter(fn (array $row) => $row['montant'] < 0)->sum('montant')),
            'operations' => $ledger->count(),
        ];

        return $pdfBuilder
            ->fromView('paiements.pdf', [
                'entries' => $ledger,
                'stats' => $stats,
                'generatedAt' => now(),
            ])
            ->download('paiements-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
