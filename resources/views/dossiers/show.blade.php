@extends('layouts.app')

@section('title', 'Dossier Medical - ' . $dossier->numero_dossier)
@section('topbar_subtitle', 'Lecture detaillee du dossier, actions cliniques et archivage dans une vue harmonisee.')

@push('styles')
<style>
.dossier-page {
    --dossier-primary: #2c7be5;
    --dossier-primary-strong: #1f5ea8;
    --dossier-accent: #0ea5e9;
    --dossier-success: #0f9f77;
    --dossier-warning: #d97706;
    --dossier-surface: linear-gradient(180deg, #f4f8fd 0%, #eef5fb 100%);
    --dossier-surface-strong: #ffffff;
    --dossier-border: #d8e4f2;
    --dossier-text: #15314d;
    --dossier-muted: #5f7896;
    width: 100%;
    max-width: none;
    padding: 10px 8px 92px;
}

.dossier-shell {
    display: grid;
    gap: 16px;
}

.dossier-hero {
    position: relative;
    overflow: hidden;
    display: grid;
    gap: 16px;
    padding: 18px;
    border-radius: 22px;
    border: 1px solid var(--dossier-border);
    background:
        radial-gradient(circle at top right, rgba(44, 123, 229, 0.16) 0%, rgba(44, 123, 229, 0) 32%),
        radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
        var(--dossier-surface);
    box-shadow: 0 24px 48px -38px rgba(20, 52, 84, 0.42);
}

.dossier-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0) 100%);
}

.dossier-hero > * {
    position: relative;
    z-index: 1;
}

.dossier-hero-head {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.95fr);
    gap: 16px;
    align-items: start;
}

.dossier-hero-main {
    min-width: 0;
}

.dossier-breadcrumbs {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 12px;
    padding: 0;
    list-style: none;
    font-size: .8rem;
    color: var(--dossier-muted);
    font-weight: 700;
}

.dossier-breadcrumbs a {
    color: inherit;
    text-decoration: none;
}

.dossier-breadcrumbs a:hover {
    color: var(--dossier-primary);
}

.dossier-breadcrumb-separator {
    color: #98abc0;
}

.dossier-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.dossier-title-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #ffffff;
    font-size: 1.3rem;
    background: linear-gradient(135deg, var(--dossier-primary) 0%, var(--dossier-primary-strong) 100%);
    box-shadow: 0 16px 26px -18px rgba(44, 123, 229, 0.58);
}

.dossier-title-block {
    min-width: 0;
    display: grid;
    gap: 6px;
}

.dossier-title {
    margin: 0;
    font-size: clamp(1.45rem, 2.5vw, 2.15rem);
    font-weight: 800;
    line-height: 1.06;
    letter-spacing: -0.04em;
    color: var(--dossier-text);
}

.dossier-title-subtitle {
    margin: 0;
    max-width: 72ch;
    color: var(--dossier-muted);
    font-size: .97rem;
    line-height: 1.6;
    font-weight: 600;
}

.dossier-chip-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 14px;
}

.dossier-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 34px;
    padding: 0 14px;
    border-radius: 999px;
    border: 1px solid #d4e2f2;
    background: #f6fafe;
    color: #1d4f91;
    font-size: .8rem;
    font-weight: 800;
}

.dossier-chip.archive {
    background: rgba(120, 136, 154, 0.14);
    border-color: rgba(120, 136, 154, 0.18);
    color: #55697e;
}

.dossier-chip.active {
    background: rgba(15, 159, 119, 0.12);
    border-color: rgba(15, 159, 119, 0.18);
    color: #0f7b5c;
}

.dossier-hero-tools {
    display: flex;
    justify-content: flex-end;
    align-items: flex-start;
    align-content: start;
}

.dossier-actions-panel {
    border: 0;
    border-radius: 0;
    background: transparent;
    padding: 0;
    box-shadow: none;
}

.dossier-panel-label {
    display: block;
    margin-bottom: 10px;
    color: var(--dossier-muted);
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 800;
}

.dossier-header-actions,
.dossier-action-group {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.dossier-header-actions {
    justify-content: flex-end;
}

.dossier-action-group {
    min-width: 0;
}

.dossier-btn {
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

.dossier-btn:hover,
.dossier-btn:focus {
    transform: translateY(-1px);
    text-decoration: none;
}

.dossier-btn-soft {
    border-color: #cfdef0;
    background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
    color: #385674;
    box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
}

.dossier-btn-soft:hover,
.dossier-btn-soft:focus {
    color: #1f6fa3;
    border-color: rgba(44, 123, 229, 0.3);
    background: linear-gradient(180deg, #ffffff 0%, #edf5fb 100%);
}

.dossier-btn-primary {
    background: linear-gradient(135deg, var(--dossier-primary) 0%, var(--dossier-primary-strong) 100%);
    color: #fff;
    box-shadow: 0 18px 28px -22px rgba(44, 123, 229, 0.55);
}

.dossier-btn-primary:hover,
.dossier-btn-primary:focus {
    color: #fff;
    box-shadow: 0 20px 32px -22px rgba(31, 94, 168, 0.62);
}

.dossier-btn-danger {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    color: #fff;
    box-shadow: 0 18px 28px -22px rgba(234, 88, 12, 0.44);
}

.dossier-btn-danger:hover,
.dossier-btn-danger:focus {
    color: #fff;
}

.dossier-btn-block {
    width: 100%;
}

.dossier-btn-icon {
    width: 28px;
    height: 28px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(44, 123, 229, 0.1);
    color: var(--dossier-primary);
}

.dossier-btn-primary .dossier-btn-icon,
.dossier-btn-danger .dossier-btn-icon {
    background: rgba(255, 255, 255, 0.16);
    color: inherit;
}

.dossier-layout {
    display: grid;
    grid-template-columns: clamp(272px, 24vw, 330px) minmax(0, 1fr);
    gap: clamp(14px, 1.2vw, 18px);
    align-items: stretch;
}

.dossier-layout > aside,
.dossier-layout > section {
    display: flex;
    min-width: 0;
}

.dossier-card {
    background: var(--dossier-surface-strong);
    border: 1px solid var(--dossier-border);
    border-radius: 22px;
    box-shadow: 0 22px 34px -34px rgba(15, 23, 42, .44);
}

.patient-summary-card {
    flex: 1 1 auto;
    height: 100%;
    padding: 18px;
    position: sticky;
    top: 92px;
    overflow: hidden;
}

.patient-summary-card::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 132px;
    pointer-events: none;
    background:
        radial-gradient(circle at top right, rgba(44, 123, 229, 0.18) 0%, rgba(44, 123, 229, 0) 44%),
        linear-gradient(180deg, rgba(244, 249, 255, 0.92) 0%, rgba(244, 249, 255, 0) 100%);
}

.patient-summary-head {
    position: relative;
    display: flex;
    gap: 12px;
    align-items: flex-start;
    margin-bottom: 16px;
}

.summary-avatar {
    width: 66px;
    height: 66px;
    border-radius: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: 1.1rem;
    background: linear-gradient(135deg, var(--dossier-primary) 0%, var(--dossier-accent) 100%);
    box-shadow: 0 18px 28px -20px rgba(44, 123, 229, 0.56);
}

.summary-head-copy {
    min-width: 0;
    flex: 1 1 auto;
}

.summary-name {
    margin: 0;
    font-size: 1.28rem;
    font-weight: 800;
    line-height: 1.08;
    color: var(--dossier-text);
}

.summary-sub {
    margin: 5px 0 0;
    color: var(--dossier-muted);
    font-size: .88rem;
    font-weight: 700;
}

.summary-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.summary-chip {
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

.summary-chip-strong {
    border-color: #a7d5fc;
    background: #e0f2fe;
    color: #0c4a6e;
}

.summary-meta {
    margin: 0;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.summary-meta div {
    padding: 12px;
    border-radius: 16px;
    border: 1px solid #e2ebf6;
    background: #fbfdff;
}

.summary-meta dt {
    font-size: .68rem;
    color: var(--dossier-muted);
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 6px;
    font-weight: 800;
}

.summary-meta dd {
    margin: 0;
    font-size: .92rem;
    font-weight: 700;
    color: var(--dossier-text);
    line-height: 1.45;
}

.summary-quick-actions {
    margin-top: 16px;
    display: grid;
    gap: 8px;
}

.summary-nav {
    margin-top: 16px;
    display: grid;
    gap: 8px;
}

.summary-nav-link {
    min-height: 44px;
    padding: 0 14px;
    border-radius: 14px;
    border: 1px solid #d9e6f3;
    background: #f8fbff;
    color: #284867;
    font-size: .9rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.summary-nav-link:hover,
.summary-nav-link:focus {
    color: var(--dossier-primary-strong);
    border-color: #bad0e8;
    background: #eef6ff;
    text-decoration: none;
}

.summary-nav-link i:first-child {
    width: 30px;
    height: 30px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(44, 123, 229, 0.1);
    color: var(--dossier-primary);
}

.dossier-main-card {
    padding: 0;
    overflow: hidden;
    min-width: 0;
    flex: 1 1 auto;
    min-height: 100%;
}

.dossier-main-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 18px 18px 0;
}

.dossier-main-head > div {
    min-width: 0;
    flex: 1 1 420px;
}

.dossier-main-kicker {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 10px;
    border-radius: 999px;
    border: 1px solid #d8e7f6;
    background: #f4f9ff;
    color: var(--dossier-primary-strong);
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.dossier-main-title {
    margin: 0;
    font-size: 1.18rem;
    font-weight: 800;
    color: var(--dossier-text);
    line-height: 1.15;
}

.dossier-main-copy {
    margin: 5px 0 0;
    color: var(--dossier-muted);
    font-size: .9rem;
    line-height: 1.5;
}

.dossier-main-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 38px;
    padding: 0 14px;
    border-radius: 999px;
    border: 1px solid #d7e6f4;
    background: #f7fbff;
    color: #274867;
    font-size: .84rem;
    font-weight: 800;
}

.tabs-scroll-wrap {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 16px 18px 12px;
    border-bottom: 1px solid var(--dossier-border);
    scrollbar-width: thin;
}

.dossier-tab-link {
    border: 1px solid #d6e4f2;
    background: linear-gradient(180deg, #fbfdff 0%, #f5f9fd 100%);
    color: #355273;
    border-radius: 14px;
    padding: 10px 14px;
    font-size: .9rem;
    font-weight: 800;
    white-space: nowrap;
    transition: all .2s ease;
}

.dossier-tab-link.active,
.dossier-tab-link:hover,
.dossier-tab-link:focus-visible {
    background: linear-gradient(135deg, rgba(44, 123, 229, 0.12) 0%, rgba(14, 165, 233, 0.08) 100%);
    border-color: #91bbea;
    color: var(--dossier-primary-strong);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
    outline: none;
}

.dossier-tab-content {
    padding: 18px;
}

.section-grid-two {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.section-card {
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    border: 1px solid #dfe9f5;
    border-radius: 18px;
    padding: 16px;
    box-shadow: 0 16px 28px -30px rgba(15, 23, 42, 0.32);
}

.section-card-full {
    width: 100%;
}

.section-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
}

.section-card h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    color: #1c4068;
}

.section-card p {
    margin: 0;
    color: var(--dossier-muted);
    line-height: 1.75;
    font-size: .95rem;
}

.section-card-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(44, 123, 229, 0.1);
    color: var(--dossier-primary);
    flex-shrink: 0;
}

.section-card-note {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 10px;
    border-radius: 999px;
    background: #eef6ff;
    color: var(--dossier-primary-strong);
    font-size: .75rem;
    font-weight: 800;
}

.timeline-list {
    display: grid;
    gap: 14px;
}

.timeline-item {
    display: grid;
    grid-template-columns: 18px minmax(0, 1fr);
    gap: 12px;
    align-items: start;
}

.timeline-dot {
    width: 12px;
    height: 12px;
    border-radius: 999px;
    margin-top: 14px;
    background: linear-gradient(135deg, var(--dossier-primary) 0%, var(--dossier-accent) 100%);
    box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.12);
}

.timeline-content {
    border: 1px solid #dfe9f5;
    border-radius: 18px;
    padding: 14px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    box-shadow: 0 16px 28px -30px rgba(15, 23, 42, 0.32);
}

.timeline-head {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.timeline-head span {
    color: var(--dossier-muted);
    font-size: .82rem;
    font-weight: 700;
}

.timeline-content p {
    margin: 0;
    color: var(--dossier-muted);
    line-height: 1.65;
}

.timeline-body-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 12px;
}

.responsive-table {
    overflow-x: auto;
    border: 1px solid #dfe9f5;
    border-radius: 18px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    box-shadow: 0 16px 28px -30px rgba(15, 23, 42, 0.32);
}

.dossier-table {
    width: 100%;
    margin: 0;
}

.dossier-table thead {
    background: #f7fbff;
}

.dossier-table th,
.dossier-table td {
    white-space: nowrap;
    padding: 14px 16px;
    border-color: #e4edf7;
}

.dossier-table th {
    color: #3a5676;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 800;
}

.dossier-table td {
    color: var(--dossier-text);
    font-size: .9rem;
    font-weight: 600;
}

.empty-state {
    border: 1px dashed #d2dfed;
    border-radius: 18px;
    padding: 28px 18px;
    text-align: center;
    color: var(--dossier-muted);
    background: linear-gradient(180deg, #fcfdff 0%, #f7fbff 100%);
}

.empty-state i {
    font-size: 1.45rem;
    margin-bottom: 10px;
    color: #4f8dd6;
}

.section-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid #e4edf7;
}

.section-actions .dossier-btn {
    min-width: 210px;
}

.dossier-mobile-actionbar {
    display: none;
}

body.dark-mode .dossier-page,
body.theme-dark .dossier-page {
    --dossier-surface: linear-gradient(180deg, #152233 0%, #122032 100%);
    --dossier-surface-strong: #162332;
    --dossier-border: #2f4358;
    --dossier-text: #e6edf6;
    --dossier-muted: #9eb1c7;
}

body.dark-mode .dossier-actions-panel,
body.dark-mode .summary-meta div,
body.theme-dark .dossier-actions-panel,
body.theme-dark .summary-meta div {
    background: rgba(17, 34, 54, 0.88);
    border-color: #35506a;
}

body.dark-mode .dossier-actions-panel,
body.theme-dark .dossier-actions-panel {
    background: transparent;
    border-color: transparent;
}

body.dark-mode .section-actions,
body.theme-dark .section-actions {
    border-top-color: #2d445d;
}

body.dark-mode .summary-chip,
body.theme-dark .summary-chip {
    background: #14273e;
    border-color: #305173;
    color: #cde2ff;
}

body.dark-mode .dossier-chip,
body.theme-dark .dossier-chip {
    background: #14273e;
    border-color: #305173;
    color: #cde2ff;
}

body.dark-mode .dossier-chip.archive,
body.theme-dark .dossier-chip.archive {
    background: #223140;
    border-color: #46586a;
    color: #d2dbe5;
}

body.dark-mode .dossier-tab-link,
body.theme-dark .dossier-tab-link {
    background: #13263f;
    border-color: #36516b;
    color: #d3e3f7;
}

body.dark-mode .dossier-tab-link.active,
body.theme-dark .dossier-tab-link.active {
    background: #183556;
    border-color: #4f74a3;
    color: #dbeafe;
}

body.dark-mode .dossier-btn-soft,
body.theme-dark .dossier-btn-soft {
    border-color: #365b7d;
    background: linear-gradient(150deg, #183552 0%, #14304b 100%);
    color: #d2e6fb;
}

body.dark-mode .dossier-btn-soft:hover,
body.dark-mode .dossier-btn-soft:focus,
body.theme-dark .dossier-btn-soft:hover,
body.theme-dark .dossier-btn-soft:focus {
    border-color: #4c7094;
    background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
    color: #ffffff;
}

body.dark-mode .summary-nav-link,
body.theme-dark .summary-nav-link {
    border-color: #35506a;
    background: #13263f;
    color: #d3e3f7;
}

body.dark-mode .summary-nav-link:hover,
body.dark-mode .summary-nav-link:focus,
body.theme-dark .summary-nav-link:hover,
body.theme-dark .summary-nav-link:focus {
    background: #183556;
    border-color: #4f74a3;
}

body.dark-mode .summary-nav-link i:first-child,
body.theme-dark .summary-nav-link i:first-child,
body.dark-mode .dossier-btn-icon,
body.theme-dark .dossier-btn-icon,
body.dark-mode .section-card-icon,
body.theme-dark .section-card-icon {
    background: rgba(119, 183, 255, 0.16);
    color: #9fd0ff;
}

body.dark-mode .section-card,
body.dark-mode .timeline-content,
body.dark-mode .responsive-table,
body.theme-dark .section-card,
body.theme-dark .timeline-content,
body.theme-dark .responsive-table {
    background: #0f1a28;
    border-color: #2f4358;
}

body.dark-mode .dossier-table thead,
body.theme-dark .dossier-table thead {
    background: #16273d;
}

body.dark-mode .dossier-table th,
body.theme-dark .dossier-table th {
    color: #aac4de;
}

body.dark-mode .dossier-table td,
body.theme-dark .dossier-table td {
    color: #e6edf6;
    border-color: #24384d;
}

body.dark-mode .empty-state,
body.theme-dark .empty-state {
    background: linear-gradient(180deg, #132236 0%, #102031 100%);
    border-color: #33506d;
}

body.dark-mode .dossier-title,
body.dark-mode .summary-name,
body.dark-mode .dossier-main-title,
body.dark-mode .section-card h3,
body.theme-dark .dossier-title,
body.theme-dark .summary-name,
body.theme-dark .dossier-main-title,
body.theme-dark .section-card h3 {
    color: #eef5ff;
}

@media (max-width: 1199.98px) {
    .dossier-hero-head {
        grid-template-columns: 1fr;
    }

    .dossier-hero-tools,
    .dossier-header-actions {
        justify-content: flex-start;
    }

    .dossier-layout {
        grid-template-columns: 1fr;
    }

    .section-grid-two {
        grid-template-columns: 1fr;
    }

    .patient-summary-card {
        position: static;
        height: auto;
    }
}

@media (max-width: 991.98px) {
    .dossier-layout {
        grid-template-columns: 1fr;
    }

    .patient-summary-card {
        position: static;
    }

}

@media (max-width: 767.98px) {
    .dossier-page {
        padding: 6px 0 88px;
    }

    .dossier-hero {
        padding: 14px;
        border-radius: 18px;
    }

    .section-grid-two {
        grid-template-columns: 1fr;
    }

    .summary-meta {
        grid-template-columns: 1fr;
    }

    .section-actions {
        justify-content: stretch;
    }

    .section-actions .dossier-btn {
        width: 100%;
        min-width: 0;
    }

    .dossier-header-actions {
        display: none;
    }

    .dossier-mobile-actionbar {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        position: fixed;
        left: 8px;
        right: 8px;
        bottom: calc(10px + env(safe-area-inset-bottom));
        z-index: 1050;
        background: var(--dossier-surface-strong);
        border: 1px solid var(--dossier-border);
        border-radius: 18px;
        padding: 8px;
        box-shadow: 0 16px 28px -20px rgba(0, 0, 0, .46);
    }

    .dossier-mobile-actionbar .dossier-btn {
        min-height: 44px;
    }
}

@media (max-width: 575.98px) {
    .dossier-title-row {
        align-items: flex-start;
    }

    .dossier-title-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
    }

    .tabs-scroll-wrap,
    .dossier-tab-content,
    .dossier-main-head {
        padding-left: 14px;
        padding-right: 14px;
    }

    .dossier-main-head {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush

@section('content')
@php
    $patient = $dossier->patient;
    $fullName = trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? '')) ?: 'Patient';
    $isArchived = ($dossier->statut ?? null) === 'archive';
@endphp
<div class="container-fluid dossier-page">
    <div class="dossier-shell">
        <header class="dossier-hero">
            <div class="dossier-hero-head">
                <div class="dossier-hero-main">
                    <ol class="dossier-breadcrumbs" aria-label="Fil d'Ariane du dossier">
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="dossier-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li><a href="{{ route('dossiers.index') }}">Dossiers</a></li>
                        <li class="dossier-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li aria-current="page">{{ $dossier->numero_dossier }}</li>
                    </ol>

                    <div class="dossier-title-row">
                        <span class="dossier-title-icon" aria-hidden="true"><i class="fas fa-file-medical"></i></span>
                        <div class="dossier-title-block">
                            <h1 class="dossier-title">Dossier medical de {{ $fullName }}</h1>
                            <p class="dossier-title-subtitle">Vue clinique simplifiee du dossier {{ $dossier->numero_dossier }}, avec lecture directe des informations essentielles du patient.</p>
                        </div>
                    </div>

                    <div class="dossier-chip-row">
                        <span class="dossier-chip"><i class="fas fa-fingerprint"></i>{{ $dossier->numero_dossier }}</span>
                        <span class="dossier-chip {{ $isArchived ? 'archive' : 'active' }}"><i class="fas {{ $isArchived ? 'fa-box-archive' : 'fa-circle-check' }}"></i>{{ ucfirst($dossier->statut ?? 'actif') }}</span>
                        <span class="dossier-chip"><i class="fas fa-calendar-day"></i>{{ $dossier->date_ouverture?->format('d/m/Y') ?: 'Date non renseignee' }}</span>
                    </div>                </div>

                <div class="dossier-hero-tools">                    <section class="dossier-actions-panel" aria-label="Actions principales du dossier">
                        <span class="dossier-panel-label">Actions</span>
                        <div class="dossier-header-actions">
                            <div class="dossier-action-group">
                                <a href="{{ route('dossiers.index') }}" class="dossier-btn dossier-btn-soft">
                                    <span class="dossier-btn-icon"><i class="fas fa-arrow-left"></i></span>
                                    <span>Retour à la liste</span>
                                </a>
                                <a href="{{ route('dossiers.edit', $dossier) }}" class="dossier-btn dossier-btn-primary">
                                    <span class="dossier-btn-icon"><i class="fas fa-pen"></i></span>
                                    <span>Modifier</span>
                                </a>
                            </div>
                            @if(! $isArchived)
                                <form action="{{ route('dossiers.archive', $dossier) }}" method="POST" onsubmit="return confirm('Archiver ce dossier ?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="dossier-btn dossier-btn-danger">
                                        <span class="dossier-btn-icon"><i class="fas fa-box-archive"></i></span>
                                        <span>Archiver</span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('dossiers.archives') }}" class="dossier-btn dossier-btn-soft">
                                    <span class="dossier-btn-icon"><i class="fas fa-box-archive"></i></span>
                                    <span>Voir les archives</span>
                                </a>
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </header>

        <div class="dossier-layout">
            <aside>
                @include('dossiers.partials.patient_summary', ['dossier' => $dossier])
            </aside>

            <section>
                @include('dossiers.partials.tabs', ['dossier' => $dossier])
            </section>
        </div>

        <div class="dossier-mobile-actionbar" aria-label="Actions dossier">
            <a href="{{ route('dossiers.index') }}" class="dossier-btn dossier-btn-soft dossier-btn-block">
                <span class="dossier-btn-icon"><i class="fas fa-arrow-left"></i></span>
                <span>Retour à la liste</span>
            </a>
            <a href="{{ route('dossiers.edit', $dossier) }}" class="dossier-btn dossier-btn-primary dossier-btn-block">
                <span class="dossier-btn-icon"><i class="fas fa-pen"></i></span>
                <span>Modifier</span>
            </a>
            @if(! $isArchived)
                <form action="{{ route('dossiers.archive', $dossier) }}" method="POST" onsubmit="return confirm('Archiver ce dossier ?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="dossier-btn dossier-btn-danger dossier-btn-block">
                        <span class="dossier-btn-icon"><i class="fas fa-box-archive"></i></span>
                        <span>Archiver</span>
                    </button>
                </form>
            @else
                <a href="{{ route('dossiers.archives') }}" class="dossier-btn dossier-btn-soft dossier-btn-block">
                    <span class="dossier-btn-icon"><i class="fas fa-box-archive"></i></span>
                    <span>Voir les archives</span>
                </a>
            @endif
    </div>
    </div>
</div>

@endsection



