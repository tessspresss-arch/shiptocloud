@extends('layouts.app')

@section('title', 'Gestion des factures')
@section('topbar_subtitle', 'Pilotage financier et suivi des paiements dans une interface premium unifiee.')

@push('styles')
<style>
    .billing-page {
        --billing-primary: #1760a5;
        --billing-primary-strong: #0f4c84;
        --billing-accent: #0ea5e9;
        --billing-success: #0f9f77;
        --billing-warning: #c57d10;
        --billing-danger: #cb4d58;
        --billing-surface: linear-gradient(180deg, #f5f9fd 0%, #eef5fb 100%);
        --billing-card: #ffffff;
        --billing-border: #d8e4f1;
        --billing-text: #15314d;
        --billing-muted: #5f7896;
        width: 100%;
        max-width: none;
        padding: 10px 8px 36px;
    }

    .billing-shell {
        display: grid;
        gap: 16px;
    }

    .billing-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--billing-border);
        border-radius: 28px;
        padding: 18px;
        background:
            radial-gradient(circle at top right, rgba(23, 96, 165, 0.18) 0%, rgba(23, 96, 165, 0) 30%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
            var(--billing-surface);
        box-shadow: 0 28px 48px -40px rgba(20, 52, 84, 0.42);
    }

    .billing-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .billing-hero > * {
        position: relative;
        z-index: 1;
    }

    .billing-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 14px;
        align-items: start;
    }

    .billing-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(23, 96, 165, 0.1);
        color: var(--billing-primary);
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .billing-title-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-top: 10px;
    }

    .billing-title-content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex: 1 1 0;
        flex-wrap: wrap;
        min-width: 0;
    }

    .billing-title-copy {
        flex: 1 1 440px;
        min-width: 0;
    }

    .billing-title-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
        font-size: 1.35rem;
        background: linear-gradient(135deg, var(--billing-primary) 0%, var(--billing-primary-strong) 100%);
        box-shadow: 0 16px 28px -18px rgba(23, 96, 165, 0.58);
    }

    .billing-title {
        margin: 0;
        color: var(--billing-text);
        font-size: clamp(1.6rem, 2.8vw, 2.25rem);
        line-height: 1.04;
        letter-spacing: -0.04em;
        font-weight: 900;
    }

    .billing-subtitle {
        margin: 10px 0 0;
        max-width: 72ch;
        color: var(--billing-muted);
        font-size: .98rem;
        line-height: 1.65;
        font-weight: 600;
    }

    .billing-badge-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .billing-badge,
    .billing-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #d4e2f2;
        background: rgba(255, 255, 255, 0.76);
        color: #1a4d86;
        font-size: .82rem;
        font-weight: 800;
    }

    .billing-badge {
        background: linear-gradient(135deg, rgba(23, 96, 165, 0.12) 0%, rgba(14, 165, 233, 0.1) 100%);
        border-color: rgba(23, 96, 165, 0.2);
    }

    .billing-filter-kicker,
    .billing-table-kicker {
        margin: 0 0 10px;
        color: var(--billing-muted);
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .billing-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-self: flex-start;
    }

    .billing-btn,
    .billing-filter-btn {
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid transparent;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: .92rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
        white-space: nowrap;
    }

    .billing-btn:hover,
    .billing-btn:focus,
    .billing-filter-btn:hover,
    .billing-filter-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .billing-btn.secondary {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #385674;
        box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
    }

    .billing-btn.secondary:hover,
    .billing-btn.secondary:focus {
        color: #1f6fa3;
        border-color: rgba(23, 96, 165, 0.28);
        background: linear-gradient(180deg, #ffffff 0%, #edf5fb 100%);
    }

    .billing-btn.primary,
    .billing-filter-btn.primary {
        color: #ffffff;
        background: linear-gradient(135deg, var(--billing-primary) 0%, var(--billing-primary-strong) 100%);
        box-shadow: 0 18px 30px -22px rgba(23, 96, 165, 0.58);
    }

    .billing-btn.primary:hover,
    .billing-btn.primary:focus,
    .billing-filter-btn.primary:hover,
    .billing-filter-btn.primary:focus {
        color: #ffffff;
    }

    .billing-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(23, 96, 165, 0.1);
        color: var(--billing-primary);
    }

    .billing-btn.primary .billing-btn-icon,
    .billing-filter-btn.primary .billing-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: inherit;
    }

    .billing-kpi,
    .billing-filter-card,
    .billing-table-card {
        background: var(--billing-card);
        border: 1px solid var(--billing-border);
        border-radius: 24px;
        box-shadow: 0 24px 36px -34px rgba(15, 23, 42, 0.4);
    }

    .billing-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .billing-kpi {
        padding: 18px;
        display: grid;
        gap: 14px;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .billing-kpi:hover {
        transform: translateY(-2px);
        border-color: #c7d9ea;
        box-shadow: 0 28px 40px -36px rgba(15, 23, 42, 0.25);
    }

    .billing-kpi-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .billing-kpi-label {
        margin: 0;
        color: var(--billing-muted);
        font-size: .82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .billing-kpi-value {
        margin: 8px 0 0;
        color: var(--billing-text);
        font-size: clamp(1.5rem, 2.5vw, 2rem);
        line-height: 1;
        font-weight: 900;
        letter-spacing: -.04em;
    }

    .billing-kpi-value.amount {
        color: var(--billing-primary-strong);
    }

    .billing-kpi-copy {
        margin: 0;
        color: var(--billing-muted);
        font-size: .9rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .billing-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .billing-kpi-icon.total {
        color: #1d6fc0;
        background: rgba(29, 111, 192, 0.12);
    }

    .billing-kpi-icon.paid {
        color: #0f9f77;
        background: rgba(15, 159, 119, 0.14);
    }

    .billing-kpi-icon.unpaid {
        color: #cb4d58;
        background: rgba(203, 77, 88, 0.14);
    }

    .billing-kpi-icon.amount {
        color: #b7791f;
        background: rgba(183, 121, 31, 0.15);
    }

    .billing-filter-card,
    .billing-table-card {
        padding: 20px;
    }

    .billing-filter-head,
    .billing-table-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .billing-filter-title,
    .billing-table-title {
        margin: 0;
        color: var(--billing-text);
        font-size: 1.28rem;
        font-weight: 900;
        letter-spacing: -.03em;
    }

    .billing-filter-copy,
    .billing-table-copy,
    .billing-pagination-copy {
        margin: 8px 0 0;
        color: var(--billing-muted);
        font-size: .93rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .billing-active-filters,
    .billing-table-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .billing-inline-tag,
    .billing-table-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d7e4f2;
        background: #f7fbff;
        color: #567089;
        font-size: .8rem;
        font-weight: 800;
    }

    .billing-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) repeat(3, minmax(170px, .6fr)) auto;
        gap: 14px;
        align-items: end;
    }

    .billing-field {
        min-width: 0;
    }

    .billing-field label {
        display: block;
        margin-bottom: 8px;
        color: var(--billing-muted);
        font-size: .8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .billing-search-wrap {
        position: relative;
    }

    .billing-search-wrap i {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #91a5bc;
    }

    .billing-search,
    .billing-select {
        width: 100%;
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid #d8e4f1;
        background: #ffffff;
        color: var(--billing-text);
        font-size: .94rem;
        font-weight: 600;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .billing-search {
        padding: 0 16px 0 42px;
    }

    .billing-select {
        padding: 0 14px;
    }

    .billing-search:focus,
    .billing-select:focus {
        outline: none;
        border-color: rgba(23, 96, 165, 0.4);
        box-shadow: 0 0 0 4px rgba(23, 96, 165, 0.1);
    }

    .billing-filter-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        min-width: 0;
    }

    .billing-filter-btn.soft {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #385674;
    }

    .billing-table-wrap {
        overflow-x: auto;
        border: 1px solid #dfeaf4;
        border-radius: 20px;
    }

    .billing-table {
        width: 100%;
        min-width: 940px;
        border-collapse: separate;
        border-spacing: 0;
        background: #ffffff;
    }

    .billing-table thead th {
        padding: 16px 18px;
        border-bottom: 1px solid #dfe8f3;
        background: #f7fbff;
        color: #516a85;
        font-size: .78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
        vertical-align: middle;
    }

    .billing-table tbody td {
        padding: 18px;
        border-bottom: 1px solid #edf3f8;
        color: var(--billing-text);
        font-size: .92rem;
        font-weight: 600;
        vertical-align: middle;
    }

    .billing-table tbody tr {
        transition: background .18s ease, transform .18s ease;
    }

    .billing-table tbody tr:hover {
        background: linear-gradient(90deg, #fbfdff 0%, #f2f8ff 100%);
    }

    .billing-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .text-end {
        text-align: right;
    }

    .billing-id-stack,
    .billing-patient-stack,
    .billing-date-stack {
        display: grid;
        gap: 4px;
    }

    .billing-row-id {
        color: #7d97b0;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .billing-row-number {
        color: var(--billing-primary-strong);
        font-size: 1rem;
        font-weight: 900;
        letter-spacing: -.02em;
    }

    .billing-row-meta,
    .billing-patient-meta,
    .billing-date-meta,
    .billing-amount-meta {
        color: #738da7;
        font-size: .82rem;
        font-weight: 600;
    }

    .billing-patient-name {
        color: var(--billing-text);
        font-size: .98rem;
        font-weight: 800;
        letter-spacing: -.01em;
    }

    .billing-date-main {
        color: var(--billing-text);
        font-size: .95rem;
        font-weight: 800;
    }

    .billing-date-main.muted {
        color: #8aa0b7;
    }

    .billing-date-alert {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--billing-danger);
        font-size: .78rem;
        font-weight: 800;
    }

    .billing-amount-cell {
        text-align: right;
    }

    .billing-amount-stack {
        display: grid;
        gap: 4px;
        justify-items: end;
    }

    .billing-amount-main {
        color: var(--billing-primary-strong);
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: -.02em;
        font-variant-numeric: tabular-nums;
    }

    .billing-currency {
        color: #4c739e;
        font-size: .78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-left: 6px;
    }

    .billing-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: .82rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .billing-status-pill.payee {
        background: rgba(15, 159, 119, 0.13);
        border-color: rgba(15, 159, 119, 0.18);
        color: #0f7e5f;
    }

    .billing-status-pill.impayee {
        background: rgba(203, 77, 88, 0.12);
        border-color: rgba(203, 77, 88, 0.18);
        color: #b43a46;
    }

    .billing-status-pill.partiellement_payee {
        background: rgba(197, 125, 16, 0.13);
        border-color: rgba(197, 125, 16, 0.18);
        color: #9a6817;
    }

    .billing-status-pill.annulee {
        background: rgba(100, 116, 139, 0.12);
        border-color: rgba(100, 116, 139, 0.18);
        color: #51606f;
    }

    .billing-status-pill.brouillon {
        background: rgba(29, 111, 192, 0.11);
        border-color: rgba(29, 111, 192, 0.17);
        color: #1b5e9d;
    }

    .billing-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .billing-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1px solid #d9e5f1;
        background: #ffffff;
        color: #607991;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: transform .18s ease, border-color .18s ease, background .18s ease, color .18s ease, box-shadow .18s ease;
        box-shadow: 0 10px 18px -18px rgba(15, 23, 42, 0.55);
    }

    .billing-icon-btn:hover,
    .billing-icon-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
        background: #f7fbff;
        border-color: #bdd3ea;
    }

    .billing-icon-btn.view:hover,
    .billing-icon-btn.view:focus {
        color: var(--billing-success);
    }

    .billing-icon-btn.edit:hover,
    .billing-icon-btn.edit:focus {
        color: var(--billing-warning);
    }

    .billing-icon-btn.print:hover,
    .billing-icon-btn.print:focus {
        color: var(--billing-accent);
    }

    .billing-icon-btn.delete:hover,
    .billing-icon-btn.delete:focus {
        color: var(--billing-danger);
    }

    .billing-icon-btn.is-locked {
        background: rgba(203, 77, 88, 0.1);
        border-color: rgba(203, 77, 88, 0.16);
        color: #b43a46;
    }

    .billing-table-tools {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .billing-mode-compact .billing-table tbody td {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .billing-mode-compact .billing-row-meta,
    .billing-mode-compact .billing-patient-meta,
    .billing-mode-compact .billing-date-meta,
    .billing-mode-compact .billing-amount-meta,
    .billing-mode-compact .billing-date-alert {
        display: none;
    }

    .billing-mode-compact .billing-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
    }

    .billing-mode-cards .billing-table thead {
        display: none;
    }

    .billing-mode-cards .billing-table,
    .billing-mode-cards .billing-table tbody {
        display: grid;
        gap: 12px;
        width: 100%;
    }

    .billing-mode-cards .billing-table tbody tr {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 14px;
        padding: 16px;
        border: 1px solid #dbe6f1;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 16px 22px -26px rgba(15, 23, 42, .16);
    }

    .billing-mode-cards .billing-table tbody td {
        display: grid;
        gap: 4px;
        padding: 0;
        border: none;
    }

    .billing-mode-cards .billing-table tbody td::before {
        content: attr(data-label);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #7a8ea5;
    }

    .billing-mode-cards .billing-table tbody td[data-label="Actions"] {
        grid-column: 1 / -1;
    }

    .billing-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 18px;
    }

    .billing-empty {
        padding: 34px 18px;
        text-align: center;
    }

    .billing-empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(23, 96, 165, 0.1);
        color: var(--billing-primary);
        font-size: 1.5rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.85);
    }

    .billing-empty-title {
        margin: 0;
        color: var(--billing-text);
        font-size: 1.18rem;
        font-weight: 900;
    }

    .billing-empty-copy {
        margin: 10px auto 0;
        max-width: 54ch;
        color: var(--billing-muted);
        font-size: .95rem;
        line-height: 1.65;
        font-weight: 600;
    }

    html.dark body .billing-page,
    body.dark-mode .billing-page,
    body.theme-dark .billing-page {
        --billing-surface: linear-gradient(180deg, #152233 0%, #122032 100%);
        --billing-card: #162332;
        --billing-border: #2f4358;
        --billing-text: #e6edf6;
        --billing-muted: #9eb1c7;
    }

    html.dark body .billing-hero,
    body.dark-mode .billing-hero,
    body.theme-dark .billing-hero {
        background:
            radial-gradient(circle at top right, rgba(64, 140, 219, 0.18) 0%, rgba(64, 140, 219, 0) 32%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 36%),
            linear-gradient(180deg, #16283c 0%, #122032 100%);
        border-color: #35506a;
        box-shadow: 0 28px 48px -40px rgba(3, 10, 20, 0.8);
    }

    html.dark body .billing-hero::before,
    body.dark-mode .billing-hero::before,
    body.theme-dark .billing-hero::before {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0) 100%);
    }

    html.dark body .billing-eyebrow,
    body.dark-mode .billing-eyebrow,
    body.theme-dark .billing-eyebrow {
        background: rgba(101, 173, 241, 0.14);
        color: #8dc6ff;
    }

    html.dark body .billing-filter-card,
    html.dark body .billing-kpi,
    html.dark body .billing-table-card,
    body.dark-mode .billing-filter-card,
    body.dark-mode .billing-kpi,
    body.dark-mode .billing-table-card,
    body.theme-dark .billing-filter-card,
    body.theme-dark .billing-kpi,
    body.theme-dark .billing-table-card,
    body.theme-dark .billing-table-card {
        border-color: #35506a;
    }

    html.dark body .billing-kpi,
    body.dark-mode .billing-kpi,
    body.theme-dark .billing-kpi {
        background: linear-gradient(180deg, rgba(22, 38, 56, 0.96) 0%, rgba(18, 32, 50, 0.96) 100%);
    }

    html.dark body .billing-filter-card,
    html.dark body .billing-table-card,
    body.dark-mode .billing-filter-card,
    body.dark-mode .billing-table-card,
    body.theme-dark .billing-filter-card,
    body.theme-dark .billing-table-card {
        background: linear-gradient(180deg, rgba(22, 38, 56, 0.98) 0%, rgba(18, 32, 50, 0.98) 100%);
    }


    html.dark body .billing-badge,
    html.dark body .billing-chip,
    html.dark body .billing-inline-tag,
    html.dark body .billing-table-chip,
    body.dark-mode .billing-badge,
    body.dark-mode .billing-chip,
    body.dark-mode .billing-inline-tag,
    body.dark-mode .billing-table-chip,
    body.theme-dark .billing-badge,
    body.theme-dark .billing-chip,
    body.theme-dark .billing-inline-tag,
    body.theme-dark .billing-table-chip {
        background: #14273e;
        border-color: #305173;
        color: #cde2ff;
    }

    html.dark body .billing-btn.secondary,
    html.dark body .billing-filter-btn.soft,
    body.dark-mode .billing-btn.secondary,
    body.dark-mode .billing-filter-btn.soft,
    body.theme-dark .billing-btn.secondary,
    body.theme-dark .billing-filter-btn.soft {
        border-color: #365b7d;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
        color: #d2e6fb;
    }

    html.dark body .billing-btn.secondary:hover,
    html.dark body .billing-btn.secondary:focus,
    html.dark body .billing-filter-btn.soft:hover,
    html.dark body .billing-filter-btn.soft:focus,
    body.dark-mode .billing-btn.secondary:hover,
    body.dark-mode .billing-btn.secondary:focus,
    body.dark-mode .billing-filter-btn.soft:hover,
    body.dark-mode .billing-filter-btn.soft:focus,
    body.theme-dark .billing-btn.secondary:hover,
    body.theme-dark .billing-btn.secondary:focus,
    body.theme-dark .billing-filter-btn.soft:hover,
    body.theme-dark .billing-filter-btn.soft:focus {
        border-color: #4c7094;
        background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
        color: #ffffff;
    }

    html.dark body .billing-btn-icon,
    body.dark-mode .billing-btn-icon,
    body.theme-dark .billing-btn-icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    html.dark body .billing-kpi-icon,
    body.dark-mode .billing-kpi-icon,
    body.theme-dark .billing-kpi-icon {
        background: rgba(119, 183, 255, 0.12);
        border-color: rgba(119, 183, 255, 0.08);
    }

    html.dark body .billing-kpi-value.amount,
    body.dark-mode .billing-kpi-value.amount,
    body.theme-dark .billing-kpi-value.amount {
        color: #7fc3ff;
    }

    html.dark body .billing-search,
    html.dark body .billing-select,
    body.dark-mode .billing-search,
    body.dark-mode .billing-select,
    body.theme-dark .billing-search,
    body.theme-dark .billing-select {
        background: #13263f;
        border-color: #355985;
        color: #deebf9;
    }

    html.dark body .billing-search::placeholder,
    body.dark-mode .billing-search::placeholder,
    body.theme-dark .billing-search::placeholder {
        color: #98adc4;
    }

    html.dark body .billing-search-wrap i,
    body.dark-mode .billing-search-wrap i,
    body.theme-dark .billing-search-wrap i {
        color: #98adc4;
    }

    html.dark body .billing-table-wrap,
    body.dark-mode .billing-table-wrap,
    body.theme-dark .billing-table-wrap {
        background: linear-gradient(180deg, #13263b 0%, #102032 100%);
        border-color: #29455f;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
    }

    html.dark body .billing-table,
    body.dark-mode .billing-table,
    body.theme-dark .billing-table {
        background: #0f1a28;
    }

    html.dark body .billing-table thead th,
    body.dark-mode .billing-table thead th,
    body.theme-dark .billing-table thead th {
        background: #102337;
        border-color: #284660;
        color: #a8bfd7;
    }

    html.dark body .billing-table tbody td,
    body.dark-mode .billing-table tbody td,
    body.theme-dark .billing-table tbody td {
        border-bottom-color: #203c57;
    }

    html.dark body .billing-table tbody tr:hover,
    body.dark-mode .billing-table tbody tr:hover,
    body.theme-dark .billing-table tbody tr:hover {
        background: rgba(20, 43, 66, 0.86);
    }

    html.dark body .billing-filter-head,
    html.dark body .billing-table-head,
    body.dark-mode .billing-filter-head,
    body.dark-mode .billing-table-head,
    body.theme-dark .billing-filter-head,
    body.theme-dark .billing-table-head {
        border-bottom-color: #29445d;
    }

    html.dark body .billing-table-footer,
    body.dark-mode .billing-table-footer,
    body.theme-dark .billing-table-footer {
        border-top-color: #29445d;
        background: linear-gradient(180deg, rgba(19, 38, 59, 0.72) 0%, rgba(19, 38, 59, 0) 100%);
    }

    html.dark body .billing-icon-btn,
    body.dark-mode .billing-icon-btn,
    body.theme-dark .billing-icon-btn {
        background: #13263f;
        border-color: #35506a;
        color: #c8dbef;
    }

    html.dark body .billing-icon-btn:hover,
    html.dark body .billing-icon-btn:focus,
    body.dark-mode .billing-icon-btn:hover,
    body.dark-mode .billing-icon-btn:focus,
    body.theme-dark .billing-icon-btn:hover,
    body.theme-dark .billing-icon-btn:focus {
        background: #183556;
        border-color: #4f74a3;
    }

    @media (max-width: 1200px) {
        .billing-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .billing-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .billing-filter-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 992px) {
        .billing-hero-grid {
            grid-template-columns: 1fr;
        }

        .billing-hero-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .billing-hero-btn {
            width: 100%;
        }

        .billing-table-wrap {
            overflow: visible;
            border: 0;
            border-radius: 0;
            background: transparent;
        }

        .billing-table {
            min-width: 0;
            display: block;
            background: transparent;
        }

        .billing-table thead {
            display: none;
        }

        .billing-table tbody {
            display: grid;
            gap: 14px;
        }

        .billing-table tbody tr {
            display: grid;
            gap: 12px;
            padding: 18px;
            border: 1px solid #dbe7f1;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 16px 28px -28px rgba(15, 40, 65, 0.3);
        }

        .billing-table tbody td {
            display: grid;
            grid-template-columns: minmax(92px, 116px) minmax(0, 1fr);
            gap: 10px;
            align-items: start;
            padding: 0;
            border: 0;
        }

        .billing-table tbody td::before {
            content: attr(data-label);
            color: #718aa3;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .billing-table tbody td:last-child {
            grid-template-columns: 1fr;
        }

        .billing-actions {
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        html.dark body .billing-table tbody tr,
        body.dark-mode .billing-table tbody tr,
        body.theme-dark .billing-table tbody tr {
            background: #11273d;
            border-color: #26435d;
        }
    }

    @media (max-width: 767px) {
        .billing-page {
            padding-left: 0;
            padding-right: 0;
        }

        .billing-kpi-grid,
        .billing-filter-form {
            grid-template-columns: 1fr;
        }

        .billing-filter-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .billing-hero-actions {
            grid-template-columns: 1fr;
        }

        .billing-btn,
        .billing-filter-btn {
            width: 100%;
        }

        .billing-title-content {
            flex-direction: column;
            align-items: stretch;
        }

        .billing-filter-head,
        .billing-table-head,
        .billing-table-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .billing-active-filters,
        .billing-table-meta {
            justify-content: flex-start;
        }

        .billing-filter-card,
        .billing-table-card {
            padding: 18px;
        }

        .billing-table tbody tr {
            padding: 16px;
        }

        .billing-table tbody td {
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .billing-table tbody td::before {
            margin-bottom: 2px;
        }

        .billing-status-pill,
        .billing-btn,
        .billing-filter-btn {
            width: 100%;
            justify-content: center;
        }

        .billing-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(38px, 38px));
        }
    }
</style>
@endpush

@section('content')
@php($displayMode = request('display', 'table'))
<div class="container-fluid billing-page billing-mode-{{ $displayMode }}">
    <div class="billing-shell">
        <section class="billing-hero">
            <div class="billing-hero-grid">
                <div>
                    <span class="billing-eyebrow">Pilotage financier</span>
                    <div class="billing-title-row">
                        <span class="billing-title-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </span>
                        <div class="billing-title-content">
                            <div class="billing-title-copy">
                                <h1 class="billing-title">Gestion des factures</h1>
                                <p class="billing-subtitle">Suivez les factures du cabinet avec une lecture plus claire des montants, des echeances et des statuts de paiement, dans une experience coherente avec les autres modules premium.</p>
                            </div>

                            <div class="billing-hero-actions">
                                <a href="#" class="billing-btn billing-hero-btn secondary" id="exportBtn">
                                    <span class="billing-btn-icon"><i class="fas fa-file-export"></i></span>
                                    <span>Exporter</span>
                                </a>
                                <a href="{{ route('factures.create') }}" class="billing-btn billing-hero-btn primary">
                                    <span class="billing-btn-icon"><i class="fas fa-plus"></i></span>
                                    <span>Nouvelle facture</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="billing-badge-row">
                        <span class="billing-badge"><i class="fas fa-layer-group"></i>{{ $factures->total() }} resultat{{ $factures->total() > 1 ? 's' : '' }}</span>
                        <span class="billing-chip"><i class="fas fa-circle-check"></i>{{ $paidFactures }} payee{{ $paidFactures > 1 ? 's' : '' }}</span>
                        <span class="billing-chip"><i class="fas fa-hourglass-half"></i>{{ $unpaidFactures }} a suivre</span>
                    </div>
                </div>

            </div>
        </section>

        <section class="billing-kpi-grid">
            <article class="billing-kpi">
                <div class="billing-kpi-top">
                    <div>
                        <p class="billing-kpi-label">Factures totales</p>
                        <p class="billing-kpi-value">{{ $totalFactures }}</p>
                    </div>
                    <span class="billing-kpi-icon total"><i class="fas fa-file-invoice"></i></span>
                </div>
                <p class="billing-kpi-copy">Volume total de factures actuellement prises en compte dans le perimetre d'affichage.</p>
            </article>

            <article class="billing-kpi">
                <div class="billing-kpi-top">
                    <div>
                        <p class="billing-kpi-label">Factures payees</p>
                        <p class="billing-kpi-value">{{ $paidFactures }}</p>
                    </div>
                    <span class="billing-kpi-icon paid"><i class="fas fa-circle-check"></i></span>
                </div>
                <p class="billing-kpi-copy">Factures reglees, utiles pour suivre la performance d'encaissement du cabinet.</p>
            </article>

            <article class="billing-kpi">
                <div class="billing-kpi-top">
                    <div>
                        <p class="billing-kpi-label">Factures impayees</p>
                        <p class="billing-kpi-value">{{ $unpaidFactures }}</p>
                    </div>
                    <span class="billing-kpi-icon unpaid"><i class="fas fa-triangle-exclamation"></i></span>
                </div>
                <p class="billing-kpi-copy">Factures a relancer ou a surveiller pour garder une vue finance plus actionnable.</p>
            </article>

            <article class="billing-kpi">
                <div class="billing-kpi-top">
                    <div>
                        <p class="billing-kpi-label">Montant total</p>
                        <p class="billing-kpi-value amount">{{ number_format($totalAmount, 2, ',', ' ') }}</p>
                    </div>
                    <span class="billing-kpi-icon amount"><i class="fas fa-wallet"></i></span>
                </div>
                <p class="billing-kpi-copy">Lecture immediate des DH factures avec une mise en avant renforcee des montants.</p>
            </article>
        </section>

        <section class="billing-filter-card">
            <div class="billing-filter-head">
                <div>
                    <p class="billing-filter-kicker">Recherche et filtrage</p>
                    <h2 class="billing-filter-title">Filtres facturation</h2>
                    <p class="billing-filter-copy">Affinez la liste par texte libre, statut, periode et volume d'affichage avec une grille plus compacte et mieux alignee.</p>
                </div>

                @if($hasFilters)
                    <div class="billing-active-filters">
                        @if(request('search'))
                            <span class="billing-inline-tag"><i class="fas fa-search"></i>{{ request('search') }}</span>
                        @endif
                        @if($statusLabel)
                            <span class="billing-inline-tag"><i class="fas fa-circle-info"></i>{{ $statusLabel }}</span>
                        @endif
                        @if($selectedPeriodLabel)
                            <span class="billing-inline-tag"><i class="fas fa-calendar-days"></i>{{ $selectedPeriodLabel }}</span>
                        @endif
                        <span class="billing-inline-tag"><i class="fas fa-stream"></i>{{ $currentPerPage }} / page</span>
                    </div>
                @endif
            </div>

            <form method="GET" action="{{ route('factures.index') }}" class="billing-filter-form">
                <input type="hidden" name="display" value="{{ $displayMode }}">
                <div class="billing-field">
                    <label for="billingSearch">Recherche</label>
                    <div class="billing-search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="billingSearch" name="search" class="billing-search" value="{{ request('search') }}" placeholder="Numero de facture, patient...">
                    </div>
                </div>

                <div class="billing-field">
                    <label for="billingStatus">Statut</label>
                    <select id="billingStatus" name="status" class="billing-select">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ $selectedStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="billing-field">
                    <label for="billingPeriod">Periode</label>
                    <select id="billingPeriod" name="period" class="billing-select">
                        <option value="">Toutes les periodes</option>
                        <option value="month" {{ $selectedPeriod === 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="quarter" {{ $selectedPeriod === 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                        <option value="year" {{ $selectedPeriod === 'year' ? 'selected' : '' }}>Cette annee</option>
                    </select>
                </div>

                <div class="billing-field">
                    <label for="billingPerPage">Lignes</label>
                    <select id="billingPerPage" name="per_page" class="billing-select">
                        @foreach([15, 25, 50, 100] as $perPageOption)
                            <option value="{{ $perPageOption }}" {{ $currentPerPage === $perPageOption ? 'selected' : '' }}>{{ $perPageOption }} / page</option>
                        @endforeach
                    </select>
                </div>

                <div class="billing-filter-actions">
                    <button type="submit" class="billing-filter-btn primary">
                        <span class="billing-btn-icon"><i class="fas fa-filter"></i></span>
                        <span>Appliquer</span>
                    </button>

                    @if($hasFilters)
                        <a href="{{ route('factures.index', ['display' => $displayMode]) }}" class="billing-filter-btn soft">
                            <span class="billing-btn-icon"><i class="fas fa-rotate-left"></i></span>
                            <span>Reinitialiser</span>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="billing-table-card">
            <div class="billing-table-head">
                <div>
                    <p class="billing-table-kicker">Registre financier</p>
                    <h2 class="billing-table-title">Liste des factures</h2>
                    <p class="billing-table-copy">Mettez en regard numero, patient, echeance, montant et statut dans un tableau plus respirant et plus business.</p>
                </div>

                <div class="billing-table-tools">
                    <div class="billing-table-meta">
                        <span class="billing-table-chip"><i class="fas fa-table-list"></i>{{ $factures->count() }} lignes</span>
                        <span class="billing-table-chip"><i class="fas fa-wallet"></i>Montants aligns</span>
                        <span class="billing-table-chip"><i class="fas fa-mobile-screen-button"></i>Scroll mobile</span>
                    </div>
                    <div class="display-mode-switch" role="group" aria-label="Mode d affichage">
                        <a href="{{ request()->fullUrlWithQuery(['display' => 'table', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'table' ? 'active' : '' }}">Mode tableau</a>
                        <a href="{{ request()->fullUrlWithQuery(['display' => 'compact', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'compact' ? 'active' : '' }}">Mode compact</a>
                        <a href="{{ request()->fullUrlWithQuery(['display' => 'cards', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'cards' ? 'active' : '' }}">Mode cartes</a>
                    </div>
                </div>
            </div>

            <div class="billing-table-wrap">
                <table class="billing-table">
                    <thead>
                        <tr>
                            <th>Numero / reference</th>
                            <th>Patient</th>
                            <th>Date facture</th>
                            <th>Echeance</th>
                            <th class="text-end">Montant total</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($factures as $facture)
                            <tr>
                                <td data-label="Numero / reference">
                                    <div class="billing-id-stack">
                                        <span class="billing-row-id">Facture #{{ $facture->id }}</span>
                                        <span class="billing-row-number">{{ $facture->numero_facture ?: 'Reference non renseignee' }}</span>
                                        <span class="billing-row-meta">Document de facturation du cabinet</span>
                                    </div>
                                </td>
                                <td data-label="Patient">
                                    <div class="billing-patient-stack">
                                        <span class="billing-patient-name">{{ $facture->display_patient_name }}</span>
                                        <span class="billing-patient-meta">ID patient: {{ $facture->patient->id ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td data-label="Date facture">
                                    <div class="billing-date-stack">
                                        <span class="billing-date-main {{ $facture->display_date_facture_muted }}">{{ $facture->display_date_facture }}</span>
                                        <span class="billing-date-meta">{{ $facture->display_date_facture_human }}</span>
                                    </div>
                                </td>
                                <td data-label="Echeance">
                                    <div class="billing-date-stack">
                                        <span class="billing-date-main {{ $facture->display_date_echeance_muted }}">{{ $facture->display_date_echeance }}</span>
                                        <span class="billing-date-meta">{{ $facture->display_date_echeance_human }}</span>
                                        @if($facture->is_overdue)
                                            <span class="billing-date-alert"><i class="fas fa-triangle-exclamation"></i>En retard</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="billing-amount-cell" data-label="Montant total">
                                    <div class="billing-amount-stack">
                                        <div class="billing-amount-main">
                                            {{ number_format($facture->montant_total, 2, ',', ' ') }}<span class="billing-currency">DH</span>
                                        </div>
                                        @if((float) $facture->remise > 0)
                                            <span class="billing-amount-meta">Remise: {{ number_format($facture->remise, 2, ',', ' ') }} DH</span>
                                        @else
                                            <span class="billing-amount-meta">Sans remise appliquee</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Statut">
                                    <span class="billing-status-pill {{ $facture->status_class }}">
                                        <i class="fas {{ $facture->status_icon }}"></i>
                                        {{ $facture->status_label }}
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <div class="billing-actions">
                                        <a href="{{ route('factures.show', $facture->id) }}" class="billing-icon-btn view action-tone-view" title="Voir la facture" aria-label="Voir la facture {{ $facture->numero_facture ?? $facture->id }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('factures.edit', $facture->id) }}" class="billing-icon-btn edit action-tone-edit" title="Modifier la facture" aria-label="Modifier la facture {{ $facture->numero_facture ?? $facture->id }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="#" class="billing-icon-btn print action-tone-print" title="Imprimer la facture" aria-label="Imprimer la facture {{ $facture->numero_facture ?? $facture->id }}" onclick="window.open('{{ route('factures.show', $facture->id) }}?print=1', '_blank'); return false;">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <form action="{{ route('factures.destroy', $facture->id) }}" method="POST" class="d-inline facture-delete-form" data-is-paid="{{ $facture->is_paid_invoice ? '1' : '0' }}" data-facture-number="{{ $facture->numero_facture ?? $facture->id }}">
                                            @csrf
                                            @method('DELETE')
                                            @if($facture->is_paid_invoice)
                                                <button type="button" class="billing-icon-btn delete is-locked js-delete-paid-blocked" title="Suppression non autorisee" aria-label="Suppression non autorisee pour la facture {{ $facture->numero_facture ?? $facture->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="billing-icon-btn delete action-tone-delete" title="Supprimer la facture" aria-label="Supprimer la facture {{ $facture->numero_facture ?? $facture->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="billing-empty">
                                        <div class="billing-empty-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                        <h3 class="billing-empty-title">Aucune facture trouvee</h3>
                                        <p class="billing-empty-copy">Ajustez vos filtres ou creez une nouvelle facture pour demarrer le suivi financier du cabinet.</p>
                                        <a href="{{ route('factures.create') }}" class="billing-btn primary mt-3">
                                            <span class="billing-btn-icon"><i class="fas fa-plus"></i></span>
                                            <span>Nouvelle facture</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="billing-table-footer">
                <p class="billing-pagination-copy">Affichage de {{ $factures->firstItem() ?? 0 }} a {{ $factures->lastItem() ?? 0 }} sur {{ $factures->total() }} factures</p>
                @if($factures->hasPages())
                    <div>{{ $factures->links() }}</div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/vendor-alerts.js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const flashError = @json(session('error'));
        const flashSuccess = @json(session('success'));

        const showModernAlert = (options) => {
            if (window.Swal) {
                return Swal.fire(options);
            }
            const fallbackText = [options.title, options.text].filter(Boolean).join('\n');
            alert(fallbackText);
            return Promise.resolve({ isConfirmed: false });
        };

        if (flashError) {
            showModernAlert({
                icon: 'warning',
                title: 'Action impossible',
                text: flashError,
                confirmButtonText: 'Compris',
                confirmButtonColor: '#dc2626',
            });
        } else if (flashSuccess) {
            showModernAlert({
                icon: 'success',
                title: 'Operation reussie',
                text: flashSuccess,
                timer: 2200,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        }

        const exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showModernAlert({
                    icon: 'info',
                    title: 'Export',
                    text: 'Fonctionnalite d export a implementer.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2563eb',
                });
            });
        }

        document.querySelectorAll('.js-delete-paid-blocked').forEach(function (button) {
            button.addEventListener('click', function () {
                showModernAlert({
                    icon: 'warning',
                    title: 'Action impossible',
                    text: 'Cette facture a deja ete reglee. La suppression n est pas autorisee.',
                    confirmButtonText: 'Compris',
                    confirmButtonColor: '#dc2626',
                });
            });
        });

        document.querySelectorAll('.facture-delete-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const isPaid = form.dataset.isPaid === '1';
                if (isPaid) {
                    e.preventDefault();
                    showModernAlert({
                        icon: 'warning',
                        title: 'Action impossible',
                        text: 'Cette facture a deja ete reglee. La suppression n est pas autorisee.',
                        confirmButtonText: 'Compris',
                        confirmButtonColor: '#dc2626',
                    });
                    return;
                }

                e.preventDefault();
                const factureNumber = form.dataset.factureNumber || 'cette facture';
                showModernAlert({
                    icon: 'warning',
                    title: 'Confirmer la suppression',
                    text: 'Voulez-vous vraiment supprimer ' + factureNumber + ' ? Cette action est irreversible.',
                    showCancelButton: true,
                    confirmButtonText: 'Supprimer',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    reverseButtons: true,
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
