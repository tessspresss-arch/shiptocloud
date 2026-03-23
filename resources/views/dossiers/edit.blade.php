@extends('layouts.app')

@section('title', 'Modifier le Dossier Médical - ' . $dossier->numero_dossier)
@section('topbar_subtitle', 'Edition structuree du dossier, repères cliniques et actions rapides dans une interface premium harmonisee.')

@push('styles')
<style>
.dossier-edit-page {
    --dossier-edit-primary: #2c7be5;
    --dossier-edit-primary-strong: #1f5ea8;
    --dossier-edit-accent: #0ea5e9;
    --dossier-edit-success: #0f9f77;
    --dossier-edit-surface: linear-gradient(180deg, #f4f8fd 0%, #eef5fb 100%);
    --dossier-edit-card: #ffffff;
    --dossier-edit-border: #d8e4f2;
    --dossier-edit-text: #15314d;
    --dossier-edit-muted: #5f7896;
    width: 100%;
    max-width: none;
    padding: 10px 8px 92px;
}

.dossier-edit-shell {
    display: grid;
    gap: 16px;
}

.dossier-edit-breadcrumbs {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 12px;
    padding: 0;
    list-style: none;
    font-size: .8rem;
    color: var(--dossier-edit-muted);
    font-weight: 700;
}

.dossier-edit-breadcrumbs a {
    color: inherit;
    text-decoration: none;
}

.dossier-edit-breadcrumbs a:hover {
    color: var(--dossier-edit-primary);
}

.dossier-edit-breadcrumb-separator {
    color: #98abc0;
}

.dossier-edit-title {
    margin: 0;
    font-size: clamp(1.45rem, 2.5vw, 2.1rem);
    font-weight: 800;
    line-height: 1.06;
    letter-spacing: -0.04em;
    color: var(--dossier-edit-text);
}

.dossier-edit-title-subtitle {
    margin: 10px 0 0;
    max-width: 72ch;
    color: var(--dossier-edit-muted);
    font-size: .97rem;
    line-height: 1.6;
    font-weight: 600;
}

.dossier-edit-hero {
    position: relative;
    overflow: hidden;
    display: grid;
    gap: 16px;
    padding: 18px;
    border-radius: 22px;
    border: 1px solid var(--dossier-edit-border);
    background:
        radial-gradient(circle at top right, rgba(44, 123, 229, 0.16) 0%, rgba(44, 123, 229, 0) 32%),
        radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
        var(--dossier-edit-surface);
    box-shadow: 0 24px 48px -38px rgba(20, 52, 84, 0.42);
}

.dossier-edit-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0) 100%);
}

.dossier-edit-hero > * {
    position: relative;
    z-index: 1;
}

.dossier-edit-hero-head {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.95fr);
    gap: 16px;
    align-items: start;
}

.dossier-edit-hero-main {
    min-width: 0;
}

.dossier-edit-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.dossier-edit-title-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #ffffff;
    font-size: 1.3rem;
    background: linear-gradient(135deg, var(--dossier-edit-primary) 0%, var(--dossier-edit-primary-strong) 100%);
    box-shadow: 0 16px 26px -18px rgba(44, 123, 229, 0.58);
}

.dossier-edit-title-block {
    min-width: 0;
}

.dossier-edit-hero-eyebrow {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
}

.dossier-edit-hero-tools {
    display: grid;
    gap: 12px;
}

.dossier-edit-panel {
    border: 1px solid rgba(208, 221, 237, 0.96);
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.78);
    padding: 14px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.68);
}

.dossier-edit-panel-label {
    display: block;
    margin-bottom: 10px;
    color: var(--dossier-edit-muted);
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 800;
}

.dossier-edit-panel-copy {
    margin: 0;
    color: var(--dossier-edit-muted);
    font-size: .88rem;
    line-height: 1.55;
}

.dossier-edit-panel-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 9px;
}

.dossier-edit-panel-list li {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid rgba(208, 221, 237, 0.92);
    background: rgba(247, 251, 255, 0.84);
    color: var(--dossier-edit-muted);
    font-size: .84rem;
    font-weight: 700;
}

.dossier-edit-panel-list strong {
    color: var(--dossier-edit-text);
    font-weight: 800;
    text-align: right;
}

