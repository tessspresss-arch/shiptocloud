@extends('layouts.app')

@section('title', $medicament->nom_commercial)

@push('styles')
<style>
    .med-show-page {
        --med-show-primary: #2c7be5;
        --med-show-primary-strong: #1f5ea8;
        --med-show-accent: #0ea5e9;
        --med-show-success: #0f9f77;
        --med-show-warning: #d97706;
        --med-show-danger: #dc2626;
        --med-show-surface: linear-gradient(180deg, #f4f8fd 0%, #eef5fb 100%);
        --med-show-card: #ffffff;
        --med-show-border: #d8e4f2;
        --med-show-text: #15314d;
        --med-show-muted: #5f7896;
        width: 100%;
        max-width: none;
        padding: 10px 8px 92px;
    }

    .med-show-shell {
        display: grid;
        gap: 16px;
    }

    .med-show-breadcrumbs {
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0 0 12px;
        padding: 0;
        list-style: none;
        font-size: .8rem;
        color: var(--med-show-muted);
        font-weight: 700;
    }

    .med-show-breadcrumbs a {
        color: inherit;
        text-decoration: none;
    }

    .med-show-breadcrumbs a:hover {
        color: var(--med-show-primary);
    }

    .med-show-breadcrumb-separator {
        color: #98abc0;
    }

    .med-show-hero {
        position: relative;
        overflow: hidden;
        display: grid;
        gap: 16px;
        padding: 18px;
        border-radius: 22px;
        border: 1px solid var(--med-show-border);
        background:
            radial-gradient(circle at top right, rgba(44, 123, 229, 0.16) 0%, rgba(44, 123, 229, 0) 32%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
            var(--med-show-surface);
        box-shadow: 0 24px 48px -38px rgba(20, 52, 84, 0.42);
    }

    .med-show-hero::before,
    .med-show-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .med-show-hero > *,
    .med-show-card > * {
        position: relative;
        z-index: 1;
    }

    .med-show-hero-head {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.95fr);
        gap: 16px;
        align-items: start;
    }

    .med-show-title-row {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .med-show-title-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
        font-size: 1.25rem;
        background: linear-gradient(135deg, var(--med-show-primary) 0%, var(--med-show-primary-strong) 100%);
        box-shadow: 0 16px 26px -18px rgba(44, 123, 229, 0.58);
    }

    .med-show-title {
        margin: 0;
        font-size: clamp(1.45rem, 2.5vw, 2.1rem);
        font-weight: 800;
        line-height: 1.06;
        letter-spacing: -0.04em;
        color: var(--med-show-text);
    }

    .med-show-subtitle {
        margin: 8px 0 0;
        max-width: 74ch;
        color: var(--med-show-muted);
        font-size: .97rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .med-show-chips,
    .med-show-actions,
    .med-show-kpis,
    .med-show-meta-grid,
    .med-show-tabs,
    .med-show-pane-grid,
    .med-show-note-grid,
    .med-show-history-stats,
    .med-show-mobile-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .med-show-chip,
    .med-show-badge {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        border-radius: 999px;
        border: 1px solid #d4e2f2;
        background: #f6fafe;
        color: #1d4f91;
        padding: 0 12px;
        font-size: .77rem;
        font-weight: 800;
    }

    .med-show-actions {
        justify-content: flex-end;
    }

    .med-show-btn {
        min-height: 44px;
        border-radius: 14px;
        border: 1px solid transparent;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: .92rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
        white-space: nowrap;
    }

    .med-show-btn:hover,
    .med-show-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .med-show-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(44, 123, 229, 0.1);
        color: var(--med-show-primary);
    }

    .med-show-btn-primary {
        background: linear-gradient(135deg, var(--med-show-primary) 0%, var(--med-show-primary-strong) 100%);
        color: #fff;
        box-shadow: 0 18px 28px -22px rgba(44, 123, 229, 0.55);
    }

    .med-show-btn-primary .med-show-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: inherit;
    }

    .med-show-btn-soft {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #385674;
        box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
    }

    .med-show-kpis {
        margin-top: 14px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .med-show-kpi {
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid rgba(206, 221, 238, 0.96);
        background: rgba(255, 255, 255, 0.72);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.62);
    }

    .med-show-kpi-label {
        display: block;
        margin-bottom: 6px;
        color: var(--med-show-muted);
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 800;
    }

    .med-show-kpi-value {
        display: block;
        color: var(--med-show-text);
        font-size: 1.2rem;
        font-weight: 900;
        line-height: 1;
    }

    .med-show-kpi-meta {
        display: block;
        margin-top: 5px;
        color: #7290b0;
        font-size: .82rem;
        font-weight: 600;
    }

    .med-show-layout {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .med-show-card {
        position: relative;
        overflow: hidden;
        background: var(--med-show-card);
        border: 1px solid var(--med-show-border);
        border-radius: 22px;
        box-shadow: 0 22px 34px -34px rgba(15, 23, 42, 0.44);
    }

    .med-show-side {
        position: sticky;
        top: 92px;
        padding: 18px;
    }

    .med-show-side::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 128px;
        background:
            radial-gradient(circle at top right, rgba(44, 123, 229, 0.18) 0%, rgba(44, 123, 229, 0) 44%),
            linear-gradient(180deg, rgba(244, 249, 255, 0.92) 0%, rgba(244, 249, 255, 0) 100%);
    }

    .med-show-side-head {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .med-show-avatar {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
        font-size: 1.05rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--med-show-primary) 0%, var(--med-show-accent) 100%);
        box-shadow: 0 18px 28px -20px rgba(44, 123, 229, 0.56);
    }

    .med-show-side-name {
        margin: 0;
        font-size: 1.22rem;
        line-height: 1.08;
        font-weight: 800;
        color: var(--med-show-text);
    }

    .med-show-side-subtitle {
        margin: 5px 0 0;
        color: var(--med-show-muted);
        font-size: .88rem;
        font-weight: 700;
    }

    .med-show-side-title,
    .med-show-main-title,
    .med-show-pane-card h3,
    .med-show-stock-card h3 {
        margin: 0;
        color: var(--med-show-text);
        font-weight: 800;
    }

    .med-show-side-title {
        font-size: .92rem;
        margin-bottom: 12px;
    }

    .med-show-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }

    .med-show-meta-item {
        border: 1px solid #e2ebf6;
        border-radius: 16px;
        background: #fbfdff;
        padding: 12px;
    }

    .med-show-meta-item small {
        color: var(--med-show-muted);
        display: block;
        font-size: .68rem;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 800;
    }

    .med-show-meta-item strong,
    .med-show-price-table strong {
        color: var(--med-show-text);
        font-weight: 800;
    }

    .med-show-stock-card {
        border: 1px solid #dfe9f5;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        padding: 16px;
    }

    .med-show-stock-top {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: center;
        margin-bottom: 12px;
    }

    .med-show-stock-value {
        display: block;
        margin-top: 8px;
        color: var(--med-show-text);
        font-size: 2rem;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .med-show-stock-label,
    .med-show-muted,
    .med-show-pane-card p,
    .med-show-empty,
    .med-show-history-table td,
    .med-show-history-table th,
    .med-show-price-table td,
    .med-show-price-table th {
        color: var(--med-show-muted);
    }

    .med-show-progress {
        height: 10px;
        border-radius: 999px;
        background: #e8f0fb;
        overflow: hidden;
        margin: 14px 0;
    }

    .med-show-progress-bar {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--med-show-primary) 0%, var(--med-show-accent) 100%);
    }

    .med-show-main-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 18px 18px 0;
    }

    .med-show-main-title {
        font-size: 1.16rem;
    }

    .med-show-main-copy {
        margin: 6px 0 0;
        color: var(--med-show-muted);
        font-size: .9rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .med-show-body {
        padding: 18px;
        display: grid;
        gap: 16px;
    }

    .med-show-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .med-show-tab {
        min-height: 44px;
        border-radius: 14px;
        border: 1px solid #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #385674;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .9rem;
        font-weight: 800;
    }

    .med-show-tab.active {
        border-color: transparent;
        background: linear-gradient(135deg, var(--med-show-primary) 0%, var(--med-show-primary-strong) 100%);
        color: #fff;
        box-shadow: 0 18px 28px -22px rgba(44, 123, 229, 0.55);
    }

    .med-show-pane-grid,
    .med-show-note-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .med-show-pane-card {
        border: 1px solid #dfe9f5;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        padding: 16px;
        box-shadow: 0 16px 28px -30px rgba(15, 23, 42, 0.32);
    }

    .med-show-pane-card.full {
        grid-column: 1 / -1;
    }

    .med-show-pane-card h3 {
        font-size: 1rem;
        margin-bottom: 12px;
    }

    .med-show-price-table,
    .med-show-history-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .med-show-price-table th,
    .med-show-history-table th {
        font-size: .77rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: 0 0 10px;
        width: 48%;
    }

    .med-show-price-table td {
        padding: 0 0 10px;
        font-size: .94rem;
        font-weight: 600;
    }

    .med-show-history-wrap {
        border: 1px solid #dfe9f5;
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .med-show-history-table th,
    .med-show-history-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #edf2f7;
        font-size: .9rem;
    }

    .med-show-history-table thead th {
        background: linear-gradient(180deg, #f7fbff 0%, #edf5fd 100%);
        color: var(--med-show-primary-strong);
    }

    .med-show-pill {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        border-radius: 999px;
        padding: 0 12px;
        font-size: .76rem;
        font-weight: 800;
    }

    .pill-blue { background: #dbeafe; color: #1d4ed8; }
    .pill-green { background: #dcfce7; color: #166534; }
    .pill-red { background: #fee2e2; color: #991b1b; }
    .pill-amber { background: #fef3c7; color: #92400e; }
    .pill-slate { background: #e2e8f0; color: #334155; }

    .med-show-empty {
        display: grid;
        place-items: center;
        text-align: center;
        min-height: 180px;
        border: 1px dashed #d4e2f2;
        border-radius: 18px;
        background: #fbfdff;
        padding: 24px;
        font-weight: 600;
    }

    .med-show-mobile-actions {
        display: none;
    }

    body.dark-mode .med-show-page,
    body.theme-dark .med-show-page {
        --med-show-surface: linear-gradient(180deg, #152233 0%, #122032 100%);
        --med-show-card: #162332;
        --med-show-border: #2f4358;
        --med-show-text: #e6edf6;
        --med-show-muted: #9eb1c7;
    }

    body.dark-mode .med-show-card,
    body.dark-mode .med-show-kpi,
    body.dark-mode .med-show-chip,
    body.dark-mode .med-show-badge,
    body.dark-mode .med-show-meta-item,
    body.dark-mode .med-show-stock-card,
    body.dark-mode .med-show-pane-card,
    body.dark-mode .med-show-history-wrap,
    body.dark-mode .med-show-empty,
    body.theme-dark .med-show-card,
    body.theme-dark .med-show-kpi,
    body.theme-dark .med-show-chip,
    body.theme-dark .med-show-badge,
    body.theme-dark .med-show-meta-item,
    body.theme-dark .med-show-stock-card,
    body.theme-dark .med-show-pane-card,
    body.theme-dark .med-show-history-wrap,
    body.theme-dark .med-show-empty {
        background: rgba(17, 34, 54, 0.88);
        border-color: #35506a;
    }

    body.dark-mode .med-show-btn-soft,
    body.theme-dark .med-show-btn-soft,
    body.dark-mode .med-show-tab,
    body.theme-dark .med-show-tab {
        border-color: #365b7d;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
        color: #d2e6fb;
    }

    body.dark-mode .med-show-history-table thead th,
    body.theme-dark .med-show-history-table thead th {
        background: #16273d;
        color: #dceafe;
    }

    body.dark-mode .pill-blue { background: rgba(37, 99, 235, 0.2); color: #9fc2ff; }
    body.dark-mode .pill-green { background: rgba(22, 163, 74, 0.2); color: #86efac; }
    body.dark-mode .pill-red { background: rgba(239, 68, 68, 0.24); color: #fda4af; }
    body.dark-mode .pill-amber { background: rgba(245, 158, 11, 0.22); color: #fcd34d; }
    body.dark-mode .pill-slate { background: rgba(148, 163, 184, 0.25); color: #d5dfec; }

    body.theme-dark .pill-blue { background: rgba(37, 99, 235, 0.2); color: #9fc2ff; }
    body.theme-dark .pill-green { background: rgba(22, 163, 74, 0.2); color: #86efac; }
    body.theme-dark .pill-red { background: rgba(239, 68, 68, 0.24); color: #fda4af; }
    body.theme-dark .pill-amber { background: rgba(245, 158, 11, 0.22); color: #fcd34d; }
    body.theme-dark .pill-slate { background: rgba(148, 163, 184, 0.25); color: #d5dfec; }

    @media (max-width: 1199.98px) {
        .med-show-hero-head,
        .med-show-layout {
            grid-template-columns: 1fr;
        }

        .med-show-side {
            position: static;
        }
    }

    @media (max-width: 991.98px) {
        .med-show-kpis,
        .med-show-pane-grid,
        .med-show-note-grid,
        .med-show-meta-grid {
            grid-template-columns: 1fr;
        }

        .med-show-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .med-show-page {
            padding: 6px 0 88px;
        }

        .med-show-hero,
        .med-show-card {
            border-radius: 18px;
        }

        .med-show-actions {
            display: none;
        }

        .med-show-mobile-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            position: fixed;
            left: 8px;
            right: 8px;
            bottom: calc(10px + env(safe-area-inset-bottom));
            z-index: 1050;
            background: var(--med-show-card);
            border: 1px solid var(--med-show-border);
            border-radius: 18px;
            padding: 8px;
            box-shadow: 0 16px 24px -20px rgba(0, 0, 0, .46);
        }

        .med-show-mobile-actions .med-show-btn {
            width: 100%;
        }

        .med-show-history-table {
            min-width: 820px;
        }
    }

    @media (max-width: 575.98px) {
        .med-show-hero {
            padding: 14px;
        }

        .med-show-title-row {
            align-items: flex-start;
        }

        .med-show-title-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .med-show-mobile-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $typeLabel = match ($medicament->type) {
        'otc' => 'OTC',
        'controlled' => 'Contrôlé',
        default => 'Prescription',
    };
    $typeClass = match ($medicament->type) {
        'otc' => 'pill-green',
        'controlled' => 'pill-red',
        default => 'pill-blue',
    };
    $statusClass = match ($medicament->statut) {
        'actif' => 'pill-green',
        'rupture', 'expired' => 'pill-red',
        default => 'pill-slate',
    };
    $statusLabel = match ($medicament->statut) {
        'actif' => 'Actif',
        'rupture' => 'Rupture',
        'expired' => 'Expiré',
        default => 'Inactif',
    };
    $expiryClass = match ($medicament->expiration_status) {
        'expire' => 'pill-red',
        'bientot_expire' => 'pill-amber',
        default => 'pill-green',
    };
    $stockClass = match ($medicament->stock_status) {
        'rupture' => 'pill-red',
        'faible' => 'pill-amber',
        default => 'pill-green',
    };
    $stockPercent = $medicament->quantite_ideale > 0 ? min(100, ($medicament->quantite_stock / $medicament->quantite_ideale) * 100) : 0;
    $movements = $medicament->mouvementStocks()->latest()->take(20)->get();
    $hasMedicalContent = collect([
        $medicament->posologie,
        $medicament->contre_indications,
        $medicament->effets_secondaires,
        $medicament->interactions,
        $medicament->precautions,
        $medicament->conservation,
        $medicament->composants,
    ])->filter(fn ($value) => filled($value))->isNotEmpty();
@endphp

<div class="container-fluid med-show-page">
    <div class="med-show-shell">
        <header class="med-show-hero">
            <div class="med-show-hero-head">
                <div>
                    <ol class="med-show-breadcrumbs" aria-label="Fil d'Ariane détail médicament">
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="med-show-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li><a href="{{ route('medicaments.index') }}">Médicaments</a></li>
                        <li class="med-show-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li aria-current="page">{{ $medicament->nom_commercial }}</li>
                    </ol>

                    <div class="med-show-title-row">
                        <span class="med-show-title-icon" aria-hidden="true"><i class="fas fa-pills"></i></span>
                        <div>
                            <h1 class="med-show-title">{{ $medicament->nom_commercial }}</h1>
                            <p class="med-show-subtitle">Fiche complète du médicament avec synthèse catalogue, état du stock, tarification, données cliniques et historique des mouvements.</p>
                        </div>
                    </div>

                    <div class="med-show-chips" style="margin-top:14px;">
                        <span class="med-show-pill {{ $typeClass }}">{{ $typeLabel }}</span>
                        <span class="med-show-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                        @if($medicament->date_peremption)
                            <span class="med-show-pill {{ $expiryClass }}">Péremption {{ $medicament->date_peremption->format('d/m/Y') }}</span>
                        @endif
                    </div>

                    <div class="med-show-kpis" aria-label="Indicateurs du médicament">
                        <article class="med-show-kpi">
                            <span class="med-show-kpi-label">Stock</span>
                            <span class="med-show-kpi-value">{{ $medicament->quantite_stock }}</span>
                            <span class="med-show-kpi-meta">Unités actuellement disponibles</span>
                        </article>
                        <article class="med-show-kpi">
                            <span class="med-show-kpi-label">Prix de vente</span>
                            <span class="med-show-kpi-value">{{ number_format((float) $medicament->prix_vente, 2) }} DH</span>
                            <span class="med-show-kpi-meta">Tarif catalogue actuel</span>
                        </article>
                        <article class="med-show-kpi">
                            <span class="med-show-kpi-label">Valeur stock</span>
                            <span class="med-show-kpi-value">{{ number_format((float) $medicament->valeur_stock, 2) }} DH</span>
                            <span class="med-show-kpi-meta">Valorisation du stock disponible</span>
                        </article>
                    </div>
                </div>

                <div class="med-show-actions">
                    <a href="{{ route('medicaments.index') }}" class="med-show-btn med-show-btn-soft">
                        <span class="med-show-btn-icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Retour</span>
                    </a>
                    <a href="{{ route('medicaments.edit', $medicament) }}" class="med-show-btn med-show-btn-primary">
                        <span class="med-show-btn-icon"><i class="fas fa-pen"></i></span>
                        <span>Modifier</span>
                    </a>
                </div>
            </div>
        </header>

        <div class="med-show-layout">
            <aside class="med-show-card med-show-side">
                <div class="med-show-side-head">
                    <span class="med-show-avatar" aria-hidden="true">{{ strtoupper(substr($medicament->nom_commercial, 0, 2)) }}</span>
                    <div>
                        <h2 class="med-show-side-name">{{ $medicament->nom_commercial }}</h2>
                        <p class="med-show-side-subtitle">{{ $medicament->dci ?: 'DCI non renseignée' }}</p>
                    </div>
                </div>

                <h2 class="med-show-side-title">Synthèse catalogue</h2>
                <div class="med-show-meta-grid">
                    <div class="med-show-meta-item">
                        <small>Code CIP</small>
                        <strong>{{ $medicament->code_cip ?: 'Non renseigné' }}</strong>
                    </div>
                    <div class="med-show-meta-item">
                        <small>Code interne</small>
                        <strong>{{ $medicament->code_medicament ?: 'Non renseigné' }}</strong>
                    </div>
                    <div class="med-show-meta-item">
                        <small>Catégorie</small>
                        <strong>{{ $medicament->categorie ?: 'Non renseignée' }}</strong>
                    </div>
                    <div class="med-show-meta-item">
                        <small>Laboratoire</small>
                        <strong>{{ $medicament->laboratoire ?: 'Non renseigné' }}</strong>
                    </div>
                    <div class="med-show-meta-item">
                        <small>Présentation</small>
                        <strong>{{ $medicament->presentation ?: 'Non renseignée' }}</strong>
                    </div>
                    <div class="med-show-meta-item">
                        <small>Voie</small>
                        <strong>{{ $medicament->voie_administration ? ucfirst($medicament->voie_administration) : 'Non renseignée' }}</strong>
                    </div>
                    <div class="med-show-meta-item">
                        <small>Lot</small>
                        <strong>{{ $medicament->numero_lot ?: 'Non renseigné' }}</strong>
                    </div>
                    <div class="med-show-meta-item">
                        <small>Fournisseur</small>
                        <strong>{{ $medicament->fournisseur ?: 'Non renseigné' }}</strong>
                    </div>
                </div>

                <div class="med-show-stock-card">
                    <div class="med-show-stock-top">
                        <div>
                            <h3>État du stock</h3>
                            <span class="med-show-stock-label">Suivi des quantités et du seuil d’alerte</span>
                        </div>
                        <span class="med-show-pill {{ $stockClass }}">{{ $medicament->stock_status_label ?? ucfirst($medicament->stock_status ?? 'normal') }}</span>
                    </div>

                    <span class="med-show-stock-value">{{ $medicament->quantite_stock }}</span>
                    <span class="med-show-stock-label">unités disponibles</span>

                    <div class="med-show-progress" aria-hidden="true">
                        <div class="med-show-progress-bar" style="width: {{ $stockPercent }}%"></div>
                    </div>

                    <div class="med-show-meta-grid" style="margin-bottom:0;">
                        <div class="med-show-meta-item">
                            <small>Seuil d'alerte</small>
                            <strong>{{ $medicament->quantite_seuil }}</strong>
                        </div>
                        <div class="med-show-meta-item">
                            <small>Stock idéal</small>
                            <strong>{{ $medicament->quantite_ideale }}</strong>
                        </div>
                    </div>
                </div>
            </aside>

            <section class="med-show-card">
                <div class="med-show-main-head">
                    <div>
                        <h2 class="med-show-main-title">Fiche détaillée</h2>
                        <p class="med-show-main-copy">Retrouvez ici la tarification, les paramètres de remboursement, les informations médicales et l’historique du stock dans une présentation plus claire.</p>
                    </div>
                    <span class="med-show-badge">{{ $statusLabel }}</span>
                </div>

                <div class="med-show-body">
                    <div class="med-show-tabs nav" id="medicament-tabs" role="tablist">
                        <button class="med-show-tab active" id="med-show-prix-tab" data-bs-toggle="pill" data-bs-target="#med-show-prix" type="button" role="tab" aria-controls="med-show-prix" aria-selected="true">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Prix et remboursement</span>
                        </button>
                        <button class="med-show-tab" id="med-show-medical-tab" data-bs-toggle="pill" data-bs-target="#med-show-medical" type="button" role="tab" aria-controls="med-show-medical" aria-selected="false">
                            <i class="fas fa-stethoscope"></i>
                            <span>Informations médicales</span>
                        </button>
                        <button class="med-show-tab" id="med-show-history-tab" data-bs-toggle="pill" data-bs-target="#med-show-history" type="button" role="tab" aria-controls="med-show-history" aria-selected="false">
                            <i class="fas fa-clock-rotate-left"></i>
                            <span>Historique des mouvements</span>
                        </button>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="med-show-prix" role="tabpanel" aria-labelledby="med-show-prix-tab">
                            <div class="med-show-pane-grid">
                                <article class="med-show-pane-card">
                                    <h3>Tarification</h3>
                                    <table class="med-show-price-table">
                                        <tr>
                                            <th>Prix d'achat</th>
                                            <td><strong>{{ number_format((float) $medicament->prix_achat, 2) }} DH</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Prix de vente</th>
                                            <td><strong>{{ number_format((float) $medicament->prix_vente, 2) }} DH</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Valeur du stock</th>
                                            <td><strong>{{ number_format((float) $medicament->valeur_stock, 2) }} DH</strong></td>
                                        </tr>
                                    </table>
                                </article>

                                <article class="med-show-pane-card">
                                    <h3>Prise en charge</h3>
                                    <table class="med-show-price-table">
                                        <tr>
                                            <th>Remboursable</th>
                                            <td><span class="med-show-pill {{ $medicament->remboursable ? 'pill-green' : 'pill-slate' }}">{{ $medicament->remboursable ? 'Oui' : 'Non' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Prix remboursé</th>
                                            <td><strong>{{ $medicament->prix_remboursement ? number_format((float) $medicament->prix_remboursement, 2) . ' DH' : 'Non renseigné' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Taux de remboursement</th>
                                            <td><strong>{{ $medicament->taux_remboursement ? number_format((float) $medicament->taux_remboursement, 2) . '%' : 'Non renseigné' }}</strong></td>
                                        </tr>
                                        @if($medicament->remboursable && isset($medicament->prix_remboursement_calcule))
                                            <tr>
                                                <th>Montant calculé</th>
                                                <td><strong>{{ number_format((float) $medicament->prix_remboursement_calcule, 2) }} DH</strong></td>
                                            </tr>
                                        @endif
                                    </table>
                                </article>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="med-show-medical" role="tabpanel" aria-labelledby="med-show-medical-tab">
                            @if($hasMedicalContent)
                                <div class="med-show-note-grid">
                                    @if($medicament->posologie)
                                        <article class="med-show-pane-card">
                                            <h3>Posologie</h3>
                                            <p>{{ $medicament->posologie }}</p>
                                        </article>
                                    @endif
                                    @if($medicament->contre_indications)
                                        <article class="med-show-pane-card">
                                            <h3>Contre-indications</h3>
                                            <p>{{ $medicament->contre_indications }}</p>
                                        </article>
                                    @endif
                                    @if($medicament->effets_secondaires)
                                        <article class="med-show-pane-card">
                                            <h3>Effets secondaires</h3>
                                            <p>{{ $medicament->effets_secondaires }}</p>
                                        </article>
                                    @endif
                                    @if($medicament->interactions)
                                        <article class="med-show-pane-card">
                                            <h3>Interactions médicamenteuses</h3>
                                            <p>{{ $medicament->interactions }}</p>
                                        </article>
                                    @endif
                                    @if($medicament->precautions)
                                        <article class="med-show-pane-card">
                                            <h3>Précautions d'emploi</h3>
                                            <p>{{ $medicament->precautions }}</p>
                                        </article>
                                    @endif
                                    @if($medicament->conservation)
                                        <article class="med-show-pane-card">
                                            <h3>Conditions de conservation</h3>
                                            <p>{{ $medicament->conservation }}</p>
                                        </article>
                                    @endif
                                    @if($medicament->composants)
                                        <article class="med-show-pane-card full">
                                            <h3>Composition</h3>
                                            <p>{{ $medicament->composants }}</p>
                                        </article>
                                    @endif
                                </div>
                            @else
                                <div class="med-show-empty">
                                    Aucune information clinique complémentaire n’est encore renseignée pour ce médicament.
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="med-show-history" role="tabpanel" aria-labelledby="med-show-history-tab">
                            <div class="med-show-history-stats" style="margin-bottom:12px;">
                                <span class="med-show-badge">{{ $movements->count() }} mouvement{{ $movements->count() > 1 ? 's' : '' }}</span>
                                <span class="med-show-badge">Historique récent</span>
                            </div>
                            <div class="med-show-history-wrap table-responsive">
                                <table class="med-show-history-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Quantité</th>
                                            <th>Stock avant</th>
                                            <th>Stock après</th>
                                            <th>Motif</th>
                                            <th>Utilisateur</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($movements as $mouvement)
                                            <tr>
                                                <td>{{ $mouvement->date_mouvement->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <span class="med-show-pill {{ $mouvement->type_mouvement == 'entree' ? 'pill-green' : 'pill-red' }}">{{ $mouvement->type_mouvement_label }}</span>
                                                </td>
                                                <td><strong class="{{ $mouvement->quantite >= 0 ? 'text-success' : 'text-danger' }}">{{ $mouvement->quantite_formatee }}</strong></td>
                                                <td>{{ $mouvement->quantite_avant }}</td>
                                                <td>{{ $mouvement->quantite_apres }}</td>
                                                <td>{{ $mouvement->motif ?: '-' }}</td>
                                                <td>{{ $mouvement->user->name ?? 'Système' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="med-show-empty">Aucun mouvement enregistré pour ce médicament.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="med-show-mobile-actions">
        <a href="{{ route('medicaments.index') }}" class="med-show-btn med-show-btn-soft">
            <span class="med-show-btn-icon"><i class="fas fa-arrow-left"></i></span>
            <span>Retour</span>
        </a>
        <a href="{{ route('medicaments.edit', $medicament) }}" class="med-show-btn med-show-btn-primary">
            <span class="med-show-btn-icon"><i class="fas fa-pen"></i></span>
            <span>Modifier</span>
        </a>
    </div>
</div>
@endsection