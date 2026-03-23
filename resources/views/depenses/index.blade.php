@extends('layouts.app')

@section('title', 'Liste des D&eacute;penses')

@section('content')
<style>
    :root {
        --dep-bg: linear-gradient(180deg, #f4f9ff 0%, #eef5ff 100%);
        --dep-surface: rgba(255, 255, 255, 0.84);
        --dep-card: #ffffff;
        --dep-border: #d8e4f0;
        --dep-border-strong: #cad9eb;
        --dep-text: #17324c;
        --dep-muted: #64809b;
        --dep-primary: #1f78c8;
        --dep-primary-strong: #145d99;
        --dep-accent: #0ea5e9;
        --dep-success: #0f9f77;
        --dep-warning: #d97706;
        --dep-danger: #dc2626;
        --dep-shadow: 0 24px 48px -38px rgba(15, 40, 65, 0.38);
    }

    .depenses-page {
        width: 100%;
        max-width: none;
        padding: 10px 8px 92px;
    }

    .depenses-shell {
        display: grid;
        gap: 18px;
    }

    .dep-hero,
    .dep-kpi,
    .dep-card,
    .dep-table-card,
    .dep-empty-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--dep-border);
        border-radius: 24px;
        box-shadow: var(--dep-shadow);
    }

    .dep-hero {
        padding: 20px;
        background:
            radial-gradient(circle at top right, rgba(31, 120, 200, 0.16) 0%, rgba(31, 120, 200, 0) 34%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 32%),
            var(--dep-bg);
    }

    .dep-kpi,
    .dep-card,
    .dep-table-card,
    .dep-empty-card {
        background: var(--dep-surface);
    }

    .dep-hero::before,
    .dep-kpi::before,
    .dep-card::before,
    .dep-table-card::before,
    .dep-empty-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .dep-hero > *,
    .dep-kpi > *,
    .dep-card > *,
    .dep-table-card > *,
    .dep-empty-card > * {
        position: relative;
        z-index: 1;
    }

    .dep-hero-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
    }

    .dep-hero-main {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        min-width: 0;
        flex: 1 1 auto;
    }

    .dep-hero-left {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        gap: 14px;
        min-width: 0;
        flex: 1 1 auto;
    }

    .dep-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(31, 120, 200, 0.16);
        background: rgba(255, 255, 255, 0.64);
        color: var(--dep-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dep-title-copy {
        min-width: 0;
        flex: 1 1 auto;
    }

    .dep-title-row {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        margin-top: 0;
        flex-wrap: nowrap;
    }

    .dep-title-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
        font-size: 1.35rem;
        background: linear-gradient(135deg, var(--dep-primary) 0%, var(--dep-primary-strong) 100%);
        box-shadow: 0 18px 28px -20px rgba(31, 120, 200, 0.58);
    }

    .dep-title {
        margin: 0;
        font-size: clamp(1.6rem, 2.5vw, 2.25rem);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.04em;
        color: var(--dep-text);
    }

    .dep-subtitle {
        margin: 8px 0 0;
        max-width: 68ch;
        color: var(--dep-muted);
        font-size: .98rem;
        line-height: 1.62;
        font-weight: 600;
    }

    .dep-hero-actions {
        display: flex;
        gap: 0;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: flex-end;
        flex: 0 0 auto;
    }

    .dep-btn {
        min-height: 50px;
        border-radius: 16px;
        border: 1px solid transparent;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        font-size: .92rem;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .dep-btn:hover,
    .dep-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .dep-btn-secondary {
        border-color: #cfdeef;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, rgba(244, 249, 255, 0.94) 100%);
        color: #40617f;
        box-shadow: 0 14px 24px -28px rgba(15, 40, 65, 0.42);
    }

    .dep-btn-secondary:hover,
    .dep-btn-secondary:focus {
        border-color: rgba(31, 120, 200, 0.28);
        background: linear-gradient(180deg, #ffffff 0%, #eef5fb 100%);
        color: var(--dep-primary-strong);
    }

    .dep-btn-primary {
        background: linear-gradient(135deg, var(--dep-primary) 0%, var(--dep-primary-strong) 100%);
        color: #fff;
        box-shadow: 0 18px 28px -22px rgba(31, 120, 200, 0.55);
    }

    .dep-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #d7e3f1;
        background: #f8fbff;
        color: #55708d;
        font-size: .8rem;
        font-weight: 800;
    }

    .dep-kpis {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .dep-kpi {
        padding: 18px;
        display: grid;
        gap: 16px;
    }

    .dep-kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .dep-kpi-label {
        margin: 0;
        color: var(--dep-muted);
        font-size: .82rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .dep-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .dep-kpi-icon.primary {
        background: rgba(31, 120, 200, 0.14);
        color: var(--dep-primary);
    }

    .dep-kpi-icon.success {
        background: rgba(15, 159, 119, 0.14);
        color: var(--dep-success);
    }

    .dep-kpi-icon.warning {
        background: rgba(217, 119, 6, 0.14);
        color: var(--dep-warning);
    }

    .dep-kpi-value {
        margin: 0;
        font-size: clamp(1.95rem, 3vw, 2.55rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.05em;
        color: var(--dep-text);
    }

    .dep-kpi-note {
        margin: 0;
        color: var(--dep-muted);
        font-size: .9rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .dep-card {
        padding: 18px;
    }

    .dep-card-head,
    .dep-table-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .dep-card-title {
        margin: 0;
        font-size: 1.08rem;
        font-weight: 800;
        color: var(--dep-text);
    }

    .dep-card-copy {
        margin: 6px 0 0;
        color: var(--dep-muted);
        font-size: .92rem;
        line-height: 1.56;
        font-weight: 600;
    }

    .dep-counter {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d5e1ef;
        background: #f6fafe;
        color: var(--dep-primary-strong);
        font-size: .8rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) repeat(2, minmax(190px, .8fr)) auto auto;
        gap: 12px;
        align-items: end;
    }

    .filter-group {
        display: grid;
        gap: 8px;
        min-width: 0;
    }

    .filter-label {
        color: #4f6983;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .filter-input,
    .filter-select {
        width: 100%;
        min-height: 52px;
        border-radius: 16px;
        border: 1px solid var(--dep-border-strong);
        background: rgba(255, 255, 255, 0.92);
        color: var(--dep-text);
        font-size: .95rem;
        font-weight: 600;
        padding: 0 16px;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: rgba(31, 120, 200, 0.42);
        box-shadow: 0 0 0 4px rgba(31, 120, 200, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
    }

    .btn-filter,
    .btn-reset {
        min-height: 52px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid transparent;
        font-size: .9rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .btn-filter {
        background: linear-gradient(135deg, var(--dep-primary) 0%, var(--dep-accent) 100%);
        color: #fff;
        box-shadow: 0 16px 24px -24px rgba(14, 165, 233, 0.9);
    }

    .btn-filter:hover {
        color: #fff;
        background: linear-gradient(135deg, var(--dep-primary-strong) 0%, #0a89c8 100%);
        transform: translateY(-1px);
    }

    .btn-reset {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        color: #3b5976;
    }

    .btn-reset:hover {
        color: var(--dep-primary-strong);
        border-color: rgba(31, 120, 200, 0.3);
        background: linear-gradient(180deg, #ffffff 0%, #ebf4fb 100%);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .dep-table-card {
        background: rgba(255, 255, 255, 0.86);
    }

    .dep-table-head {
        padding: 18px 18px 0;
        margin-bottom: 0;
    }

    .dep-table-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .dep-table-wrap {
        padding: 16px 18px 0;
        overflow-x: auto;
    }

    .dep-table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .dep-table thead th {
        padding: 0 16px 14px;
        border-bottom: 1px solid #dce6f1;
        text-align: left;
        color: #6a819a;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .dep-table tbody td {
        padding: 18px 16px;
        border-bottom: 1px solid rgba(216, 228, 240, 0.78);
        vertical-align: middle;
        color: var(--dep-text);
        font-size: .93rem;
    }

    .dep-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(244, 249, 255, 0.9) 0%, rgba(238, 245, 255, 0.86) 100%);
    }

    .dep-description {
        display: grid;
        gap: 6px;
    }

    .dep-description-title {
        color: var(--dep-text);
        font-size: .95rem;
        font-weight: 800;
        line-height: 1.45;
    }

    .dep-description-sub {
        color: var(--dep-muted);
        font-size: .84rem;
        font-weight: 600;
    }

    .date-cell,
    .montant-cell {
        font-weight: 700;
    }

    .categorie-badge,
    .statut-badge {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .categorie-fournitures {
        background: rgba(31, 120, 200, 0.12);
        color: #0c4a6e;
    }

    .categorie-medicaments {
        background: rgba(15, 159, 119, 0.12);
        color: #065f46;
    }

    .categorie-loyer {
        background: rgba(220, 38, 38, 0.12);
        color: #7f1d1d;
    }

    .categorie-personnel {
        background: rgba(217, 119, 6, 0.12);
        color: #713f12;
    }

    .categorie-utilites,
    .categorie-maintenance,
    .categorie-formation,
    .categorie-autre {
        background: #eef4fb;
        color: #4c6886;
    }

    .statut-payee {
        background: rgba(15, 159, 119, 0.12);
        color: #065f46;
    }

    .statut-enregistre {
        background: rgba(31, 120, 200, 0.12);
        color: #0c4a6e;
    }

    .statut-en_attente {
        background: rgba(217, 119, 6, 0.12);
        color: #713f12;
    }

    .actions-cell {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .btn-icon {
        min-height: 38px;
        padding: 0 12px;
        border-radius: 12px;
        border: 1px solid #dae5f1;
        background: #fff;
        color: #57718d;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: .82rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform .2s ease, border-color .2s ease, background .2s ease, color .2s ease;
    }

    .btn-icon:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .btn-icon.edit:hover {
        color: var(--dep-primary);
        border-color: rgba(31, 120, 200, 0.28);
        background: rgba(31, 120, 200, 0.08);
    }

    .btn-icon.delete:hover {
        color: var(--dep-danger);
        border-color: rgba(220, 38, 38, 0.28);
        background: rgba(220, 38, 38, 0.08);
    }

    .dep-empty-card {
        padding: 34px 20px 38px;
        text-align: center;
    }

    .empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(31, 120, 200, 0.14) 0%, rgba(14, 165, 233, 0.2) 100%);
        color: var(--dep-primary);
        font-size: 1.6rem;
    }

    .empty-state h3 {
        margin: 0;
        color: var(--dep-text);
        font-size: 1.2rem;
        font-weight: 800;
    }

    .empty-state p {
        margin: 10px auto 0;
        max-width: 52ch;
        color: var(--dep-muted);
        font-size: .95rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .empty-state .dep-btn {
        margin-top: 18px;
    }

    .pagination-section {
        padding: 18px;
        border-top: 1px solid rgba(216, 228, 240, 0.85);
        background: rgba(247, 251, 255, 0.88);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid #d2deed;
        border-radius: 10px;
        text-decoration: none;
        color: #5b7592;
        font-size: .84rem;
        font-weight: 700;
        transition: all .2s ease;
    }

    .pagination a:hover {
        background: var(--dep-primary);
        color: #fff;
        border-color: var(--dep-primary);
    }

    .pagination .active span {
        background: var(--dep-primary);
        color: #fff;
        border-color: var(--dep-primary);
    }

    body.dark-mode {
        --dep-text: #ebf4ff;
        --dep-muted: #a9c4df;
        --dep-border: #294863;
        --dep-border-strong: #355273;
    }

    body.dark-mode .dep-hero {
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.16) 0%, rgba(56, 189, 248, 0) 34%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 32%),
            linear-gradient(180deg, #11253b 0%, #0e2033 100%);
        border-color: #2a4660;
    }

    body.dark-mode .dep-hero::before,
    body.dark-mode .dep-kpi::before,
    body.dark-mode .dep-card::before,
    body.dark-mode .dep-table-card::before,
    body.dark-mode .dep-empty-card::before {
        background: linear-gradient(180deg, rgba(8, 18, 30, 0.12) 0%, rgba(8, 18, 30, 0) 100%);
    }

    body.dark-mode .dep-kpi,
    body.dark-mode .dep-card,
    body.dark-mode .dep-table-card,
    body.dark-mode .dep-empty-card {
        background: linear-gradient(180deg, rgba(16, 33, 54, 0.94) 0%, rgba(13, 28, 46, 0.96) 100%);
        border-color: #294863;
        box-shadow: 0 20px 40px -32px rgba(0, 0, 0, 0.52);
    }

    body.dark-mode .dep-eyebrow,
    body.dark-mode .dep-chip,
    body.dark-mode .dep-counter {
        border-color: #355879;
        background: linear-gradient(180deg, rgba(23, 48, 76, 0.92) 0%, rgba(18, 38, 60, 0.94) 100%);
        color: #cfe5ff;
    }

    body.dark-mode .dep-title,
    body.dark-mode .dep-kpi-value,
    body.dark-mode .dep-card-title,
    body.dark-mode .dep-description-title,
    body.dark-mode .dep-table thead th,
    body.dark-mode .dep-table tbody td,
    body.dark-mode .empty-state h3,
    body.dark-mode .montant-cell {
        color: #ebf4ff;
    }

    body.dark-mode .dep-subtitle,
    body.dark-mode .dep-kpi-label,
    body.dark-mode .dep-kpi-note,
    body.dark-mode .dep-card-copy,
    body.dark-mode .filter-label,
    body.dark-mode .dep-description-sub,
    body.dark-mode .date-cell,
    body.dark-mode .empty-state p,
    body.dark-mode .pagination a,
    body.dark-mode .pagination span {
        color: #a9c4df;
    }

    body.dark-mode .filter-input,
    body.dark-mode .filter-select,
    body.dark-mode .btn-reset,
    body.dark-mode .btn-icon {
        background: #102035;
        border-color: #355273;
        color: #e4efff;
    }

    body.dark-mode .filter-input::placeholder {
        color: #95aecb;
    }

    body.dark-mode .filter-input:focus,
    body.dark-mode .filter-select:focus {
        border-color: #63a9ff;
        box-shadow: 0 0 0 4px rgba(99, 169, 255, 0.2);
    }

    body.dark-mode .dep-table thead th,
    body.dark-mode .pagination-section {
        border-color: #2a4660;
        background: rgba(18, 38, 60, 0.9);
    }

    body.dark-mode .dep-table tbody td {
        border-bottom-color: #24415b;
    }

    body.dark-mode .dep-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(21, 50, 82, 0.9) 0%, rgba(17, 39, 64, 0.92) 100%);
    }

    body.dark-mode .btn-reset:hover {
        background: linear-gradient(180deg, #1b3857 0%, #15304d 100%);
        border-color: #4a739a;
        color: #ffffff;
    }

    body.dark-mode .dep-btn-secondary {
        border-color: #365b7d;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
        color: #d2e6fb;
        box-shadow: 0 16px 26px -30px rgba(0, 0, 0, 0.42);
    }

    body.dark-mode .dep-btn-secondary:hover,
    body.dark-mode .dep-btn-secondary:focus {
        border-color: #4c7094;
        background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
        color: #ffffff;
    }

    body.dark-mode .btn-icon.edit:hover {
        color: #8fd8ff;
        border-color: rgba(56, 189, 248, 0.28);
        background: rgba(56, 189, 248, 0.08);
    }

    body.dark-mode .btn-icon.delete:hover {
        color: #fda4af;
        border-color: rgba(220, 38, 38, 0.28);
        background: rgba(220, 38, 38, 0.08);
    }

    body.dark-mode .categorie-fournitures {
        background: rgba(31, 120, 200, 0.18);
        color: #8fd8ff;
    }

    body.dark-mode .categorie-medicaments,
    body.dark-mode .statut-payee {
        background: rgba(15, 159, 119, 0.18);
        color: #6ee7b7;
    }

    body.dark-mode .categorie-loyer {
        background: rgba(220, 38, 38, 0.18);
        color: #fda4af;
    }

    body.dark-mode .categorie-personnel,
    body.dark-mode .statut-en_attente {
        background: rgba(217, 119, 6, 0.18);
        color: #fcd34d;
    }

    body.dark-mode .categorie-utilites,
    body.dark-mode .categorie-maintenance,
    body.dark-mode .categorie-formation,
    body.dark-mode .categorie-autre,
    body.dark-mode .statut-enregistre {
        background: rgba(31, 120, 200, 0.18);
        color: #8fd8ff;
    }

    @media (max-width: 1200px) {
        .dep-kpis {
            grid-template-columns: 1fr;
        }

        .filters-grid {
            grid-template-columns: 1fr 1fr;
        }

        .filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 1024px) {
        .dep-hero-top,
        .dep-card-head,
        .dep-table-head {
            grid-template-columns: 1fr;
            flex-direction: column;
            align-items: flex-start;
        }

        .dep-hero-main,
        .dep-hero-left,
        .dep-title-row {
            width: 100%;
        }

        .dep-hero-actions,
        .dep-table-meta {
            width: 100%;
            justify-content: flex-start;
        }

        .dep-hero-actions .dep-btn {
            width: 100%;
        }

        .dep-table {
            min-width: 0;
            display: block;
        }

        .dep-table thead {
            display: none;
        }

        .dep-table tbody {
            display: grid;
            gap: 14px;
        }

        .dep-table tbody tr {
            display: grid;
            gap: 12px;
            padding: 16px;
            border: 1px solid var(--dep-border);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 16px 26px -30px rgba(15, 23, 42, 0.34);
        }

        .dep-table tbody td {
            display: grid;
            grid-template-columns: minmax(106px, 130px) minmax(0, 1fr);
            gap: 12px;
            align-items: start;
            padding: 0;
            border: 0;
        }

        .dep-table tbody td::before {
            content: attr(data-label);
            color: #657e98;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .dep-table tbody td[data-label="Actions"] {
            grid-template-columns: 1fr;
        }

        body.dark-mode .dep-table tbody tr {
            background: rgba(16, 33, 54, 0.92);
            border-color: #294863;
        }

        body.dark-mode .dep-table tbody td::before {
            color: #9fb7d0;
        }
    }

    @media (max-width: 768px) {
        .depenses-page {
            padding-bottom: 104px;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions,
        .filter-actions > * {
            width: 100%;
        }

        .dep-table-wrap,
        .dep-table-head {
            padding-left: 16px;
            padding-right: 16px;
        }

        .pagination-section {
            justify-content: flex-start;
        }
    }

    @media (max-width: 576px) {
        .dep-title-row {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .dep-table tbody td {
            grid-template-columns: 1fr;
        }

        .dep-table tbody td::before {
            margin-bottom: 4px;
        }
    }
</style>

<div class="depenses-page">
    <div class="depenses-shell">
        <section class="dep-hero">
            <div class="dep-hero-top">
                <div class="dep-hero-main">
                    <div class="dep-hero-left">
                        <div class="dep-title-row">
                            <span class="dep-title-icon"><i class="fas fa-wallet"></i></span>
                            <div class="dep-title-copy">
                                <span class="dep-eyebrow">Pilotage financier</span>
                                <h1 class="dep-title">Liste des D&eacute;penses</h1>
                                <p class="dep-subtitle">Suivez les d&eacute;penses du cabinet dans une interface plus claire, plus fluide et plus homog&egrave;ne, avec une lecture rapide des indicateurs, des filtres et des actions.</p>
                            </div>
                        </div>
                    </div>

                    <div class="dep-hero-actions">
                        <a href="{{ route('depenses.create') }}" class="dep-btn dep-btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Nouvelle D&eacute;pense</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="dep-kpis">
            <article class="dep-kpi">
                <div class="dep-kpi-top">
                    <p class="dep-kpi-label">Total ce mois</p>
                    <span class="dep-kpi-icon primary"><i class="fas fa-coins"></i></span>
                </div>
                <h2 class="dep-kpi-value">{{ isset($totalMois) ? number_format($totalMois, 2, ',', ' ') : '0,00' }} DH</h2>
                <p class="dep-kpi-note">Vision mensuelle des sorties pour garder une lecture budg&eacute;taire imm&eacute;diate.</p>
            </article>

            <article class="dep-kpi">
                <div class="dep-kpi-top">
                    <p class="dep-kpi-label">Total cette ann&eacute;e</p>
                    <span class="dep-kpi-icon success"><i class="fas fa-calendar-alt"></i></span>
                </div>
                <h2 class="dep-kpi-value">{{ isset($totalAnnee) ? number_format($totalAnnee, 2, ',', ' ') : '0,00' }} DH</h2>
                <p class="dep-kpi-note">Cumuls annuels r&eacute;unis dans une carte plus lisible et mieux hi&eacute;rarchis&eacute;e.</p>
            </article>

            <article class="dep-kpi">
                <div class="dep-kpi-top">
                    <p class="dep-kpi-label">Total d&eacute;penses</p>
                    <span class="dep-kpi-icon warning"><i class="fas fa-receipt"></i></span>
                </div>
                <h2 class="dep-kpi-value">{{ $depenses->total() ?? 0 }}</h2>
                <p class="dep-kpi-note">Nombre total dÃ¢â‚¬â„¢entr&eacute;es enregistr&eacute;es dans le module, toutes pages confondues.</p>
            </article>
        </section>

        <section class="dep-card">
            <div class="dep-card-head">
                <div>
                    <h2 class="dep-card-title">Filtres et recherche</h2>
                    <p class="dep-card-copy">Affinez lÃ¢â‚¬â„¢affichage rapidement avec des champs compacts, des hauteurs harmonis&eacute;es et des actions plus nettes.</p>
                </div>
                <span class="dep-counter">{{ $depenses->count() }} r&eacute;sultat(s) sur cette page</span>
            </div>

            <form method="GET" action="{{ route('depenses.index') }}">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Recherche</label>
                        <input type="text" name="search" class="filter-input" placeholder="Description, b&eacute;n&eacute;ficiaire..." value="{{ request('search') }}">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Cat&eacute;gorie</label>
                        <select name="categorie" class="filter-select">
                            <option value="">Toutes</option>
                            <option value="fournitures" {{ request('categorie') == 'fournitures' ? 'selected' : '' }}>Fournitures</option>
                            <option value="medicaments" {{ request('categorie') == 'medicaments' ? 'selected' : '' }}>M&eacute;dicaments</option>
                            <option value="loyer" {{ request('categorie') == 'loyer' ? 'selected' : '' }}>Loyer</option>
                            <option value="personnel" {{ request('categorie') == 'personnel' ? 'selected' : '' }}>Personnel</option>
                            <option value="utilites" {{ request('categorie') == 'utilites' ? 'selected' : '' }}>Utilit&eacute;s</option>
                            <option value="maintenance" {{ request('categorie') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="formation" {{ request('categorie') == 'formation' ? 'selected' : '' }}>Formation</option>
                            <option value="autre" {{ request('categorie') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Statut</label>
                        <select name="statut" class="filter-select">
                            <option value="">Tous</option>
                            <option value="payee" {{ request('statut') == 'payee' ? 'selected' : '' }}>Pay&eacute;e</option>
                            <option value="enregistre" {{ request('statut') == 'enregistre' ? 'selected' : '' }}>Enregistr&eacute;e</option>
                            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i>
                            <span>Filtrer</span>
                        </button>
                    </div>
                    <div class="filter-actions">
                        <a href="{{ route('depenses.index') }}" class="btn-reset">
                            <i class="fas fa-rotate-left"></i>
                            <span>R&eacute;initialiser</span>
                        </a>
                    </div>
                </div>
            </form>
        </section>

        @if($depenses->count() > 0)
            <section class="dep-table-card">
                <div class="dep-table-head">
                    <div>
                        <h2 class="dep-card-title">Liste des d&eacute;penses</h2>
                        <p class="dep-card-copy">Un tableau plus respirant, des badges plus int&eacute;gr&eacute;s et des actions mieux align&eacute;es pour &eacute;viter lÃ¢â‚¬â„¢effet assemblage.</p>
                    </div>
                    <div class="dep-table-meta">
                        <span class="dep-counter">Vue premium</span>
                        <span class="dep-chip"><i class="fas fa-table-list"></i> Pagination conserv&eacute;e</span>
                    </div>
                </div>

                <div class="dep-table-wrap">
                    <table class="dep-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Cat&eacute;gorie</th>
                                <th>Montant</th>
                                <th>B&eacute;n&eacute;ficiaire</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($depenses as $depense)
                                <tr>
                                    <td data-label="Description">
                                        <div class="dep-description">
                                            <div class="dep-description-title">{{ $depense->description }}</div>
                                            <div class="dep-description-sub">{{ $depense->details ? \Illuminate\Support\Str::limit($depense->details, 90) : 'Aucun d&eacute;tail compl&eacute;mentaire' }}</div>
                                        </div>
                                    </td>
                                    <td data-label="Date" class="date-cell">{{ $depense->date_depense->format('d/m/Y') }}</td>
                                    <td data-label="Cat&eacute;gorie">
                                        <span class="categorie-badge categorie-{{ $depense->categorie }}">
                                            {{ ucfirst(str_replace('_', ' ', $depense->categorie)) }}
                                        </span>
                                    </td>
                                    <td data-label="Montant" class="montant-cell">{{ number_format($depense->montant, 2, ',', ' ') }} DH</td>
                                    <td data-label="B&eacute;n&eacute;ficiaire">{{ $depense->beneficiaire ?? '-' }}</td>
                                    <td data-label="Statut">
                                        <span class="statut-badge statut-{{ $depense->statut }}">
                                            {{ ucfirst(str_replace('_', ' ', $depense->statut)) }}
                                        </span>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="actions-cell">
                                            <a href="{{ route('depenses.edit', $depense->id) }}" class="btn-icon edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                                <span>Modifier</span>
                                            </a>
                                            <form method="POST" action="{{ route('depenses.destroy', $depense->id) }}" style="display: inline;" onsubmit="return confirm('Confirmer la suppression?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon delete" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                    <span>Supprimer</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($depenses->hasPages())
                    <div class="pagination-section">
                        {{ $depenses->withQueryString()->links() }}
                    </div>
                @endif
            </section>
        @else
            <section class="dep-empty-card empty-state">
                <div class="empty-icon"><i class="fas fa-wallet"></i></div>
                <h3>Aucune d&eacute;pense enregistr&eacute;e</h3>
                <p>La liste est encore vide. Ajoutez une premi&egrave;re d&eacute;pense pour commencer le suivi financier du cabinet dans une interface plus structur&eacute;e.</p>
                <a href="{{ route('depenses.create') }}" class="dep-btn dep-btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Nouvelle D&eacute;pense</span>
                </a>
            </section>
        @endif
    </div>
</div>
@endsection