.dossier-edit-panel-note {
    margin: 10px 0 0;
    color: var(--dossier-edit-muted);
    font-size: .82rem;
    line-height: 1.55;
}

.dossier-edit-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.dossier-edit-btn {
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

.dossier-edit-btn:hover,
.dossier-edit-btn:focus {
    transform: translateY(-1px);
    text-decoration: none;
}

.dossier-edit-btn-soft {
    border-color: #cfdef0;
    background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
    color: #385674;
    box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
}

.dossier-edit-btn-soft:hover,
.dossier-edit-btn-soft:focus {
    color: #1f6fa3;
    border-color: rgba(44, 123, 229, 0.3);
    background: linear-gradient(180deg, #ffffff 0%, #edf5fb 100%);
}

.dossier-edit-btn-primary {
    background: linear-gradient(135deg, var(--dossier-edit-primary) 0%, var(--dossier-edit-primary-strong) 100%);
    color: #fff;
    box-shadow: 0 18px 28px -22px rgba(44, 123, 229, 0.55);
}

.dossier-edit-btn-primary:hover,
.dossier-edit-btn-primary:focus {
    color: #fff;
}

.dossier-edit-btn-icon {
    width: 28px;
    height: 28px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(44, 123, 229, 0.1);
    color: var(--dossier-edit-primary);
}

.dossier-edit-btn-primary .dossier-edit-btn-icon {
    background: rgba(255, 255, 255, 0.16);
    color: inherit;
}

.dossier-edit-kpis {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.dossier-edit-kpi {
    padding: 12px 14px;
    border-radius: 16px;
    border: 1px solid rgba(206, 221, 238, 0.96);
    background: rgba(255, 255, 255, 0.72);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.62);
}

.dossier-edit-kpi-label {
    display: block;
    margin-bottom: 6px;
    color: var(--dossier-edit-muted);
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 800;
}

.dossier-edit-kpi-value {
    display: block;
    color: var(--dossier-edit-text);
    font-size: 1.2rem;
    font-weight: 900;
    line-height: 1;
}

.dossier-edit-kpi-meta {
    display: block;
    margin-top: 5px;
    color: #7290b0;
    font-size: .82rem;
    font-weight: 600;
}

.dossier-edit-layout {
    display: grid;
    grid-template-columns: 320px minmax(0, 1fr);
    gap: 16px;
    align-items: start;
}

.dossier-edit-card {
    background: var(--dossier-edit-card);
    border: 1px solid var(--dossier-edit-border);
    border-radius: 22px;
    box-shadow: 0 22px 34px -34px rgba(15, 23, 42, 0.44);
}

.dossier-edit-side {
    overflow: hidden;
    position: sticky;
    top: 92px;
    padding: 18px;
}

.dossier-edit-side::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 128px;
    pointer-events: none;
    background:
        radial-gradient(circle at top right, rgba(44, 123, 229, 0.18) 0%, rgba(44, 123, 229, 0) 44%),
        linear-gradient(180deg, rgba(244, 249, 255, 0.92) 0%, rgba(244, 249, 255, 0) 100%);
}

.dossier-edit-side > * {
    position: relative;
    z-index: 1;
}

.dossier-edit-patient-head {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    margin-bottom: 16px;
}

.dossier-edit-avatar {
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
    background: linear-gradient(135deg, var(--dossier-edit-primary) 0%, var(--dossier-edit-accent) 100%);
    box-shadow: 0 18px 28px -20px rgba(44, 123, 229, 0.56);
}

.dossier-edit-patient-copy {
    min-width: 0;
}

.dossier-edit-side-name {
    margin: 0;
    font-size: 1.22rem;
    line-height: 1.08;
    font-weight: 800;
    color: var(--dossier-edit-text);
}

.dossier-edit-side-subtitle {
    margin: 5px 0 0;
    color: var(--dossier-edit-muted);
    font-size: .88rem;
    font-weight: 700;
}

.dossier-edit-side-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.dossier-edit-chip {
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

.dossier-edit-side-title {
    margin: 0 0 12px;
    font-size: .92rem;
    font-weight: 800;
    color: var(--dossier-edit-text);
}

.dossier-edit-side-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.dossier-edit-side-list li {
    border: 1px solid #e2ebf6;
    border-radius: 16px;
    background: #fbfdff;
    padding: 12px;
}

.dossier-edit-side-list small {
    color: var(--dossier-edit-muted);
    display: block;
    font-size: .68rem;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 800;
}

.dossier-edit-side-list strong {
    font-size: .92rem;
    line-height: 1.45;
    color: var(--dossier-edit-text);
}

.dossier-edit-main {
    overflow: hidden;
}

.dossier-edit-main-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 18px 18px 0;
    background: transparent;
    border-bottom: 0;
}

.dossier-edit-main-title {
    margin: 0;
    font-size: 1.16rem;
    font-weight: 800;
    color: var(--dossier-edit-text);
}

.dossier-edit-badge {
    background: #eef6ff;
    border: 1px solid #d4e2f2;
    color: var(--dossier-edit-primary-strong);
    border-radius: 999px;
    padding: 5px 12px;
    font-size: .76rem;
    font-weight: 800;
}

.dossier-edit-body {
    padding: 18px;
    display: grid;
    gap: 16px;
}

.dossier-edit-section {
    border: 1px solid #dfe9f5;
    border-radius: 20px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    box-shadow: 0 16px 28px -30px rgba(15, 23, 42, 0.32);
    overflow: hidden;
}

.dossier-edit-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid #e6eef8;
    background: linear-gradient(180deg, #f7fbff 0%, #eff6fd 100%);
}

.dossier-edit-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.dossier-edit-section-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(44, 123, 229, 0.1);
    color: var(--dossier-edit-primary);
}

.dossier-edit-section-head h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    color: var(--dossier-edit-text);
}

.dossier-edit-section-help {
    margin: 3px 0 0;
    color: var(--dossier-edit-muted);
    font-size: .84rem;
    line-height: 1.45;
}

.dossier-edit-section-tag {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 0 10px;
    border-radius: 999px;
    background: #eef6ff;
    color: var(--dossier-edit-primary-strong);
    font-size: .75rem;
    font-weight: 800;
}

.dossier-edit-section-body {
    padding: 16px;
}

.dossier-edit-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.dossier-field {
    display: flex;
    flex-direction: column;
}

.dossier-field.full {
    grid-column: 1 / -1;
}

.dossier-field label {
    font-size: .78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--dossier-edit-muted);
    margin-bottom: 8px;
}

.dossier-field .form-control,
.dossier-field textarea {
    min-height: 52px;
    border-radius: 14px;
    border: 1px solid #d4e1ee;
    background: #fff;
    color: var(--dossier-edit-text);
    padding: 13px 14px;
    font-size: .95rem;
    font-weight: 600;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.78), 0 10px 24px -28px rgba(15, 23, 42, 0.28);
    transition: border-color .2s ease, box-shadow .2s ease, background .2s ease, transform .2s ease;
}

.dossier-field .form-control:focus,
.dossier-field textarea:focus {
    border-color: rgba(44, 123, 229, 0.46);
    box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.12), 0 14px 28px -26px rgba(31, 111, 163, 0.34);
    transform: translateY(-1px);
}

.dossier-field textarea {
    min-height: 128px;
    resize: vertical;
}

.dossier-field .form-control[readonly] {
    background: #f8fbff;
    color: #47627f;
}

.dossier-field-hint {
    margin-top: 8px;
    color: var(--dossier-edit-muted);
    font-size: .83rem;
    line-height: 1.45;
}

.dossier-field .invalid-feedback.d-block {
    margin-top: 8px;
    font-size: .84rem;
    font-weight: 700;
}

.dossier-edit-footer {
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

.dossier-edit-mobile-actions {
    display: none;
}

html.dark body .dossier-edit-page,
body.dark-mode .dossier-edit-page,
body.theme-dark .dossier-edit-page {
    --dossier-edit-surface: linear-gradient(180deg, #152233 0%, #122032 100%);
    --dossier-edit-card: #162332;
    --dossier-edit-border: #2f4358;
    --dossier-edit-text: #e6edf6;
    --dossier-edit-muted: #9eb1c7;
}

html.dark body .dossier-edit-side-list li,
html.dark body .dossier-edit-kpi,
html.dark body .dossier-edit-panel,
html.dark body .dossier-edit-panel-list li,
body.dark-mode .dossier-edit-side-list li,
body.dark-mode .dossier-edit-kpi,
body.dark-mode .dossier-edit-panel,
body.dark-mode .dossier-edit-panel-list li,
body.theme-dark .dossier-edit-side-list li,
body.theme-dark .dossier-edit-kpi,
body.theme-dark .dossier-edit-panel,
body.theme-dark .dossier-edit-panel-list li {
    background: rgba(17, 34, 54, 0.88);
    border-color: #35506a;
}

html.dark body .dossier-edit-btn-soft,
body.dark-mode .dossier-edit-btn-soft,
body.theme-dark .dossier-edit-btn-soft {
    border-color: #365b7d;
    background: linear-gradient(150deg, #183552 0%, #14304b 100%);
    color: #d2e6fb;
}

html.dark body .dossier-edit-btn-soft:hover,
html.dark body .dossier-edit-btn-soft:focus,
body.dark-mode .dossier-edit-btn-soft:hover,
body.dark-mode .dossier-edit-btn-soft:focus,
body.theme-dark .dossier-edit-btn-soft:hover,
body.theme-dark .dossier-edit-btn-soft:focus {
    border-color: #4c7094;
    background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
    color: #ffffff;
}

html.dark body .dossier-edit-btn-icon,
html.dark body .dossier-edit-section-icon,
body.dark-mode .dossier-edit-btn-icon,
body.theme-dark .dossier-edit-btn-icon,
body.dark-mode .dossier-edit-section-icon,
body.theme-dark .dossier-edit-section-icon {
    background: rgba(119, 183, 255, 0.16);
    color: #9fd0ff;
}

html.dark body .dossier-edit-section,
body.dark-mode .dossier-edit-section,
body.theme-dark .dossier-edit-section {
    background: #0f1a28;
    border-color: #2f4358;
}

html.dark body .dossier-edit-section-head,
body.dark-mode .dossier-edit-section-head,
body.theme-dark .dossier-edit-section-head {
    background: #16273d;
    border-color: #294055;
}

html.dark body .dossier-edit-main-title,
html.dark body .dossier-edit-side-title,
html.dark body .dossier-edit-section-head h3,
html.dark body .dossier-edit-side-name,
body.dark-mode .dossier-edit-main-title,
body.dark-mode .dossier-edit-side-title,
body.dark-mode .dossier-edit-section-head h3,
body.dark-mode .dossier-edit-side-name,
body.theme-dark .dossier-edit-main-title,
body.theme-dark .dossier-edit-side-title,
body.theme-dark .dossier-edit-section-head h3,
body.theme-dark .dossier-edit-side-name {
    color: #eef5ff;
}

html.dark body .dossier-field .form-control,
html.dark body .dossier-field textarea,
body.dark-mode .dossier-field .form-control,
body.dark-mode .dossier-field textarea,
body.theme-dark .dossier-field .form-control,
body.theme-dark .dossier-field textarea {
    background: #13263f;
    border-color: #355985;
    color: #deebf9;
}

html.dark body .dossier-field .form-control[readonly],
body.dark-mode .dossier-field .form-control[readonly],
body.theme-dark .dossier-field .form-control[readonly] {
    background: #1a3150;
    color: #b7cee6;
}

html.dark body .dossier-edit-footer,
body.dark-mode .dossier-edit-footer,
body.theme-dark .dossier-edit-footer {
    background: linear-gradient(180deg, rgba(18, 35, 52, 0.84) 0%, rgba(18, 35, 52, 0.98) 100%);
    border-color: #294055;
}

@media (max-width: 1199.98px) {
    .dossier-edit-hero-head {
        grid-template-columns: 1fr;
    }

    .dossier-edit-layout {
        grid-template-columns: 300px minmax(0, 1fr);
    }
}

@media (max-width: 991.98px) {
    .dossier-edit-layout {
        grid-template-columns: 1fr;
    }

    .dossier-edit-side {
        position: static;
    }

    .dossier-edit-grid {
        grid-template-columns: 1fr;
    }

    .dossier-edit-side-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dossier-edit-kpis {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767.98px) {
    .dossier-edit-page {
        padding: 6px 0 88px;
    }

    .dossier-edit-actions,
    .dossier-edit-footer {
        display: none;
    }

    .dossier-edit-mobile-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        position: fixed;
        left: 8px;
        right: 8px;
        bottom: calc(10px + env(safe-area-inset-bottom));
        z-index: 1050;
        background: var(--dossier-edit-card);
        border: 1px solid var(--dossier-edit-border);
        border-radius: 18px;
        padding: 8px;
        box-shadow: 0 16px 24px -20px rgba(0, 0, 0, .46);
    }

    .dossier-edit-mobile-actions .dossier-edit-btn {
        width: 100%;
    }

    .dossier-edit-side-list {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 575.98px) {
    .dossier-edit-hero {
        padding: 14px;
        border-radius: 18px;
    }

    .dossier-edit-title-row {
        align-items: flex-start;
    }

    .dossier-edit-title-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
    }

    .dossier-edit-main-head,
    .dossier-edit-body {
        padding-left: 14px;
        padding-right: 14px;
    }
}
</style>
@endpush

@section('content')
@php
    $patient = $dossier->patient;
    $fullName = trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? '')) ?: 'Patient';
    $initials = collect(preg_split('/\s+/', $fullName) ?: [])->filter()->take(2)->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))->implode('');
    if ($initials === '') {
        $initials = 'P';
    }
    $age = $patient?->date_naissance ? \Illuminate\Support\Carbon::parse($patient->date_naissance)->age : null;
    $documentCount = count($dossier->documents ?? []);
@endphp
<div class="container-fluid dossier-edit-page">
    <div class="dossier-edit-shell">
        <header class="dossier-edit-hero">
            <div class="dossier-edit-hero-head">
                <div class="dossier-edit-hero-main">
                    <ol class="dossier-edit-breadcrumbs" aria-label="Fil d'Ariane édition dossier">
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="dossier-edit-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li><a href="{{ route('dossiers.index') }}">Dossiers</a></li>
                        <li class="dossier-edit-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li><a href="{{ route('dossiers.show', $dossier) }}">{{ $dossier->numero_dossier }}</a></li>
                        <li class="dossier-edit-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li aria-current="page">Édition</li>
                    </ol>

                    <div class="dossier-edit-hero-eyebrow" aria-label="Repères édition dossier">
                        <span class="dossier-edit-chip"><i class="fas fa-fingerprint"></i>{{ $dossier->numero_dossier }}</span>
                        <span class="dossier-edit-chip"><i class="fas {{ ($dossier->statut ?? 'actif') === 'archive' ? 'fa-box-archive' : 'fa-circle-check' }}"></i>{{ ucfirst($dossier->statut ?? 'actif') }}</span>
                        <span class="dossier-edit-chip"><i class="fas fa-calendar-day"></i>{{ $dossier->date_ouverture ? \Illuminate\Support\Carbon::parse($dossier->date_ouverture)->format('d/m/Y') : 'Date non renseignée' }}</span>
                    </div>

                    <div class="dossier-edit-title-row">
                        <span class="dossier-edit-title-icon" aria-hidden="true"><i class="fas fa-pen-to-square"></i></span>
                        <div class="dossier-edit-title-block">
                            <h1 class="dossier-edit-title">Modifier le dossier médical</h1>
                            <p class="dossier-edit-title-subtitle">Mettez à jour les informations cliniques du dossier {{ $dossier->numero_dossier }} avec un formulaire structuré, lisible et cohérent avec l’expérience premium du produit.</p>
                        </div>
                    </div>

                    <div class="dossier-edit-kpis" aria-label="Indicateurs du formulaire">
                        <article class="dossier-edit-kpi">
                            <span class="dossier-edit-kpi-label">Patient</span>
                            <span class="dossier-edit-kpi-value">{{ $fullName }}</span>
                            <span class="dossier-edit-kpi-meta">Dossier {{ $dossier->numero_dossier }}</span>
                        </article>
                        <article class="dossier-edit-kpi">
                            <span class="dossier-edit-kpi-label">Consultations</span>
                            <span class="dossier-edit-kpi-value">{{ $editStats['consultations'] }}</span>
                            <span class="dossier-edit-kpi-meta">Historique clinique associé</span>
                        </article>
                        <article class="dossier-edit-kpi">
                            <span class="dossier-edit-kpi-label">Documents</span>
                            <span class="dossier-edit-kpi-value">{{ $editStats['documents'] }}</span>
                            <span class="dossier-edit-kpi-meta">Pièces jointes déjà rattachées</span>
                        </article>
                    </div>
                </div>

                <div class="dossier-edit-hero-tools">
                    <section class="dossier-edit-panel">
                        <span class="dossier-edit-panel-label">Contexte</span>
                        <ul class="dossier-edit-panel-list">
                            <li>
                                <span>Ordonnances liées</span>
                                <strong>{{ $editStats['ordonnances'] }}</strong>
                            </li>
                            <li>
                                <span>Dernière mise à jour</span>
                                <strong>{{ $dossier->updated_at ? $dossier->updated_at->format('d/m/Y') : 'N/A' }}</strong>
                            </li>
                            <li>
                                <span>Ancienneté du dossier</span>
                                <strong>{{ $dossier->created_at ? $dossier->created_at->diffForHumans() : 'N/A' }}</strong>
                            </li>
                        </ul>
                        <p class="dossier-edit-panel-note">L’édition conserve les champs métier actuels, avec une présentation désormais calée sur l’index, les archives et la fiche détail du module.</p>
                    </section>
                    <section class="dossier-edit-panel">
                        <span class="dossier-edit-panel-label">Actions rapides</span>
                        <div class="dossier-edit-actions">
                            <a href="{{ route('dossiers.show', $dossier) }}" class="dossier-edit-btn dossier-edit-btn-soft">
                                <span class="dossier-edit-btn-icon"><i class="fas fa-arrow-left"></i></span>
                                <span>Fiche dossier</span>
                            </a>
                            <a href="{{ route('dossiers.archives') }}" class="dossier-edit-btn dossier-edit-btn-soft">
                                <span class="dossier-edit-btn-icon"><i class="fas fa-box-archive"></i></span>
                                <span>Archives</span>
                            </a>
                            <button type="submit" form="dossierEditForm" class="dossier-edit-btn dossier-edit-btn-primary">
                                <span class="dossier-edit-btn-icon"><i class="fas fa-save"></i></span>
                                <span>Enregistrer</span>
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </header>

        <div class="dossier-edit-layout">
            <aside class="dossier-edit-card dossier-edit-side">
                <div class="dossier-edit-patient-head">
                    <span class="dossier-edit-avatar" aria-hidden="true">{{ $initials }}</span>
                    <div class="dossier-edit-patient-copy">
                        <h2 class="dossier-edit-side-name">{{ $fullName }}</h2>
                        <p class="dossier-edit-side-subtitle">{{ $dossier->numero_dossier }}</p>
                    </div>
                </div>

                <div class="dossier-edit-side-badges">
                    <span class="dossier-edit-chip">Statut: {{ ucfirst($dossier->statut ?? 'actif') }}</span>
                    @if($age !== null)
                        <span class="dossier-edit-chip">{{ $age }} ans</span>
                    @endif
                    <span class="dossier-edit-chip"><i class="fas fa-paperclip"></i>{{ $documentCount }} document{{ $documentCount > 1 ? 's' : '' }}</span>
                </div>

                <h2 class="dossier-edit-side-title">Résumé patient</h2>
                <ul class="dossier-edit-side-list">
                    <li>
                        <small>Patient</small>
                        <strong>{{ $fullName }}</strong>
                    </li>
                    <li>
                        <small>Dossier</small>
                        <strong>{{ $dossier->numero_dossier }}</strong>
                    </li>
                    <li>
                        <small>Date de naissance</small>
                        <strong>{{ $patient->date_naissance ? \Illuminate\Support\Carbon::parse($patient->date_naissance)->format('d/m/Y') : 'Non renseignée' }}</strong>
                    </li>
                    <li>
                        <small>Téléphone</small>
                        <strong>{{ $patient->telephone ?: 'Non renseigné' }}</strong>
                    </li>
                    <li>
                        <small>Statut</small>
                        <strong>{{ ucfirst($dossier->statut ?? 'actif') }}</strong>
                    </li>
                    <li>
                        <small>Ouverture</small>
                        <strong>{{ $dossier->date_ouverture ? \Illuminate\Support\Carbon::parse($dossier->date_ouverture)->format('d/m/Y') : ($dossier->created_at ? $dossier->created_at->format('d/m/Y') : 'Non renseignée') }}</strong>
                    </li>
                </ul>
            </aside>

            <section class="dossier-edit-card dossier-edit-main">
                <div class="dossier-edit-main-head">
                    <div>
                        <h2 class="dossier-edit-main-title">Formulaire d’édition structuré</h2>
                        <p class="dossier-edit-title-subtitle">Mettez à jour les informations du dossier par sections claires, avec une hiérarchie de lecture pensée pour un usage clinique quotidien.</p>
                    </div>
                    <span class="dossier-edit-badge">{{ $dossier->numero_dossier }}</span>
                </div>

                <form action="{{ route('dossiers.update', $dossier) }}" method="POST" id="dossierEditForm">
                    @csrf
                    @method('PUT')
                    <div class="dossier-edit-body">
                        <section class="dossier-edit-section">
                            <div class="dossier-edit-section-head">
                                <div class="dossier-edit-section-title">
                                    <span class="dossier-edit-section-icon"><i class="fas fa-circle-info"></i></span>
                                    <div>
                                        <h3>Informations générales</h3>
                                        <p class="dossier-edit-section-help">Contexte du dossier et informations patient en lecture rapide.</p>
                                    </div>
                                </div>
                                <span class="dossier-edit-section-tag">Lecture</span>
                            </div>
                            <div class="dossier-edit-section-body">
                                <div class="dossier-edit-grid">
                                    <div class="dossier-field full">
                                        <label>Patient</label>
                                        <input type="text" class="form-control" value="{{ $fullName }}" readonly>
                                        <p class="dossier-field-hint">Le patient associé au dossier ne peut pas être modifié depuis cet écran.</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="dossier-edit-section">
                            <div class="dossier-edit-section-head">
                                <div class="dossier-edit-section-title">
                                    <span class="dossier-edit-section-icon"><i class="fas fa-notes-medical"></i></span>
                                    <div>
                                        <h3>Antécédents</h3>
                                        <p class="dossier-edit-section-help">Historique clinique, antécédents importants et contexte médical de référence.</p>
                                    </div>
                                </div>
                                <span class="dossier-edit-section-tag">Clinique</span>
                            </div>
                            <div class="dossier-edit-section-body">
                                <div class="dossier-edit-grid">
                                    <div class="dossier-field full">
                                        <label for="antecedents">Antécédents médicaux</label>
                                        <textarea name="antecedents" id="antecedents" class="form-control @error('antecedents') is-invalid @enderror" rows="4">{{ old('antecedents', $dossier->antecedents) }}</textarea>
                                        <p class="dossier-field-hint">Renseignez les pathologies, interventions ou éléments cliniques structurants du patient.</p>
                                        @error('antecedents')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="dossier-edit-section">
                            <div class="dossier-edit-section-head">
                                <div class="dossier-edit-section-title">
                                    <span class="dossier-edit-section-icon"><i class="fas fa-triangle-exclamation"></i></span>
                                    <div>
                                        <h3>Allergies</h3>
                                        <p class="dossier-edit-section-help">Éléments de vigilance à afficher clairement pour sécuriser la prise en charge.</p>
                                    </div>
                                </div>
                                <span class="dossier-edit-section-tag">Vigilance</span>
                            </div>
                            <div class="dossier-edit-section-body">
                                <div class="dossier-edit-grid">
                                    <div class="dossier-field full">
                                        <label for="allergies">Allergies</label>
                                        <textarea name="allergies" id="allergies" class="form-control @error('allergies') is-invalid @enderror" rows="3">{{ old('allergies', $dossier->allergies) }}</textarea>
                                        <p class="dossier-field-hint">Mentionnez toute allergie médicamenteuse, alimentaire ou environnementale utile au suivi.</p>
                                        @error('allergies')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="dossier-edit-section">
                            <div class="dossier-edit-section-head">
                                <div class="dossier-edit-section-title">
                                    <span class="dossier-edit-section-icon"><i class="fas fa-capsules"></i></span>
                                    <div>
                                        <h3>Traitements</h3>
                                        <p class="dossier-edit-section-help">Traitements courants et continuité thérapeutique du patient.</p>
                                    </div>
                                </div>
                                <span class="dossier-edit-section-tag">Suivi</span>
                            </div>
                            <div class="dossier-edit-section-body">
                                <div class="dossier-edit-grid">
                                    <div class="dossier-field full">
                                        <label for="traitements_courants">Traitements courants</label>
                                        <textarea name="traitements_courants" id="traitements_courants" class="form-control @error('traitements_courants') is-invalid @enderror" rows="3">{{ old('traitements_courants', $dossier->traitements_courants) }}</textarea>
                                        <p class="dossier-field-hint">Décrivez les traitements actifs ou le protocole actuellement suivi par le patient.</p>
                                        @error('traitements_courants')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="dossier-edit-section">
                            <div class="dossier-edit-section-head">
                                <div class="dossier-edit-section-title">
                                    <span class="dossier-edit-section-icon"><i class="fas fa-folder-open"></i></span>
                                    <div>
                                        <h3>Autres informations</h3>
                                        <p class="dossier-edit-section-help">Vaccinations, examens, comptes rendus et observations générales du dossier.</p>
                                    </div>
                                </div>
                                <span class="dossier-edit-section-tag">Compléments</span>
                            </div>
                            <div class="dossier-edit-section-body">
                                <div class="dossier-edit-grid">
                                    <div class="dossier-field full">
                                        <label for="vaccinations">Vaccinations</label>
                                        <textarea name="vaccinations" id="vaccinations" class="form-control @error('vaccinations') is-invalid @enderror" rows="3">{{ old('vaccinations', $dossier->vaccinations) }}</textarea>
                                        <p class="dossier-field-hint">Renseignez le carnet vaccinal ou les rappels importants liés à la prévention.</p>
                                        @error('vaccinations')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-field full">
                                        <label for="examens_complementaires">Examens complémentaires</label>
                                        <textarea name="examens_complementaires" id="examens_complementaires" class="form-control @error('examens_complementaires') is-invalid @enderror" rows="3" placeholder="Résultats d'examens biologiques, radiologiques, etc.">{{ old('examens_complementaires', $dossier->examens_complementaires) }}</textarea>
                                        <p class="dossier-field-hint">Centralisez ici les résultats ou synthèses d’examens externes utiles au suivi clinique.</p>
                                        @error('examens_complementaires')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-field full">
                                        <label for="comptes_rendus">Comptes rendus</label>
                                        <textarea name="comptes_rendus" id="comptes_rendus" class="form-control @error('comptes_rendus') is-invalid @enderror" rows="3" placeholder="Comptes rendus de consultations, hospitalisations, etc.">{{ old('comptes_rendus', $dossier->comptes_rendus) }}</textarea>
                                        <p class="dossier-field-hint">Ajoutez les synthèses utiles des consultations, hospitalisations ou épisodes de soins.</p>
                                        @error('comptes_rendus')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="dossier-field full">
                                        <label for="observations">Observations générales</label>
                                        <textarea name="observations" id="observations" class="form-control @error('observations') is-invalid @enderror" rows="3">{{ old('observations', $dossier->observations) }}</textarea>
                                        <p class="dossier-field-hint">Utilisez cet espace pour les notes transverses ne relevant pas d’une rubrique spécifique.</p>
                                        @error('observations')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="dossier-edit-footer">
                        <a href="{{ route('dossiers.show', $dossier) }}" class="dossier-edit-btn dossier-edit-btn-soft">
                            <span class="dossier-edit-btn-icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Annuler</span>
                        </a>
                        <button type="submit" class="dossier-edit-btn dossier-edit-btn-primary">
                            <span class="dossier-edit-btn-icon"><i class="fas fa-save"></i></span>
                            <span>Enregistrer</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="dossier-edit-mobile-actions" aria-label="Actions édition dossier">
        <a href="{{ route('dossiers.show', $dossier) }}" class="dossier-edit-btn dossier-edit-btn-soft">
            <span class="dossier-edit-btn-icon"><i class="fas fa-arrow-left"></i></span>
            <span>Annuler</span>
        </a>
        <button type="submit" form="dossierEditForm" class="dossier-edit-btn dossier-edit-btn-primary">
            <span class="dossier-edit-btn-icon"><i class="fas fa-save"></i></span>
            <span>Enregistrer</span>
        </button>
    </div>
</div>
@endsection
