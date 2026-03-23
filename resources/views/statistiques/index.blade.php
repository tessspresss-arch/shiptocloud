@extends('layouts.app')

@section('title', 'Statistiques')

@section('content')
@php
    $totalFacturesValue = (float) ($totalFactures ?? 0);
    $facturesPayeesValue = (float) ($facturesPayees ?? 0);
    $paymentRate = $totalFacturesValue > 0 ? ($facturesPayeesValue / $totalFacturesValue) * 100 : 0;
    $pendingAmount = $totalFacturesValue - $facturesPayeesValue;
@endphp

<style>
    .stats-page {
        --stats-primary: #0b7ac7;
        --stats-primary-dark: #0863a6;
        --stats-accent: #14b8a6;
        --stats-success: #16a34a;
        --stats-warning: #f59e0b;
        --stats-danger: #ef4444;
        --stats-bg: #f3f8ff;
        --stats-card: #ffffff;
        --stats-border: #d6e3f3;
        --stats-title: #102a4a;
        --stats-text: #425977;
        --stats-muted: #6b7f9a;
        width: 100%;
        padding: 14px 16px 26px;
    }

    .stats-shell {
        width: 100%;
        max-width: none;
    }

    .stats-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        padding: 2px 0 18px;
        border-bottom: 1px solid #dce8f5;
    }

    .stats-head-main {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex-wrap: wrap;
    }

    .stats-back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 48px;
        padding: 0 18px 0 14px;
        border-radius: 16px;
        white-space: nowrap;
        border: 1px solid rgba(191, 207, 223, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(245, 249, 253, 0.92) 100%);
        color: #385674;
        font-weight: 700;
        letter-spacing: -0.01em;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92), 0 16px 28px -26px rgba(15, 23, 42, 0.28);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .stats-back-btn:hover {
        border-color: rgba(44, 123, 229, 0.3);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 244, 251, 0.98) 100%);
        color: #1f6fa3;
        transform: translateY(-1px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.96), 0 18px 32px -24px rgba(31, 111, 163, 0.22);
        text-decoration: none;
    }

    .stats-back-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(44, 123, 229, 0.1);
        color: #2c7be5;
        flex-shrink: 0;
    }

    .stats-head-title {
        min-width: 0;
    }

    .stats-title-row {
        display: flex;
        align-items: center;
        gap: 11px;
        flex-wrap: wrap;
    }

    .stats-title-row i {
        font-size: 1.6rem;
        color: #3b82f6;
    }

    .stats-title-row h1 {
        margin: 0;
        color: #1e3a8a;
        font-size: clamp(1.45rem, 2.2vw, 1.95rem);
        line-height: 1.1;
        font-weight: 700;
    }

    .stats-head-title p {
        margin: 7px 0 0;
        color: #5f7896;
        font-size: .95rem;
        font-weight: 600;
    }

    .stats-count-badge {
        background: linear-gradient(90deg, #3b82f6 60%, #1e3a8a 100%);
        color: #fff;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: .9rem;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(59, 130, 246, .12);
        white-space: nowrap;
    }

    .stats-head-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .stats-head-btn {
        height: 40px;
        border-radius: 10px;
        padding: 0 16px;
        border: 1px solid transparent;
        font-size: .92rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        transition: all .2s ease;
    }

    .stats-head-btn.secondary {
        background: #eef2f7;
        border-color: #dbe5f1;
        color: #486482;
    }

    .stats-head-btn.secondary:hover {
        background: #e3ebf4;
        color: #2c4b6c;
    }

    .stats-head-btn.success {
        background: #11b47a;
        border-color: #11b47a;
        color: #fff;
    }

    .stats-head-btn.success:hover {
        background: #0fa06d;
        border-color: #0fa06d;
        color: #fff;
    }

    .period-card {
        border: 1px solid var(--stats-border);
        border-radius: 14px;
        background: var(--stats-card);
        padding: 10px;
        margin-bottom: 14px;
        box-shadow: 0 10px 24px -30px rgba(30, 84, 146, .7);
    }

    .period-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .period-btn {
        border: 1px solid #c8d9ed;
        background: #f8fbff;
        color: #355273;
        border-radius: 10px;
        min-height: 40px;
        padding: 8px 14px;
        font-weight: 700;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: .2s ease;
    }

    .period-btn:hover,
    .period-btn.active {
        border-color: var(--stats-primary);
        background: linear-gradient(120deg, #0b7ac7 0%, #148bda 100%);
        color: #fff;
        box-shadow: 0 8px 16px -14px rgba(11, 122, 199, .8);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .stat-card {
        background: var(--stats-card);
        border: 1px solid var(--stats-border);
        border-radius: 14px;
        padding: 16px 16px 14px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 24px -28px rgba(16, 57, 104, .85);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 4px;
        background: var(--stats-primary);
    }

    .stat-card.accent::before { background: #22c0d6; }
    .stat-card.secondary::before { background: var(--stats-success); }
    .stat-card.warning::before { background: var(--stats-warning); }

    .stat-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .stat-label {
        margin: 0;
        color: var(--stats-text);
        font-weight: 700;
        font-size: .95rem;
    }

    .stat-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--stats-primary);
        background: #eaf4ff;
        font-size: 14px;
    }

    .stat-card.secondary .stat-icon {
        color: var(--stats-success);
        background: #e8f9ef;
    }

    .stat-card.warning .stat-icon {
        color: #b86800;
        background: #fff3df;
    }

    .stat-number {
        margin: 12px 0 6px;
        color: var(--stats-title);
        font-size: 2rem;
        font-weight: 900;
        line-height: 1;
    }

    .stat-hint {
        margin: 0;
        color: var(--stats-muted);
        font-size: .95rem;
    }

    .trend-up {
        color: var(--stats-success);
        font-weight: 800;
    }

    .panel-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }

    .card-modern {
        border: 1px solid var(--stats-border);
        border-radius: 14px;
        background: var(--stats-card);
        overflow: hidden;
        box-shadow: 0 14px 24px -28px rgba(16, 57, 104, .85);
    }

    .section-header {
        border-bottom: 1px solid var(--stats-border);
        background: linear-gradient(180deg, #f2f9ff 0%, #e7f3ff 100%);
        padding: 14px 16px;
    }

    .section-header h2 {
        margin: 0;
        color: var(--stats-title);
        font-size: 1.07rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .section-body {
        padding: 14px 16px;
    }

    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }

    .table-modern thead {
        background: #f7fbff;
        border-bottom: 2px solid #d8e5f4;
    }

    .table-modern th {
        font-size: .82rem;
        letter-spacing: .2px;
        color: #385273;
        text-transform: uppercase;
        font-weight: 800;
        padding: 10px 12px;
        text-align: left;
    }

    .table-modern td {
        padding: 11px 12px;
        border-bottom: 1px solid #e2ebf6;
        color: #203a59;
        font-weight: 600;
    }

    .table-modern tbody tr:hover {
        background: #f9fcff;
    }

    .empty-row {
        margin: 0;
        text-align: center;
        color: #7891ad;
        padding: 20px 0;
    }

    .finance-item {
        margin-bottom: 14px;
    }

    .finance-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 7px;
        color: #324e70;
        font-weight: 700;
    }

    .finance-value {
        color: #0f2e53;
        font-weight: 800;
    }

    .finance-value.success {
        color: var(--stats-success);
    }

    .progress-track {
        width: 100%;
        height: 9px;
        border-radius: 999px;
        overflow: hidden;
        background: #dfe8f3;
    }

    .progress-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #10b981 0%, #1ccc96 100%);
    }

    .finance-summary {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        border-top: 1px solid #e2ebf6;
        padding-top: 12px;
    }

    .summary-item {
        text-align: center;
    }

    .summary-label {
        margin: 0;
        color: #6b7f9a;
        font-size: .9rem;
        font-weight: 600;
    }

    .summary-value {
        margin: 3px 0 0;
        font-size: 1.6rem;
        font-weight: 900;
        color: #132e4e;
    }

    .summary-value.warning {
        color: #d48200;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: .8rem;
        font-weight: 800;
    }

    .badge-positive {
        background: #e7faef;
        color: #118844;
    }

    .badge-negative {
        background: #ffe8e8;
        color: #b42323;
    }

    .badge-neutral {
        background: #eef3f8;
        color: #37506e;
    }

    .trend-up,
    .trend-down,
    .trend-neutral {
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .trend-down {
        color: var(--stats-danger);
    }

    .trend-neutral {
        color: #6d819a;
    }

    .stats-actions-row {
        margin-top: 14px;
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

    .stats-btn {
        min-height: 42px;
        border-radius: 10px;
        border: 1px solid #c8d9ed;
        padding: 8px 15px;
        font-weight: 800;
        font-size: .92rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .stats-btn.secondary {
        background: #f8fbff;
        color: #2f4f73;
    }

    .stats-btn.secondary:hover {
        background: #edf6ff;
        color: #244566;
    }

    .stats-btn.primary {
        border-color: var(--stats-primary);
        background: linear-gradient(120deg, var(--stats-primary-dark) 0%, var(--stats-primary) 100%);
        color: #fff;
        box-shadow: 0 8px 16px -12px rgba(8, 87, 150, .85);
    }

    .stats-btn.primary:hover {
        color: #fff;
        filter: brightness(1.03);
    }

    @media (max-width: 1600px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 1120px) {
        .panel-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .stats-page {
            padding: 10px 10px 20px;
        }

        .stats-head {
            flex-direction: column;
            align-items: stretch;
            padding-bottom: 14px;
        }

        .stats-head-main {
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .stats-back-btn {
            width: 100%;
            justify-content: center;
        }

        .stats-head-actions,
        .stats-head-btn {
            width: 100%;
        }

        .period-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .period-btn {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-number {
            font-size: 1.8rem;
        }

        .finance-summary {
            grid-template-columns: 1fr;
        }

        .stats-actions-row {
            justify-content: stretch;
        }

        .stats-actions-row .stats-btn {
            width: 100%;
            justify-content: center;
        }
    }

    body.dark-mode .stats-page {
        --stats-bg: #0f1d30;
        --stats-card: #11243b;
        --stats-border: #2d4f78;
        --stats-title: #d7e9ff;
        --stats-text: #a9c3de;
        --stats-muted: #8eaccb;
    }

    body.dark-mode .stats-head { border-bottom-color: #365a7b; }

    body.dark-mode .stats-back-btn {
        border-color: #365b7d;
        color: #d2e6fb;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04), 0 16px 28px -26px rgba(3, 12, 24, 0.85);
    }

    body.dark-mode .stats-back-btn:hover {
        border-color: #4c7094;
        color: #fff;
        background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
    }

    body.dark-mode .stats-back-btn-icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    body.dark-mode .stats-title-row i { color: #77b7ff; }

    body.dark-mode .stats-title-row h1 { color: #e4f1ff; }

    body.dark-mode .stats-head-title p { color: #a9c2dc; }

    body.dark-mode .stats-count-badge {
        background: linear-gradient(90deg, #1f5fb3 60%, #123771 100%);
    }

    body.dark-mode .stats-head-btn.secondary {
        color: #d2e6fb;
        border-color: #3c5f81;
        background: #1a3855;
    }

    body.dark-mode .stats-head-btn.secondary:hover {
        color: #fff;
        background: #234a6d;
    }

    body.dark-mode .period-btn {
        background: #142a45;
        border-color: #3a608a;
        color: #c3dcf8;
    }

    body.dark-mode .period-btn:hover,
    body.dark-mode .period-btn.active {
        border-color: #60a5eb;
        background: linear-gradient(120deg, #1a6eb3 0%, #238dd8 100%);
    }

    body.dark-mode .section-header {
        background: linear-gradient(180deg, #173151 0%, #183459 100%);
        border-bottom-color: #2d4f78;
    }

    body.dark-mode .table-modern thead {
        background: #162f4e;
        border-bottom-color: #365c87;
    }

    body.dark-mode .table-modern th {
        color: #b2cee9;
    }

    body.dark-mode .table-modern td {
        color: #c6ddf5;
        border-bottom-color: #27476d;
    }

    body.dark-mode .table-modern tbody tr:hover {
        background: #173355;
    }

    body.dark-mode .progress-track {
        background: #28486f;
    }

    body.dark-mode .stats-btn.secondary {
        background: #173456;
        border-color: #416893;
        color: #d0e5fb;
    }

    body.dark-mode .stats-btn.secondary:hover {
        background: #1d3f66;
        color: #eef7ff;
    }
</style>

<div class="stats-page">
    <div class="stats-shell">
        <div class="stats-head">
            <div class="stats-head-main">
                <a href="{{ route('dashboard') }}" class="stats-back-btn">
                    <span class="stats-back-btn-icon"><i class="fas fa-arrow-left"></i></span>
                    <span class="d-none d-sm-inline">Retour</span>
                </a>
                <div class="stats-head-title">
                    <div class="stats-title-row">
                        <i class="fas fa-chart-line"></i>
                        <h1>Tableau de bord statistiques</h1>
                        <span class="stats-count-badge">{{ $totalPatients ?? 0 }} Patients</span>
                    </div>
                    <p>Analyse complete de l activite medicale et financiere du cabinet.</p>
                </div>
            </div>
            <div class="stats-head-actions">
                <a href="{{ route('statistiques.rapport', request()->all()) }}" class="stats-head-btn secondary">
                    <i class="fas fa-file-lines"></i> Voir rapport
                </a>
                <a href="{{ route('statistiques', ['periode' => request('periode', 30)]) }}" class="stats-head-btn success">
                    <i class="fas fa-sync-alt"></i> Rafraichir
                </a>
            </div>
        </div>

        <div class="period-card">
            <form method="GET" action="{{ route('statistiques') }}" class="period-selector">
                <button type="submit" name="periode" value="7" class="period-btn {{ request('periode', 30) == 7 ? 'active' : '' }}">
                    <i class="fas fa-calendar-week"></i> 7 jours
                </button>
                <button type="submit" name="periode" value="30" class="period-btn {{ request('periode', 30) == 30 ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> 30 jours
                </button>
                <button type="submit" name="periode" value="90" class="period-btn {{ request('periode', 30) == 90 ? 'active' : '' }}">
                    <i class="fas fa-calendar"></i> 90 jours
                </button>
                <button type="submit" name="periode" value="365" class="period-btn {{ request('periode', 30) == 365 ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i> 1 an
                </button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-meta">
                    <p class="stat-label">Total patients</p>
                    <span class="stat-icon"><i class="fas fa-users"></i></span>
                </div>
                <div class="stat-number">{{ $totalPatients ?? 0 }}</div>
                <p class="stat-hint">{{ $nouveauxPatients ?? 0 }} nouveaux cette periode</p>
            </div>

            <div class="stat-card accent">
                <div class="stat-meta">
                    <p class="stat-label">Consultations</p>
                    <span class="stat-icon"><i class="fas fa-notes-medical"></i></span>
                </div>
                <div class="stat-number">{{ $consultationsPeriode ?? 0 }}</div>
                <p class="stat-hint">Total: {{ $totalConsultations ?? 0 }}</p>
            </div>

            <div class="stat-card secondary">
                <div class="stat-meta">
                    <p class="stat-label">Rendez-vous</p>
                    <span class="stat-icon"><i class="fas fa-calendar-check"></i></span>
                </div>
                <div class="stat-number">{{ $rendezVousPeriode ?? 0 }}</div>
                <p class="stat-hint">Total: {{ $totalRendezVous ?? 0 }}</p>
            </div>

            <div class="stat-card warning">
                <div class="stat-meta">
                    <p class="stat-label">Revenus</p>
                    <span class="stat-icon"><i class="fas fa-wallet"></i></span>
                </div>
                <div class="stat-number">{{ number_format($facturesPeriode ?? 0, 2) }} DH</div>
                <p class="stat-hint"><span class="trend-up">{{ number_format($paymentRate, 0) }}% paye</span></p>
            </div>
        </div>

        <div class="panel-grid">
            <div class="card-modern">
                <div class="section-header">
                    <h2><i class="fas fa-user-md"></i> Consultations par medecin</h2>
                </div>
                <div class="section-body">
                    @if($statsMedecins && count($statsMedecins) > 0)
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Medecin</th>
                                    <th>Consultations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statsMedecins->take(6) as $medecin)
                                    <tr>
                                        <td>{{ $medecin->name ?? 'N/A' }}</td>
                                        <td style="color: var(--stats-primary); font-weight: 800;">{{ $medecin->consultations_count ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="empty-row">Aucune donnee disponible</p>
                    @endif
                </div>
            </div>

            <div class="card-modern">
                <div class="section-header">
                    <h2><i class="fas fa-credit-card"></i> Performance financiere</h2>
                </div>
                <div class="section-body">
                    <div class="finance-item">
                        <div class="finance-row">
                            <span>Total factures</span>
                            <span class="finance-value">{{ number_format($totalFacturesValue, 2) }} DH</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: 100%;"></div>
                        </div>
                    </div>

                    <div class="finance-item">
                        <div class="finance-row">
                            <span>Payees</span>
                            <span class="finance-value success">{{ number_format($facturesPayeesValue, 2) }} DH</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ min($paymentRate, 100) }}%;"></div>
                        </div>
                    </div>

                    <div class="finance-summary">
                        <div class="summary-item">
                            <p class="summary-label">Taux de paiement</p>
                            <p class="summary-value">{{ number_format($paymentRate, 1) }}%</p>
                        </div>
                        <div class="summary-item">
                            <p class="summary-label">Montant en attente</p>
                            <p class="summary-value warning">{{ number_format($pendingAmount, 2) }} DH</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-modern">
            <div class="section-header">
                <h2><i class="fas fa-chart-area"></i> Synthese mensuelle</h2>
            </div>
            <div class="section-body" style="overflow-x: auto;">
                @if($rapportSynthese && count($rapportSynthese) > 0)
                    <table class="table-modern">
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
                            @foreach($rapportSynthese as $item)
                                <tr>
                                    <td>{{ $item['metric'] ?? 'N/A' }}</td>
                                    <td>{{ $item['courant'] ?? '-' }}</td>
                                    <td>{{ $item['precedent'] ?? '-' }}</td>
                                    <td>
                                        @if(isset($item['variation']))
                                            <span class="badge {{ $item['variation'] >= 0 ? 'badge-positive' : 'badge-negative' }}">
                                                {{ $item['variation'] >= 0 ? '+' : '' }}{{ $item['variation'] }}%
                                            </span>
                                        @else
                                            <span class="badge badge-neutral">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($item['tendance']))
                                            @if($item['tendance'] === 'up')
                                                <span class="trend-up"><i class="fas fa-arrow-up"></i> Hausse</span>
                                            @elseif($item['tendance'] === 'down')
                                                <span class="trend-down"><i class="fas fa-arrow-down"></i> Baisse</span>
                                            @else
                                                <span class="trend-neutral"><i class="fas fa-arrows-alt-h"></i> Stable</span>
                                            @endif
                                        @else
                                            <span class="trend-neutral">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="empty-row">Aucune donnee pour cette periode.</p>
                @endif
            </div>
        </div>

        <div class="stats-actions-row">
            <a href="{{ route('statistiques.rapport', ['periode' => request('periode', 30), 'print' => 1]) }}" class="stats-btn secondary">
                <i class="fas fa-print"></i> Imprimer le rapport detaille
            </a>
            <a href="{{ route('statistiques.rapport', ['periode' => request('periode', 30)]) }}" class="stats-btn primary">
                <i class="fas fa-file-alt"></i> Rapport detaille
            </a>
        </div>
    </div>
</div>
@endsection
