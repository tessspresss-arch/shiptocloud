@extends('layouts.app')

@section('title', isset($ordonnance) && $ordonnance ? 'Modifier une Ordonnance' : 'Creer une Ordonnance')
@section('topbar_subtitle', 'Prescription clinique, apercu direct et validation plus fluide dans un header aligne avec les modules premium.')

@section('content')
<style>
    :root {
        --rx-primary: #1f78c8;
        --rx-primary-strong: #145d99;
        --rx-accent: #0ea5e9;
        --rx-success: #0f9f77;
        --rx-warning: #d97706;
        --rx-danger: #dc2626;
        --rx-text: #15314d;
        --rx-muted: #64809b;
        --rx-border: #d8e4f0;
    }
    .rx-create-page { padding: 16px 0 36px; }
    .rx-create-shell { max-width: 100%; }
    .rx-create-form { display: flex; flex-direction: column; gap: 18px; }
    .rx-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(280px, .9fr);
        gap: 18px;
        padding: 24px 26px;
        border: 1px solid #d8e5fb;
        border-radius: 28px;
        background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.14), transparent 34%), linear-gradient(135deg, #ffffff 0%, #f7fbff 100%);
        box-shadow: 0 28px 50px rgba(15, 23, 42, 0.08);
    }
    .rx-hero-copy { display: flex; flex-direction: column; gap: 14px; }
    .rx-kicker-row { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
    .rx-kicker {
        display: inline-flex; align-items: center; gap: 8px; width: fit-content; padding: 8px 14px; border-radius: 999px;
        border: 1px solid rgba(37, 99, 235, 0.18); background: rgba(255, 255, 255, 0.82); color: #1d4ed8;
        font-size: .76rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase;
    }
    .rx-breadcrumbs {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px; color: #6380a0; font-size: .82rem; font-weight: 700;
    }
    .rx-breadcrumbs a { color: #2c64b7; text-decoration: none; }
    .rx-breadcrumbs a:hover { color: #154d93; text-decoration: none; }
    .rx-breadcrumb-sep { color: #93a9c3; }
    .rx-title { margin: 0; color: #12376b; font-size: clamp(2rem, 2.6vw, 2.8rem); font-weight: 900; line-height: 1.02; }
    .rx-subtitle { margin: 0; max-width: 800px; color: #52719b; font-size: 1rem; line-height: 1.7; }
    .rx-hero-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .rx-hero-stat {
        padding: 14px 16px; border-radius: 20px; border: 1px solid #dbe8fb; background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 16px 30px rgba(37, 99, 235, 0.06);
    }
    .rx-hero-stat-label { display: block; color: #6b85a8; font-size: .74rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .rx-hero-stat-value { display: block; margin-top: 6px; color: #12376b; font-size: 1.42rem; font-weight: 900; }
    .rx-hero-side { display: grid; gap: 14px; align-content: space-between; }
    .rx-context-card, .rx-action-card, .rx-surface, .rx-preview-sheet {
        border: 1px solid #dce8f8; border-radius: 24px; background: #ffffff; box-shadow: 0 22px 44px rgba(15, 23, 42, 0.06);
    }
    .rx-context-card { padding: 18px; display: grid; gap: 12px; }
    .rx-context-eyebrow { color: #6f89aa; font-size: .76rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
    .rx-context-name { margin: 0; color: #12376b; font-size: 1.28rem; font-weight: 900; }
    .rx-context-meta { display: flex; flex-wrap: wrap; gap: 8px; }
    .rx-chip {
        display: inline-flex; align-items: center; gap: 8px; padding: 9px 12px; border-radius: 999px;
        background: #eef5ff; color: #2554a7; font-size: .86rem; font-weight: 700;
    }
    .rx-chip-muted { background: #f7f9fd; color: #627b9e; }
    .rx-action-card { padding: 18px; display: grid; gap: 12px; }
    .rx-action-card h2 { margin: 0; color: #12376b; font-size: 1.04rem; font-weight: 900; }
    .rx-action-card p { margin: 0; color: #6782a6; line-height: 1.55; }
    .rx-inline-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .rx-top-nav { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .rx-inline-pill {
        display: inline-flex; align-items: center; gap: 8px; padding: 10px 12px; border-radius: 14px;
        border: 1px solid #d9e6fb; background: #f8fbff; color: #2858aa; font-size: .86rem; font-weight: 700;
    }
    .rx-chip-row { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px; }
    .rx-stat-chip {
        display: inline-flex; align-items: center; gap: 8px; min-height: 38px; padding: 0 14px; border-radius: 999px;
        border: 1px solid #d7e4f8; background: rgba(255, 255, 255, 0.84); color: #587292; font-size: .84rem; font-weight: 800;
    }
    .rx-stat-chip strong { color: #12376b; font-weight: 900; }
    .rx-flow-strip {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px;
    }
    .rx-flow-card {
        padding: 16px 18px; border-radius: 22px; border: 1px solid #dce8f8; background: linear-gradient(180deg, rgba(255,255,255,.94), rgba(247,251,255,.98));
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.06); display: grid; gap: 8px;
    }
    .rx-flow-head { display: flex; align-items: center; gap: 10px; color: #12376b; font-weight: 900; }
    .rx-flow-icon {
        width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(14, 165, 233, 0.18)); color: #1f5ea8; flex-shrink: 0;
    }
    .rx-flow-card p { margin: 0; color: #6882a6; line-height: 1.58; }
    .rx-builder { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(360px, .85fr); gap: 20px; align-items: start; }
    .rx-stack { display: grid; gap: 18px; }
    .rx-surface { padding: 22px; overflow: hidden; }
    .rx-surface-head {
        display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 14px;
        margin: -22px -22px 18px;
        padding: 16px 22px;
        border-bottom: 1px solid #eef3fb;
        background: #f8fbff;
    }
    .rx-section-copy { display: flex; align-items: flex-start; gap: 10px; min-width: 0; }
    .rx-section-copy > div { min-width: 0; }
    .rx-section-icon {
        width: 32px; height: 32px; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid #c9def6; background: #e7f3ff; color: #153b84; flex-shrink: 0;
    }
    .rx-surface-head h2, .rx-preview-sheet h2 { margin: 0; color: #153b84; font-size: 1.03rem; font-weight: 800; }
    .rx-surface-head p, .rx-preview-sheet p { margin: 4px 0 0; color: #6a84a7; line-height: 1.6; }
    .rx-section-tag {
        display: inline-flex; align-items: center; min-height: 34px; padding: 0 12px; border-radius: 999px; border: 1px solid #d7e4f8;
        background: #f7fbff; color: #587292; font-size: .78rem; font-weight: 800; white-space: nowrap;
    }
    .rx-grid-2, .rx-grid-3 { display: grid; gap: 16px; }
    .rx-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .rx-grid-3 { grid-template-columns: 1.2fr .9fr .9fr; }
    .rx-field { display: grid; gap: 8px; }
    .rx-field-wide { grid-column: 1 / -1; }
    .rx-label {
        display: flex; align-items: center; justify-content: space-between; gap: 10px; color: #25456f;
        font-size: .86rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
    }
    .rx-label-note { color: #6d86a8; font-size: .72rem; font-weight: 700; letter-spacing: normal; text-transform: none; }
    .rx-input, .rx-select, .rx-textarea {
        width: 100%; border: 1px solid #cfe0f8; border-radius: 16px; background: #fbfdff; color: #12376b;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8); transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .rx-input, .rx-select { min-height: 56px; padding: 0 16px; }
    .rx-textarea { min-height: 150px; padding: 14px 16px; resize: vertical; }
    .rx-input:focus, .rx-select:focus, .rx-textarea:focus {
        outline: none; border-color: #60a5fa; box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.18); transform: translateY(-1px);
    }
    .rx-field-error { color: #d03357; font-size: .85rem; font-weight: 700; }
    .rx-search-shell { position: relative; }
    .rx-search-shell .rx-input { padding-right: 46px; }
    .rx-search-shell i { position: absolute; top: 50%; right: 16px; transform: translateY(-50%); color: #7a95b8; }
    .rx-search-results {
        position: absolute; top: calc(100% + 8px); left: 0; right: 0; z-index: 30; display: none; padding: 10px;
        border: 1px solid #d7e4fb; border-radius: 18px; background: #ffffff; box-shadow: 0 24px 48px rgba(15, 23, 42, 0.12);
    }
    .rx-search-results.is-open { display: grid; gap: 6px; }
    .rx-search-option {
        display: grid; gap: 2px; width: 100%; padding: 12px 14px; border: 0; border-radius: 14px; background: #f7fbff;
        color: #16386c; text-align: left; cursor: pointer; transition: background .2s ease, transform .2s ease;
    }
    .rx-search-option:hover, .rx-search-option:focus { background: #eaf3ff; transform: translateY(-1px); outline: none; }
    .rx-search-option small { color: #6d86a8; }
    .rx-empty-search { padding: 10px 12px; color: #7a95b8; font-size: .88rem; }
    .rx-patient-card {
        display: grid; gap: 14px; padding: 18px; border-radius: 22px; border: 1px solid #d6e6fb;
        background: linear-gradient(180deg, rgba(244, 249, 255, 0.95), rgba(255, 255, 255, 0.98));
    }
    .rx-patient-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px; }
    .rx-patient-name { margin: 0; color: #12376b; font-size: 1.18rem; font-weight: 900; }
    .rx-patient-subtitle { margin: 4px 0 0; color: #6882a6; }
    .rx-patient-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .rx-mini-panel { padding: 13px 14px; border-radius: 18px; border: 1px solid #d8e6fb; background: #ffffff; }
    .rx-mini-panel strong {
        display: block; color: #12376b; font-size: .82rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em;
    }
    .rx-mini-panel span { display: block; margin-top: 8px; color: #5b7498; line-height: 1.6; white-space: pre-line; }
    .rx-doctor-card {
        display: grid; gap: 8px; padding: 16px 18px; border-radius: 18px; border: 1px solid #d7e5fb;
        background: linear-gradient(180deg, #ffffff, #f7fbff);
    }
    .rx-doctor-title { display: flex; align-items: center; gap: 10px; color: #12376b; font-size: 1rem; font-weight: 900; }
    .rx-doctor-card p { margin: 0; color: #607a9f; }
    .rx-template-box {
        display: grid; gap: 14px; padding: 16px; border-radius: 20px; border: 1px dashed #cfe0f8; background: #f9fbff;
    }
    .rx-template-preview {
        min-height: 68px; padding: 14px 16px; border-radius: 16px; background: #ffffff; border: 1px solid #dde8f9;
        color: #5d7699; line-height: 1.6; white-space: pre-line;
    }
    .rx-med-rows { display: grid; gap: 14px; }
    .rx-med-row {
        display: grid; gap: 14px; padding: 16px; border-radius: 22px; border: 1px solid #d7e4f8;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }
    .rx-med-row-top { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; }
    .rx-med-row-title { color: #12376b; font-size: 1rem; font-weight: 900; }
    .rx-med-grid { display: grid; grid-template-columns: 1.4fr repeat(4, minmax(0, 1fr)); gap: 14px; align-items: start; }
    .rx-med-meta { display: flex; flex-wrap: wrap; gap: 8px; }
    .rx-med-meta .rx-chip { padding: 7px 10px; font-size: .8rem; }
    .rx-button, .rx-link {
        display: inline-flex; align-items: center; justify-content: center; gap: 10px; min-height: 52px; padding: 0 18px;
        border-radius: 16px; border: 1px solid transparent; font-weight: 800; font-size: .96rem; text-decoration: none; cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease;
    }
    .rx-button:hover, .rx-link:hover { transform: translateY(-1px); box-shadow: 0 18px 30px rgba(15, 23, 42, 0.12); }
    .rx-button-primary { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; }
    .rx-button-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; }
    .rx-button-light { border-color: #d6e3f8; background: #ffffff; color: #1e4f9b; }
    .rx-button-warm { border-color: #f5d1a2; background: #fff7ed; color: #b45309; }
    .rx-button-danger { border-color: #f5d1d8; background: #fff4f6; color: #c2415c; }
    .rx-button-soft { border-color: #d7e4f8; background: #f7fbff; color: #2854a4; }
    .rx-button[disabled] { opacity: .58; cursor: not-allowed; transform: none; box-shadow: none; }
    .rx-preview-pane { position: sticky; top: 92px; display: grid; gap: 16px; }
    .rx-preview-sheet { overflow: hidden; }
    .rx-preview-head {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 16px 22px;
        border-bottom: 1px solid #eef3fb; background: #f8fbff;
    }
    .rx-preview-head strong { display: block; color: #153b84; font-size: 1.03rem; font-weight: 800; }
    .rx-preview-head span { color: #6982a7; }
    .rx-preview-body { display: grid; gap: 18px; padding: 22px; }
    .rx-preview-section { display: grid; gap: 10px; }
    .rx-preview-section h3 { margin: 0; color: #12376b; font-size: .98rem; font-weight: 900; }
    .rx-preview-section p, .rx-preview-section li { margin: 0; color: #4f698f; line-height: 1.7; }
    .rx-preview-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .rx-preview-card { padding: 14px 16px; border-radius: 18px; background: #f8fbff; border: 1px solid #dbe6f8; }
    .rx-preview-card strong {
        display: block; color: #12376b; font-size: .76rem; letter-spacing: .08em; text-transform: uppercase;
    }
    .rx-preview-card span { display: block; margin-top: 6px; color: #547096; line-height: 1.6; white-space: pre-line; }
    .rx-preview-list { display: grid; gap: 10px; list-style: none; padding: 0; margin: 0; }
    .rx-preview-item { padding: 14px 16px; border-radius: 18px; border: 1px solid #d9e6fb; background: #ffffff; }
    .rx-preview-item strong { display: block; color: #12376b; font-size: .98rem; font-weight: 900; }
    .rx-preview-item span { display: block; margin-top: 6px; color: #5d7699; line-height: 1.6; white-space: pre-line; }
    .rx-preview-empty { padding: 14px 16px; border-radius: 18px; background: #f8fbff; border: 1px dashed #d3e1f9; color: #6f88aa; }
    .rx-footer-bar {
        position: sticky; bottom: 16px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;
        padding: 16px 18px; border: 1px solid #d8e5fb; border-radius: 24px; background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(14px); box-shadow: 0 22px 40px rgba(15, 23, 42, 0.08);
    }
    .rx-footer-meta { display: flex; flex-wrap: wrap; gap: 10px; }
    .rx-footer-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 10px; }
    .rx-mobile-preview-link { display: none; }
    .rx-error-summary { padding: 18px 20px; border-radius: 22px; border: 1px solid #f5ccd4; background: #fff4f6; color: #b83257; }
    .rx-error-summary p { margin: 0 0 10px; font-weight: 900; }
    .rx-error-summary ul { margin: 0; padding-left: 18px; }
    .rx-empty-state { padding: 16px; border-radius: 18px; border: 1px dashed #d4e1f8; background: #f9fbff; color: #6b85a8; }

    body.dark-mode .rx-hero {
        border-color: #27425f;
        background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.18), transparent 34%), linear-gradient(135deg, #122033 0%, #16263b 100%);
        box-shadow: 0 26px 46px rgba(5, 14, 25, 0.42);
    }
    body.dark-mode .rx-kicker,
    body.dark-mode .rx-hero-stat,
    body.dark-mode .rx-context-card,
    body.dark-mode .rx-action-card,
    body.dark-mode .rx-surface,
    body.dark-mode .rx-preview-sheet,
    body.dark-mode .rx-patient-card,
    body.dark-mode .rx-mini-panel,
    body.dark-mode .rx-doctor-card,
    body.dark-mode .rx-template-preview,
    body.dark-mode .rx-med-row,
    body.dark-mode .rx-preview-card,
    body.dark-mode .rx-preview-item,
    body.dark-mode .rx-footer-bar,
    body.dark-mode .rx-search-results,
    body.dark-mode .rx-search-option {
        background: #122033;
        border-color: #27425f;
        box-shadow: 0 18px 32px -28px rgba(5, 14, 25, 0.82);
    }
    body.dark-mode .rx-kicker,
    body.dark-mode .rx-button-light,
    body.dark-mode .rx-button-soft,
    body.dark-mode .rx-button-warm,
    body.dark-mode .rx-button-danger,
    body.dark-mode .rx-inline-pill,
    body.dark-mode .rx-chip-muted,
    body.dark-mode .rx-empty-state {
        background: #16263b;
        border-color: #2a4663;
        color: #c8daee;
    }
    body.dark-mode .rx-title,
    body.dark-mode .rx-hero-stat-value,
    body.dark-mode .rx-context-name,
    body.dark-mode .rx-action-card h2,
    body.dark-mode .rx-surface-head h2,
    body.dark-mode .rx-preview-sheet h2,
    body.dark-mode .rx-patient-name,
    body.dark-mode .rx-mini-panel strong,
    body.dark-mode .rx-doctor-title,
    body.dark-mode .rx-med-row-title,
    body.dark-mode .rx-preview-head strong,
    body.dark-mode .rx-preview-section h3,
    body.dark-mode .rx-preview-item strong {
        color: #eef5ff;
    }
    body.dark-mode .rx-subtitle,
    body.dark-mode .rx-hero-stat-label,
    body.dark-mode .rx-context-eyebrow,
    body.dark-mode .rx-action-card p,
    body.dark-mode .rx-surface-head p,
    body.dark-mode .rx-preview-sheet p,
    body.dark-mode .rx-patient-subtitle,
    body.dark-mode .rx-mini-panel span,
    body.dark-mode .rx-doctor-card p,
    body.dark-mode .rx-template-preview,
    body.dark-mode .rx-preview-head span,
    body.dark-mode .rx-preview-section p,
    body.dark-mode .rx-preview-section li,
    body.dark-mode .rx-preview-card span,
    body.dark-mode .rx-preview-item span,
    body.dark-mode .rx-search-option small,
    body.dark-mode .rx-empty-search,
    body.dark-mode .rx-empty-state {
        color: #93afca;
    }
    body.dark-mode .rx-chip {
        background: #183251;
        color: #d7e8fb;
    }
    body.dark-mode .rx-surface-head,
    body.dark-mode .rx-preview-head {
        background: rgba(18, 49, 79, 0.7);
        border-bottom-color: #27425f;
    }
    body.dark-mode .rx-section-icon {
        background: #16324f;
        color: #8ec5ff;
        border-color: #2f4f72;
    }
    body.dark-mode .rx-label {
        color: #d8e6f7;
    }
    body.dark-mode .rx-label-note {
        color: #8da9c7;
    }
    body.dark-mode .rx-input,
    body.dark-mode .rx-select,
    body.dark-mode .rx-textarea {
        background: #0f1a2a;
        border-color: #2a4663;
        color: #edf4ff;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
    }
    body.dark-mode .rx-input::placeholder,
    body.dark-mode .rx-textarea::placeholder {
        color: #7e9ab8;
    }
    body.dark-mode .rx-input:focus,
    body.dark-mode .rx-select:focus,
    body.dark-mode .rx-textarea:focus {
        border-color: #4f8ff7;
        box-shadow: 0 0 0 4px rgba(79, 143, 247, 0.18);
    }
    body.dark-mode .rx-search-shell i {
        color: #8da9c7;
    }
    body.dark-mode .rx-search-option:hover,
    body.dark-mode .rx-search-option:focus {
        background: #183251;
    }
    body.dark-mode .rx-template-box,
    body.dark-mode .rx-preview-empty {
        background: #16263b;
        border-color: #2a4663;
        color: #9ab5d2;
    }
    body.dark-mode .rx-preview-card,
    body.dark-mode .rx-preview-item,
    body.dark-mode .rx-doctor-card,
    body.dark-mode .rx-mini-panel,
    body.dark-mode .rx-template-preview {
        background: #16263b;
    }
    body.dark-mode .rx-footer-bar {
        background: rgba(18, 32, 51, 0.94);
        border-color: #27425f;
        backdrop-filter: blur(14px);
    }
    body.dark-mode .rx-button-light:hover,
    body.dark-mode .rx-button-soft:hover,
    body.dark-mode .rx-button-warm:hover,
    body.dark-mode .rx-button-danger:hover {
        box-shadow: 0 18px 30px rgba(5, 14, 25, 0.48);
    }
    body.dark-mode .rx-button-warm {
        background: #3a2713;
        border-color: #7c5427;
        color: #f8cf99;
    }
    body.dark-mode .rx-button-danger {
        background: #3a1d26;
        border-color: #7a3044;
        color: #f4b7c4;
    }
    body.dark-mode .rx-field-error {
        color: #f8b4c4;
    }
    body.dark-mode .rx-breadcrumbs,
    body.dark-mode .rx-flow-card p,
    body.dark-mode .rx-section-tag {
        color: #9ab5d2;
    }
    body.dark-mode .rx-breadcrumbs a {
        color: #8ec5ff;
    }
    body.dark-mode .rx-flow-card,
    body.dark-mode .rx-section-tag {
        background: #122033;
        border-color: #27425f;
    }
    body.dark-mode .rx-error-summary {
        background: #3a1824;
        border-color: #7c2d43;
        color: #ffd4dd;
    }
    body.dark-mode .rx-error-summary p,
    body.dark-mode .rx-error-summary li {
        color: #ffd4dd;
    }
    @media (max-width: 1360px) { .rx-builder { grid-template-columns: minmax(0, 1fr); } .rx-preview-pane { position: static; } }
    @media (max-width: 1120px) {
        .rx-hero, .rx-grid-3, .rx-med-grid, .rx-patient-grid, .rx-flow-strip { grid-template-columns: 1fr; }
        .rx-hero-stats, .rx-grid-2, .rx-preview-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 760px) {
        .rx-create-page { padding-top: 8px; }
        .rx-hero, .rx-surface, .rx-preview-sheet, .rx-footer-bar { padding-left: 16px; padding-right: 16px; }
        .rx-hero-stats, .rx-grid-2, .rx-preview-grid { grid-template-columns: 1fr; }
        .rx-create-form { padding-bottom: 110px; }
        .rx-search-results {
            max-height: min(260px, 46vh);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .rx-search-option {
            padding: 14px 16px;
        }
        .rx-input,
        .rx-select,
        .rx-textarea {
            font-size: 16px;
        }
        .rx-input:focus,
        .rx-select:focus,
        .rx-textarea:focus {
            transform: none;
        }
        .rx-preview-head {
            align-items: flex-start;
            flex-direction: column;
        }
        .rx-top-nav,
        .rx-inline-actions {
            width: 100%;
        }
        .rx-surface-head {
            padding: 14px 16px;
            margin-left: -16px;
            margin-right: -16px;
        }
        .rx-footer-bar {
            position: sticky;
            bottom: calc(env(safe-area-inset-bottom, 0px) + 8px);
            z-index: 30;
            padding-top: 14px;
            padding-bottom: calc(14px + env(safe-area-inset-bottom, 0px));
        }
        .rx-mobile-preview-link {
            display: inline-flex;
        }
        .rx-footer-actions, .rx-inline-actions { width: 100%; }
        .rx-footer-actions .rx-button, .rx-footer-actions .rx-link, .rx-inline-actions .rx-button, .rx-inline-actions .rx-link { width: 100%; }
    }
</style>

<div class="rx-create-page">
    <div class="rx-create-shell">
        <form method="POST" action="{{ $formAction }}" id="ordonnanceForm" class="rx-create-form">
            @csrf
            @if($isEditing)
                @method('PUT')
            @endif
            <input type="hidden" name="print_after_save" id="print_after_save" value="0">

            <section class="rx-hero">
                <div class="rx-hero-copy">
                    <div class="rx-kicker-row">
                        <span class="rx-kicker"><i class="fas fa-prescription-bottle-medical"></i> Prescription moderne</span>
                        <span class="rx-inline-pill"><i class="fas fa-wave-square"></i> {{ $isEditing ? 'Mode edition' : 'Mode creation' }}</span>
                    </div>
                    <div>
                        <h1 class="rx-title">{{ $isEditing ? 'Mettre a jour une ordonnance en gardant le controle clinique' : 'Creer une ordonnance plus rapide, plus claire, plus clinique' }}</h1>
                        <p class="rx-subtitle">
                            {{ $isEditing
                                ? "Ajustez le patient, le prescripteur et le traitement en conservant un apercu temps reel avant sauvegarde ou export."
                                : "Recherchez le patient, pre-remplissez le prescripteur, composez le traitement ligne par ligne et verifiez l'ordonnance finale en direct avant enregistrement ou impression." }}
                        </p>
                    </div>
                    <div class="rx-chip-row">
                        <span class="rx-stat-chip"><i class="fas fa-users"></i><strong>{{ collect($patientDirectoryData ?? [])->count() }}</strong> patients indexables</span>
                        <span class="rx-stat-chip"><i class="fas fa-capsules"></i><strong>{{ collect($medicamentCatalogData ?? [])->count() }}</strong> medicaments</span>
                        <span class="rx-stat-chip"><i class="fas fa-layer-group"></i><strong>{{ $templateCount }}</strong> modeles</span>
                    </div>
                </div>

                <div class="rx-hero-side">
                    <div class="rx-action-card">
                        <div>
                            <h2>Actions de page</h2>
                            <p>Gardez les utilitaires a droite sans surcharger l'entete ni concurrencer l'action principale du formulaire.</p>
                        </div>
                        <div class="rx-top-nav">
                            <a href="{{ route('ordonnances.index') }}" class="rx-link rx-button-light">
                                <i class="fas fa-arrow-left"></i> Retour a la liste
                            </a>
                        </div>
                        <div class="rx-inline-actions">
                            <button type="button" class="rx-button rx-button-light" id="quickPrintBtn">
                                <i class="fas fa-print"></i> Imprimer
                            </button>
                            <button type="button" class="rx-button rx-button-soft" id="quickPdfBtn">
                                <i class="fas fa-file-pdf"></i> Telecharger PDF
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            @if ($errors->any())
                <div class="rx-error-summary" role="alert" aria-live="polite">
                    <p><i class="fas fa-circle-exclamation"></i> Certaines informations doivent etre corrigees.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rx-builder">
                <div class="rx-stack">
                    <section class="rx-surface">
                        <div class="rx-surface-head">
                            <div class="rx-section-copy">
                                <span class="rx-section-icon"><i class="fas fa-user-injured"></i></span>
                                <div>
                                <h2>Patient et contexte clinique</h2>
                                <p>Le patient, la consultation source et le medecin sont definis ici avant la prescription.</p>
                                </div>
                            </div>
                            <span class="rx-section-tag">Contexte</span>
                        </div>

                        <div class="rx-grid-3">
                            <div class="rx-field rx-field-wide">
                                <label class="rx-label" for="patient_search">
                                    <span>Recherche patient</span>
                                    <span class="rx-label-note">Nom, prenom, dossier, telephone</span>
                                </label>
                                <div class="rx-search-shell">
                                    <input type="hidden" name="patient_id" id="patient_id" value="{{ $selectedPatientId }}">
                                    <input id="patient_search" type="text" class="rx-input" value="{{ $selectedPatient['label'] ?? '' }}" placeholder="Rechercher rapidement un patient..." autocomplete="off">
                                    <i class="fas fa-magnifying-glass"></i>
                                    <div id="patientResults" class="rx-search-results"></div>
                                </div>
                                @error('patient_id')
                                    <span class="rx-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="rx-field">
                                <label class="rx-label" for="consultation_id">
                                    <span>Consultation associee</span>
                                    <span class="rx-label-note">Optionnelle</span>
                                </label>
                                <select name="consultation_id" id="consultation_id" class="rx-select">
                                    <option value="">Aucune consultation</option>
                                    @foreach($consultations as $consultation)
                                        <option value="{{ $consultation->id }}" @selected($selectedConsultationId === (string) $consultation->id)>
                                            {{ optional($consultation->date_consultation)->format('d/m/Y') }} - {{ optional($consultation->patient)->prenom }} {{ optional($consultation->patient)->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="rx-field">
                                <label class="rx-label" for="date_prescription">
                                    <span>Date de prescription</span>
                                    <span class="rx-label-note">Obligatoire</span>
                                </label>
                                <input id="date_prescription" type="date" name="date_prescription" class="rx-input" value="{{ $initialDateValue }}" required>
                                @error('date_prescription')
                                    <span class="rx-field-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="rx-field" style="margin-top: 16px;">
                            <label class="rx-label">
                                <span>Prescripteur</span>
                                <span class="rx-label-note">Rempli automatiquement quand un medecin connecte est reconnu</span>
                            </label>
                            @if($currentMedecin)
                                <input type="hidden" name="medecin_id" id="medecin_id" value="{{ $currentMedecin->id }}">
                                <div class="rx-doctor-card" id="doctorCard">
                                    <div class="rx-doctor-title">
                                        <i class="fas fa-user-doctor"></i>
                                        <span>{{ trim(($currentMedecin->civilite ?? 'Dr.') . ' ' . $currentMedecin->prenom . ' ' . $currentMedecin->nom) }}</span>
                                    </div>
                                    <p>{{ $currentMedecin->specialite ?: 'Medecin generaliste' }}</p>
                                    <p>{{ $currentMedecin->email ?: 'Aucune adresse email' }} @if($currentMedecin->telephone) | {{ $currentMedecin->telephone }} @endif</p>
                                </div>
                            @else
                                <select name="medecin_id" id="medecin_id" class="rx-select" required>
                                    <option value="">Selectionner un medecin</option>
                                    @foreach($medecins as $medecin)
                                        <option value="{{ $medecin->id }}" @selected($selectedMedecinId === (string) $medecin->id)>
                                            {{ trim(($medecin->civilite ?? 'Dr.') . ' ' . $medecin->prenom . ' ' . $medecin->nom) }} @if($medecin->specialite)- {{ $medecin->specialite }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('medecin_id')
                                    <span class="rx-field-error">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>

                        <div class="rx-patient-card" id="patientInfoCard" style="margin-top: 18px;">
                            <div class="rx-patient-head">
                                <div>
                                    <h3 class="rx-patient-name" id="patientInfoName">{{ $selectedPatient['label'] ?? 'Aucun patient selectionne' }}</h3>
                                    <p class="rx-patient-subtitle" id="patientInfoSubtitle">{{ $selectedPatient['numero_dossier'] ?? 'Selectionnez un patient pour afficher son contexte medical.' }}</p>
                                </div>
                                <div class="rx-context-meta">
                                    <span class="rx-chip rx-chip-muted"><i class="fas fa-user-clock"></i> <span id="patientInfoAge">{{ isset($selectedPatient['age']) && $selectedPatient['age'] !== null ? $selectedPatient['age'] . ' ans' : 'Age inconnu' }}</span></span>
                                    <span class="rx-chip rx-chip-muted"><i class="fas fa-envelope"></i> <span id="patientInfoEmail">{{ $selectedPatient['email'] ?? 'Email non renseigne' }}</span></span>
                                </div>
                            </div>

                            <div class="rx-patient-grid">
                                <div class="rx-mini-panel">
                                    <strong>Allergies</strong>
                                    <span id="patientInfoAllergies">{{ $selectedPatient['allergies'] ?? 'Aucune allergie documentee.' }}</span>
                                </div>
                                <div class="rx-mini-panel">
                                    <strong>Traitements actifs</strong>
                                    <span id="patientInfoTreatments">{{ $selectedPatient['traitements'] ?? 'Aucun traitement actif connu.' }}</span>
                                </div>
                                <div class="rx-mini-panel">
                                    <strong>Points de dossier</strong>
                                    <span id="patientInfoNotes">{{ $selectedPatient['notes'] ?? 'Aucun point d attention complementaire.' }}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rx-surface">
                        <div class="rx-surface-head">
                            <div class="rx-section-copy">
                                <span class="rx-section-icon"><i class="fas fa-layer-group"></i></span>
                                <div>
                                <h2>Modeles d'ordonnance</h2>
                                <p>Accedez a des modeles reutilisables pour gagner du temps sur les prescriptions courantes.</p>
                                </div>
                            </div>
                            <span class="rx-section-tag">{{ $templateCount }} modele(s)</span>
                        </div>

                        @if($templateCount > 0)
                            <div class="rx-template-box">
                                <div class="rx-grid-2">
                                    <div class="rx-field">
                                        <label class="rx-label" for="ordonnance_template_id">
                                            <span>Modele predefini</span>
                                            <span class="rx-label-note">General ou personnel</span>
                                        </label>
                                        <select id="ordonnance_template_id" class="rx-select">
                                            <option value="">Selectionner un modele</option>
                                            @foreach($ordonnanceTemplates as $template)
                                                <option value="{{ $template->id }}">
                                                    {{ $template->nom }}{{ $template->est_template_general ? ' - General' : ' - Personnel' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="rx-field">
                                        <label class="rx-label">
                                            <span>Action</span>
                                            <span class="rx-label-note">Injecte dans le formulaire</span>
                                        </label>
                                        <button type="button" class="rx-button rx-button-light" id="applyTemplateBtn">
                                            <i class="fas fa-wand-magic-sparkles"></i> Appliquer le modele
                                        </button>
                                    </div>
                                </div>
                                <div class="rx-template-preview" id="templatePreviewBox">Selectionnez un modele pour voir le contexte, les consignes et les medicaments proposes avant chargement.</div>
                            </div>
                        @else
                            <div class="rx-empty-state">
                                Aucun modele d'ordonnance actif pour le moment. Vous pouvez en creer depuis le module Parametres.
                            </div>
                        @endif
                    </section>

                    <section class="rx-surface">
                        <div class="rx-surface-head">
                            <div class="rx-section-copy">
                                <span class="rx-section-icon"><i class="fas fa-notes-medical"></i></span>
                                <div>
                                <h2>Diagnostic et instructions generales</h2>
                                <p>Le diagnostic et les consignes globales alimentent l'apercu et peuvent etre repris dans l'impression.</p>
                                </div>
                            </div>
                            <span class="rx-section-tag">Synthese clinique</span>
                        </div>

                        <div class="rx-grid-2">
                            <div class="rx-field">
                                <label class="rx-label" for="diagnostic">
                                    <span>Diagnostic / contexte</span>
                                    <span class="rx-label-note">Visible dans l'apercu</span>
                                </label>
                                <textarea name="diagnostic" id="diagnostic" class="rx-textarea" placeholder="Saisir le contexte, le motif ou le diagnostic principal...">{{ $initialDiagnostic }}</textarea>
                                @error('diagnostic')
                                    <span class="rx-field-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="rx-field">
                                <label class="rx-label" for="instructions">
                                    <span>Instructions generales</span>
                                    <span class="rx-label-note">Conseils, surveillance, recommandations</span>
                                </label>
                                <textarea name="instructions" id="instructions" class="rx-textarea" placeholder="Indiquer les recommandations generales pour le patient...">{{ $initialInstructions }}</textarea>
                                @error('instructions')
                                    <span class="rx-field-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rx-surface">
                        <div class="rx-surface-head">
                            <div class="rx-section-copy">
                                <span class="rx-section-icon"><i class="fas fa-capsules"></i></span>
                                <div>
                                <h2>Medicaments prescrits</h2>
                                <p>Ajoutez plusieurs lignes, recherchez rapidement le bon medicament et reutilisez sa posologie habituelle.</p>
                                </div>
                            </div>
                            <div class="rx-top-nav">
                                <span class="rx-section-tag">Traitement</span>
                                <button type="button" class="rx-button rx-button-success" id="addMedicationBtn">
                                    <i class="fas fa-plus"></i> Ajouter medicament
                                </button>
                            </div>
                        </div>

                        <div id="medicationRows" class="rx-med-rows">
                            @foreach($prescriptionRows as $index => $row)
                                <article class="rx-med-row" data-index="{{ $index }}">
                                    <div class="rx-med-row-top">
                                        <div class="rx-med-row-title">Ligne medicament #{{ $index + 1 }}</div>
                                        @if($index > 0)
                                            <button type="button" class="rx-button rx-button-danger js-remove-medication">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        @endif
                                    </div>

                                    <div class="rx-med-grid">
                                        <div class="rx-field">
                                            <label class="rx-label">
                                                <span>Medicament</span>
                                                <span class="rx-label-note">Recherche avec suggestions</span>
                                            </label>
                                            <div class="rx-search-shell">
                                                <input type="hidden" name="medicaments[{{ $index }}][medicament_id]" class="js-medication-id" value="{{ $row['medicament_id'] ?? '' }}">
                                                <input type="hidden" name="medicaments[{{ $index }}][medicament_label]" class="js-medication-label" value="{{ $row['display_label'] ?? '' }}">
                                                <input type="text" class="rx-input js-medication-search" value="{{ $row['display_label'] ?? '' }}" placeholder="Nom commercial, DCI, classe therapeutique..." autocomplete="off">
                                                <i class="fas fa-capsules"></i>
                                                <div class="rx-search-results js-medication-results"></div>
                                            </div>
                                            <div class="rx-med-meta js-medication-meta"></div>
                                            @error("medicaments.$index.medicament_id")
                                                <span class="rx-field-error">{{ $message }}</span>
                                            @enderror
                                            @error("medicaments.$index.medicament_label")
                                                <span class="rx-field-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="rx-field">
                                            <label class="rx-label" for="med_posologie_{{ $index }}">Posologie</label>
                                            <input id="med_posologie_{{ $index }}" type="text" name="medicaments[{{ $index }}][posologie]" class="rx-input js-medication-field" value="{{ $row['posologie'] ?? '' }}" placeholder="Ex: 1 comprime matin et soir">
                                            @error("medicaments.$index.posologie")
                                                <span class="rx-field-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="rx-field">
                                            <label class="rx-label" for="med_duree_{{ $index }}">Duree</label>
                                            <input id="med_duree_{{ $index }}" type="text" name="medicaments[{{ $index }}][duree]" class="rx-input js-medication-field" value="{{ $row['duree'] ?? '' }}" placeholder="Ex: 7 jours">
                                            @error("medicaments.$index.duree")
                                                <span class="rx-field-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="rx-field">
                                            <label class="rx-label" for="med_quantite_{{ $index }}">Quantite</label>
                                            <input id="med_quantite_{{ $index }}" type="text" name="medicaments[{{ $index }}][quantite]" class="rx-input js-medication-field" value="{{ $row['quantite'] ?? '' }}" placeholder="Ex: 14">
                                        </div>

                                        <div class="rx-field">
                                            <label class="rx-label" for="med_instruction_{{ $index }}">Instruction specifique</label>
                                            <input id="med_instruction_{{ $index }}" type="text" name="medicaments[{{ $index }}][instructions]" class="rx-input js-medication-field" value="{{ $row['instructions'] ?? '' }}" placeholder="Avant repas, soir, surveiller...">
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                </div>

                <aside class="rx-preview-pane" id="previewSection">
                    <section class="rx-preview-sheet">
                        <div class="rx-preview-head">
                            <div class="rx-section-copy">
                                <span class="rx-section-icon"><i class="fas fa-eye"></i></span>
                                <div>
                                    <strong>Apercu ordonnance en temps reel</strong>
                                    <span>Version imprimee / PDF telle qu'elle apparaitra au patient</span>
                                </div>
                            </div>
                            <span class="rx-chip"><i class="fas fa-eye"></i> Live preview</span>
                        </div>

                        <div class="rx-preview-body" id="previewSheet">
                            <div class="rx-preview-section">
                                <div class="rx-preview-grid">
                                    <div class="rx-preview-card">
                                        <strong>Patient</strong>
                                        <span id="previewPatientCard">{{ $selectedPatient['label'] ?? 'Aucun patient selectionne' }}</span>
                                    </div>
                                    <div class="rx-preview-card">
                                        <strong>Prescripteur</strong>
                                        <span id="previewDoctorCard">{{ $selectedMedecin ? trim(($selectedMedecin->civilite ?? 'Dr.') . ' ' . $selectedMedecin->prenom . ' ' . $selectedMedecin->nom) : 'Medecin a confirmer' }}</span>
                                    </div>
                                    <div class="rx-preview-card">
                                        <strong>Date</strong>
                                        <span id="previewDateCard">{{ $initialDateValue ? \Illuminate\Support\Carbon::parse($initialDateValue)->format('d/m/Y') : 'Non renseignee' }}</span>
                                    </div>
                                    <div class="rx-preview-card">
                                        <strong>Points d'attention</strong>
                                        <span id="previewAttentionCard">{{ $selectedPatient['allergies'] ?? 'Aucune allergie documentee.' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="rx-preview-section">
                                <h3>Diagnostic / motif</h3>
                                <p id="previewDiagnostic">{{ $initialDiagnostic ?: 'Aucun diagnostic saisi pour le moment.' }}</p>
                            </div>

                            <div class="rx-preview-section">
                                <h3>Instructions generales</h3>
                                <p id="previewInstructions">{{ $initialInstructions ?: 'Aucune instruction generale saisie.' }}</p>
                            </div>

                            <div class="rx-preview-section">
                                <h3>Traitement prescrit</h3>
                                <ul class="rx-preview-list" id="previewMedicationList"></ul>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>

            <div class="rx-footer-bar">
                <div class="rx-footer-meta">
                    <a href="#previewSection" class="rx-inline-pill rx-mobile-preview-link"><i class="fas fa-eye"></i> Voir l'apercu</a>
                    <span class="rx-inline-pill"><i class="fas fa-folder-open"></i> Ajout automatique au dossier patient</span>
                    <span class="rx-inline-pill"><i class="fas fa-shield-heart"></i> Allergies et traitements visibles avant validation</span>
                </div>

                <div class="rx-footer-actions">
                    <a href="{{ route('ordonnances.index') }}" class="rx-link rx-button-light">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button type="button" class="rx-button rx-button-light" id="previewPrintBtn">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                    <button type="button" class="rx-button rx-button-soft" id="previewPdfBtn">
                        <i class="fas fa-file-pdf"></i> Telecharger PDF
                    </button>
                    <button type="button" class="rx-button rx-button-warm" id="sendPatientBtn">
                        <i class="fas fa-paper-plane"></i> Envoyer au patient
                    </button>
                    <button type="submit" class="rx-button rx-button-primary">
                        <i class="fas fa-save"></i> {{ $isEditing ? 'Mettre a jour' : 'Enregistrer' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="application/json" id="ordonnanceCreatePayload">
{{ \Illuminate\Support\Js::from([
    'patientCatalog' => $patientDirectoryData ?? [],
    'medicamentCatalog' => $medicamentCatalogData ?? [],
    'consultationCatalog' => $consultationDirectoryData ?? [],
    'templateCatalog' => $templateCatalogData ?? [],
    'doctorLocked' => (bool) ($currentMedecin ?? null),
    'initialDoctor' => $initialDoctorPayload ?? null,
    'previewPdfUrl' => route('ordonnances.preview-pdf'),
    'medicationIndex' => $prescriptionRows->count(),
]) }}
</script>
@endsection

