@extends('layouts.app')

@section('title', 'Historique des SMS')
@section('topbar_subtitle', 'Journal des rappels SMS envoyes, planifies ou en echec.')

@section('content')
<style>
    :root {
        --sms-primary: #1274d8;
        --sms-primary-dark: #0f5cad;
        --sms-success: #1c9b74;
        --sms-danger: #cf4d5d;
        --sms-warning: #c88414;
        --sms-ink: #17324d;
        --sms-muted: #637b94;
        --sms-line: #d9e6f2;
        --sms-soft: #eff6fc;
        --sms-surface: rgba(255, 255, 255, 0.92);
        --sms-shadow: 0 24px 42px -34px rgba(15, 36, 64, 0.42);
    }

    .sms-logs-page {
        min-height: 100%;
        padding: 18px clamp(12px, 1.8vw, 26px) 30px;
        border-radius: 22px;
        border: 1px solid #e0ebf5;
        background:
            radial-gradient(circle at top right, rgba(18, 116, 216, 0.14) 0%, transparent 32%),
            radial-gradient(circle at bottom left, rgba(28, 155, 116, 0.08) 0%, transparent 28%),
            linear-gradient(140deg, #f5f9fd 0%, #fbfdff 100%);
        box-shadow: 0 24px 42px -36px rgba(15, 36, 64, 0.72);
    }

    .sms-hero,
    .sms-hero-side,
    .sms-kpi,
    .sms-filters-card,
    .sms-table-shell {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        border: 1px solid var(--sms-line);
        background: var(--sms-surface);
        box-shadow: var(--sms-shadow);
        backdrop-filter: blur(10px);
    }

    .sms-hero,
    .sms-hero-side,
    .sms-filters-card,
    .sms-table-shell {
        padding: clamp(20px, 2.3vw, 30px);
    }

    .sms-hero::before,
    .sms-hero-side::before,
    .sms-table-shell::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), transparent 55%);
    }

    .sms-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(280px, 0.85fr);
        gap: 18px;
        margin-bottom: 22px;
    }

    .sms-hero-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .sms-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 12px;
        border: 1px solid #cfdeee;
        background: rgba(255, 255, 255, 0.72);
        color: #4a6682;
        font-size: 0.9rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .sms-back-btn:hover {
        color: #1f4d7a;
        border-color: #bdd2e7;
        background: #f4f9fe;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-back-icon {
        width: 24px;
        height: 24px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(18, 116, 216, 0.10);
        color: var(--sms-primary);
    }

    .sms-badge-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sms-badge,
    .sms-chip,
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        font-weight: 700;
        white-space: nowrap;
    }

    .sms-badge {
        background: linear-gradient(135deg, var(--sms-primary) 0%, #0f4f93 100%);
        color: #fff;
        box-shadow: 0 14px 24px -22px rgba(18, 116, 216, 0.9);
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

    .sms-title-row {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .sms-title-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(18, 116, 216, 0.14), rgba(18, 116, 216, 0.04));
        color: var(--sms-primary);
        font-size: 1.35rem;
        box-shadow: inset 0 0 0 1px rgba(18, 116, 216, 0.08);
    }

    .sms-title-block h1 {
        margin: 0;
        color: #123355;
        font-size: clamp(1.7rem, 2.5vw, 2.2rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .sms-title-block p {
        margin: 8px 0 0;
        max-width: 760px;
        color: var(--sms-muted);
        font-size: 0.98rem;
        line-height: 1.6;
        font-weight: 500;
    }

    .sms-head-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-end;
        margin-top: 22px;
    }

    .sms-head-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 13px;
        font-size: 0.94rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .sms-head-btn.secondary {
        background: #eff4fa;
        border-color: #d9e5f1;
        color: #46637d;
    }

    .sms-head-btn.secondary:hover {
        background: #e5eef8;
        color: #274c72;
        text-decoration: none;
    }

    .sms-head-btn.primary {
        background: linear-gradient(135deg, var(--sms-success) 0%, #168062 100%);
        color: #fff;
        box-shadow: 0 14px 24px -22px rgba(28, 155, 116, 0.92);
    }

    .sms-head-btn.primary:hover {
        color: #fff;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-hero-side {
        display: grid;
        gap: 12px;
        align-content: space-between;
        background: linear-gradient(180deg, rgba(243, 248, 253, 0.96) 0%, rgba(255, 255, 255, 0.96) 100%);
    }

    .sms-side-label {
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #7b93ad;
    }

    .sms-side-value {
        margin: 8px 0 0;
        color: var(--sms-ink);
        font-size: clamp(2rem, 3vw, 2.5rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .sms-side-text {
        margin: 10px 0 0;
        color: var(--sms-muted);
        font-size: 0.95rem;
        line-height: 1.55;
    }

    .sms-side-metrics {
        display: grid;
        gap: 10px;
    }

    .sms-side-metric {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        background: #f6fafe;
        border: 1px solid #dce8f3;
    }

    .sms-side-metric span {
        color: #55708a;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .sms-side-metric strong {
        color: var(--sms-ink);
        font-size: 1rem;
        font-weight: 800;
    }

    .sms-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 18px;
    }

    .sms-kpi {
        padding: 22px;
    }

    .sms-kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sms-kpi-label {
        margin: 0 0 6px;
        color: #64809d;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .sms-kpi-caption {
        color: var(--sms-muted);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .sms-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sms-kpi.total .sms-kpi-icon {
        background: rgba(18, 116, 216, 0.10);
        color: var(--sms-primary);
    }

    .sms-kpi.sent .sms-kpi-icon {
        background: rgba(28, 155, 116, 0.12);
        color: var(--sms-success);
    }

    .sms-kpi.failed .sms-kpi-icon {
        background: rgba(207, 77, 93, 0.12);
        color: var(--sms-danger);
    }

    .sms-kpi.off .sms-kpi-icon {
        background: rgba(200, 132, 20, 0.12);
        color: var(--sms-warning);
    }

    .sms-kpi-value {
        margin: 0;
        color: var(--sms-ink);
        font-size: clamp(1.9rem, 3vw, 2.4rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.05em;
    }

    .sms-filters-card {
        margin-bottom: 18px;
    }

    .sms-filters-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .sms-filters-top h2 {
        margin: 0;
        color: var(--sms-ink);
        font-size: 1.16rem;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .sms-filters-top p {
        margin: 6px 0 0;
        color: var(--sms-muted);
        font-size: 0.92rem;
    }

    .sms-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        gap: 12px;
        align-items: end;
    }

    .sms-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sms-label {
        margin: 0;
        font-size: 0.8rem;
        font-weight: 800;
        color: #38506a;
        text-transform: uppercase;
        letter-spacing: 0.14em;
    }

    .sms-select {
        width: 100%;
        min-height: 50px;
        border: 1px solid #cddceb;
        border-radius: 14px;
        background: #fff;
        color: #1e293b;
        font-size: 0.96rem;
        padding: 0 16px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .sms-select:focus {
        outline: none;
        border-color: #7eb9f5;
        box-shadow: 0 0 0 4px rgba(18, 116, 216, 0.12);
        transform: translateY(-1px);
    }

    .sms-filter-btn {
        min-height: 50px;
        border-radius: 14px;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 18px;
        font-weight: 800;
        font-size: 0.94rem;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .sms-filter-btn.primary {
        background: linear-gradient(135deg, var(--sms-primary) 0%, var(--sms-primary-dark) 100%);
        color: #fff;
        box-shadow: 0 18px 28px -22px rgba(18, 116, 216, 0.9);
    }

    .sms-filter-btn.secondary {
        background: #eef3f8;
        border-color: #dbe6f1;
        color: #47627d;
    }

    .sms-filter-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-table-shell {
        padding: 18px;
    }

    .sms-table-card {
        border-radius: 18px;
        border: 1px solid var(--sms-line);
        overflow: hidden;
        background: #fff;
    }

    .sms-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px 20px;
        border-bottom: 1px solid #e6eef7;
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9fd 100%);
    }

    .sms-table-header h2 {
        margin: 0;
        color: var(--sms-ink);
        font-size: 1.2rem;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .sms-table-header p {
        margin: 6px 0 0;
        color: var(--sms-muted);
        font-size: 0.92rem;
    }

    .sms-table-wrap {
        overflow-x: auto;
    }

    .sms-table {
        width: 100%;
        min-width: 1080px;
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

    .sms-table tbody tr:hover {
        background: #f8fbfe;
    }

    .patient-name {
        display: block;
        color: var(--sms-ink);
        font-size: 0.98rem;
        font-weight: 700;
    }

    .cell-subtext {
        display: block;
        margin-top: 4px;
        color: var(--sms-muted);
        font-size: 0.86rem;
    }

    .status-pill.is-planifie {
        background: rgba(18, 116, 216, 0.10);
        color: var(--sms-primary-dark);
    }

    .status-pill.is-envoye {
        background: rgba(28, 155, 116, 0.12);
        color: #167657;
    }

    .status-pill.is-echec {
        background: rgba(207, 77, 93, 0.12);
        color: #a73d4a;
    }

    .status-pill.is-desactive {
        background: rgba(200, 132, 20, 0.12);
        color: #9b680f;
    }

    .sms-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sms-actions form {
        margin: 0;
    }

    .sms-icon-btn {
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
        text-decoration: none;
    }

    .sms-icon-btn:hover {
        color: #fff;
        border-color: var(--sms-primary);
        background: var(--sms-primary);
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-icon-btn.danger {
        background: rgba(207, 77, 93, 0.08);
        border-color: rgba(207, 77, 93, 0.18);
        color: #bb4253;
    }

    .sms-icon-btn.danger:hover {
        background: var(--sms-danger);
        border-color: var(--sms-danger);
        color: #fff;
    }

    .sms-empty {
        padding: 52px 20px;
        text-align: center;
    }

    .sms-empty-icon {
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
        margin-bottom: 14px;
    }

    .sms-empty h3 {
        margin: 0;
        color: var(--sms-ink);
        font-size: 1.3rem;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .sms-empty p {
        margin: 10px auto 0;
        max-width: 540px;
        color: var(--sms-muted);
        font-size: 0.98rem;
        line-height: 1.7;
    }

    .sms-pagination {
        padding: 18px 22px;
        border-top: 1px solid #e6eef7;
        background: #fbfdff;
    }

    html.dark .sms-logs-page,
    body.dark-mode .sms-logs-page {
        --sms-ink: #e5effb;
        --sms-muted: #a8bed4;
        border-color: #284763;
        background:
            radial-gradient(circle at top right, rgba(87, 156, 255, 0.14) 0%, transparent 30%),
            radial-gradient(circle at bottom left, rgba(28, 155, 116, 0.10) 0%, transparent 28%),
            linear-gradient(140deg, #0e1a2b 0%, #102033 100%);
        box-shadow: 0 24px 42px -34px rgba(0, 0, 0, 0.9);
    }

    html.dark .sms-hero,
    html.dark .sms-hero-side,
    html.dark .sms-kpi,
    html.dark .sms-filters-card,
    html.dark .sms-table-shell,
    html.dark .sms-table-card,
    body.dark-mode .sms-hero,
    body.dark-mode .sms-hero-side,
    body.dark-mode .sms-kpi,
    body.dark-mode .sms-filters-card,
    body.dark-mode .sms-table-shell,
    body.dark-mode .sms-table-card {
        background: rgba(17, 34, 53, 0.92);
        border-color: #2f4f6e;
        box-shadow: 0 22px 36px -28px rgba(0, 0, 0, 0.55);
    }

    html.dark .sms-hero-side,
    body.dark-mode .sms-hero-side {
        background: linear-gradient(180deg, rgba(19, 38, 60, 0.96) 0%, rgba(17, 34, 53, 0.96) 100%);
    }

    html.dark .sms-title-block h1,
    html.dark .sms-side-value,
    html.dark .sms-filters-top h2,
    html.dark .sms-table-header h2,
    html.dark .sms-kpi-value,
    html.dark .patient-name,
    html.dark .sms-table td,
    html.dark .sms-table th,
    html.dark .sms-empty h3,
    body.dark-mode .sms-title-block h1,
    body.dark-mode .sms-side-value,
    body.dark-mode .sms-filters-top h2,
    body.dark-mode .sms-table-header h2,
    body.dark-mode .sms-kpi-value,
    body.dark-mode .patient-name,
    body.dark-mode .sms-table td,
    body.dark-mode .sms-table th,
    body.dark-mode .sms-empty h3 {
        color: #e5effb;
    }

    html.dark .sms-title-block p,
    html.dark .sms-side-text,
    html.dark .sms-side-label,
    html.dark .sms-side-metric span,
    html.dark .sms-kpi-label,
    html.dark .sms-kpi-caption,
    html.dark .sms-filters-top p,
    html.dark .cell-subtext,
    html.dark .sms-empty p,
    html.dark .sms-label,
    body.dark-mode .sms-title-block p,
    body.dark-mode .sms-side-text,
    body.dark-mode .sms-side-label,
    body.dark-mode .sms-side-metric span,
    body.dark-mode .sms-kpi-label,
    body.dark-mode .sms-kpi-caption,
    body.dark-mode .sms-filters-top p,
    body.dark-mode .cell-subtext,
    body.dark-mode .sms-empty p,
    body.dark-mode .sms-label {
        color: #a8bed4;
    }

    html.dark .sms-chip,
    html.dark .sms-side-metric,
    html.dark .sms-filter-btn.secondary,
    html.dark .sms-icon-btn,
    body.dark-mode .sms-chip,
    body.dark-mode .sms-side-metric,
    body.dark-mode .sms-filter-btn.secondary,
    body.dark-mode .sms-icon-btn {
        background: #17314c;
        border-color: #355777;
        color: #d5e7fb;
    }

    html.dark .sms-back-btn,
    body.dark-mode .sms-back-btn {
        background: #17314d;
        border-color: #355777;
        color: #d5e7fb;
    }

    html.dark .sms-back-icon,
    body.dark-mode .sms-back-icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    html.dark .sms-back-btn:hover,
    html.dark .sms-head-btn.secondary:hover,
    html.dark .sms-filter-btn.secondary:hover,
    html.dark .sms-icon-btn:hover,
    body.dark-mode .sms-back-btn:hover,
    body.dark-mode .sms-head-btn.secondary:hover,
    body.dark-mode .sms-filter-btn.secondary:hover,
    body.dark-mode .sms-icon-btn:hover {
        background: #21486f;
        border-color: #4d7aa5;
        color: #fff;
    }

    html.dark .sms-select,
    body.dark-mode .sms-select {
        background: #13263f;
        border-color: #355985;
        color: #deebf9;
    }

    html.dark .sms-table-header,
    html.dark .sms-table thead,
    html.dark .sms-pagination,
    body.dark-mode .sms-table-header,
    body.dark-mode .sms-table thead,
    body.dark-mode .sms-pagination {
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

    @media (max-width: 1199px) {
        .sms-hero-grid {
            grid-template-columns: 1fr;
        }

        .sms-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 992px) {
        .sms-filter-form {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .sms-logs-page {
            padding: 12px;
            border-radius: 18px;
        }

        .sms-hero,
        .sms-hero-side,
        .sms-kpi,
        .sms-filters-card,
        .sms-table-shell,
        .sms-table-card {
            border-radius: 18px;
        }

        .sms-top-mobile,
        .sms-title-row,
        .sms-badge-row,
        .sms-head-actions {
            align-items: stretch;
        }

        .sms-back-btn,
        .sms-head-btn,
        .sms-filter-btn {
            width: 100%;
        }

        .sms-head-actions {
            display: grid;
        }

        .sms-kpis {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="sms-logs-page">
    <div class="sms-hero-grid">
        <section class="sms-hero">
            <div class="sms-hero-top sms-top-mobile">
                <a href="{{ route('sms.index') }}" class="sms-back-btn">
                    <span class="sms-back-icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Retour</span>
                </a>

                <div class="sms-badge-row">
                    <span class="sms-badge">
                        <i class="fas fa-history"></i>
                        {{ $stats['total'] ?? 0 }} SMS
                    </span>
                    <span class="sms-chip">
                        <i class="fas fa-filter"></i>
                        {{ $selectedStatut !== '' ? ucfirst($selectedStatut) : 'Tous les statuts' }}
                    </span>
                </div>
            </div>

            <div class="sms-title-row">
                <span class="sms-title-icon"><i class="fas fa-clock-rotate-left"></i></span>
                <div class="sms-title-block">
                    <h1>Historique des SMS</h1>
                    <p>Consultez les rappels planifies, envoyes, desactives ou en echec depuis une vue plus claire, plus premium et mieux structuree.</p>
                </div>
            </div>

            <div class="sms-head-actions">
                <a href="{{ route('sms.index') }}" class="sms-head-btn secondary">
                    <i class="fas fa-list"></i>
                    Liste rappels
                </a>
                <a href="{{ route('sms.create') }}" class="sms-head-btn primary">
                    <i class="fas fa-plus"></i>
                    Nouveau rappel
                </a>
            </div>
        </section>

        <aside class="sms-hero-side">
            <div>
                <div class="sms-side-label">Vue d ensemble</div>
                <p class="sms-side-value">{{ $reminders->total() }}</p>
                <p class="sms-side-text">Resultats affiches dans l historique avec filtre actuellement applique et pagination preservee.</p>
            </div>

            <div class="sms-side-metrics">
                <div class="sms-side-metric">
                    <span>Page courante</span>
                    <strong>{{ $reminders->count() }}</strong>
                </div>
                <div class="sms-side-metric">
                    <span>Planifies</span>
                    <strong>{{ $stats['planifies'] ?? 0 }}</strong>
                </div>
                <div class="sms-side-metric">
                    <span>Envoyes</span>
                    <strong>{{ $stats['envoyes'] ?? 0 }}</strong>
                </div>
            </div>
        </aside>
    </div>

    <section class="sms-kpis">
        <article class="sms-kpi total">
            <div class="sms-kpi-top">
                <div>
                    <p class="sms-kpi-label">Total</p>
                    <div class="sms-kpi-caption">Historique complet des rappels SMS</div>
                </div>
                <span class="sms-kpi-icon"><i class="fas fa-layer-group"></i></span>
            </div>
            <p class="sms-kpi-value">{{ $stats['total'] ?? 0 }}</p>
        </article>

        <article class="sms-kpi sent">
            <div class="sms-kpi-top">
                <div>
                    <p class="sms-kpi-label">Envoyes</p>
                    <div class="sms-kpi-caption">Messages transmis avec succes</div>
                </div>
                <span class="sms-kpi-icon"><i class="fas fa-paper-plane"></i></span>
            </div>
            <p class="sms-kpi-value">{{ $stats['envoyes'] ?? 0 }}</p>
        </article>

        <article class="sms-kpi failed">
            <div class="sms-kpi-top">
                <div>
                    <p class="sms-kpi-label">Echecs</p>
                    <div class="sms-kpi-caption">Rappels necessitant une reprise</div>
                </div>
                <span class="sms-kpi-icon"><i class="fas fa-triangle-exclamation"></i></span>
            </div>
            <p class="sms-kpi-value">{{ $stats['echoues'] ?? 0 }}</p>
        </article>

        <article class="sms-kpi off">
            <div class="sms-kpi-top">
                <div>
                    <p class="sms-kpi-label">Desactives</p>
                    <div class="sms-kpi-caption">Rappels suspendus ou annules</div>
                </div>
                <span class="sms-kpi-icon"><i class="fas fa-pause"></i></span>
            </div>
            <p class="sms-kpi-value">{{ $stats['desactives'] ?? 0 }}</p>
        </article>
    </section>

    <section class="sms-filters-card">
        <div class="sms-filters-top">
            <div>
                <h2>Filtres de lecture</h2>
                <p>Affinez l historique par statut sans perdre la pagination ni le contexte visuel.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('sms.logs') }}" class="sms-filter-form">
            <div class="sms-field">
                <label class="sms-label" for="statut">Statut</label>
                <select name="statut" id="statut" class="sms-select">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $statut)
                        <option value="{{ $statut }}" {{ $selectedStatut === $statut ? 'selected' : '' }}>{{ ucfirst($statut) }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="sms-filter-btn primary">
                <i class="fas fa-filter"></i>
                Filtrer
            </button>

            <a href="{{ route('sms.logs') }}" class="sms-filter-btn secondary">
                <i class="fas fa-rotate-left"></i>
                Reinitialiser
            </a>
        </form>
    </section>

    <section class="sms-table-shell">
        <div class="sms-table-card">
            <div class="sms-table-header">
                <div>
                    <h2>Journal des rappels SMS</h2>
                    <p>{{ $reminders->total() }} resultat(s) trouves dans l historique.</p>
                </div>
            </div>

            @if($reminders->count())
                <div class="sms-table-wrap">
                    <table class="sms-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Telephone</th>
                                <th>Rendez-vous</th>
                                <th>Envoi prevu</th>
                                <th>Envoi reel</th>
                                <th>Statut</th>
                                <th>Provider</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reminders as $reminder)
                                <tr>
                                    <td>
                                        <span class="patient-name">{{ $reminder->display_patient_name }}</span>
                                        <span class="cell-subtext">Rappel #{{ $reminder->id }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $reminder->telephone ?? '--' }}</span>
                                    </td>
                                    <td>
                                        <span class="patient-name">{{ $reminder->display_rdv_date }}</span>
                                        <span class="cell-subtext">{{ $reminder->display_doctor_name ? 'Dr. ' . $reminder->display_doctor_name : 'Medecin non renseigne' }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $reminder->display_send_date }}</span>
                                    </td>
                                    <td>
                                        <span>{{ optional($reminder->date_envoi_reelle)->format('d/m/Y H:i') ?: '--' }}</span>
                                    </td>
                                    <td>
                                        <span class="status-pill is-{{ $reminder->statut }}">
                                            <i class="fas fa-circle"></i>
                                            {{ $reminder->display_status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span>{{ $reminder->provider ?: '--' }}</span>
                                    </td>
                                    <td>
                                        <div class="sms-actions">
                                            <a href="{{ route('sms.show', $reminder) }}" class="sms-icon-btn" title="Voir" aria-label="Voir rappel {{ $reminder->id }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sms.edit', $reminder) }}" class="sms-icon-btn" title="Modifier" aria-label="Modifier rappel {{ $reminder->id }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form method="POST" action="{{ route('sms.resend', $reminder) }}">
                                                @csrf
                                                <button type="submit" class="sms-icon-btn danger" title="Renvoyer" aria-label="Renvoyer rappel {{ $reminder->id }}" onclick="return confirm('Confirmer le renvoi de ce SMS ?')">
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
                <div class="sms-empty">
                    <div class="sms-empty-icon"><i class="fas fa-inbox"></i></div>
                    <h3>Aucun rappel SMS dans l'historique</h3>
                    <p>Vous n'avez encore aucun rappel correspondant a ce filtre. Creez un rappel ou reinitialisez les filtres pour consulter l'ensemble du journal.</p>
                    <div class="sms-head-actions" style="justify-content: center; margin-top: 18px;">
                        <a href="{{ route('sms.logs') }}" class="sms-head-btn secondary">
                            <i class="fas fa-rotate-left"></i>
                            Voir tout l'historique
                        </a>
                        <a href="{{ route('sms.create') }}" class="sms-head-btn primary">
                            <i class="fas fa-plus"></i>
                            Nouveau rappel
                        </a>
                    </div>
                </div>
            @endif

            @if($reminders->hasPages())
                <div class="sms-pagination">
                    {{ $reminders->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
