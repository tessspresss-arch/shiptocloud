@extends('layouts.app')

@section('title', 'Liste des Rendez-vous')

@push('styles')
<style>
    .rdv-index-page {
        --rdv-primary: #2563eb;
        --rdv-primary-dark: #1d4fbe;
        --rdv-border: #d8e4f0;
        --rdv-border-strong: #c7d7e8;
        --rdv-surface: #ffffff;
        --rdv-surface-soft: #f7fbff;
        --rdv-bg: radial-gradient(circle at top right, rgba(37, 99, 235, 0.08) 0%, rgba(37, 99, 235, 0) 26%), linear-gradient(180deg, #f4f8fc 0%, #f9fbff 100%);
        --rdv-title: #173454;
        --rdv-text: #4b6481;
        --rdv-muted: #7086a2;
        --rdv-shadow: 0 22px 46px -34px rgba(15, 45, 82, 0.28);
        --rdv-shadow-hover: 0 28px 52px -32px rgba(15, 45, 82, 0.36);
        padding: 18px;
        border: 1px solid #dbe6f1;
        border-radius: 22px;
        background: var(--rdv-bg);
        box-shadow: var(--rdv-shadow);
    }

    .rdv-index-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
        padding: 2px 0 22px;
        border-bottom: 1px solid #dbe6f1;
    }

    .rdv-index-head-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }

    .rdv-index-head-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-grid;
        place-items: center;
        flex: 0 0 auto;
        color: var(--rdv-primary);
        background: linear-gradient(145deg, #eff5ff 0%, #ddeafe 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
        font-size: 1.18rem;
    }

    .rdv-index-head-copy {
        min-width: 0;
        display: grid;
        gap: 8px;
    }

    .rdv-index-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d9e6f3;
        background: rgba(255, 255, 255, 0.82);
        color: var(--rdv-primary-dark);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .rdv-index-page h1 {
        margin: 0;
        color: var(--rdv-title);
        font-size: clamp(1.7rem, 2.6vw, 2.2rem);
        font-weight: 800;
        line-height: 1.02;
        letter-spacing: -0.04em;
    }

    .rdv-index-subtitle {
        margin: 0;
        color: var(--rdv-muted);
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.6;
    }

    .rdv-index-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .rdv-head-btn {
        min-height: 44px;
        border-radius: 14px;
        padding: 0 18px;
        border: 1px solid transparent;
        background: linear-gradient(180deg, #ffffff 0%, #f3f7fb 100%);
        color: #486482;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 800;
        white-space: nowrap;
        box-shadow: 0 14px 22px -24px rgba(15, 45, 82, 0.3);
        transition: all 0.2s ease;
    }

    .rdv-head-btn:hover {
        background: linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
        color: #2c4b6c;
        transform: translateY(-1px);
        box-shadow: 0 18px 26px -24px rgba(15, 45, 82, 0.38);
    }

    .rdv-head-btn.primary {
        background: linear-gradient(135deg, var(--rdv-primary) 0%, var(--rdv-primary-dark) 100%);
        color: #fff;
        border-color: transparent;
    }

    .rdv-head-btn.primary:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        color: #fff;
        box-shadow: 0 20px 30px -24px rgba(37, 99, 235, 0.55);
    }

    .rdv-head-btn.secondary-soft {
        min-height: 42px;
        padding: 0 15px;
        border-color: #dde7f1;
        background: rgba(255, 255, 255, 0.72);
        color: #5f7690;
        box-shadow: none;
        font-weight: 700;
    }

    .rdv-head-btn.secondary-soft:hover {
        background: rgba(255, 255, 255, 0.9);
        color: #3f5d7d;
        box-shadow: 0 12px 18px -24px rgba(15, 45, 82, 0.2);
    }

    .rdv-index-page .card {
        border: 1px solid var(--rdv-border);
        border-radius: 20px;
        background: var(--rdv-surface);
        box-shadow: var(--rdv-shadow);
        overflow: hidden;
    }

    .rdv-index-page .card-body {
        padding: 1.1rem 1.15rem;
    }

    .rdv-filter-card .card-body {
        padding: 0.95rem 1rem;
    }

    .rdv-filter-form .row {
        --bs-gutter-x: 0.9rem;
        --bs-gutter-y: 0.75rem;
    }

    .rdv-index-page label {
        font-weight: 700;
        color: #3b526d;
        margin-bottom: 0.38rem;
    }

    .rdv-index-page .form-control,
    .rdv-index-page .form-select {
        min-height: 44px;
        border-color: #d6e2ee;
        border-radius: 12px;
        background: #fbfdff;
        color: #23415f;
        font-weight: 600;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    .rdv-index-page .form-control:focus,
    .rdv-index-page .form-select:focus {
        border-color: #8db4e6;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .rdv-filter-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.7rem;
        flex-wrap: wrap;
        padding-top: 0.1rem;
    }

    .rdv-filter-btn {
        min-height: 44px;
        border-radius: 12px;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 800;
        transition: all 0.2s ease;
    }

    .rdv-filter-btn.btn-primary {
        background: linear-gradient(135deg, var(--rdv-primary) 0%, var(--rdv-primary-dark) 100%);
        border-color: transparent;
        box-shadow: 0 18px 28px -24px rgba(37, 99, 235, 0.48);
    }

    .rdv-filter-btn.btn-primary:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .rdv-filter-btn.btn-secondary {
        background: linear-gradient(180deg, #ffffff 0%, #f3f7fb 100%);
        border-color: #d7e1ec;
        color: #5a6f88;
        font-weight: 700;
        box-shadow: none;
    }

    .rdv-filter-btn.btn-secondary:hover {
        transform: translateY(-1px);
        background: linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
        color: #36506f;
    }

    .rdv-table-card .card-body {
        padding: 0;
    }

    .rdv-index-page .table {
        margin-bottom: 0;
    }

    .rdv-index-page .table thead th {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.78rem;
        color: #4f6581;
        border-bottom-width: 2px;
        border-color: #dce8f6;
        white-space: nowrap;
        padding: 1rem 0.95rem;
        background: #f8fbfe;
    }

    .rdv-index-page .table tbody td {
        vertical-align: middle;
        border-color: #e5edf5;
        padding: 1rem 0.95rem;
        color: #27405e;
    }

    .rdv-row {
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .rdv-index-page .table-hover > tbody > tr.rdv-row:hover {
        background: #f6faff;
    }

    .rdv-date {
        display: grid;
        gap: 0.18rem;
    }

    .rdv-date strong {
        color: var(--rdv-title);
        font-weight: 800;
    }

    .rdv-date small {
        color: var(--rdv-muted);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .rdv-person {
        display: grid;
        gap: 0.18rem;
    }

    .rdv-person-name {
        color: var(--rdv-title);
        font-weight: 800;
        line-height: 1.35;
    }

    .rdv-person-sub {
        color: var(--rdv-muted);
        font-size: 0.83rem;
        font-weight: 600;
    }

    .rdv-doctor {
        display: grid;
        gap: 0.18rem;
    }

    .rdv-doctor-name {
        color: var(--rdv-title);
        font-weight: 700;
    }

    .rdv-doctor-sub {
        color: var(--rdv-muted);
        font-size: 0.83rem;
        font-weight: 600;
    }

    .rdv-meta-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.34rem 0.68rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 800;
        line-height: 1.15;
        border: 1px solid transparent;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
        white-space: nowrap;
    }

    .rdv-type-badge {
        background: #eef4ff;
        color: #335f9a;
        border-color: #d3e0f3;
    }

    .rdv-status-upcoming { background: #eef4ff; color: #2d66d8; border-color: #c9dafb; }
    .rdv-status-waiting { background: #fff6eb; color: #c06a15; border-color: #f3d2ad; }
    .rdv-status-active { background: #ebf8f5; color: #0f8a63; border-color: #b8e5d4; }
    .rdv-status-done { background: #edf8f2; color: #0d7a57; border-color: #b8e2cf; }
    .rdv-status-missed { background: #f7f9fc; color: #50657f; border-color: #d9e2eb; }
    .rdv-status-cancelled { background: #fff4ef; color: #b66325; border-color: #f1d2bc; }

    .rdv-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
    }

    .rdv-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 11px;
        border: 1px solid #d7e1ec;
        background: #ffffff;
        color: #607892;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    .rdv-action-btn:hover {
        transform: translateY(-1px);
        background: #f6faff;
        color: #274666;
    }

    .rdv-action-btn.action-tone-view:hover {
        border-color: #bfd4eb;
        color: #365f96;
    }

    .rdv-action-btn.action-tone-edit:hover {
        border-color: #bfd4eb;
        color: #2f67b7;
    }

    .rdv-row-menu {
        position: relative;
        display: inline-flex;
    }

    .rdv-row-menu summary {
        list-style: none;
    }

    .rdv-row-menu summary::-webkit-details-marker {
        display: none;
    }

    .rdv-action-btn.more {
        color: #7a8fa5;
    }

    .rdv-action-btn.more:hover {
        border-color: #cbd9e7;
        color: #50657f;
        background: #f6f9fc;
    }

    .rdv-row-menu[open] .rdv-action-btn.more {
        background: #f4f8fc;
        border-color: #c7d7e6;
        color: #48607a;
    }

    .rdv-row-menu-panel {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 150px;
        padding: 8px;
        border-radius: 14px;
        border: 1px solid #dbe5ef;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 18px 30px -24px rgba(15, 45, 82, 0.28);
        z-index: 5;
    }

    .rdv-row-menu-action {
        width: 100%;
        min-height: 38px;
        padding: 0 12px;
        border-radius: 10px;
        border: 1px solid transparent;
        background: transparent;
        color: #a54545;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .rdv-row-menu-action:hover {
        background: #fff4f4;
        color: #913838;
    }

    .rdv-empty {
        padding: 3rem 1.25rem;
    }

    .rdv-empty i {
        width: 64px;
        height: 64px;
        display: inline-grid;
        place-items: center;
        border-radius: 20px;
        background: linear-gradient(145deg, #eef5ff 0%, #ddeafe 100%);
        color: var(--rdv-primary);
        font-size: 1.55rem;
        margin-bottom: 0.9rem;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
    }

    .rdv-pagination {
        padding: 1rem 1.15rem 1.15rem;
    }

    body.dark-mode .rdv-index-page .card {
        background: #102137;
        border-color: #304b69;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.32);
    }

    body.dark-mode .rdv-index-page h1,
    body.dark-mode .rdv-index-page .table tbody td,
    body.dark-mode .rdv-index-page .table thead th,
    body.dark-mode .rdv-index-page label {
        color: #e5eefc;
    }

    body.dark-mode .rdv-index-page {
        background: linear-gradient(180deg, #0f1f31 0%, #0d1a2b 100%);
        border-color: #274666;
    }

    body.dark-mode .rdv-index-header {
        border-bottom-color: #365a7b;
    }

    body.dark-mode .rdv-index-head-icon {
        color: #77b7ff;
        background: linear-gradient(145deg, #173251 0%, #16304c 100%);
    }

    body.dark-mode .rdv-index-eyebrow {
        border-color: #355978;
        background: rgba(19, 43, 69, 0.72);
        color: #d4e7fb;
    }

    body.dark-mode .rdv-index-subtitle,
    body.dark-mode .rdv-date small,
    body.dark-mode .rdv-person-sub,
    body.dark-mode .rdv-doctor-sub {
        color: #a5bbd4 !important;
    }

    body.dark-mode .rdv-head-btn,
    body.dark-mode .rdv-filter-btn.btn-secondary {
        color: #d2e6fb;
        border-color: #3c5f81;
        background: linear-gradient(180deg, #183554 0%, #17324d 100%);
    }

    body.dark-mode .rdv-head-btn:hover,
    body.dark-mode .rdv-filter-btn.btn-secondary:hover {
        color: #fff;
        background: #234a6d;
    }

    body.dark-mode .rdv-head-btn.primary,
    body.dark-mode .rdv-filter-btn.btn-primary {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        border-color: transparent;
        color: #fff;
    }

    body.dark-mode .rdv-head-btn.secondary-soft {
        background: rgba(19, 43, 69, 0.78);
        border-color: #355978;
        color: #c6def8;
        box-shadow: none;
    }

    body.dark-mode .rdv-head-btn.secondary-soft:hover {
        background: rgba(28, 58, 92, 0.96);
        color: #f2f8ff;
    }

    body.dark-mode .rdv-index-page p.text-muted,
    body.dark-mode .rdv-index-page .text-muted,
    body.dark-mode .rdv-index-page small {
        color: #a5bbd4 !important;
    }

    body.dark-mode .rdv-index-page .table-light,
    body.dark-mode .rdv-index-page .table-light > th,
    body.dark-mode .rdv-index-page .table-light > td {
        background: #17304b !important;
        color: #e5eefc;
        border-color: #355272 !important;
    }

    body.dark-mode .rdv-index-page .table > :not(caption) > * > * {
        background-color: transparent;
        border-color: #2e4a67;
    }

    body.dark-mode .rdv-index-page .table-hover > tbody > tr:hover {
        background: #183455;
    }

    body.dark-mode .rdv-index-page .table thead th {
        border-color: #335b86;
        background: #173251;
        color: #b9d3ee;
    }

    body.dark-mode .rdv-index-page .form-control,
    body.dark-mode .rdv-index-page .form-select {
        background: #0f1d2f;
        border-color: #38597d;
        color: #e5efff;
    }

    body.dark-mode .rdv-index-page .form-control::placeholder {
        color: #95adc9;
    }

    body.dark-mode .rdv-index-page .form-control:focus,
    body.dark-mode .rdv-index-page .form-select:focus {
        border-color: #63a9ff;
        box-shadow: 0 0 0 0.2rem rgba(99, 169, 255, 0.2);
    }

    body.dark-mode .rdv-type-badge {
        background: #1a3656;
        border-color: #345b84;
        color: #cfe4ff;
    }

    body.dark-mode .rdv-status-upcoming { background: #1e3a5f; color: #bfdbfe; border-color: #295a96; }
    body.dark-mode .rdv-status-waiting { background: #3b2a1a; color: #fed7aa; border-color: #8a5224; }
    body.dark-mode .rdv-status-active { background: #133d3b; color: #99f6e4; border-color: #0f766e; }
    body.dark-mode .rdv-status-done { background: #15382c; color: #a7f3d0; border-color: #1f6a4b; }
    body.dark-mode .rdv-status-missed { background: #1f2937; color: #cbd5e1; border-color: #475569; }
    body.dark-mode .rdv-status-cancelled { background: #462717; color: #fed7aa; border-color: #8a5224; }

    body.dark-mode .rdv-action-btn {
        background: #173456;
        border-color: #355978;
        color: #d3e8ff;
    }

    body.dark-mode .rdv-action-btn:hover {
        background: #1d3f66;
        color: #f0f7ff;
    }

    body.dark-mode .rdv-row-menu[open] .rdv-action-btn.more {
        background: #21476f;
        border-color: #4a7099;
        color: #f0f7ff;
    }

    body.dark-mode .rdv-row-menu-panel {
        background: rgba(16, 33, 55, 0.98);
        border-color: #355978;
        box-shadow: 0 18px 30px -24px rgba(0, 0, 0, 0.48);
    }

    body.dark-mode .rdv-row-menu-action {
        color: #ffc8d1;
    }

    body.dark-mode .rdv-row-menu-action:hover {
        background: rgba(123, 59, 70, 0.18);
        color: #ffdbe1;
    }

    body.dark-mode .rdv-empty i {
        color: #9ac8ff;
        background: linear-gradient(145deg, #173251 0%, #16304c 100%);
    }

    .rdv-display-toolbar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 14px;
    }

    .rdv-display-toolbar .display-mode-switch {
        padding: 4px;
        border-radius: 16px;
        border: 1px solid #dbe5ef;
        background: #f8fbfe;
        box-shadow: none;
    }

    .rdv-display-toolbar .display-mode-option {
        min-height: 34px;
        padding: 0 14px;
        border-radius: 12px;
        font-size: 0.83rem;
        font-weight: 700;
    }

    body.dark-mode .rdv-display-toolbar .display-mode-switch {
        background: #12263d;
        border-color: #355978;
    }

    .rdv-mode-compact .rdv-index-page .table tbody td {
        padding-top: 12px;
        padding-bottom: 12px;
        font-size: .92rem;
    }

    .rdv-mode-compact .rdv-person-sub,
    .rdv-mode-compact .rdv-doctor-sub {
        display: none;
    }

    .rdv-mode-compact .rdv-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
    }

    .rdv-mode-cards .rdv-index-page .table thead {
        display: none;
    }

    .rdv-mode-cards .rdv-index-page .table,
    .rdv-mode-cards .rdv-index-page .table tbody {
        display: grid;
        gap: 12px;
        width: 100%;
    }

    .rdv-mode-cards .rdv-index-page .table tbody tr.rdv-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 14px;
        padding: 16px;
        border-radius: 18px;
        border: 1px solid #dbe5f0;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 16px 22px -26px rgba(15, 23, 42, .16);
    }

    .rdv-mode-cards .rdv-index-page .table tbody td {
        display: grid;
        gap: 4px;
        padding: 0;
        border: none;
    }

    .rdv-mode-cards .rdv-index-page .table tbody td::before {
        content: attr(data-label);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #7a8ea5;
    }

    .rdv-mode-cards .rdv-index-page .table tbody td[data-label="Actions"] {
        grid-column: 1 / -1;
    }

    @media (max-width: 767.98px) {
        .rdv-index-page {
            padding: 12px;
        }

        .rdv-index-header {
            flex-direction: column;
            align-items: stretch;
            padding-bottom: 18px;
        }

        .rdv-index-actions,
        .rdv-head-btn,
        .rdv-filter-actions .rdv-filter-btn {
            width: 100%;
        }

        .rdv-head-btn,
        .rdv-filter-actions .rdv-filter-btn {
            justify-content: center;
        }

        .rdv-filter-actions {
            justify-content: stretch;
        }

        .rdv-index-page .card-body {
            padding: 1rem;
        }

        .rdv-index-head-main {
            align-items: flex-start;
        }

        .rdv-index-head-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .rdv-index-page h1 {
            font-size: 1.5rem;
        }

        .rdv-index-subtitle {
            font-size: 0.94rem;
        }

        .rdv-table-card .table-responsive {
            overflow: visible;
        }

        .rdv-index-page .table {
            min-width: 0;
            display: block;
        }

        .rdv-index-page .table thead {
            display: none;
        }

        .rdv-index-page .table tbody {
            display: grid;
            gap: 14px;
        }

        .rdv-index-page .table tbody tr.rdv-row {
            display: grid;
            gap: 12px;
            padding: 16px;
            border: 1px solid #dbe7f1;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 16px 28px -28px rgba(15, 40, 65, 0.3);
        }

        .rdv-index-page .table tbody td {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
            padding: 0;
            border: 0;
        }

        .rdv-index-page .table tbody td::before {
            content: attr(data-label);
            color: #718aa3;
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .rdv-index-page .table tbody tr.rdv-empty-row {
            display: table-row;
            padding: 0;
            border: 0;
            background: transparent;
            box-shadow: none;
        }

        .rdv-index-page .table tbody tr.rdv-empty-row td {
            display: table-cell;
        }

        .rdv-index-page .table tbody tr.rdv-empty-row td::before {
            content: none;
        }

        .rdv-actions {
            justify-content: flex-start;
        }

        .rdv-row-menu-panel {
            left: 0;
            right: auto;
        }

        body.dark-mode .rdv-index-page .table tbody tr.rdv-row {
            background: #11273d;
            border-color: #26435d;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid rdv-index-page">
    <div class="rdv-index-header">
        <div class="rdv-index-head-main">
            <span class="rdv-index-head-icon"><i class="fas fa-calendar-check"></i></span>
            <div class="rdv-index-head-copy">
                <span class="rdv-index-eyebrow"><i class="fas fa-clock"></i> Planning cabinet</span>
                <h1>Liste des Rendez-vous</h1>
                <p class="rdv-index-subtitle">Gerez tous les rendez-vous de votre cabinet avec une lecture plus claire du planning.</p>
            </div>
        </div>
        <div class="rdv-index-actions">
            <a href="{{ route('rendezvous.create') }}" class="rdv-head-btn primary">
                <i class="fas fa-plus"></i> Nouveau rendez-vous
            </a>
            <a href="{{ route('agenda.index') }}" class="rdv-head-btn secondary-soft">
                <i class="fas fa-calendar-alt"></i> Vue Agenda
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card rdv-filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('rendezvous.index') }}" class="rdv-filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control"
                               value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Patient</label>
                        <select name="patient_id" class="form-select">
                            <option value="">Tous les patients</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}"
                                        {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->nom }} {{ $patient->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Medecin</label>
                        <select name="medecin_id" class="form-select">
                            <option value="">Tous les medecins</option>
                            @foreach($medecins as $medecin)
                                <option value="{{ $medecin->id }}"
                                        {{ request('medecin_id') == $medecin->id ? 'selected' : '' }}>
                                    Dr. {{ $medecin->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Statut</label>
                        <select name="statut" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="a_venir" {{ request('statut') == 'a_venir' ? 'selected' : '' }}>A venir</option>
                            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="en_soins" {{ request('statut') == 'en_soins' ? 'selected' : '' }}>En soins</option>
                            <option value="vu" {{ request('statut') == 'vu' ? 'selected' : '' }}>Vu</option>
                            <option value="absent" {{ request('statut') == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annule</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="rdv-filter-actions">
                        <button type="submit" class="btn btn-primary rdv-filter-btn">
                            <i class="fas fa-filter"></i> Appliquer
                        </button>
                        <a href="{{ route('rendezvous.index') }}" class="btn btn-secondary rdv-filter-btn">
                            <i class="fas fa-times"></i> Reinitialiser
                        </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des rendez-vous -->
    <div class="card rdv-table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Date & Heure</th>
                            <th>Patient</th>
                            <th>Medecin</th>
                            <th>Duree</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rendezvous as $rdv)
                        <tr class="rdv-row">
                            <td data-label="ID">{{ $rdv->id }}</td>
                            <td data-label="Date et heure">
                                <div class="rdv-date">
                                    <strong>{{ $rdv->date_heure->format('d/m/Y') }}</strong>
                                    <small>{{ $rdv->date_heure->format('H:i') }}</small>
                                </div>
                            </td>
                            <td data-label="Patient">
                                <div class="rdv-person">
                                    <span class="rdv-person-name">{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</span>
                                    <span class="rdv-person-sub">{{ $rdv->patient->telephone }}</span>
                                </div>
                            </td>
                            <td data-label="Medecin">
                                <div class="rdv-doctor">
                                    <span class="rdv-doctor-name">Dr. {{ $rdv->medecin->nom }}</span>
                                    <span class="rdv-doctor-sub">{{ $rdv->medecin->specialite }}</span>
                                </div>
                            </td>
                            <td data-label="Duree">{{ $rdv->duree }} min</td>
                            <td data-label="Type">
                                <span class="rdv-meta-badge rdv-type-badge">{{ $rdv->type }}</span>
                            </td>
                            <td data-label="Statut">
                                <span class="rdv-meta-badge {{ $rdv->status_class }}">{{ $rdv->status_label }}</span>
                            </td>
                            <td data-label="Actions">
                                <div class="rdv-actions">
                                    <a href="{{ route('rendezvous.show', $rdv->id) }}"
                                       class="rdv-action-btn action-tone-view" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('rendezvous.edit', $rdv->id) }}"
                                       class="rdv-action-btn action-tone-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <details class="rdv-row-menu">
                                        <summary class="rdv-action-btn more" title="Plus d actions" aria-label="Plus d actions">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </summary>
                                        <div class="rdv-row-menu-panel">
                                            <form action="{{ route('rendezvous.destroy', $rdv->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rdv-row-menu-action"
                                                        onclick="return confirm('Supprimer ce rendez-vous?')"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                    <span>Supprimer</span>
                                                </button>
                                            </form>
                                        </div>
                                    </details>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="rdv-empty-row">
                            <td colspan="8" class="text-center text-muted py-4 rdv-empty">
                                <i class="fas fa-calendar-times"></i><br>
                                Aucun rendez-vous trouve.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($rendezvous->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3 rdv-pagination">
                <div class="text-muted">
                    Affichage de {{ $rendezvous->firstItem() }} a {{ $rendezvous->lastItem() }}
                    sur {{ $rendezvous->total() }} rendez-vous
                </div>
                <div>
                    {{ $rendezvous->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
