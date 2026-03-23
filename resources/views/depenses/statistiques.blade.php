@extends('layouts.app')

@section('title', 'Statistiques des Depenses')
@section('topbar_subtitle', 'Dashboard analytique des depenses avec filtres, variations et tendances.')

@section('content')
<style>
    :root {
        --dep-stat-bg: linear-gradient(180deg, #f4f8ff 0%, #edf5ff 100%);
        --dep-stat-surface: rgba(255, 255, 255, 0.88);
        --dep-stat-card: #ffffff;
        --dep-stat-border: #d6e2ef;
        --dep-stat-border-strong: #c8d8ea;
        --dep-stat-text: #16324b;
        --dep-stat-muted: #647f99;
        --dep-stat-primary: #0f6cbd;
        --dep-stat-primary-strong: #124e82;
        --dep-stat-cyan: #0ea5e9;
        --dep-stat-success: #0f9f77;
        --dep-stat-warning: #d97706;
        --dep-stat-danger: #dc2626;
        --dep-stat-shadow: 0 24px 52px -38px rgba(12, 37, 63, 0.36);
    }

    .dep-stat-page {
        width: 100%;
        max-width: none;
        padding: 10px 8px 92px;
    }

    .dep-stat-shell {
        display: grid;
        gap: 18px;
    }

    .dep-stat-hero,
    .dep-stat-filters,
    .dep-stat-kpi,
    .dep-stat-panel,
    .dep-stat-empty {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--dep-stat-border);
        border-radius: 26px;
        box-shadow: var(--dep-stat-shadow);
    }

    .dep-stat-hero {
        padding: 22px;
        background:
            radial-gradient(circle at right top, rgba(15, 108, 189, 0.16) 0%, rgba(15, 108, 189, 0) 34%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 35%),
            var(--dep-stat-bg);
    }

    .dep-stat-filters,
    .dep-stat-kpi,
    .dep-stat-panel,
    .dep-stat-empty {
        background: var(--dep-stat-surface);
    }

    .dep-stat-hero::before,
    .dep-stat-filters::before,
    .dep-stat-kpi::before,
    .dep-stat-panel::before,
    .dep-stat-empty::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.56) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .dep-stat-hero > *,
    .dep-stat-filters > *,
    .dep-stat-kpi > *,
    .dep-stat-panel > *,
    .dep-stat-empty > * {
        position: relative;
        z-index: 1;
    }

    .dep-stat-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(290px, 0.8fr);
        gap: 18px;
        align-items: start;
    }

    .dep-stat-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(15, 108, 189, 0.16);
        background: rgba(255, 255, 255, 0.68);
        color: var(--dep-stat-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dep-stat-title-row {
        display: flex;
        gap: 14px;
        align-items: center;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    .dep-stat-title-icon {
        width: 58px;
        height: 58px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.4rem;
        background: linear-gradient(135deg, var(--dep-stat-primary) 0%, var(--dep-stat-primary-strong) 100%);
        box-shadow: 0 18px 28px -20px rgba(15, 108, 189, 0.54);
    }

    .dep-stat-title {
        margin: 0;
        color: var(--dep-stat-text);
        font-size: clamp(1.75rem, 2.8vw, 2.45rem);
        font-weight: 800;
        letter-spacing: -0.04em;
        line-height: 1.04;
    }

    .dep-stat-subtitle {
        margin: 10px 0 0;
        max-width: 70ch;
        color: var(--dep-stat-muted);
        font-size: .98rem;
        line-height: 1.68;
        font-weight: 600;
    }

    .dep-stat-tags,
    .dep-stat-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .dep-stat-tags {
        margin-top: 18px;
    }

    .dep-stat-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #d7e4f2;
        background: rgba(255, 255, 255, 0.78);
        color: #56718c;
        font-size: .82rem;
        font-weight: 800;
    }

    .dep-stat-actions {
        margin-top: 18px;
    }

    .dep-stat-btn {
        min-height: 50px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        text-decoration: none;
        font-size: .92rem;
        font-weight: 800;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease, color .18s ease;
    }

    .dep-stat-btn:hover,
    .dep-stat-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .dep-stat-btn-primary {
        color: #fff;
        background: linear-gradient(135deg, var(--dep-stat-primary) 0%, var(--dep-stat-primary-strong) 100%);
        box-shadow: 0 18px 28px -22px rgba(15, 108, 189, 0.56);
    }

    .dep-stat-btn-secondary {
        color: var(--dep-stat-primary-strong);
        border-color: rgba(15, 108, 189, 0.18);
        background: rgba(255, 255, 255, 0.8);
    }

    .dep-stat-side {
        display: grid;
        gap: 12px;
    }

    .dep-stat-summary,
    .dep-stat-focus {
        padding: 16px;
        border-radius: 22px;
        border: 1px solid rgba(200, 216, 234, 0.84);
        background: rgba(255, 255, 255, 0.76);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.76);
    }

    .dep-stat-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .dep-stat-summary-item {
        padding: 14px;
        border-radius: 18px;
        border: 1px solid #dbe7f3;
        background: rgba(255, 255, 255, 0.84);
    }

    .dep-stat-summary-label,
    .dep-stat-focus-label,
    .dep-stat-panel-head p,
    .dep-stat-kpi-label,
    .dep-stat-chart-meta,
    .dep-stat-table th,
    .dep-stat-filter-label {
        color: var(--dep-stat-muted);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dep-stat-summary-value,
    .dep-stat-focus-value {
        display: block;
        margin-top: 6px;
        color: var(--dep-stat-text);
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.4;
    }

    .dep-stat-focus h2,
    .dep-stat-panel-head h2 {
        margin: 0;
        color: var(--dep-stat-text);
        font-size: 1.02rem;
        font-weight: 800;
    }

    .dep-stat-focus p,
    .dep-stat-panel-head p {
        margin: 8px 0 0;
        line-height: 1.54;
        text-transform: none;
        letter-spacing: 0;
        font-size: .88rem;
    }

    .dep-stat-focus-grid {
        display: grid;
        gap: 12px;
        margin-top: 14px;
    }

    .dep-stat-focus-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 16px;
        background: #f8fbff;
        border: 1px solid #e1ebf6;
    }

    .dep-stat-focus-copy {
        color: var(--dep-stat-muted);
        font-size: .86rem;
        line-height: 1.55;
        font-weight: 700;
    }

    .dep-stat-filters {
        padding: 18px;
    }

    .dep-stat-filters-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
        align-items: end;
    }

    .dep-stat-field {
        display: grid;
        gap: 8px;
    }

    .dep-stat-field input,
    .dep-stat-field select {
        min-height: 48px;
        width: 100%;
        border-radius: 15px;
        border: 1px solid #d7e3f0;
        background: rgba(255, 255, 255, 0.92);
        color: var(--dep-stat-text);
        padding: 0 14px;
        font-size: .94rem;
        font-weight: 700;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .dep-stat-field input:focus,
    .dep-stat-field select:focus {
        border-color: rgba(15, 108, 189, 0.34);
        box-shadow: 0 0 0 4px rgba(15, 108, 189, 0.09);
        background: #fff;
    }

    .dep-stat-field.is-hidden {
        display: none;
    }

    .dep-stat-filter-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .dep-stat-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .dep-stat-kpi {
        padding: 18px;
        display: grid;
        gap: 14px;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .dep-stat-kpi:hover {
        transform: translateY(-2px);
        border-color: var(--dep-stat-border-strong);
        box-shadow: 0 30px 52px -42px rgba(12, 37, 63, 0.42);
    }

    .dep-stat-kpi-top,
    .dep-stat-kpi-bottom,
    .dep-stat-panel-head,
    .dep-stat-row-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .dep-stat-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1rem;
        background: linear-gradient(135deg, rgba(15, 108, 189, 0.94) 0%, rgba(18, 78, 130, 0.94) 100%);
        box-shadow: 0 14px 22px -16px rgba(15, 108, 189, 0.54);
    }

    .dep-stat-kpi-value {
        margin: 0;
        color: var(--dep-stat-text);
        font-size: clamp(1.4rem, 2.3vw, 2.1rem);
        font-weight: 800;
        letter-spacing: -0.04em;
        line-height: 1.04;
    }

    .dep-stat-kpi-copy {
        margin: 6px 0 0;
        color: var(--dep-stat-muted);
        font-size: .88rem;
        line-height: 1.58;
        font-weight: 700;
    }

    .dep-stat-delta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .dep-stat-delta.up {
        color: var(--dep-stat-danger);
        background: rgba(220, 38, 38, 0.1);
        border-color: rgba(220, 38, 38, 0.14);
    }

    .dep-stat-delta.down {
        color: var(--dep-stat-success);
        background: rgba(15, 159, 119, 0.1);
        border-color: rgba(15, 159, 119, 0.14);
    }

    .dep-stat-delta.flat {
        color: #496581;
        background: rgba(73, 101, 129, 0.1);
        border-color: rgba(73, 101, 129, 0.14);
    }

    .dep-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .dep-stat-panel {
        padding: 18px;
        display: grid;
        gap: 18px;
    }

    .dep-stat-panel.span-2 {
        grid-column: 1 / -1;
    }

    .dep-stat-chart {
        display: grid;
        gap: 12px;
    }

    .dep-stat-trend-chart {
        padding: 18px;
        border-radius: 22px;
        border: 1px solid #dce8f4;
        background: linear-gradient(180deg, rgba(250, 252, 255, 0.96) 0%, rgba(244, 248, 252, 0.92) 100%);
    }

    .dep-stat-trend-svg {
        display: block;
        width: 100%;
        height: 210px;
    }

    .dep-stat-trend-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 10px;
    }

    .dep-stat-trend-card {
        padding: 12px 12px 14px;
        border-radius: 16px;
        border: 1px solid #dfeaf5;
        background: rgba(255, 255, 255, 0.86);
    }

    .dep-stat-trend-card strong,
    .dep-stat-row-value,
    .dep-stat-table td,
    .dep-stat-empty h2 {
        color: var(--dep-stat-text);
        font-weight: 800;
    }

    .dep-stat-trend-card span,
    .dep-stat-row-copy,
    .dep-stat-table td small,
    .dep-stat-empty p {
        color: var(--dep-stat-muted);
        font-size: .84rem;
        line-height: 1.55;
        font-weight: 700;
    }

    .dep-stat-breakdown {
        display: grid;
        gap: 12px;
    }

    .dep-stat-row {
        padding: 14px;
        border-radius: 18px;
        border: 1px solid #dfebf6;
        background: rgba(255, 255, 255, 0.84);
        transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease;
    }

    .dep-stat-row:hover {
        transform: translateY(-1px);
        border-color: #cddcef;
        box-shadow: 0 18px 26px -24px rgba(12, 37, 63, 0.26);
    }

    .dep-stat-row-copy {
        margin-top: 4px;
    }

    .dep-stat-bar-track {
        position: relative;
        height: 11px;
        margin-top: 12px;
        border-radius: 999px;
        overflow: hidden;
        background: #e8f0f8;
    }

    .dep-stat-bar-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--dep-stat-primary) 0%, var(--dep-stat-cyan) 100%);
    }

    .dep-stat-bar-fill.status-payee {
        background: linear-gradient(90deg, #0f9f77 0%, #34d399 100%);
    }

    .dep-stat-bar-fill.status-en_attente {
        background: linear-gradient(90deg, #d97706 0%, #f59e0b 100%);
    }

    .dep-stat-bar-fill.status-enregistre {
        background: linear-gradient(90deg, #0f6cbd 0%, #60a5fa 100%);
    }

    .dep-stat-table-wrap {
        overflow-x: auto;
    }

    .dep-stat-table {
        width: 100%;
        border-collapse: collapse;
    }

    .dep-stat-table th,
    .dep-stat-table td {
        padding: 14px 10px;
        border-bottom: 1px solid #e8f0f8;
        text-align: left;
        vertical-align: top;
    }

    .dep-stat-table th {
        font-size: .72rem;
    }

    .dep-stat-table td {
        font-size: .92rem;
        line-height: 1.55;
        font-weight: 700;
    }

    .dep-stat-pill {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .dep-stat-pill.payee {
        color: #0d7d5f;
        background: rgba(15, 159, 119, 0.1);
        border-color: rgba(15, 159, 119, 0.14);
    }

    .dep-stat-pill.en_attente {
        color: #b35b04;
        background: rgba(217, 119, 6, 0.1);
        border-color: rgba(217, 119, 6, 0.16);
    }

    .dep-stat-pill.enregistre {
        color: #0f5f9f;
        background: rgba(15, 108, 189, 0.1);
        border-color: rgba(15, 108, 189, 0.16);
    }

    .dep-stat-empty {
        padding: 32px;
        text-align: center;
    }

    .dep-stat-empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.6rem;
        background: linear-gradient(135deg, var(--dep-stat-primary) 0%, var(--dep-stat-cyan) 100%);
        box-shadow: 0 18px 30px -22px rgba(15, 108, 189, 0.52);
    }

    .dep-stat-empty p {
        max-width: 60ch;
        margin: 10px auto 0;
    }

    .dep-stat-empty-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    body.dark-mode .dep-stat-hero,
    body.dark-mode .dep-stat-filters,
    body.dark-mode .dep-stat-kpi,
    body.dark-mode .dep-stat-panel,
    body.dark-mode .dep-stat-empty,
    html.dark .dep-stat-hero,
    html.dark .dep-stat-filters,
    html.dark .dep-stat-kpi,
    html.dark .dep-stat-panel,
    html.dark .dep-stat-empty {
        background: #122033;
        border-color: #27425f;
        box-shadow: 0 28px 46px -34px rgba(4, 12, 22, 0.82);
    }

    body.dark-mode .dep-stat-hero,
    html.dark .dep-stat-hero {
        background:
            radial-gradient(circle at right top, rgba(96, 165, 250, 0.16) 0%, rgba(96, 165, 250, 0) 34%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 35%),
            linear-gradient(180deg, #16283d 0%, #132235 100%);
    }

    body.dark-mode .dep-stat-summary,
    body.dark-mode .dep-stat-focus,
    body.dark-mode .dep-stat-summary-item,
    body.dark-mode .dep-stat-focus-row,
    body.dark-mode .dep-stat-trend-chart,
    body.dark-mode .dep-stat-trend-card,
    body.dark-mode .dep-stat-row,
    body.dark-mode .dep-stat-field input,
    body.dark-mode .dep-stat-field select,
    html.dark .dep-stat-summary,
    html.dark .dep-stat-focus,
    html.dark .dep-stat-summary-item,
    html.dark .dep-stat-focus-row,
    html.dark .dep-stat-trend-chart,
    html.dark .dep-stat-trend-card,
    html.dark .dep-stat-row,
    html.dark .dep-stat-field input,
    html.dark .dep-stat-field select {
        background: #16283d;
        border-color: #2a4561;
        color: #e2edf8;
    }

    body.dark-mode .dep-stat-tag,
    body.dark-mode .dep-stat-btn-secondary,
    html.dark .dep-stat-tag,
    html.dark .dep-stat-btn-secondary {
        background: rgba(19, 34, 53, 0.9);
        border-color: #2b4560;
        color: #b4c8db;
    }

    body.dark-mode .dep-stat-title,
    body.dark-mode .dep-stat-focus h2,
    body.dark-mode .dep-stat-panel-head h2,
    body.dark-mode .dep-stat-summary-value,
    body.dark-mode .dep-stat-focus-value,
    body.dark-mode .dep-stat-kpi-value,
    body.dark-mode .dep-stat-row-value,
    body.dark-mode .dep-stat-table td,
    body.dark-mode .dep-stat-empty h2,
    html.dark .dep-stat-title,
    html.dark .dep-stat-focus h2,
    html.dark .dep-stat-panel-head h2,
    html.dark .dep-stat-summary-value,
    html.dark .dep-stat-focus-value,
    html.dark .dep-stat-kpi-value,
    html.dark .dep-stat-row-value,
    html.dark .dep-stat-table td,
    html.dark .dep-stat-empty h2 {
        color: #e5eef8;
    }

    body.dark-mode .dep-stat-subtitle,
    body.dark-mode .dep-stat-summary-label,
    body.dark-mode .dep-stat-focus-label,
    body.dark-mode .dep-stat-panel-head p,
    body.dark-mode .dep-stat-kpi-label,
    body.dark-mode .dep-stat-kpi-copy,
    body.dark-mode .dep-stat-chart-meta,
    body.dark-mode .dep-stat-row-copy,
    body.dark-mode .dep-stat-table th,
    body.dark-mode .dep-stat-table td small,
    body.dark-mode .dep-stat-empty p,
    body.dark-mode .dep-stat-filter-label,
    html.dark .dep-stat-subtitle,
    html.dark .dep-stat-summary-label,
    html.dark .dep-stat-focus-label,
    html.dark .dep-stat-panel-head p,
    html.dark .dep-stat-kpi-label,
    html.dark .dep-stat-kpi-copy,
    html.dark .dep-stat-chart-meta,
    html.dark .dep-stat-row-copy,
    html.dark .dep-stat-table th,
    html.dark .dep-stat-table td small,
    html.dark .dep-stat-empty p,
    html.dark .dep-stat-filter-label {
        color: #9eb5cb;
    }

    body.dark-mode .dep-stat-table th,
    body.dark-mode .dep-stat-table td,
    html.dark .dep-stat-table th,
    html.dark .dep-stat-table td {
        border-bottom-color: #24384d;
    }

    body.dark-mode .dep-stat-bar-track,
    html.dark .dep-stat-bar-track {
        background: #203347;
    }

    @media (max-width: 1180px) {
        .dep-stat-hero-grid,
        .dep-stat-grid,
        .dep-stat-kpis,
        .dep-stat-filters-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dep-stat-panel.span-2 {
            grid-column: auto;
        }
    }

    @media (max-width: 860px) {
        .dep-stat-hero-grid,
        .dep-stat-summary-grid,
        .dep-stat-kpis,
        .dep-stat-grid,
        .dep-stat-filters-grid {
            grid-template-columns: 1fr;
        }

        .dep-stat-filter-actions {
            justify-content: stretch;
        }

        .dep-stat-filter-actions .dep-stat-btn {
            flex: 1 1 auto;
        }
    }

    @media (max-width: 640px) {
        .dep-stat-page {
            padding: 4px 0 80px;
        }

        .dep-stat-hero,
        .dep-stat-filters,
        .dep-stat-kpi,
        .dep-stat-panel,
        .dep-stat-empty {
            border-radius: 22px;
        }

        .dep-stat-hero,
        .dep-stat-filters,
        .dep-stat-panel,
        .dep-stat-empty {
            padding: 16px;
        }

        .dep-stat-kpi {
            padding: 16px;
        }

        .dep-stat-title-row {
            align-items: flex-start;
        }

        .dep-stat-trend-svg {
            height: 180px;
        }
    }
</style>

<div class="dep-stat-page">
    <div class="dep-stat-shell">
        <section class="dep-stat-hero">
            <div class="dep-stat-hero-grid">
                <div>
                    <span class="dep-stat-eyebrow">Pilotage financier</span>
                    <div class="dep-stat-title-row">
                        <span class="dep-stat-title-icon"><i class="fas fa-chart-line"></i></span>
                        <div>
                            <h1 class="dep-stat-title">Statistiques des depenses</h1>
                            <p class="dep-stat-subtitle">
                                Suivez les volumes, les variations et les postes de depenses dominants sur la periode analysee.
                                Le dashboard croise les statuts, les categories et la tendance mensuelle pour accelerer la lecture budgetaire.
                            </p>
                        </div>
                    </div>

                    <div class="dep-stat-tags">
                        <span class="dep-stat-tag"><i class="fas fa-calendar-alt"></i> {{ $stats['periode']['label'] ?? 'Toutes les periodes' }}</span>
                        @if($selectedCategorie !== '')
                            <span class="dep-stat-tag"><i class="fas fa-layer-group"></i> {{ $categoryLabels[$selectedCategorie] ?? ucfirst(str_replace('_', ' ', $selectedCategorie)) }}</span>
                        @endif
                        @if($selectedStatut !== '')
                            <span class="dep-stat-tag"><i class="fas fa-receipt"></i> {{ $statusLabels[$selectedStatut] ?? ucfirst(str_replace('_', ' ', $selectedStatut)) }}</span>
                        @endif
                        @if($selectedSearch !== '')
                            <span class="dep-stat-tag"><i class="fas fa-search"></i> {{ $selectedSearch }}</span>
                        @endif
                    </div>

                    <div class="dep-stat-actions">
                        <a href="{{ route('depenses.create') }}" class="dep-stat-btn dep-stat-btn-primary">
                            <i class="fas fa-plus"></i>
                            Nouvelle depense
                        </a>
                        <a href="{{ route('depenses.export', request()->query()) }}" class="dep-stat-btn dep-stat-btn-secondary">
                            <i class="fas fa-file-csv"></i>
                            Exporter CSV
                        </a>
                        <a href="{{ route('depenses.index') }}" class="dep-stat-btn dep-stat-btn-secondary">
                            <i class="fas fa-list"></i>
                            Liste des depenses
                        </a>
                    </div>
                </div>

                <div class="dep-stat-side">
                    <div class="dep-stat-summary">
                        <div class="dep-stat-summary-grid">
                            <article class="dep-stat-summary-item">
                                <span class="dep-stat-summary-label">Montant analyse</span>
                                <strong class="dep-stat-summary-value">{{ $currency($stats['montant_total'] ?? 0) }}</strong>
                            </article>
                            <article class="dep-stat-summary-item">
                                <span class="dep-stat-summary-label">Variation</span>
                                <strong class="dep-stat-summary-value">{{ $signedPercent($stats['variation_montant']['percentage'] ?? null) }}</strong>
                            </article>
                            <article class="dep-stat-summary-item">
                                <span class="dep-stat-summary-label">Taux de paiement</span>
                                <strong class="dep-stat-summary-value">{{ number_format((float) ($stats['taux_paiement'] ?? 0), 1, ',', ' ') }}%</strong>
                            </article>
                            <article class="dep-stat-summary-item">
                                <span class="dep-stat-summary-label">Top categorie</span>
                                <strong class="dep-stat-summary-value">{{ $topCategorie['label'] ?? 'Aucune donnee' }}</strong>
                            </article>
                        </div>
                    </div>

                    <div class="dep-stat-focus">
                        <h2>Lecture rapide</h2>
                        <p>Les signaux prioritaires pour la periode selectionnee.</p>

                        <div class="dep-stat-focus-grid">
                            <div class="dep-stat-focus-row">
                                <div>
                                    <span class="dep-stat-focus-label">Comparaison</span>
                                    <strong class="dep-stat-focus-value">{{ $stats['periode']['comparison_label'] ?? 'Periode precedente' }}</strong>
                                </div>
                                <span class="dep-stat-delta {{ $stats['variation_montant']['direction'] ?? 'flat' }}">
                                    {{ $signedCurrency($stats['variation_montant']['delta'] ?? 0) }}
                                </span>
                            </div>

                            <div class="dep-stat-focus-row">
                                <div>
                                    <span class="dep-stat-focus-label">Montant en attente</span>
                                    <strong class="dep-stat-focus-value">{{ $currency($stats['montant_en_attente'] ?? 0) }}</strong>
                                </div>
                                <span class="dep-stat-focus-copy">A surveiller pour les sorties non reglees</span>
                            </div>

                            <div class="dep-stat-focus-row">
                                <div>
                                    <span class="dep-stat-focus-label">Plus grosse depense</span>
                                    <strong class="dep-stat-focus-value">{{ $largestExpense['description'] ?? 'Aucune depense' }}</strong>
                                </div>
                                <span class="dep-stat-focus-copy">{{ $largestExpense ? $currency($largestExpense['montant']) : '0,00 DH' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <form method="GET" action="{{ route('depenses.statistiques') }}" class="dep-stat-filters">
            <div class="dep-stat-panel-head">
                <div>
                    <h2>Filtres d'analyse</h2>
                    <p>Ajustez la periode, la categorie et le statut pour recalculer toutes les tuiles du dashboard.</p>
                </div>
            </div>

            <div class="dep-stat-filters-grid">
                <label class="dep-stat-field">
                    <span class="dep-stat-filter-label">Periode</span>
                    <select name="period" id="dep-stat-period">
                        <option value="month" @selected($selectedPeriod === 'month')>Mois</option>
                        <option value="year" @selected($selectedPeriod === 'year')>Annee</option>
                        <option value="custom" @selected($selectedPeriod === 'custom')>Personnalisee</option>
                        <option value="all" @selected($selectedPeriod === 'all')>Toutes les periodes</option>
                    </select>
                </label>

                <label class="dep-stat-field {{ $showMonthFields ? '' : 'is-hidden' }}" data-period-group="month-year">
                    <span class="dep-stat-filter-label">Mois</span>
                    <select name="month" id="dep-stat-month">
                        @foreach($monthOptions as $value => $label)
                            <option value="{{ $value }}" @selected($selectedMonth === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="dep-stat-field {{ $showMonthFields ? '' : 'is-hidden' }}" data-period-group="month-year">
                    <span class="dep-stat-filter-label">Annee</span>
                    <select name="year" id="dep-stat-year">
                        @for($year = now()->year + 1; $year >= now()->year - 5; $year--)
                            <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                        @endfor
                    </select>
                </label>

                <label class="dep-stat-field {{ $showCustomFields ? '' : 'is-hidden' }}" data-period-group="custom">
                    <span class="dep-stat-filter-label">Du</span>
                    <input type="date" name="date_from" value="{{ $selectedDateFrom }}">
                </label>

                <label class="dep-stat-field {{ $showCustomFields ? '' : 'is-hidden' }}" data-period-group="custom">
                    <span class="dep-stat-filter-label">Au</span>
                    <input type="date" name="date_to" value="{{ $selectedDateTo }}">
                </label>

                <label class="dep-stat-field">
                    <span class="dep-stat-filter-label">Categorie</span>
                    <select name="categorie">
                        <option value="">Toutes</option>
                        @foreach(($stats['filtres']['categories'] ?? []) as $categorie)
                            <option value="{{ $categorie }}" @selected($selectedCategorie === $categorie)>
                                {{ $categoryLabels[$categorie] ?? ucfirst(str_replace('_', ' ', $categorie)) }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="dep-stat-field">
                    <span class="dep-stat-filter-label">Statut</span>
                    <select name="statut">
                        <option value="">Tous</option>
                        @foreach(($stats['filtres']['statuts'] ?? []) as $statut)
                            <option value="{{ $statut }}" @selected($selectedStatut === $statut)>
                                {{ $statusLabels[$statut] ?? ucfirst(str_replace('_', ' ', $statut)) }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="dep-stat-field">
                    <span class="dep-stat-filter-label">Recherche</span>
                    <input type="text" name="search" value="{{ $selectedSearch }}" placeholder="Description, beneficiaire, facture...">
                </label>

                <div class="dep-stat-filter-actions">
                    <button type="submit" class="dep-stat-btn dep-stat-btn-primary">
                        <i class="fas fa-filter"></i>
                        Appliquer
                    </button>
                    <a href="{{ route('depenses.statistiques') }}" class="dep-stat-btn dep-stat-btn-secondary">
                        <i class="fas fa-rotate-left"></i>
                        Reinitialiser
                    </a>
                </div>
            </div>
        </form>

        @if(($stats['total_depenses'] ?? 0) > 0)
            <section class="dep-stat-kpis">
                <article class="dep-stat-kpi">
                    <div class="dep-stat-kpi-top">
                        <div>
                            <span class="dep-stat-kpi-label">Montant sur la periode</span>
                            <h2 class="dep-stat-kpi-value">{{ $currency($stats['montant_total'] ?? 0) }}</h2>
                            <p class="dep-stat-kpi-copy">Base sur {{ $stats['periode']['label'] ?? 'la periode selectionnee' }}.</p>
                        </div>
                        <span class="dep-stat-kpi-icon"><i class="fas fa-wallet"></i></span>
                    </div>
                    <div class="dep-stat-kpi-bottom">
                        <span class="dep-stat-delta {{ $stats['variation_montant']['direction'] ?? 'flat' }}">{{ $signedPercent($stats['variation_montant']['percentage'] ?? null) }}</span>
                        <span class="dep-stat-kpi-copy">vs {{ $stats['variation_montant']['comparison_label'] ?? 'periode precedente' }}</span>
                    </div>
                </article>

                <article class="dep-stat-kpi">
                    <div class="dep-stat-kpi-top">
                        <div>
                            <span class="dep-stat-kpi-label">Volume de depenses</span>
                            <h2 class="dep-stat-kpi-value">{{ $stats['total_depenses'] ?? 0 }}</h2>
                            <p class="dep-stat-kpi-copy">Nombre total d'enregistrements consolides.</p>
                        </div>
                        <span class="dep-stat-kpi-icon"><i class="fas fa-receipt"></i></span>
                    </div>
                    <div class="dep-stat-kpi-bottom">
                        <span class="dep-stat-delta {{ $stats['variation_volume']['direction'] ?? 'flat' }}">{{ $signedPercent($stats['variation_volume']['percentage'] ?? null) }}</span>
                        <span class="dep-stat-kpi-copy">{{ number_format((float) ($stats['variation_volume']['delta'] ?? 0), 0, ',', ' ') }} element(s) d'ecart</span>
                    </div>
                </article>

                <article class="dep-stat-kpi">
                    <div class="dep-stat-kpi-top">
                        <div>
                            <span class="dep-stat-kpi-label">Ticket moyen</span>
                            <h2 class="dep-stat-kpi-value">{{ $currency($stats['ticket_moyen'] ?? 0) }}</h2>
                            <p class="dep-stat-kpi-copy">Montant moyen par depense sur la selection active.</p>
                        </div>
                        <span class="dep-stat-kpi-icon"><i class="fas fa-scale-balanced"></i></span>
                    </div>
                    <div class="dep-stat-kpi-bottom">
                        <span class="dep-stat-delta flat">Stable</span>
                        <span class="dep-stat-kpi-copy">Aide a detecter une hausse de panier moyen.</span>
                    </div>
                </article>

                <article class="dep-stat-kpi">
                    <div class="dep-stat-kpi-top">
                        <div>
                            <span class="dep-stat-kpi-label">Taux de paiement</span>
                            <h2 class="dep-stat-kpi-value">{{ number_format((float) ($stats['taux_paiement'] ?? 0), 1, ',', ' ') }}%</h2>
                            <p class="dep-stat-kpi-copy">{{ $currency($stats['montant_paye'] ?? 0) }} regles et {{ $currency($stats['montant_en_attente'] ?? 0) }} en attente.</p>
                        </div>
                        <span class="dep-stat-kpi-icon"><i class="fas fa-circle-check"></i></span>
                    </div>
                    <div class="dep-stat-kpi-bottom">
                        <span class="dep-stat-delta {{ (($stats['taux_paiement'] ?? 0) >= 75) ? 'down' : 'up' }}">{{ (($stats['taux_paiement'] ?? 0) >= 75) ? 'Sain' : 'A surveiller' }}</span>
                        <span class="dep-stat-kpi-copy">Le ratio paye doit rester sous controle des montants en attente.</span>
                    </div>
                </article>
            </section>

            <div class="dep-stat-grid">
                <section class="dep-stat-panel span-2">
                    <div class="dep-stat-panel-head">
                        <div>
                            <h2>Tendance mensuelle</h2>
                            <p>Evolution glissante des depenses pour visualiser les accelerations ou les reflux budgetaires.</p>
                        </div>
                        <span class="dep-stat-delta {{ $stats['variation_montant']['direction'] ?? 'flat' }}">{{ $signedCurrency($stats['variation_montant']['delta'] ?? 0) }}</span>
                    </div>

                    <div class="dep-stat-chart">
                        <div class="dep-stat-trend-chart">
                            <svg viewBox="0 0 100 58" preserveAspectRatio="none" class="dep-stat-trend-svg" aria-hidden="true">
                                <defs>
                                    <linearGradient id="depStatArea" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="rgba(15,108,189,0.34)"></stop>
                                        <stop offset="100%" stop-color="rgba(15,108,189,0.04)"></stop>
                                    </linearGradient>
                                    <linearGradient id="depStatLine" x1="0" x2="1" y1="0" y2="0">
                                        <stop offset="0%" stop-color="#0f6cbd"></stop>
                                        <stop offset="100%" stop-color="#0ea5e9"></stop>
                                    </linearGradient>
                                </defs>
                                <line x1="0" y1="48" x2="100" y2="48" stroke="rgba(109, 136, 165, 0.26)" stroke-width="0.6"></line>
                                <polygon points="{{ $trendAreaPoints }}" fill="url(#depStatArea)"></polygon>
                                <polyline points="{{ $trendPoints }}" fill="none" stroke="url(#depStatLine)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></polyline>
                                @foreach($trendPointCoordinates as $point)
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="1.5" fill="#ffffff" stroke="#0f6cbd" stroke-width="1.2"></circle>
                                @endforeach
                            </svg>

                            <div class="dep-stat-trend-grid">
                                @foreach($trendSeries as $point)
                                    <article class="dep-stat-trend-card">
                                        <strong>{{ $point['label'] }}</strong>
                                        <div class="dep-stat-row-value">{{ $currency($point['montant'] ?? 0) }}</div>
                                        <span>{{ $point['total'] ?? 0 }} depense(s)</span>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                <section class="dep-stat-panel">
                    <div class="dep-stat-panel-head">
                        <div>
                            <h2>Repartition par statut</h2>
                            <p>Lecture instantanee du poids financier de chaque niveau de traitement.</p>
                        </div>
                    </div>

                    <div class="dep-stat-breakdown">
                        @foreach(($stats['par_statut'] ?? []) as $item)
                            <article class="dep-stat-row">
                                <div class="dep-stat-row-head">
                                    <div>
                                        <div class="dep-stat-row-value">{{ $item['label'] }}</div>
                                        <div class="dep-stat-row-copy">{{ $item['total'] }} depense(s) pour {{ $currency($item['montant']) }}</div>
                                    </div>
                                    <span class="dep-stat-pill {{ $item['key'] }}">{{ number_format((float) $item['share'], 1, ',', ' ') }}%</span>
                                </div>
                                <div class="dep-stat-bar-track">
                                    <div class="dep-stat-bar-fill status-{{ $item['key'] }}" style="width: {{ min(max((float) $item['share'], 4), 100) }}%;"></div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="dep-stat-panel">
                    <div class="dep-stat-panel-head">
                        <div>
                            <h2>Repartition par categorie</h2>
                            <p>Identifiez les postes qui concentrent le budget et ceux a arbitrer rapidement.</p>
                        </div>
                    </div>

                    <div class="dep-stat-breakdown">
                        @foreach(($stats['par_categorie'] ?? []) as $item)
                            <article class="dep-stat-row">
                                <div class="dep-stat-row-head">
                                    <div>
                                        <div class="dep-stat-row-value">{{ $item['label'] }}</div>
                                        <div class="dep-stat-row-copy">{{ $item['total'] }} depense(s) pour {{ $currency($item['montant']) }}</div>
                                    </div>
                                    <span class="dep-stat-pill enregistre">{{ number_format((float) $item['share'], 1, ',', ' ') }}%</span>
                                </div>
                                <div class="dep-stat-bar-track">
                                    <div class="dep-stat-bar-fill" style="width: {{ min(max((float) $item['share'], 4), 100) }}%;"></div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="dep-stat-panel span-2">
                    <div class="dep-stat-panel-head">
                        <div>
                            <h2>Top depenses de la selection</h2>
                            <p>Les plus gros montants a verifier ou a documenter en priorite.</p>
                        </div>
                    </div>

                    <div class="dep-stat-table-wrap">
                        <table class="dep-stat-table">
                            <thead>
                                <tr>
                                    <th>Depense</th>
                                    <th>Categorie</th>
                                    <th>Beneficiaire</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($stats['top_depenses'] ?? []) as $item)
                                    <tr>
                                        <td>
                                            {{ $item['description'] }}
                                            @if(!empty($item['beneficiaire']))
                                                <small>{{ $item['beneficiaire'] }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $categoryLabels[$item['categorie']] ?? ucfirst(str_replace('_', ' ', $item['categorie'])) }}</td>
                                        <td>{{ $item['beneficiaire'] ?: '--' }}</td>
                                        <td>{{ $item['date'] ?: '--' }}</td>
                                        <td>
                                            <span class="dep-stat-pill {{ $item['statut'] }}">
                                                {{ $statusLabels[$item['statut']] ?? ucfirst(str_replace('_', ' ', $item['statut'])) }}
                                            </span>
                                        </td>
                                        <td>{{ $currency($item['montant']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @else
            <section class="dep-stat-empty">
                <div class="dep-stat-empty-icon"><i class="fas fa-chart-pie"></i></div>
                <h2>Aucune donnee exploitable pour cette selection</h2>
                <p>
                    Elargissez la periode, retirez certains filtres ou ajoutez de nouvelles depenses pour alimenter les indicateurs,
                    les repartitions par statut et la tendance mensuelle.
                </p>
                <div class="dep-stat-empty-actions">
                    <a href="{{ route('depenses.statistiques') }}" class="dep-stat-btn dep-stat-btn-secondary">
                        <i class="fas fa-rotate-left"></i>
                        Reinitialiser les filtres
                    </a>
                    <a href="{{ route('depenses.create') }}" class="dep-stat-btn dep-stat-btn-primary">
                        <i class="fas fa-plus"></i>
                        Ajouter une depense
                    </a>
                </div>
            </section>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var periodField = document.getElementById('dep-stat-period');
        if (!periodField) {
            return;
        }

        var syncPeriodFields = function () {
            var value = periodField.value;
            document.querySelectorAll('[data-period-group="month-year"]').forEach(function (node) {
                node.classList.toggle('is-hidden', value !== 'month' && value !== 'year');
            });
            document.querySelectorAll('[data-period-group="custom"]').forEach(function (node) {
                node.classList.toggle('is-hidden', value !== 'custom');
            });
            var monthField = document.getElementById('dep-stat-month');
            if (monthField) {
                monthField.disabled = value !== 'month';
            }
        };

        periodField.addEventListener('change', syncPeriodFields);
        syncPeriodFields();
    });
</script>
@endsection
