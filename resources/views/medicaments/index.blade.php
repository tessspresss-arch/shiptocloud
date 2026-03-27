@extends('layouts.app')

@section('title', 'Gestion des Médicaments')

@push('styles')
<style>
    :root {
        --med-bg: linear-gradient(180deg, #f5f9ff 0%, #eef5ff 100%);
        --med-surface: rgba(255, 255, 255, 0.78);
        --med-card: #ffffff;
        --med-border: #d8e4f2;
        --med-border-strong: #c9d8ea;
        --med-text: #15314d;
        --med-muted: #5f7896;
        --med-primary: #2c7be5;
        --med-primary-strong: #1f5ea8;
        --med-accent: #0ea5e9;
        --med-success: #0f9f77;
        --med-warning: #d97706;
        --med-danger: #dc2626;
    }

    .med-page {
        width: 100%;
        max-width: none;
        padding: 10px 8px 92px;
    }

    .med-shell {
        display: grid;
        gap: 16px;
    }

    .med-hero,
    .med-section-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--med-border);
        border-radius: 22px;
        box-shadow: 0 24px 48px -38px rgba(20, 52, 84, 0.42);
    }

    .med-hero {
        padding: 18px;
        background:
            radial-gradient(circle at top right, rgba(44, 123, 229, 0.16) 0%, rgba(44, 123, 229, 0) 32%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
            var(--med-bg);
    }

    .med-hero::before,
    .med-section-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .med-hero > *,
    .med-section-card > * {
        position: relative;
        z-index: 1;
    }

    .med-head {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: end;
    }

    .med-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(44, 123, 229, 0.16);
        background: rgba(255, 255, 255, 0.62);
        color: var(--med-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .med-title-row {
        display: flex;
        gap: 14px;
        align-items: center;
        margin-top: 0;
        flex-wrap: wrap;
    }

    .med-title-row .med-eyebrow {
        flex-shrink: 0;
    }

    .med-title-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
        font-size: 1.25rem;
        background: linear-gradient(135deg, var(--med-primary) 0%, var(--med-primary-strong) 100%);
        box-shadow: 0 16px 26px -18px rgba(44, 123, 229, 0.58);
    }

    .med-title-block {
        min-width: 0;
    }

    .med-title-block.is-compact {
        display: none;
    }

    .med-title-block.is-compact .med-title,
    .med-title-block.is-compact .med-subtitle {
        display: none;
    }

    .med-title {
        margin: 0;
        font-size: clamp(1.45rem, 2.3vw, 2.05rem);
        font-weight: 800;
        line-height: 1.06;
        letter-spacing: -0.04em;
        color: var(--med-text);
    }

    .med-subtitle {
        margin: 8px 0 0;
        max-width: 74ch;
        color: var(--med-muted);
        font-size: .97rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .med-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .med-btn {
        min-height: 48px;
        border-radius: 16px;
        border: 1px solid transparent;
        padding: 0 16px;
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

    .med-btn:hover,
    .med-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .med-btn-icon {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(44, 123, 229, 0.1);
        color: var(--med-primary);
    }

    .med-btn-primary {
        background: linear-gradient(135deg, var(--med-primary) 0%, var(--med-primary-strong) 100%);
        color: #fff;
        box-shadow: 0 18px 28px -22px rgba(44, 123, 229, 0.55);
    }

    .med-btn-primary .med-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: inherit;
    }

    .med-btn-secondary {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #385674;
        box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
    }

    .med-btn-secondary:hover,
    .med-btn-secondary:focus {
        color: #1f6fa3;
        border-color: rgba(44, 123, 229, 0.3);
        background: linear-gradient(180deg, #ffffff 0%, #edf5fb 100%);
    }

    .med-section-card {
        background: rgba(255, 255, 255, 0.84);
        padding: 18px;
    }

    .med-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .med-section-title {
        margin: 0;
        font-size: 1.08rem;
        font-weight: 800;
        color: var(--med-text);
    }

    .med-section-copy {
        margin: 6px 0 0;
        color: var(--med-muted);
        font-size: .9rem;
        line-height: 1.55;
    }

    .med-section-meta {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .med-counter-badge {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d4e2f2;
        background: #f6fafe;
        color: #1d4f91;
        font-size: .78rem;
        font-weight: 800;
    }

    .med-filter-form {
        display: grid;
        grid-template-columns: minmax(220px, 1.8fr) repeat(4, minmax(130px, 1fr)) auto;
        gap: 12px;
        align-items: end;
    }

    .med-filter-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .med-filter-field label {
        font-size: .76rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--med-muted);
    }

    .med-search-field {
        position: relative;
    }

    .med-search-field i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #7290b0;
    }

    .med-filter-field .form-control,
    .med-filter-field .form-select {
        min-height: 52px;
        border-radius: 15px;
        border: 1px solid #d4e1ee;
        background: #fff;
        color: var(--med-text);
        padding: 13px 14px;
        font-size: .95rem;
        font-weight: 600;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.78), 0 10px 24px -28px rgba(15, 23, 42, 0.28);
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease, transform .2s ease;
    }

    .med-search-field .form-control {
        padding-left: 42px;
    }

    .med-filter-field .form-control:focus,
    .med-filter-field .form-select:focus {
        border-color: rgba(44, 123, 229, 0.46);
        box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.12), 0 14px 28px -26px rgba(31, 111, 163, 0.34);
        transform: translateY(-1px);
    }

    .med-filter-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
        height: 100%;
    }

    .med-filter-submit,
    .med-filter-reset {
        min-height: 52px;
        border-radius: 15px;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: .9rem;
        font-weight: 800;
        text-decoration: none;
        border: 1px solid transparent;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .med-filter-submit:hover,
    .med-filter-reset:hover,
    .med-filter-submit:focus,
    .med-filter-reset:focus {
        transform: translateY(-1px);
    }

    .med-filter-submit {
        color: #fff;
        background: linear-gradient(135deg, var(--med-primary) 0%, var(--med-primary-strong) 100%);
        box-shadow: 0 18px 28px -22px rgba(44, 123, 229, 0.55);
        border: 0;
    }

    .med-filter-reset {
        color: #385674;
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
    }

    .med-stats {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 12px;
    }

    .med-stat {
        display: grid;
        gap: 12px;
        min-height: 156px;
        padding: 16px;
        border: 1px solid var(--med-border);
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 18px 28px -30px rgba(15, 23, 42, 0.28);
    }

    .med-stat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .med-stat-label {
        margin: 0;
        color: var(--med-muted);
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 800;
    }

    .med-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.05rem;
    }

    .med-stat-value {
        margin: 0;
        color: var(--med-text);
        font-size: clamp(1.4rem, 2vw, 1.8rem);
        font-weight: 900;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .med-stat-meta {
        margin: 0;
        color: var(--med-muted);
        font-size: .84rem;
        line-height: 1.5;
        font-weight: 600;
    }

    .med-stat-total .med-stat-icon {
        background: rgba(44, 123, 229, 0.12);
        color: var(--med-primary);
    }

    .med-stat-active .med-stat-icon {
        background: rgba(15, 159, 119, 0.14);
        color: var(--med-success);
    }

    .med-stat-low .med-stat-icon {
        background: rgba(217, 119, 6, 0.14);
        color: var(--med-warning);
    }

    .med-stat-expired .med-stat-icon {
        background: rgba(220, 38, 38, 0.14);
        color: var(--med-danger);
    }

    .med-stat-soon .med-stat-icon {
        background: rgba(244, 63, 94, 0.14);
        color: #e11d48;
    }

    .med-stat-value-stock .med-stat-icon {
        background: rgba(71, 85, 105, 0.12);
        color: #475569;
    }

    .med-results-card {
        padding-bottom: 0;
    }

    .med-table-wrap {
        border: 1px solid var(--med-border);
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
    }

    .med-table {
        margin: 0;
    }

    .med-table thead th {
        padding: 14px 16px;
        background: linear-gradient(180deg, #f7fbff 0%, #edf5fd 100%);
        border-bottom: 1px solid var(--med-border);
        color: var(--med-primary-strong);
        font-weight: 800;
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        white-space: nowrap;
    }

    .med-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-color: #edf2f7;
        color: var(--med-text);
    }

    .med-table tbody tr {
        transition: background .2s ease;
    }

    .med-table tbody tr:hover {
        background: #f8fbff;
    }

    .med-sort-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: inherit;
        text-decoration: none;
    }

    .med-sort-link:hover,
    .med-sort-link:focus {
        color: var(--med-primary);
    }

    .med-name {
        display: grid;
        gap: 4px;
    }

    .med-name strong {
        font-size: .98rem;
    }

    .med-subtext {
        color: var(--med-muted);
        font-size: .82rem;
        line-height: 1.45;
        font-weight: 600;
    }

    .med-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        border-radius: 999px;
        padding: 0 12px;
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .pill-blue { background: #dbeafe; color: #1d4ed8; }
    .pill-green { background: #dcfce7; color: #166534; }
    .pill-red { background: #fee2e2; color: #991b1b; }
    .pill-amber { background: #fef3c7; color: #92400e; }
    .pill-slate { background: #e2e8f0; color: #334155; }

    .med-actions-inline {
        display: inline-flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .med-mini-btn {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #fff !important;
        transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
    }

    .med-mini-btn:hover,
    .med-mini-btn:focus {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .btn-view { background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); }
    .btn-edit { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
    .btn-add { background: linear-gradient(135deg, #16a34a 0%, #0f9f77 100%); }
    .btn-remove { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .med-empty-cell {
        padding: 28px !important;
        background: linear-gradient(180deg, #fbfdff 0%, #f7fbff 100%);
    }

    .med-empty-state {
        display: grid;
        gap: 10px;
        place-items: center;
        text-align: center;
        color: var(--med-muted);
    }

    .med-empty-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: var(--med-primary);
        background: rgba(44, 123, 229, 0.1);
    }

    .med-empty-state h3 {
        margin: 0;
        color: var(--med-text);
        font-size: 1.05rem;
        font-weight: 800;
    }

    .med-empty-state p {
        margin: 0;
        max-width: 48ch;
        font-size: .9rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .med-pagination-wrap {
        padding-top: 16px;
    }

    body.dark-mode .med-page,
    body.theme-dark .med-page {
        --med-bg: linear-gradient(180deg, #152233 0%, #122032 100%);
        --med-surface: rgba(18, 35, 52, 0.78);
        --med-card: #162332;
        --med-border: #2f4358;
        --med-border-strong: #35506a;
        --med-text: #e6edf6;
        --med-muted: #9eb1c7;
    }

    body.dark-mode .med-eyebrow,
    body.dark-mode .med-section-card,
    body.dark-mode .med-stat,
    body.dark-mode .med-table-wrap,
    body.theme-dark .med-eyebrow,
    body.theme-dark .med-section-card,
    body.theme-dark .med-stat,
    body.theme-dark .med-table-wrap {
        background: rgba(17, 34, 54, 0.88);
        border-color: var(--med-border-strong);
    }

    body.dark-mode .med-btn-secondary,
    body.theme-dark .med-btn-secondary,
    body.dark-mode .med-filter-reset,
    body.theme-dark .med-filter-reset {
        border-color: #365b7d;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
        color: #d2e6fb;
    }

    body.dark-mode .med-btn-secondary:hover,
    body.dark-mode .med-btn-secondary:focus,
    body.theme-dark .med-btn-secondary:hover,
    body.theme-dark .med-btn-secondary:focus,
    body.dark-mode .med-filter-reset:hover,
    body.dark-mode .med-filter-reset:focus,
    body.theme-dark .med-filter-reset:hover,
    body.theme-dark .med-filter-reset:focus {
        border-color: #4c7094;
        background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
        color: #ffffff;
    }

    body.dark-mode .med-btn-icon,
    body.dark-mode .med-title-icon,
    body.dark-mode .med-stat-icon,
    body.dark-mode .med-empty-icon,
    body.theme-dark .med-btn-icon,
    body.theme-dark .med-title-icon,
    body.theme-dark .med-stat-icon,
    body.theme-dark .med-empty-icon {
        box-shadow: none;
    }

    body.dark-mode .med-counter-badge,
    body.theme-dark .med-counter-badge {
        border-color: #365b7d;
        background: rgba(30, 58, 94, 0.72);
        color: #d6e6f9;
    }

    body.dark-mode .med-filter-field .form-control,
    body.dark-mode .med-filter-field .form-select,
    body.theme-dark .med-filter-field .form-control,
    body.theme-dark .med-filter-field .form-select {
        background: #13263f;
        border-color: #355985;
        color: #deebf9;
    }

    body.dark-mode .med-filter-field .form-control::placeholder,
    body.theme-dark .med-filter-field .form-control::placeholder {
        color: #9eb1c7;
    }

    body.dark-mode .med-table thead th,
    body.theme-dark .med-table thead th {
        background: #16273d;
        border-bottom-color: #294055;
        color: #dceafe;
    }

    body.dark-mode .med-table tbody td,
    body.theme-dark .med-table tbody td,
    body.dark-mode .med-sort-link,
    body.theme-dark .med-sort-link,
    body.dark-mode .med-empty-state h3,
    body.theme-dark .med-empty-state h3 {
        color: var(--med-text);
    }

    body.dark-mode .med-table tbody tr:hover,
    body.theme-dark .med-table tbody tr:hover {
        background: #183455;
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

    body.dark-mode .modal-content,
    body.theme-dark .modal-content {
        background: #102136;
        border: 1px solid #355273;
        color: #e7f0ff;
    }

    body.dark-mode .modal-header,
    body.dark-mode .modal-footer,
    body.theme-dark .modal-header,
    body.theme-dark .modal-footer {
        border-color: #2e4a67;
    }

    body.dark-mode .modal-content .form-control,
    body.theme-dark .modal-content .form-control {
        background: #0d1a2b;
        border-color: #3a5b80;
        color: #e5efff;
    }

    body.dark-mode .modal-content .form-label,
    body.theme-dark .modal-content .form-label {
        color: #dceafe;
    }

    @media (max-width: 1399.98px) {
        .med-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .med-filter-form {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .med-filter-search {
            grid-column: 1 / -1;
        }

        .med-filter-actions {
            justify-content: stretch;
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 991.98px) {
        .med-page {
            padding: 6px 0 88px;
        }

        .med-head,
        .med-section-head {
            grid-template-columns: 1fr;
            display: grid;
            align-items: stretch;
        }

        .med-actions,
        .med-section-meta {
            justify-content: flex-start;
        }

        .med-actions {
            width: 100%;
        }

        .med-btn {
            flex: 1 1 calc(50% - 5px);
        }

        .med-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .med-hero,
        .med-section-card {
            border-radius: 18px;
            padding: 14px;
        }

        .med-title-row {
            align-items: flex-start;
        }

        .med-title-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .med-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .med-btn {
            width: 100%;
        }

        .med-filter-form,
        .med-stats {
            grid-template-columns: 1fr;
        }

        .med-filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .med-filter-submit,
        .med-filter-reset {
            width: 100%;
        }

        .med-table {
            min-width: 1080px;
        }
    }

    @media (max-width: 479.98px) {
        .med-actions,
        .med-filter-actions {
            grid-template-columns: 1fr;
        }

        .med-stat {
            min-height: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="med-page">
    <div class="med-shell">
        <header class="med-hero">
            <div class="med-head">
                <div>
                    <div class="med-title-row">
                        <span class="med-title-icon" aria-hidden="true"><i class="fas fa-pills"></i></span>
                        <span class="med-eyebrow">Pharmacie</span>
                        <div class="med-title-block is-compact">
                            <h1 class="med-title">Gestion des médicaments</h1>
                            <p class="med-subtitle">Centralisez le catalogue, les niveaux de stock, les alertes de péremption et les actions métier dans une interface plus lisible, plus cohérente et mieux équilibrée.</p>
                        </div>
                    </div>
                </div>

                <div class="med-actions">
                    <a href="{{ route('medicaments.create') }}" class="med-btn med-btn-primary">
                        <span class="med-btn-icon"><i class="fas fa-plus"></i></span>
                        <span>Nouveau médicament</span>
                    </a>
                    <a href="{{ route('medicaments.reports') }}" class="med-btn med-btn-secondary">
                        <span class="med-btn-icon"><i class="fas fa-chart-bar"></i></span>
                        <span>Rapports</span>
                    </a>
                </div>
            </div>
        </header>

        <section class="med-section-card med-filter-card">
            <div class="med-section-head">
                <div>
                    <h2 class="med-section-title">Recherche et filtres</h2>
                    <p class="med-section-copy">Affinez rapidement le catalogue par nom, catégorie, type, statut ou état de stock avec une barre de filtres plus lisible et mieux alignée.</p>
                </div>
                <div class="med-section-meta">
                    @if($activeFilterCount > 0)
                        <span class="med-counter-badge">{{ $activeFilterCount }} filtre{{ $activeFilterCount > 1 ? 's' : '' }} actif{{ $activeFilterCount > 1 ? 's' : '' }}</span>
                    @endif
                    <span class="med-counter-badge">{{ $resultCount }} résultat{{ $resultCount > 1 ? 's' : '' }}</span>
                </div>
            </div>

            <form method="GET" action="{{ route('medicaments.index') }}" class="med-filter-form">
                <div class="med-filter-field med-filter-search">
                    <label for="med-search">Recherche</label>
                    <div class="med-search-field">
                        <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                        <input id="med-search" type="text" name="search" class="form-control" placeholder="Nom commercial, DCI, code CIP..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="med-filter-field">
                    <label for="med-categorie">Catégorie</label>
                    <select id="med-categorie" name="categorie" class="form-select">
                        <option value="">Toutes catégories</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie }}" {{ request('categorie') == $categorie ? 'selected' : '' }}>{{ $categorie }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="med-filter-field">
                    <label for="med-type">Type</label>
                    <select id="med-type" name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="prescription" {{ request('type') == 'prescription' ? 'selected' : '' }}>Prescription</option>
                        <option value="otc" {{ request('type') == 'otc' ? 'selected' : '' }}>OTC</option>
                        <option value="controlled" {{ request('type') == 'controlled' ? 'selected' : '' }}>Contrôlé</option>
                    </select>
                </div>

                <div class="med-filter-field">
                    <label for="med-statut">Statut</label>
                    <select id="med-statut" name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                        <option value="rupture" {{ request('statut') == 'rupture' ? 'selected' : '' }}>Rupture</option>
                        <option value="expired" {{ request('statut') == 'expired' ? 'selected' : '' }}>Expiré</option>
                    </select>
                </div>

                <div class="med-filter-field">
                    <label for="med-stock-status">Stock</label>
                    <select id="med-stock-status" name="stock_status" class="form-select">
                        <option value="">Tous les stocks</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stock faible</option>
                        <option value="expired" {{ request('stock_status') == 'expired' ? 'selected' : '' }}>Expirés</option>
                        <option value="expiring_soon" {{ request('stock_status') == 'expiring_soon' ? 'selected' : '' }}>Expire bientôt</option>
                    </select>
                </div>

                <div class="med-filter-actions">
                    <button type="submit" class="med-filter-submit" aria-label="Appliquer les filtres">
                        <i class="fas fa-search"></i>
                        <span>Rechercher</span>
                    </button>
                    <a href="{{ route('medicaments.index') }}" class="med-filter-reset" title="Réinitialiser les filtres">
                        <i class="fas fa-rotate-left"></i>
                        <span>Réinitialiser</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="med-stats" aria-label="Indicateurs du module médicaments">
            <article class="med-stat med-stat-total">
                <div class="med-stat-head">
                    <p class="med-stat-label">Total</p>
                    <span class="med-stat-icon" aria-hidden="true"><i class="fas fa-pills"></i></span>
                </div>
                <p class="med-stat-value">{{ $stats['total'] }}</p>
                <p class="med-stat-meta">Références actuellement suivies dans le catalogue.</p>
            </article>

            <article class="med-stat med-stat-active">
                <div class="med-stat-head">
                    <p class="med-stat-label">Actifs</p>
                    <span class="med-stat-icon" aria-hidden="true"><i class="fas fa-circle-check"></i></span>
                </div>
                <p class="med-stat-value">{{ $stats['actifs'] }}</p>
                <p class="med-stat-meta">Médicaments disponibles et activés pour l’usage courant.</p>
            </article>

            <article class="med-stat med-stat-low">
                <div class="med-stat-head">
                    <p class="med-stat-label">Stock faible</p>
                    <span class="med-stat-icon" aria-hidden="true"><i class="fas fa-triangle-exclamation"></i></span>
                </div>
                <p class="med-stat-value">{{ $stats['stock_faible'] }}</p>
                <p class="med-stat-meta">Références sous le seuil de surveillance défini.</p>
            </article>

            <article class="med-stat med-stat-expired">
                <div class="med-stat-head">
                    <p class="med-stat-label">Expirés</p>
                    <span class="med-stat-icon" aria-hidden="true"><i class="fas fa-calendar-xmark"></i></span>
                </div>
                <p class="med-stat-value">{{ $stats['expires'] }}</p>
                <p class="med-stat-meta">Produits arrivés à péremption et à traiter en priorité.</p>
            </article>

            <article class="med-stat med-stat-soon">
                <div class="med-stat-head">
                    <p class="med-stat-label">Expire bientôt</p>
                    <span class="med-stat-icon" aria-hidden="true"><i class="fas fa-clock"></i></span>
                </div>
                <p class="med-stat-value">{{ $stats['expire_bientot'] }}</p>
                <p class="med-stat-meta">Médicaments à surveiller sur l’horizon court.</p>
            </article>

            <article class="med-stat med-stat-value-stock">
                <div class="med-stat-head">
                    <p class="med-stat-label">Valeur stock</p>
                    <span class="med-stat-icon" aria-hidden="true"><i class="fas fa-money-bill-wave"></i></span>
                </div>
                <p class="med-stat-value">{{ number_format($stats['valeur_stock'], 2) }} DH</p>
                <p class="med-stat-meta">Valorisation globale du stock de médicaments.</p>
            </article>
        </section>

        <section class="med-section-card med-results-card">
            <div class="med-section-head">
                <div>
                    <h2 class="med-section-title">Catalogue des médicaments</h2>
                    <p class="med-section-copy">Consultez les niveaux de stock, les prix, les dates de péremption et agissez rapidement sur chaque référence depuis une table plus lisible.</p>
                </div>
                <div class="med-section-meta">
                    <span class="med-counter-badge">{{ $medicaments->count() }} affiché{{ $medicaments->count() > 1 ? 's' : '' }}</span>
                </div>
            </div>

            <div class="med-table-wrap table-responsive">
                <table class="table med-table align-middle text-nowrap">
                    <thead>
                        <tr>
                            <th>
                                <a class="med-sort-link" href="{{ route('medicaments.index', array_merge(request()->query(), ['sort_by' => 'nom_commercial', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    <span>Nom commercial</span>
                                    @if(request('sort_by') == 'nom_commercial')
                                        <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Code CIP</th>
                            <th>Catégorie</th>
                            <th>Type</th>
                            <th>Stock</th>
                            <th>Prix</th>
                            <th>Péremption</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicaments as $medicament)
                            <tr>
                                <td>
                                    <div class="med-name">
                                        <strong>{{ $medicament->nom_commercial }}</strong>
                                        @if($medicament->dci)
                                            <span class="med-subtext">{{ $medicament->dci }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $medicament->code_cip }}</td>
                                <td>{{ $medicament->categorie }}</td>
                                <td>
                                    @switch($medicament->type)
                                        @case('prescription')
                                            <span class="med-pill pill-blue">Prescription</span>
                                            @break
                                        @case('otc')
                                            <span class="med-pill pill-green">OTC</span>
                                            @break
                                        @case('controlled')
                                            <span class="med-pill pill-red">Contrôlé</span>
                                            @break
                                        @default
                                            <span class="med-pill pill-slate">-</span>
                                    @endswitch
                                </td>
                                <td>
                                    <span class="med-pill {{ $medicament->display_stock_class }}">{{ $medicament->quantite_stock }}</span>
                                    @if($medicament->quantite_seuil > 0)
                                        <div class="med-subtext">Seuil: {{ $medicament->quantite_seuil }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="med-name">
                                        <strong>{{ number_format($medicament->prix_vente, 2) }} DH</strong>
                                        @if($medicament->prix_remboursement)
                                            <span class="med-subtext">Remb.: {{ number_format($medicament->prix_remboursement, 2) }} DH</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($medicament->date_peremption)
                                        <span class="med-pill {{ $medicament->display_expiration_class }}">{{ $medicament->date_peremption->format('d/m/Y') }}</span>
                                        @if($medicament->jours_restants !== null && $medicament->jours_restants <= 30)
                                            <div class="med-subtext">{{ $medicament->jours_restants }} jour{{ $medicament->jours_restants > 1 ? 's' : '' }} restants</div>
                                        @endif
                                    @else
                                        <span class="med-subtext">Non renseignée</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($medicament->statut)
                                        @case('actif')
                                            <span class="med-pill pill-green">Actif</span>
                                            @break
                                        @case('inactif')
                                            <span class="med-pill pill-slate">Inactif</span>
                                            @break
                                        @case('rupture')
                                            <span class="med-pill pill-red">Rupture</span>
                                            @break
                                        @case('expired')
                                            <span class="med-pill pill-red">Expiré</span>
                                            @break
                                        @default
                                            <span class="med-pill pill-slate">-</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="med-actions-inline">
                                        <a href="{{ route('medicaments.show', $medicament) }}" class="med-mini-btn btn-view" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('medicaments.edit', $medicament) }}" class="med-mini-btn btn-edit" title="Modifier">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <button type="button" class="med-mini-btn btn-add" title="Ajouter du stock" onclick="openStockModal({{ $medicament->id }}, 'add')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="med-mini-btn btn-remove" title="Retirer du stock" onclick="openStockModal({{ $medicament->id }}, 'remove')">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="med-empty-cell">
                                    <div class="med-empty-state">
                                        <span class="med-empty-icon" aria-hidden="true"><i class="fas fa-capsules"></i></span>
                                        <h3>Aucun médicament trouvé</h3>
                                        <p>Aucun résultat ne correspond aux critères actuels. Ajustez les filtres ou créez une nouvelle référence pour enrichir le catalogue.</p>
                                        <a href="{{ route('medicaments.create') }}" class="med-btn med-btn-primary">
                                            <span class="med-btn-icon"><i class="fas fa-plus"></i></span>
                                            <span>Nouveau médicament</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($medicaments->hasPages())
                <div class="med-pagination-wrap">
                    {{ $medicaments->appends(request()->query())->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

<div class="modal fade" id="stockModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockModalTitle">Gérer le stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stockForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quantite" class="form-label">Quantité</label>
                        <input type="number" class="form-control" id="quantite" name="quantite" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="motif" class="form-label">Motif</label>
                        <input type="text" class="form-control" id="motif" name="motif" placeholder="Motif du mouvement">
                    </div>
                    <div class="mb-0">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" class="form-control" id="reference" name="reference" placeholder="Numéro de commande, etc.">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="stockSubmitBtn">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stockModalElement = document.getElementById('stockModal');
    const stockForm = document.getElementById('stockForm');
    const stockModalTitle = document.getElementById('stockModalTitle');
    const stockSubmitBtn = document.getElementById('stockSubmitBtn');
    const quantiteInput = document.getElementById('quantite');
    const motifInput = document.getElementById('motif');
    const referenceInput = document.getElementById('reference');
    const stockModal = stockModalElement && window.bootstrap ? new window.bootstrap.Modal(stockModalElement) : null;

    window.openStockModal = function (medicamentId, action) {
        if (!stockModal || !stockForm || !stockModalTitle || !stockSubmitBtn) {
            return;
        }

        stockForm.action = `/medicaments/${medicamentId}/stock`;

        const existingAction = stockForm.querySelector('input[name="action"]');
        if (existingAction) {
            existingAction.remove();
        }

        const hiddenActionInput = document.createElement('input');
        hiddenActionInput.type = 'hidden';
        hiddenActionInput.name = 'action';
        hiddenActionInput.value = action === 'add' ? 'add' : 'remove';
        stockForm.appendChild(hiddenActionInput);

        stockSubmitBtn.classList.remove('btn-success', 'btn-danger');

        if (action === 'add') {
            stockModalTitle.textContent = 'Ajouter du stock';
            stockSubmitBtn.classList.add('btn-success');
            stockSubmitBtn.textContent = 'Ajouter';
        } else {
            stockModalTitle.textContent = 'Retirer du stock';
            stockSubmitBtn.classList.add('btn-danger');
            stockSubmitBtn.textContent = 'Retirer';
        }

        if (quantiteInput) quantiteInput.value = '';
        if (motifInput) motifInput.value = '';
        if (referenceInput) referenceInput.value = '';

        stockModal.show();
    };

    stockModalElement?.addEventListener('hidden.bs.modal', function () {
        const existingAction = stockForm?.querySelector('input[name="action"]');
        if (existingAction) {
            existingAction.remove();
        }
    });
});
</script>
@endpush
