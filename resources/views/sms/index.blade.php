@extends('layouts.app')

@section('title', 'Rappels SMS')
@section('topbar_subtitle', 'Suivi des rappels SMS planifies, envoyes et a renvoyer.')

@section('content')
<style>
    :root {
        --sms-primary: #1274d8;
        --sms-primary-dark: #0f5cad;
        --sms-success: #1c9b74;
        --sms-danger: #cf4d5d;
        --sms-ink: #17324d;
        --sms-muted: #637b94;
        --sms-soft: #eef4fb;
        --sms-line: #d9e6f2;
        --sms-surface: #ffffff;
        --sms-surface-alt: #f7fafd;
        --sms-shadow: 0 24px 40px -34px rgba(18, 46, 78, 0.45);
    }

    .sms-page {
        min-height: 100%;
        padding: 18px clamp(12px, 1.8vw, 26px) 30px;
        border-radius: 22px;
        border: 1px solid #e0ebf5;
        background:
            radial-gradient(circle at top right, rgba(18, 116, 216, 0.16) 0%, transparent 30%),
            radial-gradient(circle at left top, rgba(56, 189, 248, 0.1) 0%, transparent 26%),
            linear-gradient(180deg, #f4f9fd 0%, #edf5fb 100%);
        box-shadow: 0 24px 42px -36px rgba(15, 36, 64, 0.72);
    }

    .sms-alert-stack {
        display: grid;
        gap: 10px;
        margin-bottom: 16px;
    }

    .sms-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .sms-hero-main,
    .stat-card,
    .table-shell {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        border: 1px solid var(--sms-line);
        background: rgba(255, 255, 255, 0.92);
        box-shadow: var(--sms-shadow);
        backdrop-filter: blur(10px);
    }

    .sms-hero-main {
        padding: clamp(20px, 2.3vw, 30px);
        background:
            radial-gradient(circle at top right, rgba(18, 116, 216, 0.08) 0%, transparent 34%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, rgba(248, 251, 254, 0.94) 100%);
    }

    .sms-hero-main::before,
    .table-shell::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent 55%);
    }

    .sms-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(18, 116, 216, 0.16);
        background: rgba(255, 255, 255, 0.7);
        color: var(--sms-primary-dark);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sms-badge-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .sms-count-badge,
    .sms-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        font-weight: 700;
        white-space: nowrap;
    }

    .sms-count-badge {
        background: linear-gradient(180deg, #eef5ff 0%, #e2ecfb 100%);
        color: var(--sms-primary-dark);
        border: 1px solid #d4e1f4;
        box-shadow: 0 10px 16px -18px rgba(37, 99, 235, 0.32);
    }

    .sms-chip {
        border: 1px solid #d5e5f5;
        background: #f5f9fe;
        color: #53718d;
        font-size: 0.85rem;
    }

    .sms-chip i {
        color: var(--sms-primary);
    }

    .sms-title-wrap {
        display: grid;
        gap: 12px;
        margin-top: 10px;
    }

    .sms-title-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: nowrap;
    }

    .sms-title-content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex: 1 1 0;
        flex-wrap: wrap;
        min-width: 0;
    }

    .sms-title-block {
        min-width: 0;
        flex: 1 1 440px;
    }

    .sms-title-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--sms-primary) 0%, var(--sms-primary-dark) 100%);
        color: #ffffff;
        font-size: 1.35rem;
        box-shadow: 0 18px 28px -20px rgba(18, 116, 216, 0.58);
        flex-shrink: 0;
    }

    .sms-title-block h1 {
        margin: 0;
        color: #123355;
        font-size: clamp(1.6rem, 2.6vw, 2.3rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .sms-title-block p {
        margin: 8px 0 0;
        max-width: 70ch;
        color: var(--sms-muted);
        font-size: 0.98rem;
        line-height: 1.64;
        font-weight: 600;
    }

    .sms-hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
        align-items: center;
        align-self: flex-start;
    }

    .sms-head-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 50px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid transparent;
        font-size: 0.92rem;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .sms-head-btn:hover,
    .sms-head-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-head-btn.secondary {
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        border-color: #cfdeec;
        color: #48657f;
        box-shadow: 0 14px 22px -24px rgba(15, 23, 42, 0.42);
    }

    .sms-head-btn.secondary:hover {
        background: linear-gradient(180deg, #ffffff 0%, #ebf4fb 100%);
        border-color: rgba(18, 116, 216, 0.3);
        color: var(--sms-primary-dark);
    }

    .sms-head-btn.success {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #fff;
        box-shadow: 0 18px 28px -24px rgba(37, 99, 235, 0.5);
    }

    .sms-head-btn.success:hover {
        color: #fff;
        box-shadow: 0 24px 32px -24px rgba(37, 99, 235, 0.58);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: clamp(14px, 1.8vw, 20px);
        margin-bottom: 26px;
    }

    .stat-card {
        padding: 22px 22px 20px;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        inset: auto 18px 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(18, 116, 216, 0.18), transparent);
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .stat-meta {
        min-width: 0;
    }

    .stat-label {
        margin: 0 0 6px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64809d;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .stat-label i {
        color: inherit;
        font-size: 0.88rem;
    }

    .stat-caption {
        color: var(--sms-muted);
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .stat-card.is-planned .stat-icon {
        background: rgba(18, 116, 216, 0.10);
        color: var(--sms-primary);
    }

    .stat-card.is-sent .stat-icon {
        background: rgba(28, 155, 116, 0.12);
        color: var(--sms-success);
    }

    .stat-card.is-failed .stat-icon {
        background: rgba(207, 77, 93, 0.12);
        color: var(--sms-danger);
    }

    .stat-value {
        margin: 0;
        color: var(--sms-ink);
        font-size: clamp(2rem, 3vw, 2.6rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.05em;
    }

    .stat-change {
        margin-top: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        background: var(--sms-soft);
        color: #4e6c88;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .table-shell {
        padding: 18px;
    }

    .table-container {
        border-radius: 18px;
        border: 1px solid var(--sms-line);
        overflow: hidden;
        background: var(--sms-surface);
    }

    .header-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px 20px;
        border-bottom: 1px solid #e6eef7;
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9fd 100%);
    }

    .section-title-wrap {
        display: grid;
        gap: 6px;
    }

    .section-kicker {
        color: #6f8ba7;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .header-section h2 {
        margin: 0;
        color: var(--sms-ink);
        font-size: clamp(1.25rem, 2vw, 1.55rem);
        line-height: 1.15;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .header-section p {
        margin: 0;
        color: var(--sms-muted);
        font-size: 0.93rem;
        line-height: 1.55;
    }

    .table-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 12px;
        border: 1px solid #d3e0ed;
        background: #fff;
        color: #48657f;
        font-size: 0.92rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        background: #f3f8fd;
        color: #214f7b;
        border-color: #bfd4e8;
        transform: translateY(-1px);
    }

    .btn-action i {
        color: var(--sms-primary);
    }

    .table-wrap {
        overflow-x: auto;
    }

    .sms-table {
        width: 100%;
        min-width: 980px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .sms-table thead {
        background: #f5f9fd;
    }

    .sms-table th {
        padding: 16px 18px;
        text-align: left;
        color: #5c7893;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        border-bottom: 1px solid #e1ebf4;
        white-space: nowrap;
    }

    .sms-table td {
        padding: 18px;
        color: #334e68;
        font-size: 0.94rem;
        line-height: 1.45;
        vertical-align: middle;
        border-bottom: 1px solid #edf3f8;
    }

    .sms-table tbody tr {
        transition: background-color 0.18s ease, transform 0.18s ease;
    }

    .sms-table tbody tr:hover {
        background: #f8fbfe;
    }

    .patient-cell {
        min-width: 230px;
    }

    .patient-name {
        display: block;
        color: var(--sms-ink);
        font-size: 0.98rem;
        font-weight: 700;
    }

    .patient-subtext,
    .date-cell {
        display: block;
        margin-top: 4px;
        color: var(--sms-muted);
        font-size: 0.86rem;
    }

    .rdv-cell strong {
        display: block;
        color: var(--sms-ink);
        font-size: 0.94rem;
        font-weight: 700;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: 0.83rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-planned {
        background: rgba(18, 116, 216, 0.10);
        color: #0f5cad;
    }

    .status-sent {
        background: rgba(28, 155, 116, 0.12);
        color: #167657;
    }

    .status-failed {
        background: rgba(207, 77, 93, 0.12);
        color: #a73d4a;
    }

    .actions-cell {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .actions-cell form {
        margin: 0;
    }

    .btn-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1px solid #d8e4ef;
        background: #f6f9fc;
        color: #47627d;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        color: #fff;
        border-color: var(--sms-primary);
        background: var(--sms-primary);
        transform: translateY(-1px);
    }

    .btn-icon.btn-danger-soft {
        background: rgba(207, 77, 93, 0.08);
        border-color: rgba(207, 77, 93, 0.18);
        color: #bb4253;
    }

    .btn-icon.btn-danger-soft:hover {
        background: var(--sms-danger);
        border-color: var(--sms-danger);
        color: #fff;
    }

    .empty-state {
        padding: clamp(34px, 5vw, 56px) 20px;
        text-align: center;
    }

    .empty-state-inner {
        max-width: 520px;
        margin: 0 auto;
        display: grid;
        gap: 14px;
        justify-items: center;
    }

    .empty-state-icon {
        width: 78px;
        height: 78px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(18, 116, 216, 0.14), rgba(28, 155, 116, 0.10));
        color: var(--sms-primary);
        font-size: 1.9rem;
        box-shadow: inset 0 0 0 1px rgba(18, 116, 216, 0.08);
    }

    .empty-state h3 {
        margin: 0;
        color: var(--sms-ink);
        font-size: clamp(1.25rem, 2vw, 1.55rem);
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .empty-state p {
        margin: 0;
        color: var(--sms-muted) !important;
        font-size: 0.98rem;
        line-height: 1.7;
    }

    .empty-state .sms-head-btn {
        margin-top: 4px;
    }

    .pagination-wrap {
        padding: 18px 22px;
        border-top: 1px solid #e6eef7;
        background: #fbfdff;
    }

    html.dark .sms-page,
    body.dark-mode .sms-page {
        --sms-ink: #e5effb;
        --sms-muted: #a8bed4;
        border-color: #284763;
        background:
            radial-gradient(circle at top right, rgba(87, 156, 255, 0.14) 0%, transparent 30%),
            radial-gradient(circle at bottom left, rgba(28, 155, 116, 0.10) 0%, transparent 28%),
            linear-gradient(140deg, #0e1a2b 0%, #102033 100%);
        box-shadow: 0 24px 42px -34px rgba(0, 0, 0, 0.9);
    }

    html.dark .sms-hero-main,
    html.dark .stat-card,
    html.dark .table-shell,
    html.dark .table-container,
    body.dark-mode .sms-hero-main,
    body.dark-mode .stat-card,
    body.dark-mode .table-shell,
    body.dark-mode .table-container {
        background: rgba(17, 34, 53, 0.92);
        border-color: #2f4f6e;
        box-shadow: 0 22px 36px -28px rgba(0, 0, 0, 0.55);
    }

    html.dark .sms-hero-main,
    body.dark-mode .sms-hero-main {
        background:
            radial-gradient(circle at top right, rgba(87, 156, 255, 0.1) 0%, transparent 34%),
            linear-gradient(180deg, rgba(17, 34, 53, 0.96) 0%, rgba(15, 30, 47, 0.96) 100%);
    }

    html.dark .sms-title-block h1,
    html.dark .stat-value,
    html.dark .header-section h2,
    html.dark .patient-name,
    html.dark .rdv-cell strong,
    html.dark .empty-state h3,
    html.dark .sms-table td,
    html.dark .sms-table th,
    body.dark-mode .sms-title-block h1,
    body.dark-mode .stat-value,
    body.dark-mode .header-section h2,
    body.dark-mode .patient-name,
    body.dark-mode .rdv-cell strong,
    body.dark-mode .empty-state h3,
    body.dark-mode .sms-table td,
    body.dark-mode .sms-table th {
        color: #e5effb;
    }

    html.dark .sms-title-block p,
    html.dark .stat-caption,
    html.dark .stat-label,
    html.dark .section-kicker,
    html.dark .header-section p,
    html.dark .patient-subtext,
    html.dark .date-cell,
    html.dark .empty-state p,
    body.dark-mode .sms-title-block p,
    body.dark-mode .stat-caption,
    body.dark-mode .stat-label,
    body.dark-mode .section-kicker,
    body.dark-mode .header-section p,
    body.dark-mode .patient-subtext,
    body.dark-mode .date-cell,
    body.dark-mode .empty-state p {
        color: #a8bed4;
    }

    html.dark .sms-chip,
    html.dark .stat-change,
    html.dark .btn-action,
    html.dark .btn-icon,
    body.dark-mode .sms-chip,
    body.dark-mode .stat-change,
    body.dark-mode .btn-action,
    body.dark-mode .btn-icon {
        background: #17314c;
        border-color: #355777;
        color: #d5e7fb;
    }

    html.dark .btn-action:hover,
    body.dark-mode .btn-action:hover,
    html.dark .btn-icon:hover,
    body.dark-mode .btn-icon:hover {
        background: #21486f;
        border-color: #4d7aa5;
        color: #fff;
    }

    html.dark .header-section,
    html.dark .sms-table thead,
    html.dark .pagination-wrap,
    body.dark-mode .header-section,
    body.dark-mode .sms-table thead,
    body.dark-mode .pagination-wrap {
        background: #132940;
        border-color: #29435f;
    }

    html.dark .sms-table td,
    body.dark-mode .sms-table td {
        border-bottom-color: #243d58;
    }

    html.dark .sms-table tbody tr:hover,
    body.dark-mode .sms-table tbody tr:hover {
        background: #152e49;
    }

    html.dark .status-planned,
    body.dark-mode .status-planned {
        background: rgba(87, 156, 255, 0.18);
        color: #bfdbfe;
    }

    html.dark .status-sent,
    body.dark-mode .status-sent {
        background: rgba(28, 155, 116, 0.22);
        color: #b8f1dc;
    }

    html.dark .status-failed,
    body.dark-mode .status-failed {
        background: rgba(207, 77, 93, 0.2);
        color: #fec9d0;
    }

    html.dark .empty-state .empty-state-inner p,
    body.dark-mode .empty-state .empty-state-inner p {
        color: #a8bed4;
    }

    @media (max-width: 1199px) {
        .sms-hero {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .header-section {
            align-items: stretch;
        }

        .table-actions {
            width: 100%;
            justify-content: stretch;
        }

        .btn-action {
            flex: 1 1 180px;
        }
    }

    @media (max-width: 768px) {
        .sms-page {
            padding: 12px;
            border-radius: 18px;
        }

        .sms-hero-main,
        .stat-card,
        .table-shell {
            border-radius: 18px;
        }

        .sms-hero-main,
        .header-section,
        .table-shell {
            padding-left: 16px;
            padding-right: 16px;
        }

        .sms-title-row,
        .sms-badge-row,
        .sms-hero-actions {
            align-items: stretch;
        }

        .sms-title-row {
            flex-wrap: wrap;
        }

        .sms-head-btn {
            width: 100%;
        }

        .sms-hero-actions {
            display: grid;
        }

        .header-section {
            padding-top: 18px;
            padding-bottom: 18px;
        }

        .table-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .sms-table th,
        .sms-table td {
            padding: 15px 14px;
        }

        .pagination-wrap {
            padding: 14px 16px;
        }
    }

    @media (min-width: 1400px) {
        .sms-page {
            padding-inline: clamp(18px, 2vw, 30px);
        }
    }
</style>

<div class="sms-page">
    <div class="sms-alert-stack">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-0">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-0">{{ session('error') }}</div>
        @endif
    </div>

    <section class="sms-hero">
        <div class="sms-hero-main">
            <span class="sms-eyebrow">Parcours SMS</span>

            <div class="sms-title-wrap">
                <div class="sms-title-row">
                    <span class="sms-title-icon">
                        <i class="fas fa-sms"></i>
                    </span>
                    <div class="sms-title-content">
                        <div class="sms-title-block">
                            <h1>Rappels SMS</h1>
                            <p>Supervisez les rappels planifies, les envois effectues et les relances en attente depuis une interface plus claire et mieux structuree.</p>
                        </div>

                        <div class="sms-hero-actions">
                            <a href="{{ route('sms.logs') }}" class="sms-head-btn secondary">
                                <i class="fas fa-history"></i>
                                Historique
                            </a>
                            <a href="{{ route('sms.create') }}" class="sms-head-btn success">
                                <i class="fas fa-plus"></i>
                                Nouveau rappel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sms-badge-row">
                <span class="sms-count-badge">
                    <i class="fas fa-layer-group"></i>
                    {{ $stats['total'] ?? 0 }} Rappels
                </span>
                <span class="sms-chip">
                    <i class="fas fa-wave-square"></i>
                    Tableau de suivi SMS
                </span>
            </div>
        </div>
    </section>

    <section class="stats-grid">
        <article class="stat-card is-planned">
            <div class="stat-top">
                <div class="stat-meta">
                    <p class="stat-label"><i class="fas fa-calendar-alt"></i> Planifies</p>
                    <div class="stat-caption">Rappels en attente d'envoi ou de confirmation.</div>
                </div>
                <span class="stat-icon">
                    <i class="fas fa-clock"></i>
                </span>
            </div>
            <p class="stat-value">{{ $stats['planifies'] ?? 0 }}</p>
            <div class="stat-change">
                <i class="fas fa-hourglass-half"></i>
                En attente d'envoi
            </div>
        </article>

        <article class="stat-card is-sent">
            <div class="stat-top">
                <div class="stat-meta">
                    <p class="stat-label"><i class="fas fa-paper-plane"></i> Envoyes</p>
                    <div class="stat-caption">Messages confirmes sur la periode en cours.</div>
                </div>
                <span class="stat-icon">
                    <i class="fas fa-check"></i>
                </span>
            </div>
            <p class="stat-value">{{ $stats['envoyes'] ?? 0 }}</p>
            <div class="stat-change">
                <i class="fas fa-calendar-day"></i>
                Suivi du mois en cours
            </div>
        </article>

        <article class="stat-card is-failed">
            <div class="stat-top">
                <div class="stat-meta">
                    <p class="stat-label"><i class="fas fa-triangle-exclamation"></i> Echoues</p>
                    <div class="stat-caption">Envois a verifier ou a reprogrammer rapidement.</div>
                </div>
                <span class="stat-icon">
                    <i class="fas fa-rotate-right"></i>
                </span>
            </div>
            <p class="stat-value">{{ $stats['echoues'] ?? 0 }}</p>
            <div class="stat-change">
                <i class="fas fa-bell"></i>
                Actions de relance recommandees
            </div>
        </article>
    </section>

    <section class="table-shell">
        <div class="table-container">
            <div class="header-section">
                <div class="section-title-wrap">
                    <span class="section-kicker">Historique des rappels</span>
                    <h2>Suivi detaille des rappels SMS</h2>
                    <p>Consultez les rappels planifies, leurs dates d'envoi, leur statut et les actions rapides disponibles.</p>
                </div>

                <div class="table-actions">
                    <button class="btn-action" type="button">
                        <i class="fas fa-filter"></i>
                        Filtrer
                    </button>
                    <button class="btn-action" type="button">
                        <i class="fas fa-download"></i>
                        Exporter
                    </button>
                </div>
            </div>

            @if(($reminders ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator && $reminders->count())
                <div class="table-wrap">
                    <table class="sms-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Telephone</th>
                                <th>Rendez-vous</th>
                                <th>Date d'envoi</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reminders as $reminder)
                                <tr>
                                    <td class="patient-cell">
                                        <span class="patient-name">{{ $reminder->display_patient_name }}</span>
                                        <span class="patient-subtext">Rappel #{{ $reminder->id }}</span>
                                    </td>
                                    <td>
                                        <span class="date-cell">{{ $reminder->telephone ?: ($reminder->patient->telephone ?? '--') }}</span>
                                    </td>
                                    <td class="rdv-cell">
                                        <strong>{{ $reminder->display_rdv_date }}</strong>
                                        <span class="date-cell">{{ $reminder->display_doctor_name ? 'Dr. ' . $reminder->display_doctor_name : 'Medecin non renseigne' }}</span>
                                    </td>
                                    <td>
                                        <span class="date-cell">{{ $reminder->display_send_date }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $reminder->display_status_class }}">
                                            <i class="fas {{ $reminder->display_status_icon }}"></i>
                                            {{ $reminder->display_status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="{{ route('sms.show', $reminder) }}" class="btn-icon" title="Voir" aria-label="Voir rappel {{ $reminder->id }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sms.edit', $reminder) }}" class="btn-icon" title="Modifier" aria-label="Modifier rappel {{ $reminder->id }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form method="POST" action="{{ route('sms.resend', $reminder) }}">
                                                @csrf
                                                <button type="submit" class="btn-icon btn-danger-soft" title="Renvoyer" aria-label="Renvoyer rappel {{ $reminder->id }}" onclick="return confirm('Confirmer le renvoi de ce SMS ?')">
                                                    <i class="fas fa-rotate-right"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-inner">
                        <span class="empty-state-icon">
                            <i class="fas fa-message"></i>
                        </span>
                        <h3>Vous n'avez aucun rappel SMS pour le moment</h3>
                        <p style="color: var(--sms-muted);">Commencez par planifier un rappel pour un rendez-vous a venir afin de suivre vos envois depuis ce tableau centralise.</p>
                        <a href="{{ route('sms.create') }}" class="sms-head-btn success">
                            <i class="fas fa-plus"></i>
                            Nouveau rappel
                        </a>
                    </div>
                </div>
            @endif

            @if(($reminders ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator && $reminders->hasPages())
                <div class="pagination-wrap">
                    {{ $reminders->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
