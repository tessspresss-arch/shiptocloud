@extends('layouts.app')

@section('title', 'Resultats des examens')
@section('topbar_subtitle', 'Visualisez et gerez les resultats d examens de vos patients.')

@push('styles')
<style>
    .exam-results-page {
        --exr-primary: #1760a5;
        --exr-primary-strong: #0f4c84;
        --exr-accent: #0ea5e9;
        --exr-success: #0f9f77;
        --exr-warning: #c57d10;
        --exr-danger: #cb4d58;
        --exr-muted: #5f7896;
        --exr-text: #15314d;
        --exr-border: #d8e4f1;
        --exr-card: #ffffff;
        --exr-surface: linear-gradient(180deg, #f5f9fd 0%, #eef5fb 100%);
        width: 100%;
        max-width: none;
        padding: 10px 8px 36px;
    }

    .exam-results-shell {
        display: grid;
        gap: 18px;
    }

    .exam-results-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--exr-border);
        border-radius: 28px;
        padding: 22px;
        background:
            radial-gradient(circle at top right, rgba(23, 96, 165, 0.18) 0%, rgba(23, 96, 165, 0) 30%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
            var(--exr-surface);
        box-shadow: 0 28px 48px -40px rgba(20, 52, 84, 0.42);
    }

    .exam-results-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .exam-results-hero > * {
        position: relative;
        z-index: 1;
    }

    .exam-results-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(300px, 0.92fr);
        gap: 18px;
        align-items: start;
    }

    .exam-results-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(23, 96, 165, 0.1);
        color: var(--exr-primary);
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .exam-results-title-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-top: 14px;
    }

    .exam-results-title-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
        font-size: 1.35rem;
        background: linear-gradient(135deg, var(--exr-primary) 0%, var(--exr-primary-strong) 100%);
        box-shadow: 0 16px 28px -18px rgba(23, 96, 165, 0.58);
    }

    .exam-results-title {
        margin: 0;
        color: var(--exr-text);
        font-size: clamp(1.6rem, 2.8vw, 2.25rem);
        line-height: 1.04;
        letter-spacing: -0.04em;
        font-weight: 900;
    }

    .exam-results-subtitle {
        margin: 10px 0 0;
        max-width: 72ch;
        color: var(--exr-muted);
        font-size: .98rem;
        line-height: 1.65;
        font-weight: 600;
    }

    .exam-results-badge-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .exam-results-badge,
    .exam-results-chip,
    .exam-results-table-chip,
    .exam-results-inline-tag {
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

    .exam-results-badge {
        background: linear-gradient(135deg, rgba(23, 96, 165, 0.12) 0%, rgba(14, 165, 233, 0.1) 100%);
        border-color: rgba(23, 96, 165, 0.2);
    }

    .exam-results-action-card {
        margin-top: 18px;
        padding: 16px;
        border-radius: 20px;
        border: 1px solid rgba(208, 221, 237, 0.96);
        background: rgba(255, 255, 255, 0.8);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.75);
    }

    .exam-results-action-label,
    .exam-results-side-label,
    .exam-results-filter-kicker,
    .exam-results-table-kicker {
        margin: 0 0 10px;
        color: var(--exr-muted);
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .exam-results-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .exam-results-btn,
    .exam-results-filter-btn,
    .exam-results-icon-btn {
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

    .exam-results-btn:hover,
    .exam-results-btn:focus,
    .exam-results-filter-btn:hover,
    .exam-results-filter-btn:focus,
    .exam-results-icon-btn:hover,
    .exam-results-icon-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .exam-results-btn.secondary,
    .exam-results-filter-btn.soft {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #385674;
        box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
    }

    .exam-results-btn.secondary:hover,
    .exam-results-btn.secondary:focus,
    .exam-results-filter-btn.soft:hover,
    .exam-results-filter-btn.soft:focus {
        color: #1f6fa3;
        border-color: rgba(23, 96, 165, 0.28);
        background: linear-gradient(180deg, #ffffff 0%, #edf5fb 100%);
    }

    .exam-results-btn.primary,
    .exam-results-filter-btn.primary {
        color: #ffffff;
        background: linear-gradient(135deg, var(--exr-primary) 0%, var(--exr-primary-strong) 100%);
        box-shadow: 0 18px 30px -22px rgba(23, 96, 165, 0.58);
    }

    .exam-results-btn.primary:hover,
    .exam-results-btn.primary:focus,
    .exam-results-filter-btn.primary:hover,
    .exam-results-filter-btn.primary:focus {
        color: #ffffff;
    }

    .exam-results-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(23, 96, 165, 0.1);
        color: var(--exr-primary);
    }

    .exam-results-btn.primary .exam-results-btn-icon,
    .exam-results-filter-btn.primary .exam-results-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: inherit;
    }

    .exam-results-side-card,
    .exam-results-kpi,
    .exam-results-filter-card,
    .exam-results-table-card {
        background: var(--exr-card);
        border: 1px solid var(--exr-border);
        border-radius: 24px;
        box-shadow: 0 24px 36px -34px rgba(15, 23, 42, 0.4);
    }

    .exam-results-side-card {
        padding: 18px;
        display: grid;
        gap: 18px;
    }

    .exam-results-side-value {
        margin: 0;
        color: var(--exr-text);
        font-size: clamp(2rem, 3.3vw, 2.5rem);
        line-height: 1;
        font-weight: 900;
        letter-spacing: -.05em;
    }

    .exam-results-side-copy {
        margin: 10px 0 0;
        color: var(--exr-muted);
        font-size: .93rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .exam-results-side-metrics {
        display: grid;
        gap: 10px;
    }

    .exam-results-side-metric {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid #dce8f4;
        background: linear-gradient(180deg, #fbfdff 0%, #f6fafe 100%);
    }

    .exam-results-side-metric span {
        color: var(--exr-muted);
        font-size: .86rem;
        font-weight: 700;
    }

    .exam-results-side-metric strong {
        color: var(--exr-text);
        font-size: 1rem;
        font-weight: 900;
    }

    .exam-results-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .exam-results-kpi {
        padding: 18px;
        display: grid;
        gap: 14px;
    }

    .exam-results-kpi-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .exam-results-kpi-label {
        margin: 0;
        color: var(--exr-muted);
        font-size: .82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .exam-results-kpi-value {
        margin: 8px 0 0;
        color: var(--exr-text);
        font-size: clamp(1.5rem, 2.5vw, 2rem);
        line-height: 1;
        font-weight: 900;
        letter-spacing: -.04em;
    }

    .exam-results-kpi-copy {
        margin: 0;
        color: var(--exr-muted);
        font-size: .9rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .exam-results-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        color: var(--exr-primary);
        background: linear-gradient(180deg, #e8f3fc 0%, #dbeefe 100%);
    }

    .exam-results-kpi-icon.pending {
        color: var(--exr-warning);
        background: linear-gradient(180deg, #fef3df 0%, #fde9bf 100%);
    }

    .exam-results-kpi-icon.progress {
        color: var(--exr-accent);
        background: linear-gradient(180deg, #e6f7fe 0%, #d1f0fd 100%);
    }

    .exam-results-kpi-icon.done {
        color: var(--exr-success);
        background: linear-gradient(180deg, #e3f7f0 0%, #cff1e5 100%);
    }

    .exam-results-filter-card,
    .exam-results-table-card {
        padding: 20px;
    }

    .exam-results-filter-head,
    .exam-results-table-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .exam-results-filter-title,
    .exam-results-table-title {
        margin: 0;
        color: var(--exr-text);
        font-size: 1.22rem;
        font-weight: 900;
        letter-spacing: -.03em;
    }

    .exam-results-filter-copy,
    .exam-results-table-copy {
        margin: 8px 0 0;
        color: var(--exr-muted);
        font-size: .92rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .exam-results-active-filters,
    .exam-results-table-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .exam-results-filter-form {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-top: 18px;
    }

    .exam-results-field {
        display: grid;
        gap: 8px;
    }

    .exam-results-field label {
        color: var(--exr-text);
        font-size: .85rem;
        font-weight: 800;
    }

    .exam-results-select,
    .exam-results-input {
        width: 100%;
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid #d5e3f1;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        color: var(--exr-text);
        font-size: .92rem;
        font-weight: 600;
        padding: 0 14px;
        outline: none;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .exam-results-select:focus,
    .exam-results-input:focus {
        border-color: rgba(23, 96, 165, 0.38);
        box-shadow: 0 0 0 4px rgba(23, 96, 165, 0.08);
        background: #ffffff;
    }

    .exam-results-filter-actions {
        grid-column: 1 / -1;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        padding-top: 4px;
    }

    .exam-results-table-wrap {
        margin-top: 18px;
        overflow-x: auto;
        border-radius: 20px;
        border: 1px solid #dbe7f2;
    }

    .exam-results-table {
        width: 100%;
        min-width: 1060px;
        border-collapse: separate;
        border-spacing: 0;
        background: #ffffff;
    }

    .exam-results-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: linear-gradient(180deg, #f8fbff 0%, #f0f6fc 100%);
        color: var(--exr-muted);
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: 16px 18px;
        border-bottom: 1px solid #dce7f1;
    }

    .exam-results-table tbody td {
        padding: 18px;
        vertical-align: top;
        border-bottom: 1px solid #edf3f8;
    }

    .exam-results-table tbody tr {
        transition: background .2s ease, transform .2s ease;
    }

    .exam-results-table tbody tr:hover {
        background: #f8fbff;
    }

    .exam-results-mobile-label {
        display: none;
        color: var(--exr-muted);
        font-size: .74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 6px;
    }

    .exam-results-patient-name,
    .exam-results-date-main,
    .exam-results-result-main {
        display: block;
        color: var(--exr-text);
        font-size: .96rem;
        font-weight: 900;
        line-height: 1.45;
    }

    .exam-results-patient-meta,
    .exam-results-date-meta,
    .exam-results-result-meta,
    .exam-results-exam-meta {
        display: block;
        margin-top: 4px;
        color: var(--exr-muted);
        font-size: .84rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .exam-results-type-pill,
    .exam-results-status-pill,
    .exam-results-result-pill {
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

    .exam-results-type-pill {
        color: #1a4d86;
        background: linear-gradient(180deg, #edf6ff 0%, #e4f0fb 100%);
        border-color: #d5e5f6;
    }

    .exam-results-status-pill.demande {
        color: var(--exr-warning);
        background: #fef4e3;
        border-color: #fde1ac;
    }

    .exam-results-status-pill.en_attente {
        color: #0a7fb0;
        background: #e6f8ff;
        border-color: #baeafe;
    }

    .exam-results-status-pill.termine {
        color: var(--exr-success);
        background: #e6f7f1;
        border-color: #c3eddd;
    }

    .exam-results-status-pill.annule {
        color: var(--exr-danger);
        background: #fdebed;
        border-color: #f6c9ce;
    }

    .exam-results-status-pill.inconnu {
        color: #52657d;
        background: #eef2f6;
        border-color: #d9e2ec;
    }

    .exam-results-result-pill.success {
        color: var(--exr-success);
        background: #e6f7f1;
        border-color: #c3eddd;
    }

    .exam-results-result-pill.warning {
        color: var(--exr-warning);
        background: #fff4df;
        border-color: #f6deb1;
    }

    .exam-results-result-pill.critical {
        color: var(--exr-danger);
        background: #fdebed;
        border-color: #f6c9ce;
    }

    .exam-results-result-pill.info {
        color: #0a7fb0;
        background: #e6f8ff;
        border-color: #baeafe;
    }

    .exam-results-result-pill.muted {
        color: #52657d;
        background: #eef2f6;
        border-color: #d9e2ec;
    }

    .exam-results-actions-cell {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .exam-results-icon-btn {
        width: 42px;
        min-width: 42px;
        height: 42px;
        padding: 0;
        border-radius: 14px;
        border-color: #d7e4f1;
        background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        color: #365370;
        box-shadow: 0 16px 24px -24px rgba(15, 23, 42, 0.52);
    }

    .exam-results-icon-btn.view:hover,
    .exam-results-icon-btn.view:focus {
        color: #1a4d86;
        border-color: rgba(23, 96, 165, 0.28);
    }

    .exam-results-icon-btn.edit:hover,
    .exam-results-icon-btn.edit:focus {
        color: #0a7fb0;
        border-color: rgba(14, 165, 233, 0.32);
    }

    .exam-results-icon-btn.download:hover,
    .exam-results-icon-btn.download:focus {
        color: var(--exr-success);
        border-color: rgba(15, 159, 119, 0.28);
    }

    .exam-results-icon-btn.delete:hover,
    .exam-results-icon-btn.delete:focus {
        color: var(--exr-danger);
        border-color: rgba(203, 77, 88, 0.28);
    }

    .exam-results-icon-btn.disabled {
        pointer-events: none;
        opacity: .45;
    }

    .exam-results-table-footer {
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .exam-results-pagination-copy {
        margin: 0;
        color: var(--exr-muted);
        font-size: .9rem;
        font-weight: 600;
    }

    .exam-results-empty {
        margin-top: 18px;
        display: grid;
        place-items: center;
        min-height: 360px;
        border: 1px dashed #d8e4f1;
        border-radius: 24px;
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9fd 100%);
        padding: 24px;
    }

    .exam-results-empty-card {
        max-width: 520px;
        text-align: center;
        display: grid;
        gap: 14px;
        justify-items: center;
    }

    .exam-results-empty-icon {
        width: 78px;
        height: 78px;
        border-radius: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        color: var(--exr-primary);
        background: linear-gradient(180deg, #e9f4fe 0%, #dceefc 100%);
        box-shadow: 0 20px 34px -28px rgba(23, 96, 165, 0.55);
    }

    .exam-results-empty-title {
        margin: 0;
        color: var(--exr-text);
        font-size: 1.3rem;
        font-weight: 900;
        letter-spacing: -.03em;
    }

    .exam-results-empty-copy {
        margin: 0;
        color: var(--exr-muted);
        font-size: .95rem;
        line-height: 1.7;
        font-weight: 600;
    }

    body.dark-mode .exam-results-page,
    html.dark .exam-results-page {
        --exr-card: rgba(15, 23, 42, 0.95);
        --exr-text: #e5eef8;
        --exr-muted: #99abc2;
        --exr-border: #2b3b4f;
        --exr-surface: linear-gradient(180deg, #0f172a 0%, #111b31 100%);
    }

    body.dark-mode .exam-results-filter-card,
    body.dark-mode .exam-results-table-card,
    body.dark-mode .exam-results-side-card,
    body.dark-mode .exam-results-kpi,
    html.dark .exam-results-filter-card,
    html.dark .exam-results-table-card,
    html.dark .exam-results-side-card,
    html.dark .exam-results-kpi {
        box-shadow: 0 24px 36px -34px rgba(0, 0, 0, 0.8);
    }

    body.dark-mode .exam-results-select,
    body.dark-mode .exam-results-input,
    body.dark-mode .exam-results-table-wrap,
    body.dark-mode .exam-results-table,
    body.dark-mode .exam-results-empty,
    body.dark-mode .exam-results-action-card,
    body.dark-mode .exam-results-side-metric,
    body.dark-mode .exam-results-icon-btn,
    html.dark .exam-results-select,
    html.dark .exam-results-input,
    html.dark .exam-results-table-wrap,
    html.dark .exam-results-table,
    html.dark .exam-results-empty,
    html.dark .exam-results-action-card,
    html.dark .exam-results-side-metric,
    html.dark .exam-results-icon-btn {
        background: #111827;
        border-color: #334155;
        color: inherit;
    }

    body.dark-mode .exam-results-table thead th,
    html.dark .exam-results-table thead th {
        background: linear-gradient(180deg, #162033 0%, #111827 100%);
        border-bottom-color: #334155;
    }

    body.dark-mode .exam-results-table tbody td,
    html.dark .exam-results-table tbody td {
        border-bottom-color: #243244;
    }

    body.dark-mode .exam-results-table tbody tr:hover,
    html.dark .exam-results-table tbody tr:hover {
        background: rgba(30, 41, 59, 0.72);
    }

    @media (max-width: 1240px) {
        .exam-results-kpi-grid,
        .exam-results-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .exam-results-hero-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .exam-results-page {
            padding: 8px 4px 24px;
        }

        .exam-results-hero,
        .exam-results-filter-card,
        .exam-results-table-card {
            padding: 18px;
            border-radius: 22px;
        }

        .exam-results-kpi-grid,
        .exam-results-filter-form {
            grid-template-columns: 1fr;
        }

        .exam-results-actions,
        .exam-results-filter-actions {
            flex-direction: column;
        }

        .exam-results-btn,
        .exam-results-filter-btn {
            width: 100%;
        }

        .exam-results-table-wrap {
            overflow: visible;
            border: 0;
            border-radius: 0;
        }

        .exam-results-table {
            min-width: 0;
            display: block;
            background: transparent;
        }

        .exam-results-table thead {
            display: none;
        }

        .exam-results-table tbody {
            display: grid;
            gap: 14px;
        }

        .exam-results-table tbody tr {
            display: grid;
            gap: 12px;
            border: 1px solid #dbe7f2;
            border-radius: 20px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 20px 28px -28px rgba(15, 23, 42, 0.55);
        }

        .exam-results-table tbody td {
            display: block;
            padding: 0;
            border: 0;
        }

        .exam-results-mobile-label {
            display: inline-flex;
        }

        .exam-results-actions-cell {
            justify-content: flex-start;
        }

        .exam-results-table-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid exam-results-page">
    <div class="exam-results-shell">
        <section class="exam-results-hero">
            <div class="exam-results-hero-grid">
                <div>
                    <span class="exam-results-eyebrow"><i class="fas fa-vial-circle-check"></i> Parcours laboratoire</span>
                    <div class="exam-results-title-row">
                        <span class="exam-results-title-icon">
                            <i class="fas fa-flask-vial"></i>
                        </span>
                        <div>
                            <h1 class="exam-results-title">Resultats des examens</h1>
                            <p class="exam-results-subtitle">Visualisez et gerez les resultats d examens de vos patients.</p>
                        </div>
                    </div>

                    <div class="exam-results-badge-row">
                        <span class="exam-results-badge"><i class="fas fa-layer-group"></i>{{ $totalExamens }} examen{{ $totalExamens > 1 ? 's' : '' }}</span>
                        <span class="exam-results-chip"><i class="fas fa-hourglass-half"></i>{{ $examensEnAttente }} en attente</span>
                        <span class="exam-results-chip"><i class="fas fa-wave-square"></i>{{ $examensEnCours }} en cours</span>
                    </div>

                    <div class="exam-results-action-card">
                        <p class="exam-results-action-label">Actions rapides</p>
                        <div class="exam-results-actions">
                            <a href="{{ route('examens.create') }}" class="exam-results-btn primary">
                                <span class="exam-results-btn-icon"><i class="fas fa-plus"></i></span>
                                <span>Ajouter un examen</span>
                            </a>
                            <a href="{{ route('examens.index') }}" class="exam-results-btn secondary">
                                <span class="exam-results-btn-icon"><i class="fas fa-table-list"></i></span>
                                <span>Voir tous les examens</span>
                            </a>
                        </div>
                    </div>
                </div>

                <aside class="exam-results-side-card">
                    <div>
                        <p class="exam-results-side-label">Vue rapide</p>
                        <p class="exam-results-side-value">{{ $results->count() }}</p>
                        <p class="exam-results-side-copy">Lignes actuellement affichees dans la vue resultats, apres application des filtres eventuels.</p>
                    </div>

                    <div class="exam-results-side-metrics">
                        <div class="exam-results-side-metric">
                            <span>Termines</span>
                            <strong>{{ $examensTermines }}</strong>
                        </div>
                        <div class="exam-results-side-metric">
                            <span>Filtres actifs</span>
                            <strong>{{ $hasFilters ? 'Oui' : 'Non' }}</strong>
                        </div>
                        <div class="exam-results-side-metric">
                            <span>Page courante</span>
                            <strong>{{ $results->currentPage() }}</strong>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="exam-results-kpi-grid">
            <article class="exam-results-kpi">
                <div class="exam-results-kpi-top">
                    <div>
                        <p class="exam-results-kpi-label">Total examens</p>
                        <p class="exam-results-kpi-value">{{ $totalExamens }}</p>
                    </div>
                    <span class="exam-results-kpi-icon"><i class="fas fa-flask"></i></span>
                </div>
                <p class="exam-results-kpi-copy">Volume global des examens visibles dans ce perimetre de consultation.</p>
            </article>

            <article class="exam-results-kpi">
                <div class="exam-results-kpi-top">
                    <div>
                        <p class="exam-results-kpi-label">En attente</p>
                        <p class="exam-results-kpi-value">{{ $examensEnAttente }}</p>
                    </div>
                    <span class="exam-results-kpi-icon pending"><i class="fas fa-hourglass-start"></i></span>
                </div>
                <p class="exam-results-kpi-copy">Examens demandes mais pas encore entres dans le flux de resultat.</p>
            </article>

            <article class="exam-results-kpi">
                <div class="exam-results-kpi-top">
                    <div>
                        <p class="exam-results-kpi-label">En cours</p>
                        <p class="exam-results-kpi-value">{{ $examensEnCours }}</p>
                    </div>
                    <span class="exam-results-kpi-icon progress"><i class="fas fa-spinner"></i></span>
                </div>
                <p class="exam-results-kpi-copy">Demandes actuellement en traitement avec resultats attendus ou partiels.</p>
            </article>

            <article class="exam-results-kpi">
                <div class="exam-results-kpi-top">
                    <div>
                        <p class="exam-results-kpi-label">Termines</p>
                        <p class="exam-results-kpi-value">{{ $examensTermines }}</p>
                    </div>
                    <span class="exam-results-kpi-icon done"><i class="fas fa-circle-check"></i></span>
                </div>
                <p class="exam-results-kpi-copy">Examens avec parcours finalise et resultat consultable depuis le registre.</p>
            </article>
        </section>

        <section class="exam-results-filter-card">
            <div class="exam-results-filter-head">
                <div>
                    <p class="exam-results-filter-kicker">Affinage</p>
                    <h2 class="exam-results-filter-title">Filtres</h2>
                    <p class="exam-results-filter-copy">Filtrez par patient, type, statut ou date pour recentrer la lecture clinique et administrative.</p>
                </div>

                @if($hasFilters)
                    <div class="exam-results-active-filters">
                        @if($selectedPatientLabel)
                            <span class="exam-results-inline-tag"><i class="fas fa-user"></i>{{ $selectedPatientLabel }}</span>
                        @endif
                        @if($selectedType !== '' && isset($types[$selectedType]))
                            <span class="exam-results-inline-tag"><i class="fas fa-vials"></i>{{ $types[$selectedType] }}</span>
                        @endif
                        @if($selectedStatutLabel)
                            <span class="exam-results-inline-tag"><i class="fas fa-signal"></i>{{ $selectedStatutLabel }}</span>
                        @endif
                        @if($selectedDateLabel)
                            <span class="exam-results-inline-tag"><i class="fas fa-calendar-day"></i>{{ $selectedDateLabel }}</span>
                        @endif
                    </div>
                @endif
            </div>

            <form method="GET" action="{{ route('examens.results') }}" class="exam-results-filter-form">
                <div class="exam-results-field">
                    <label for="examResultsPatient">Patient</label>
                    <select id="examResultsPatient" name="patient" class="exam-results-select">
                        <option value="">Tous les patients</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ (string) $selectedPatient === (string) $patient->id ? 'selected' : '' }}>{{ $patient->display_label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="exam-results-field">
                    <label for="examResultsType">Type d examen</label>
                    <select id="examResultsType" name="type" class="exam-results-select">
                        <option value="">Tous les types</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ $selectedType === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="exam-results-field">
                    <label for="examResultsStatus">Statut</label>
                    <select id="examResultsStatus" name="statut" class="exam-results-select">
                        <option value="">Tous les statuts</option>
                        @foreach($resultStatusOptions as $value => $label)
                            <option value="{{ $value }}" {{ $selectedStatut === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="exam-results-field">
                    <label for="examResultsDate">Date</label>
                    <input id="examResultsDate" type="date" name="date" class="exam-results-input" value="{{ $selectedDate }}">
                </div>

                <div class="exam-results-filter-actions">
                    <button type="submit" class="exam-results-filter-btn primary">
                        <span class="exam-results-btn-icon"><i class="fas fa-filter"></i></span>
                        <span>Appliquer</span>
                    </button>

                    @if($hasFilters)
                        <a href="{{ route('examens.results') }}" class="exam-results-filter-btn soft">
                            <span class="exam-results-btn-icon"><i class="fas fa-rotate-left"></i></span>
                            <span>Reinitialiser</span>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="exam-results-table-card">
            <div class="exam-results-table-head">
                <div>
                    <p class="exam-results-table-kicker">Registre des resultats</p>
                    <h2 class="exam-results-table-title">Tableau des resultats</h2>
                    <p class="exam-results-table-copy">Retrouvez en un coup d oeil le patient, l examen, son etat d avancement, le resume du resultat et les actions disponibles.</p>
                </div>

                <div class="exam-results-table-meta">
                    <span class="exam-results-table-chip"><i class="fas fa-table-list"></i>{{ $results->count() }} ligne{{ $results->count() > 1 ? 's' : '' }}</span>
                    <span class="exam-results-table-chip"><i class="fas fa-arrow-down-wide-short"></i>Tri par date descendante</span>
                </div>
            </div>

            @if($results->count() > 0)
                <div class="exam-results-table-wrap">
                    <table class="exam-results-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Examen</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Resultat</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resultRows as $examen)
                                <tr>
                                    <td>
                                        <span class="exam-results-mobile-label">Patient</span>
                                        <span class="exam-results-patient-name">{{ $examen->display_patient_name }}</span>
                                        <span class="exam-results-patient-meta">{{ $examen->patient->email ?? 'Email non renseigne' }}</span>
                                    </td>
                                    <td>
                                        <span class="exam-results-mobile-label">Examen</span>
                                        <span class="exam-results-result-main">{{ $examen->nom_examen ?: 'Examen non precise' }}</span>
                                        <span class="exam-results-exam-meta">{{ $examen->description ? \Illuminate\Support\Str::limit($examen->description, 88) : 'Aucune precision complementaire.' }}</span>
                                    </td>
                                    <td>
                                        <span class="exam-results-mobile-label">Type</span>
                                        <span class="exam-results-type-pill">{{ $types[$examen->type] ?? ucfirst((string) $examen->type) }}</span>
                                    </td>
                                    <td>
                                        <span class="exam-results-mobile-label">Date</span>
                                        <span class="exam-results-date-main">{{ $examen->display_date_label ?: '-' }}</span>
                                        <span class="exam-results-date-meta">
                                            @if($examen->date_demande)
                                                {{ $examen->display_date_meta }} &middot; {{ $examen->date_demande->diffForHumans() }}
                                            @else
                                                Date non planifiee
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="exam-results-mobile-label">Resultat</span>
                                        <span class="exam-results-result-pill {{ $examen->results_tone }}">{{ $examen->results_summary }}</span>
                                        <span class="exam-results-result-meta">{{ $examen->results_preview }}</span>
                                    </td>
                                    <td>
                                        <span class="exam-results-mobile-label">Statut</span>
                                        <span class="exam-results-status-pill {{ $examen->results_status_class }}">
                                            <i class="fas fa-circle"></i>
                                            {{ $examen->results_status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="exam-results-mobile-label">Actions</span>
                                        <div class="exam-results-actions-cell">
                                            <a href="{{ route('examens.show', $examen->id) }}" class="exam-results-icon-btn view" title="Voir resultat" aria-label="Voir resultat examen {{ $examen->id }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('examens.edit', $examen->id) }}" class="exam-results-icon-btn edit" title="Modifier" aria-label="Modifier examen {{ $examen->id }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            @if($examen->results_download_url)
                                                <a href="{{ $examen->results_download_url }}" class="exam-results-icon-btn download" title="Telecharger" aria-label="Telecharger resultat examen {{ $examen->id }}" target="_blank" rel="noopener noreferrer">
                                                    <i class="fas fa-file-arrow-down"></i>
                                                </a>
                                            @else
                                                <span class="exam-results-icon-btn download disabled" title="Aucun document PDF disponible" aria-hidden="true">
                                                    <i class="fas fa-file-arrow-down"></i>
                                                </span>
                                            @endif
                                            <form action="{{ route('examens.destroy', $examen->id) }}" method="POST" onsubmit="return confirm('Supprimer cet examen ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="exam-results-icon-btn delete" title="Supprimer" aria-label="Supprimer examen {{ $examen->id }}">
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

                <div class="exam-results-table-footer">
                    <p class="exam-results-pagination-copy">Affichage de {{ $results->firstItem() ?? 0 }} a {{ $results->lastItem() ?? 0 }} sur {{ $results->total() }} examens.</p>
                    @if($results->hasPages())
                        <div>{{ $results->links() }}</div>
                    @endif
                </div>
            @endif

            @if($results->count() === 0)
                <div class="exam-results-empty">
                    <div class="exam-results-empty-card">
                        <div class="exam-results-empty-icon"><i class="fas fa-vial"></i></div>
                        <h3 class="exam-results-empty-title">Aucun resultat d examen disponible</h3>
                        <p class="exam-results-empty-copy">Commencez par ajouter ou importer un examen pour enrichir le registre, suivre les statuts et centraliser les comptes rendus patients.</p>
                        <a href="{{ route('examens.create') }}" class="exam-results-btn primary">
                            <span class="exam-results-btn-icon"><i class="fas fa-plus"></i></span>
                            <span>Ajouter un examen</span>
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
