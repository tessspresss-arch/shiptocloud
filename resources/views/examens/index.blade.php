@extends('layouts.app')

@section('title', 'Examens medicaux')
@section('topbar_subtitle', 'Suivi des examens, priorisation du laboratoire et pilotage des statuts dans une interface premium unifiee.')

@push('styles')
<style>
    .exam-page {
        --exam-primary: #1760a5;
        --exam-primary-strong: #0f4c84;
        --exam-accent: #0ea5e9;
        --exam-success: #0f9f77;
        --exam-warning: #c57d10;
        --exam-danger: #cb4d58;
        --exam-surface: linear-gradient(180deg, #f5f9fd 0%, #eef5fb 100%);
        --exam-card: #ffffff;
        --exam-border: #d8e4f1;
        --exam-text: #15314d;
        --exam-muted: #5f7896;
        width: 100%;
        max-width: none;
        padding: 10px 8px 36px;
    }

    .exam-shell {
        display: grid;
        gap: 18px;
    }

    .exam-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--exam-border);
        border-radius: 28px;
        padding: 22px;
        background:
            radial-gradient(circle at top right, rgba(23, 96, 165, 0.18) 0%, rgba(23, 96, 165, 0) 30%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
            var(--exam-surface);
        box-shadow: 0 28px 48px -40px rgba(20, 52, 84, 0.42);
    }

    .exam-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .exam-hero > * {
        position: relative;
        z-index: 1;
    }

    .exam-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .exam-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(23, 96, 165, 0.1);
        color: var(--exam-primary);
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .exam-title-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-top: 14px;
    }

    .exam-title-content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex: 1 1 0;
        flex-wrap: wrap;
        min-width: 0;
    }

    .exam-title-copy {
        flex: 1 1 440px;
        min-width: 0;
    }

    .exam-title-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
        font-size: 1.35rem;
        background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-strong) 100%);
        box-shadow: 0 16px 28px -18px rgba(23, 96, 165, 0.58);
    }

    .exam-title {
        margin: 0;
        color: var(--exam-text);
        font-size: clamp(1.6rem, 2.8vw, 2.25rem);
        line-height: 1.04;
        letter-spacing: -0.04em;
        font-weight: 900;
    }

    .exam-subtitle {
        margin: 10px 0 0;
        max-width: 72ch;
        color: var(--exam-muted);
        font-size: .98rem;
        line-height: 1.65;
        font-weight: 600;
    }

    .exam-badge-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .exam-badge,
    .exam-chip {
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

    .exam-badge {
        background: linear-gradient(135deg, rgba(23, 96, 165, 0.12) 0%, rgba(14, 165, 233, 0.1) 100%);
        border-color: rgba(23, 96, 165, 0.2);
    }

    .exam-filter-kicker,
    .exam-table-kicker {
        margin: 0 0 10px;
        color: var(--exam-muted);
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .exam-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-self: flex-start;
    }

    .exam-btn,
    .exam-filter-btn {
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

    .exam-btn:hover,
    .exam-btn:focus,
    .exam-filter-btn:hover,
    .exam-filter-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .exam-btn.secondary,
    .exam-filter-btn.soft {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #385674;
        box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
    }

    .exam-btn.secondary:hover,
    .exam-btn.secondary:focus,
    .exam-filter-btn.soft:hover,
    .exam-filter-btn.soft:focus {
        color: #1f6fa3;
        border-color: rgba(23, 96, 165, 0.28);
        background: linear-gradient(180deg, #ffffff 0%, #edf5fb 100%);
    }

    .exam-btn.primary,
    .exam-filter-btn.primary {
        color: #ffffff;
        background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-strong) 100%);
        box-shadow: 0 18px 30px -22px rgba(23, 96, 165, 0.58);
    }

    .exam-btn.primary:hover,
    .exam-btn.primary:focus,
    .exam-filter-btn.primary:hover,
    .exam-filter-btn.primary:focus {
        color: #ffffff;
    }

    .exam-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(23, 96, 165, 0.1);
        color: var(--exam-primary);
    }

    .exam-btn.primary .exam-btn-icon,
    .exam-filter-btn.primary .exam-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: inherit;
    }

    .exam-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .exam-kpi {
        padding: 18px;
        display: grid;
        gap: 14px;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .exam-kpi:hover {
        transform: translateY(-2px);
        border-color: #c7d9ea;
        box-shadow: 0 28px 40px -36px rgba(15, 23, 42, 0.25);
    }

    .exam-kpi-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .exam-kpi-label {
        margin: 0;
        color: var(--exam-muted);
        font-size: .82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .exam-kpi-value {
        margin: 8px 0 0;
        color: var(--exam-text);
        font-size: clamp(1.5rem, 2.5vw, 2rem);
        line-height: 1;
        font-weight: 900;
        letter-spacing: -.04em;
    }

    .exam-kpi-copy {
        margin: 0;
        color: var(--exam-muted);
        font-size: .9rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .exam-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .exam-kpi-icon.total {
        color: #1d6fc0;
        background: rgba(29, 111, 192, 0.12);
    }

    .exam-kpi-icon.waiting {
        color: #b7791f;
        background: rgba(183, 121, 31, 0.15);
    }

    .exam-kpi-icon.progress {
        color: #0891b2;
        background: rgba(8, 145, 178, 0.14);
    }

    .exam-kpi-icon.done {
        color: #0f9f77;
        background: rgba(15, 159, 119, 0.14);
    }

    .exam-filter-card,
    .exam-table-card {
        padding: 20px;
        overflow: hidden;
    }

    .exam-filter-head,
    .exam-table-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .exam-filter-title,
    .exam-table-title {
        margin: 0;
        color: var(--exam-text);
        font-size: 1.28rem;
        font-weight: 900;
        letter-spacing: -.03em;
    }

    .exam-filter-copy,
    .exam-table-copy,
    .exam-pagination-copy {
        margin: 8px 0 0;
        color: var(--exam-muted);
        font-size: .93rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .exam-table-head {
        margin-bottom: 0;
        padding-bottom: 18px;
        border-bottom: 1px solid #dfe8f3;
    }

    .exam-active-filters,
    .exam-table-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .exam-inline-tag,
    .exam-table-chip {
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
        box-shadow: 0 10px 18px -22px rgba(15, 40, 65, 0.26);
    }

    .exam-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) repeat(3, minmax(170px, .6fr)) auto;
        gap: 14px;
        align-items: end;
    }

    .exam-field {
        min-width: 0;
    }

    .exam-field label {
        display: block;
        margin-bottom: 8px;
        color: var(--exam-muted);
        font-size: .8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .exam-search-wrap {
        position: relative;
    }

    .exam-search-wrap i {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #91a5bc;
    }

    .exam-search,
    .exam-select {
        width: 100%;
        min-height: 52px;
        border-radius: 16px;
        border: 1px solid #d8e4f1;
        background: #ffffff;
        color: var(--exam-text);
        font-size: .94rem;
        font-weight: 600;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .exam-search {
        padding: 0 16px 0 42px;
    }

    .exam-select {
        padding: 0 14px;
    }

    .exam-search:focus,
    .exam-select:focus {
        outline: none;
        border-color: rgba(23, 96, 165, 0.4);
        box-shadow: 0 0 0 4px rgba(23, 96, 165, 0.1);
    }

    .exam-filter-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .exam-table-wrap {
        overflow-x: auto;
        border: 1px solid #dfeaf4;
        border-radius: 20px;
        margin-top: 18px;
        background: linear-gradient(180deg, #fcfdff 0%, #f7fbff 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
    }

    .exam-table {
        width: 100%;
        min-width: 1040px;
        border-collapse: separate;
        border-spacing: 0;
        background: #ffffff;
    }

    .exam-table thead th {
        padding: 16px 18px;
        border-bottom: 1px solid #dfe8f3;
        background: linear-gradient(180deg, #f9fbff 0%, #f2f7fd 100%);
        color: #516a85;
        font-size: .78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
        vertical-align: middle;
    }

    .exam-table tbody td {
        padding: 18px;
        border-bottom: 1px solid #edf3f8;
        color: var(--exam-text);
        font-size: .92rem;
        font-weight: 600;
        vertical-align: middle;
    }

    .exam-table tbody tr {
        transition: background .18s ease, transform .18s ease;
    }

    .exam-table tbody tr:hover {
        background: linear-gradient(90deg, #fbfdff 0%, #f2f8ff 100%);
    }

    .exam-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .exam-patient-stack,
    .exam-date-stack,
    .exam-amount-stack {
        display: grid;
        gap: 4px;
    }

    .exam-patient-name {
        color: var(--exam-text);
        font-size: .98rem;
        font-weight: 800;
        letter-spacing: -.01em;
    }

    .exam-patient-meta,
    .exam-date-meta,
    .exam-amount-meta {
        color: #738da7;
        font-size: .82rem;
        font-weight: 600;
    }

    .exam-type-pill,
    .exam-pay-pill,
    .exam-status-pill {
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

    .exam-type-pill {
        background: rgba(29, 111, 192, 0.11);
        border-color: rgba(29, 111, 192, 0.17);
        color: #1b5e9d;
    }

    .exam-status-pill.status-demande {
        background: rgba(183, 121, 31, 0.14);
        border-color: rgba(183, 121, 31, 0.18);
        color: #9a6817;
    }

    .exam-status-pill.status-en_cours,
    .exam-status-pill.status-en_attente {
        background: rgba(8, 145, 178, 0.14);
        border-color: rgba(8, 145, 178, 0.18);
        color: #0c7994;
    }

    .exam-status-pill.status-termine {
        background: rgba(15, 159, 119, 0.13);
        border-color: rgba(15, 159, 119, 0.18);
        color: #0f7e5f;
    }

    .exam-status-pill.status-annule,
    .exam-status-pill.status-default {
        background: rgba(100, 116, 139, 0.12);
        border-color: rgba(100, 116, 139, 0.18);
        color: #51606f;
    }

    .exam-pay-pill.pay-yes {
        background: rgba(15, 159, 119, 0.13);
        border-color: rgba(15, 159, 119, 0.18);
        color: #0f7e5f;
    }

    .exam-pay-pill.pay-no {
        background: rgba(203, 77, 88, 0.12);
        border-color: rgba(203, 77, 88, 0.18);
        color: #b43a46;
    }

    .exam-date-main,
    .exam-amount-main {
        color: var(--exam-text);
        font-size: .95rem;
        font-weight: 800;
    }

    .exam-amount-main {
        font-variant-numeric: tabular-nums;
    }

    .exam-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .exam-icon-btn {
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

    .exam-icon-btn:hover,
    .exam-icon-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
        background: #f7fbff;
        border-color: #bdd3ea;
    }

    .exam-icon-btn.view:hover,
    .exam-icon-btn.view:focus {
        color: var(--exam-success);
    }

    .exam-icon-btn.edit:hover,
    .exam-icon-btn.edit:focus {
        color: var(--exam-warning);
    }

    .exam-icon-btn.delete:hover,
    .exam-icon-btn.delete:focus {
        color: var(--exam-danger);
    }

    .exam-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid #dfe8f3;
        background: linear-gradient(180deg, rgba(245, 249, 252, 0.78) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .exam-empty {
        padding: 34px 18px;
        text-align: center;
    }

    .exam-empty-card {
        padding: 28px 20px;
        border: 1px dashed #d2dfed;
        border-radius: 22px;
        background: linear-gradient(180deg, #fcfdff 0%, #f7fbff 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.82);
    }

    .exam-empty-icon {
        width: 68px;
        height: 68px;
        margin: 0 auto 16px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(23, 96, 165, 0.1);
        color: var(--exam-primary);
        font-size: 1.6rem;
    }

    .exam-empty-title {
        margin: 0;
        color: var(--exam-text);
        font-size: 1.22rem;
        font-weight: 900;
    }

    .exam-empty-copy {
        margin: 10px auto 0;
        max-width: 54ch;
        color: var(--exam-muted);
        font-size: .95rem;
        line-height: 1.65;
        font-weight: 600;
    }

    html.dark body .exam-page,
    body.dark-mode .exam-page,
    body.theme-dark .exam-page {
        --exam-surface: linear-gradient(180deg, #152233 0%, #122032 100%);
        --exam-card: #162332;
        --exam-border: #2f4358;
        --exam-text: #e6edf6;
        --exam-muted: #9eb1c7;
    }

    html.dark body .exam-kpi,
    html.dark body .exam-filter-card,
    html.dark body .exam-table-card,
    html.dark body .exam-empty-card,
    body.dark-mode .exam-kpi,
    body.dark-mode .exam-filter-card,
    body.dark-mode .exam-table-card,
    body.dark-mode .exam-empty-card,
    body.theme-dark .exam-kpi,
    body.theme-dark .exam-filter-card,
    body.theme-dark .exam-table-card,
    body.theme-dark .exam-empty-card {
        background: rgba(17, 34, 54, 0.9);
        border-color: #35506a;
    }

    html.dark body .exam-badge,
    html.dark body .exam-chip,
    html.dark body .exam-inline-tag,
    html.dark body .exam-table-chip,
    body.dark-mode .exam-badge,
    body.dark-mode .exam-chip,
    body.dark-mode .exam-inline-tag,
    body.dark-mode .exam-table-chip,
    body.theme-dark .exam-badge,
    body.theme-dark .exam-chip,
    body.theme-dark .exam-inline-tag,
    body.theme-dark .exam-table-chip {
        background: #14273e;
        border-color: #305173;
        color: #cde2ff;
    }

    html.dark body .exam-btn.secondary,
    html.dark body .exam-filter-btn.soft,
    body.dark-mode .exam-btn.secondary,
    body.dark-mode .exam-filter-btn.soft,
    body.theme-dark .exam-btn.secondary,
    body.theme-dark .exam-filter-btn.soft {
        border-color: #365b7d;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
        color: #d2e6fb;
    }

    html.dark body .exam-btn.secondary:hover,
    html.dark body .exam-btn.secondary:focus,
    html.dark body .exam-filter-btn.soft:hover,
    html.dark body .exam-filter-btn.soft:focus,
    body.dark-mode .exam-btn.secondary:hover,
    body.dark-mode .exam-btn.secondary:focus,
    body.dark-mode .exam-filter-btn.soft:hover,
    body.dark-mode .exam-filter-btn.soft:focus,
    body.theme-dark .exam-btn.secondary:hover,
    body.theme-dark .exam-btn.secondary:focus,
    body.theme-dark .exam-filter-btn.soft:hover,
    body.theme-dark .exam-filter-btn.soft:focus {
        border-color: #4c7094;
        background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
        color: #ffffff;
    }

    html.dark body .exam-select,
    body.dark-mode .exam-search,
    body.dark-mode .exam-select,
    body.theme-dark .exam-search,
        border-color: #355985;
        color: #deebf9;
    }

    body.theme-dark .exam-search::placeholder {
        color: #98adc4;
    }
    body.theme-dark .exam-filter-btn.soft:focus {
    body.theme-dark .exam-search-wrap i {
        color: #98adc4;
    }

    html.dark body .exam-table-wrap,
    body.dark-mode .exam-table-wrap,
    body.theme-dark .exam-table-wrap {
        border-color: #29455f;
    }

    html.dark body .exam-table,
    body.dark-mode .exam-table,
    body.theme-dark .exam-table {
        background: #0f1a28;
    }

    html.dark body .exam-table thead th,
    body.dark-mode .exam-table thead th,
    body.theme-dark .exam-table thead th {
        background: #102337;
        border-color: #284660;
        color: #a8bfd7;
    }

    html.dark body .exam-table tbody td,
    body.dark-mode .exam-table tbody td,
    body.theme-dark .exam-table tbody td {
        border-bottom-color: #203c57;
    }

    html.dark body .exam-table tbody tr:hover,
    body.dark-mode .exam-table tbody tr:hover,
    body.theme-dark .exam-table tbody tr:hover {
        background: rgba(20, 43, 66, 0.86);
    }

    html.dark body .exam-icon-btn,
    body.dark-mode .exam-icon-btn,
    body.theme-dark .exam-icon-btn {
        background: #13263f;
        border-color: #35506a;
        color: #c8dbef;
    }

    html.dark body .exam-icon-btn:hover,
    html.dark body .exam-icon-btn:focus,
    body.dark-mode .exam-icon-btn:hover,
    body.dark-mode .exam-icon-btn:focus,
    body.theme-dark .exam-icon-btn:hover,
    body.theme-dark .exam-icon-btn:focus {
        background: #183556;
        border-color: #4f74a3;
    }

    @media (max-width: 1200px) {
        .exam-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .exam-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .exam-filter-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 992px) {
        .exam-hero-grid {
            grid-template-columns: 1fr;
        }

        .exam-hero-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .exam-table {
            min-width: 980px;
        }
    }

    @media (max-width: 767px) {
        .exam-page {
            padding-left: 0;
            padding-right: 0;
        }

        .exam-kpi-grid,
        .exam-filter-form {
            grid-template-columns: 1fr;
        }

        .exam-filter-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .exam-hero-actions {
            grid-template-columns: 1fr;
        }

        .exam-btn,
        .exam-filter-btn {
            width: 100%;
        }

        .exam-title-content {
            flex-direction: column;
            align-items: stretch;
        }

        .exam-filter-head,
        .exam-table-head,
        .exam-table-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .exam-active-filters,
        .exam-table-meta {
            justify-content: flex-start;
        }

        .exam-filter-card,
        .exam-table-card {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid exam-page">
    <div class="exam-shell">
        <section class="exam-hero">
            <div class="exam-hero-grid">
                <div>
                    <span class="exam-eyebrow">Flux laboratoire</span>
                    <div class="exam-title-row">
                        <span class="exam-title-icon">
                            <i class="fas fa-microscope"></i>
                        </span>
                        <div class="exam-title-content">
                            <div class="exam-title-copy">
                                <h1 class="exam-title">Liste des examens</h1>
                                <p class="exam-subtitle">Pilotez les demandes, le suivi d'avancement et les resultats d'examens dans une interface plus claire, plus fluide et coherente avec le reste du produit.</p>
                            </div>

                            <div class="exam-hero-actions">
                                <a href="{{ route('examens.export', request()->all()) }}" class="exam-btn secondary">
                                    <span class="exam-btn-icon"><i class="fas fa-file-export"></i></span>
                                    <span>Exporter CSV</span>
                                </a>
                                <a href="{{ route('examens.create') }}" class="exam-btn primary">
                                    <span class="exam-btn-icon"><i class="fas fa-plus"></i></span>
                                    <span>Nouvel examen</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="exam-badge-row">
                        <span class="exam-badge"><i class="fas fa-layer-group"></i>{{ $examensTotal }} examens</span>
                        <span class="exam-chip"><i class="fas fa-hourglass-half"></i>{{ $examensEnAttente ?? 0 }} en attente</span>
                        <span class="exam-chip"><i class="fas fa-vial-circle-check"></i>{{ $examensTermines ?? 0 }} termines</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="exam-kpi-grid">
            <article class="exam-kpi">
                <div class="exam-kpi-top">
                    <div>
                        <p class="exam-kpi-label">Total affiche</p>
                        <p class="exam-kpi-value">{{ $examensTotal }}</p>
                    </div>
                    <span class="exam-kpi-icon total"><i class="fas fa-layer-group"></i></span>
                </div>
                <p class="exam-kpi-copy">Volume total d'examens actuellement pris en compte dans la liste affichee.</p>
            </article>

            <article class="exam-kpi">
                <div class="exam-kpi-top">
                    <div>
                        <p class="exam-kpi-label">En attente</p>
                        <p class="exam-kpi-value">{{ $examensEnAttente ?? 0 }}</p>
                    </div>
                    <span class="exam-kpi-icon waiting"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <p class="exam-kpi-copy">Demandes a traiter rapidement pour garder un circuit d'examen fluide.</p>
            </article>

            <article class="exam-kpi">
                <div class="exam-kpi-top">
                    <div>
                        <p class="exam-kpi-label">En cours</p>
                        <p class="exam-kpi-value">{{ $inProgress }}</p>
                    </div>
                    <span class="exam-kpi-icon progress"><i class="fas fa-spinner"></i></span>
                </div>
                <p class="exam-kpi-copy">Examens deja lances, en attente de resultat ou de cloture du suivi.</p>
            </article>

            <article class="exam-kpi">
                <div class="exam-kpi-top">
                    <div>
                        <p class="exam-kpi-label">Termines</p>
                        <p class="exam-kpi-value">{{ $examensTermines ?? 0 }}</p>
                    </div>
                    <span class="exam-kpi-icon done"><i class="fas fa-circle-check"></i></span>
                </div>
                <p class="exam-kpi-copy">Examens clotures et prets a etre consultes ou rattaches au suivi du patient.</p>
            </article>
        </section>

        <section class="exam-filter-card">
            <div class="exam-filter-head">
                <div>
                    <p class="exam-filter-kicker">Recherche et ciblage</p>
                    <h2 class="exam-filter-title">Filtres examens</h2>
                    <p class="exam-filter-copy">Affinez la liste par texte libre, patient, statut et type avec une grille plus compacte et mieux alignee.</p>
                </div>

                @if($hasFilters)
                    <div class="exam-active-filters">
                        @if(request('search'))
                            <span class="exam-inline-tag"><i class="fas fa-search"></i>{{ request('search') }}</span>
                        @endif
                        @if($selectedPatientLabel)
                            <span class="exam-inline-tag"><i class="fas fa-user"></i>{{ $selectedPatientLabel }}</span>
                        @endif
                        @if($selectedStatus)
                            <span class="exam-inline-tag"><i class="fas fa-circle-info"></i>{{ ucfirst(str_replace('_', ' ', $selectedStatus)) }}</span>
                        @endif
                        @if($selectedType)
                            <span class="exam-inline-tag"><i class="fas fa-flask"></i>{{ ucfirst($selectedType) }}</span>
                        @endif
                    </div>
                @endif
            </div>

            <form method="GET" action="{{ route('examens.index') }}" class="exam-filter-form">
                <div class="exam-field">
                    <label for="examSearch">Recherche</label>
                    <div class="exam-search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="examSearch" name="search" class="exam-search" value="{{ request('search') }}" placeholder="Nom patient, email, description...">
                    </div>
                </div>

                <div class="exam-field">
                    <label for="examPatient">Patient</label>
                    <select id="examPatient" name="patient" class="exam-select">
                        <option value="">Tous les patients</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ (string) request('patient') === (string) $patient->id ? 'selected' : '' }}>
                                {{ trim(($patient->nom ?? '') . ' ' . ($patient->prenom ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="exam-field">
                    <label for="examStatus">Statut</label>
                    <select id="examStatus" name="statut" class="exam-select">
                        <option value="">Tous les statuts</option>
                        @foreach($statuts as $statut)
                            <option value="{{ $statut }}" {{ request('statut') === $statut ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $statut)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="exam-field">
                    <label for="examType">Type</label>
                    <select id="examType" name="type" class="exam-select">
                        <option value="">Tous les types</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="exam-filter-actions">
                    <button type="submit" class="exam-filter-btn primary">
                        <span class="exam-btn-icon"><i class="fas fa-filter"></i></span>
                        <span>Filtrer</span>
                    </button>

                    @if($hasFilters)
                        <a href="{{ route('examens.index') }}" class="exam-filter-btn soft">
                            <span class="exam-btn-icon"><i class="fas fa-rotate-left"></i></span>
                            <span>Reinitialiser</span>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="exam-table-card">
            <div class="exam-table-head">
                <div>
                    <p class="exam-table-kicker">Registre clinique</p>
                    <h2 class="exam-table-title">Liste des examens</h2>
                    <p class="exam-table-copy">Suivez patients, type d'examen, date, statut et paiement dans une presentation plus aerée et plus lisible.</p>
                </div>

                <div class="exam-table-meta">
                    <span class="exam-table-chip"><i class="fas fa-table-list"></i>{{ method_exists($examens, 'count') ? $examens->count() : count($examens ?? []) }} lignes</span>
                    <span class="exam-table-chip"><i class="fas fa-mobile-screen-button"></i>Scroll mobile</span>
                </div>
            </div>

            @if($examens && count($examens) > 0)
                <div class="exam-table-wrap">
                    <table class="exam-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Coût</th>
                                <th>Paiement</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examens as $examen)
                                <tr>
                                    <td>
                                        <div class="exam-patient-stack">
                                            <span class="exam-patient-name">{{ $examen->display_patient_name }}</span>
                                            <span class="exam-patient-meta">{{ $examen->patient->email ?? 'Email non renseigne' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="exam-type-pill">{{ $examen->type_examen ?? $examen->type ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="exam-date-stack">
                                            <span class="exam-date-main">{{ $examen->display_date_examen }}</span>
                                            <span class="exam-date-meta">{{ $examen->display_date_examen_human }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="exam-status-pill {{ $examen->display_status_class }}">{{ $examen->display_status_text }}</span>
                                    </td>
                                    <td>
                                        <div class="exam-amount-stack">
                                            <span class="exam-amount-main">{{ $examen->cout ? number_format($examen->cout, 2, ',', ' ') . ' DH' : '-' }}</span>
                                            <span class="exam-amount-meta">{{ $examen->payee ? 'Paiement confirme' : 'Paiement en attente' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($examen->payee)
                                            <span class="exam-pay-pill pay-yes"><i class="fas fa-check"></i>Payee</span>
                                        @else
                                            <span class="exam-pay-pill pay-no"><i class="fas fa-xmark"></i>Non payee</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="exam-actions">
                                            <a href="{{ route('examens.show', $examen->id) }}" class="exam-icon-btn view" title="Voir" aria-label="Voir examen {{ $examen->id }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('examens.edit', $examen->id) }}" class="exam-icon-btn edit" title="Modifier" aria-label="Modifier examen {{ $examen->id }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('examens.destroy', $examen->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet examen ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="exam-icon-btn delete" title="Supprimer" aria-label="Supprimer examen {{ $examen->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($examens, 'links'))
                    <div class="exam-table-footer">
                        <p class="exam-pagination-copy">Affichage de {{ $examens->firstItem() ?? 0 }} a {{ $examens->lastItem() ?? 0 }} sur {{ $examens->total() ?? count($examens ?? []) }} examens</p>
                        <div>{{ $examens->links() }}</div>
                    </div>
                @endif
            @else
                <div class="exam-empty">
                    <div class="exam-empty-card">
                        <div class="exam-empty-icon"><i class="fas fa-flask-vial"></i></div>
                        <h3 class="exam-empty-title">Aucun examen enregistre pour le moment</h3>
                        <p class="exam-empty-copy">Commencez par creer un examen pour organiser les demandes, suivre l'avancement et centraliser les resultats du cabinet.</p>
                        <a href="{{ route('examens.create') }}" class="exam-btn primary mt-3">
                            <span class="exam-btn-icon"><i class="fas fa-plus"></i></span>
                            <span>Creer un examen</span>
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
