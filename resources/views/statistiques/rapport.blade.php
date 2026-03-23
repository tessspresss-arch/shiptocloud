@extends('layouts.app')

@section('title', 'Rapport Statistiques')

@section('content')
@php
    $currency = 'DH';
    $totalFacturesValue = (float) ($totalFactures ?? 0);
    $facturesPayeesValue = (float) ($facturesPayees ?? 0);
    $facturesPeriodeValue = (float) ($facturesPeriode ?? 0);
    $pendingValue = max($totalFacturesValue - $facturesPayeesValue, 0);
    $paymentRate = $totalFacturesValue > 0 ? ($facturesPayeesValue / $totalFacturesValue) * 100 : 0;

    $formatAmount = static function ($amount) use ($currency): string {
        return number_format((float) $amount, 2, ',', ' ') . ' ' . $currency;
    };
@endphp

<style>
    .stats-report-page {
        --sr-primary: #0b7ac7;
        --sr-primary-2: #0f5f9e;
        --sr-success: #10b981;
        --sr-warning: #f59e0b;
        --sr-danger: #ef4444;
        --sr-card: #ffffff;
        --sr-border: #dce8f6;
        --sr-bg: linear-gradient(160deg, #f4f9ff 0%, #f8fbff 55%, #f2f7ff 100%);
        --sr-title: #102b4b;
        --sr-text: #3d5777;
        --sr-muted: #6d86a6;
        --sr-table-head: #f5f9ff;

        width: 100%;
        max-width: none;
        padding: 14px 16px 24px;
        border: 1px solid #deebf8;
        border-radius: 16px;
        background: var(--sr-bg);
    }

    .stats-report-shell {
        width: 100%;
        max-width: none;
    }

    .stats-report-head {
        border: 1px solid var(--sr-border);
        border-top: 4px solid var(--sr-primary);
        border-radius: 14px;
        background: linear-gradient(140deg, #f3f9ff 0%, #e7f2ff 100%);
        padding: clamp(16px, 2.2vw, 26px);
        margin-bottom: 16px;
    }

    .stats-report-title {
        margin: 0;
        font-size: clamp(1.6rem, 2.7vw, 2.2rem);
        color: var(--sr-title);
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        line-height: 1.1;
    }

    .stats-report-period {
        margin: 10px 0 0;
        color: var(--sr-muted);
        font-size: 0.95rem;
    }

    .stats-report-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .sr-btn {
        min-height: 42px;
        border-radius: 10px;
        border: 1px solid transparent;
        padding: 10px 18px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: .18s ease;
    }

    .sr-btn-primary {
        background: linear-gradient(135deg, var(--sr-primary), var(--sr-primary-2));
        color: #fff;
    }

    .sr-btn-primary:hover {
        filter: brightness(1.03);
        color: #fff;
    }

    .sr-btn-secondary {
        background: #f1f5f9;
        border-color: #d4e1ef;
        color: #334155;
    }

    .sr-btn-secondary:hover {
        background: #e7eef7;
        color: #334155;
    }

    .sr-card {
        background: var(--sr-card);
        border: 1px solid var(--sr-border);
        border-radius: 14px;
        box-shadow: 0 16px 24px -30px rgba(16, 57, 104, .9);
        overflow: hidden;
        margin-bottom: 16px;
    }

    .sr-card-head {
        border-top: 4px solid var(--sr-primary);
        background: linear-gradient(135deg, #f2f8ff 0%, #e7f2ff 100%);
        padding: 12px 16px;
    }

    .sr-card-head h2 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--sr-title);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .sr-table-wrap {
        overflow-x: auto;
    }

    .sr-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }

    .sr-table thead {
        background: var(--sr-table-head);
        border-bottom: 2px solid var(--sr-border);
    }

    .sr-table th {
        padding: 11px 14px;
        text-align: left;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #355172;
        font-weight: 800;
        white-space: nowrap;
    }

    .sr-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2ebf6;
        color: var(--sr-text);
        font-weight: 600;
        vertical-align: middle;
    }

    .sr-table tbody tr:hover {
        background: #f9fcff;
    }

    .sr-metric {
        color: var(--sr-title);
        font-weight: 800;
    }

    .sr-empty {
        text-align: center;
        color: var(--sr-muted);
        padding: 36px 12px;
    }

    .sr-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: .76rem;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .sr-badge.up {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .sr-badge.down {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .sr-trend {
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .sr-trend.up { color: #059669; }
    .sr-trend.down { color: #dc2626; }
    .sr-trend.stable { color: #6b7280; }

    .sr-kpi-grid {
        padding: 16px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .sr-kpi {
        border: 1px solid var(--sr-border);
        border-radius: 12px;
        padding: 14px;
        text-align: center;
        background: linear-gradient(135deg, #f8fbff 0%, #edf5ff 100%);
    }

    .sr-kpi.green {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-color: #86efac;
    }

    .sr-kpi.yellow {
        background: linear-gradient(135deg, #fef9c3 0%, #fde68a 100%);
        border-color: #fcd34d;
    }

    .sr-kpi.red {
        background: linear-gradient(135deg, #fff1f2 0%, #fee2e2 100%);
        border-color: #fecaca;
    }

    .sr-kpi-icon {
        font-size: 1.3rem;
        margin-bottom: 7px;
        color: #3b82f6;
    }

    .sr-kpi.green .sr-kpi-icon { color: #10b981; }
    .sr-kpi.yellow .sr-kpi-icon { color: #d97706; }
    .sr-kpi.red .sr-kpi-icon { color: #ef4444; }

    .sr-kpi-label {
        color: var(--sr-muted);
        font-size: .83rem;
        margin: 0;
    }

    .sr-kpi-value {
        margin: 7px 0;
        color: var(--sr-title);
        font-weight: 900;
        font-size: clamp(1.5rem, 2.4vw, 2rem);
        line-height: 1;
    }

    .sr-kpi-sub {
        margin: 0;
        color: var(--sr-text);
        font-size: .84rem;
    }

    .sr-fin-grid {
        padding: 16px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .sr-fin-box {
        border: 1px solid var(--sr-border);
        border-radius: 12px;
        background: #f8fbff;
        padding: 14px;
    }

    .sr-fin-box p {
        margin: 0;
    }

    .sr-fin-label {
        color: var(--sr-muted);
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 700;
    }

    .sr-fin-value {
        margin-top: 8px !important;
        font-size: 1.45rem;
        font-weight: 900;
        color: var(--sr-title);
    }

    .sr-fin-box.primary .sr-fin-value { color: var(--sr-primary); }
    .sr-fin-box.success .sr-fin-value { color: var(--sr-success); }
    .sr-fin-box.warning .sr-fin-value { color: var(--sr-warning); }
    .sr-fin-box.danger .sr-fin-value { color: var(--sr-danger); }

    body.dark-mode .stats-report-page {
        --sr-card: #12243b;
        --sr-border: #2e5179;
        --sr-bg: linear-gradient(160deg, #0f1f31 0%, #0d1b2c 55%, #101f30 100%);
        --sr-title: #d5e7ff;
        --sr-text: #c2d8f1;
        --sr-muted: #93afcb;
        --sr-table-head: #173251;
    }

    body.dark-mode .stats-report-head {
        background: linear-gradient(135deg, #14345a 0%, #153a62 100%);
        border-color: #2e5179;
    }

    body.dark-mode .stats-report-title {
        color: #e7f2ff;
    }

    body.dark-mode .stats-report-period {
        color: #a6bfdc;
    }

    body.dark-mode .sr-btn-secondary {
        background: #1a3656;
        border-color: #355b84;
        color: #d8eaff;
    }

    body.dark-mode .sr-btn-secondary:hover {
        background: #234567;
        color: #ffffff;
    }

    body.dark-mode .sr-card-head {
        background: linear-gradient(135deg, #173251 0%, #14304b 100%);
        border-top-color: #2893d8;
    }

    body.dark-mode .sr-card-head h2 {
        color: #e7f2ff;
    }

    body.dark-mode .sr-table th {
        color: #b9d2ed;
    }

    body.dark-mode .sr-table td {
        border-bottom-color: #26486f;
    }

    body.dark-mode .sr-table tbody tr:hover {
        background: #173456;
    }

    body.dark-mode .sr-badge.up {
        background: #18372b;
        color: #9fe3c8;
        border-color: #2f7157;
    }

    body.dark-mode .sr-badge.down {
        background: #3a2327;
        color: #ffc8d1;
        border-color: #7b3b46;
    }

    body.dark-mode .sr-trend.up { color: #6ee7b7; }
    body.dark-mode .sr-trend.down { color: #fca5a5; }
    body.dark-mode .sr-trend.stable { color: #9ab1cc; }

    body.dark-mode .sr-kpi {
        background: #132b43;
        border-color: #325578;
    }

    body.dark-mode .sr-kpi.green {
        background: #163829;
        border-color: #2f7157;
    }

    body.dark-mode .sr-kpi.yellow {
        background: #3a2f1a;
        border-color: #8a6324;
    }

    body.dark-mode .sr-kpi.red {
        background: #3a2327;
        border-color: #7b3b46;
    }

    body.dark-mode .sr-fin-box {
        background: #132b43;
        border-color: #325578;
    }

    @media (max-width: 1200px) {
        .sr-kpi-grid,
        .sr-fin-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .stats-report-page {
            padding: 10px;
        }

        .stats-report-actions {
            justify-content: stretch;
        }

        .sr-btn {
            width: 100%;
        }
    }

    @media (max-width: 640px) {
        .sr-kpi-grid,
        .sr-fin-grid {
            grid-template-columns: 1fr;
        }

        .sr-table {
            min-width: 640px;
        }
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        html,
        body {
            background: #fff !important;
        }

        body * {
            visibility: hidden !important;
        }

        .stats-report-page,
        .stats-report-page * {
            visibility: visible !important;
        }

        .stats-report-page {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            box-shadow: none !important;
            background: #fff !important;
        }

        .stats-report-shell {
            width: 100% !important;
            max-width: 100% !important;
        }

        .stats-report-actions {
            display: none !important;
        }

        .sr-card,
        .sr-card-head,
        .sr-kpi,
        .sr-fin-box {
            page-break-inside: avoid;
            break-inside: avoid;
            box-shadow: none !important;
        }
    }
</style>

<div class="stats-report-page">
    <div class="stats-report-shell">
        <div class="stats-report-head">
            <h1 class="stats-report-title">
                <i class="fas fa-chart-column"></i>
                Rapport Statistiques Detaille
            </h1>
            <p class="stats-report-period">
                Periode: {{ now()->subDays($periode)->format('d/m/Y') }} - {{ now()->format('d/m/Y') }}
            </p>
        </div>

        <div class="stats-report-actions">
            <a href="{{ route('statistiques', ['periode' => $periode]) }}" class="sr-btn sr-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Retour au tableau
            </a>
            <button type="button" onclick="window.print()" class="sr-btn sr-btn-primary">
                <i class="fas fa-print"></i>
                Imprimer
            </button>
        </div>

        <div class="sr-card">
            <div class="sr-card-head">
                <h2><i class="fas fa-chart-line"></i> Synthese mensuelle</h2>
            </div>
            <div class="sr-table-wrap">
                <table class="sr-table">
                    <thead>
                        <tr>
                            <th>Metrique</th>
                            <th>Mois courant</th>
                            <th>Mois precedent</th>
                            <th>Variation</th>
                            <th>Tendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($rapportSynthese && count($rapportSynthese) > 0)
                            @foreach($rapportSynthese as $item)
                                @php
                                    $metric = $item['metric'] ?? 'N/A';
                                    $isRevenueMetric = \Illuminate\Support\Str::contains($metric, 'Revenus');
                                    $tendance = $item['tendance'] ?? 'stable';
                                    $variation = (float) ($item['variation'] ?? 0);
                                @endphp
                                <tr>
                                    <td class="sr-metric">{{ $metric }}</td>
                                    <td>{{ $item['courant'] ?? '-' }}{{ $isRevenueMetric ? ' ' . $currency : '' }}</td>
                                    <td>{{ $item['precedent'] ?? '-' }}{{ $isRevenueMetric ? ' ' . $currency : '' }}</td>
                                    <td>
                                        <span class="sr-badge {{ $variation >= 0 ? 'up' : 'down' }}">
                                            {{ $variation >= 0 ? '+' : '' }}{{ $variation }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if($tendance === 'up')
                                            <span class="sr-trend up"><i class="fas fa-arrow-trend-up"></i> Hausse</span>
                                        @elseif($tendance === 'down')
                                            <span class="sr-trend down"><i class="fas fa-arrow-trend-down"></i> Baisse</span>
                                        @else
                                            <span class="sr-trend stable"><i class="fas fa-minus"></i> Stable</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="sr-empty">Aucune donnee disponible</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sr-card">
            <div class="sr-card-head">
                <h2><i class="fas fa-bullseye"></i> Indicateurs cles de performance</h2>
            </div>
            <div class="sr-kpi-grid">
                <div class="sr-kpi">
                    <div class="sr-kpi-icon"><i class="fas fa-users"></i></div>
                    <p class="sr-kpi-label">Total Patients</p>
                    <div class="sr-kpi-value">{{ $totalPatients ?? 0 }}</div>
                    <p class="sr-kpi-sub">+{{ $nouveauxPatients ?? 0 }} cette periode</p>
                </div>

                <div class="sr-kpi green">
                    <div class="sr-kpi-icon"><i class="fas fa-stethoscope"></i></div>
                    <p class="sr-kpi-label">Consultations</p>
                    <div class="sr-kpi-value">{{ $totalConsultations ?? 0 }}</div>
                    <p class="sr-kpi-sub">{{ $consultationsPeriode ?? 0 }} cette periode</p>
                </div>

                <div class="sr-kpi yellow">
                    <div class="sr-kpi-icon"><i class="fas fa-calendar-check"></i></div>
                    <p class="sr-kpi-label">Rendez-vous</p>
                    <div class="sr-kpi-value">{{ $totalRendezVous ?? 0 }}</div>
                    <p class="sr-kpi-sub">{{ $rendezVousPeriode ?? 0 }} cette periode</p>
                </div>

                <div class="sr-kpi red">
                    <div class="sr-kpi-icon"><i class="fas fa-sack-dollar"></i></div>
                    <p class="sr-kpi-label">Revenus</p>
                    <div class="sr-kpi-value">{{ number_format($totalFacturesValue, 0, ',', ' ') }} {{ $currency }}</div>
                    <p class="sr-kpi-sub">{{ number_format($facturesPeriodeValue, 0, ',', ' ') }} {{ $currency }} cette periode</p>
                </div>
            </div>
        </div>

        <div class="sr-card">
            <div class="sr-card-head">
                <h2><i class="fas fa-user-doctor"></i> Performance par medecin</h2>
            </div>
            <div class="sr-table-wrap">
                <table class="sr-table">
                    <thead>
                        <tr>
                            <th>Medecin</th>
                            <th>Consultations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($statsMedecins && count($statsMedecins) > 0)
                            @foreach($statsMedecins as $medecin)
                                <tr>
                                    <td class="sr-metric">{{ $medecin->name ?? 'N/A' }}</td>
                                    <td>{{ $medecin->consultations_count ?? 0 }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2" class="sr-empty">Aucun medecin disponible</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sr-card">
            <div class="sr-card-head">
                <h2><i class="fas fa-wallet"></i> Resume financier</h2>
            </div>
            <div class="sr-fin-grid">
                <div class="sr-fin-box primary">
                    <p class="sr-fin-label">Total factures</p>
                    <p class="sr-fin-value">{{ $formatAmount($totalFacturesValue) }}</p>
                </div>

                <div class="sr-fin-box success">
                    <p class="sr-fin-label">Factures payees</p>
                    <p class="sr-fin-value">{{ $formatAmount($facturesPayeesValue) }}</p>
                </div>

                <div class="sr-fin-box warning">
                    <p class="sr-fin-label">Montant en attente</p>
                    <p class="sr-fin-value">{{ $formatAmount($pendingValue) }}</p>
                </div>

                <div class="sr-fin-box danger">
                    <p class="sr-fin-label">Taux de paiement</p>
                    <p class="sr-fin-value">{{ number_format($paymentRate, 1, ',', ' ') }}%</p>
                </div>
            </div>
        </div>
    </div>
</div>
@if(request()->boolean('print'))
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            window.print();
        }, 250);
    });
    </script>
    @endpush
@endif
@endsection
