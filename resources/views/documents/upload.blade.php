@extends('layouts.app')

@section('title', 'Documents upload')
@section('topbar_subtitle', 'Ajout et classement des documents patients dans une interface plus premium et plus coherente.')

@section('content')
<style>
    .document-upload-page {
        --du-bg-1: #f7fbff;
        --du-bg-2: #eef4fb;
        --du-card: #ffffff;
        --du-border: #d8e5f5;
        --du-text-1: #0f172a;
        --du-text-2: #4b5563;
        --du-muted: #7b8ba3;
        --du-primary: #0284c7;
        --du-primary-2: #0369a1;
        --du-accent: #f59e0b;
        --du-danger: #ef4444;
        --du-field-bg: #f8fbff;
        --du-field-border: #c9d9ee;
        --du-drop-bg: #f8fbff;
        --du-drop-border: #c9d9ee;
        --du-shadow: 0 20px 34px -30px rgba(15, 23, 42, .7);

        width: 100%;
        min-height: 100%;
        padding: 14px clamp(10px, 1.3vw, 24px) 22px;
        border: 1px solid #dbe8f7;
        border-radius: 16px;
        background:
            radial-gradient(circle at 100% 0%, rgba(14, 165, 233, .10) 0%, transparent 34%),
            radial-gradient(circle at 0% 100%, rgba(245, 158, 11, .08) 0%, transparent 32%),
            linear-gradient(135deg, var(--du-bg-1), var(--du-bg-2));
    }

    .upload-shell {
        width: 100%;
        max-width: none;
    }

    .du-module-head {
        display: grid;
        grid-template-columns: minmax(0, 1.18fr) auto;
        gap: 18px;
        margin-bottom: 18px;
        padding: 22px;
        border: 1px solid #d8e5f5;
        border-radius: 24px;
        background:
            radial-gradient(circle at right top, rgba(2, 132, 199, .12) 0%, transparent 32%),
            radial-gradient(circle at left top, rgba(14, 165, 233, .10) 0%, transparent 34%),
            linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(243, 249, 255, .96));
        box-shadow: 0 24px 42px -34px rgba(15, 23, 42, .5);
        align-items: start;
    }

    .du-hero-copy {
        display: grid;
        gap: 14px;
        min-width: 0;
    }

    .du-eyebrow {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(2, 132, 199, .16);
        background: rgba(255, 255, 255, .8);
        color: #0f5d91;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .du-head-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }

    .du-title-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
        font-size: 1.35rem;
        background: linear-gradient(135deg, var(--du-primary) 0%, var(--du-primary-2) 100%);
        box-shadow: 0 18px 28px -20px rgba(3, 105, 161, .75);
    }

    .du-head-title {
        min-width: 0;
    }

    .du-title-row {
        display: flex;
        align-items: center;
        gap: 11px;
        flex-wrap: wrap;
    }

    .du-title-row h1 {
        margin: 0;
        color: #173a65;
        font-size: clamp(1.65rem, 2.35vw, 2.15rem);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -.03em;
    }

    .du-head-title p {
        margin: 8px 0 0;
        color: #5f7896;
        font-size: .96rem;
        font-weight: 600;
        line-height: 1.65;
    }

    .du-count-badge {
        background: linear-gradient(135deg, #eaf5ff 0%, #d9ebff 100%);
        border: 1px solid #c6dcf5;
        color: #1d4f7c;
        box-shadow: none;
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: .86rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .du-head-actions {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .du-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .du-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #d6e4f3;
        background: rgba(255, 255, 255, .8);
        color: #547090;
        font-size: .82rem;
        font-weight: 800;
    }

    .du-head-btn {
        min-height: 50px;
        border-radius: 16px;
        padding: 0 18px;
        border: 1px solid #d5e3f1;
        font-size: .92rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease, color .18s ease;
    }

    .du-head-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .du-head-btn.secondary {
        background: rgba(255, 255, 255, .82);
        color: #486482;
    }

    .du-head-btn.secondary:hover {
        background: #edf5fd;
        border-color: #c7dbef;
        color: #2c4b6c;
    }

    .du-head-btn.success {
        background: linear-gradient(135deg, var(--du-primary) 0%, var(--du-primary-2) 100%);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 18px 24px -22px rgba(3, 105, 161, .85);
    }

    .du-head-btn.success:hover {
        background: linear-gradient(135deg, #0274b0 0%, #035884 100%);
        color: #fff;
    }

    .du-card {
        border: 1px solid var(--du-border);
        border-radius: 14px;
        background: var(--du-card);
        box-shadow: var(--du-shadow);
        overflow: hidden;
    }

    .du-section {
        border-bottom: 1px solid #e2eaf6;
        padding: clamp(14px, 1.8vw, 24px);
    }

    .du-section:last-of-type {
        border-bottom: 0;
    }

    .du-head {
        margin: calc(-1 * clamp(14px, 1.8vw, 24px)) calc(-1 * clamp(14px, 1.8vw, 24px)) 16px;
        padding: 12px clamp(14px, 1.8vw, 24px);
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #deebf8;
        background: linear-gradient(135deg, #f5faff 0%, #edf5ff 100%);
    }

    .du-head h2 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        letter-spacing: .02em;
        color: var(--du-text-1);
        text-transform: uppercase;
    }

    .du-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid #cfe0f4;
        background: #ffffff;
        color: #0f4f85;
        font-size: 14px;
    }

    .du-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: clamp(12px, 1.4vw, 18px);
    }

    .du-grid.one {
        grid-template-columns: 1fr;
    }

    .du-grid.two {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }

    .du-field {
        display: flex;
        flex-direction: column;
    }

    .du-label {
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #30455f;
    }

    .du-required {
        color: var(--du-danger);
        margin-left: 4px;
    }

    .du-input,
    .du-select,
    .du-textarea {
        width: 100%;
        min-height: 45px;
        border: 1px solid var(--du-field-border);
        border-radius: 10px;
        background: var(--du-field-bg);
        color: #1f2937;
        font-size: 14px;
        padding: 11px 13px;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .du-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .du-input:focus,
    .du-select:focus,
    .du-textarea:focus {
        outline: none;
        border-color: var(--du-primary);
        box-shadow: 0 0 0 3px rgba(2, 132, 199, .14);
        transform: translateY(-.5px);
    }

    .du-input.error,
    .du-select.error,
    .du-textarea.error {
        border-color: #ef4444;
        background: #fff4f4;
    }

    .du-help {
        margin-top: 6px;
        color: var(--du-muted);
        font-size: 12px;
    }

    .du-patient-summary {
        margin-top: 10px;
        padding: 12px 14px;
        border: 1px solid #d7e6f5;
        border-radius: 12px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);
    }

    .du-patient-summary strong {
        display: block;
        color: #17385a;
        font-size: .96rem;
        line-height: 1.3;
    }

    .du-patient-summary span {
        color: #647c98;
        font-size: .82rem;
        font-weight: 700;
    }

    .du-inline-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .du-inline-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid #d8e5f5;
        background: #eff6ff;
        color: #1d4f7e;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .du-inline-badge.patient {
        background: #ecfdf3;
        border-color: #c7efd5;
        color: #0f7b43;
    }

    .du-inline-badge.scan {
        background: #fff7ed;
        border-color: #fbd5a5;
        color: #b45309;
    }

    .du-category-list {
        margin-top: 10px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 8px;
    }

    .du-category-item {
        border: 1px solid #dbe8f7;
        border-radius: 10px;
        background: #fff;
        padding: 9px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        text-align: left;
        cursor: pointer;
        transition: border-color .16s ease, background .16s ease, transform .16s ease;
    }

    .du-category-item:hover {
        border-color: #91c5f0;
        background: #f5faff;
        transform: translateY(-1px);
    }

    .du-category-main {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .du-category-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }

    .du-category-name {
        margin: 0;
        color: #16324f;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.25;
    }

    .du-category-count {
        margin: 0;
        color: #6f89a7;
        font-size: 11px;
        font-weight: 700;
    }

    .du-category-tags {
        margin-top: 5px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .du-category-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 7px;
        border-radius: 999px;
        background: #f3f6fb;
        border: 1px solid #dde7f2;
        color: #56708e;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .du-category-tag.patient {
        background: #edfdf4;
        border-color: #c6efd7;
        color: #0f7b43;
    }

    .du-category-badge {
        border-radius: 999px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 800;
        color: #1b5b8f;
        background: #e8f4ff;
        border: 1px solid #cbe3f8;
        white-space: nowrap;
    }

    .du-category-empty {
        margin-top: 10px;
        border: 1px dashed #b9cee5;
        border-radius: 10px;
        background: #f7fbff;
        padding: 10px 11px;
        color: #5d7896;
        font-size: 12px;
        font-weight: 600;
    }

    .du-category-empty a {
        color: #0f6fb0;
        font-weight: 800;
        text-decoration: none;
    }

    .du-category-empty a:hover {
        color: #0a5a90;
        text-decoration: underline;
    }

    .du-error {
        margin-top: 6px;
        color: #dc2626;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .du-drop {
        border: 2px dashed var(--du-drop-border);
        border-radius: 12px;
        padding: clamp(24px, 3vw, 38px) 14px;
        text-align: center;
        background: var(--du-drop-bg);
        cursor: pointer;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .du-drop:hover {
        border-color: var(--du-primary);
        background: #f1f8ff;
    }

    .du-drop.dragover {
        border-color: var(--du-accent);
        background: #fff6e8;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, .12);
    }

    .du-drop-icon {
        font-size: 38px;
        color: #90a4bf;
        margin-bottom: 9px;
    }

    .du-drop-title {
        margin: 0 0 7px;
        font-weight: 700;
        color: #334155;
    }

    .du-drop-sub {
        margin: 0;
        color: #94a3b8;
        font-size: 13px;
    }

    #fileInput {
        display: none;
    }

    .du-files {
        margin-top: 12px;
        border: 1px solid #dce8f7;
        border-radius: 10px;
        background: #f8fbff;
        padding: 10px;
        display: none;
    }

    .du-file {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 9px;
        border: 1px solid #dbe8f7;
        border-radius: 9px;
        background: #fff;
    }

    .du-file-main {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .du-file-icon {
        width: 34px;
        height: 34px;
        border: 1px solid #dbe8f7;
        border-radius: 8px;
        background: #eef6ff;
        color: #0f4f85;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .du-file-name {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        word-break: break-all;
        margin: 0;
    }

    .du-file-size {
        font-size: 12px;
        color: #8aa0bc;
        margin: 2px 0 0;
    }

    .du-file-remove {
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fff1f2;
        color: #be123c;
        font-size: 12px;
        font-weight: 700;
        padding: 8px 12px;
        min-height: 36px;
        cursor: pointer;
    }

    .du-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        border-top: 1px solid #deebf8;
        background: #f7fbff;
        padding: 14px clamp(14px, 1.8vw, 24px);
    }

    .du-btn {
        min-height: 44px;
        border-radius: 10px;
        border: 1px solid transparent;
        font-size: 14px;
        font-weight: 700;
        padding: 11px 18px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: transform .16s ease, box-shadow .16s ease, background .16s ease, border-color .16s ease;
    }

    .du-btn-primary {
        color: #fff;
        background: linear-gradient(135deg, var(--du-primary), var(--du-primary-2));
        box-shadow: 0 14px 20px -18px rgba(3, 105, 161, .8);
    }

    .du-btn-primary:hover {
        transform: translateY(-1px);
    }

    .du-btn-secondary {
        color: #2a4668;
        border-color: #c8d9ed;
        background: #ecf4fe;
    }

    .du-btn-secondary:hover {
        background: #e3eefb;
    }

    body.dark-mode .document-upload-page,
    html.dark-mode .document-upload-page,
    body[data-theme="dark"] .document-upload-page {
        --du-card: #0f2136;
        --du-border: #2f4b67;
        --du-text-1: #e8f0fb;
        --du-text-2: #a3b8d3;
        --du-muted: #96acc8;
        --du-field-bg: #0c1a2b;
        --du-field-border: #385978;
        --du-drop-bg: #102339;
        --du-drop-border: #3a5b7a;
        --du-shadow: 0 18px 30px -26px rgba(0, 0, 0, .9);
        background:
            radial-gradient(circle at 100% 0%, rgba(14, 165, 233, .11) 0%, transparent 34%),
            radial-gradient(circle at 0% 100%, rgba(245, 158, 11, .09) 0%, transparent 32%),
            linear-gradient(135deg, #0b1625, #0e1b2b);
        border-color: #2d4662;
    }

    body.dark-mode .du-module-head,
    html.dark-mode .du-module-head,
    body[data-theme="dark"] .du-module-head {
        border-color: #31516f;
        background:
            radial-gradient(circle at right top, rgba(59, 130, 246, .18) 0%, transparent 32%),
            radial-gradient(circle at left top, rgba(14, 165, 233, .12) 0%, transparent 34%),
            linear-gradient(135deg, rgba(15, 31, 48, .96), rgba(18, 36, 55, .96));
        box-shadow: 0 24px 42px -34px rgba(0, 0, 0, .85);
    }

    body.dark-mode .du-eyebrow,
    html.dark-mode .du-eyebrow,
    body[data-theme="dark"] .du-eyebrow {
        border-color: #3a5f82;
        background: rgba(23, 52, 79, .86);
        color: #d8ebff;
    }

    body.dark-mode .du-title-icon,
    html.dark-mode .du-title-icon,
    body[data-theme="dark"] .du-title-icon {
        background: linear-gradient(135deg, #1d5ea8 0%, #123f71 100%);
    }

    body.dark-mode .du-title-row h1,
    html.dark-mode .du-title-row h1,
    body[data-theme="dark"] .du-title-row h1 {
        color: #e4f1ff;
    }

    body.dark-mode .du-head-title p,
    html.dark-mode .du-head-title p,
    body[data-theme="dark"] .du-head-title p {
        color: #a9c2dc;
    }

    body.dark-mode .du-count-badge,
    html.dark-mode .du-count-badge,
    body[data-theme="dark"] .du-count-badge {
        background: #183451;
        border-color: #355a7d;
        color: #d8ebff;
    }

    body.dark-mode .du-chip,
    html.dark-mode .du-chip,
    body[data-theme="dark"] .du-chip {
        background: rgba(22, 45, 70, .9);
        border-color: #355a7d;
        color: #c5d8f0;
    }

    body.dark-mode .du-head-btn.secondary,
    html.dark-mode .du-head-btn.secondary,
    body[data-theme="dark"] .du-head-btn.secondary {
        color: #d2e6fb;
        border-color: #3c5f81;
        background: #1a3855;
    }

    body.dark-mode .du-head-btn.secondary:hover,
    html.dark-mode .du-head-btn.secondary:hover,
    body[data-theme="dark"] .du-head-btn.secondary:hover {
        color: #fff;
        background: #234a6d;
    }

    body.dark-mode .du-head,
    html.dark-mode .du-head,
    body[data-theme="dark"] .du-head {
        background: linear-gradient(135deg, #142d4a, #12304b);
        border-bottom-color: #2f4b67;
    }

    body.dark-mode .du-icon,
    html.dark-mode .du-icon,
    body[data-theme="dark"] .du-icon {
        background: #193a5c;
        border-color: #315676;
        color: #cde2ff;
    }

    body.dark-mode .du-label,
    html.dark-mode .du-label,
    body[data-theme="dark"] .du-label {
        color: #c5d8f0;
    }

    body.dark-mode .du-category-item,
    html.dark-mode .du-category-item,
    body[data-theme="dark"] .du-category-item {
        border-color: #2f4b67;
        background: #132a43;
    }

    body.dark-mode .du-category-item:hover,
    html.dark-mode .du-category-item:hover,
    body[data-theme="dark"] .du-category-item:hover {
        border-color: #4f7397;
        background: #1a3552;
    }

    body.dark-mode .du-category-name,
    html.dark-mode .du-category-name,
    body[data-theme="dark"] .du-category-name {
        color: #d8e9fb;
    }

    body.dark-mode .du-category-count,
    html.dark-mode .du-category-count,
    body[data-theme="dark"] .du-category-count {
        color: #9ab0ca;
    }

    body.dark-mode .du-category-badge,
    html.dark-mode .du-category-badge,
    body[data-theme="dark"] .du-category-badge {
        color: #c8e4ff;
        background: #1b3b5c;
        border-color: #3a5f84;
    }

    body.dark-mode .du-category-empty,
    html.dark-mode .du-category-empty,
    body[data-theme="dark"] .du-category-empty {
        color: #a9c2dd;
        border-color: #3f6287;
        background: #16304c;
    }

    body.dark-mode .du-category-empty a,
    html.dark-mode .du-category-empty a,
    body[data-theme="dark"] .du-category-empty a {
        color: #8ec8ff;
    }

    body.dark-mode .du-input,
    body.dark-mode .du-select,
    body.dark-mode .du-textarea,
    html.dark-mode .du-input,
    html.dark-mode .du-select,
    html.dark-mode .du-textarea,
    body[data-theme="dark"] .du-input,
    body[data-theme="dark"] .du-select,
    body[data-theme="dark"] .du-textarea {
        color: #eaf2ff;
    }

    body.dark-mode .du-input::placeholder,
    body.dark-mode .du-textarea::placeholder,
    html.dark-mode .du-input::placeholder,
    html.dark-mode .du-textarea::placeholder,
    body[data-theme="dark"] .du-input::placeholder,
    body[data-theme="dark"] .du-textarea::placeholder {
        color: #85a2c2;
    }

    body.dark-mode .du-drop-title,
    html.dark-mode .du-drop-title,
    body[data-theme="dark"] .du-drop-title {
        color: #d8e7fa;
    }

    body.dark-mode .du-drop-sub,
    body.dark-mode .du-drop-icon,
    body.dark-mode .du-help,
    html.dark-mode .du-drop-sub,
    html.dark-mode .du-drop-icon,
    html.dark-mode .du-help,
    body[data-theme="dark"] .du-drop-sub,
    body[data-theme="dark"] .du-drop-icon,
    body[data-theme="dark"] .du-help {
        color: #90a8c6;
    }

    body.dark-mode .du-drop:hover,
    html.dark-mode .du-drop:hover,
    body[data-theme="dark"] .du-drop:hover {
        background: #162b44;
        border-color: #3f658a;
    }

    body.dark-mode .du-drop.dragover,
    html.dark-mode .du-drop.dragover,
    body[data-theme="dark"] .du-drop.dragover {
        border-color: #f59e0b;
        background: #2b2a22;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, .16);
    }

    body.dark-mode .du-files,
    html.dark-mode .du-files,
    body[data-theme="dark"] .du-files {
        border-color: #2f4b67;
        background: #102339;
    }

    body.dark-mode .du-file,
    html.dark-mode .du-file,
    body[data-theme="dark"] .du-file {
        border-color: #2f4b67;
        background: #132a43;
    }

    body.dark-mode .du-file-icon,
    html.dark-mode .du-file-icon,
    body[data-theme="dark"] .du-file-icon {
        background: #1c3b5d;
        border-color: #2f4b67;
        color: #d7e8ff;
    }

    body.dark-mode .du-file-name,
    html.dark-mode .du-file-name,
    body[data-theme="dark"] .du-file-name {
        color: #e8f0fb;
    }

    body.dark-mode .du-file-size,
    html.dark-mode .du-file-size,
    body[data-theme="dark"] .du-file-size {
        color: #9ab0ca;
    }

    body.dark-mode .du-actions,
    html.dark-mode .du-actions,
    body[data-theme="dark"] .du-actions {
        background: #0f2136;
        border-top-color: #2f4b67;
    }

    body.dark-mode .du-btn-secondary,
    html.dark-mode .du-btn-secondary,
    body[data-theme="dark"] .du-btn-secondary {
        color: #d7e8ff;
        border-color: #3b5f84;
        background: #193554;
    }

    body.dark-mode .du-btn-secondary:hover,
    html.dark-mode .du-btn-secondary:hover,
    body[data-theme="dark"] .du-btn-secondary:hover {
        background: #244567;
    }

    body.dark-mode .du-error,
    html.dark-mode .du-error,
    body[data-theme="dark"] .du-error {
        color: #fca5a5;
    }

    body.dark-mode .du-input.error,
    body.dark-mode .du-select.error,
    body.dark-mode .du-textarea.error,
    html.dark-mode .du-input.error,
    html.dark-mode .du-select.error,
    html.dark-mode .du-textarea.error,
    body[data-theme="dark"] .du-input.error,
    body[data-theme="dark"] .du-select.error,
    body[data-theme="dark"] .du-textarea.error {
        background: rgba(127, 29, 29, .22);
    }

    @media (max-width: 880px) {
        .document-upload-page {
            padding: 10px;
        }

        .du-module-head {
            grid-template-columns: 1fr;
            padding: 18px;
        }

        .du-head-main {
            align-items: flex-start;
        }

        .du-head-actions,
        .du-head-btn {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .du-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .du-actions {
            flex-direction: column-reverse;
        }

        .du-btn {
            width: 100%;
        }

        .du-file {
            flex-direction: column;
            align-items: flex-start;
        }

        .du-file-remove {
            width: 100%;
        }
    }
</style>

<div class="document-upload-page">
    <div class="upload-shell">
        <div class="du-module-head">
            <div class="du-hero-copy">
                <span class="du-eyebrow">Archivage medical</span>
                <div class="du-head-main">
                    <span class="du-title-icon" aria-hidden="true">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </span>
                    <div class="du-head-title">
                        <div class="du-title-row">
                            <h1>Televerser un document</h1>
                            <span class="du-count-badge">{{ $activeCategoriesCount }} categories actives</span>
                        </div>
                        <p>Ajoutez un document medical ou administratif au dossier du patient avec un classement plus propre, plus lisible et plus rapide.</p>
                    </div>
                </div>

                <div class="du-chip-row">
                    @if($displayPatient)
                        <span class="du-chip"><i class="fas fa-user-link"></i>{{ trim($displayPatient->prenom . ' ' . $displayPatient->nom) }}</span>
                    @endif
                    <span class="du-chip"><i class="fas fa-folder-tree"></i>Classement structure</span>
                </div>
            </div>
            <div class="du-head-actions">
                <a href="{{ route('documents.index') }}" class="du-head-btn secondary">
                    <i class="fas fa-arrow-left"></i> Retour liste
                </a>
                <a href="{{ route('documents.categories') }}" class="du-head-btn secondary">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="{{ route('documents.index') }}" class="du-head-btn success">
                    <i class="fas fa-folder-open"></i> Voir documents
                </a>
            </div>
        </div>

        <div class="du-card">
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="du-section">
                    <div class="du-head">
                        <span class="du-icon"><i class="fas fa-user-injured"></i></span>
                        <h2>Dossier patient</h2>
                    </div>

                    <div class="du-grid two">
                        <div class="du-field">
                            <label for="patient_id" class="du-label">Patient <span class="du-required">*</span></label>
                            <select id="patient_id" name="patient_id" class="du-select {{ $errors->has('patient_id') ? 'error' : '' }}" required>
                                <option value="">-- Selectionner un patient --</option>
                                @foreach($patients as $patient)
                                    <option
                                        value="{{ $patient->id }}"
                                        {{ (string) old('patient_id', $selectedPatient?->id) === (string) $patient->id ? 'selected' : '' }}
                                    >
                                        {{ trim($patient->prenom . ' ' . $patient->nom) }}{{ $patient->numero_dossier ? ' - ' . $patient->numero_dossier : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('patient_id'))
                                <div class="du-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('patient_id') }}</div>
                            @endif
                            <p class="du-help">Le document sera automatiquement associe a l'archive du patient selectionne.</p>

                            @if($displayPatient)
                                <div class="du-patient-summary">
                                    <strong>{{ trim($displayPatient->prenom . ' ' . $displayPatient->nom) }}</strong>
                                    <span>{{ $displayPatient->numero_dossier ?: 'Dossier patient actif' }}</span>
                                    <div class="du-inline-badges">
                                        <span class="du-inline-badge patient"><i class="fas fa-link"></i> Association automatique au dossier</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="du-field">
                            <label for="source_document" class="du-label">Mode d'entree</label>
                            <select id="source_document" name="source_document" class="du-select {{ $errors->has('source_document') ? 'error' : '' }}">
                                <option value="telechargement" {{ old('source_document', 'telechargement') === 'telechargement' ? 'selected' : '' }}>Televersement / import</option>
                                <option value="scan_cabinet" {{ old('source_document', 'telechargement') === 'scan_cabinet' ? 'selected' : '' }}>Document scanne au cabinet</option>
                            </select>
                            @if($errors->has('source_document'))
                                <div class="du-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('source_document') }}</div>
                            @endif
                            <p class="du-help">Permet d'identifier clairement les documents numerises directement au cabinet.</p>
                            <div class="du-inline-badges">
                                <span class="du-inline-badge scan"><i class="fas fa-scanner"></i> Classement structure pour documents scannes</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="du-section">
                    <div class="du-head">
                        <span class="du-icon"><i class="fas fa-folder-open"></i></span>
                        <h2>Categorie</h2>
                    </div>

                    <div class="du-grid one">
                        <div class="du-field">
                            <label for="categorie_document_id" class="du-label">Categorie <span class="du-required">*</span></label>
                            <select id="categorie_document_id" name="categorie_document_id" class="du-select {{ $errors->has('categorie_document_id') ? 'error' : '' }}" required>
                                <option value="">-- Selectionner une categorie --</option>
                                @foreach($categoriesActive as $categorie)
                                    <option value="{{ $categorie->id }}" {{ old('categorie_document_id') == $categorie->id ? 'selected' : '' }}>
                                        {{ $categorie->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('categorie_document_id'))
                                <div class="du-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('categorie_document_id') }}</div>
                            @endif
                            <p class="du-help">Choisissez la categorie appropriee pour ce document.</p>

                            @if($categoriesActive->count() > 0)
                                <div class="du-category-list">
                                    @foreach($categoriesActive as $categorie)
                                        <button type="button" class="du-category-item" data-category-id="{{ $categorie->id }}">
                                            <span class="du-category-main">
                                                <span class="du-category-icon" style="background: {{ $categorie->display_color }};">
                                                    <i class="{{ $categorie->display_icon }}"></i>
                                                </span>
                                                <span>
                                                    <p class="du-category-name">{{ $categorie->nom }}</p>
                                                    <p class="du-category-count">{{ $categorie->display_documents_count }} document(s)</p>
                                                    <div class="du-category-tags">
                                                        @if($categorie->est_document_patient)
                                                            <span class="du-category-tag patient"><i class="fas fa-user"></i> Patient</span>
                                                        @endif
                                                        @if($categorie->confidentiel)
                                                            <span class="du-category-tag"><i class="fas fa-lock"></i> Confidentiel</span>
                                                        @endif
                                                    </div>
                                                </span>
                                            </span>
                                            <span class="du-category-badge">Choisir</span>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="du-category-empty">
                                    Aucune categorie active. Creez d'abord une categorie depuis
                                    <a href="{{ route('documents.categories') }}">la page categories</a>.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="du-section">
                    <div class="du-head">
                        <span class="du-icon"><i class="fas fa-file-lines"></i></span>
                        <h2>Fichier</h2>
                    </div>

                    <div class="du-grid one">
                        <div class="du-field">
                            <label for="fileInput" class="du-label">Fichier <span class="du-required">*</span></label>

                            <div class="du-drop" id="uploadZone" role="button" tabindex="0" aria-label="Zone de depot du fichier">
                                <div class="du-drop-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                                <p class="du-drop-title">Glissez-deposez votre fichier ici</p>
                                <p class="du-drop-sub">Ou cliquez pour selectionner</p>
                            </div>

                            <input type="file" id="fileInput" name="fichier" class="{{ $errors->has('fichier') ? 'error' : '' }}" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip" required>

                            @if($errors->has('fichier'))
                                <div class="du-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('fichier') }}</div>
                            @endif

                            <p class="du-help">Formats acceptes : PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG, GIF, ZIP (max 20 MB).</p>

                            <div id="fileList" class="du-files"></div>
                        </div>
                    </div>
                </div>

                <div class="du-section">
                    <div class="du-head">
                        <span class="du-icon"><i class="fas fa-circle-info"></i></span>
                        <h2>Informations complementaires</h2>
                    </div>

                    <div class="du-grid one">
                        <div class="du-field">
                            <label for="description" class="du-label">Description</label>
                            <textarea id="description" name="description" class="du-textarea {{ $errors->has('description') ? 'error' : '' }}" placeholder="Description ou details additionnels du document...">{{ old('description') }}</textarea>
                            @if($errors->has('description'))
                                <div class="du-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('description') }}</div>
                            @endif
                            <p class="du-help">Ajoutez des details pour identifier ce document plus facilement.</p>
                        </div>
                    </div>
                </div>

                <div class="du-actions">
                    <a href="{{ route('documents.index') }}" class="du-btn du-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour a la liste</span>
                    </a>
                    <button type="submit" class="du-btn du-btn-primary">
                        <i class="fas fa-cloud-arrow-up"></i>
                        <span>Televerser</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const categorySelect = document.getElementById('categorie_document_id');
    const categoryButtons = document.querySelectorAll('[data-category-id]');

    function getFileIconClass(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            pdf: 'fas fa-file-pdf',
            doc: 'fas fa-file-word',
            docx: 'fas fa-file-word',
            xls: 'fas fa-file-excel',
            xlsx: 'fas fa-file-excel',
            txt: 'fas fa-file-lines',
            jpg: 'fas fa-file-image',
            jpeg: 'fas fa-file-image',
            png: 'fas fa-file-image',
            gif: 'fas fa-file-image',
            zip: 'fas fa-file-archive'
        };
        return icons[ext] || 'fas fa-file';
    }

    function removeFile() {
        fileInput.value = '';
        fileList.style.display = 'none';
        fileList.innerHTML = '';
    }

    function displayFiles() {
        if (!fileInput.files || fileInput.files.length === 0) {
            fileList.style.display = 'none';
            fileList.innerHTML = '';
            return;
        }

        fileList.innerHTML = '';
        for (const file of fileInput.files) {
            const sizeMb = (file.size / 1024 / 1024).toFixed(2);
            const iconClass = getFileIconClass(file.name);
            const item = document.createElement('div');
            item.className = 'du-file';
            item.innerHTML = `
                <div class="du-file-main">
                    <span class="du-file-icon"><i class="${iconClass}"></i></span>
                    <div>
                        <p class="du-file-name">${file.name}</p>
                        <p class="du-file-size">${sizeMb} MB</p>
                    </div>
                </div>
                <button type="button" class="du-file-remove" id="removeSelectedFile">Supprimer</button>
            `;
            fileList.appendChild(item);
        }

        fileList.style.display = 'block';
        const removeBtn = document.getElementById('removeSelectedFile');
        if (removeBtn) {
            removeBtn.addEventListener('click', removeFile);
        }
    }

    if (uploadZone && fileInput && fileList) {
        uploadZone.addEventListener('click', () => fileInput.click());
        uploadZone.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                fileInput.click();
            }
        });

        uploadZone.addEventListener('dragover', (event) => {
            event.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (event) => {
            event.preventDefault();
            uploadZone.classList.remove('dragover');
            fileInput.files = event.dataTransfer.files;
            displayFiles();
        });

        fileInput.addEventListener('change', displayFiles);
    }

    if (categorySelect && categoryButtons.length > 0) {
        categoryButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const categoryId = button.getAttribute('data-category-id');
                if (!categoryId) {
                    return;
                }

                categorySelect.value = categoryId;
                categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
                categorySelect.focus();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const firstError = document.querySelector('.du-input.error, .du-select.error, .du-textarea.error');
        if (firstError) {
            firstError.focus();
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endsection
