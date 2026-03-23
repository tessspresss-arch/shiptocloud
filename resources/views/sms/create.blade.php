@extends('layouts.app')

@section('title', 'Nouveau Rappel SMS')
@section('topbar_subtitle', 'Preparation d un rappel SMS automatise pour un rendez-vous.')

@section('content')
<style>
    :root {
        --sms-primary: #1274d8;
        --sms-primary-dark: #0f5cad;
        --sms-success: #1c9b74;
        --sms-danger: #cf4d5d;
        --sms-ink: #17324d;
        --sms-muted: #637b94;
        --sms-line: #d9e6f2;
        --sms-soft: #eff6fc;
        --sms-soft-info: #f4f9ff;
        --sms-surface: rgba(255, 255, 255, 0.92);
        --sms-shadow: 0 24px 42px -34px rgba(15, 36, 64, 0.42);
    }

    .sms-form-page {
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

    .sms-create-shell {
        width: 100%;
        max-width: none;
        margin: 0 auto;
    }

    .sms-create-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
        margin-bottom: 18px;
    }

    .sms-create-card,
    .form-wrapper,
    .form-section,
    .preview-panel,
    .sticky-actions {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        border: 1px solid var(--sms-line);
        background: var(--sms-surface);
        box-shadow: var(--sms-shadow);
        backdrop-filter: blur(10px);
    }

    .sms-create-card,
    .form-wrapper {
        padding: clamp(20px, 2.3vw, 30px);
    }

    .sms-create-card::before,
    .form-wrapper::before,
    .preview-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), transparent 55%);
    }

    .sms-create-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .sms-create-back-btn {
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

    .sms-create-back-btn:hover {
        color: #1f4d7a;
        border-color: #bdd2e7;
        background: #f4f9fe;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-create-back-btn-icon {
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

    .sms-create-count-badge,
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

    .sms-create-count-badge {
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

    .sms-title-wrap {
        display: grid;
        gap: 12px;
    }

    .sms-create-title-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: nowrap;
    }

    .sms-create-title-content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex: 1 1 0;
        flex-wrap: wrap;
        min-width: 0;
    }

    .sms-create-title-copy {
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

    .sms-create-title-row h1 {
        margin: 0;
        color: #123355;
        font-size: clamp(1.6rem, 2.6vw, 2.3rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .sms-create-head-title p {
        margin: 8px 0 0;
        max-width: 70ch;
        color: var(--sms-muted);
        font-size: 0.98rem;
        line-height: 1.64;
        font-weight: 600;
    }

    .sms-create-head-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
        align-items: center;
        align-self: flex-start;
    }

    .sms-create-head-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 50px;
        padding: 0 18px;
        border-radius: 16px;
        font-size: 0.92rem;
        font-weight: 800;
        text-decoration: none;
        border: 1px solid transparent;
        white-space: nowrap;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .sms-create-head-btn:hover,
    .sms-create-head-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-create-head-btn.secondary {
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        border-color: #cfdeec;
        color: #48657f;
        box-shadow: 0 14px 22px -24px rgba(15, 23, 42, 0.42);
    }

    .sms-create-head-btn.secondary:hover {
        background: linear-gradient(180deg, #ffffff 0%, #ebf4fb 100%);
        border-color: rgba(18, 116, 216, 0.3);
        color: var(--sms-primary-dark);
    }

    .sms-create-head-btn.primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #fff;
        box-shadow: 0 18px 28px -24px rgba(37, 99, 235, 0.5);
    }

    .sms-create-head-btn.primary:hover {
        color: #fff;
        box-shadow: 0 24px 32px -24px rgba(37, 99, 235, 0.58);
    }

    .form-wrapper {
        padding-bottom: 112px;
    }

    .form-error {
        margin-bottom: 16px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid #f2c3ca;
        background: #fff5f6;
    }

    .form-error-title {
        color: #b63f4f;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .form-error-list {
        margin: 0;
        padding-left: 18px;
        color: #8e2f3c;
    }

    .form-info {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 20px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid #d9e8f7;
        background: var(--sms-soft-info);
        color: #396180;
    }

    .form-info i {
        margin-top: 2px;
        color: var(--sms-primary);
    }

    .form-info-text {
        margin: 0;
        font-size: 0.92rem;
        line-height: 1.55;
        font-weight: 500;
    }

    .sms-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(300px, 0.82fr);
        gap: 18px;
    }

    .form-main-column {
        display: grid;
        gap: 18px;
    }

    .form-side-column {
        display: grid;
        gap: 18px;
        align-content: start;
    }

    .form-section {
        padding: 22px;
    }

    .form-section-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1px solid #eaf1f7;
    }

    .form-section-icon {
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

    .form-section-heading {
        display: grid;
        gap: 4px;
    }

    .form-section-heading h2 {
        margin: 0;
        color: #153b84;
        font-size: 1.08rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .form-section-heading p {
        margin: 0;
        color: var(--sms-muted);
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .form-grid.full {
        grid-template-columns: 1fr;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.span-2 {
        grid-column: span 2;
    }

    .form-label {
        font-size: 0.8rem;
        font-weight: 800;
        color: #38506a;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        margin: 0;
    }

    .required {
        color: var(--sms-danger);
    }

    .hint {
        color: var(--sms-muted);
        font-size: 0.82rem;
        line-height: 1.45;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        min-height: 52px;
        border: 1px solid #cddceb;
        border-radius: 14px;
        background: #fff;
        color: #1e293b;
        font-size: 0.96rem;
        padding: 14px 16px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, transform 0.2s ease;
        font-family: inherit;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #7eb9f5;
        box-shadow: 0 0 0 4px rgba(18, 116, 216, 0.12);
        transform: translateY(-1px);
    }

    .form-input:disabled {
        background: #f3f7fb;
        color: #6b8098;
    }

    .form-textarea {
        min-height: 186px;
        resize: vertical;
    }

    .auto-fill-info {
        min-height: 52px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid #d7e6df;
        background: #f3fbf7;
        color: #23624e;
        font-size: 0.94rem;
        font-weight: 600;
    }

    .auto-fill-info i {
        color: var(--sms-success);
    }

    .message-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .char-counter {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: var(--sms-soft);
        color: #4e6c88;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .char-counter.warning {
        background: rgba(217, 119, 6, 0.12);
        color: #b96f0d;
    }

    .char-counter.danger {
        background: rgba(207, 77, 93, 0.12);
        color: #b63f4f;
    }

    .message-info {
        margin-top: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid #dce7f4;
        background: #f5f9ff;
        color: #456b8d;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .quick-templates {
        margin-top: 16px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .template-btn {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 48px;
        border: 1px solid #d6e4f2;
        background: #fff;
        color: #35506b;
        border-radius: 12px;
        text-align: left;
        font-size: 0.88rem;
        font-weight: 700;
        padding: 0 14px;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .template-btn:hover {
        background: #0f72d6;
        border-color: #0f72d6;
        color: #fff;
        transform: translateY(-1px);
    }

    .check-stack {
        display: grid;
        gap: 12px;
    }

    .check-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin: 0;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid #dbe7f2;
        background: #f9fbfd;
        cursor: pointer;
    }

    .check-row input {
        margin-top: 4px;
    }

    .check-text {
        color: #49637e;
        font-size: 0.93rem;
        line-height: 1.5;
        font-weight: 600;
    }

    .preview-panel {
        padding: 22px;
    }

    .preview-kicker {
        margin: 0 0 8px;
        color: #6f8ba7;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .preview-panel h3 {
        margin: 0;
        color: var(--sms-ink);
        font-size: 1.18rem;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .preview-meta {
        margin-top: 16px;
        display: grid;
        gap: 10px;
    }

    .preview-meta-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid #dce8f3;
        background: #f6fafe;
    }

    .preview-meta-item span {
        color: #5b7690;
        font-size: 0.88rem;
        font-weight: 600;
    }

    .preview-meta-item strong {
        color: var(--sms-ink);
        font-size: 0.92rem;
        font-weight: 800;
        text-align: right;
    }

    .sms-preview-box {
        margin-top: 16px;
        border-radius: 18px;
        border: 1px solid #dce8f3;
        background: linear-gradient(180deg, #f9fbfe 0%, #f2f7fc 100%);
        padding: 18px;
    }

    .sms-preview-phone {
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

    .sms-preview-message {
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

    .sms-preview-message.is-empty {
        background: #e9f0f7;
        color: #5e7893;
        box-shadow: none;
    }

    .sticky-actions {
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

    .sticky-copy {
        min-width: 0;
    }

    .sticky-copy strong {
        display: block;
        color: var(--sms-ink);
        font-size: 0.96rem;
        font-weight: 800;
    }

    .sticky-copy span {
        display: block;
        margin-top: 2px;
        color: var(--sms-muted);
        font-size: 0.84rem;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .btn {
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

    .btn-submit {
        color: #fff;
        background: linear-gradient(135deg, var(--sms-primary) 0%, #0f5cad 100%);
        box-shadow: 0 18px 28px -22px rgba(18, 116, 216, 0.9);
    }

    .btn-submit:hover {
        color: #fff;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .btn-cancel {
        background: #eef3f8;
        border-color: #dbe6f1;
        color: #47627d;
    }

    .btn-cancel:hover {
        background: #e2ebf4;
        color: #274c72;
        text-decoration: none;
    }

    html.dark .sms-form-page,
    body.dark-mode .sms-form-page {
        --sms-ink: #e5effb;
        --sms-muted: #a8bed4;
        border-color: #284763;
        background:
            radial-gradient(circle at top right, rgba(87, 156, 255, 0.14) 0%, transparent 30%),
            radial-gradient(circle at bottom left, rgba(28, 155, 116, 0.10) 0%, transparent 28%),
            linear-gradient(140deg, #0e1a2b 0%, #102033 100%);
        box-shadow: 0 24px 42px -34px rgba(0, 0, 0, 0.9);
    }

    html.dark .sms-create-card,
    html.dark .form-wrapper,
    html.dark .form-section,
    html.dark .preview-panel,
    html.dark .sticky-actions,
    body.dark-mode .sms-create-card,
    body.dark-mode .form-wrapper,
    body.dark-mode .form-section,
    body.dark-mode .preview-panel,
    body.dark-mode .sticky-actions {
        background: rgba(17, 34, 53, 0.92);
        border-color: #2f4f6e;
        box-shadow: 0 22px 36px -28px rgba(0, 0, 0, 0.55);
    }

    html.dark .sms-create-title-row h1,
    html.dark .form-section-heading h2,
    html.dark .form-label,
    html.dark .preview-panel h3,
    html.dark .preview-meta-item strong,
    html.dark .sticky-copy strong,
    body.dark-mode .sms-create-title-row h1,
    body.dark-mode .form-section-heading h2,
    body.dark-mode .form-label,
    body.dark-mode .preview-panel h3,
    body.dark-mode .preview-meta-item strong,
    body.dark-mode .sticky-copy strong {
        color: #e5effb;
    }

    html.dark .sms-create-head-title p,
    html.dark .form-section-heading p,
    html.dark .hint,
    html.dark .message-info,
    html.dark .check-text,
    html.dark .preview-kicker,
    html.dark .preview-meta-item span,
    html.dark .sticky-copy span,
    body.dark-mode .sms-create-head-title p,
    body.dark-mode .form-section-heading p,
    body.dark-mode .hint,
    body.dark-mode .message-info,
    body.dark-mode .check-text,
    body.dark-mode .preview-kicker,
    body.dark-mode .preview-meta-item span,
    body.dark-mode .sticky-copy span {
        color: #a8bed4;
    }

    html.dark .sms-chip,
    html.dark .form-info,
    html.dark .auto-fill-info,
    html.dark .char-counter,
    html.dark .message-info,
    html.dark .check-row,
    html.dark .preview-meta-item,
    html.dark .sms-preview-box,
    html.dark .sms-preview-phone,
    html.dark .btn-cancel,
    body.dark-mode .sms-chip,
    body.dark-mode .form-info,
    body.dark-mode .auto-fill-info,
    body.dark-mode .char-counter,
    body.dark-mode .message-info,
    body.dark-mode .check-row,
    body.dark-mode .preview-meta-item,
    body.dark-mode .sms-preview-box,
    body.dark-mode .sms-preview-phone,
    body.dark-mode .btn-cancel {
        background: #17314c;
        border-color: #355777;
        color: #d5e7fb;
    }

    html.dark .sms-create-back-btn,
    body.dark-mode .sms-create-back-btn {
        background: #17314d;
        border-color: #355777;
        color: #d5e7fb;
    }

    html.dark .sms-create-back-btn-icon,
    body.dark-mode .sms-create-back-btn-icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    html.dark .sms-create-back-btn:hover,
    html.dark .btn-cancel:hover,
    html.dark .template-btn:hover,
    body.dark-mode .sms-create-back-btn:hover,
    body.dark-mode .btn-cancel:hover,
    body.dark-mode .template-btn:hover {
        background: #21486f;
        border-color: #4d7aa5;
        color: #fff;
    }

    html.dark .form-input,
    html.dark .form-select,
    html.dark .form-textarea,
    body.dark-mode .form-input,
    body.dark-mode .form-select,
    body.dark-mode .form-textarea {
        background: #13263f;
        border-color: #355985;
        color: #deebf9;
    }

    html.dark .form-input:disabled,
    body.dark-mode .form-input:disabled {
        background: #1a3150;
        color: #9fb9d8;
    }

    html.dark .form-section-header,
    body.dark-mode .form-section-header {
        border-bottom-color: #29445f;
    }

    html.dark .form-section-icon,
    body.dark-mode .form-section-icon {
        background: #16324f;
        color: #8ec5ff;
        border-color: #2f4f72;
    }

    html.dark .template-btn,
    body.dark-mode .template-btn {
        background: #13263f;
        border-color: #355985;
        color: #dbe9f8;
    }

    html.dark .sms-preview-message.is-empty,
    body.dark-mode .sms-preview-message.is-empty {
        background: #203851;
        color: #a8bed4;
    }

    @media (max-width: 1199px) {
        .sms-create-hero,
        .sms-form-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 992px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.span-2 {
            grid-column: auto;
        }

        .sticky-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .form-actions {
            width: 100%;
            justify-content: stretch;
        }

        .btn {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .sms-form-page {
            padding: 12px;
            border-radius: 18px;
        }

        .sms-create-card,
        .form-wrapper,
        .form-section,
        .preview-panel,
        .sticky-actions {
            border-radius: 18px;
        }

        .sms-create-top,
        .sms-create-title-row,
        .sms-badge-row,
        .sms-create-head-actions {
            align-items: stretch;
        }

        .sms-create-title-row {
            flex-wrap: wrap;
        }

        .sms-create-back-btn,
        .sms-create-head-btn {
            width: 100%;
        }

        .sms-create-head-actions {
            display: grid;
        }

        .quick-templates {
            grid-template-columns: 1fr;
        }

        .message-toolbar {
            align-items: stretch;
        }
    }

    @media (min-width: 1400px) {
        .sms-form-page {
            padding-inline: clamp(18px, 2vw, 30px);
        }
    }
</style>

<div class="sms-form-page">
    <div class="sms-create-shell">
        <section class="sms-create-hero">
            <div class="sms-create-card">
                <div class="sms-create-top">
                    <a href="{{ route('sms.index') }}" class="sms-create-back-btn">
                        <span class="sms-create-back-btn-icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Retour</span>
                    </a>

                    <div class="sms-badge-row">
                        <span class="sms-create-count-badge">
                            <i class="fas fa-calendar-check"></i>
                            {{ $rendezvousCount }} RDV dispo
                        </span>
                        <span class="sms-chip">
                            <i class="fas fa-bolt"></i>
                            Creation rapide
                        </span>
                    </div>
                </div>

                <div class="sms-create-head-title">
                    <div class="sms-create-title-row">
                        <span class="sms-title-icon"><i class="fas fa-mobile-alt"></i></span>
                        <div class="sms-create-title-content">
                            <div class="sms-create-title-copy">
                                <h1>Nouveau rappel SMS</h1>
                                <p>Planifiez un rappel patient dans une interface plus claire, plus fluide et mieux alignee avec les modules premium du produit.</p>
                            </div>

                            <div class="sms-create-head-actions">
                                <a href="{{ route('sms.logs') }}" class="sms-create-head-btn secondary">
                                    <i class="fas fa-history"></i>
                                    Historique
                                </a>
                                <a href="{{ route('sms.index') }}" class="sms-create-head-btn primary">
                                    <i class="fas fa-list"></i>
                                    Liste rappels
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="form-wrapper">
            @if ($errors->any())
                <div class="form-error">
                    <div class="form-error-title">
                        <i class="fas fa-exclamation-circle"></i>
                        Erreurs de validation
                    </div>
                    <ul class="form-error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sms.store') }}">
                @csrf

                <div class="form-info">
                    <i class="fas fa-circle-info"></i>
                    <p class="form-info-text">Le rappel se pre-remplit a partir du rendez-vous selectionne. Vous pouvez ajuster le telephone, la date d'envoi et le contenu avant validation.</p>
                </div>

                <div class="sms-form-grid">
                    <div class="form-main-column">
                        <section class="form-section">
                            <div class="form-section-header">
                                <span class="form-section-icon"><i class="fas fa-calendar-alt"></i></span>
                                <div class="form-section-heading">
                                    <h2>Rendez-vous</h2>
                                    <p>Selection du rendez-vous et verification du patient associe.</p>
                                </div>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Rendez-vous <span class="required">*</span></label>
                                    <select name="rendezvous_id" id="rendezvousSelect" class="form-select" required>
                                        <option value="">-- Selectionner un RDV --</option>
                                        @if(!empty($rendezvousList) && count($rendezvousList) > 0)
                                            @foreach($rendezvousList as $rdv)
                                                <option
                                                    value="{{ optional($rdv)->id }}"
                                                    data-patient="{{ trim((optional(optional($rdv)->patient)->prenom ?? '') . ' ' . (optional(optional($rdv)->patient)->nom ?? '')) }}"
                                                    data-phone="{{ optional(optional($rdv)->patient)->telephone ?? '' }}"
                                                    data-date="{{ optional(optional($rdv)->date_heure)->format('Y-m-d\\TH:i') }}"
                                                    {{ (string) old('rendezvous_id', $selectedRendezvousId ?? '') === (string) optional($rdv)->id ? 'selected' : '' }}
                                                >
                                                    {{ trim((optional(optional($rdv)->medecin)->prenom ?? '') . ' ' . (optional(optional($rdv)->medecin)->nom ?? '')) ?: 'Medecin' }}
                                                    - {{ optional(optional($rdv)->date_heure)->format('d/m/Y H:i') }}
                                                    - {{ trim((optional(optional($rdv)->patient)->prenom ?? '') . ' ' . (optional(optional($rdv)->patient)->nom ?? '')) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>Aucun rendez-vous disponible</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Patient</label>
                                    <div class="auto-fill-info">
                                        <i class="fas fa-user"></i>
                                        <span id="patientPreview">Auto-complete depuis le RDV</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-header">
                                <span class="form-section-icon"><i class="fas fa-address-card"></i></span>
                                <div class="form-section-heading">
                                    <h2>Coordonnees</h2>
                                    <p>Controlez le numero du patient et la date effective du rappel.</p>
                                </div>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Numero de telephone <span class="required">*</span></label>
                                    <input
                                        type="tel"
                                        name="telephone"
                                        id="telephoneInput"
                                        class="form-input"
                                        placeholder="+212612345678"
                                        pattern="^(\+212|0)[0-9]{9}$"
                                        value="{{ old('telephone') }}"
                                        required
                                    >
                                    <span class="hint">Format attendu: +212XXXXXXXXX ou 06XXXXXXXX.</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Date d'envoi</label>
                                    <input
                                        type="datetime-local"
                                        name="date_envoi_prevue"
                                        id="sendDateInput"
                                        class="form-input"
                                        value="{{ old('date_envoi_prevue') }}"
                                    >
                                    <span class="hint">Laissez vide pour planifier automatiquement 24h avant le rendez-vous.</span>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-header">
                                <span class="form-section-icon"><i class="fas fa-comment-dots"></i></span>
                                <div class="form-section-heading">
                                    <h2>Message SMS</h2>
                                    <p>Redigez un rappel concis, lisible et directement exploitable par le patient.</p>
                                </div>
                            </div>

                            <div class="form-grid full">
                                <div class="form-group">
                                    <label class="form-label">Message <span class="required">*</span></label>
                                    <textarea
                                        name="message_template"
                                        id="messageTemplate"
                                        class="form-textarea"
                                        placeholder="Tapez votre message de rappel..."
                                        maxlength="160"
                                        onkeyup="updateCharCount(this)"
                                        required
                                    >{{ old('message_template') }}</textarea>

                                    <div class="message-toolbar">
                                        <div class="char-counter" id="charCounterWrapper">
                                            <i class="fas fa-signal"></i>
                                            <span id="char-count">0/160</span>
                                        </div>
                                        <span class="hint">SMS standard recommande: 160 caracteres maximum.</span>
                                    </div>

                                    <div class="message-info">
                                        <i class="fas fa-lightbulb"></i>
                                        <span>Conseil: utilisez un message court et clair. Les SMS plus longs peuvent etre comptes comme plusieurs envois.</span>
                                    </div>

                                    <div>
                                        <label class="form-label">Modeles rapides</label>
                                        <div class="quick-templates">
                                            <button type="button" class="template-btn" onclick="setTemplate('Rappel: Votre RDV est demain a 14:30 avec Dr. Ahmed. A bientot!')">
                                                <i class="fas fa-repeat"></i>
                                                Demain
                                            </button>
                                            <button type="button" class="template-btn" onclick="setTemplate('Rappel: Vous avez un RDV aujourdhui a 10:00 avec Dr. Fatima.')">
                                                <i class="fas fa-clock"></i>
                                                Aujourd hui
                                            </button>
                                            <button type="button" class="template-btn" onclick="setTemplate('Dr. Ahmed vous rappelle votre consultation de demain. Merci!')">
                                                <i class="fas fa-stethoscope"></i>
                                                Consultation
                                            </button>
                                            <button type="button" class="template-btn" onclick="setTemplate('Rappel de votre visite medicale prevue demain. Appelez-nous pour annuler.')">
                                                <i class="fas fa-calendar-check"></i>
                                                Visite
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-header">
                                <span class="form-section-icon"><i class="fas fa-sliders"></i></span>
                                <div class="form-section-heading">
                                    <h2>Options avancees</h2>
                                    <p>Conservez les options disponibles sans surcharger le formulaire principal.</p>
                                </div>
                            </div>

                            <div class="check-stack">
                                <label class="check-row">
                                    <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }}>
                                    <span class="check-text">Marquer comme urgent pour signaler un rappel prioritaire.</span>
                                </label>
                                <label class="check-row">
                                    <input type="checkbox" name="send_email_copy" value="1" {{ old('send_email_copy') ? 'checked' : '' }}>
                                    <span class="check-text">Envoyer egalement une copie par email au patient.</span>
                                </label>
                            </div>
                        </section>
                    </div>

                    <aside class="form-side-column">
                        <section class="preview-panel">
                            <p class="preview-kicker">Apercu du rappel</p>
                            <h3>Simulation du SMS</h3>

                            <div class="preview-meta">
                                <div class="preview-meta-item">
                                    <span>Patient</span>
                                    <strong id="previewPatient">En attente de selection</strong>
                                </div>
                                <div class="preview-meta-item">
                                    <span>Envoi prevu</span>
                                    <strong id="previewSendDate">Auto 24h avant</strong>
                                </div>
                            </div>

                            <div class="sms-preview-box">
                                <span class="sms-preview-phone" id="previewPhone">
                                    <i class="fas fa-phone"></i>
                                    Numero non renseigne
                                </span>
                                <div class="sms-preview-message is-empty" id="messagePreview">Le contenu du message apparaitra ici des que vous commencez a rediger.</div>
                            </div>
                        </section>
                    </aside>
                </div>

                <div class="sticky-actions">
                    <div class="sticky-copy">
                        <strong>Finaliser le rappel</strong>
                        <span>Verifiez le rendez-vous, la date d'envoi et le texte avant validation.</span>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('sms.index') }}" class="btn btn-cancel">
                            <i class="fas fa-xmark"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-paper-plane"></i>
                            Planifier le SMS
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toReminderDate(rdvDateIso) {
        if (!rdvDateIso) return '';
        const rdvDate = new Date(rdvDateIso);
        if (Number.isNaN(rdvDate.getTime())) return '';
        rdvDate.setHours(rdvDate.getHours() - 24);
        const year = rdvDate.getFullYear();
        const month = String(rdvDate.getMonth() + 1).padStart(2, '0');
        const day = String(rdvDate.getDate()).padStart(2, '0');
        const hour = String(rdvDate.getHours()).padStart(2, '0');
        const minute = String(rdvDate.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hour}:${minute}`;
    }

    function formatPreviewDate(isoDate) {
        if (!isoDate) return 'Auto 24h avant';
        const date = new Date(isoDate);
        if (Number.isNaN(date.getTime())) return 'Auto 24h avant';
        return date.toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function updatePreview() {
        const patientPreview = document.getElementById('patientPreview');
        const phoneInput = document.getElementById('telephoneInput');
        const sendDateInput = document.getElementById('sendDateInput');
        const messageTemplate = document.getElementById('messageTemplate');
        const previewPatient = document.getElementById('previewPatient');
        const previewPhone = document.getElementById('previewPhone');
        const previewSendDate = document.getElementById('previewSendDate');
        const messagePreview = document.getElementById('messagePreview');

        if (previewPatient && patientPreview) {
            previewPatient.textContent = patientPreview.textContent.trim() || 'En attente de selection';
        }

        if (previewPhone && phoneInput) {
            previewPhone.innerHTML = `<i class="fas fa-phone"></i>${phoneInput.value.trim() || 'Numero non renseigne'}`;
        }

        if (previewSendDate && sendDateInput) {
            previewSendDate.textContent = formatPreviewDate(sendDateInput.value);
        }

        if (messagePreview && messageTemplate) {
            const content = messageTemplate.value.trim();
            messagePreview.textContent = content || 'Le contenu du message apparaitra ici des que vous commencez a rediger.';
            messagePreview.classList.toggle('is-empty', !content);
        }
    }

    function updateRdvDetails() {
        const rdvSelect = document.getElementById('rendezvousSelect');
        const phoneInput = document.getElementById('telephoneInput');
        const sendDateInput = document.getElementById('sendDateInput');
        const patientPreview = document.getElementById('patientPreview');
        if (!rdvSelect || !patientPreview || !phoneInput || !sendDateInput) return;

        const selectedOption = rdvSelect.options[rdvSelect.selectedIndex];
        const patientName = selectedOption?.dataset?.patient || '';
        const patientPhone = selectedOption?.dataset?.phone || '';
        const rdvDate = selectedOption?.dataset?.date || '';

        patientPreview.textContent = patientName || 'Auto-complete depuis le RDV';

        if (!phoneInput.value && patientPhone) {
            phoneInput.value = patientPhone;
        }

        if (!sendDateInput.value && rdvDate) {
            sendDateInput.value = toReminderDate(rdvDate);
        }

        updatePreview();
    }

    function updateCharCount(textarea) {
        const count = textarea.value.length;
        const counter = document.getElementById('char-count');
        const wrapper = document.getElementById('charCounterWrapper');

        if (counter) {
            counter.textContent = count + '/160';
        }

        if (wrapper) {
            wrapper.classList.remove('warning', 'danger');
            if (count > 150) {
                wrapper.classList.add('danger');
            } else if (count > 120) {
                wrapper.classList.add('warning');
            }
        }

        updatePreview();
    }

    function setTemplate(text) {
        const textarea = document.querySelector('[name="message_template"]');
        if (!textarea) return;
        textarea.value = text;
        updateCharCount(textarea);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const rdvSelect = document.getElementById('rendezvousSelect');
        const phoneInput = document.getElementById('telephoneInput');
        const sendDateInput = document.getElementById('sendDateInput');
        const textarea = document.getElementById('messageTemplate');

        if (rdvSelect) {
            rdvSelect.addEventListener('change', updateRdvDetails);
            updateRdvDetails();
        }

        if (phoneInput) {
            phoneInput.addEventListener('input', updatePreview);
        }

        if (sendDateInput) {
            sendDateInput.addEventListener('input', updatePreview);
        }

        if (textarea) {
            updateCharCount(textarea);
            textarea.addEventListener('input', function () {
                updateCharCount(textarea);
            });
        } else {
            updatePreview();
        }
    });
</script>
@endsection
