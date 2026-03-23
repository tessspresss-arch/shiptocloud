@extends('layouts.app')

@section('title', 'Nouvel examen')
@section('topbar_subtitle', 'Creation guidee d un examen avec actions rapides dans une interface premium harmonisee.')

@section('content')
@php
    $patientsList = $patients ?? \App\Models\Patient::orderBy('nom')->get();
    $medecinsList = $medecins ?? \App\Models\User::where('role', 'medecin')->orWhere('role', 'admin')->orderBy('name')->get();
    $selectedPatient = old('patient_id', optional($patient ?? null)->id);
    $selectedPatientModel = $patientsList->firstWhere('id', (int) $selectedPatient);
    $selectedPatientName = $selectedPatientModel ? trim(($selectedPatientModel->nom ?? '') . ' ' . ($selectedPatientModel->prenom ?? '')) : 'Patient a selectionner';
    $selectedDate = old('date_examen');
    $selectedDateDisplay = $selectedDate ? \Illuminate\Support\Carbon::parse($selectedDate)->format('d/m/Y') : 'A planifier';
    $typeOptions = [
        'Analyse de sang',
        'Radiographie',
        'Echographie',
        'ECG',
        'IRM',
        'Autre',
    ];
@endphp

<style>
    .exam-record-page {
        --exam-primary: #1760a5;
        --exam-primary-strong: #0f4c84;
        --exam-accent: #0ea5e9;
        --exam-success: #0f9f77;
        --exam-warning: #c57d10;
        --exam-danger: #cb4d58;
        --exam-surface: linear-gradient(180deg, #f5f9fd 0%, #eef5fb 100%);
        --exam-card: #ffffff;
        --exam-border: #d8e4f1;
        --exam-text: #15314d;
        --exam-muted: #5f7896;
        width: 100%;
        max-width: none;
        padding: 10px 8px 88px;
    }

    .exam-record-shell {
        display: grid;
        gap: 16px;
    }

    .exam-record-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--exam-border);
        border-radius: 24px;
        padding: 18px;
        background:
            radial-gradient(circle at top right, rgba(23, 96, 165, 0.18) 0%, rgba(23, 96, 165, 0) 30%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
            var(--exam-surface);
        box-shadow: 0 28px 48px -40px rgba(20, 52, 84, 0.42);
    }

    .exam-record-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .exam-record-hero > * {
        position: relative;
        z-index: 1;
    }

    .exam-record-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .exam-record-eyebrow,
    .exam-record-panel-label,
    .exam-record-section-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(23, 96, 165, 0.1);
        color: var(--exam-primary);
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .exam-record-title-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 10px;
    }

    .exam-record-hero-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .exam-record-title-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
        font-size: 1.35rem;
        background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-strong) 100%);
        box-shadow: 0 16px 28px -18px rgba(23, 96, 165, 0.58);
    }

    .exam-record-title {
        margin: 0;
        color: var(--exam-text);
        font-size: clamp(1.6rem, 2.8vw, 2.2rem);
        line-height: 1.04;
        letter-spacing: -0.04em;
        font-weight: 900;
    }

    .exam-record-subtitle {
        margin: 8px 0 0;
        max-width: 72ch;
        color: var(--exam-muted);
        font-size: .95rem;
        line-height: 1.58;
        font-weight: 600;
    }

    .exam-record-actions,
    .exam-record-main-actions,
    .exam-record-mobile-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .exam-record-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #d4e2f2;
        background: rgba(255, 255, 255, 0.76);
        color: #1a4d86;
        font-size: .82rem;
        font-weight: 800;
    }

    .exam-record-panel,
    .exam-record-card,
    .exam-record-section {
        background: var(--exam-card);
        border: 1px solid var(--exam-border);
        border-radius: 20px;
        box-shadow: 0 22px 34px -34px rgba(15, 23, 42, 0.44);
    }

    .exam-record-panel-copy,
    .exam-record-field-hint,
    .exam-record-main-copy {
        display: block;
        margin-top: 6px;
        color: var(--exam-muted);
        font-size: .84rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .exam-record-hero-tools {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 12px;
    }

    .exam-record-panel {
        padding: 10px;
        background: rgba(255, 255, 255, 0.72);
    }

    .exam-record-panel-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 10px;
    }

    .exam-record-panel-list li {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 14px;
        border: 1px solid #dce8f4;
        background: linear-gradient(180deg, #fbfdff 0%, #f6fafe 100%);
        color: var(--exam-muted);
        font-size: .85rem;
        font-weight: 700;
    }

    .exam-record-panel-list strong {
        color: var(--exam-text);
        font-weight: 800;
        text-align: right;
    }

    .exam-record-btn {
        min-height: 46px;
        border-radius: 14px;
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
        white-space: nowrap;
    }

    .exam-record-btn:hover,
    .exam-record-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .exam-record-btn.soft {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #385674;
    }

    .exam-record-btn.soft:hover,
    .exam-record-btn.soft:focus {
        color: #1f6fa3;
        border-color: rgba(23, 96, 165, 0.28);
        background: linear-gradient(180deg, #ffffff 0%, #edf5fb 100%);
    }

    .exam-record-btn.primary {
        color: #ffffff;
        background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-strong) 100%);
        box-shadow: 0 18px 30px -22px rgba(23, 96, 165, 0.58);
    }

    .exam-record-btn.primary:hover,
    .exam-record-btn.primary:focus {
        color: #ffffff;
    }

    .exam-record-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(23, 96, 165, 0.1);
        color: var(--exam-primary);
    }

    .exam-record-btn.primary .exam-record-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: inherit;
    }

    .exam-record-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .exam-record-main {
        padding: 18px;
    }

    .exam-record-main-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .exam-record-main-title {
        margin: 0;
        color: var(--exam-text);
        font-size: 1.3rem;
        font-weight: 900;
        letter-spacing: -.03em;
    }

    .exam-record-body {
        display: grid;
        gap: 14px;
    }

    .exam-record-section {
        overflow: hidden;
    }

    .exam-record-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 18px;
        border-bottom: 1px solid #e6eef7;
        background: #f8fbff;
    }

    .exam-record-section-title {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .exam-record-section-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(23, 96, 165, 0.1);
        color: var(--exam-primary);
        flex-shrink: 0;
    }

    .exam-record-section-head h3 {
        margin: 0;
        color: var(--exam-text);
        font-size: 1rem;
        font-weight: 900;
    }

    .exam-record-section-help {
        margin: 6px 0 0;
        color: var(--exam-muted);
        font-size: .86rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .exam-record-section-body {
        padding: 18px;
    }

    .exam-record-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .exam-record-field.full {
        grid-column: 1 / -1;
    }

    .exam-record-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .exam-record-field label {
        color: #375273;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .exam-record-field label .req {
        color: var(--exam-danger);
    }

    .exam-record-input,
    .exam-record-select,
    .exam-record-textarea {
        width: 100%;
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid #cfe0f0;
        background: #ffffff;
        color: var(--exam-text);
        font-size: .94rem;
        font-weight: 600;
        padding: 0 14px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .exam-record-textarea {
        min-height: 118px;
        padding: 12px 14px;
        resize: vertical;
    }

    .exam-record-input:focus,
    .exam-record-select:focus,
    .exam-record-textarea:focus {
        outline: none;
        border-color: rgba(23, 96, 165, 0.4);
        box-shadow: 0 0 0 4px rgba(23, 96, 165, 0.1);
    }

    .exam-record-input.error,
    .exam-record-select.error,
    .exam-record-textarea.error {
        border-color: #f39ca9;
        background: #fff4f6;
    }

    .exam-record-error {
        color: #bf2020;
        font-size: .79rem;
        font-weight: 700;
    }

    .exam-record-check {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 46px;
    }

    .exam-record-check input {
        width: 18px;
        height: 18px;
        accent-color: var(--exam-primary);
    }

    .exam-record-footer {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid #e4edf7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .exam-record-mobile-actions {
        display: none;
    }

    html.dark body .exam-record-page,
    body.dark-mode .exam-record-page,
    body.theme-dark .exam-record-page {
        --exam-surface: linear-gradient(180deg, #152233 0%, #122032 100%);
        --exam-card: #162332;
        --exam-border: #2f4358;
        --exam-text: #e6edf6;
        --exam-muted: #9eb1c7;
    }

    html.dark body .exam-record-kpi,
    html.dark body .exam-record-panel,
    html.dark body .exam-record-card,
    html.dark body .exam-record-section,
    html.dark body .exam-record-panel-list li,
    body.dark-mode .exam-record-kpi,
    body.dark-mode .exam-record-panel,
    body.dark-mode .exam-record-card,
    body.dark-mode .exam-record-section,
    body.dark-mode .exam-record-panel-list li,
    body.theme-dark .exam-record-kpi,
    body.theme-dark .exam-record-panel,
    body.theme-dark .exam-record-card,
    body.theme-dark .exam-record-section,
    body.theme-dark .exam-record-panel-list li {
        background: rgba(17, 34, 54, 0.9);
        border-color: #35506a;
    }

    html.dark body .exam-record-btn.soft,
    body.dark-mode .exam-record-btn.soft,
    body.theme-dark .exam-record-btn.soft {
        border-color: #365b7d;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
        color: #d2e6fb;
    }

    html.dark body .exam-record-btn-icon,
    html.dark body .exam-record-section-icon,
    body.dark-mode .exam-record-btn-icon,
    body.dark-mode .exam-record-section-icon,
    body.theme-dark .exam-record-btn-icon,
    body.theme-dark .exam-record-section-icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    html.dark body .exam-record-section-head,
    body.dark-mode .exam-record-section-head,
    body.theme-dark .exam-record-section-head {
        background: #16273d;
        border-color: #294055;
    }

    html.dark body .exam-record-input,
    html.dark body .exam-record-select,
    html.dark body .exam-record-textarea,
    body.dark-mode .exam-record-input,
    body.dark-mode .exam-record-select,
    body.dark-mode .exam-record-textarea,
    body.theme-dark .exam-record-input,
    body.theme-dark .exam-record-select,
    body.theme-dark .exam-record-textarea {
        background: #13263f;
        border-color: #355985;
        color: #deebf9;
    }

    @media (max-width: 1200px) {
        .exam-record-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 992px) {
        .exam-record-hero-grid,
        .exam-record-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .exam-record-page {
            padding-left: 0;
            padding-right: 0;
        }

        .exam-record-grid {
            grid-template-columns: 1fr;
        }

        .exam-record-actions,
        .exam-record-main-actions,
        .exam-record-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .exam-record-btn,
        .exam-record-panel,
        .exam-record-mobile-actions {
            width: 100%;
        }

        .exam-record-mobile-actions {
            display: flex;
            position: sticky;
            bottom: 12px;
            z-index: 20;
            padding: 12px;
            border: 1px solid var(--exam-border);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 22px 40px -30px rgba(15, 23, 42, 0.4);
        }
    }
</style>

<div class="container-fluid exam-record-page">
    <div class="exam-record-shell">
        <section class="exam-record-hero">
            <div class="exam-record-hero-grid">
                <div>
                    <div class="exam-record-hero-top">
                        <span class="exam-record-eyebrow">Creation examen</span>
                        <section class="exam-record-panel">
                            <span class="exam-record-panel-label">Actions rapides</span>
                            <div class="exam-record-actions">
                                <a href="{{ route('examens.index') }}" class="exam-record-btn soft">
                                    <span class="exam-record-btn-icon"><i class="fas fa-arrow-left"></i></span>
                                    <span>Retour</span>
                                </a>
                                <button type="submit" form="examCreateForm" class="exam-record-btn primary">
                                    <span class="exam-record-btn-icon"><i class="fas fa-save"></i></span>
                                    <span>Creer l examen</span>
                                </button>
                            </div>
                        </section>
                    </div>

                    <div class="exam-record-title-row">
                        <span class="exam-record-title-icon"><i class="fas fa-microscope"></i></span>
                        <div>
                            <h1 class="exam-record-title">Nouvel examen medical</h1>
                            <p class="exam-record-subtitle">CrÃ©ez une demande d examen complementaire avec une saisie plus lisible, des reperes plus clairs et une experience coherente avec l index premium du module.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <div class="exam-record-layout">
            <section class="exam-record-card exam-record-main">
                <div class="exam-record-main-head">
                    <div>
                        <h2 class="exam-record-main-title">Formulaire de creation structure</h2>
                        <p class="exam-record-main-copy">Renseignez les informations patient, le contexte clinique, le paiement et les resultats previsionnels dans une sequence plus claire.</p>
                    </div>
                    <div class="exam-record-main-actions">
                        <span class="exam-record-chip"><i class="fas fa-shield-heart"></i>Workflow premium</span>
                    </div>
                </div>

                <form action="{{ route('examens.store') }}" method="POST" enctype="multipart/form-data" novalidate id="examCreateForm">
                    @csrf
                    <div class="exam-record-body">
                        <section class="exam-record-section">
                            <div class="exam-record-section-head">
                                <div class="exam-record-section-title">
                                    <span class="exam-record-section-icon"><i class="fas fa-user-injured"></i></span>
                                    <div>
                                        <h3>Patient et prescripteur</h3>
                                        <p class="exam-record-section-help">Associez l examen au bon patient et au medecin prescripteur si besoin.</p>
                                    </div>
                                </div>
                                <span class="exam-record-section-tag">Base</span>
                            </div>
                            <div class="exam-record-section-body">
                                <div class="exam-record-grid">
                                    <div class="exam-record-field">
                                        <label for="patient_id">Patient <span class="req">*</span></label>
                                        <select id="patient_id" name="patient_id" class="exam-record-select {{ $errors->has('patient_id') ? 'error' : '' }}" required>
                                            <option value="">-- Selectionner un patient --</option>
                                            @foreach($patientsList as $patientItem)
                                                <option value="{{ $patientItem->id }}" {{ (string) $selectedPatient === (string) $patientItem->id ? 'selected' : '' }}>
                                                    {{ trim(($patientItem->nom ?? '') . ' ' . ($patientItem->prenom ?? '')) }} ({{ $patientItem->telephone ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('patient_id')<div class="exam-record-error">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="exam-record-field">
                                        <label for="medecin_id">Medecin prescripteur</label>
                                        <select id="medecin_id" name="medecin_id" class="exam-record-select {{ $errors->has('medecin_id') ? 'error' : '' }}">
                                            <option value="">-- Selectionner un medecin --</option>
                                            @foreach($medecinsList as $medecinItem)
                                                <option value="{{ $medecinItem->id }}" {{ (string) old('medecin_id') === (string) $medecinItem->id ? 'selected' : '' }}>
                                                    {{ $medecinItem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('medecin_id')<div class="exam-record-error">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="exam-record-section">
                            <div class="exam-record-section-head">
                                <div class="exam-record-section-title">
                                    <span class="exam-record-section-icon"><i class="fas fa-clipboard-list"></i></span>
                                    <div>
                                        <h3>Details de l examen</h3>
                                        <p class="exam-record-section-help">Definissez le type d examen, la date et le contexte de la demande.</p>
                                    </div>
                                </div>
                                <span class="exam-record-section-tag">Clinique</span>
                            </div>
                            <div class="exam-record-section-body">
                                <div class="exam-record-grid">
                                    <div class="exam-record-field">
                                        <label for="type_examen">Type d examen <span class="req">*</span></label>
                                        <select id="type_examen" name="type_examen" class="exam-record-select {{ $errors->has('type_examen') ? 'error' : '' }}" required>
                                            <option value="">-- Selectionner un type --</option>
                                            @foreach($typeOptions as $typeOption)
                                                <option value="{{ $typeOption }}" {{ old('type_examen') === $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                                            @endforeach
                                        </select>
                                        @error('type_examen')<div class="exam-record-error">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="exam-record-field">
                                        <label for="date_examen">Date de l examen <span class="req">*</span></label>
                                        <input type="date" id="date_examen" name="date_examen" class="exam-record-input {{ $errors->has('date_examen') ? 'error' : '' }}" value="{{ old('date_examen') }}" required>
                                        @error('date_examen')<div class="exam-record-error">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="exam-record-field full">
                                        <label for="description">Motif / Description</label>
                                        <textarea id="description" name="description" class="exam-record-textarea {{ $errors->has('description') ? 'error' : '' }}" placeholder="Decrivez le motif de l examen...">{{ old('description') }}</textarea>
                                        @error('description')<div class="exam-record-error">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="exam-record-section">
                            <div class="exam-record-section-head">
                                <div class="exam-record-section-title">
                                    <span class="exam-record-section-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <div>
                                        <h3>Localisation et equipement</h3>
                                        <p class="exam-record-section-help">Precisez la zone concernee et l appareil utilise si l information est deja connue.</p>
                                    </div>
                                </div>
                                <span class="exam-record-section-tag">Technique</span>
                            </div>
                            <div class="exam-record-section-body">
                                <div class="exam-record-grid">
                                    <div class="exam-record-field">
                                        <label for="localisation">Localisation / Zone</label>
                                        <input type="text" id="localisation" name="localisation" class="exam-record-input" placeholder="Ex: Bras gauche, Thorax, Abdomen..." value="{{ old('localisation') }}">
                                    </div>
                                    <div class="exam-record-field">
                                        <label for="appareil">Appareil / equipement</label>
                                        <input type="text" id="appareil" name="appareil" class="exam-record-input" placeholder="Ex: Echographe, radio numerique..." value="{{ old('appareil') }}">
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="exam-record-section">
                            <div class="exam-record-section-head">
                                <div class="exam-record-section-title">
                                    <span class="exam-record-section-icon"><i class="fas fa-wallet"></i></span>
                                    <div>
                                        <h3>Paiement</h3>
                                        <p class="exam-record-section-help">Suivez le montant facture et le statut du paiement des la creation.</p>
                                    </div>
                                </div>
                                <span class="exam-record-section-tag">Finance</span>
                            </div>
                            <div class="exam-record-section-body">
                                <div class="exam-record-grid">
                                    <div class="exam-record-field">
                                        <label for="cout">Cout (DH)</label>
                                        <input type="number" id="cout" name="cout" class="exam-record-input" placeholder="0.00" step="0.01" min="0" value="{{ old('cout') }}">
                                        <p class="exam-record-field-hint">Laisser 0.00 pour un examen gratuit.</p>
                                    </div>
                                    <div class="exam-record-field">
                                        <label>Statut du paiement</label>
                                        <label for="payee" class="exam-record-check">
                                            <input type="checkbox" id="payee" name="payee" value="1" {{ old('payee') ? 'checked' : '' }}>
                                            <span>Examen paye</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="exam-record-section">
                            <div class="exam-record-section-head">
                                <div class="exam-record-section-title">
                                    <span class="exam-record-section-icon"><i class="fas fa-file-medical-alt"></i></span>
                                    <div>
                                        <h3>Resultats et observations</h3>
                                        <p class="exam-record-section-help">Renseignez les premieres notes cliniques, les resultats saisis et les recommandations patient.</p>
                                    </div>
                                </div>
                                <span class="exam-record-section-tag">Suivi</span>
                            </div>
                            <div class="exam-record-section-body">
                                <div class="exam-record-grid">
                                    <div class="exam-record-field full">
                                        <label for="resultats">Resultats</label>
                                        <textarea id="resultats" name="resultats" class="exam-record-textarea {{ $errors->has('resultats') ? 'error' : '' }}" placeholder="Resultats de l examen...">{{ old('resultats') }}</textarea>
                                        @error('resultats')<div class="exam-record-error">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="exam-record-field full">
                                        <label for="observations">Observations cliniques</label>
                                        <textarea id="observations" name="observations" class="exam-record-textarea {{ $errors->has('observations') ? 'error' : '' }}" placeholder="Observations et notes cliniques...">{{ old('observations') }}</textarea>
                                        @error('observations')<div class="exam-record-error">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="exam-record-field full">
                                        <label for="recommandations">Recommandations</label>
                                        <textarea id="recommandations" name="recommandations" class="exam-record-textarea {{ $errors->has('recommandations') ? 'error' : '' }}" placeholder="Recommandations pour le patient...">{{ old('recommandations') }}</textarea>
                                        @error('recommandations')<div class="exam-record-error">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="exam-record-section">
                            <div class="exam-record-section-head">
                                <div class="exam-record-section-title">
                                    <span class="exam-record-section-icon"><i class="fas fa-tag"></i></span>
                                    <div>
                                        <h3>Statut de l examen</h3>
                                        <p class="exam-record-section-help">Definissez l etat de traitement initial pour le circuit laboratoire ou imagerie.</p>
                                    </div>
                                </div>
                                <span class="exam-record-section-tag">Pilotage</span>
                            </div>
                            <div class="exam-record-section-body">
                                <div class="exam-record-grid">
                                    <div class="exam-record-field">
                                        <label for="statut">Statut <span class="req">*</span></label>
                                        <select id="statut" name="statut" class="exam-record-select {{ $errors->has('statut') ? 'error' : '' }}" required>
                                            <option value="">-- Selectionner un statut --</option>
                                            <option value="demande" {{ old('statut', 'demande') === 'demande' ? 'selected' : '' }}>Demande</option>
                                            <option value="en_cours" {{ old('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                            <option value="termine" {{ old('statut') === 'termine' ? 'selected' : '' }}>Termine</option>
                                            <option value="annule" {{ old('statut') === 'annule' ? 'selected' : '' }}>Annule</option>
                                        </select>
                                        @error('statut')<div class="exam-record-error">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="exam-record-footer">
                        <a href="{{ route('examens.index') }}" class="exam-record-btn soft">
                            <span class="exam-record-btn-icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Retour a la liste</span>
                        </a>
                        <button type="submit" class="exam-record-btn primary">
                            <span class="exam-record-btn-icon"><i class="fas fa-save"></i></span>
                            <span>Creer l examen</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <div class="exam-record-mobile-actions" aria-label="Actions creation examen">
            <a href="{{ route('examens.index') }}" class="exam-record-btn soft">
                <span class="exam-record-btn-icon"><i class="fas fa-arrow-left"></i></span>
                <span>Retour</span>
            </a>
            <button type="submit" form="examCreateForm" class="exam-record-btn primary">
                <span class="exam-record-btn-icon"><i class="fas fa-save"></i></span>
                <span>Creer</span>
            </button>
        </div>
    </div>
</div>

@endsection