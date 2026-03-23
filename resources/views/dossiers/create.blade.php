@extends('layouts.app')

@section('title', 'Nouveau Dossier Médical')
@section('topbar_subtitle', 'Creation guidee d\'un dossier, contexte patient et actions rapides dans une vue premium harmonisee.')

@push('styles')
<style>
.dossier-create-page {
    --dossier-create-primary: #2c7be5;
    --dossier-create-primary-strong: #1f5ea8;
    --dossier-create-accent: #0ea5e9;
    --dossier-create-surface: linear-gradient(180deg, #f4f8fd 0%, #eef5fb 100%);
    --dossier-create-card: #ffffff;
    --dossier-create-border: #d8e4f2;
    --dossier-create-text: #15314d;
    --dossier-create-muted: #5f7896;
    width: 100%;
    max-width: none;
    padding: 10px 8px 92px;
}

.dossier-create-shell {
    display: grid;
    gap: 16px;
}

.dossier-create-breadcrumbs {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 12px;
    padding: 0;
    list-style: none;
    font-size: .8rem;
    color: var(--dossier-create-muted);
    font-weight: 700;
}

.dossier-create-breadcrumbs a {
    color: inherit;
    text-decoration: none;
}

.dossier-create-breadcrumbs a:hover {
    color: var(--dossier-create-primary);
}

.dossier-create-breadcrumb-separator {
    color: #98abc0;
}

.dossier-create-title {
    margin: 0;
    font-size: clamp(1.45rem, 2.5vw, 2.1rem);
    font-weight: 800;
    line-height: 1.06;
    letter-spacing: -0.04em;
    color: var(--dossier-create-text);
}

.dossier-create-title-subtitle {
    margin: 10px 0 0;
    max-width: 72ch;
    color: var(--dossier-create-muted);
    font-size: .97rem;
    line-height: 1.6;
    font-weight: 600;
}

.dossier-create-hero {
    position: relative;
    overflow: hidden;
    display: grid;
    gap: 16px;
    padding: 18px;
    border-radius: 22px;
    border: 1px solid var(--dossier-create-border);
    background:
        radial-gradient(circle at top right, rgba(44, 123, 229, 0.16) 0%, rgba(44, 123, 229, 0) 32%),
        radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
        var(--dossier-create-surface);
    box-shadow: 0 24px 48px -38px rgba(20, 52, 84, 0.42);
}

.dossier-create-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0) 100%);
}

.dossier-create-hero > * {
    position: relative;
    z-index: 1;
}

.dossier-create-hero-head {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.95fr);
    gap: 16px;
    align-items: start;
}

.dossier-create-hero-main {
    min-width: 0;
}

.dossier-create-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.dossier-create-title-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #ffffff;
    font-size: 1.3rem;
    background: linear-gradient(135deg, var(--dossier-create-primary) 0%, var(--dossier-create-primary-strong) 100%);
    box-shadow: 0 16px 26px -18px rgba(44, 123, 229, 0.58);
}

.dossier-create-title-block {
    min-width: 0;
}

.dossier-create-hero-eyebrow {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
}

.dossier-create-hero-tools {
    display: grid;
    gap: 12px;
}

.dossier-create-panel {
    border: 1px solid rgba(208, 221, 237, 0.96);
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.78);
    padding: 14px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.68);
}

.dossier-create-panel-label {
    display: block;
    margin-bottom: 10px;
    color: var(--dossier-create-muted);
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 800;
}

.dossier-create-panel-copy {
    margin: 0;
    color: var(--dossier-create-muted);
    font-size: .88rem;
    line-height: 1.55;
}

.dossier-create-panel-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 9px;
}

.dossier-create-panel-list li {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid rgba(208, 221, 237, 0.92);
    background: rgba(247, 251, 255, 0.84);
    color: var(--dossier-create-muted);
    font-size: .84rem;
    font-weight: 700;
}

.dossier-create-panel-list strong {
    color: var(--dossier-create-text);
    font-weight: 800;
    text-align: right;
}

.dossier-create-panel-note {
    margin: 10px 0 0;
    color: var(--dossier-create-muted);
    font-size: .82rem;
    line-height: 1.55;
}

.dossier-create-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.dossier-create-btn {
    min-height: 44px;
    border-radius: 14px;
    border: 1px solid transparent;
    padding: 0 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: .92rem;
    font-weight: 800;
    text-decoration: none;
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    white-space: nowrap;
}

.dossier-create-btn:hover,
.dossier-create-btn:focus {
    transform: translateY(-1px);
    text-decoration: none;
}

.dossier-create-btn-soft {
    border-color: #cfdef0;
    background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
    color: #385674;
    box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
}

