@extends('layouts.app')

@section('title', 'Gestion des Ordonnances')

@push('styles')
<style>
    :root {
        --ord-bg: linear-gradient(180deg, #f4f9ff 0%, #eef5ff 100%);
        --ord-surface: rgba(255, 255, 255, 0.82);
        --ord-card: #ffffff;
        --ord-border: #d8e4f0;
        --ord-border-strong: #cad9eb;
        --ord-text: #17324c;
        --ord-muted: #64809b;
        --ord-primary: #1f78c8;
        --ord-primary-strong: #145d99;
        --ord-accent: #0ea5e9;
        --ord-success: #0f9f77;
        --ord-warning: #d97706;
        --ord-danger: #dc2626;
        --ord-shadow: 0 24px 48px -38px rgba(15, 40, 65, 0.38);
    }

    .ord-page {
        width: 100%;
        max-width: none;
        padding: 8px 8px 92px;
    }

    .ord-shell {
        display: grid;
        gap: 18px;
    }

    .ord-hero,
    .ord-card,
    .ord-kpi,
    .ord-table-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--ord-border);
        border-radius: 24px;
        box-shadow: var(--ord-shadow);
    }

    .ord-hero {
        padding: 20px;
        background:
            radial-gradient(circle at top right, rgba(31, 120, 200, 0.18) 0%, rgba(31, 120, 200, 0) 34%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.14) 0%, rgba(14, 165, 233, 0) 32%),
            var(--ord-bg);
    }

    .ord-card,
    .ord-kpi,
    .ord-table-card {
        background: var(--ord-surface);
    }

    .ord-hero::before,
    .ord-card::before,
    .ord-kpi::before,
    .ord-table-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .ord-hero > *,
    .ord-card > *,
    .ord-kpi > *,
    .ord-table-card > * {
        position: relative;
        z-index: 1;
    }

    .ord-hero-top {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: end;
    }

    .ord-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(31, 120, 200, 0.16);
        background: rgba(255, 255, 255, 0.64);
        color: var(--ord-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .ord-title-row {
        display: flex;
        gap: 14px;
        align-items: center;
        margin-top: 0;
        flex-wrap: wrap;
    }

    .ord-title-row .ord-eyebrow {
        flex-shrink: 0;
    }

    .ord-title-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.35rem;
        background: linear-gradient(135deg, var(--ord-primary) 0%, var(--ord-primary-strong) 100%);
        box-shadow: 0 18px 28px -20px rgba(31, 120, 200, 0.58);
        flex-shrink: 0;
    }

    .ord-title-block {
        min-width: 0;
    }

    .ord-title-block.is-compact {
        display: none;
    }

    .ord-title {
        margin: 0;
        font-size: clamp(1.5rem, 2.5vw, 2.2rem);
        font-weight: 800;
        line-height: 1.06;
        letter-spacing: -0.04em;
        color: var(--ord-text);
    }

    .ord-subtitle {
        margin: 8px 0 0;
        max-width: 70ch;
        color: var(--ord-muted);
        font-size: .98rem;
        line-height: 1.62;
        font-weight: 600;
    }

    .ord-search-box {
        position: relative;
    }

    .ord-search-box i {
        position: absolute;
        top: 50%;
        left: 16px;
        transform: translateY(-50%);
        color: #8aa1bb;
    }

    .ord-search-input,
    .ord-filter-select {
        width: 100%;
        min-height: 52px;
        border-radius: 16px;
        border: 1px solid var(--ord-border-strong);
        background: rgba(255, 255, 255, 0.92);
        color: var(--ord-text);
        font-size: .95rem;
        font-weight: 600;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .ord-search-input {
        padding: 0 16px 0 46px;
    }

    .ord-filter-select {
        padding: 0 16px;
    }

    .ord-search-input:focus,
    .ord-filter-select:focus {
        outline: none;
        border-color: rgba(31, 120, 200, 0.42);
        box-shadow: 0 0 0 4px rgba(31, 120, 200, 0.1);
    }

    .ord-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .ord-btn {
        min-height: 50px;
        border-radius: 16px;
        border: 1px solid transparent;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        white-space: nowrap;
        font-size: .92rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .ord-btn:hover,
    .ord-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .ord-btn-icon {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(31, 120, 200, 0.12);
    }

    .ord-btn-secondary {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        color: #3b5976;
        box-shadow: 0 14px 22px -24px rgba(15, 23, 42, 0.42);
    }

    .ord-btn-secondary:hover,
    .ord-btn-secondary:focus {
        color: var(--ord-primary-strong);
        border-color: rgba(31, 120, 200, 0.3);
        background: linear-gradient(180deg, #ffffff 0%, #ebf4fb 100%);
    }

    .ord-btn-primary {
        background: linear-gradient(135deg, var(--ord-success) 0%, #0b8c6a 100%);
        color: #fff;
        box-shadow: 0 18px 28px -22px rgba(15, 159, 119, 0.52);
    }

    .ord-btn-primary .ord-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: inherit;
    }

    .ord-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        border-radius: 999px;
        padding: 0 14px;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .01em;
        white-space: nowrap;
    }

    .ord-chip {
        border: 1px solid #d7e3f1;
        background: #f8fbff;
        color: #55708d;
    }

    .ord-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .ord-kpi {
        padding: 18px;
        display: grid;
        gap: 16px;
    }

    .ord-kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .ord-kpi-label {
        margin: 0;
        color: var(--ord-muted);
        font-size: .82rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .ord-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .ord-kpi-icon.primary {
        background: rgba(31, 120, 200, 0.14);
        color: var(--ord-primary);
    }

    .ord-kpi-icon.success {
        background: rgba(15, 159, 119, 0.14);
        color: var(--ord-success);
    }

    .ord-kpi-icon.warning {
        background: rgba(217, 119, 6, 0.14);
        color: var(--ord-warning);
    }

    .ord-kpi-icon.danger {
        background: rgba(220, 38, 38, 0.14);
        color: var(--ord-danger);
    }

    .ord-kpi-value {
        margin: 0;
        font-size: clamp(1.9rem, 3vw, 2.5rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.05em;
        color: var(--ord-text);
    }

    .ord-kpi-note {
        margin: 0;
        color: var(--ord-muted);
        font-size: .9rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .ord-card {
        padding: 18px;
    }

    .ord-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
    }

    .ord-card-title {
        margin: 0;
        font-size: 1.08rem;
        font-weight: 800;
        color: var(--ord-text);
    }

    .ord-card-copy {
        margin: 6px 0 0;
        color: var(--ord-muted);
        font-size: .92rem;
        line-height: 1.56;
        font-weight: 600;
    }

    .ord-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(210px, .7fr) auto auto;
        gap: 12px;
        align-items: end;
    }

    .ord-filter-field {
        display: grid;
        gap: 8px;
    }

    .ord-filter-field label {
        color: #4f6983;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .ord-filter-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .ord-btn-filter {
        min-height: 52px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid transparent;
        font-size: .9rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--ord-primary) 0%, var(--ord-accent) 100%);
        color: #fff;
        box-shadow: 0 16px 24px -24px rgba(14, 165, 233, 0.9);
    }

    .ord-btn-filter:hover,
    .ord-btn-filter:focus {
        color: #fff;
        background: linear-gradient(135deg, var(--ord-primary-strong) 0%, #0a89c8 100%);
        text-decoration: none;
    }

    .ord-btn-reset {
        min-height: 52px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid var(--ord-border-strong);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #fff;
        color: #59738e;
        font-size: .9rem;
        font-weight: 800;
        text-decoration: none;
    }

    .ord-btn-reset:hover,
    .ord-btn-reset:focus {
        color: var(--ord-primary-strong);
        text-decoration: none;
        border-color: rgba(31, 120, 200, 0.3);
        background: #f7fbff;
    }

    .ord-table-card {
        background: rgba(255, 255, 255, 0.86);
    }

    .ord-table-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 18px 18px 0;
    }

    .ord-table-meta {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .ord-counter {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d5e1ef;
        background: #f6fafe;
        color: var(--ord-primary-strong);
        font-size: .8rem;
        font-weight: 800;
    }

    .ord-table-wrap {
        padding: 16px 18px 0;
        overflow-x: auto;
    }

    .ord-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 900px;
    }

    .ord-table thead th {
        padding: 0 16px 14px;
        border-bottom: 1px solid #dce6f1;
        color: #6a819a;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ord-table tbody tr {
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .ord-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(244, 249, 255, 0.9) 0%, rgba(238, 245, 255, 0.86) 100%);
    }

    .ord-table tbody td {
        padding: 18px 16px;
        border-bottom: 1px solid rgba(216, 228, 240, 0.78);
        vertical-align: middle;
        color: var(--ord-text);
    }

    .ord-ref {
        display: grid;
        gap: 8px;
    }

    .ord-id {
        font-size: .78rem;
        font-weight: 800;
        color: #7a91aa;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .ord-num {
        display: inline-flex;
        align-items: center;
        max-width: fit-content;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: #edf5ff;
        color: var(--ord-primary-strong);
        font-size: .84rem;
        font-weight: 800;
    }

    .ord-person {
        display: grid;
        gap: 6px;
    }

    .ord-person-name {
        color: var(--ord-text);
        font-size: .95rem;
        font-weight: 800;
        line-height: 1.4;
    }

    .ord-person-meta {
        color: var(--ord-muted);
        font-size: .85rem;
        font-weight: 600;
    }

    .ord-date {
        display: grid;
        gap: 6px;
    }

    .ord-date-main {
        color: var(--ord-text);
        font-size: .92rem;
        font-weight: 800;
    }

    .ord-date-note {
        color: var(--ord-muted);
        font-size: .82rem;
        font-weight: 700;
    }

    .ord-date-note.warning {
        color: var(--ord-warning);
    }

    .ord-date-note.danger {
        color: var(--ord-danger);
    }

    .ord-date-note.success {
        color: var(--ord-success);
    }

    .ord-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .ord-status::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
        opacity: .75;
    }

    .ord-status-active {
        background: rgba(15, 159, 119, 0.12);
        color: var(--ord-success);
    }

    .ord-status-expiree {
        background: rgba(217, 119, 6, 0.14);
        color: var(--ord-warning);
    }

    .ord-status-annulee {
        background: rgba(220, 38, 38, 0.12);
        color: var(--ord-danger);
    }

    .ord-status-default {
        background: rgba(31, 120, 200, 0.12);
        color: var(--ord-primary);
    }

    .ord-actions-cell {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .ord-row-btn {
        min-height: 38px;
        padding: 0 12px;
        border-radius: 12px;
        border: 1px solid #dae5f1;
        background: #fff;
        color: #57718d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: .82rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform .2s ease, border-color .2s ease, background .2s ease, color .2s ease;
    }

    .ord-row-btn:hover,
    .ord-row-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .ord-row-btn.view:hover,
    .ord-row-btn.view:focus {
        color: var(--ord-success);
        border-color: rgba(15, 159, 119, 0.28);
        background: rgba(15, 159, 119, 0.08);
    }

    .ord-row-btn.edit:hover,
    .ord-row-btn.edit:focus {
        color: var(--ord-warning);
        border-color: rgba(217, 119, 6, 0.28);
        background: rgba(217, 119, 6, 0.08);
    }

    .ord-row-btn.print:hover,
    .ord-row-btn.print:focus {
        color: var(--ord-primary);
        border-color: rgba(31, 120, 200, 0.28);
        background: rgba(31, 120, 200, 0.08);
    }

    .ord-row-btn.delete:hover,
    .ord-row-btn.delete:focus {
        color: var(--ord-danger);
        border-color: rgba(220, 38, 38, 0.28);
        background: rgba(220, 38, 38, 0.08);
    }

    .ord-empty {
        padding: 36px 20px 40px;
        text-align: center;
    }

    .ord-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 24px;
        margin: 0 auto 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(31, 120, 200, 0.14) 0%, rgba(14, 165, 233, 0.2) 100%);
        color: var(--ord-primary);
        font-size: 1.6rem;
    }

    .ord-empty h3 {
        margin: 0;
        color: var(--ord-text);
        font-size: 1.2rem;
        font-weight: 800;
    }

    .ord-empty p {
        margin: 10px auto 0;
        max-width: 52ch;
        color: var(--ord-muted);
        font-size: .95rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .ord-table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 18px;
        border-top: 1px solid rgba(216, 228, 240, 0.85);
        background: rgba(247, 251, 255, 0.88);
    }

    .ord-pagination-info {
        color: var(--ord-muted);
        font-size: .9rem;
        font-weight: 600;
    }

    body.dark-mode {
        --ord-text: #ebf4ff;
        --ord-muted: #a9c4df;
        --ord-border: #294863;
        --ord-border-strong: #355273;
    }

    body.dark-mode .ord-hero {
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.16) 0%, rgba(56, 189, 248, 0) 34%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 32%),
            linear-gradient(180deg, #11253b 0%, #0e2033 100%);
        border-color: #2a4660;
    }

    body.dark-mode .ord-hero::before,
    body.dark-mode .ord-card::before,
    body.dark-mode .ord-kpi::before,
    body.dark-mode .ord-table-card::before {
        background: linear-gradient(180deg, rgba(8, 18, 30, 0.12) 0%, rgba(8, 18, 30, 0) 100%);
    }

    body.dark-mode .ord-card,
    body.dark-mode .ord-kpi,
    body.dark-mode .ord-table-card {
        background: linear-gradient(180deg, rgba(16, 33, 54, 0.94) 0%, rgba(13, 28, 46, 0.96) 100%);
        border-color: #294863;
        box-shadow: 0 20px 40px -32px rgba(0, 0, 0, 0.52);
    }

    body.dark-mode .ord-eyebrow,
    body.dark-mode .ord-counter,
    body.dark-mode .ord-chip,
    body.dark-mode .ord-num {
        border-color: #355879;
        background: linear-gradient(180deg, rgba(23, 48, 76, 0.92) 0%, rgba(18, 38, 60, 0.94) 100%);
        color: #cfe5ff;
    }

    body.dark-mode .ord-kpi-icon.primary {
        background: rgba(56, 189, 248, 0.14);
        color: #8fd8ff;
    }

    body.dark-mode .ord-kpi-icon.success {
        background: rgba(15, 159, 119, 0.16);
        color: #6ee7b7;
    }

    body.dark-mode .ord-kpi-icon.warning {
        background: rgba(217, 119, 6, 0.16);
        color: #fcd34d;
    }

    body.dark-mode .ord-kpi-icon.danger {
        background: rgba(220, 38, 38, 0.16);
        color: #fda4af;
    }

    body.dark-mode .ord-title,
    body.dark-mode .ord-kpi-value,
    body.dark-mode .ord-card-title,
    body.dark-mode .ord-person-name,
    body.dark-mode .ord-date-main,
    body.dark-mode .ord-table thead th,
    body.dark-mode .ord-table tbody td {
        color: #ebf4ff;
    }

    body.dark-mode .ord-subtitle,
    body.dark-mode .ord-kpi-note,
    body.dark-mode .ord-kpi-label,
    body.dark-mode .ord-card-copy,
    body.dark-mode .ord-filter-field label,
    body.dark-mode .ord-id,
    body.dark-mode .ord-person-meta,
    body.dark-mode .ord-date-note,
    body.dark-mode .ord-pagination-info {
        color: #9fbbd8;
    }

    body.dark-mode .ord-hero .ord-subtitle,
    body.dark-mode .ord-kpi .ord-kpi-note,
    body.dark-mode .ord-card .ord-card-copy,
    body.dark-mode .ord-table-card .ord-card-copy,
    body.dark-mode .ord-filter-form .ord-filter-field label,
    body.dark-mode .ord-table .ord-id,
    body.dark-mode .ord-table .ord-person-meta,
    body.dark-mode .ord-table .ord-date-note,
    body.dark-mode .ord-table-footer .ord-pagination-info {
        color: #a9c4df !important;
    }

    body.dark-mode .ord-search-input,
    body.dark-mode .ord-filter-select,
    body.dark-mode .ord-row-btn,
    body.dark-mode .ord-btn-reset {
        background: #102035;
        border-color: #355273;
        color: #e4efff;
    }

    body.dark-mode .ord-search-box i {
        color: #8ca8c9;
    }

    body.dark-mode .ord-btn-secondary {
        border-color: #355273;
        background: linear-gradient(180deg, #17304c 0%, #12253d 100%);
        color: #dce9f9;
    }

    body.dark-mode .ord-btn-secondary:hover,
    body.dark-mode .ord-btn-secondary:focus {
        color: #ffffff;
        border-color: #4a739a;
        background: linear-gradient(180deg, #1b3857 0%, #15304d 100%);
    }

    body.dark-mode .ord-table thead th,
    body.dark-mode .ord-table-footer {
        border-color: #2a4660;
        background: rgba(18, 38, 60, 0.9);
    }

    body.dark-mode .ord-table tbody td {
        border-bottom-color: #24415b;
    }

    body.dark-mode .ord-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(21, 50, 82, 0.9) 0%, rgba(17, 39, 64, 0.92) 100%);
    }

    body.dark-mode .ord-empty {
        background: linear-gradient(180deg, rgba(15, 31, 50, 0.92) 0%, rgba(12, 24, 40, 0.96) 100%);
        border-radius: 24px;
    }

    body.dark-mode .ord-empty h3 {
        color: #edf5ff;
    }

    body.dark-mode .ord-empty p {
        color: #9fbbd8;
    }

    body.dark-mode .ord-status-default {
        background: rgba(31, 120, 200, 0.18);
        color: #8dcbff;
    }

    body.dark-mode .ord-status-active {
        background: rgba(15, 159, 119, 0.16);
        color: #6ee7b7;
    }

    body.dark-mode .ord-status-expiree {
        background: rgba(217, 119, 6, 0.18);
        color: #fcd34d;
    }

    body.dark-mode .ord-status-annulee {
        background: rgba(220, 38, 38, 0.18);
        color: #fda4af;
    }

    @media (max-width: 1200px) {
        .ord-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ord-filter-form {
            grid-template-columns: 1fr 1fr;
        }

        .ord-filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 1024px) {
        .ord-hero-top,
        .ord-table-head,
        .ord-card-head,
        .ord-table-footer {
            display: grid;
            grid-template-columns: 1fr;
            flex-direction: column;
            align-items: stretch;
        }

        .ord-actions,
        .ord-table-meta {
            width: 100%;
            justify-content: flex-start;
        }

        .ord-actions .ord-btn {
            flex: 1 1 calc(50% - 5px);
        }

        .ord-table {
            min-width: 0;
            display: block;
        }

        .ord-table thead {
            display: none;
        }

        .ord-table tbody {
            display: grid;
            gap: 14px;
        }

        .ord-table tbody tr {
            display: grid;
            gap: 12px;
            padding: 16px;
            border: 1px solid var(--ord-border);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 16px 26px -30px rgba(15, 23, 42, 0.34);
        }

        .ord-table tbody td {
            display: grid;
            grid-template-columns: minmax(106px, 130px) minmax(0, 1fr);
            gap: 12px;
            align-items: start;
            padding: 0;
            border: 0;
        }

        .ord-table tbody td::before {
            content: attr(data-label);
            color: #657e98;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .ord-table tbody td[data-label="Actions"] {
            grid-template-columns: 1fr;
        }

        .ord-table tbody td[data-label="Actions"]::before {
            margin-bottom: 2px;
        }

        .ord-table tbody tr.empty-state-row {
            display: block;
            padding: 0;
            border: none;
            background: transparent;
            box-shadow: none;
        }

        .ord-table tbody tr.empty-state-row td {
            display: block;
        }

        .ord-table tbody tr.empty-state-row td::before {
            content: none;
        }

        body.dark-mode .ord-table tbody tr {
            background: rgba(16, 33, 54, 0.92);
            border-color: #294863;
        }

        body.dark-mode .ord-table tbody td::before {
            color: #8faecc;
        }
    }

    @media (max-width: 768px) {
        .ord-page {
            padding-bottom: 104px;
        }

        .ord-kpis {
            grid-template-columns: 1fr;
        }

        .ord-filter-form {
            grid-template-columns: 1fr;
        }

        .ord-title-row {
            align-items: flex-start;
        }

        .ord-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .ord-filter-actions > * {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .ord-actions {
            grid-template-columns: 1fr;
        }

        .ord-table tbody td {
            grid-template-columns: 1fr;
        }

        .ord-table tbody td::before {
            margin-bottom: 4px;
        }

        .ord-row-btn {
            flex: 1 1 calc(50% - 8px);
            justify-content: center;
        }
    }

    @media (max-width: 1024px) {
        .ord-table-wrap {
            overflow: visible;
        }

        .ord-table td,
        .ord-ref,
        .ord-person,
        .ord-person-name,
        .ord-person-meta,
        .ord-date-main,
        .ord-date-note,
        .ord-num,
        .ord-id {
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .ord-actions-cell {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }
    }

    @media (max-width: 576px) {
        .ord-actions-cell {
            grid-template-columns: 1fr;
        }

        .ord-row-btn {
            width: 100%;
        }
    }

    @media (max-width: 390px) {
        .ord-table tbody {
            gap: 12px;
        }

        .ord-table tbody tr {
            padding: 14px;
            gap: 10px;
            border-radius: 16px;
        }

        .ord-table tbody td {
            grid-template-columns: 1fr;
            gap: 6px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid ord-page">
    <div class="ord-shell">
        <section class="ord-hero">
            <div class="ord-hero-top">
                <div>
                    <div class="ord-title-row">
                        <span class="ord-title-icon"><i class="fas fa-prescription-bottle-alt"></i></span>
                        <span class="ord-eyebrow">Pilotage des prescriptions</span>
                        <div class="ord-title-block is-compact">
                            <h1 class="ord-title">Gestion des Ordonnances</h1>
                            <p class="ord-subtitle">Centralisez les prescriptions du cabinet dans une interface plus lisible, plus fluide et plus professionnelle, avec un suivi clair des patients, des mÃƒÂ©decins et des statuts.</p>
                        </div>
                    </div>
                </div>
                <div class="ord-actions">
                    <a href="{{ route('ordonnances.create') }}" class="ord-btn ord-btn-primary">
                        <span class="ord-btn-icon"><i class="fas fa-plus"></i></span>
                        <span>Nouvelle Ordonnance</span>
                    </a>
                    <a href="#" class="ord-btn ord-btn-secondary" id="exportBtn">
                        <span class="ord-btn-icon"><i class="fas fa-file-export"></i></span>
                        <span>Exporter</span>
                    </a>
                </div>
            </div>
        </section>

        <section class="ord-kpis">
            <article class="ord-kpi">
                <div class="ord-kpi-top">
                    <p class="ord-kpi-label">Ordonnances totales</p>
                    <span class="ord-kpi-icon primary"><i class="fas fa-prescription-bottle-alt"></i></span>
                </div>
                <h2 class="ord-kpi-value">{{ $ordonnances->total() }}</h2>
                <p class="ord-kpi-note">Volume global des prescriptions actuellement suivies sur cette liste.</p>
            </article>

            <article class="ord-kpi">
                <div class="ord-kpi-top">
                    <p class="ord-kpi-label">Ordonnances actives</p>
                    <span class="ord-kpi-icon success"><i class="fas fa-check-circle"></i></span>
                </div>
                <h2 class="ord-kpi-value">{{ $activeCount }}</h2>
                <p class="ord-kpi-note">Prescriptions exploitables immÃƒÂ©diatement parmi les ordonnances affichÃƒÂ©es.</p>
            </article>

            <article class="ord-kpi">
                <div class="ord-kpi-top">
                    <p class="ord-kpi-label">Ordonnances expirÃƒÂ©es</p>
                    <span class="ord-kpi-icon warning"><i class="fas fa-clock"></i></span>
                </div>
                <h2 class="ord-kpi-value">{{ $expiredCount }}</h2>
                <p class="ord-kpi-note">Ordonnances ÃƒÂ  surveiller ou ÃƒÂ  renouveler pour garder un suivi cohÃƒÂ©rent.</p>
            </article>

            <article class="ord-kpi">
                <div class="ord-kpi-top">
                    <p class="ord-kpi-label">Ordonnances annulÃƒÂ©es</p>
                    <span class="ord-kpi-icon danger"><i class="fas fa-times-circle"></i></span>
                </div>
                <h2 class="ord-kpi-value">{{ $cancelledCount }}</h2>
                <p class="ord-kpi-note">Prescriptions stoppÃƒÂ©es, visibles sans alourdir la lecture opÃƒÂ©rationnelle.</p>
            </article>
        </section>

        <section class="ord-card">
            <div class="ord-card-head">
                <div>
                    <h2 class="ord-card-title">Filtres et recherche</h2>
                    <p class="ord-card-copy">Affinez lÃ¢â‚¬â„¢affichage rapidement avec une recherche globale et un filtre de statut harmonisÃƒÂ©s avec le reste du design system.</p>
                </div>
                <span class="ord-counter">{{ $ordonnances->count() }} rÃƒÂ©sultat(s) sur cette page</span>
            </div>

            <div class="ord-filter-form">
                <div class="ord-filter-field">
                    <label for="ordSearchInput">Recherche</label>
                    <div class="ord-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="ordSearchInput" name="search" class="ord-search-input" placeholder="NumÃƒÂ©ro d'ordonnance, patient ou mÃƒÂ©decin..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="ord-filter-field">
                    <label for="statusFilter">Statut</label>
                    <select class="ord-filter-select" id="statusFilter">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expiree" {{ request('statut') == 'expiree' ? 'selected' : '' }}>ExpirÃƒÂ©e</option>
                        <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>AnnulÃƒÂ©e</option>
                    </select>
                </div>

                <div class="ord-filter-actions">
                    <button type="button" class="ord-btn-filter" id="applyFilters">
                        <i class="fas fa-filter"></i>
                        <span>Appliquer</span>
                    </button>

                    @if(request()->hasAny(['search', 'statut']))
                        <a href="{{ route('ordonnances.index') }}" class="ord-btn-reset">
                            <i class="fas fa-rotate-left"></i>
                            <span>RÃƒÂ©initialiser</span>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="ord-table-card">
            <div class="ord-table-head">
                <div>
                    <h2 class="ord-card-title">Liste des ordonnances</h2>
                    <p class="ord-card-copy">Une lecture plus respirante, des statuts plus clairs et des actions harmonisÃƒÂ©es pour rÃƒÂ©duire lÃ¢â‚¬â„¢effet tableau administratif.</p>
                </div>
                <div class="ord-table-meta">
                    <span class="ord-counter">Vue premium</span>
                    <span class="ord-chip"><i class="fas fa-table-list"></i> Pagination conservÃƒÂ©e</span>
                </div>
            </div>

            <div class="ord-table-wrap">
                <table class="ord-table">
            <thead>
                <tr>
                    <th>ID / NumÃƒÂ©ro</th>
                    <th>Patient</th>
                    <th>MÃƒÂ©decin</th>
                    <th>Date Prescription</th>
                    <th>Date Expiration</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordonnances as $ordonnance)
                    <tr>
                        <td data-label="ID / NumÃƒÂ©ro">
                            <div class="ord-ref">
                                <div class="ord-id">Ordonnance #{{ $ordonnance->id }}</div>
                            @if($ordonnance->numero_ordonnance)
                                    <div class="ord-num">{{ $ordonnance->numero_ordonnance }}</div>
                                @else
                                    <div class="ord-num">Non gÃƒÂ©nÃƒÂ©rÃƒÂ©</div>
                                @endif
                            </div>
                        </td>
                        <td data-label="Patient">
                            @if($ordonnance->patient)
                                <div class="ord-person">
                                    <div class="ord-person-name">{{ strtoupper($ordonnance->patient->nom) }} {{ $ordonnance->patient->prenom }}</div>
                                    <div class="ord-person-meta">ID patient : {{ $ordonnance->patient->id }}</div>
                                </div>
                            @else
                                <div class="ord-person">
                                    <div class="ord-person-name" style="color: var(--ord-danger);">Patient non trouvÃƒÂ©</div>
                                    <div class="ord-person-meta">VÃƒÂ©rifier lÃ¢â‚¬â„¢association du dossier</div>
                                </div>
                            @endif
                        </td>
                        <td data-label="MÃƒÂ©decin">
                            @if($ordonnance->display_medecin_name)
                                <div class="ord-person">
                                    <div class="ord-person-name">{{ $ordonnance->display_medecin_name }}</div>
                                    <div class="ord-person-meta">{{ $ordonnance->display_medecin_specialite }}</div>
                                </div>
                            @else
                                <div class="ord-person">
                                    <div class="ord-person-name" style="color: var(--ord-danger);">MÃƒÂ©decin non trouvÃƒÂ©</div>
                                    <div class="ord-person-meta">Aucune rÃƒÂ©fÃƒÂ©rence clinique disponible</div>
                                </div>
                            @endif
                        </td>
                        <td data-label="Date Prescription">
                            <div class="ord-date">
                                <div class="ord-date-main">{{ $ordonnance->display_date_prescription }}</div>
                                <div class="ord-date-note">Prescription enregistrÃƒÂ©e</div>
                            </div>
                        </td>
                        <td data-label="Date Expiration">
                            @if($ordonnance->date_expiration)
                                <div class="ord-date">
                                    <div class="ord-date-main">{{ $ordonnance->display_date_expiration }}</div>
                                    <div class="ord-date-note {{ $ordonnance->display_expiration_tone }}">{{ $ordonnance->display_expiration_note }}</div>
                                </div>
                            @else
                                <div class="ord-date">
                                    <div class="ord-date-main">-</div>
                                    <div class="ord-date-note">{{ $ordonnance->display_expiration_note }}</div>
                                </div>
                            @endif
                        </td>
                        <td data-label="Statut">
                            <span class="ord-status {{ $ordonnance->display_statut_class }}">
                                {{ $ordonnance->display_statut_text }}
                            </span>
                        </td>
                        <td data-label="Actions">
                            <div class="ord-actions-cell">
                                <a href="{{ route('ordonnances.show', $ordonnance->id) }}"
                                   class="ord-row-btn view action-tone-view"
                                   title="Voir l'ordonnance">
                                    <i class="fas fa-eye"></i>
                                    <span>Voir</span>
                                </a>
                                <a href="{{ route('ordonnances.edit', $ordonnance->id) }}"
                                   class="ord-row-btn edit action-tone-edit"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                    <span>Modifier</span>
                                </a>
                                <a href="#"
                                   class="ord-row-btn print action-tone-print"
                                   title="Imprimer"
                                   aria-label="Imprimer l'ordonnance {{ $ordonnance->numero_ordre }}"
                                   onclick="window.open('{{ route('ordonnances.show', $ordonnance->id) }}?print=1', '_blank'); return false;">
                                    <i class="fas fa-print"></i>
                                    <span>Imprimer</span>
                                </a>
                                <form action="{{ route('ordonnances.destroy', $ordonnance->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Voulez-vous vraiment supprimer cette ordonnance ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="ord-row-btn delete action-tone-delete"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                        <span>Supprimer</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-state-row">
                        <td colspan="7" class="text-center py-4">
                            <div class="ord-empty">
                                <div class="ord-empty-icon"><i class="fas fa-prescription-bottle-alt"></i></div>
                                <h3>Aucune ordonnance trouvÃƒÂ©e</h3>
                                <p>La liste est encore vide ou ne correspond pas ÃƒÂ  vos filtres actuels. CrÃƒÂ©ez une premiÃƒÂ¨re ordonnance pour lancer le suivi des prescriptions du cabinet.</p>
                                <a href="{{ route('ordonnances.create') }}" class="ord-btn ord-btn-primary mt-3">
                                    <span class="ord-btn-icon"><i class="fas fa-plus"></i></span>
                                    <span>CrÃƒÂ©er une ordonnance</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
            </div>

            <div class="ord-table-footer">
                <div class="ord-pagination-info">
                Affichage de {{ $ordonnances->firstItem() ?? 0 }} ÃƒÂ  {{ $ordonnances->lastItem() ?? 0 }}
                sur {{ $ordonnances->total() }} ordonnances
                </div>

                @if($ordonnances->hasPages())
                    <div>
                        {{ $ordonnances->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection





