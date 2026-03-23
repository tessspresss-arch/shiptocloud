@extends('layouts.app')

@section('title', __('messages.settings.title'))

@section('content')
<style>
    .settings-shell {
        --sp-bg: #f8fafc;
        --sp-card: #ffffff;
        --sp-border: #e2e8f0;
        --sp-title: #0f172a;
        --sp-muted: #64748b;
        --sp-accent: #1d6fdc;
        width: 100%;
        margin: 0 auto;
        padding: 18px clamp(12px, 2vw, 28px) 26px;
        background: var(--sp-bg);
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        min-height: auto;
        box-shadow: 0 24px 46px -42px rgba(15, 23, 42, 0.22);
    }

    .settings-hero {
        border: 1px solid var(--sp-border);
        background: #ffffff;
        border-radius: 22px;
        padding: clamp(18px, 2vw, 24px);
        margin-bottom: clamp(16px, 1.9vw, 24px);
        display: block;
        box-shadow: 0 18px 30px -34px rgba(15, 23, 42, 0.18);
    }

    .settings-hero-main {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .settings-hero-actions {
        grid-area: actions;
        display: grid;
        align-content: start;
        gap: 14px;
        min-width: 0;
    }

    .settings-hero-panel {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #f8fafc;
        padding: 15px;
        box-shadow: none;
    }

    .settings-hero-panel h2 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .settings-hero-panel p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .settings-hero-panel-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 14px;
    }

    .settings-hero-meta {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        padding: 11px 12px;
    }

    .settings-hero-meta strong {
        display: block;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .settings-hero-meta span {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
    }

    .hero-btn {
        border: 1px solid #d5deea;
        border-radius: 12px;
        padding: 10px 14px;
        color: #334155;
        background: #ffffff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 44px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .hero-btn:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .settings-hero-copy {
        display: grid;
        gap: 10px;
        padding: 4px 2px 0;
    }

    .settings-hero-intro {
        display: grid;
        gap: 10px;
    }

    .settings-hero-glance {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .settings-hero-glance-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 11px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        box-shadow: none;
    }

    .settings-kicker {
        width: fit-content;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 11px;
        border-radius: 999px;
        border: 1px solid #dbe5f0;
        background: #f8fafc;
        color: #475569;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .settings-hero h1 {
        margin: 0;
        color: var(--sp-title);
        font-weight: 800;
        font-size: clamp(1.85rem, 2.5vw, 2.35rem);
        line-height: 1.04;
        overflow-wrap: anywhere;
    }

    .settings-hero p {
        margin: 0;
        color: var(--sp-muted);
        font-size: 14px;
        line-height: 1.6;
        max-width: 62ch;
        overflow-wrap: anywhere;
    }

    .settings-overview {
        grid-area: overview;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .settings-hero-btns {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .settings-stat {
        position: relative;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 16px 16px 14px;
        background: #ffffff;
        min-height: 132px;
        box-shadow: 0 14px 26px -34px rgba(15, 23, 42, 0.18);
        display: grid;
        gap: 8px;
    }

    .settings-stat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .settings-stat-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .settings-stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 1px solid #dbe5f0;
        color: #1d4ed8;
        background: #eff6ff;
    }

    .settings-stat-value {
        color: #0f172a;
        font-size: clamp(1.35rem, 2vw, 2rem);
        font-weight: 800;
        line-height: 1;
        margin: 0;
        letter-spacing: -0.03em;
    }

    .settings-stat-hint {
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
        margin: 0;
    }

    .settings-stat-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        width: fit-content;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.02em;
        white-space: normal;
        text-align: center;
    }

    .settings-stat-meta {
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
        margin: 0;
    }

    .settings-stat[data-tone="slate"] .settings-stat-icon,
    .settings-stat[data-tone="blue"] .settings-stat-icon,
    .settings-stat[data-tone="emerald"] .settings-stat-icon,
    .settings-stat[data-tone="amber"] .settings-stat-icon,
    .settings-stat[data-tone="violet"] .settings-stat-icon {
        color: #1d4ed8;
        background: #eff6ff;
        border-color: #dbe5f0;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: 290px minmax(0, 1fr);
        gap: clamp(14px, 1.5vw, 22px);
    }

    .settings-grid > section {
        min-width: 0;
    }

    .settings-content-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .settings-rail {
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        background: #ffffff;
        padding: 16px 18px;
        box-shadow: 0 14px 26px -34px rgba(15, 23, 42, 0.16);
    }

    .settings-rail-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .settings-rail-title {
        margin: 0;
        color: #143961;
        font-size: 1.02rem;
        font-weight: 800;
    }

    .settings-rail-copy p {
        margin: 6px 0 0;
        color: #6a809b;
        font-size: 13px;
        line-height: 1.6;
        max-width: 70ch;
    }

    .settings-rail-chipset {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .settings-rail-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 11px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
    }

    .settings-nav-group {
        margin-bottom: 16px;
    }

    .settings-nav-group:last-child {
        margin-bottom: 0;
    }

    .settings-nav-group-title {
        display: block;
        margin: 0 0 8px;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        padding: 0 4px;
    }

    .settings-nav {
        border: 1px solid var(--sp-border);
        border-radius: 20px;
        background: #fff;
        padding: 12px;
        height: fit-content;
        position: sticky;
        top: 12px;
        box-shadow: 0 18px 34px -36px rgba(18, 57, 97, 0.26);
    }

    .settings-hero-panel-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .settings-hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        border-radius: 999px;
        border: 1px solid #d8e6f7;
        background: #f8fbff;
        color: #25507e;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .settings-hero-health {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 14px;
    }

    .settings-hero-health-card {
        border: 1px solid #dbe7f5;
        border-radius: 16px;
        background: #ffffff;
        padding: 14px;
        box-shadow: 0 14px 28px -34px rgba(18, 57, 97, 0.2);
    }

    .settings-hero-health-card strong {
        display: block;
        color: #0f2f56;
        font-size: 1.3rem;
        font-weight: 800;
        line-height: 1;
    }

    .settings-hero-health-card span {
        display: block;
        margin-top: 6px;
        color: #6d819a;
        font-size: 12px;
        line-height: 1.45;
    }

    .settings-hero-note {
        margin-top: 14px;
        padding: 12px 13px;
        border-radius: 14px;
        border: 1px dashed #d7e4f4;
        background: #fbfdff;
        color: #5e7895;
        font-size: 12px;
        line-height: 1.55;
    }

    .settings-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid transparent;
        border-radius: 14px;
        padding: 12px 13px;
        margin-bottom: 8px;
        color: #334155;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all .2s ease;
        min-height: 44px;
    }

    .settings-nav a:last-child {
        margin-bottom: 0;
    }

    .settings-nav a.active,
    .settings-nav a:hover {
        border-color: #c9ddfb;
        background: linear-gradient(135deg, #f5f9ff 0%, #ebf4ff 100%);
        color: #1e40af;
        transform: translateX(2px);
        box-shadow: 0 12px 20px -24px rgba(30, 64, 175, 0.34);
    }

    .settings-nav-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
        color: #1d4ed8;
        border: 1px solid #c7dbfb;
        flex: 0 0 auto;
    }

    .settings-nav-copy {
        min-width: 0;
    }

    .settings-nav-copy strong {
        display: block;
        font-size: 13px;
        font-weight: 800;
        color: inherit;
        overflow-wrap: anywhere;
    }

    .settings-nav-copy span {
        display: block;
        font-size: 11px;
        color: #6b7f98;
        margin-top: 2px;
        overflow-wrap: anywhere;
    }

    .settings-card {
        border: 1px solid var(--sp-border);
        border-radius: 20px;
        background: var(--sp-card);
        overflow: hidden;
        box-shadow: 0 22px 38px -36px rgba(17, 54, 102, 0.28);
        min-height: 540px;
    }

    .settings-card.hidden {
        display: none;
    }

    .settings-head {
        border-bottom: 1px solid var(--sp-border);
        padding: clamp(16px, 1.7vw, 22px) clamp(18px, 1.9vw, 24px);
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .settings-head h2 {
        margin: 0;
        color: #12335e;
        font-size: 1.22rem;
        font-weight: 800;
    }

    .settings-body {
        padding: clamp(18px, 1.9vw, 24px);
        min-height: 460px;
    }

    .settings-group {
        margin-bottom: clamp(18px, 1.8vw, 24px);
        padding-bottom: clamp(18px, 1.8vw, 24px);
        border-bottom: 1px dashed #dbe7f6;
    }

    .settings-group:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: 0;
    }

    .group-title {
        margin-bottom: 14px;
        color: #1e3a5f;
        font-size: .92rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: clamp(10px, 1.1vw, 14px);
    }

    .grid-1 {
        display: grid;
        grid-template-columns: 1fr;
        gap: clamp(10px, 1.1vw, 14px);
    }

    .field label {
        display: block;
        margin-bottom: 5px;
        color: #334155;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .35px;
    }

    .field input,
    .field select,
    .field textarea {
        width: 100%;
        border: 1px solid #cfdcf0;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 14px;
        background: #fcfdff;
        min-height: 48px;
    }

    .field textarea {
        min-height: 120px;
        resize: vertical;
    }

    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        outline: none;
        border-color: #7aa9ef;
        box-shadow: 0 0 0 3px rgba(29, 111, 220, .12);
    }

    .actions {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sticky-actions {
        position: sticky;
        bottom: 10px;
        padding-top: 16px;
        background: linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.95) 26%, #fff 100%);
        z-index: 2;
    }

    .btn-save {
        border: 0;
        border-radius: 12px;
        padding: 12px 18px;
        color: #fff;
        font-weight: 700;
        background: linear-gradient(135deg, #1d6fdc 0%, #3d84e8 100%);
        box-shadow: 0 16px 24px -22px rgba(29, 111, 220, 0.66);
        min-height: 44px;
    }

    .btn-save:hover {
        filter: brightness(1.05);
    }

    .btn-soft {
        border: 1px solid #cfdcf0;
        border-radius: 12px;
        padding: 11px 15px;
        color: #1e3a8a;
        font-weight: 700;
        background: #ffffff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        text-align: center;
    }

    .btn-soft:hover {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .section-note {
        margin: 0 0 18px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.65;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: clamp(10px, 1.1vw, 14px);
    }

    .toggle-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .toggle-card {
        border: 1px solid #dbe7f6;
        border-radius: 16px;
        background: #fbfdff;
        padding: 14px 16px;
        box-shadow: 0 14px 30px -34px rgba(18, 57, 97, 0.18);
    }

    .toggle-card label {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin: 0;
        color: #1f3555;
        font-size: 14px;
        font-weight: 700;
        text-transform: none;
        letter-spacing: 0;
    }

    .toggle-card small {
        display: block;
        color: #64748b;
        font-size: 12px;
        margin-top: 6px;
        line-height: 1.45;
    }

    .field-inline-note {
        margin-top: 6px;
        font-size: 12px;
        color: #64748b;
    }

    .logo-uploader {
        display: grid;
        grid-template-columns: 140px minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .logo-preview {
        width: 140px;
        height: 140px;
        border-radius: 24px;
        border: 1px dashed #c9dcf7;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #1d4ed8;
        font-weight: 800;
    }

    .logo-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: #fff;
    }

    .backup-list {
        border: 1px solid #dbe7f6;
        border-radius: 16px;
        overflow: hidden;
    }

    .backup-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
        padding: 14px 16px;
        border-bottom: 1px solid #edf2f8;
        background: #fff;
    }

    .backup-item:last-child {
        border-bottom: 0;
    }

    .backup-meta strong {
        display: block;
        color: #0f2746;
        font-size: 13px;
    }

    .backup-meta span {
        display: block;
        color: #64748b;
        font-size: 12px;
        margin-top: 2px;
    }

    .alert {
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 14px;
        font-weight: 600;
        font-size: 14px;
    }

    .alert-success {
        border: 1px solid #86efac;
        background: #f0fdf4;
        color: #166534;
    }

    .alert-error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .permissions-info {
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1e3a8a;
        border-radius: 14px;
        padding: 13px 14px;
        margin-bottom: 18px;
        font-size: 13px;
    }

    .permissions-wrap {
        border: 1px solid #dbe5f2;
        border-radius: 16px;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
    }

    .permissions-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1200px;
    }

    .permissions-table th,
    .permissions-table td {
        border-bottom: 1px solid #edf2f8;
        padding: 10px 12px;
        text-align: center;
        font-size: 13px;
    }

    .permissions-table th {
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
    }

    .permissions-table td:first-child,
    .permissions-table th:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        z-index: 1;
        background: #fff;
    }

    .permissions-name {
        font-weight: 700;
        color: #0f172a;
    }

    .permissions-email {
        display: block;
        color: #64748b;
        font-size: 12px;
    }

    .template-section-head {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .template-section-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        max-width: 72ch;
    }

    .template-chipset {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
    }

    .template-chip {
        border: 1px solid #dbe7f5;
        background: #f8fbff;
        color: #1d4f91;
        border-radius: 999px;
        padding: 9px 12px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .template-dashboard {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .template-stat-card {
        border: 1px solid #dbe7f5;
        border-radius: 18px;
        padding: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 18px 30px -34px rgba(18, 57, 97, 0.22);
    }

    .template-stat-card span {
        display: block;
        color: #6f84a2;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 8px;
    }

    .template-stat-card strong {
        display: block;
        color: #11345c;
        font-size: 1.85rem;
        line-height: 1;
        margin-bottom: 8px;
        font-weight: 800;
    }

    .template-stat-card small {
        color: #71849d;
        font-size: 13px;
    }

    .template-two-column {
        display: grid;
        grid-template-columns: minmax(360px, 0.92fr) minmax(0, 1.28fr);
        gap: 18px;
        align-items: start;
    }

    .template-panel {
        border: 1px solid #dbe7f5;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 20px 34px -36px rgba(18, 57, 97, 0.2);
        overflow: hidden;
    }

    .template-panel-head {
        padding: 18px 20px 14px;
        border-bottom: 1px solid #e4edf7;
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
    }

    .template-panel-head h3 {
        margin: 0;
        color: #123a67;
        font-size: 1.1rem;
        font-weight: 800;
    }

    .template-panel-head p {
        margin: 6px 0 0;
        color: #70839b;
        font-size: 13px;
        line-height: 1.55;
    }

    .template-panel-body {
        padding: 18px 20px 20px;
        display: grid;
        gap: 16px;
    }

    .template-flag-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .template-flag {
        border: 1px solid #dbe7f5;
        border-radius: 16px;
        background: #ffffff;
        padding: 13px 14px;
    }

    .template-flag label {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #143961;
        font-size: 13px;
        font-weight: 700;
    }

    .template-flag small {
        display: block;
        margin-top: 8px;
        color: #70839b;
        font-size: 12px;
        line-height: 1.5;
    }

    .template-example-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .template-example {
        padding: 7px 12px;
        border-radius: 999px;
        background: #f1f7ff;
        color: #1d4f91;
        border: 1px solid #dbe7f5;
        font-size: 12px;
        font-weight: 700;
    }

    .template-meds-box {
        border: 1px dashed #cfe0f5;
        border-radius: 18px;
        background: #fbfdff;
        padding: 14px;
        display: grid;
        gap: 12px;
    }

    .template-med-row {
        border: 1px solid #dbe7f5;
        border-radius: 16px;
        background: #ffffff;
        padding: 14px;
        display: grid;
        gap: 12px;
    }

    .template-med-row-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .template-med-row-title {
        color: #143961;
        font-size: 13px;
        font-weight: 800;
    }

    .template-med-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .template-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: space-between;
        align-items: center;
    }

    .template-inline-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .template-list {
        display: grid;
        gap: 14px;
    }

    .template-card {
        border: 1px solid #dbe7f5;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 18px 34px -36px rgba(18, 57, 97, 0.2);
        overflow: hidden;
    }

    .template-card summary {
        list-style: none;
        cursor: pointer;
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
    }

    .template-card summary::-webkit-details-marker {
        display: none;
    }

    .template-card-title {
        display: grid;
        gap: 8px;
    }

    .template-card-title strong {
        color: #103862;
        font-size: 1rem;
        font-weight: 800;
    }

    .template-card-subtitle {
        color: #6d8099;
        font-size: 13px;
    }

    .template-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .template-badge {
        padding: 7px 10px;
        border-radius: 999px;
        border: 1px solid #dbe7f5;
        background: #f8fbff;
        color: #1d4f91;
        font-size: 12px;
        font-weight: 800;
    }

    .template-badge.success {
        color: #047857;
        background: #ecfdf5;
        border-color: #b7efce;
    }

    .template-badge.muted {
        color: #64748b;
        background: #f8fafc;
        border-color: #dbe3ee;
    }

    .template-card-body {
        padding: 0 18px 18px;
        border-top: 1px solid #e8eef6;
        display: grid;
        gap: 16px;
    }

    .template-note {
        padding: 12px 14px;
        border-radius: 14px;
        background: #f8fbff;
        border: 1px solid #dce8f8;
        color: #59738e;
        font-size: 13px;
        line-height: 1.6;
    }

    .template-med-preview-list {
        display: grid;
        gap: 8px;
    }

    .template-med-preview-item {
        border: 1px dashed #d5e3f3;
        border-radius: 14px;
        background: #fbfdff;
        padding: 10px 12px;
        color: #23456e;
        font-size: 13px;
        line-height: 1.55;
    }

    .template-soft-btn {
        border: 1px solid #d4e1f2;
        background: #ffffff;
        color: #1d4f91;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .template-soft-btn:hover {
        background: #eff6ff;
        color: #1d3f74;
    }

    body.dark-mode .settings-shell {
        --sp-bg: linear-gradient(180deg, #0f1b2f 0%, #0b1424 100%);
        --sp-card: #0f1b2f;
        --sp-border: #263a57;
        --sp-title: #e2e8f0;
        --sp-muted: #94a3b8;
        --sp-accent: #60a5fa;
        border-color: #20314a;
        box-shadow: 0 16px 34px rgba(2, 6, 23, 0.35);
    }

    body.dark-mode .settings-hero {
        border-color: #2a3f5e;
        background: radial-gradient(100% 100% at 0% 0%, #1a3150 0%, #10213a 100%);
    }

    body.dark-mode .settings-kicker,
    body.dark-mode .settings-stat,
    body.dark-mode .settings-rail,
    body.dark-mode .hero-btn {
        border-color: #2a3f5e;
        background: #10213a;
        color: #dbeafe;
    }

    body.dark-mode .hero-btn {
        color: #bfdbfe;
    }

    body.dark-mode .hero-btn:hover {
        background: #1b3d67;
        color: #dbeafe;
    }

    body.dark-mode .settings-nav {
        background: #0d192c;
        border-color: #2a3f5e;
    }

    body.dark-mode .settings-nav a {
        color: #cbd5e1;
    }

    body.dark-mode .settings-nav a.active,
    body.dark-mode .settings-nav a:hover {
        border-color: #3c5f8f;
        background: #122b4a;
        color: #bfdbfe;
    }

    body.dark-mode .settings-nav-icon {
        border-color: #365b88;
        background: #143154;
        color: #bfdbfe;
    }

    body.dark-mode .settings-nav-copy span {
        color: #8ea4bf;
    }

    body.dark-mode .settings-card {
        background: #0f1b2f;
        border-color: #2a3f5e;
        box-shadow: 0 14px 26px rgba(2, 6, 23, 0.36);
    }

    body.dark-mode .settings-stat-label,
    body.dark-mode .settings-stat-hint {
        color: #9cb3cc;
    }

    body.dark-mode .settings-stat-meta {
        color: #b3c2d4;
    }

    body.dark-mode .settings-stat-value {
        color: #f5f9ff;
    }

    body.dark-mode .settings-stat-status,
    body.dark-mode .settings-hero-pill,
    body.dark-mode .settings-hero-glance-item,
    body.dark-mode .settings-hero-note {
        border-color: #2f4767;
        background: #132640;
        color: #c9def7;
    }

    body.dark-mode .settings-hero-panel,
    body.dark-mode .settings-hero-meta,
    body.dark-mode .settings-hero-health-card {
        border-color: #2a3f5e;
        background: #10213a;
    }

    body.dark-mode .settings-rail-title {
        color: #e6effa;
    }

    body.dark-mode .settings-rail-copy p,
    body.dark-mode .settings-rail-chip {
        color: #a8bfd8;
    }

    body.dark-mode .settings-hero-health-card strong {
        color: #f5f9ff;
    }

    body.dark-mode .settings-hero-health-card span {
        color: #9cb3cc;
    }

    body.dark-mode .settings-head {
        border-color: #2a3f5e;
        background: linear-gradient(180deg, #10233f 0%, #0d1b31 100%);
    }

    body.dark-mode .settings-head h2,
    body.dark-mode .group-title,
    body.dark-mode .field label,
    body.dark-mode .permissions-name {
        color: #dbe7f5;
    }

    body.dark-mode .settings-group {
        border-bottom-color: #2b3f5d;
    }

    body.dark-mode .field input,
    body.dark-mode .field select,
    body.dark-mode .field textarea {
        background: #0a1729;
        border-color: #2b4363;
        color: #e2e8f0;
    }

    body.dark-mode .field input::placeholder,
    body.dark-mode .field textarea::placeholder {
        color: #8aa1bd;
    }

    body.dark-mode .field input:focus,
    body.dark-mode .field select:focus,
    body.dark-mode .field textarea:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, .2);
    }

    body.dark-mode .sticky-actions {
        background: linear-gradient(180deg, rgba(15,27,47,0) 0%, rgba(15,27,47,.92) 26%, #0f1b2f 100%);
    }

    body.dark-mode .btn-soft,
    body.dark-mode .toggle-card,
    body.dark-mode .logo-preview,
    body.dark-mode .backup-list,
    body.dark-mode .backup-item {
        border-color: #2a3f5e;
        background: #10213a;
        color: #dbe7f5;
    }

    body.dark-mode .field-inline-note,
    body.dark-mode .toggle-card small,
    body.dark-mode .backup-meta span,
    body.dark-mode .section-note {
        color: #94a3b8;
    }

    body.dark-mode .alert-success {
        border-color: #166534;
        background: rgba(22, 101, 52, 0.18);
        color: #bbf7d0;
    }

    body.dark-mode .alert-error {
        border-color: #7f1d1d;
        background: rgba(127, 29, 29, 0.25);
        color: #fecaca;
    }

    body.dark-mode .permissions-info {
        border-color: #365a8f;
        background: rgba(37, 99, 235, 0.18);
        color: #bfdbfe;
    }

    body.dark-mode .permissions-wrap {
        border-color: #2a3f5e;
    }

    body.dark-mode .permissions-table th,
    body.dark-mode .permissions-table td {
        border-bottom-color: #2b3f5d;
    }

    body.dark-mode .permissions-table th {
        background: #10223c;
        color: #dbe7f5;
    }

    body.dark-mode .permissions-table td {
        color: #cbd5e1;
    }

    body.dark-mode .permissions-table td:first-child,
    body.dark-mode .permissions-table th:first-child {
        background: #0f1b2f;
    }

    body.dark-mode .permissions-email {
        color: #94a3b8;
    }

    @media (max-width: 992px) {
        .settings-hero {
            display: block;
        }

        .settings-overview {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .settings-grid {
            grid-template-columns: 1fr;
        }

        .template-dashboard,
        .template-two-column {
            grid-template-columns: 1fr;
        }

        .template-section-head,
        .template-panel-head,
        .template-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .settings-nav {
            position: static;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 6px;
            overflow: visible;
            white-space: normal;
            padding: 6px;
        }

        .settings-rail {
            padding: 14px;
        }

        .permissions-wrap {
            margin-inline: -4px;
            border-radius: 14px;
            -webkit-overflow-scrolling: touch;
        }

        .permissions-table {
            min-width: 860px;
        }

        .permissions-table th,
        .permissions-table td {
            padding: 9px 10px;
            font-size: 12px;
        }

        .settings-nav a {
            margin-bottom: 0;
            min-width: 0;
        }

        .settings-card,
        .settings-body {
            min-height: auto;
        }
    }

    @media (max-width: 720px) {
        .settings-shell {
            padding: 10px;
            border-radius: 14px;
        }

        .settings-hero h1 {
            font-size: 1.55rem;
        }

        .settings-overview {
            grid-template-columns: 1fr;
        }

        .settings-nav {
            grid-template-columns: 1fr;
        }

        .settings-hero-health,
        .settings-hero-btns,
        .settings-hero-panel-meta {
            grid-template-columns: 1fr;
        }

        .settings-hero-panel-top {
            flex-direction: column;
            align-items: flex-start;
        }

        .permissions-wrap {
            margin-inline: -2px;
        }

        .permissions-table {
            min-width: 640px;
        }

        .permissions-table th,
        .permissions-table td {
            padding: 8px 9px;
            font-size: 11px;
        }

        .permissions-email {
            font-size: 11px;
        }

        .settings-hero-panel-meta,
        .settings-hero-btns {
            grid-template-columns: 1fr;
        }

        .grid-2,
        .grid-3,
        .toggle-grid,
        .logo-uploader,
        .template-flag-grid,
        .template-med-grid {
            grid-template-columns: 1fr;
        }

        .template-chipset,
        .template-inline-actions {
            justify-content: flex-start;
        }

        .template-card summary {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (min-width: 1700px) {
        .settings-shell {
            width: 100%;
        }
    }
</style>

<div class="settings-shell">
    <div class="settings-hero">
        <div class="settings-hero-main">
            <div class="settings-hero-copy">
                <span class="settings-kicker"><i class="fas fa-gears"></i> Paramètres</span>
                <div class="settings-hero-intro">
                    <h1>Paramètres du cabinet</h1>
                    <p>Centralisez la configuration du système, la sécurité, les sauvegardes, la communication et les ordonnances dans une interface plus claire.</p>
                    <div class="settings-hero-glance">
                        <span class="settings-hero-glance-item"><i class="fas fa-layer-group"></i> Domaines regroupés</span>
                        <span class="settings-hero-glance-item"><i class="fas fa-shield-alt"></i> Gouvernance centralisée</span>
                    </div>
                </div>
            </div>
            <div class="settings-overview">
                @foreach($settingsOverview as $item)
                    <article class="settings-stat" data-tone="{{ $item['tone'] }}">
                        <div class="settings-stat-head">
                            <span class="settings-stat-label">{{ $item['label'] }}</span>
                            <span class="settings-stat-icon"><i class="fas {{ $item['icon'] }}"></i></span>
                        </div>
                        <div class="settings-stat-value">{{ $item['value'] }}</div>
                        <span class="settings-stat-status">{{ $item['status'] }}</span>
                        <p class="settings-stat-hint">{{ $item['hint'] }}</p>
                        <p class="settings-stat-meta">{{ $item['meta'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="settings-grid">
        <aside class="settings-nav">
            @foreach(collect($navSections)->groupBy('domain') as $domain => $items)
                <div class="settings-nav-group">
                    <span class="settings-nav-group-title">{{ $domain }}</span>
                    @foreach($items as $index => $item)
                        <a href="#{{ $item['id'] }}" class="{{ $domain === collect($navSections)->first()['domain'] && $loop->first ? 'active' : '' }}" onclick="showSection(event, '{{ $item['id'] }}')">
                            <span class="settings-nav-icon"><i class="fas {{ $item['icon'] }}"></i></span>
                            <span class="settings-nav-copy">
                                <strong>{{ $item['label'] }}</strong>
                                <span>{{ $item['desc'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endforeach
            <div class="settings-nav-group">
                <span class="settings-nav-group-title">Audit</span>
                <a href="{{ route('admin.settings.audit') }}">
                    <span class="settings-nav-icon"><i class="fas fa-clipboard-list"></i></span>
                    <span class="settings-nav-copy">
                        <strong>Journal d'audit</strong>
                        <span>Traçabilité et événements d'administration</span>
                    </span>
                </a>
            </div>
        </aside>

        <section class="settings-content-stack">
            <div id="general" class="settings-card">
                <div class="settings-head"><h2>Configuration système</h2></div>
                <div class="settings-body">
                    <form method="POST" action="{{ route('parametres.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="general">
                        <p class="section-note">Centralisez ici les preferences globales du cabinet, la langue d interface et les donnees generales visibles dans les autres modules.</p>

                        <div class="settings-group">
                            <div class="group-title">Informations de base</div>
                            <div class="grid-2">
                                <div class="field"><label>Nom du cabinet</label><input type="text" name="cabinet_name" value="{{ $allSettings['cabinet_name'] ?? 'Cabinet Medical' }}"></div>
                                <div class="field"><label>Email principal</label><input type="email" name="email_principal" value="{{ $allSettings['email_principal'] ?? '' }}"></div>
                                <div class="field"><label>Telephone</label><input type="tel" name="phone" value="{{ $allSettings['phone'] ?? '' }}"></div>
                                <div class="field">
                                    <label>Fuseau horaire</label>
                                    <select name="timezone">
                                        <option value="Africa/Casablanca" {{ ($allSettings['timezone'] ?? '') === 'Africa/Casablanca' ? 'selected' : '' }}>Africa/Casablanca</option>
                                        <option value="Europe/Paris" {{ ($allSettings['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                        <option value="UTC" {{ ($allSettings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="settings-group">
                            <div class="group-title">Preferences</div>
                            <div class="grid-2">
                                <div class="field">
                                    <label>Devise</label>
                                    <select name="currency">
                                        <option value="EUR" {{ ($allSettings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                        <option value="MAD" {{ ($allSettings['currency'] ?? '') === 'MAD' ? 'selected' : '' }}>Dirham (MAD)</option>
                                        <option value="USD" {{ ($allSettings['currency'] ?? '') === 'USD' ? 'selected' : '' }}>Dollar (USD)</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Langue</label>
                                    <select name="language">
                                        <option value="fr" {{ ($allSettings['language'] ?? 'fr') === 'fr' ? 'selected' : '' }}>Francais</option>
                                        <option value="en" {{ ($allSettings['language'] ?? '') === 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="actions sticky-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button></div>
                    </form>
                </div>
            </div>

            <div id="cabinet" class="settings-card hidden">
                <div class="settings-head"><h2>Cabinet et identité</h2></div>
                <div class="settings-body">
                    <form method="POST" action="{{ route('parametres.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <input type="hidden" name="section" value="cabinet">
                        <p class="section-note">Renseignez l identite visuelle et les informations officielles du cabinet pour les documents, devis, communications et impressions.</p>
                        <div class="settings-group">
                            <div class="group-title">Adresse et identification</div>
                            <div class="logo-uploader" style="margin-bottom: 16px;">
                                <div class="logo-preview" id="cabinetLogoPreview">
                                    @if($cabinetLogoUrl)
                                        <img src="{{ $cabinetLogoUrl }}" alt="Logo du cabinet">
                                    @else
                                        <span>LOGO</span>
                                    @endif
                                </div>
                                <div>
                                    <div class="field">
                                        <label>Logo du cabinet</label>
                                        <input type="file" id="cabinet_logo" name="cabinet_logo" accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <div class="field-inline-note">Ajoutez le logo utilise sur les documents et emails officiels du cabinet.</div>
                                    <label style="display:inline-flex;align-items:center;gap:8px;margin-top:10px;font-size:13px;color:#334155;">
                                        <input type="checkbox" name="remove_cabinet_logo" value="1">
                                        Retirer le logo actuel
                                    </label>
                                </div>
                            </div>
                            <div class="grid-1"><div class="field"><label>Adresse complete</label><input type="text" name="cabinet_address" value="{{ $allSettings['cabinet_address'] ?? '' }}"></div></div>
                            <div class="grid-2" style="margin-top:14px;">
                                <div class="field"><label>Ville</label><input type="text" name="cabinet_city" value="{{ $allSettings['cabinet_city'] ?? '' }}"></div>
                                <div class="field"><label>Code postal</label><input type="text" name="cabinet_zip" value="{{ $allSettings['cabinet_zip'] ?? '' }}"></div>
                                <div class="field"><label>SIRET</label><input type="text" name="siret" value="{{ $allSettings['siret'] ?? '' }}"></div>
                                <div class="field"><label>Numero TVA</label><input type="text" name="tva_number" value="{{ $allSettings['tva_number'] ?? '' }}"></div>
                            </div>
                            <div class="grid-3" style="margin-top:14px;">
                                <div class="field"><label>Telephone</label><input type="tel" name="cabinet_phone" value="{{ $allSettings['cabinet_phone'] ?? ($allSettings['phone'] ?? '') }}"></div>
                                <div class="field"><label>Email</label><input type="email" name="cabinet_email" value="{{ $allSettings['cabinet_email'] ?? ($allSettings['email_principal'] ?? '') }}"></div>
                                <div class="field"><label>Site web</label><input type="url" name="cabinet_website" value="{{ $allSettings['cabinet_website'] ?? '' }}" placeholder="https://"></div>
                            </div>
                        </div>
                        <div class="actions sticky-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button></div>
                    </form>
                </div>
            </div>

            <div id="communication" class="settings-card hidden">
                <div class="settings-head"><h2>Communication</h2></div>
                <div class="settings-body">
                    <form method="POST" action="{{ route('parametres.update') }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="section" value="communication">
                        <p class="section-note">Pilotez ici les canaux de communication patient, les rappels automatiques et les modeles de messages utilises par le cabinet.</p>
                        <div class="settings-group">
                            <div class="group-title">Rappels et automatisation</div>
                            <div class="toggle-grid">
                                <div class="toggle-card">
                                    <label><input type="hidden" name="sms_enabled" value="0"><input type="checkbox" name="sms_enabled" value="1" {{ !empty($allSettings['sms_enabled']) ? 'checked' : '' }}> Activer l envoi SMS</label>
                                    <small>Permet d envoyer des rappels ou notifications aux patients via la passerelle configuree.</small>
                                </div>
                                <div class="toggle-card">
                                    <label><input type="hidden" name="appointment_reminders_enabled" value="0"><input type="checkbox" name="appointment_reminders_enabled" value="1" {{ !empty($allSettings['appointment_reminders_enabled']) ? 'checked' : '' }}> Activer les rappels automatiques</label>
                                    <small>Envoie des rappels planifies avant les rendez-vous a venir.</small>
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="field"><label>Delai rappel avant RDV (heures)</label><input type="number" name="appointment_reminder_delay_hours" value="{{ $allSettings['appointment_reminder_delay_hours'] ?? 24 }}"></div>
                                <div class="field"><label>Canal prefere</label>
                                    <select name="appointment_reminder_channel">
                                        <option value="sms" {{ ($allSettings['appointment_reminder_channel'] ?? '') === 'sms' ? 'selected' : '' }}>SMS</option>
                                        <option value="email" {{ ($allSettings['appointment_reminder_channel'] ?? 'email') === 'email' ? 'selected' : '' }}>Email</option>
                                        <option value="email_sms" {{ ($allSettings['appointment_reminder_channel'] ?? '') === 'email_sms' ? 'selected' : '' }}>Email + SMS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="settings-group">
                            <div class="group-title">Modeles de messages</div>
                            <div class="grid-1">
                                <div class="field"><label>Modele SMS rendez-vous</label><textarea name="sms_template_appointment">{{ $allSettings['sms_template_appointment'] ?? 'Bonjour {patient}, rappel de votre rendez-vous le {date} a {heure} avec {medecin}.' }}</textarea></div>
                                <div class="field"><label>Modele email rendez-vous</label><textarea name="email_template_appointment">{{ $allSettings['email_template_appointment'] ?? 'Bonjour {patient},\n\nNous vous rappelons votre rendez-vous prevu le {date} a {heure} avec {medecin}.\n\nMerci.' }}</textarea></div>
                            </div>
                        </div>
                        <div class="settings-group">
                            <div class="group-title">Coordonnees de communication</div>
                            <div class="grid-2">
                                <div class="field"><label>Nom expediteur</label><input type="text" name="communication_sender_name" value="{{ $allSettings['communication_sender_name'] ?? ($allSettings['cabinet_name'] ?? 'Cabinet Medical') }}"></div>
                                <div class="field"><label>Email de reponse</label><input type="email" name="communication_reply_to" value="{{ $allSettings['communication_reply_to'] ?? ($allSettings['email_principal'] ?? '') }}"></div>
                            </div>
                        </div>
                        <div class="actions sticky-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button></div>
                    </form>
                </div>
            </div>

            <div id="medical" class="settings-card hidden">
                <div class="settings-head"><h2>Flux médical</h2></div>
                <div class="settings-body">
                    <form method="POST" action="{{ route('parametres.update') }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="section" value="medical">
                        <p class="section-note">Structurez ici les regles par defaut de consultation, de rendez-vous, d urgence et le fonctionnement de la salle d attente.</p>
                        <div class="settings-group">
                            <div class="group-title">Organisation clinique</div>
                            <div class="grid-3">
                                <div class="field"><label>Services disponibles</label><input type="text" name="services" value="{{ $allSettings['services'] ?? 'Consultation, Diagnostic, Traitement' }}"></div>
                                <div class="field"><label>Duree consultation par defaut (min)</label><input type="number" name="consultation_duration" value="{{ $allSettings['consultation_duration'] ?? 30 }}"></div>
                                <div class="field"><label>Intervalle entre rendez-vous (min)</label><input type="number" name="rdv_min_gap" value="{{ $allSettings['rdv_min_gap'] ?? 15 }}"></div>
                                <div class="field"><label>Duree slot urgence (min)</label><input type="number" name="emergency_slot_duration" value="{{ $allSettings['emergency_slot_duration'] ?? 15 }}"></div>
                                <div class="field"><label>Capacite salle d attente</label><input type="number" name="waiting_room_capacity" value="{{ $allSettings['waiting_room_capacity'] ?? 12 }}"></div>
                                <div class="field"><label>Delai critique avant retard (min)</label><input type="number" name="waiting_room_alert_threshold" value="{{ $allSettings['waiting_room_alert_threshold'] ?? 20 }}"></div>
                            </div>
                        </div>
                        <div class="settings-group">
                            <div class="group-title">Urgences et salle d attente</div>
                            <div class="toggle-grid">
                                <div class="toggle-card">
                                    <label><input type="hidden" name="emergency_management_enabled" value="0"><input type="checkbox" name="emergency_management_enabled" value="1" {{ !empty($allSettings['emergency_management_enabled']) ? 'checked' : '' }}> Gestion des urgences</label>
                                    <small>Autorise l insertion de patients urgents avec priorite dans le planning.</small>
                                </div>
                                <div class="toggle-card">
                                    <label><input type="hidden" name="waiting_room_enabled" value="0"><input type="checkbox" name="waiting_room_enabled" value="1" {{ !empty($allSettings['waiting_room_enabled']) ? 'checked' : '' }}> Salle d attente active</label>
                                    <small>Active l affichage et le suivi temps reel des patients en attente.</small>
                                </div>
                            </div>
                        </div>
                        <div class="actions sticky-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button></div>
                    </form>
                </div>
            </div>

            <div id="ordonnances" class="settings-card hidden">
                <div class="settings-head"><h2>Ordonnances</h2></div>
                <div class="settings-body">
                    <div class="template-section-head">
                        <div>
                            <h3 style="margin:0;color:#123a67;font-size:1.25rem;font-weight:800;">Bibliotheque des prescriptions reutilisables</h3>
                            <p>Configurez ici des modeles d ordonnance modernes et reutilisables. Chaque modele peut charger automatiquement le contexte clinique, les consignes et les lignes medicaments dans l interface de prescription.</p>
                        </div>
                        <div class="template-chipset">
                            <span class="template-chip"><i class="fas fa-bolt"></i> Chargement instantane</span>
                            <span class="template-chip"><i class="fas fa-notes-medical"></i> Contexte et consignes</span>
                            <span class="template-chip"><i class="fas fa-capsules"></i> Lignes medicaments type</span>
                        </div>
                    </div>

                    <div class="template-dashboard">
                        <div class="template-stat-card">
                            <span>Modeles total</span>
                            <strong>{{ $ordonnanceTemplateStats['total'] ?? 0 }}</strong>
                            <small>Bibliotheque disponible dans le module Ordonnances.</small>
                        </div>
                        <div class="template-stat-card">
                            <span>Actifs</span>
                            <strong>{{ $ordonnanceTemplateStats['active'] ?? 0 }}</strong>
                            <small>Modeles directement utilisables dans les prescriptions.</small>
                        </div>
                        <div class="template-stat-card">
                            <span>Cabinet</span>
                            <strong>{{ $ordonnanceTemplateStats['general'] ?? 0 }}</strong>
                            <small>Modeles partages a l echelle du cabinet.</small>
                        </div>
                        <div class="template-stat-card">
                            <span>Lignes traitement</span>
                            <strong>{{ $ordonnanceTemplateStats['medications'] ?? 0 }}</strong>
                            <small>Medicaments preconfigures memorises.</small>
                        </div>
                    </div>

                    <div class="template-two-column">
                        <div class="template-panel">
                            <div class="template-panel-head">
                                <div>
                                    <h3>Creer un nouveau modele</h3>
                                    <p>Preconfigurez une ordonnance type pour les situations frequentes du cabinet et gagnez du temps a la prescription.</p>
                                </div>
                                <span class="template-badge">Nouveau modele</span>
                            </div>
                            <div class="template-panel-body">
                                <div class="template-example-list">
                                    @foreach($ordonnanceTemplateCategories as $category)
                                        <span class="template-example">{{ $category }}</span>
                                    @endforeach
                                </div>

                                <form method="POST" action="{{ route('parametres.ordonnances.templates.store') }}" class="template-config-form">
                                    @csrf
                                    <div class="grid-2">
                                        <div class="field">
                                            <label>Nom du modele</label>
                                            <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Ex: Hypertension - suivi standard" required>
                                        </div>
                                        <div class="field">
                                            <label>Categorie</label>
                                            <select name="categorie">
                                                <option value="">Selectionner une categorie</option>
                                                @foreach($ordonnanceTemplateCategories as $category)
                                                    <option value="{{ $category }}" {{ old('categorie') === $category ? 'selected' : '' }}>{{ $category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid-2">
                                        <div class="field">
                                            <label>Diagnostic ou contexte</label>
                                            <textarea name="diagnostic_contexte" placeholder="Ex: Douleur moderee sans signe de gravite, traitement symptomatique standard.">{{ old('diagnostic_contexte') }}</textarea>
                                        </div>
                                        <div class="field">
                                            <label>Instructions generales</label>
                                            <textarea name="instructions_generales" placeholder="Ex: Repos, hydratation, surveillance, recontrole si persistance des symptomes.">{{ old('instructions_generales') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="grid-2">
                                        <div class="field">
                                            <label>Medecin rattache</label>
                                            <select name="medecin_id">
                                                <option value="">Aucun medecin specifique</option>
                                                @foreach($medecins as $medecin)
                                                    <option value="{{ $medecin->id }}" {{ (string) old('medecin_id') === (string) $medecin->id ? 'selected' : '' }}>
                                                        {{ trim($medecin->prenom . ' ' . $medecin->nom) }}{{ $medecin->specialite ? ' - ' . $medecin->specialite : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="template-flag-grid">
                                            <div class="template-flag">
                                                <input type="hidden" name="est_template_general" value="0">
                                                <label><input type="checkbox" name="est_template_general" value="1" {{ old('est_template_general', '1') ? 'checked' : '' }}> Modele global du cabinet</label>
                                                <small>Diffuse le modele a tous les medecins autorises dans le module Ordonnances.</small>
                                            </div>
                                            <div class="template-flag">
                                                <input type="hidden" name="is_actif" value="0">
                                                <label><input type="checkbox" name="is_actif" value="1" {{ old('is_actif', '1') ? 'checked' : '' }}> Modele actif</label>
                                                <small>Le modele devient disponible immediatement dans l interface de prescription.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="template-meds-box">
                                        <div class="template-toolbar">
                                            <div>
                                                <div class="group-title" style="margin-bottom:4px;">Medicaments par defaut</div>
                                                <div class="section-note" style="margin:0;">Ajoutez des lignes types avec posologie, duree, quantite et instructions.</div>
                                            </div>
                                            <button type="button" class="template-soft-btn js-add-template-med" data-target="newOrdTemplateRows">
                                                <i class="fas fa-plus"></i> Ajouter medicament
                                            </button>
                                        </div>
                                        <div class="js-template-med-list" id="newOrdTemplateRows">
                                            @foreach($newTemplateRows as $index => $row)
                                                <div class="template-med-row js-template-med-row">
                                                    <div class="template-med-row-head">
                                                        <span class="template-med-row-title">Ligne medicament #{{ $index + 1 }}</span>
                                                        <button type="button" class="template-soft-btn js-remove-template-med"><i class="fas fa-trash"></i> Retirer</button>
                                                    </div>
                                                    <div class="template-med-grid">
                                                        <div class="field">
                                                            <label>Medicament</label>
                                                            <select name="medicaments_template[{{ $index }}][medicament_id]">
                                                                <option value="">Selectionner un medicament</option>
                                                                @foreach($medicaments as $medicament)
                                                                    <option value="{{ $medicament->id }}" {{ (string) data_get($row, 'medicament_id') === (string) $medicament->id ? 'selected' : '' }}>
                                                                        {{ $medicament->nom_commercial }}{{ $medicament->presentation ? ' (' . $medicament->presentation . ')' : '' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="field">
                                                            <label>Posologie</label>
                                                            <input type="text" name="medicaments_template[{{ $index }}][posologie]" value="{{ data_get($row, 'posologie') }}" placeholder="Ex: 1 comprime matin et soir">
                                                        </div>
                                                        <div class="field">
                                                            <label>Duree</label>
                                                            <input type="text" name="medicaments_template[{{ $index }}][duree]" value="{{ data_get($row, 'duree') }}" placeholder="Ex: 7 jours">
                                                        </div>
                                                        <div class="field">
                                                            <label>Quantite</label>
                                                            <input type="text" name="medicaments_template[{{ $index }}][quantite]" value="{{ data_get($row, 'quantite') }}" placeholder="Ex: 14">
                                                        </div>
                                                        <div class="field" style="grid-column:1 / -1;">
                                                            <label>Instructions specifiques</label>
                                                            <input type="text" name="medicaments_template[{{ $index }}][instructions]" value="{{ data_get($row, 'instructions') }}" placeholder="Avant repas, soir, surveillance, adaptation si besoin...">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="actions sticky-actions">
                                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer le modele</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="template-panel">
                            <div class="template-panel-head">
                                <div>
                                    <h3>Modeles existants</h3>
                                    <p>Consultez, mettez a jour, activez ou desactivez les modeles deja disponibles dans la bibliotheque du cabinet.</p>
                                </div>
                                <span class="template-badge">{{ count($ordonnanceTemplates) }} modele(s)</span>
                            </div>
                            <div class="template-panel-body">
                                @if($ordonnanceTemplates->isEmpty())
                                    <div class="template-note">Aucun modele d ordonnance n est encore configure. Creez votre premiere fiche type depuis le panneau de gauche.</div>
                                @else
                                    <div class="template-list">
                                        @foreach($ordonnanceTemplates as $template)
                                            
                                            <details class="template-card" {{ (string) old('template_id') === (string) $template->id ? 'open' : '' }}>
                                                <summary>
                                                    <div class="template-card-title">
                                                        <strong>{{ $template->nom }}</strong>
                                                        <span class="template-card-subtitle">
                                                            {{ $template->categorie ?: 'Categorie libre' }}
                                                            @if($template->medecin)
                                                                &middot; {{ trim($template->medecin->prenom . ' ' . $template->medecin->nom) }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="template-badges">
                                                        <span class="template-badge {{ $template->is_actif ? 'success' : 'muted' }}">{{ $template->is_actif ? 'Actif' : 'Desactive' }}</span>
                                                        <span class="template-badge">{{ $template->est_template_general ? 'Cabinet' : 'Medecin' }}</span>
                                                        <span class="template-badge muted">{{ count($template->medicaments_template ?? []) }} medicament(s)</span>
                                                    </div>
                                                </summary>
                                                <div class="template-card-body">
                                                    <div class="template-note">
                                                        {{ $template->diagnostic_contexte ?: 'Aucun diagnostic ou contexte type renseigne.' }}
                                                    </div>

                                                    @if(!empty($template->medicaments_template))
                                                        <div class="template-med-preview-list">
                                                            @foreach($template->medicaments_template as $row)
                                                                <div class="template-med-preview-item">
                                                                    <strong>{{ $row['medicament_label'] ?? 'Medicament' }}</strong>
                                                                    <div>
                                                                        {{ $row['posologie'] ?: 'Posologie a definir' }}
                                                                        @if(!empty($row['duree'])) &middot; {{ $row['duree'] }} @endif
                                                                        @if(!empty($row['quantite'])) &middot; Qte {{ $row['quantite'] }} @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <form method="POST" action="{{ route('parametres.ordonnances.templates.update', $template) }}" class="template-config-form">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                                                        <div class="grid-2">
                                                            <div class="field">
                                                                <label>Nom du modele</label>
                                                                <input type="text" name="nom" value="{{ $template->nom }}" required>
                                                            </div>
                                                            <div class="field">
                                                                <label>Categorie</label>
                                                                <select name="categorie">
                                                                    <option value="">Selectionner une categorie</option>
                                                                    @foreach($ordonnanceTemplateCategories as $category)
                                                                        <option value="{{ $category }}" {{ $template->categorie === $category ? 'selected' : '' }}>{{ $category }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="grid-2">
                                                            <div class="field">
                                                                <label>Diagnostic ou contexte</label>
                                                                <textarea name="diagnostic_contexte">{{ $template->diagnostic_contexte }}</textarea>
                                                            </div>
                                                            <div class="field">
                                                                <label>Instructions generales</label>
                                                                <textarea name="instructions_generales">{{ $template->instructions_generales }}</textarea>
                                                            </div>
                                                        </div>

                                                        <div class="grid-2">
                                                            <div class="field">
                                                                <label>Medecin rattache</label>
                                                                <select name="medecin_id">
                                                                    <option value="">Aucun medecin specifique</option>
                                                                    @foreach($medecins as $medecin)
                                                                        <option value="{{ $medecin->id }}" {{ (string) $template->medecin_id === (string) $medecin->id ? 'selected' : '' }}>
                                                                            {{ trim($medecin->prenom . ' ' . $medecin->nom) }}{{ $medecin->specialite ? ' - ' . $medecin->specialite : '' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="template-flag-grid">
                                                                <div class="template-flag">
                                                                    <input type="hidden" name="est_template_general" value="0">
                                                                    <label><input type="checkbox" name="est_template_general" value="1" {{ $template->est_template_general ? 'checked' : '' }}> Modele global du cabinet</label>
                                                                    <small>Partage le modele a tous les medecins autorises.</small>
                                                                </div>
                                                                <div class="template-flag">
                                                                    <input type="hidden" name="is_actif" value="0">
                                                                    <label><input type="checkbox" name="is_actif" value="1" {{ $template->is_actif ? 'checked' : '' }}> Modele actif</label>
                                                                    <small>Maintient le modele visible dans la liste de selection rapide.</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="template-meds-box">
                                                            <div class="template-toolbar">
                                                                <div>
                                                                    <div class="group-title" style="margin-bottom:4px;">Medicaments du modele</div>
                                                                    <div class="section-note" style="margin:0;">Ces lignes seront injectees automatiquement dans le formulaire d ordonnance.</div>
                                                                </div>
                                                                <button type="button" class="template-soft-btn js-add-template-med" data-target="ordonnanceTemplateRows{{ $template->id }}">
                                                                    <i class="fas fa-plus"></i> Ajouter medicament
                                                                </button>
                                                            </div>
                                                            <div class="js-template-med-list" id="ordonnanceTemplateRows{{ $template->id }}">
                                                                @foreach($template->template_rows as $index => $row)
                                                                    <div class="template-med-row js-template-med-row">
                                                                        <div class="template-med-row-head">
                                                                            <span class="template-med-row-title">Ligne medicament #{{ $index + 1 }}</span>
                                                                            <button type="button" class="template-soft-btn js-remove-template-med"><i class="fas fa-trash"></i> Retirer</button>
                                                                        </div>
                                                                        <div class="template-med-grid">
                                                                            <div class="field">
                                                                                <label>Medicament</label>
                                                                                <select name="medicaments_template[{{ $index }}][medicament_id]">
                                                                                    <option value="">Selectionner un medicament</option>
                                                                                    @foreach($medicaments as $medicament)
                                                                                        <option value="{{ $medicament->id }}" {{ (string) data_get($row, 'medicament_id') === (string) $medicament->id ? 'selected' : '' }}>
                                                                                            {{ $medicament->nom_commercial }}{{ $medicament->presentation ? ' (' . $medicament->presentation . ')' : '' }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="field">
                                                                                <label>Posologie</label>
                                                                                <input type="text" name="medicaments_template[{{ $index }}][posologie]" value="{{ data_get($row, 'posologie') }}" placeholder="Ex: 1 comprime matin et soir">
                                                                            </div>
                                                                            <div class="field">
                                                                                <label>Duree</label>
                                                                                <input type="text" name="medicaments_template[{{ $index }}][duree]" value="{{ data_get($row, 'duree') }}" placeholder="Ex: 7 jours">
                                                                            </div>
                                                                            <div class="field">
                                                                                <label>Quantite</label>
                                                                                <input type="text" name="medicaments_template[{{ $index }}][quantite]" value="{{ data_get($row, 'quantite') }}" placeholder="Ex: 14">
                                                                            </div>
                                                                            <div class="field" style="grid-column:1 / -1;">
                                                                                <label>Instructions specifiques</label>
                                                                                <input type="text" name="medicaments_template[{{ $index }}][instructions]" value="{{ data_get($row, 'instructions') }}" placeholder="Avant repas, soir, surveillance, adaptation si besoin...">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <div class="template-inline-actions">
                                                            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Mettre a jour</button>
                                                        </div>
                                                    </form>

                                                    <div class="template-inline-actions">
                                                        <form method="POST" action="{{ route('parametres.ordonnances.templates.toggle', $template) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="template-soft-btn">
                                                                <i class="fas {{ $template->is_actif ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                                                {{ $template->is_actif ? 'Desactiver' : 'Activer' }}
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('parametres.ordonnances.templates.destroy', $template) }}" onsubmit="return confirm('Supprimer ce modele d ordonnance ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="template-soft-btn"><i class="fas fa-trash"></i> Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </details>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="security" class="settings-card hidden">
                <div class="settings-head"><h2>Sécurité</h2></div>
                <div class="settings-body">
                    <form method="POST" action="{{ route('parametres.update') }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="section" value="security">
                        <p class="section-note">Renforcez ici la securite des comptes, les durees de session et l acces au journal de securite.</p>
                        <div class="settings-group">
                            <div class="group-title">Authentification</div>
                            <div class="grid-3">
                                <div class="field"><label>Duree session (min)</label><input type="number" name="session_timeout" value="{{ $allSettings['session_timeout'] ?? 120 }}"></div>
                                <div class="field"><label>Max tentatives login</label><input type="number" name="max_login_attempts" value="{{ $allSettings['max_login_attempts'] ?? 5 }}"></div>
                                <div class="field"><label>Retention journal securite (jours)</label><input type="number" name="security_log_retention_days" value="{{ $allSettings['security_log_retention_days'] ?? 180 }}"></div>
                            </div>
                        </div>
                        <div class="settings-group">
                            <div class="group-title">Protection avancee</div>
                            <div class="toggle-grid">
                                <div class="toggle-card">
                                    <label><input type="hidden" name="security_2fa_enforced" value="0"><input type="checkbox" name="security_2fa_enforced" value="1" {{ !empty($allSettings['security_2fa_enforced']) ? 'checked' : '' }}> Activation 2FA</label>
                                    <small>Recommande ou impose la double authentification pour les comptes sensibles.</small>
                                </div>
                                <div class="toggle-card">
                                    <label><input type="hidden" name="security_log_enabled" value="0"><input type="checkbox" name="security_log_enabled" value="1" {{ !empty($allSettings['security_log_enabled']) ? 'checked' : '' }}> Journal de securite</label>
                                    <small>Conserve les evenements critiques et facilite l audit des acces.</small>
                                </div>
                            </div>
                        </div>
                        <div class="actions sticky-actions">
                            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button>
                            <a href="{{ route('admin.settings.audit') }}" class="btn-soft"><i class="fas fa-book-shield"></i> Ouvrir le journal de securite</a>
                        </div>
                    </form>
                </div>
            </div>

            <div id="backup" class="settings-card hidden">
                <div class="settings-head"><h2>Sauvegardes</h2></div>
                <div class="settings-body">
                    <form method="POST" action="{{ route('parametres.update') }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="section" value="backup">
                        <p class="section-note">Planifiez les sauvegardes automatiques, lancez une sauvegarde manuelle, telechargez les derniers fichiers et restaurez un export si necessaire.</p>
                        <div class="settings-group">
                            <div class="group-title">Planification sauvegarde</div>
                            <div class="grid-3">
                                <div class="field">
                                    <label>Frequence</label>
                                    <select name="backup_frequency">
                                        <option value="daily" {{ ($allSettings['backup_frequency'] ?? '') === 'daily' ? 'selected' : '' }}>Quotidienne</option>
                                        <option value="weekly" {{ ($allSettings['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                                        <option value="monthly" {{ ($allSettings['backup_frequency'] ?? '') === 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                                    </select>
                                </div>
                                <div class="field"><label>Heure</label><input type="time" name="backup_time" value="{{ $allSettings['backup_time'] ?? '02:00' }}"></div>
                                <div class="field"><label>Retention (jours)</label><input type="number" name="backup_retention_days" value="{{ $allSettings['backup_retention_days'] ?? 14 }}"></div>
                            </div>
                        </div>
                        <div class="actions sticky-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button></div>
                    </form>

                    <div class="settings-group" style="margin-top: 18px;">
                        <div class="group-title">Operations de sauvegarde</div>
                        <p class="section-note" style="margin-bottom: 10px;">Le systeme cree des sauvegardes SQL dans `storage/app/backups/database`. La restauration declenche d abord une sauvegarde de securite.</p>
                        <div class="actions" style="margin-top: 0; margin-bottom: 14px;">
                            <button type="button" class="btn-save" id="manualBackupBtn"><i class="fas fa-database"></i> Lancer une sauvegarde</button>
                            <a href="{{ route('parametres.backup-download') }}" class="btn-soft"><i class="fas fa-download"></i> Telecharger la plus recente</a>
                        </div>

                        <div class="backup-list" style="margin-bottom: 16px;">
                            @forelse($backupFiles as $backup)
                                <div class="backup-item">
                                    <div class="backup-meta">
                                        <strong>{{ $backup['name'] }}</strong>
                                        <span>{{ $backup['updated_at'] }} &middot; {{ $backup['size_human'] }}</span>
                                    </div>
                                    <a href="{{ route('parametres.backup-download', ['file' => $backup['relative_path']]) }}" class="btn-soft"><i class="fas fa-download"></i> Telecharger</a>
                                </div>
                            @empty
                                <div class="backup-item">
                                    <div class="backup-meta">
                                        <strong>Aucune sauvegarde detectee</strong>
                                        <span>Lancez une sauvegarde manuelle pour initialiser l historique.</span>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('parametres.backup-restore') }}" enctype="multipart/form-data" onsubmit="return confirm('Confirmer la restauration de cette sauvegarde sur la base courante ?');">
                            @csrf
                            <div class="grid-2">
                                <div class="field">
                                    <label>Restaurer une sauvegarde</label>
                                    <input type="file" name="restore_backup_file" accept=".sql,.sqlite,.db" required>
                                    <div class="field-inline-note">Chargez un export `.sql`, `.sqlite` ou `.db` selon votre moteur de base de donnees.</div>
                                </div>
                                <div class="actions" style="align-items: end; margin-top: 0;">
                                    <button type="submit" class="btn-save"><i class="fas fa-rotate-left"></i> Restaurer la sauvegarde</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="settings-group" style="margin-top: 18px;">
                        <div class="group-title">Maintenance systeme</div>
                        <p style="margin: 0 0 10px; color: #64748b; font-size: 13px;">
                            Vider les caches applicatifs (config, routes, vues, events, cache runtime).
                        </p>
                        <form method="POST" action="{{ route('parametres.clear-cache') }}" onsubmit="return confirm('Confirmer le vidage des caches systeme ?');">
                            @csrf
                            <div class="actions" style="margin-top: 0;">
                                <button type="submit" class="btn-save">Vider les caches systeme</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="integration" class="settings-card hidden">
                <div class="settings-head"><h2>Intégrations</h2></div>
                <div class="settings-body">
                    <form method="POST" action="{{ route('parametres.update') }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="section" value="integration">
                        <p class="section-note">Configurez ici les integrations techniques : passerelle SMS, SMTP, API externes et synchronisation calendrier.</p>
                        <div class="settings-group">
                            <div class="group-title">SMTP email</div>
                            <div class="grid-3">
                                <div class="field"><label>Serveur SMTP</label><input type="text" name="smtp_host" value="{{ $allSettings['smtp_host'] ?? 'smtp.gmail.com' }}"></div>
                                <div class="field"><label>Port</label><input type="number" name="smtp_port" value="{{ $allSettings['smtp_port'] ?? 587 }}"></div>
                                <div class="field"><label>Utilisateur SMTP</label><input type="email" name="smtp_username" value="{{ $allSettings['smtp_username'] ?? '' }}"></div>
                                <div class="field"><label>Mot de passe SMTP</label><input type="password" name="smtp_password" value="{{ $allSettings['smtp_password'] ?? '' }}"></div>
                                <div class="field"><label>Chiffrement</label>
                                    <select name="smtp_encryption">
                                        <option value="tls" {{ ($allSettings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ ($allSettings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="none" {{ ($allSettings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' }}>Aucun</option>
                                    </select>
                                </div>
                                <div class="actions" style="align-items:end;margin-top:0;">
                                    <button type="button" class="btn-soft" id="testSmtpBtn"><i class="fas fa-paper-plane"></i> Tester SMTP</button>
                                </div>
                            </div>
                        </div>
                        <div class="settings-group">
                            <div class="group-title">Passerelle SMS et calendrier</div>
                            <div class="grid-3">
                                <div class="field">
                                    <label>SMS Gateway</label>
                                    <select name="sms_provider">
                                        <option value="twilio" {{ ($allSettings['sms_provider'] ?? '') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                                        <option value="nexmo" {{ ($allSettings['sms_provider'] ?? '') === 'nexmo' ? 'selected' : '' }}>Nexmo</option>
                                        <option value="custom" {{ ($allSettings['sms_provider'] ?? '') === 'custom' ? 'selected' : '' }}>API personnalisee</option>
                                    </select>
                                </div>
                                <div class="field"><label>Cle API SMS</label><input type="password" name="sms_api_key" value="{{ $allSettings['sms_api_key'] ?? '' }}"></div>
                                <div class="field"><label>Identifiant expediteur</label><input type="text" name="sms_sender_id" value="{{ $allSettings['sms_sender_id'] ?? '' }}"></div>
                                <div class="field">
                                    <label>Integration calendrier</label>
                                    <select name="calendar_provider">
                                        <option value="none" {{ ($allSettings['calendar_provider'] ?? 'none') === 'none' ? 'selected' : '' }}>Aucune</option>
                                        <option value="google" {{ ($allSettings['calendar_provider'] ?? '') === 'google' ? 'selected' : '' }}>Google Calendar</option>
                                        <option value="outlook" {{ ($allSettings['calendar_provider'] ?? '') === 'outlook' ? 'selected' : '' }}>Outlook</option>
                                    </select>
                                </div>
                                <div class="field"><label>Email calendrier / compte service</label><input type="email" name="calendar_account" value="{{ $allSettings['calendar_account'] ?? '' }}"></div>
                                <div class="field"><label>Jeton / secret calendrier</label><input type="password" name="calendar_token" value="{{ $allSettings['calendar_token'] ?? '' }}"></div>
                            </div>
                        </div>
                        <div class="settings-group">
                            <div class="group-title">API externes et webhooks</div>
                            <div class="grid-1">
                                <div class="field"><label>Google Maps API Key</label><input type="password" name="google_maps_key" value="{{ $allSettings['google_maps_key'] ?? '' }}"></div>
                                <div class="field"><label>URL Webhook Consultation</label><input type="url" name="webhook_consultation" value="{{ $allSettings['webhook_consultation'] ?? '' }}" placeholder="https://..."></div>
                                <div class="field"><label>URL Webhook Paiement</label><input type="url" name="webhook_payment" value="{{ $allSettings['webhook_payment'] ?? '' }}" placeholder="https://..."></div>
                                <div class="field"><label>API externe DMP / tiers</label><input type="url" name="external_api_base_url" value="{{ $allSettings['external_api_base_url'] ?? '' }}" placeholder="https://..."></div>
                            </div>
                        </div>
                        <div class="actions sticky-actions"><button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button></div>
                    </form>
                </div>
            </div>

            @if(auth()->user() && auth()->user()->isAdmin())
                <div id="permissions" class="settings-card hidden">
                    <div class="settings-head"><h2>Permissions et accès</h2></div>
                    <div class="settings-body">
                        <div class="permissions-info">
                            Exemple: pour donner a Karim l'acces a un module, cochez les modules souhaites sur sa ligne puis enregistrez.
                        </div>

                        <form method="POST" action="{{ route('parametres.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="permissions">

                            <div class="permissions-wrap">
                                <table class="permissions-table">
                                    <thead>
                                        <tr>
                                            <th>Utilisateur</th>
                                            @foreach($managedModules as $module)
                                                <th>{{ $module['label'] }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($permissionUsers as $user)
                                            <tr>
                                                <td>
                                                    <span class="permissions-name">{{ $user->name }}</span>
                                                    <span class="permissions-email">{{ $user->email }}</span>
                                                </td>
                                                @foreach($managedModules as $module)
                                                    <td>
                                                        <input
                                                            type="checkbox"
                                                            name="module_permissions[{{ $user->id }}][]"
                                                            value="{{ $module['id'] }}"
                                                            {{ $user->hasModuleAccess($module['id']) ? 'checked' : '' }}
                                                        >
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ count($managedModules) + 1 }}">Aucun utilisateur non-admin trouve.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="actions">
                                <button type="submit" class="btn-save">Enregistrer les droits</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>

<template id="ordonnanceTemplateMedicationRowTemplate">
    <div class="template-med-row js-template-med-row">
        <div class="template-med-row-head">
            <span class="template-med-row-title">Ligne medicament #__NUMBER__</span>
            <button type="button" class="template-soft-btn js-remove-template-med"><i class="fas fa-trash"></i> Retirer</button>
        </div>
        <div class="template-med-grid">
            <div class="field">
                <label>Medicament</label>
                <select name="medicaments_template[__INDEX__][medicament_id]">
                    <option value="">Selectionner un medicament</option>
                    @foreach($medicaments as $medicament)
                        <option value="{{ $medicament->id }}">
                            {{ $medicament->nom_commercial }}{{ $medicament->presentation ? ' (' . $medicament->presentation . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Posologie</label>
                <input type="text" name="medicaments_template[__INDEX__][posologie]" placeholder="Ex: 1 comprime matin et soir">
            </div>
            <div class="field">
                <label>Duree</label>
                <input type="text" name="medicaments_template[__INDEX__][duree]" placeholder="Ex: 7 jours">
            </div>
            <div class="field">
                <label>Quantite</label>
                <input type="text" name="medicaments_template[__INDEX__][quantite]" placeholder="Ex: 14">
            </div>
            <div class="field" style="grid-column:1 / -1;">
                <label>Instructions specifiques</label>
                <input type="text" name="medicaments_template[__INDEX__][instructions]" placeholder="Avant repas, soir, surveillance, adaptation si besoin...">
            </div>
        </div>
    </div>
</template>

<script>
    function showSection(event, sectionId) {
        event.preventDefault();
        document.querySelectorAll('.settings-card').forEach(card => card.classList.add('hidden'));
        const section = document.getElementById(sectionId);
        if (section) {
            section.classList.remove('hidden');
        }
        document.querySelectorAll('.settings-nav a').forEach(item => item.classList.remove('active'));
        event.currentTarget.classList.add('active');
        window.location.hash = sectionId;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const smtpBtn = document.getElementById('testSmtpBtn');
        const backupBtn = document.getElementById('manualBackupBtn');
        const cabinetLogoInput = document.getElementById('cabinet_logo');
        const cabinetLogoPreview = document.getElementById('cabinetLogoPreview');
        const medicationRowTemplate = document.getElementById('ordonnanceTemplateMedicationRowTemplate');

        function refreshTemplateMedicationTitles(container) {
            if (!container) {
                return;
            }

            container.querySelectorAll('.js-template-med-row').forEach((row, index) => {
                const title = row.querySelector('.template-med-row-title');
                if (title) {
                    title.textContent = `Ligne medicament #${index + 1}`;
                }
            });
        }

        function addTemplateMedicationRow(targetId) {
            const container = document.getElementById(targetId);
            if (!container || !medicationRowTemplate) {
                return;
            }

            const currentCounter = Number(container.dataset.nextIndex || container.querySelectorAll('.js-template-med-row').length);
            const nextIndex = Number.isNaN(currentCounter) ? container.querySelectorAll('.js-template-med-row').length : currentCounter;
            const html = medicationRowTemplate.innerHTML
                .replaceAll('__INDEX__', String(nextIndex))
                .replaceAll('__NUMBER__', String(nextIndex + 1));

            container.insertAdjacentHTML('beforeend', html);
            container.dataset.nextIndex = String(nextIndex + 1);
            refreshTemplateMedicationTitles(container);
        }

        const hash = window.location.hash ? window.location.hash.slice(1) : '';
        if (hash) {
            const targetSection = document.getElementById(hash);
            const targetNav = Array.from(document.querySelectorAll('.settings-nav a'))
                .find(a => (a.getAttribute('onclick') || '').includes("'" + hash + "'"));

            if (targetSection && targetNav) {
                document.querySelectorAll('.settings-card').forEach(card => card.classList.add('hidden'));
                targetSection.classList.remove('hidden');
                document.querySelectorAll('.settings-nav a').forEach(item => item.classList.remove('active'));
                targetNav.classList.add('active');
            }
        }

        if (cabinetLogoInput && cabinetLogoPreview) {
            cabinetLogoInput.addEventListener('change', function () {
                if (!cabinetLogoInput.files || !cabinetLogoInput.files[0]) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    const src = event.target && event.target.result ? event.target.result : '';
                    if (src) {
                        cabinetLogoPreview.innerHTML = '<img src="' + src + '" alt="Logo cabinet">';
                    }
                };
                reader.readAsDataURL(cabinetLogoInput.files[0]);
            });
        }

        document.querySelectorAll('.js-template-med-list').forEach((container) => {
            container.dataset.nextIndex = String(container.querySelectorAll('.js-template-med-row').length);
            refreshTemplateMedicationTitles(container);
        });

        document.querySelectorAll('.js-add-template-med').forEach((button) => {
            button.addEventListener('click', function () {
                const targetId = button.dataset.target;
                addTemplateMedicationRow(targetId);
            });
        });

        document.addEventListener('click', function (event) {
            const removeButton = event.target.closest('.js-remove-template-med');
            if (!removeButton) {
                return;
            }

            const container = removeButton.closest('.js-template-med-list');
            const row = removeButton.closest('.js-template-med-row');

            if (!container || !row) {
                return;
            }

            if (container.querySelectorAll('.js-template-med-row').length === 1) {
                const fields = row.querySelectorAll('input, select');
                fields.forEach((field) => {
                    field.value = '';
                });
                return;
            }

            row.remove();
            refreshTemplateMedicationTitles(container);
        });

        if (smtpBtn) {
            smtpBtn.addEventListener('click', async function () {
                smtpBtn.disabled = true;
                const original = smtpBtn.innerHTML;
                smtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Test en cours';

                try {
                    const response = await fetch('{{ route('parametres.test-smtp') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });
                    const payload = await response.json();
                    alert(payload.message || (response.ok ? 'Test SMTP termine.' : 'Erreur de test SMTP.'));
                } catch (error) {
                    alert('Impossible de tester la configuration SMTP.');
                } finally {
                    smtpBtn.disabled = false;
                    smtpBtn.innerHTML = original;
                }
            });
        }

        if (backupBtn) {
            backupBtn.addEventListener('click', async function () {
                backupBtn.disabled = true;
                const original = backupBtn.innerHTML;
                backupBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde en cours';

                try {
                    const response = await fetch('{{ route('parametres.backup') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });
                    const payload = await response.json();
                    alert(payload.message || (response.ok ? 'Sauvegarde terminee.' : 'Erreur de sauvegarde.'));
                    if (response.ok) {
                        window.location.reload();
                    }
                } catch (error) {
                    alert('Impossible de lancer la sauvegarde.');
                } finally {
                    backupBtn.disabled = false;
                    backupBtn.innerHTML = original;
                }
            });
        }
    });
</script>
@endsection
