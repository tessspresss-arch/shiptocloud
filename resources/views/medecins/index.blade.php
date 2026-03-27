@extends('layouts.app')

@section('title', 'Gestion des Medecins')
@section('topbar_subtitle', 'Pilotage des profils medicaux, disponibilites et coordination du cabinet dans une interface premium.')

@push('styles')
<style>
    :root {
        --med-bg: linear-gradient(180deg, #f4f9fd 0%, #edf5fb 100%);
        --med-surface: rgba(255, 255, 255, 0.88);
        --med-border: #d8e5ef;
        --med-text: #17324c;
        --med-muted: #68829a;
        --med-primary: #1b79c9;
        --med-primary-strong: #145d98;
        --med-success: #16956f;
        --med-warning: #c98212;
        --med-danger: #d74d5d;
        --med-shadow: 0 24px 42px -34px rgba(15, 40, 65, 0.36);
    }

    .med-page {
        width: 100%;
        max-width: none;
        padding: 8px 8px 92px;
    }

    .med-shell {
        display: grid;
        gap: 18px;
    }

    .med-hero,
    .med-kpi,
    .med-filter-card,
    .med-table-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--med-border);
        border-radius: 24px;
        box-shadow: var(--med-shadow);
    }

    .med-hero {
        padding: 22px;
        background:
            radial-gradient(circle at top right, rgba(27, 121, 201, 0.15) 0%, rgba(27, 121, 201, 0) 34%),
            radial-gradient(circle at left top, rgba(22, 149, 111, 0.08) 0%, rgba(22, 149, 111, 0) 30%),
            var(--med-bg);
    }

    .med-kpi,
    .med-filter-card,
    .med-table-card {
        background: var(--med-surface);
        backdrop-filter: blur(10px);
    }

    .med-hero::before,
    .med-kpi::before,
    .med-filter-card::before,
    .med-table-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .med-hero > *,
    .med-kpi > *,
    .med-filter-card > *,
    .med-table-card > * {
        position: relative;
        z-index: 1;
    }

    .med-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .med-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(27, 121, 201, 0.14);
        background: rgba(255, 255, 255, 0.76);
        color: var(--med-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .med-title-row {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: nowrap;
        margin-top: 0;
    }

    .med-title-row .med-eyebrow {
        flex-shrink: 0;
    }

    .med-title-content {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 16px;
        flex: 0 0 auto;
        flex-wrap: wrap;
        min-width: auto;
        margin-left: auto;
    }

    .med-title-copy {
        flex: 1 1 440px;
        min-width: 0;
    }

    .med-title-copy.is-compact {
        display: none;
    }

    .med-title-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.4rem;
        background: linear-gradient(135deg, var(--med-primary) 0%, var(--med-primary-strong) 100%);
        box-shadow: 0 18px 28px -20px rgba(27, 121, 201, 0.58);
        flex-shrink: 0;
    }

    .med-title {
        margin: 0;
        color: var(--med-text);
        font-size: clamp(1.6rem, 2.6vw, 2.3rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .med-subtitle {
        margin: 8px 0 0;
        max-width: 72ch;
        color: var(--med-muted);
        font-size: .98rem;
        line-height: 1.64;
        font-weight: 600;
    }

    .med-badge-row,
    .med-hero-actions,
    .med-active-filters,
    .med-table-meta,
    .med-contact-stack,
    .med-actions-cell {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .med-badge-row {
        margin-top: 16px;
    }

    .med-badge,
    .med-chip,
    .med-inline-tag,
    .med-status-pill,
    .med-specialty-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        white-space: nowrap;
        font-weight: 700;
    }

    .med-badge {
        background: linear-gradient(135deg, var(--med-primary) 0%, var(--med-primary-strong) 100%);
        color: #fff;
        box-shadow: 0 16px 24px -22px rgba(27, 121, 201, 0.92);
    }

    .med-chip,
    .med-inline-tag,
    .med-specialty-pill {
        border: 1px solid #d7e4ef;
        background: #f6fafe;
        color: #57728c;
        font-size: .85rem;
    }

    .med-chip i,
    .med-inline-tag i,
    .med-specialty-pill i {
        color: var(--med-primary);
    }

    .med-kpi-label,
    .med-filter-kicker,
    .med-table-kicker {
        margin: 0;
        color: #708aa2;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .med-btn,
    .med-filter-btn,
    .med-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .med-btn,
    .med-filter-btn {
        min-height: 50px;
        border-radius: 16px;
        border: 1px solid transparent;
        padding: 0 18px;
        font-size: .92rem;
        font-weight: 800;
    }

    .med-btn:hover,
    .med-btn:focus,
    .med-filter-btn:hover,
    .med-filter-btn:focus,
    .med-icon-btn:hover,
    .med-icon-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .med-btn.secondary,
    .med-filter-btn.secondary {
        border-color: #cfdeec;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        color: #48657f;
        box-shadow: 0 14px 22px -24px rgba(15, 23, 42, 0.42);
    }

    .med-btn.secondary:hover,
    .med-filter-btn.secondary:hover,
    .med-btn.secondary:focus,
    .med-filter-btn.secondary:focus {
        color: var(--med-primary-strong);
        border-color: rgba(27, 121, 201, 0.3);
        background: linear-gradient(180deg, #ffffff 0%, #ebf4fb 100%);
    }

    .med-btn.primary,
    .med-filter-btn.primary {
        background: linear-gradient(135deg, var(--med-success) 0%, #117454 100%);
        color: #fff;
        box-shadow: 0 18px 28px -24px rgba(22, 149, 111, 0.84);
    }

    .med-btn.primary:hover,
    .med-filter-btn.primary:hover,
    .med-btn.primary:focus,
    .med-filter-btn.primary:focus {
        color: #fff;
        box-shadow: 0 24px 32px -24px rgba(22, 149, 111, 0.82);
    }

    .med-kpi-copy,
    .med-filter-copy,
    .med-table-copy,
    .med-submeta,
    .med-contact-line,
    .med-pagination-info,
    .med-empty p {
        margin: 0;
        color: var(--med-muted);
        font-size: .92rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .med-hero-actions {
        justify-content: flex-start;
        align-self: flex-start;
    }

    .med-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .med-kpi {
        padding: 22px;
    }

    .med-kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 18px;
    }

    .med-kpi-value {
        margin: 6px 0 0;
        color: var(--med-text);
        font-size: clamp(1.8rem, 2.5vw, 2.35rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .med-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .med-kpi-icon.active {
        background: rgba(27, 121, 201, 0.12);
        color: var(--med-primary);
    }

    .med-kpi-icon.specialty {
        background: rgba(22, 149, 111, 0.12);
        color: var(--med-success);
    }

    .med-kpi-icon.rdv {
        background: rgba(201, 130, 18, 0.12);
        color: var(--med-warning);
    }

    .med-kpi-icon.inactive {
        background: rgba(215, 77, 93, 0.12);
        color: var(--med-danger);
    }

    .med-filter-card,
    .med-table-head,
    .med-table-footer {
        border-color: #dbe7f1;
    }

    .med-filter-card {
        padding: 20px;
    }

    .med-filter-head,
    .med-table-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }

    .med-filter-title,
    .med-table-title {
        margin: 4px 0 0;
        color: var(--med-text);
        font-size: 1.04rem;
        font-weight: 800;
    }

    .med-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) repeat(3, minmax(180px, .72fr)) auto;
        gap: 12px;
        align-items: end;
        margin-top: 18px;
    }

    .med-field {
        display: grid;
        gap: 8px;
    }

    .med-field label {
        color: #6f88a0;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .med-search-wrap {
        position: relative;
    }

    .med-search-wrap i {
        position: absolute;
        top: 50%;
        left: 18px;
        transform: translateY(-50%);
        color: #8ca3ba;
    }

    .med-search,
    .med-select {
        width: 100%;
        min-height: 52px;
        border-radius: 16px;
        border: 1px solid #cbd9e8;
        background: rgba(255, 255, 255, 0.94);
        color: var(--med-text);
        font-size: .95rem;
        font-weight: 600;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .med-search {
        padding: 0 16px 0 48px;
    }

    .med-select {
        padding: 0 16px;
    }

    .med-search:focus,
    .med-select:focus {
        outline: none;
        border-color: rgba(27, 121, 201, 0.42);
        box-shadow: 0 0 0 4px rgba(27, 121, 201, 0.1);
    }

    .med-filter-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
    }

    .med-table-card {
        overflow: hidden;
    }

    .med-table-head {
        padding: 20px 22px 16px;
        border-bottom: 1px solid #dbe7f1;
    }

    .med-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .med-table {
        width: 100%;
        min-width: 960px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .med-table thead th {
        padding: 16px 22px;
        border-bottom: 1px solid #dbe7f1;
        background: rgba(243, 248, 253, 0.8);
        color: #6a859d;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .med-table tbody tr {
        transition: background .2s ease;
    }

    .med-table tbody tr:hover {
        background: rgba(238, 246, 252, 0.74);
    }

    .med-table tbody td {
        padding: 18px 22px;
        border-bottom: 1px solid #e4edf5;
        vertical-align: top;
        color: var(--med-text);
        font-size: .94rem;
    }

    .med-matricule {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--med-primary-strong);
        font-size: .86rem;
        font-weight: 800;
    }

    .med-name {
        margin: 0;
        color: var(--med-text);
        font-size: .98rem;
        line-height: 1.36;
        font-weight: 800;
    }

    .med-avatar {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid #d8e5ef;
        box-shadow: 0 12px 18px -20px rgba(15, 23, 42, 0.5);
    }

    .med-profile-cell {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .med-submeta {
        margin-top: 6px;
    }

    .med-contact-stack {
        display: grid;
        gap: 8px;
    }

    .med-contact-line {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .med-contact-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef5fb;
        color: var(--med-primary);
        flex-shrink: 0;
    }

    .med-order {
        color: #3f5c77;
        font-size: .92rem;
        font-weight: 700;
    }

    .med-status-pill {
        border: 1px solid transparent;
        font-size: .82rem;
    }

    .med-status-pill.actif {
        background: rgba(22, 149, 111, 0.12);
        color: #0d6c50;
        border-color: rgba(22, 149, 111, 0.16);
    }

    .med-status-pill.inactif,
    .med-status-pill.retraite {
        background: rgba(100, 116, 139, 0.12);
        color: #475569;
        border-color: rgba(100, 116, 139, 0.18);
    }

    .med-status-pill.en_conge,
    .med-status-pill.conge {
        background: rgba(201, 130, 18, 0.13);
        color: #8c5b09;
        border-color: rgba(201, 130, 18, 0.18);
    }

    .med-table-tools {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .med-mode-compact .med-table th,
    .med-mode-compact .med-table td {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .med-mode-compact .med-submeta,
    .med-mode-compact .med-contact-line + .med-contact-line {
        display: none;
    }

    .med-mode-compact .med-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
    }

    .med-mode-cards .med-table thead {
        display: none;
    }

    .med-mode-cards .med-table,
    .med-mode-cards .med-table tbody {
        display: grid;
        gap: 12px;
        width: 100%;
    }

    .med-mode-cards .med-table tbody tr {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 14px;
        padding: 16px;
        border: 1px solid #dbe5ef;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 16px 22px -26px rgba(15, 23, 42, .16);
    }

    .med-mode-cards .med-table td {
        display: grid;
        gap: 4px;
        padding: 0;
        border: none;
    }

    .med-mode-cards .med-table td::before {
        content: attr(data-label);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #7a8ea5;
    }

    .med-mode-cards .med-table td:last-child {
        grid-column: 1 / -1;
    }

    .med-actions-cell {
        justify-content: flex-end;
    }

    .med-icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        border: 1px solid #d4e2ee;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fc 100%);
        color: #5b7691;
    }

    .med-icon-btn.view:hover,
    .med-icon-btn.view:focus {
        color: var(--med-success);
        border-color: rgba(22, 149, 111, 0.24);
    }

    .med-icon-btn.edit:hover,
    .med-icon-btn.edit:focus {
        color: var(--med-warning);
        border-color: rgba(201, 130, 18, 0.24);
    }

    .med-icon-btn.rdv:hover,
    .med-icon-btn.rdv:focus {
        color: var(--med-primary);
        border-color: rgba(27, 121, 201, 0.22);
    }

    .med-icon-btn.delete:hover,
    .med-icon-btn.delete:focus {
        color: var(--med-danger);
        border-color: rgba(215, 77, 93, 0.22);
    }

    .med-empty {
        padding: 44px 22px 48px;
        text-align: center;
    }

    .med-empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.65rem;
        color: var(--med-primary);
        background: rgba(27, 121, 201, 0.12);
        box-shadow: inset 0 0 0 1px rgba(27, 121, 201, 0.08);
    }

    .med-empty h3 {
        margin: 0;
        color: var(--med-text);
        font-size: 1.2rem;
        font-weight: 800;
    }

    .med-table-footer {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
        padding: 18px 22px 22px;
        border-top: 1px solid #dbe7f1;
        background: rgba(245, 249, 252, 0.78);
    }

    .med-table-footer .pagination {
        margin-bottom: 0;
    }

    .med-table-footer .page-link {
        border-radius: 12px;
        border-color: #d0deeb;
        color: var(--med-primary-strong);
        font-weight: 700;
    }

    .med-table-footer .page-item.active .page-link {
        background: linear-gradient(135deg, var(--med-primary) 0%, var(--med-primary-strong) 100%);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 14px 22px -20px rgba(27, 121, 201, 0.92);
    }

    html.dark body .med-hero,
    body.dark-mode .med-hero {
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.12) 0%, rgba(56, 189, 248, 0) 36%),
            radial-gradient(circle at left top, rgba(15, 118, 110, 0.18) 0%, rgba(15, 118, 110, 0) 34%),
            linear-gradient(180deg, #0f2136 0%, #10263d 100%);
        border-color: #294661;
    }

    html.dark body .med-kpi,
    html.dark body .med-filter-card,
    html.dark body .med-table-card,
    html.dark body .med-hero,
    body.dark-mode .med-kpi,
    body.dark-mode .med-filter-card,
    body.dark-mode .med-table-card,
    body.dark-mode .med-hero {
        background: rgba(12, 27, 43, 0.92);
        border-color: #294661;
        box-shadow: 0 26px 48px -36px rgba(0, 0, 0, 0.62);
    }

    html.dark body .med-title,
    html.dark body .med-kpi-value,
    html.dark body .med-filter-title,
    html.dark body .med-table-title,
    html.dark body .med-name,
    html.dark body .med-empty h3,
    body.dark-mode .med-title,
    body.dark-mode .med-kpi-value,
    body.dark-mode .med-filter-title,
    body.dark-mode .med-table-title,
    body.dark-mode .med-name,
    body.dark-mode .med-empty h3 {
        color: #e7f0fb;
    }

    html.dark body .med-subtitle,
    html.dark body .med-chip,
    html.dark body .med-inline-tag,
    html.dark body .med-specialty-pill,
    html.dark body .med-kpi-label,
    html.dark body .med-filter-kicker,
    html.dark body .med-table-kicker,
    html.dark body .med-kpi-copy,
    html.dark body .med-filter-copy,
    html.dark body .med-table-copy,
    html.dark body .med-submeta,
    html.dark body .med-contact-line,
    html.dark body .med-pagination-info,
    html.dark body .med-empty p,
    body.dark-mode .med-subtitle,
    body.dark-mode .med-chip,
    body.dark-mode .med-inline-tag,
    body.dark-mode .med-specialty-pill,
    body.dark-mode .med-kpi-label,
    body.dark-mode .med-filter-kicker,
    body.dark-mode .med-table-kicker,
    body.dark-mode .med-kpi-copy,
    body.dark-mode .med-filter-copy,
    body.dark-mode .med-table-copy,
    body.dark-mode .med-submeta,
    body.dark-mode .med-contact-line,
    body.dark-mode .med-pagination-info,
    body.dark-mode .med-empty p {
        color: #9db4cb;
    }

    html.dark body .med-chip,
    html.dark body .med-inline-tag,
    html.dark body .med-specialty-pill,
    body.dark-mode .med-chip,
    body.dark-mode .med-inline-tag,
    body.dark-mode .med-specialty-pill {
        background: #102337;
        border-color: #2c4a65;
        color: #b5c8db;
    }

    html.dark body .med-chip i,
    html.dark body .med-inline-tag i,
    html.dark body .med-specialty-pill i,
    body.dark-mode .med-chip i,
    body.dark-mode .med-inline-tag i,
    body.dark-mode .med-specialty-pill i {
        color: #69c1ff;
    }

    html.dark body .med-search,
    html.dark body .med-select,
    body.dark-mode .med-search,
    body.dark-mode .med-select {
        background: #0b1b2d;
        border-color: #31536f;
        color: #e7f0fb;
    }

    html.dark body .med-search::placeholder,
    body.dark-mode .med-search::placeholder {
        color: #8da7c1;
    }

    html.dark body .med-search-wrap i,
    body.dark-mode .med-search-wrap i {
        color: #88a4be;
    }

    html.dark body .med-btn.secondary,
    html.dark body .med-filter-btn.secondary,
    html.dark body .med-icon-btn,
    html.dark body .med-avatar,
    body.dark-mode .med-btn.secondary,
    body.dark-mode .med-filter-btn.secondary,
    body.dark-mode .med-icon-btn,
    body.dark-mode .med-avatar {
        background: #12283d;
        border-color: #31536f;
        color: #bdd0e3;
    }

    html.dark body .med-contact-icon,
    body.dark-mode .med-contact-icon {
        background: #173456;
        color: #8ec6ff;
    }

    html.dark body .med-order,
    html.dark body .med-matricule,
    body.dark-mode .med-order,
    body.dark-mode .med-matricule {
        color: #d7e8ff;
    }

    html.dark body .med-table thead th,
    html.dark body .med-table-head,
    html.dark body .med-table-footer,
    body.dark-mode .med-table thead th,
    body.dark-mode .med-table-head,
    body.dark-mode .med-table-footer {
        background: #102337;
        border-color: #284660;
    }

    html.dark body .med-table thead th,
    body.dark-mode .med-table thead th {
        color: #a8bfd7;
    }

    html.dark body .med-table tbody tr:hover,
    body.dark-mode .med-table tbody tr:hover {
        background: rgba(20, 43, 66, 0.86);
    }

    html.dark body .med-table tbody td,
    body.dark-mode .med-table tbody td {
        color: #d7e8ff;
        border-bottom-color: #203c57;
    }

    html.dark body .med-table-footer .page-link,
    body.dark-mode .med-table-footer .page-link {
        background: #12283d;
        border-color: #31536f;
        color: #c9def1;
    }

    html.dark body .med-empty-icon,
    body.dark-mode .med-empty-icon {
        background: rgba(56, 189, 248, 0.12);
        color: #7fd1ff;
    }

    @media (max-width: 1200px) {
        .med-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .med-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .med-filter-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 992px) {
        .med-hero-grid {
            grid-template-columns: 1fr;
        }

        .med-table-head {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }

        .med-table-tools,
        .display-mode-switch {
            width: 100%;
        }

        .med-table-meta {
            width: 100%;
            justify-content: flex-start;
        }

        .display-mode-switch {
            justify-content: stretch;
        }

        .display-mode-option {
            flex: 1 1 0;
            min-width: 0;
            text-align: center;
        }

        .med-table-wrap {
            overflow: visible;
            border: 0;
            border-radius: 0;
            background: transparent;
        }

        .med-table {
            min-width: 0;
            display: block;
            background: transparent;
        }

        .med-table thead {
            display: none;
        }

        .med-table tbody {
            display: grid;
            gap: 14px;
        }

        .med-table tbody tr {
            display: grid;
            gap: 12px;
            padding: 18px;
            border: 1px solid #dbe7f1;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 16px 28px -28px rgba(15, 40, 65, 0.3);
        }

        .med-table tbody td {
            display: grid;
            grid-template-columns: minmax(96px, 118px) minmax(0, 1fr);
            gap: 10px;
            align-items: start;
            padding: 0;
            border: 0;
        }

        .med-table tbody td::before {
            content: attr(data-label);
            color: #718aa3;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .med-table tbody td[data-label="Actions"] {
            grid-template-columns: 1fr;
        }

        .med-actions-cell {
            justify-content: flex-start;
        }

        html.dark body .med-table tbody tr,
        body.dark-mode .med-table tbody tr,
        body.theme-dark .med-table tbody tr {
            background: #11273d;
            border-color: #26435d;
        }
    }

    @media (max-width: 767px) {
        .med-page {
            padding-left: 0;
            padding-right: 0;
        }

        .med-kpi-grid,
        .med-filter-form {
            grid-template-columns: 1fr;
        }

        .med-hero-actions,
        .med-filter-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .med-title-row {
            flex-wrap: wrap;
        }

        .med-title-content {
            width: 100%;
            margin-left: 0;
            justify-content: flex-start;
        }

        .med-btn,
        .med-filter-btn {
            width: 100%;
        }

        .med-table-head,
        .med-table-footer {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
@php($displayMode = request('display', 'table'))
<div class="container-fluid med-page med-mode-{{ $displayMode }}">
    <div class="med-shell">
        <section class="med-hero">
            <div class="med-hero-grid">
                <div>
                    <div class="med-title-row">
                        <span class="med-title-icon">
                            <i class="fas fa-user-doctor"></i>
                        </span>
                        <span class="med-eyebrow">Equipe medicale</span>
                        <div class="med-title-content">
                            <div class="med-title-copy is-compact">
                                <h1 class="med-title">Gestion des Medecins</h1>
                                <p class="med-subtitle">Structurez les profils medicaux, les contacts et les statuts avec une interface plus lisible, premium et coherente avec le reste du produit.</p>
                            </div>

                            <div class="med-hero-actions">
                                <a href="{{ route('medecins.export', request()->query()) }}" class="med-btn secondary">
                                    <i class="fas fa-file-export"></i>
                                    Exporter
                                </a>
                                <a href="{{ route('medecins.create') }}" class="med-btn primary">
                                    <i class="fas fa-user-plus"></i>
                                    Nouveau medecin
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="med-kpi-grid">
            <article class="med-kpi">
                <div class="med-kpi-top">
                    <div>
                        <p class="med-kpi-label">Medecins actifs</p>
                        <p class="med-kpi-value">{{ $stats['actifs'] }}</p>
                    </div>
                    <span class="med-kpi-icon active"><i class="fas fa-user-md"></i></span>
                </div>
                <p class="med-kpi-copy">Praticiens disponibles et visibles dans la liste courante.</p>
            </article>

            <article class="med-kpi">
                <div class="med-kpi-top">
                    <div>
                        <p class="med-kpi-label">Specialites</p>
                        <p class="med-kpi-value">{{ $stats['specialites'] }}</p>
                    </div>
                    <span class="med-kpi-icon specialty"><i class="fas fa-stethoscope"></i></span>
                </div>
                <p class="med-kpi-copy">Diversite des expertises medicales presentes dans le perimetre filtre.</p>
            </article>

            <article class="med-kpi">
                <div class="med-kpi-top">
                    <div>
                        <p class="med-kpi-label">Rendez-vous</p>
                        <p class="med-kpi-value">{{ $stats['rendezvous'] }}</p>
                    </div>
                    <span class="med-kpi-icon rdv"><i class="fas fa-calendar-check"></i></span>
                </div>
                <p class="med-kpi-copy">Charge de planning rattachee aux medecins visibles dans la liste.</p>
            </article>

            <article class="med-kpi">
                <div class="med-kpi-top">
                    <div>
                        <p class="med-kpi-label">Inactifs</p>
                        <p class="med-kpi-value">{{ $stats['inactifs'] }}</p>
                    </div>
                    <span class="med-kpi-icon inactive"><i class="fas fa-user-clock"></i></span>
                </div>
                <p class="med-kpi-copy">Profils indisponibles, en conge, retraite ou actuellement inactifs.</p>
            </article>
        </section>

        <section class="med-filter-card">
            <div class="med-filter-head">
                <div>
                    <p class="med-filter-kicker">Recherche et ciblage</p>
                    <h2 class="med-filter-title">Filtres medecins</h2>
                    <p class="med-filter-copy">Affinez la liste par texte libre, statut, specialite et pagination.</p>
                </div>

                @if($hasFilters)
                    <div class="med-active-filters">
                        @if(request('search'))
                            <span class="med-inline-tag"><i class="fas fa-search"></i>{{ request('search') }}</span>
                        @endif
                        @if($statusLabel)
                            <span class="med-inline-tag"><i class="fas fa-circle-info"></i>{{ $statusLabel }}</span>
                        @endif
                        @if($selectedSpecialite)
                            <span class="med-inline-tag"><i class="fas fa-stethoscope"></i>{{ $selectedSpecialite }}</span>
                        @endif
                        <span class="med-inline-tag"><i class="fas fa-stream"></i>{{ $currentPerPage }} / page</span>
                    </div>
                @endif
            </div>

            <form method="GET" action="{{ route('medecins.index') }}" class="med-filter-form">
                <input type="hidden" name="display" value="{{ $displayMode }}">
                <div class="med-field">
                    <label for="medSearch">Recherche</label>
                    <div class="med-search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="medSearch" name="search" class="med-search" value="{{ request('search') }}" placeholder="Nom, prenom, specialite, matricule...">
                    </div>
                </div>

                <div class="med-field">
                    <label for="medStatus">Statut</label>
                    <select id="medStatus" name="status" class="med-select">
                        <option value="">Tous les statuts</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ $selectedStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="med-field">
                    <label for="medSpecialite">Specialite</label>
                    <select id="medSpecialite" name="specialite" class="med-select">
                        <option value="">Toutes les specialites</option>
                        @foreach($specialites as $specialite)
                            <option value="{{ $specialite }}" {{ (string) $selectedSpecialite === (string) $specialite ? 'selected' : '' }}>{{ $specialite }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="med-field">
                    <label for="medPerPage">Pagination</label>
                    <select id="medPerPage" name="per_page" class="med-select">
                        @foreach([10, 25, 50, 100] as $option)
                            <option value="{{ $option }}" {{ $currentPerPage === $option ? 'selected' : '' }}>{{ $option }} / page</option>
                        @endforeach
                    </select>
                </div>

                <div class="med-filter-actions">
                    <button type="submit" class="med-filter-btn primary">
                        <i class="fas fa-filter"></i>
                        Appliquer
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('medecins.index', ['display' => $displayMode]) }}" class="med-filter-btn secondary">
                            <i class="fas fa-rotate-left"></i>
                            Reinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="med-table-card">
            <div class="med-table-head">
                <div>
                    <p class="med-table-kicker">Annuaire</p>
                    <h2 class="med-table-title">Liste des medecins</h2>
                    <p class="med-table-copy">Acces direct au profil, a l'edition, au planning et a la suppression.</p>
                </div>
                <div class="med-table-tools">
                    <div class="med-table-meta">
                        <span class="med-chip"><i class="fas fa-table"></i>{{ $medecins->count() }} affiche{{ $medecins->count() > 1 ? 's' : '' }}</span>
                        <span class="med-chip"><i class="fas fa-sort-alpha-down"></i>Tri alphabetique</span>
                    </div>
                    <div class="display-mode-switch" role="group" aria-label="Mode d affichage">
                        <a href="{{ request()->fullUrlWithQuery(['display' => 'table', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'table' ? 'active' : '' }}">Mode tableau</a>
                        <a href="{{ request()->fullUrlWithQuery(['display' => 'compact', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'compact' ? 'active' : '' }}">Mode compact</a>
                        <a href="{{ request()->fullUrlWithQuery(['display' => 'cards', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'cards' ? 'active' : '' }}">Mode cartes</a>
                    </div>
                </div>
            </div>

            @if($medecins->count() > 0)
                <div class="med-table-wrap">
                    <table class="med-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Medecin</th>
                                <th>Specialite</th>
                                <th>Contact</th>
                                <th>Numero ordre</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medecins as $medecin)
                                <tr>
                                    <td data-label="ID">
                                        <div class="med-matricule"><i class="fas fa-id-badge"></i>{{ $medecin->matricule ?? 'N/A' }}</div>
                                        <p class="med-submeta">Cree le {{ optional($medecin->created_at)->format('d/m/Y') ?? 'N/A' }}</p>
                                    </td>
                                    <td data-label="Medecin">
                                        <div class="med-profile-cell">
                                            <img src="{{ $medecin->avatar_url }}" alt="{{ $medecin->nom_complet }}" class="med-avatar">
                                            <div>
                                                <p class="med-name">{{ $medecin->nom_complet }}</p>
                                                <p class="med-submeta">ID #{{ $medecin->id }} @if($medecin->ville)&middot; {{ $medecin->ville }} @endif</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Specialite">
                                        <span class="med-specialty-pill"><i class="fas fa-stethoscope"></i>{{ $medecin->specialite ?: 'Generaliste' }}</span>
                                    </td>
                                    <td data-label="Telephone">
                                        <div class="med-contact-stack">
                                            @if($medecin->telephone)
                                                <div class="med-contact-line"><span class="med-contact-icon"><i class="fas fa-phone"></i></span><span>{{ $medecin->telephone }}</span></div>
                                            @endif
                                            @if($medecin->email)
                                                <div class="med-contact-line"><span class="med-contact-icon"><i class="fas fa-envelope"></i></span><span>{{ $medecin->email }}</span></div>
                                            @endif
                                            @if(!$medecin->telephone && !$medecin->email)
                                                <p class="med-submeta">Aucun contact renseigne.</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Numero ordre">
                                        <span class="med-order">{{ $medecin->numero_ordre ?: 'Non renseigne' }}</span>
                                    </td>
                                    <td data-label="Statut">
                                        <span class="med-status-pill {{ $medecin->status_key }}">
                                            <i class="fas fa-circle"></i>
                                            {{ $medecin->status_label }}
                                        </span>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="med-actions-cell justify-content-lg-end">
                                            <a href="{{ route('medecins.show', $medecin->id) }}" class="med-icon-btn view action-tone-view" title="Voir le profil">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('medecins.edit', $medecin->id) }}" class="med-icon-btn edit action-tone-edit" title="Modifier le profil">
                                                <i class="fas fa-pen-to-square"></i>
                                            </a>
                                            <a href="{{ route('rendezvous.create', ['medecin_id' => $medecin->id]) }}" class="med-icon-btn rdv" title="Planifier un rendez-vous">
                                                <i class="fas fa-calendar-plus"></i>
                                            </a>
                                            <form action="{{ route('medecins.destroy', $medecin->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce medecin ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="med-icon-btn delete action-tone-delete" title="Supprimer le profil">
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
            @else
                <div class="med-empty">
                    <div class="med-empty-icon"><i class="fas fa-user-doctor"></i></div>
                    <h3>Aucun medecin trouve</h3>
                    <p>Elargissez les filtres ou ajoutez un premier medecin pour constituer l'annuaire du cabinet.</p>
                    <div class="med-hero-actions justify-content-center mt-4">
                        <a href="{{ route('medecins.create') }}" class="med-btn primary">
                            <i class="fas fa-user-plus"></i>
                            Ajouter un medecin
                        </a>
                        @if($hasFilters)
                            <a href="{{ route('medecins.index') }}" class="med-btn secondary">
                                <i class="fas fa-rotate-left"></i>
                                Retirer les filtres
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <div class="med-table-footer">
                <p class="med-pagination-info">Affichage de {{ $medecins->firstItem() ?? 0 }} a {{ $medecins->lastItem() ?? 0 }} sur {{ $medecins->total() }} medecin{{ $medecins->total() > 1 ? 's' : '' }}.</p>
                @if($medecins->hasPages())
                    <div>{{ $medecins->links() }}</div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection
