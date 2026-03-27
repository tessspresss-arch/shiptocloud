@extends('layouts.app')

@section('title', 'Gestion des Consultations')
@section('topbar_subtitle', 'Suivi des consultations, filtres rapides et actions cliniques dans une interface harmonisee.')

@push('styles')
<style>
    :root {
        --consult-bg: linear-gradient(180deg, #f4f9fd 0%, #edf5fb 100%);
        --consult-surface: rgba(255, 255, 255, 0.86);
        --consult-card: #ffffff;
        --consult-border: #d8e5ef;
        --consult-border-strong: #c9d9e8;
        --consult-text: #17324c;
        --consult-muted: #68829a;
        --consult-primary: #1b79c9;
        --consult-primary-strong: #145d98;
        --consult-accent: #13a4b8;
        --consult-success: #16956f;
        --consult-warning: #c98212;
        --consult-danger: #d74d5d;
        --consult-shadow: 0 26px 48px -36px rgba(15, 40, 65, 0.38);
    }

    .consult-page {
        width: 100%;
        max-width: none;
        padding: 8px 8px 96px;
    }

    .consult-shell {
        display: grid;
        gap: 16px;
    }

    .consult-hero,
    .consult-filter-card,
    .consult-table-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--consult-border);
        border-radius: 24px;
        box-shadow: var(--consult-shadow);
    }

    .consult-hero {
        padding: 18px;
        background:
            radial-gradient(circle at top right, rgba(27, 121, 201, 0.16) 0%, rgba(27, 121, 201, 0) 32%),
            radial-gradient(circle at left top, rgba(19, 164, 184, 0.12) 0%, rgba(19, 164, 184, 0) 34%),
            var(--consult-bg);
    }

    .consult-filter-card,
    .consult-table-card {
        background: var(--consult-surface);
        backdrop-filter: blur(10px);
    }

    .consult-hero::before,
    .consult-filter-card::before,
    .consult-table-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .consult-hero > *,
    .consult-filter-card > *,
    .consult-table-card > * {
        position: relative;
        z-index: 1;
    }

    .consult-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 14px;
        align-items: stretch;
    }

    .consult-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(27, 121, 201, 0.16);
        background: rgba(255, 255, 255, 0.7);
        color: var(--consult-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .consult-title-row {
        display: flex;
        gap: 14px;
        align-items: center;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .consult-title-content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex: 1 1 0;
        flex-wrap: wrap;
        min-width: 0;
    }

    .consult-title-copy {
        flex: 1 1 420px;
        min-width: 0;
    }

    .consult-title-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.4rem;
        background: linear-gradient(135deg, var(--consult-primary) 0%, var(--consult-primary-strong) 100%);
        box-shadow: 0 18px 28px -20px rgba(27, 121, 201, 0.58);
        flex-shrink: 0;
    }

    .consult-title {
        margin: 0;
        color: var(--consult-text);
        font-size: clamp(1.6rem, 2.6vw, 2.3rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .consult-subtitle {
        margin: 8px 0 0;
        max-width: 70ch;
        color: var(--consult-muted);
        font-size: .98rem;
        line-height: 1.64;
        font-weight: 600;
    }

    .consult-badge-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .consult-badge,
    .consult-chip,
    .consult-status-pill,
    .consult-inline-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        white-space: nowrap;
        font-weight: 700;
    }

    .consult-badge {
        background: linear-gradient(180deg, #eef5ff 0%, #e2ecfb 100%);
        color: var(--consult-primary-strong);
        border: 1px solid #d4e1f4;
        box-shadow: 0 10px 16px -18px rgba(37, 99, 235, 0.32);
    }

    .consult-chip,
    .consult-inline-tag {
        border: 1px solid #d7e4ef;
        background: #f6fafe;
        color: #57728c;
        font-size: .85rem;
    }

    .consult-chip i,
    .consult-inline-tag i {
        color: var(--consult-primary);
    }

    .consult-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .consult-hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        align-self: flex-start;
    }

    .consult-btn {
        min-height: 50px;
        border-radius: 16px;
        border: 1px solid transparent;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: .92rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .consult-btn:hover,
    .consult-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .consult-btn-secondary {
        border-color: #cfdeec;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        color: #48657f;
        box-shadow: 0 14px 22px -24px rgba(15, 23, 42, 0.42);
    }

    .consult-btn-secondary:hover,
    .consult-btn-secondary:focus {
        color: var(--consult-primary-strong);
        border-color: rgba(27, 121, 201, 0.3);
        background: linear-gradient(180deg, #ffffff 0%, #ebf4fb 100%);
    }

    .consult-btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #fff;
        box-shadow: 0 18px 28px -24px rgba(37, 99, 235, 0.5);
    }

    .consult-btn-primary:hover,
    .consult-btn-primary:focus {
        color: #fff;
        box-shadow: 0 24px 32px -24px rgba(37, 99, 235, 0.58);
    }

    .consult-filter-card {
        padding: 20px;
    }

    .consult-filter-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .consult-filter-title {
        margin: 0;
        color: var(--consult-text);
        font-size: 1.02rem;
        font-weight: 800;
    }

    .consult-filter-copy {
        margin: 4px 0 0;
        color: var(--consult-muted);
        font-size: .9rem;
        font-weight: 600;
    }

    .consult-active-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .consult-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) repeat(3, minmax(180px, .7fr)) auto;
        gap: 12px;
        align-items: end;
    }

    .consult-field {
        display: grid;
        gap: 8px;
    }

    .consult-field label {
        margin: 0;
        color: #6f88a0;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .consult-search,
    .consult-select {
        width: 100%;
        min-height: 52px;
        border-radius: 16px;
        border: 1px solid var(--consult-border-strong);
        background: rgba(255, 255, 255, 0.94);
        color: var(--consult-text);
        font-size: .95rem;
        font-weight: 600;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .consult-search {
        padding: 0 16px 0 48px;
    }

    .consult-select {
        padding: 0 16px;
    }

    .consult-search:focus,
    .consult-select:focus {
        outline: none;
        border-color: rgba(27, 121, 201, 0.42);
        box-shadow: 0 0 0 4px rgba(27, 121, 201, 0.1);
    }

    .consult-search-wrap {
        position: relative;
    }

    .consult-search-wrap i {
        position: absolute;
        top: 50%;
        left: 18px;
        transform: translateY(-50%);
        color: #8ca3ba;
    }

    .consult-filter-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
    }

    .consult-filter-btn {
        min-height: 52px;
        border-radius: 16px;
        border: 1px solid transparent;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        font-size: .92rem;
        font-weight: 800;
        white-space: nowrap;
        text-decoration: none;
    }

    .consult-filter-btn.primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #fff;
        box-shadow: 0 18px 28px -24px rgba(37, 99, 235, 0.5);
    }

    .consult-filter-btn.primary:hover,
    .consult-filter-btn.primary:focus {
        color: #fff;
        text-decoration: none;
    }

    .consult-filter-btn.secondary {
        border-color: #d1dfec;
        background: #f4f8fc;
        color: #4c6882;
    }

    .consult-filter-btn.secondary:hover,
    .consult-filter-btn.secondary:focus {
        color: var(--consult-primary-strong);
        text-decoration: none;
    }

    .consult-table-card {
        overflow: hidden;
    }

    .consult-table-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        padding: 20px 22px 16px;
        flex-wrap: wrap;
        border-bottom: 1px solid #dbe7f1;
    }

    .consult-table-title {
        margin: 0;
        color: var(--consult-text);
        font-size: 1.04rem;
        font-weight: 800;
    }

    .consult-table-copy {
        margin: 4px 0 0;
        color: var(--consult-muted);
        font-size: .9rem;
        font-weight: 600;
    }

    .consult-table-meta {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
    }

    .consult-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .consult-table {
        width: 100%;
        min-width: 940px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .consult-table thead th {
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

    .consult-table tbody tr {
        transition: background .2s ease;
    }

    .consult-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(246, 251, 255, 0.96) 0%, rgba(239, 246, 252, 0.88) 100%);
    }

    .consult-table tbody td {
        padding: 18px 22px;
        border-bottom: 1px solid #e4edf5;
        vertical-align: top;
        color: var(--consult-text);
        font-size: .94rem;
    }

    .consult-id {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .86rem;
        font-weight: 800;
        color: var(--consult-primary-strong);
    }

    .consult-patient-name,
    .consult-medecin-name {
        margin: 0;
        color: var(--consult-text);
        font-size: .97rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .consult-profile-cell {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .consult-avatar {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        object-fit: cover;
        flex: 0 0 42px;
        border: 1px solid #d9e5f1;
        background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
        box-shadow: 0 12px 20px -20px rgba(37, 99, 235, 0.42);
    }

    .consult-profile-copy {
        min-width: 0;
        display: grid;
        gap: 4px;
    }

    .consult-meta,
    .consult-date-subtext {
        margin: 6px 0 0;
        color: var(--consult-muted);
        font-size: .84rem;
        line-height: 1.5;
        font-weight: 600;
    }

    .consult-diagnostic {
        margin: 0;
        max-width: 320px;
        color: #3f5c77;
        font-size: .9rem;
        line-height: 1.58;
        font-weight: 600;
    }

    .consult-diagnostic.empty {
        color: #8aa0b7;
        font-style: italic;
    }

    .consult-status-pill {
        border: 1px solid transparent;
        font-size: .82rem;
    }

    .consult-status-pill.completed {
        background: rgba(22, 149, 111, 0.12);
        color: #0d6c50;
        border-color: rgba(22, 149, 111, 0.16);
    }

    .consult-status-pill.pending {
        background: rgba(201, 130, 18, 0.13);
        color: #8c5b09;
        border-color: rgba(201, 130, 18, 0.18);
    }

    .consult-status-pill.scheduled {
        background: rgba(27, 121, 201, 0.12);
        color: #0f5a96;
        border-color: rgba(27, 121, 201, 0.14);
    }

    .consult-status-pill.cancelled {
        background: rgba(215, 77, 93, 0.12);
        color: #a33b48;
        border-color: rgba(215, 77, 93, 0.16);
    }

    .consult-actions-cell {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .consult-icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        border: 1px solid #d4e2ee;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fc 100%);
        color: #5b7691;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: transform .2s ease, border-color .2s ease, color .2s ease, background .2s ease;
    }

    .consult-icon-btn:hover,
    .consult-icon-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
        background: linear-gradient(180deg, #ffffff 0%, #eef5ff 100%);
    }

    .consult-icon-btn.view:hover,
    .consult-icon-btn.view:focus {
        color: var(--consult-success);
        border-color: rgba(22, 149, 111, 0.24);
    }

    .consult-icon-btn.edit:hover,
    .consult-icon-btn.edit:focus {
        color: var(--consult-warning);
        border-color: rgba(201, 130, 18, 0.24);
    }

    .consult-icon-btn.ordonnance:hover,
    .consult-icon-btn.ordonnance:focus {
        color: var(--consult-primary);
        border-color: rgba(27, 121, 201, 0.22);
    }

    .consult-icon-btn.delete:hover,
    .consult-icon-btn.delete:focus {
        color: var(--consult-danger);
        border-color: rgba(215, 77, 93, 0.22);
    }

    .consult-empty {
        padding: 42px 20px 46px;
        text-align: center;
    }

    .consult-empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.65rem;
        color: var(--consult-primary);
        background: rgba(27, 121, 201, 0.12);
        box-shadow: inset 0 0 0 1px rgba(27, 121, 201, 0.08);
    }

    .consult-empty h3 {
        margin: 0;
        color: var(--consult-text);
        font-size: 1.2rem;
        font-weight: 800;
    }

    .consult-empty p {
        margin: 10px auto 0;
        max-width: 520px;
        color: var(--consult-muted);
        font-size: .96rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .consult-table-footer {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
        padding: 18px 22px 22px;
        border-top: 1px solid #dbe7f1;
        background: rgba(245, 249, 252, 0.78);
    }

    .consult-pagination-info {
        color: var(--consult-muted);
        font-size: .9rem;
        font-weight: 600;
    }

    .consult-table-footer .pagination {
        margin-bottom: 0;
        flex-wrap: wrap;
        gap: 8px;
    }

    .consult-table-footer .page-link {
        border-radius: 12px;
        border-color: #d0deeb;
        color: var(--consult-primary-strong);
        font-weight: 700;
    }

    .consult-table-footer .page-item.active .page-link {
        background: linear-gradient(135deg, var(--consult-primary) 0%, var(--consult-primary-strong) 100%);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 14px 22px -20px rgba(27, 121, 201, 0.92);
    }

    html.dark body .consult-hero,
    body.dark-mode .consult-hero {
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.12) 0%, rgba(56, 189, 248, 0) 36%),
            radial-gradient(circle at left top, rgba(15, 118, 110, 0.18) 0%, rgba(15, 118, 110, 0) 34%),
            linear-gradient(180deg, #0f2136 0%, #10263d 100%);
        border-color: #294661;
    }

    html.dark body .consult-filter-card,
    html.dark body .consult-table-card,
    body.dark-mode .consult-filter-card,
    body.dark-mode .consult-table-card {
        background: rgba(12, 27, 43, 0.92);
        border-color: #294661;
        box-shadow: 0 26px 48px -36px rgba(0, 0, 0, 0.62);
    }

    html.dark body .consult-title,
    html.dark body .consult-filter-title,
    html.dark body .consult-table-title,
    html.dark body .consult-patient-name,
    html.dark body .consult-medecin-name,
    html.dark body .consult-empty h3,
    body.dark-mode .consult-title,
    body.dark-mode .consult-filter-title,
    body.dark-mode .consult-table-title,
    body.dark-mode .consult-patient-name,
    body.dark-mode .consult-medecin-name,
    body.dark-mode .consult-empty h3 {
        color: #e7f0fb;
    }

    html.dark body .consult-avatar,
    body.dark-mode .consult-avatar {
        border-color: #355273;
        background: linear-gradient(180deg, #1b3654 0%, #173149 100%);
    }

    html.dark body .consult-subtitle,
    html.dark body .consult-chip,
    html.dark body .consult-inline-tag,
    html.dark body .consult-filter-copy,
    html.dark body .consult-meta,
    html.dark body .consult-date-subtext,
    html.dark body .consult-diagnostic,
    html.dark body .consult-empty p,
    html.dark body .consult-pagination-info,
    body.dark-mode .consult-subtitle,
    body.dark-mode .consult-chip,
    body.dark-mode .consult-inline-tag,
    body.dark-mode .consult-filter-copy,
    body.dark-mode .consult-meta,
    body.dark-mode .consult-date-subtext,
    body.dark-mode .consult-diagnostic,
    body.dark-mode .consult-empty p,
    body.dark-mode .consult-pagination-info {
        color: #9db4cb;
    }

    html.dark body .consult-chip,
    html.dark body .consult-inline-tag,
    body.dark-mode .consult-chip,
    body.dark-mode .consult-inline-tag {
        background: #102337;
        border-color: #2c4a65;
        color: #b5c8db;
    }

    html.dark body .consult-chip i,
    html.dark body .consult-inline-tag i,
    body.dark-mode .consult-chip i,
    body.dark-mode .consult-inline-tag i {
        color: #69c1ff;
    }

    html.dark body .consult-search,
    html.dark body .consult-select,
    body.dark-mode .consult-search,
    body.dark-mode .consult-select {
        background: #0b1b2d;
        border-color: #31536f;
        color: #e7f0fb;
    }

    html.dark body .consult-search::placeholder,
    body.dark-mode .consult-search::placeholder {
        color: #8da7c1;
    }

    html.dark body .consult-search-wrap i,
    body.dark-mode .consult-search-wrap i {
        color: #88a4be;
    }

    html.dark body .consult-btn-secondary,
    html.dark body .consult-filter-btn.secondary,
    html.dark body .consult-icon-btn,
    body.dark-mode .consult-btn-secondary,
    body.dark-mode .consult-filter-btn.secondary,
    body.dark-mode .consult-icon-btn {
        background: #12283d;
        border-color: #31536f;
        color: #bdd0e3;
    }

    html.dark body .consult-table-head,
    html.dark body .consult-table thead th,
    html.dark body .consult-table-footer,
    body.dark-mode .consult-table-head,
    body.dark-mode .consult-table thead th,
    body.dark-mode .consult-table-footer {
        background: #102337;
        border-color: #284660;
    }

    html.dark body .consult-table thead th,
    body.dark-mode .consult-table thead th {
        color: #a8bfd7;
    }

    html.dark body .consult-table tbody tr:hover,
    body.dark-mode .consult-table tbody tr:hover {
        background: rgba(20, 43, 66, 0.86);
    }

    html.dark body .consult-table tbody td,
    body.dark-mode .consult-table tbody td {
        color: #d7e8ff;
        border-bottom-color: #203c57;
    }

    html.dark body .consult-id,
    body.dark-mode .consult-id {
        color: #8ec6ff;
    }

    html.dark body .consult-diagnostic.empty,
    body.dark-mode .consult-diagnostic.empty {
        color: #84a0bb;
    }

    html.dark body .consult-table-footer .page-link,
    body.dark-mode .consult-table-footer .page-link {
        background: #12283d;
        border-color: #31536f;
        color: #c9def1;
    }

    html.dark body .consult-empty-icon,
    body.dark-mode .consult-empty-icon {
        background: rgba(56, 189, 248, 0.12);
        color: #7fd1ff;
    }

    @media (max-width: 1200px) {
        .consult-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .consult-title-content {
            gap: 14px;
        }

        .consult-filter-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 992px) {
        .consult-page {
            padding-bottom: 72px;
        }

        .consult-hero-grid {
            grid-template-columns: 1fr;
        }

        .consult-hero,
        .consult-filter-card,
        .consult-table-card {
            border-radius: 20px;
        }

        .consult-title-row {
            align-items: flex-start;
        }

        .consult-title-content {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
        }

        .consult-hero-actions {
            width: 100%;
            align-self: stretch;
            justify-content: flex-start;
        }

        .consult-hero-actions .consult-btn {
            flex: 1 1 220px;
        }

        .consult-filter-card {
            padding: 18px;
        }

        .consult-table-head {
            align-items: flex-start;
        }

        .consult-table-meta {
            width: 100%;
            justify-content: flex-start;
        }

        .consult-table {
            min-width: 0;
        }

        .consult-table thead {
            display: none;
        }

        .consult-table,
        .consult-table tbody,
        .consult-table tr,
        .consult-table td {
            display: block;
            width: 100%;
        }

        .consult-table tbody {
            padding: 16px;
            display: grid;
            gap: 14px;
        }

        .consult-table tbody tr {
            padding: 18px;
            border: 1px solid #dbe7f1;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 16px 28px -28px rgba(15, 40, 65, 0.3);
        }

        .consult-table tbody td {
            padding: 0;
            border: 0;
        }

        .consult-table tbody td + td {
            margin-top: 12px;
        }

        .consult-mobile-label {
            display: block;
            margin-bottom: 6px;
            color: #718aa3;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        html.dark body .consult-table tbody tr,
        body.dark-mode .consult-table tbody tr {
            background: #11273d;
            border-color: #26435d;
        }
    }

    @media (min-width: 993px) {
        .consult-mobile-label {
            display: none;
        }
    }

    @media (max-width: 767px) {
        .consult-page {
            padding-left: 0;
            padding-right: 0;
            padding-bottom: 40px;
        }

        .consult-shell {
            gap: 14px;
        }

        .consult-hero,
        .consult-filter-card,
        .consult-table-card {
            border-radius: 18px;
        }

        .consult-hero,
        .consult-filter-card {
            padding: 16px;
        }

        .consult-filter-form {
            grid-template-columns: 1fr;
        }

        .consult-actions,
        .consult-filter-actions,
        .consult-hero-actions,
        .consult-table-meta {
            flex-direction: column;
            align-items: stretch;
        }

        .consult-btn,
        .consult-filter-btn {
            width: 100%;
        }

        .consult-table-head,
        .consult-table-footer {
            padding: 16px;
        }

        .consult-title-row {
            gap: 12px;
            align-items: flex-start;
        }

        .consult-title-content {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .consult-title-icon {
            width: 50px;
            height: 50px;
            border-radius: 16px;
        }

        .consult-badge-row {
            gap: 8px;
        }

        .consult-badge,
        .consult-chip,
        .consult-inline-tag {
            width: 100%;
            justify-content: center;
        }

        .consult-table tbody {
            padding: 14px;
        }

        .consult-table tbody tr {
            padding: 16px;
            border-radius: 16px;
        }

        .consult-actions-cell {
            justify-content: flex-start;
        }

        .consult-table-footer {
            justify-content: flex-start;
        }

        .consult-table-footer nav {
            width: 100%;
        }

        .consult-table-footer .pagination {
            justify-content: flex-start !important;
        }
    }

    @media (max-width: 992px) {
        .consult-table-wrap {
            overflow: visible;
        }

        .consult-table td,
        .consult-profile-cell,
        .consult-profile-copy,
        .consult-patient-name,
        .consult-medecin-name,
        .consult-meta,
        .consult-date-subtext,
        .consult-diagnostic {
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .consult-profile-cell,
        .consult-profile-copy {
            display: grid;
            min-width: 0;
        }

        .consult-actions-cell {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(42px, 42px));
            justify-content: flex-start;
            gap: 8px;
        }
    }

    @media (max-width: 480px) {
        .consult-table tbody td {
            margin-top: 0;
            display: grid;
            gap: 6px;
        }

        .consult-table-meta {
            width: 100%;
        }

        .consult-table-meta .consult-chip {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 390px) {
        .consult-table tbody {
            padding: 12px;
            gap: 12px;
        }

        .consult-table tbody tr {
            padding: 14px;
            border-radius: 14px;
        }

        .consult-table tbody td + td {
            margin-top: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid consult-page">
    <div class="consult-shell">
        <section class="consult-hero">
            <div class="consult-hero-grid">
                <div>
                    <span class="consult-eyebrow">Parcours clinique</span>
                    <div class="consult-title-row">
                        <span class="consult-title-icon">
                            <i class="fas fa-stethoscope"></i>
                        </span>
                        <div class="consult-title-content">
                            <div class="consult-title-copy">
                                <h1 class="consult-title">Gestion des Consultations</h1>
                                <p class="consult-subtitle">Pilotez les consultations du cabinet avec une lecture plus claire des rendez-vous, du diagnostic et des actions medicales, sans perdre les filtres ni les operations existantes.</p>
                            </div>

                            <div class="consult-hero-actions">
                                <a href="{{ route('consultations.export', request()->query()) }}" class="consult-btn consult-btn-secondary">
                                    <i class="fas fa-file-export"></i>
                                    Exporter
                                </a>
                                <a href="{{ route('consultations.create') }}" class="consult-btn consult-btn-primary">
                                    <i class="fas fa-plus"></i>
                                    Nouvelle consultation
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="consult-badge-row">
                        <span class="consult-badge">
                            <i class="fas fa-layer-group"></i>
                            {{ $totalConsultations }} resultat{{ $totalConsultations > 1 ? 's' : '' }}
                        </span>
                        <span class="consult-chip">
                            <i class="fas fa-user-md"></i>
                            {{ $activeMedecins }} medecin{{ $activeMedecins > 1 ? 's' : '' }} actif{{ $activeMedecins > 1 ? 's' : '' }}
                        </span>
                        <span class="consult-chip">
                            <i class="fas fa-calendar-week"></i>
                            {{ $consultations->total() }} consultation{{ $consultations->total() > 1 ? 's' : '' }} au total
                        </span>
                    </div>
                </div>

            </div>
        </section>

        <section class="consult-filter-card">
            <form method="GET" action="{{ route('consultations.index') }}" class="consult-filter-form">
                <div class="consult-field">
                    <label for="consultationSearch">Recherche</label>
                    <div class="consult-search-wrap">
                        <i class="fas fa-search"></i>
                        <input
                            type="text"
                            id="consultationSearch"
                            class="consult-search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Patient, medecin ou diagnostic..."
                        >
                    </div>
                </div>

                <div class="consult-field">
                    <label for="consultationPeriod">Periode</label>
                    <select id="consultationPeriod" class="consult-select" name="period">
                        <option value="">Toutes les periodes</option>
                        <option value="today" {{ $selectedPeriod === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="week" {{ $selectedPeriod === 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ $selectedPeriod === 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="year" {{ $selectedPeriod === 'year' ? 'selected' : '' }}>Cette annee</option>
                    </select>
                </div>

                <div class="consult-field">
                    <label for="consultationMedecin">Medecin</label>
                    <select id="consultationMedecin" class="consult-select" name="medecin">
                        <option value="">Tous les medecins</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ (string) $selectedMedecin === (string) $medecin->id ? 'selected' : '' }}>
                                {{ trim(($medecin->prenom ?? '') . ' ' . ($medecin->nom ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="consult-field">
                    <label for="consultationPerPage">Pagination</label>
                    <select id="consultationPerPage" class="consult-select" name="per_page">
                        @foreach([10, 25, 50, 100] as $option)
                            <option value="{{ $option }}" {{ $currentPerPage === $option ? 'selected' : '' }}>{{ $option }} / page</option>
                        @endforeach
                    </select>
                </div>

                <div class="consult-filter-actions">
                    <button type="submit" class="consult-filter-btn primary">
                        <i class="fas fa-filter"></i>
                        Appliquer
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('consultations.index') }}" class="consult-filter-btn secondary">
                            <i class="fas fa-rotate-left"></i>
                            Reinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="consult-table-card">
            <div class="consult-table-head">
                <div>
                    <h2 class="consult-table-title">Liste des consultations</h2>
                    <p class="consult-table-copy">Acces direct au dossier de consultation, a la modification et a la creation d'ordonnance.</p>
                </div>
                <div class="consult-table-meta">
                    <span class="consult-chip"><i class="fas fa-table"></i>{{ $consultations->count() }} affichee{{ $consultations->count() > 1 ? 's' : '' }}</span>
                    <span class="consult-chip"><i class="fas fa-sort-amount-down"></i>Tri par date descendante</span>
                </div>
            </div>

            @if($consultations->count() > 0)
                <div class="consult-table-wrap">
                    <table class="consult-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Patient</th>
                                <th>Medecin</th>
                                <th>Date</th>
                                <th>Diagnostic</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consultations as $consultation)
                                <tr>
                                    <td>
                                        <span class="consult-mobile-label">Reference</span>
                                        <div class="consult-id">
                                            <i class="fas fa-hashtag"></i>
                                            {{ $consultation->id }}
                                        </div>
                                        <p class="consult-meta">Creee le {{ optional($consultation->created_at)->format('d/m/Y') ?? 'N/A' }}</p>
                                    </td>
                                    <td>
                                        <span class="consult-mobile-label">Patient</span>
                                        <div class="consult-profile-cell">
                                            @if($consultation->patient)
                                                <img src="{{ $consultation->patient->avatar_url }}" alt="{{ $consultation->patient->nom_complet }}" class="consult-avatar">
                                            @endif
                                            <div class="consult-profile-copy">
                                                <p class="consult-patient-name">{{ $consultation->patient ? strtoupper($consultation->patient->nom) . ' ' . ($consultation->patient->prenom ?? '') : 'Patient inconnu' }}</p>
                                                <p class="consult-meta">
                                                    @if($consultation->patient)
                                                        ID patient {{ $consultation->patient->id }}
                                                    @else
                                                        Fiche patient indisponible
                                                    @endif
                                                    @if($consultation->rendezvous)
                                                        &middot; Rendez-vous lie
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="consult-mobile-label">Medecin</span>
                                        <p class="consult-medecin-name">{{ $consultation->medecin ? trim(($consultation->medecin->prenom ?? '') . ' ' . ($consultation->medecin->nom ?? '')) : 'Medecin inconnu' }}</p>
                                        <p class="consult-meta">Suivi medical associe a cette consultation.</p>
                                    </td>
                                    <td>
                                        <span class="consult-mobile-label">Date</span>
                                        @if($consultation->display_date)
                                            <p class="consult-patient-name">{{ $consultation->display_date->format('d/m/Y') }}</p>
                                            <p class="consult-date-subtext">{{ $consultation->display_date->format('H:i') }} @if($consultation->display_date->isToday())&middot; Aujourd'hui @elseif($consultation->display_date->isFuture())&middot; A venir @else&middot; Passee @endif</p>
                                        @else
                                            <p class="consult-diagnostic empty">Date non definie</p>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="consult-mobile-label">Diagnostic</span>
                                        <p class="consult-diagnostic {{ empty($consultation->diagnostic) ? 'empty' : '' }}">
                                            {{ $consultation->diagnostic ? Str::limit($consultation->diagnostic, 120) : 'Aucun diagnostic renseigne pour le moment.' }}
                                        </p>
                                    </td>
                                    <td>
                                        <span class="consult-mobile-label">Statut</span>
                                        <span class="consult-status-pill {{ $consultation->status_class ?? 'scheduled' }}">
                                            <i class="fas fa-circle"></i>
                                            {{ $consultation->status_label ?? 'Planifiee' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="consult-mobile-label">Actions</span>
                                        <div class="consult-actions-cell justify-content-lg-end">
                                            <a href="{{ route('consultations.show', $consultation->id) }}" class="consult-icon-btn view action-tone-view" title="Voir la consultation">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('consultations.edit', $consultation->id) }}" class="consult-icon-btn edit action-tone-edit" title="Modifier la consultation">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('ordonnances.create', ['consultation_id' => $consultation->id]) }}" class="consult-icon-btn ordonnance" title="Creer une ordonnance">
                                                <i class="fas fa-prescription"></i>
                                            </a>
                                            <form action="{{ route('consultations.destroy', $consultation->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette consultation ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="consult-icon-btn delete action-tone-delete" title="Supprimer la consultation">
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
                <div class="consult-empty">
                    <div class="consult-empty-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3>Aucune consultation trouvee</h3>
                    <p>Elargissez les filtres ou creez une nouvelle consultation pour alimenter votre suivi clinique depuis cette vue.</p>
                    <div class="consult-actions justify-content-center mt-4">
                        <a href="{{ route('consultations.create') }}" class="consult-btn consult-btn-primary">
                            <i class="fas fa-plus"></i>
                            Nouvelle consultation
                        </a>
                        @if($hasFilters)
                            <a href="{{ route('consultations.index') }}" class="consult-btn consult-btn-secondary">
                                <i class="fas fa-rotate-left"></i>
                                Retirer les filtres
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <div class="consult-table-footer">
                <p class="consult-pagination-info mb-0">
                    Affichage de {{ $consultations->firstItem() ?? 0 }} &agrave; {{ $consultations->lastItem() ?? 0 }}
                    sur {{ $consultations->total() }} consultation{{ $consultations->total() > 1 ? 's' : '' }}
                </p>
                @if($consultations->hasPages())
                    <x-pagination :paginator="$consultations" />
                @endif
            </div>
        </section>
    </div>
</div>
@endsection