.dossier-create-btn-soft:hover,
.dossier-create-btn-soft:focus {
    color: #1f6fa3;
    border-color: rgba(44, 123, 229, 0.3);
    background: linear-gradient(180deg, #ffffff 0%, #edf5fb 100%);
}

.dossier-create-btn-primary {
    background: linear-gradient(135deg, var(--dossier-create-primary) 0%, var(--dossier-create-primary-strong) 100%);
    color: #fff;
    box-shadow: 0 18px 28px -22px rgba(44, 123, 229, 0.55);
}

.dossier-create-btn-primary:hover,
.dossier-create-btn-primary:focus {
    color: #fff;
}

.dossier-create-btn-icon {
    width: 28px;
    height: 28px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(44, 123, 229, 0.1);
    color: var(--dossier-create-primary);
}

.dossier-create-btn-primary .dossier-create-btn-icon {
    background: rgba(255, 255, 255, 0.16);
    color: inherit;
}

.dossier-create-kpis {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-top: 14px;
}

.dossier-create-kpi {
    padding: 12px 14px;
    border-radius: 16px;
    border: 1px solid rgba(206, 221, 238, 0.96);
    background: rgba(255, 255, 255, 0.72);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.62);
}

.dossier-create-kpi-label {
    display: block;
    margin-bottom: 6px;
    color: var(--dossier-create-muted);
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 800;
}

.dossier-create-kpi-value {
    display: block;
    color: var(--dossier-create-text);
    font-size: 1.2rem;
    font-weight: 900;
    line-height: 1;
}

.dossier-create-kpi-meta {
    display: block;
    margin-top: 5px;
    color: #7290b0;
    font-size: .82rem;
    font-weight: 600;
}

.dossier-create-layout {
    display: grid;
    grid-template-columns: 320px minmax(0, 1fr);
    gap: 16px;
    align-items: start;
}

.dossier-create-card {
    background: var(--dossier-create-card);
    border: 1px solid var(--dossier-create-border);
    border-radius: 22px;
    box-shadow: 0 22px 34px -34px rgba(15, 23, 42, 0.44);
}

.dossier-create-side {
    overflow: hidden;
    position: sticky;
    top: 92px;
    padding: 18px;
}

.dossier-create-side::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 128px;
    pointer-events: none;
    background:
        radial-gradient(circle at top right, rgba(44, 123, 229, 0.18) 0%, rgba(44, 123, 229, 0) 44%),
        linear-gradient(180deg, rgba(244, 249, 255, 0.92) 0%, rgba(244, 249, 255, 0) 100%);
}

.dossier-create-side > * {
    position: relative;
    z-index: 1;
}

.dossier-create-patient-head {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    margin-bottom: 16px;
}

.dossier-create-avatar {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #fff;
    font-size: 1.05rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--dossier-create-primary) 0%, var(--dossier-create-accent) 100%);
    box-shadow: 0 18px 28px -20px rgba(44, 123, 229, 0.56);
}

.dossier-create-patient-copy {
    min-width: 0;
}

.dossier-create-side-name {
    margin: 0;
    font-size: 1.22rem;
    line-height: 1.08;
    font-weight: 800;
    color: var(--dossier-create-text);
}

.dossier-create-side-subtitle {
    margin: 5px 0 0;
    color: var(--dossier-create-muted);
    font-size: .88rem;
    font-weight: 700;
}

.dossier-create-side-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.dossier-create-chip {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    min-height: 32px;
    border-radius: 999px;
    border: 1px solid #d4e2f2;
    background: #f6fafe;
    color: #1d4f91;
    padding: 0 12px;
    font-size: .77rem;
    font-weight: 800;
}

.dossier-create-side-title {
    margin: 0 0 12px;
    font-size: .92rem;
    font-weight: 800;
    color: var(--dossier-create-text);
}

.dossier-create-side-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.dossier-create-side-list li {
    border: 1px solid #e2ebf6;
    border-radius: 16px;
    background: #fbfdff;
    padding: 12px;
}

.dossier-create-side-list small {
    color: var(--dossier-create-muted);
    display: block;
    font-size: .68rem;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 800;
}

.dossier-create-side-list strong {
    font-size: .92rem;
    line-height: 1.45;
    color: var(--dossier-create-text);
}

.dossier-create-main {
    overflow: hidden;
}

.dossier-create-main-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 18px 18px 0;
    background: transparent;
    border-bottom: 0;
}

.dossier-create-main-title {
    margin: 0;
    font-size: 1.16rem;
    font-weight: 800;
    color: var(--dossier-create-text);
}

.dossier-create-badge {
    background: #eef6ff;
    border: 1px solid #d4e2f2;
    color: var(--dossier-create-primary-strong);
    border-radius: 999px;
    padding: 5px 12px;
    font-size: .76rem;
    font-weight: 800;
}

.dossier-create-body {
    padding: 18px;
    display: grid;
    gap: 16px;
}

.dossier-create-section {
    border: 1px solid #dfe9f5;
    border-radius: 20px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    box-shadow: 0 16px 28px -30px rgba(15, 23, 42, 0.32);
    overflow: hidden;
}

.dossier-create-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid #e6eef8;
    background: linear-gradient(180deg, #f7fbff 0%, #eff6fd 100%);
}

.dossier-create-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.dossier-create-section-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(44, 123, 229, 0.1);
    color: var(--dossier-create-primary);
}

.dossier-create-section-head h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    color: var(--dossier-create-text);
}

.dossier-create-section-help {
    margin: 3px 0 0;
    color: var(--dossier-create-muted);
    font-size: .84rem;
    line-height: 1.45;
}

.dossier-create-section-tag {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 0 10px;
    border-radius: 999px;
    background: #eef6ff;
    color: var(--dossier-create-primary-strong);
    font-size: .75rem;
    font-weight: 800;
}

.dossier-create-section-body {
    padding: 16px;
}

.dossier-create-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.dossier-create-field {
    display: flex;
    flex-direction: column;
}

.dossier-create-field.full {
    grid-column: 1 / -1;
}

.dossier-create-field label {
    font-size: .78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--dossier-create-muted);
    margin-bottom: 8px;
}

.dossier-create-field label .required {
    color: #dc2626;
}

.dossier-create-field .form-control,
.dossier-create-field .form-select,
.dossier-create-field textarea {
    min-height: 52px;
    border-radius: 14px;
    border: 1px solid #d4e1ee;
    background: #fff;
    color: var(--dossier-create-text);
    padding: 13px 14px;
    font-size: .95rem;
    font-weight: 600;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.78), 0 10px 24px -28px rgba(15, 23, 42, 0.28);
    transition: border-color .2s ease, box-shadow .2s ease, background .2s ease, transform .2s ease;
}

.dossier-create-field .form-control:focus,
.dossier-create-field .form-select:focus,
.dossier-create-field textarea:focus {
    border-color: rgba(44, 123, 229, 0.46);
    box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.12), 0 14px 28px -26px rgba(31, 111, 163, 0.34);
    transform: translateY(-1px);
}

.dossier-create-field textarea {
    min-height: 128px;
    resize: vertical;
}

.dossier-create-field-hint {
    margin-top: 8px;
    color: var(--dossier-create-muted);
    font-size: .83rem;
    line-height: 1.45;
}

.dossier-create-field .invalid-feedback.d-block {
    margin-top: 8px;
    font-size: .84rem;
    font-weight: 700;
}

.dossier-create-list {
    margin: 0;
    padding-left: 18px;
    color: var(--dossier-create-muted);
    font-size: .88rem;
    line-height: 1.7;
}

.dossier-create-footer {
    position: sticky;
    bottom: 0;
    z-index: 2;
    padding: 14px 18px 18px;
    border-top: 1px solid #e6eef8;
    display: flex;
    justify-content: space-between;
    gap: 10px;
    background: linear-gradient(180deg, rgba(255,255,255,.82) 0%, rgba(255,255,255,.96) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.dossier-create-mobile-actions {
    display: none;
}

html.dark body .dossier-create-page,
body.dark-mode .dossier-create-page,
body.theme-dark .dossier-create-page {
    --dossier-create-surface: linear-gradient(180deg, #152233 0%, #122032 100%);
    --dossier-create-card: #162332;
    --dossier-create-border: #2f4358;
    --dossier-create-text: #e6edf6;
    --dossier-create-muted: #9eb1c7;
}

html.dark body .dossier-create-side-list li,
html.dark body .dossier-create-kpi,
html.dark body .dossier-create-panel,
html.dark body .dossier-create-panel-list li,
body.dark-mode .dossier-create-side-list li,
body.dark-mode .dossier-create-kpi,
body.dark-mode .dossier-create-panel,
body.dark-mode .dossier-create-panel-list li,
body.theme-dark .dossier-create-side-list li,
body.theme-dark .dossier-create-kpi,
body.theme-dark .dossier-create-panel,
body.theme-dark .dossier-create-panel-list li {
    background: rgba(17, 34, 54, 0.88);
    border-color: #35506a;
}

html.dark body .dossier-create-btn-soft,
body.dark-mode .dossier-create-btn-soft,
body.theme-dark .dossier-create-btn-soft {
    border-color: #365b7d;
    background: linear-gradient(150deg, #183552 0%, #14304b 100%);
    color: #d2e6fb;
}

html.dark body .dossier-create-btn-soft:hover,
html.dark body .dossier-create-btn-soft:focus,
body.dark-mode .dossier-create-btn-soft:hover,
body.dark-mode .dossier-create-btn-soft:focus,
body.theme-dark .dossier-create-btn-soft:hover,
body.theme-dark .dossier-create-btn-soft:focus {
    border-color: #4c7094;
    background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
    color: #ffffff;
}

html.dark body .dossier-create-btn-icon,
html.dark body .dossier-create-section-icon,
body.dark-mode .dossier-create-btn-icon,
body.theme-dark .dossier-create-btn-icon,
body.dark-mode .dossier-create-section-icon,
body.theme-dark .dossier-create-section-icon {
    background: rgba(119, 183, 255, 0.16);
    color: #9fd0ff;
}

html.dark body .dossier-create-section,
body.dark-mode .dossier-create-section,
body.theme-dark .dossier-create-section {
    background: #0f1a28;
    border-color: #2f4358;
}

html.dark body .dossier-create-section-head,
body.dark-mode .dossier-create-section-head,
body.theme-dark .dossier-create-section-head {
    background: #16273d;
    border-color: #294055;
}

html.dark body .dossier-create-main-title,
html.dark body .dossier-create-side-title,
html.dark body .dossier-create-section-head h3,
html.dark body .dossier-create-side-name,
body.dark-mode .dossier-create-main-title,
body.dark-mode .dossier-create-side-title,
body.dark-mode .dossier-create-section-head h3,
body.dark-mode .dossier-create-side-name,
body.theme-dark .dossier-create-main-title,
body.theme-dark .dossier-create-side-title,
body.theme-dark .dossier-create-section-head h3,
body.theme-dark .dossier-create-side-name {
    color: #eef5ff;
}

html.dark body .dossier-create-field .form-control,
html.dark body .dossier-create-field .form-select,
html.dark body .dossier-create-field textarea,
body.dark-mode .dossier-create-field .form-control,
body.dark-mode .dossier-create-field .form-select,
body.dark-mode .dossier-create-field textarea,
body.theme-dark .dossier-create-field .form-control,
body.theme-dark .dossier-create-field .form-select,
body.theme-dark .dossier-create-field textarea {
    background: #13263f;
    border-color: #355985;
    color: #deebf9;
}

html.dark body .dossier-create-footer,
body.dark-mode .dossier-create-footer,
body.theme-dark .dossier-create-footer {
    background: linear-gradient(180deg, rgba(18, 35, 52, 0.84) 0%, rgba(18, 35, 52, 0.98) 100%);
    border-color: #294055;
}

@media (max-width: 1199.98px) {
    .dossier-create-hero-head {
        grid-template-columns: 1fr;
    }

    .dossier-create-layout {
        grid-template-columns: 300px minmax(0, 1fr);
    }
}

@media (max-width: 991.98px) {
    .dossier-create-layout {
        grid-template-columns: 1fr;
    }

    .dossier-create-side {
        position: static;
    }

    .dossier-create-grid {
        grid-template-columns: 1fr;
    }

    .dossier-create-side-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dossier-create-kpis {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767.98px) {
    .dossier-create-page {
        padding: 6px 0 88px;
    }

    .dossier-create-actions,
    .dossier-create-footer {
        display: none;
    }

    .dossier-create-mobile-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        position: fixed;
        left: 8px;
        right: 8px;
        bottom: calc(10px + env(safe-area-inset-bottom));
        z-index: 1050;
        background: var(--dossier-create-card);
        border: 1px solid var(--dossier-create-border);
        border-radius: 18px;
        padding: 8px;
        box-shadow: 0 16px 24px -20px rgba(0, 0, 0, .46);
    }

    .dossier-create-mobile-actions .dossier-create-btn {
        width: 100%;
    }

    .dossier-create-side-list {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 575.98px) {
    .dossier-create-hero {
        padding: 14px;
        border-radius: 18px;
    }

    .dossier-create-title-row {
        align-items: flex-start;
    }

    .dossier-create-title-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
    }

    .dossier-create-main-head,
    .dossier-create-body {
        padding-left: 14px;
        padding-right: 14px;
    }
}
</style>
@endpush

@section('content')
@php
    $selectedPatient = $patients->firstWhere('id', old('patient_id'));
    $selectedFullName = $selectedPatient ? trim(($selectedPatient->prenom ?? '') . ' ' . ($selectedPatient->nom ?? '')) : 'Patient à sélectionner';
    $selectedInitials = collect(preg_split('/\s+/', $selectedFullName) ?: [])->filter()->take(2)->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))->implode('');
    if ($selectedInitials === '') {
        $selectedInitials = 'ND';
    }
    $openDate = old('date_ouverture', date('Y-m-d'));
    $openDateDisplay = $openDate ? \Illuminate\Support\Carbon::parse($openDate)->format('d/m/Y') : 'Non renseignée';
    $selectedStatus = old('statut', 'actif');
    $selectedType = old('type', 'général');
    $selectedBirthDisplay = $selectedPatient && $selectedPatient->date_naissance
        ? \Illuminate\Support\Carbon::parse($selectedPatient->date_naissance)->format('d/m/Y')
        : 'À compléter après sélection';
@endphp

<div class="container-fluid dossier-create-page">
    <div class="dossier-create-shell">
        <header class="dossier-create-hero">
            <div class="dossier-create-hero-head">
                <div class="dossier-create-hero-main">
                    <ol class="dossier-create-breadcrumbs" aria-label="Fil d'Ariane création dossier">
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="dossier-create-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li><a href="{{ route('dossiers.index') }}">Dossiers</a></li>
                        <li class="dossier-create-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li aria-current="page">Nouveau dossier</li>
                    </ol>

                    <div class="dossier-create-hero-eyebrow" aria-label="Repères création dossier">
                        <span class="dossier-create-chip"><i class="fas fa-folder-plus"></i>Nouveau flux dossier</span>
                        <span class="dossier-create-chip"><i class="fas fa-user-group"></i>{{ $createStats['patients'] }} patients</span>
                        <span class="dossier-create-chip"><i class="fas fa-folder-open"></i>{{ $createStats['actifs'] }} dossiers actifs</span>
                    </div>

                    <div class="dossier-create-title-row">
                        <span class="dossier-create-title-icon" aria-hidden="true"><i class="fas fa-folder-plus"></i></span>
                        <div class="dossier-create-title-block">
                            <h1 class="dossier-create-title">Créer un dossier médical</h1>
                            <p class="dossier-create-title-subtitle">Préparez un nouveau dossier structuré pour le patient, avec une saisie claire, rapide et cohérente avec l’interface premium du module médical.</p>
                        </div>
                    </div>

                    <div class="dossier-create-kpis" aria-label="Indicateurs du formulaire de création">
                        <article class="dossier-create-kpi">
                            <span class="dossier-create-kpi-label">Patient</span>
                            <span class="dossier-create-kpi-value" id="createPatientKpi">{{ $selectedFullName }}</span>
                            <span class="dossier-create-kpi-meta">Renseignement automatique après sélection</span>
                        </article>
                        <article class="dossier-create-kpi">
                            <span class="dossier-create-kpi-label">Ouverture</span>
                            <span class="dossier-create-kpi-value" id="createDateKpi">{{ $openDateDisplay }}</span>
                            <span class="dossier-create-kpi-meta">Date administrative du dossier</span>
                        </article>
                        <article class="dossier-create-kpi">
                            <span class="dossier-create-kpi-label">Statut</span>
                            <span class="dossier-create-kpi-value" id="createStatusKpi">{{ ucfirst(old('statut', 'actif')) }}</span>
                            <span class="dossier-create-kpi-meta">État initial du suivi</span>
                        </article>
                    </div>
                </div>

                <div class="dossier-create-hero-tools">
                    <section class="dossier-create-panel">
                        <span class="dossier-create-panel-label">Contexte</span>
                        <ul class="dossier-create-panel-list">
                            <li>
                                <span>Patients disponibles</span>
                                <strong>{{ $createStats['patients'] }}</strong>
                            </li>
                            <li>
                                <span>Dossiers actifs</span>
                                <strong>{{ $createStats['actifs'] }}</strong>
                            </li>
                            <li>
                                <span>Dossiers archivés</span>
                                <strong>{{ $createStats['archives'] }}</strong>
                            </li>
                        </ul>
                        <p class="dossier-create-panel-note">Le formulaire conserve tous les champs métier existants, avec une hiérarchie de lecture alignée sur l’index, les archives et la fiche dossier.</p>
                    </section>
                    <section class="dossier-create-panel">
                        <span class="dossier-create-panel-label">Actions rapides</span>
                        <div class="dossier-create-actions">
                            <a href="{{ route('dossiers.index') }}" class="dossier-create-btn dossier-create-btn-soft">
                                <span class="dossier-create-btn-icon"><i class="fas fa-arrow-left"></i></span>
                                <span>Dossiers actifs</span>
                            </a>
                            <a href="{{ route('dossiers.archives') }}" class="dossier-create-btn dossier-create-btn-soft">
                                <span class="dossier-create-btn-icon"><i class="fas fa-box-archive"></i></span>
                                <span>Archives</span>
                            </a>
                            <button type="submit" form="dossierCreateForm" class="dossier-create-btn dossier-create-btn-primary">
                                <span class="dossier-create-btn-icon"><i class="fas fa-save"></i></span>
                                <span>Créer le dossier</span>
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </header>

        <div class="dossier-create-layout">
            <aside class="dossier-create-card dossier-create-side">
                <div class="dossier-create-patient-head">
                    <span class="dossier-create-avatar" id="createPatientAvatar" aria-hidden="true">{{ $selectedInitials }}</span>
                    <div class="dossier-create-patient-copy">
                        <h2 class="dossier-create-side-name" id="createPatientName">{{ $selectedFullName }}</h2>
                        <p class="dossier-create-side-subtitle">Nouveau dossier médical</p>
                    </div>
                </div>

                <div class="dossier-create-side-badges">
                    <span class="dossier-create-chip" id="createStatusChip">Statut: {{ ucfirst($selectedStatus) }}</span>
                    <span class="dossier-create-chip" id="createTypeChip">Type: {{ $selectedType }}</span>
                    <span class="dossier-create-chip"><i class="fas fa-calendar-day"></i>{{ $openDateDisplay }}</span>
                </div>

                <h2 class="dossier-create-side-title">Résumé de préparation</h2>
                <ul class="dossier-create-side-list">
                    <li>
                        <small>Patient</small>
                        <strong id="createSummaryPatient">{{ $selectedFullName }}</strong>
                    </li>
                    <li>
                        <small>Numéro</small>
                        <strong id="createSummaryNumero">{{ old('numero_dossier', 'Généré automatiquement') }}</strong>
                    </li>
                    <li>
                        <small>Date de naissance</small>
                        <strong id="createSummaryBirth">{{ $selectedBirthDisplay }}</strong>
                    </li>
                    <li>
                        <small>Téléphone</small>
                        <strong id="createSummaryPhone">{{ $selectedPatient && $selectedPatient->telephone ? $selectedPatient->telephone : 'Non renseigné' }}</strong>
                    </li>
                    <li>
                        <small>Ouverture</small>
                        <strong id="createSummaryDate">{{ $openDateDisplay }}</strong>
                    </li>
                    <li>
                        <small>Pièces jointes</small>
                        <strong id="createSummaryFiles">Aucun document sélectionné</strong>
                    </li>
                </ul>
            </aside>

            <section class="dossier-create-card dossier-create-main">
                <div class="dossier-create-main-head">
                    <div>
                        <h2 class="dossier-create-main-title">Formulaire de création structuré</h2>
                        <p class="dossier-create-title-subtitle">Renseignez les informations administratives, le contexte médical initial et les documents associés dans une séquence plus lisible pour le secrétariat et le médical.</p>
                    </div>
                    <span class="dossier-create-badge">Nouveau dossier</span>
                </div>

                <form action="{{ route('dossiers.store') }}" method="POST" enctype="multipart/form-data" id="dossierCreateForm">
                    @csrf
                    <div class="dossier-create-body">
                        <section class="dossier-create-section">
                            <div class="dossier-create-section-head">
                                <div class="dossier-create-section-title">
                                    <span class="dossier-create-section-icon"><i class="fas fa-circle-info"></i></span>
                                    <div>
                                        <h3>Informations générales</h3>
                                        <p class="dossier-create-section-help">Identifiez le patient, le numéro de dossier et les paramètres administratifs d’ouverture.</p>
                                    </div>
                                </div>
                                <span class="dossier-create-section-tag">Base</span>
                            </div>
                            <div class="dossier-create-section-body">
                                <div class="dossier-create-grid">
                                    <div class="dossier-create-field">
                                        <label for="patient_id">Patient <span class="required">*</span></label>
                                        <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                            <option value="">Sélectionner un patient...</option>
                                            @foreach($patients as $patient)
                                                @php
                                                    $patientFullName = trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? ''));
                                                    $patientBirthDisplay = $patient->date_naissance ? \Illuminate\Support\Carbon::parse($patient->date_naissance)->format('d/m/Y') : 'Non renseignée';
                                                @endphp
                                                <option
                                                    value="{{ $patient->id }}"
                                                    data-full-name="{{ $patientFullName }}"
                                                    data-phone="{{ $patient->telephone ?? '' }}"
                                                    data-birth-display="{{ $patientBirthDisplay }}"
                                                    {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                                    {{ $patientFullName }}@if($patient->cin) ({{ $patient->cin }})@endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="dossier-create-field-hint">Le patient sélectionné alimente automatiquement le résumé latéral et la proposition de numéro.</p>
                                        @error('patient_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-create-field">
                                        <label for="numero_dossier">Numéro de dossier <span class="required">*</span></label>
                                        <input type="text" class="form-control @error('numero_dossier') is-invalid @enderror" id="numero_dossier" name="numero_dossier" value="{{ old('numero_dossier') }}" required placeholder="Ex: DOS-202603-001">
                                        <p class="dossier-create-field-hint">Unique pour le cabinet. Une proposition automatique est générée après sélection du patient.</p>
                                        @error('numero_dossier')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-create-field">
                                        <label for="type">Type de dossier</label>
                                        <input type="text" class="form-control @error('type') is-invalid @enderror" id="type" name="type" list="dossier-type-options" value="{{ $selectedType }}" placeholder="Ex: général, urgence, suivi...">
                                        @if(($typeOptions ?? collect())->isNotEmpty())
                                            <datalist id="dossier-type-options">
                                                @foreach($typeOptions as $typeOption)
                                                    <option value="{{ $typeOption }}"></option>
                                                @endforeach
                                            </datalist>
                                        @endif
                                        <p class="dossier-create-field-hint">Décrit la nature du suivi dès l’ouverture du dossier.</p>
                                        @error('type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-create-field">
                                        <label for="date_ouverture">Date d’ouverture</label>
                                        <input type="date" class="form-control @error('date_ouverture') is-invalid @enderror" id="date_ouverture" name="date_ouverture" value="{{ old('date_ouverture', date('Y-m-d')) }}">
                                        <p class="dossier-create-field-hint">Par défaut sur la date du jour, ajustable si le dossier a été ouvert antérieurement.</p>
                                        @error('date_ouverture')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-create-field">
                                        <label for="statut">Statut <span class="required">*</span></label>
                                        <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                            <option value="">Sélectionner le statut...</option>
                                            <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                                            <option value="archive" {{ old('statut') == 'archive' ? 'selected' : '' }}>Archivé</option>
                                        </select>
                                        <p class="dossier-create-field-hint">Le statut peut être ajusté plus tard sans modifier l’historique du dossier.</p>
                                        @error('statut')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="dossier-create-section">
                            <div class="dossier-create-section-head">
                                <div class="dossier-create-section-title">
                                    <span class="dossier-create-section-icon"><i class="fas fa-heart-pulse"></i></span>
                                    <div>
                                        <h3>Contexte médical initial</h3>
                                        <p class="dossier-create-section-help">Préparez les premières informations cliniques visibles dès l’ouverture du dossier.</p>
                                    </div>
                                </div>
                                <span class="dossier-create-section-tag">Médical</span>
                            </div>
                            <div class="dossier-create-section-body">
                                <div class="dossier-create-grid">
                                    <div class="dossier-create-field full">
                                        <label for="observations">Observations initiales</label>
                                        <textarea class="form-control @error('observations') is-invalid @enderror" id="observations" name="observations" rows="4" placeholder="Observations générales sur le patient...">{{ old('observations') }}</textarea>
                                        <p class="dossier-create-field-hint">Notes générales utiles pour contextualiser le suivi clinique.</p>
                                        @error('observations')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-create-field full">
                                        <label for="diagnostic">Diagnostic initial</label>
                                        <textarea class="form-control @error('diagnostic') is-invalid @enderror" id="diagnostic" name="diagnostic" rows="4" placeholder="Diagnostic préliminaire...">{{ old('diagnostic') }}</textarea>
                                        <p class="dossier-create-field-hint">Synthèse du motif clinique ou de l’hypothèse diagnostique au démarrage.</p>
                                        @error('diagnostic')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-create-field full">
                                        <label for="traitement">Traitement initial</label>
                                        <textarea class="form-control @error('traitement') is-invalid @enderror" id="traitement" name="traitement" rows="4" placeholder="Traitement prescrit...">{{ old('traitement') }}</textarea>
                                        <p class="dossier-create-field-hint">Traitement ou conduite à tenir renseignés lors de l’ouverture du dossier.</p>
                                        @error('traitement')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-create-field full">
                                        <label for="prescriptions">Prescriptions initiales</label>
                                        <textarea class="form-control @error('prescriptions') is-invalid @enderror" id="prescriptions" name="prescriptions" rows="4" placeholder="Prescriptions médicales...">{{ old('prescriptions') }}</textarea>
                                        <p class="dossier-create-field-hint">Ordonnances, examens ou recommandations initiales associées au dossier.</p>
                                        @error('prescriptions')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="dossier-create-section">
                            <div class="dossier-create-section-head">
                                <div class="dossier-create-section-title">
                                    <span class="dossier-create-section-icon"><i class="fas fa-paperclip"></i></span>
                                    <div>
                                        <h3>Documents et pièces jointes</h3>
                                        <p class="dossier-create-section-help">Ajoutez les documents utiles à l’ouverture: scans, résultats, ordonnances ou justificatifs.</p>
                                    </div>
                                </div>
                                <span class="dossier-create-section-tag">Documents</span>
                            </div>
                            <div class="dossier-create-section-body">
                                <div class="dossier-create-grid">
                                    <div class="dossier-create-field full">
                                        <label for="documents">Documents joints</label>
                                        <input type="file" class="form-control @error('documents') is-invalid @enderror" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        <p class="dossier-create-field-hint">Formats acceptés: PDF, DOC, DOCX, JPG, PNG. Taille maximale: 5 Mo par fichier.</p>
                                        @error('documents')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-create-field full">
                                        <label>Rappels utiles</label>
                                        <ul class="dossier-create-list">
                                            <li>Les champs marqués d’une étoile sont obligatoires.</li>
                                            <li>Le numéro de dossier doit rester unique au sein du cabinet.</li>
                                            <li>Les informations médicales peuvent être enrichies lors des consultations suivantes.</li>
                                            <li>Les documents transmis sont stockés de manière sécurisée.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="dossier-create-footer">
                        <a href="{{ route('dossiers.index') }}" class="dossier-create-btn dossier-create-btn-soft">
                            <span class="dossier-create-btn-icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Annuler</span>
                        </a>
                        <button type="submit" class="dossier-create-btn dossier-create-btn-primary">
                            <span class="dossier-create-btn-icon"><i class="fas fa-folder-plus"></i></span>
                            <span>Créer le dossier</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="dossier-create-mobile-actions">
        <a href="{{ route('dossiers.index') }}" class="dossier-create-btn dossier-create-btn-soft">
            <span class="dossier-create-btn-icon"><i class="fas fa-arrow-left"></i></span>
            <span>Retour</span>
        </a>
        <button type="submit" form="dossierCreateForm" class="dossier-create-btn dossier-create-btn-primary">
            <span class="dossier-create-btn-icon"><i class="fas fa-save"></i></span>
            <span>Créer</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const patientSelect = document.getElementById('patient_id');
        const numeroInput = document.getElementById('numero_dossier');
        const statusSelect = document.getElementById('statut');
        const typeInput = document.getElementById('type');
        const dateInput = document.getElementById('date_ouverture');
        const fileInput = document.getElementById('documents');

        const patientNameTargets = [
            document.getElementById('createPatientKpi'),
            document.getElementById('createPatientName'),
            document.getElementById('createSummaryPatient')
        ];

        const avatarTarget = document.getElementById('createPatientAvatar');
        const birthTarget = document.getElementById('createSummaryBirth');
        const phoneTarget = document.getElementById('createSummaryPhone');
        const numeroTarget = document.getElementById('createSummaryNumero');
        const statusTargets = [
            document.getElementById('createStatusKpi'),
            document.getElementById('createStatusChip')
        ];
        const typeChipTarget = document.getElementById('createTypeChip');
        const dateTargets = [
            document.getElementById('createDateKpi'),
            document.getElementById('createSummaryDate')
        ];
        const filesTarget = document.getElementById('createSummaryFiles');

        function formatDate(value) {
            if (!value) {
                return 'Non renseignée';
            }

            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return date.toLocaleDateString('fr-FR');
        }

        function initialsFromName(name) {
            const cleanName = (name || '').trim();
            if (!cleanName) {
                return 'ND';
            }

            return cleanName
                .split(/\s+/)
                .filter(Boolean)
                .slice(0, 2)
                .map(function (part) {
                    return part.charAt(0).toUpperCase();
                })
                .join('');
        }

        function updateNumeroSummary() {
            if (numeroTarget) {
                numeroTarget.textContent = numeroInput && numeroInput.value.trim() ? numeroInput.value.trim() : 'Généré automatiquement';
            }
        }

        function generateNumero() {
            if (!numeroInput || !patientSelect || numeroInput.value.trim() || !patientSelect.value) {
                return;
            }

            const patientId = String(patientSelect.value).padStart(3, '0');
            const sourceDate = dateInput && dateInput.value ? new Date(dateInput.value) : new Date();
            const year = sourceDate.getFullYear();
            const month = String(sourceDate.getMonth() + 1).padStart(2, '0');
            numeroInput.value = 'DOS-' + year + month + '-' + patientId;
            updateNumeroSummary();
        }

        function updatePatientSummary() {
            const selectedOption = patientSelect && patientSelect.selectedOptions.length ? patientSelect.selectedOptions[0] : null;
            const fullName = selectedOption && selectedOption.value ? (selectedOption.dataset.fullName || selectedOption.textContent.trim()) : 'Patient à sélectionner';
            const phone = selectedOption && selectedOption.value ? (selectedOption.dataset.phone || 'Non renseigné') : 'Non renseigné';
            const birth = selectedOption && selectedOption.value ? (selectedOption.dataset.birthDisplay || 'Non renseignée') : 'À compléter après sélection';

            patientNameTargets.forEach(function (target) {
                if (target) {
                    target.textContent = fullName;
                }
            });

            if (avatarTarget) {
                avatarTarget.textContent = initialsFromName(fullName);
            }

            if (birthTarget) {
                birthTarget.textContent = birth;
            }

            if (phoneTarget) {
                phoneTarget.textContent = phone;
            }

            generateNumero();
        }

        function updateStatusSummary() {
            const value = statusSelect && statusSelect.value ? statusSelect.value : 'actif';
            const label = value.charAt(0).toUpperCase() + value.slice(1);
            statusTargets.forEach(function (target, index) {
                if (target) {
                    target.textContent = index === 1 ? 'Statut: ' + label : label;
                }
            });
        }

        function updateTypeSummary() {
            if (typeChipTarget) {
                typeChipTarget.textContent = 'Type: ' + (typeInput && typeInput.value.trim() ? typeInput.value.trim() : 'général');
            }
        }

        function updateDateSummary() {
            const formatted = formatDate(dateInput && dateInput.value ? dateInput.value : '');
            dateTargets.forEach(function (target) {
                if (target) {
                    target.textContent = formatted;
                }
            });
        }

        function updateFilesSummary() {
            if (!filesTarget || !fileInput) {
                return;
            }

            if (!fileInput.files || !fileInput.files.length) {
                filesTarget.textContent = 'Aucun document sélectionné';
                return;
            }

            filesTarget.textContent = fileInput.files.length === 1
                ? fileInput.files[0].name
                : fileInput.files.length + ' documents sélectionnés';
        }

        if (patientSelect) {
            patientSelect.addEventListener('change', updatePatientSummary);
        }

        if (numeroInput) {
            numeroInput.addEventListener('input', updateNumeroSummary);
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', updateStatusSummary);
        }

        if (typeInput) {
            typeInput.addEventListener('input', updateTypeSummary);
        }

        if (dateInput) {
            dateInput.addEventListener('change', function () {
                updateDateSummary();
                if (!numeroInput.value.trim()) {
                    generateNumero();
                }
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', updateFilesSummary);
        }

        updatePatientSummary();
        updateNumeroSummary();
        updateStatusSummary();
        updateTypeSummary();
        updateDateSummary();
        updateFilesSummary();
    });
</script>
@endpush