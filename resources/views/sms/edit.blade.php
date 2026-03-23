@extends('layouts.app')

@section('title', 'Modifier rappel SMS')
@section('topbar_subtitle', 'Mettez a jour les parametres du rappel SMS.')

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

    .sms-edit-page {
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

    .sms-edit-hero,
    .sms-edit-side,
    .sms-edit-form,
    .sms-edit-section,
    .sms-edit-preview,
    .sms-edit-actions {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        border: 1px solid var(--sms-line);
        background: var(--sms-surface);
        box-shadow: var(--sms-shadow);
        backdrop-filter: blur(10px);
    }

    .sms-edit-hero,
    .sms-edit-side,
    .sms-edit-form,
    .sms-edit-preview {
        padding: clamp(20px, 2.3vw, 30px);
    }

    .sms-edit-hero::before,
    .sms-edit-side::before,
    .sms-edit-form::before,
    .sms-edit-preview::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), transparent 55%);
    }

    .sms-edit-top {
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

    .sms-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(300px, 0.85fr);
        gap: 18px;
        margin-top: 18px;
    }

    .sms-edit-main {
        display: grid;
        gap: 18px;
    }

    .sms-edit-side {
        display: grid;
        gap: 12px;
        align-content: start;
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

    .sms-edit-form {
        padding-bottom: 106px;
    }

    .sms-edit-error {
        margin-bottom: 16px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid #f2c3ca;
        background: #fff5f6;
    }

    .sms-edit-error strong {
        display: block;
        margin-bottom: 8px;
        color: #b63f4f;
    }

    .sms-edit-error ul {
        margin: 0;
        padding-left: 18px;
        color: #8e2f3c;
    }

    .sms-edit-section {
        padding: 22px;
    }

    .sms-edit-section + .sms-edit-section {
        margin-top: 18px;
    }

    .sms-section-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1px solid #eaf1f7;
    }

    .sms-section-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: 1px solid #d6e4f2;
        background: #edf5fd;
        color: #174a84;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sms-section-heading h2 {
        margin: 0;
        color: #153b84;
        font-size: 1.08rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .sms-section-heading p {
        margin: 4px 0 0;
        color: var(--sms-muted);
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .sms-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .sms-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sms-field.span-2 {
        grid-column: span 2;
    }

    .sms-label {
        margin: 0;
        font-size: 0.8rem;
        font-weight: 800;
        color: #38506a;
        text-transform: uppercase;
        letter-spacing: 0.14em;
    }

    .sms-hint {
        color: var(--sms-muted);
        font-size: 0.82rem;
        line-height: 1.45;
    }

    .sms-input,
    .sms-select,
    .sms-textarea {
        width: 100%;
        min-height: 52px;
        border: 1px solid #cddceb;
        border-radius: 14px;
        background: #fff;
        color: #1e293b;
        font-size: 0.96rem;
        padding: 14px 16px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        font-family: inherit;
    }

    .sms-input:focus,
    .sms-select:focus,
    .sms-textarea:focus {
        outline: none;
        border-color: #7eb9f5;
        box-shadow: 0 0 0 4px rgba(18, 116, 216, 0.12);
        transform: translateY(-1px);
    }

    .sms-textarea {
        min-height: 168px;
        resize: vertical;
    }

    .sms-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: 0.84rem;
        font-weight: 800;
        width: fit-content;
    }

    .sms-status-pill.is-planifie {
        background: rgba(18, 116, 216, 0.10);
        color: var(--sms-primary-dark);
    }

    .sms-status-pill.is-envoye {
        background: rgba(28, 155, 116, 0.12);
        color: #167657;
    }

    .sms-status-pill.is-echec {
        background: rgba(207, 77, 93, 0.12);
        color: #a73d4a;
    }

    .sms-status-pill.is-desactive {
        background: rgba(200, 132, 20, 0.12);
        color: #9b680f;
    }

    .sms-edit-preview h3 {
        margin: 0;
        color: var(--sms-ink);
        font-size: 1.16rem;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .sms-preview-kicker {
        margin: 0 0 8px;
        color: #6f8ba7;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .sms-preview-grid {
        margin-top: 16px;
        display: grid;
        gap: 10px;
    }

    .sms-preview-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid #dce8f3;
        background: #f6fafe;
    }

    .sms-preview-item span {
        color: #5b7690;
        font-size: 0.88rem;
        font-weight: 600;
    }

    .sms-preview-item strong {
        color: var(--sms-ink);
        font-size: 0.92rem;
        font-weight: 800;
        text-align: right;
    }

    .sms-bubble-wrap {
        margin-top: 16px;
        border-radius: 18px;
        border: 1px solid #dce8f3;
        background: linear-gradient(180deg, #f9fbfe 0%, #f2f7fc 100%);
        padding: 18px;
    }

    .sms-bubble-phone {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #d9e6f2;
        color: #47637e;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .sms-bubble-message {
        margin-top: 14px;
        padding: 16px 18px;
        border-radius: 18px 18px 18px 8px;
        background: linear-gradient(135deg, var(--sms-primary) 0%, var(--sms-primary-dark) 100%);
        color: #fff;
        font-size: 0.95rem;
        line-height: 1.65;
        min-height: 116px;
        white-space: pre-wrap;
        box-shadow: 0 20px 30px -26px rgba(18, 116, 216, 0.82);
    }

    .sms-edit-actions {
        position: sticky;
        bottom: 14px;
        margin-top: 18px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: rgba(255, 255, 255, 0.92);
        z-index: 5;
    }

    .sms-actions-copy strong {
        display: block;
        color: var(--sms-ink);
        font-size: 0.96rem;
        font-weight: 800;
    }

    .sms-actions-copy span {
        display: block;
        margin-top: 2px;
        color: var(--sms-muted);
        font-size: 0.84rem;
    }

    .sms-action-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .sms-btn {
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 800;
        font-size: 0.94rem;
        text-decoration: none;
        padding: 0 18px;
        transition: 0.2s ease;
    }

    .sms-btn.primary {
        color: #fff;
        background: linear-gradient(135deg, var(--sms-primary) 0%, #0f5cad 100%);
        box-shadow: 0 18px 28px -22px rgba(18, 116, 216, 0.9);
    }

    .sms-btn.primary:hover {
        color: #fff;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-btn.secondary {
        background: #eef3f8;
        border-color: #dbe6f1;
        color: #47627d;
    }

    .sms-btn.secondary:hover {
        background: #e2ebf4;
        color: #274c72;
        text-decoration: none;
    }

    .sms-btn.danger {
        background: rgba(207, 77, 93, 0.10);
        border-color: rgba(207, 77, 93, 0.18);
        color: #b74152;
    }

    .sms-btn.danger:hover {
        background: var(--sms-danger);
        border-color: var(--sms-danger);
        color: #fff;
    }

    html.dark .sms-edit-page,
    body.dark-mode .sms-edit-page {
        --sms-ink: #e5effb;
        --sms-muted: #a8bed4;
        border-color: #284763;
        background:
            radial-gradient(circle at top right, rgba(87, 156, 255, 0.14) 0%, transparent 30%),
            radial-gradient(circle at bottom left, rgba(28, 155, 116, 0.10) 0%, transparent 28%),
            linear-gradient(140deg, #0e1a2b 0%, #102033 100%);
        box-shadow: 0 24px 42px -34px rgba(0, 0, 0, 0.9);
    }

    html.dark .sms-edit-hero,
    html.dark .sms-edit-side,
    html.dark .sms-edit-form,
    html.dark .sms-edit-section,
    html.dark .sms-edit-preview,
    html.dark .sms-edit-actions,
    body.dark-mode .sms-edit-hero,
    body.dark-mode .sms-edit-side,
    body.dark-mode .sms-edit-form,
    body.dark-mode .sms-edit-section,
    body.dark-mode .sms-edit-preview,
    body.dark-mode .sms-edit-actions {
        background: rgba(17, 34, 53, 0.92);
        border-color: #2f4f6e;
        box-shadow: 0 22px 36px -28px rgba(0, 0, 0, 0.55);
    }

    html.dark .sms-edit-side,
    body.dark-mode .sms-edit-side {
        background: linear-gradient(180deg, rgba(19, 38, 60, 0.96) 0%, rgba(17, 34, 53, 0.96) 100%);
    }

    html.dark .sms-title-block h1,
    html.dark .sms-side-value,
    html.dark .sms-section-heading h2,
    html.dark .sms-label,
    html.dark .sms-edit-preview h3,
    html.dark .sms-preview-item strong,
    html.dark .sms-actions-copy strong,
    body.dark-mode .sms-title-block h1,
    body.dark-mode .sms-side-value,
    body.dark-mode .sms-section-heading h2,
    body.dark-mode .sms-label,
    body.dark-mode .sms-edit-preview h3,
    body.dark-mode .sms-preview-item strong,
    body.dark-mode .sms-actions-copy strong {
        color: #e5effb;
    }

    html.dark .sms-title-block p,
    html.dark .sms-side-text,
    html.dark .sms-side-label,
    html.dark .sms-side-metric span,
    html.dark .sms-section-heading p,
    html.dark .sms-hint,
    html.dark .sms-preview-kicker,
    html.dark .sms-preview-item span,
    html.dark .sms-actions-copy span,
    body.dark-mode .sms-title-block p,
    body.dark-mode .sms-side-text,
    body.dark-mode .sms-side-label,
    body.dark-mode .sms-side-metric span,
    body.dark-mode .sms-section-heading p,
    body.dark-mode .sms-hint,
    body.dark-mode .sms-preview-kicker,
    body.dark-mode .sms-preview-item span,
    body.dark-mode .sms-actions-copy span {
        color: #a8bed4;
    }

    html.dark .sms-chip,
    html.dark .sms-side-metric,
    html.dark .sms-preview-item,
    html.dark .sms-bubble-wrap,
    html.dark .sms-bubble-phone,
    html.dark .sms-btn.secondary,
    html.dark .sms-btn.danger,
    body.dark-mode .sms-chip,
    body.dark-mode .sms-side-metric,
    body.dark-mode .sms-preview-item,
    body.dark-mode .sms-bubble-wrap,
    body.dark-mode .sms-bubble-phone,
    body.dark-mode .sms-btn.secondary,
    body.dark-mode .sms-btn.danger {
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
    html.dark .sms-btn.secondary:hover,
    body.dark-mode .sms-back-btn:hover,
    body.dark-mode .sms-btn.secondary:hover {
        background: #21486f;
        border-color: #4d7aa5;
        color: #fff;
    }

    html.dark .sms-input,
    html.dark .sms-select,
    html.dark .sms-textarea,
    body.dark-mode .sms-input,
    body.dark-mode .sms-select,
    body.dark-mode .sms-textarea {
        background: #13263f;
        border-color: #355985;
        color: #deebf9;
    }

    html.dark .sms-section-header,
    body.dark-mode .sms-section-header {
        border-bottom-color: #29445f;
    }

    html.dark .sms-section-icon,
    body.dark-mode .sms-section-icon {
        background: #16324f;
        color: #8ec5ff;
        border-color: #2f4f72;
    }

    @media (max-width: 1199px) {
        .sms-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 992px) {
        .sms-grid {
            grid-template-columns: 1fr;
        }

        .sms-field.span-2 {
            grid-column: auto;
        }

        .sms-edit-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .sms-action-buttons {
            width: 100%;
            justify-content: stretch;
        }

        .sms-btn {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .sms-edit-page {
            padding: 12px;
            border-radius: 18px;
        }

        .sms-edit-hero,
        .sms-edit-side,
        .sms-edit-form,
        .sms-edit-section,
        .sms-edit-preview,
        .sms-edit-actions {
            border-radius: 18px;
        }

        .sms-edit-top,
        .sms-title-row,
        .sms-badge-row,
        .sms-head-actions {
            align-items: stretch;
        }

        .sms-back-btn,
        .sms-head-btn {
            width: 100%;
        }

        .sms-head-actions {
            display: grid;
        }
    }
</style>

<div class="sms-edit-page">
    <section class="sms-edit-hero">
        <div class="sms-edit-top">
            <a href="{{ route('sms.index') }}" class="sms-back-btn">
                <span class="sms-back-icon"><i class="fas fa-arrow-left"></i></span>
                <span>Retour</span>
            </a>

            <div class="sms-badge-row">
                <span class="sms-badge">
                    <i class="fas fa-pen"></i>
                    Rappel #{{ $reminder->id }}
                </span>
                <span class="sms-chip">
                    <i class="fas fa-user"></i>
                    {{ $patientName ?: 'Patient inconnu' }}
                </span>
            </div>
        </div>

        <div class="sms-title-row">
            <span class="sms-title-icon"><i class="fas fa-pen-to-square"></i></span>
            <div class="sms-title-block">
                <h1>Modifier rappel SMS</h1>
                <p>Ajustez la date, le contenu et le statut du rappel dans une interface plus claire, plus lisible et mieux harmonisee avec le module SMS.</p>
            </div>
        </div>

        <div class="sms-head-actions">
            <a href="{{ route('sms.show', $reminder) }}" class="sms-head-btn secondary">
                <i class="fas fa-eye"></i>
                Voir le detail
            </a>
            <a href="{{ route('sms.index') }}" class="sms-head-btn primary">
                <i class="fas fa-list"></i>
                Liste rappels
            </a>
        </div>
    </section>

    <div class="sms-layout">
        <div class="sms-edit-main">
            <div class="sms-edit-form">
                @if($errors->any())
                    <div class="sms-edit-error">
                        <strong>Veuillez corriger les erreurs</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('sms.update', $reminder) }}">
                    @csrf
                    @method('PUT')

                    <section class="sms-edit-section">
                        <div class="sms-section-header">
                            <span class="sms-section-icon"><i class="fas fa-calendar-alt"></i></span>
                            <div class="sms-section-heading">
                                <h2>Planification</h2>
                                <p>Selectionnez le rendez-vous, verifiez les coordonnees et recalculez la date d envoi si necessaire.</p>
                            </div>
                        </div>

                        <div class="sms-grid">
                            <div class="sms-field">
                                <label class="sms-label">Rendez-vous</label>
                                <select name="rendezvous_id" id="rendezvousSelect" class="sms-select" required>
                                    @foreach(($rendezvousList ?? []) as $rdv)
                                        <option
                                            value="{{ optional($rdv)->id }}"
                                            data-patient="{{ trim((optional(optional($rdv)->patient)->prenom ?? '') . ' ' . (optional(optional($rdv)->patient)->nom ?? '')) }}"
                                            data-phone="{{ optional(optional($rdv)->patient)->telephone ?? '' }}"
                                            data-date="{{ optional(optional($rdv)->date_heure)->format('Y-m-d\\TH:i') }}"
                                            {{ (string) old('rendezvous_id', $reminder->rendezvous_id) === (string) optional($rdv)->id ? 'selected' : '' }}
                                        >
                                            {{ optional(optional($rdv)->date_heure)->format('d/m/Y H:i') ?: '--' }} - {{ trim((optional(optional($rdv)->patient)->prenom ?? '') . ' ' . (optional(optional($rdv)->patient)->nom ?? '')) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sms-field">
                                <label class="sms-label">Telephone</label>
                                <input
                                    type="tel"
                                    class="sms-input"
                                    id="telephoneInput"
                                    name="telephone"
                                    pattern="^(\+212|0)[0-9]{9}$"
                                    value="{{ old('telephone', $reminder->telephone) }}"
                                    required
                                >
                                <span class="sms-hint">Format attendu: +212XXXXXXXXX ou 0XXXXXXXXX.</span>
                            </div>

                            <div class="sms-field">
                                <label class="sms-label">Date d envoi prevue</label>
                                <input
                                    type="datetime-local"
                                    class="sms-input"
                                    id="sendDateInput"
                                    name="date_envoi_prevue"
                                    value="{{ old('date_envoi_prevue', optional($reminder->date_envoi_prevue)->format('Y-m-d\\TH:i')) }}"
                                >
                            </div>

                            <div class="sms-field">
                                <label class="sms-label">Heures avant</label>
                                <input
                                    type="number"
                                    min="1"
                                    max="72"
                                    class="sms-input"
                                    name="heures_avant"
                                    value="{{ old('heures_avant', $reminder->heures_avant ?? 24) }}"
                                >
                            </div>
                        </div>
                    </section>

                    <section class="sms-edit-section">
                        <div class="sms-section-header">
                            <span class="sms-section-icon"><i class="fas fa-signal"></i></span>
                            <div class="sms-section-heading">
                                <h2>Statut et contenu</h2>
                                <p>Gardez une vue claire sur l etat du rappel et adaptez le message si besoin.</p>
                            </div>
                        </div>

                        <div class="sms-grid">
                            <div class="sms-field">
                                <label class="sms-label">Statut</label>
                                <select name="statut" id="statusSelect" class="sms-select">
                                    @foreach(['planifie', 'envoye', 'echec', 'desactive'] as $statut)
                                        <option value="{{ $statut }}" {{ old('statut', $reminder->statut) === $statut ? 'selected' : '' }}>
                                            {{ ucfirst($statut) }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="sms-status-pill is-{{ old('statut', $reminder->statut) }}" id="statusPreview">
                                    <i class="fas fa-circle"></i>
                                    {{ ucfirst(old('statut', $reminder->statut)) }}
                                </span>
                            </div>

                            <div class="sms-field span-2">
                                <label class="sms-label">Message</label>
                                <textarea
                                    name="message_template"
                                    id="messageTemplate"
                                    class="sms-textarea"
                                    maxlength="1000"
                                    placeholder="Message SMS..."
                                >{{ old('message_template', $reminder->message_template) }}</textarea>
                                <span class="sms-hint">Le contenu est mis a jour en direct dans l apercu a droite.</span>
                            </div>
                        </div>
                    </section>

                    <div class="sms-edit-actions">
                        <div class="sms-actions-copy">
                            <strong>Enregistrer les modifications</strong>
                            <span>Vous pouvez aussi renvoyer le SMS immediatement si une correction est urgente.</span>
                        </div>

                        <div class="sms-action-buttons">
                            <button type="button" class="sms-btn danger" onclick="if(confirm('Confirmer le renvoi de ce SMS ?')){ document.getElementById('resendForm').submit(); }">
                                <i class="fas fa-rotate-right"></i>
                                Renvoyer maintenant
                            </button>
                            <button type="submit" class="sms-btn primary">
                                <i class="fas fa-save"></i>
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </form>

                <form id="resendForm" method="POST" action="{{ route('sms.resend', $reminder) }}" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <aside class="sms-edit-side">
            <div>
                <div class="sms-side-label">Rappel en cours</div>
                <p class="sms-side-value">#{{ $reminder->id }}</p>
                <p class="sms-side-text">Visualisez immediatement le patient, la date d envoi et le rendu du message avant enregistrement.</p>
            </div>

            <div class="sms-side-metrics">
                <div class="sms-side-metric">
                    <span>Patient</span>
                    <strong id="previewPatient">{{ $patientName ?: 'Patient inconnu' }}</strong>
                </div>
                <div class="sms-side-metric">
                    <span>Telephone</span>
                    <strong id="previewPhoneMetric">{{ $reminder->telephone ?: '--' }}</strong>
                </div>
                <div class="sms-side-metric">
                    <span>Envoi prevu</span>
                    <strong id="previewDateMetric">{{ optional($reminder->date_envoi_prevue)->format('d/m/Y H:i') ?: '--' }}</strong>
                </div>
            </div>

            <section class="sms-edit-preview">
                <p class="sms-preview-kicker">Apercu du SMS</p>
                <h3>Message au patient</h3>

                <div class="sms-preview-grid">
                    <div class="sms-preview-item">
                        <span>Statut</span>
                        <strong id="previewStatusText">{{ ucfirst(old('statut', $reminder->statut)) }}</strong>
                    </div>
                    <div class="sms-preview-item">
                        <span>Envoi prevu</span>
                        <strong id="previewDateText">{{ optional($reminder->date_envoi_prevue)->format('d/m/Y H:i') ?: '--' }}</strong>
                    </div>
                </div>

                <div class="sms-bubble-wrap">
                    <span class="sms-bubble-phone" id="previewPhoneBubble">
                        <i class="fas fa-phone"></i>
                        {{ $reminder->telephone ?: 'Numero non renseigne' }}
                    </span>
                    <div class="sms-bubble-message" id="messagePreview">{{ old('message_template', $reminder->message_template) ?: 'Aucun message personnalise' }}</div>
                </div>
            </section>
        </aside>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rdvSelect = document.getElementById('rendezvousSelect');
    const phoneInput = document.getElementById('telephoneInput');
    const sendDateInput = document.getElementById('sendDateInput');
    const statusSelect = document.getElementById('statusSelect');
    const messageTemplate = document.getElementById('messageTemplate');

    const previewPatient = document.getElementById('previewPatient');
    const previewPhoneMetric = document.getElementById('previewPhoneMetric');
    const previewDateMetric = document.getElementById('previewDateMetric');
    const previewStatusText = document.getElementById('previewStatusText');
    const previewDateText = document.getElementById('previewDateText');
    const previewPhoneBubble = document.getElementById('previewPhoneBubble');
    const messagePreview = document.getElementById('messagePreview');
    const statusPreview = document.getElementById('statusPreview');

    function formatPreviewDate(value) {
        if (!value) return '--';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '--';
        return date.toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function refreshPreview() {
        if (rdvSelect && previewPatient) {
            const selected = rdvSelect.options[rdvSelect.selectedIndex];
            const patient = selected?.dataset?.patient || 'Patient inconnu';
            previewPatient.textContent = patient;
        }

        if (phoneInput) {
            const phone = phoneInput.value.trim() || 'Numero non renseigne';
            if (previewPhoneMetric) previewPhoneMetric.textContent = phone;
            if (previewPhoneBubble) previewPhoneBubble.innerHTML = `<i class="fas fa-phone"></i>${phone}`;
        }

        if (sendDateInput) {
            const formattedDate = formatPreviewDate(sendDateInput.value);
            if (previewDateMetric) previewDateMetric.textContent = formattedDate;
            if (previewDateText) previewDateText.textContent = formattedDate;
        }

        if (statusSelect) {
            const value = statusSelect.value || 'planifie';
            const label = value.charAt(0).toUpperCase() + value.slice(1);
            if (previewStatusText) previewStatusText.textContent = label;
            if (statusPreview) {
                statusPreview.className = `sms-status-pill is-${value}`;
                statusPreview.innerHTML = `<i class="fas fa-circle"></i>${label}`;
            }
        }

        if (messageTemplate && messagePreview) {
            messagePreview.textContent = messageTemplate.value.trim() || 'Aucun message personnalise';
        }
    }

    function toReminderDate(rdvDateIso) {
        if (!rdvDateIso) return '';
        const d = new Date(rdvDateIso);
        if (Number.isNaN(d.getTime())) return '';
        d.setHours(d.getHours() - 24);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const hh = String(d.getHours()).padStart(2, '0');
        const mi = String(d.getMinutes()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}T${hh}:${mi}`;
    }

    if (rdvSelect) {
        rdvSelect.addEventListener('change', function () {
            const selected = rdvSelect.options[rdvSelect.selectedIndex];
            const phone = selected?.dataset?.phone || '';
            const rdvDate = selected?.dataset?.date || '';

            if (phone) {
                phoneInput.value = phone;
            }

            if (!sendDateInput.value && rdvDate) {
                sendDateInput.value = toReminderDate(rdvDate);
            }

            refreshPreview();
        });
    }

    [phoneInput, sendDateInput, statusSelect, messageTemplate].forEach((field) => {
        if (field) {
            field.addEventListener('input', refreshPreview);
            field.addEventListener('change', refreshPreview);
        }
    });

    refreshPreview();
});
</script>
@endsection

