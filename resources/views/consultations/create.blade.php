@extends('layouts.app')

@section('title', 'Nouvelle Consultation')
@section('topbar_subtitle', 'Creation d\'une consultation medicale avec pre-remplissage, signes vitaux et assistance IA.')

@push('styles')
<style>
    :root {
        --cc-border: #d8e5ef;
        --cc-text: #17324c;
        --cc-muted: #69839a;
        --cc-primary: #1b79c9;
        --cc-primary-strong: #145d98;
        --cc-success: #16956f;
        --cc-surface: rgba(255, 255, 255, 0.88);
        --cc-shadow: 0 24px 42px -34px rgba(15, 40, 65, 0.34);
    }

    .cc-wrap {
        width: 100%;
        max-width: 100%;
    }

    .cc-shell {
        display: grid;
        gap: 18px;
        padding: 10px 8px 90px;
    }

    .cc-hero {
        padding: 22px;
        border-radius: 24px;
        border: 1px solid var(--cc-border);
        background:
            radial-gradient(circle at top right, rgba(27, 121, 201, 0.16) 0%, rgba(27, 121, 201, 0) 34%),
            radial-gradient(circle at left top, rgba(22, 149, 111, 0.09) 0%, rgba(22, 149, 111, 0) 30%),
            linear-gradient(180deg, #f5f9fd 0%, #eef6fc 100%);
        box-shadow: var(--cc-shadow);
    }

    .cc-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(300px, 0.78fr);
        gap: 18px;
        align-items: start;
    }

    .cc-hero-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }

    .cc-back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        min-height: 42px;
        padding: 0.65rem 1rem;
        border-radius: 10px;
        border: 1px solid #c8d7e7;
        background: #fff;
        color: #456281;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        box-shadow: 0 10px 18px -18px rgba(15, 23, 42, 0.45);
    }

    .cc-back-btn:hover {
        border-color: #b8cce1;
        background: #edf4fb;
        color: #1f3d5e;
        text-decoration: none;
    }

    .cc-hero-copy {
        min-width: 0;
        display: grid;
        gap: 14px;
    }

    .cc-hero-topline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cc-hero-title-row {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        flex-wrap: wrap;
    }

    .cc-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(27, 121, 201, 0.14);
        color: var(--cc-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .cc-hero-title-row i {
        font-size: 1.6rem;
        color: #3b82f6;
    }

    .cc-hero h1 {
        margin: 0;
        color: var(--cc-text);
        font-size: clamp(1.45rem, 2.2vw, 1.95rem);
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .cc-hero p {
        margin: 0;
        color: var(--cc-muted);
        font-size: .95rem;
        font-weight: 600;
        line-height: 1.65;
    }

    .cc-hero-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cc-hero-count-badge {
        background: linear-gradient(135deg, var(--cc-primary) 0%, var(--cc-primary-strong) 100%);
        color: #fff;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: .9rem;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(59, 130, 246, .12);
        white-space: nowrap;
    }

    .cc-hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #d6e4ef;
        background: rgba(255, 255, 255, 0.82);
        color: #56718b;
        font-size: .85rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .cc-hero-chip i {
        color: var(--cc-primary);
    }

    .cc-hero-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .cc-hero-side {
        display: grid;
        gap: 12px;
        padding: 16px;
        border-radius: 20px;
        border: 1px solid #d8e6f2;
        background: rgba(255, 255, 255, 0.78);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        align-content: start;
    }

    .cc-hero-side-label {
        margin: 0;
        color: #7891aa;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .cc-hero-side-value {
        margin: 0;
        color: var(--cc-text);
        font-size: 1.45rem;
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .cc-hero-side-copy {
        margin: 0;
        color: var(--cc-muted);
        font-size: .93rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .cc-hero-side-list {
        display: grid;
        gap: 10px;
    }

    .cc-hero-side-item {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 11px 12px;
        border-radius: 14px;
        border: 1px solid #dbe8f3;
        background: #f7fbfe;
    }

    .cc-hero-side-item span {
        color: #5e7891;
        font-size: .88rem;
        font-weight: 600;
    }

    .cc-hero-side-item strong {
        color: var(--cc-text);
        font-size: .92rem;
        font-weight: 800;
    }

    .cc-hero-action-btn {
        min-height: 46px;
        border-radius: 14px;
        padding: 0 16px;
        border: 1px solid transparent;
        font-size: .92rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        transition: all .2s ease;
    }

    .cc-hero-action-btn.secondary {
        background: #eef2f7;
        border-color: #dbe5f1;
        color: #486482;
    }

    .cc-hero-action-btn.secondary:hover {
        background: #e3ebf4;
        color: #2c4b6c;
    }

    .cc-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 1rem;
        align-items: start;
    }

    .cc-section {
        background: var(--cc-surface);
        border: 1px solid #dbe5f0;
        border-radius: 18px;
        box-shadow: 0 16px 30px -28px rgba(15, 23, 42, 0.32);
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .cc-section-head {
        padding: 0.95rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #f8fbff 0%, #f3f7fd 100%);
    }

    .cc-section-head h3 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 800;
        color: #1e3a8a;
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
    }

    .cc-section-body {
        padding: 1rem;
    }

    .cc-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.9rem 1rem;
    }

    .cc-form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.9rem 1rem;
    }

    .cc-field-full {
        grid-column: 1 / -1;
    }

    .cc-label {
        display: inline-block;
        margin-bottom: 0.4rem;
        color: #334155;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .cc-label.required::after {
        content: " *";
        color: #ef4444;
    }

    .cc-input,
    .cc-select,
    .cc-textarea {
        width: 100%;
        min-height: 46px;
        padding: 0.62rem 0.82rem;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #fff;
        color: #0f172a;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .cc-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .cc-input:focus,
    .cc-select:focus,
    .cc-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.14);
    }

    .cc-side-sticky {
        position: sticky;
        top: 1rem;
    }

    .cc-actions .cc-section-body {
        display: grid;
        gap: 0.65rem;
    }

    .cc-btn {
        width: 100%;
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid transparent;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
    }

    .cc-btn-primary {
        background: linear-gradient(135deg, #1d4ed8, #0284c7);
        color: #fff;
        box-shadow: 0 10px 22px rgba(29, 78, 216, 0.24);
    }

    .cc-btn-primary:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 14px 24px rgba(29, 78, 216, 0.32);
    }

    .cc-btn-secondary {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #334155;
    }

    .cc-btn-secondary:hover {
        background: #eef2f7;
        color: #1e293b;
        text-decoration: none;
    }

    .cc-check {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #334155;
        font-weight: 600;
    }

    .cc-check input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #1d4ed8;
    }

    .cc-info-list {
        margin: 0;
        padding-left: 1.05rem;
        color: #334155;
    }

    .cc-info-list li {
        margin-bottom: 0.38rem;
    }

    .cc-bmi-box {
        margin-top: 0.6rem;
        border-radius: 10px;
        background: #e0f2fe;
        border: 1px solid #bae6fd;
        color: #0c4a6e;
        font-weight: 700;
        min-height: 42px;
        padding: 0.55rem 0.7rem;
        display: none;
        align-items: center;
        gap: 0.45rem;
    }

    .cc-feedback {
        color: #dc2626;
        font-size: 0.82rem;
        margin-top: 0.25rem;
    }

    .cc-input.is-invalid,
    .cc-select.is-invalid,
    .cc-textarea.is-invalid {
        border-color: #ef4444;
    }

    body.dark-mode .cc-hero {
        border-color: #294661;
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.12) 0%, rgba(56, 189, 248, 0) 36%),
            radial-gradient(circle at left top, rgba(15, 118, 110, 0.18) 0%, rgba(15, 118, 110, 0) 34%),
            linear-gradient(180deg, #0f2136 0%, #10263d 100%);
    }

    body.dark-mode .cc-back-btn {
        border-color: #3f6284;
        color: #d2e6fb;
        background: #173450;
    }

    body.dark-mode .cc-back-btn:hover {
        border-color: #4d7499;
        color: #fff;
        background: #214666;
    }

    body.dark-mode .cc-hero-title-row i {
        color: #77b7ff;
    }

    body.dark-mode .cc-hero h1 {
        color: #e4f1ff;
    }

    body.dark-mode .cc-hero p {
        color: #a9c2dc;
    }

    body.dark-mode .cc-eyebrow,
    body.dark-mode .cc-hero-chip,
    body.dark-mode .cc-hero-side,
    body.dark-mode .cc-hero-side-item {
        background: #11263b;
        border-color: #294661;
        color: #b8cce0;
    }

    body.dark-mode .cc-hero-chip i {
        color: #77b7ff;
    }

    body.dark-mode .cc-hero-side-value {
        color: #e4f1ff;
    }

    body.dark-mode .cc-hero-side-copy,
    body.dark-mode .cc-hero-side-item span,
    body.dark-mode .cc-hero-side-label {
        color: #a9c2dc;
    }

    body.dark-mode .cc-hero-side-item strong {
        color: #e4f1ff;
    }

    body.dark-mode .cc-hero-count-badge {
        background: linear-gradient(90deg, #1f5fb3 60%, #123771 100%);
    }

    body.dark-mode .cc-hero-action-btn.secondary {
        color: #d2e6fb;
        border-color: #3c5f81;
        background: #1a3855;
    }

    body.dark-mode .cc-hero-action-btn.secondary:hover {
        color: #fff;
        background: #234a6d;
    }

    body.dark-mode .cc-section {
        background: #0f172a;
        border-color: #1f3047;
        box-shadow: 0 14px 26px rgba(2, 6, 23, 0.34);
    }

    body.dark-mode .cc-section-head {
        background: linear-gradient(180deg, #12243d 0%, #102035 100%);
        border-color: #23354d;
    }

    body.dark-mode .cc-section-head h3 {
        color: #bfdbfe;
    }

    body.dark-mode .cc-label,
    body.dark-mode .cc-check,
    body.dark-mode .cc-info-list {
        color: #dbe7f5;
    }

    body.dark-mode .cc-input,
    body.dark-mode .cc-select,
    body.dark-mode .cc-textarea {
        background: #0b1d34;
        border-color: #2a3e5b;
        color: #e2e8f0;
    }

    body.dark-mode .cc-input::placeholder,
    body.dark-mode .cc-textarea::placeholder {
        color: #94a3b8;
    }

    body.dark-mode .cc-input:focus,
    body.dark-mode .cc-select:focus,
    body.dark-mode .cc-textarea:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2);
    }

    body.dark-mode .cc-btn-secondary {
        background: #1a2b42;
        border-color: #365074;
        color: #dbe7f5;
    }

    body.dark-mode .cc-btn-secondary:hover {
        background: #223853;
        color: #f1f5f9;
    }

    body.dark-mode .cc-bmi-box {
        background: rgba(14, 116, 144, 0.22);
        border-color: rgba(56, 189, 248, 0.38);
        color: #bae6fd;
    }

    @media (max-width: 1199px) {
        .cc-main-grid {
            grid-template-columns: 1fr;
        }

        .cc-hero-grid {
            grid-template-columns: 1fr;
        }

        .cc-side-sticky {
            position: static;
        }
    }

    @media (max-width: 991px) {
        .cc-hero-main {
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .cc-back-btn {
            width: 100%;
        }

        .cc-hero-topline {
            align-items: stretch;
        }

        .cc-hero-actions,
        .cc-hero-action-btn {
            width: 100%;
        }
    }

    @media (max-width: 767px) {
        .cc-form-grid,
        .cc-form-grid-3 {
            grid-template-columns: 1fr;
        }

        .cc-hero {
            padding-bottom: 14px;
        }

        .cc-section-head,
        .cc-section-body {
            padding: 0.85rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 cc-wrap">
    <div class="cc-shell">
        <div class="cc-hero">
            <div class="cc-hero-grid">
                <div>
                    <div class="cc-hero-main">
                        <a href="{{ route('consultations.index') }}" class="cc-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            <span>Retour</span>
                        </a>
                        <div class="cc-hero-copy">
                            <div class="cc-hero-topline">
                                <span class="cc-eyebrow">Nouveau parcours</span>
                                <div class="cc-hero-actions">
                                    <a href="{{ route('consultations.index') }}" class="cc-hero-action-btn secondary">
                                        <i class="fas fa-list"></i>
                                        <span>Liste consultations</span>
                                    </a>
                                </div>
                            </div>

                            <div class="cc-hero-title-row">
                                <i class="fas fa-stethoscope"></i>
                                <h1>Nouvelle Consultation</h1>
                                <span class="cc-hero-count-badge">{{ is_countable($patients ?? null) ? count($patients) : 0 }} Patients</span>
                            </div>
                            <p>Enregistrer une nouvelle consultation medicale avec preselection du rendez-vous, saisie des constantes et assistance IA.</p>

                            <div class="cc-hero-badges">
                                <span class="cc-hero-chip"><i class="fas fa-user-md"></i>{{ is_countable($medecins ?? null) ? count($medecins) : 0 }} medecins actifs</span>
                                @if(!empty($selectedRendezVousId))
                                    <span class="cc-hero-chip"><i class="fas fa-calendar-check"></i>Rendez-vous lie #{{ $selectedRendezVousId }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="cc-hero-side">
                    <div>
                        <p class="cc-hero-side-label">Saisie rapide</p>
                        <p class="cc-hero-side-value">{{ !empty($selectedRendezVousId) ? 'RDV' : 'Libre' }}</p>
                        <p class="cc-hero-side-copy">La consultation peut etre rattachee a un rendez-vous existant ou creee de maniere autonome depuis ce formulaire.</p>
                    </div>

                    <div class="cc-hero-side-list">
                        <div class="cc-hero-side-item">
                            <span>Patient preselectionne</span>
                            <strong>{{ $selectedPatientId ? 'Oui' : 'Non' }}</strong>
                        </div>
                        <div class="cc-hero-side-item">
                            <span>Medecin preselectionne</span>
                            <strong>{{ $selectedMedecinId ? 'Oui' : 'Non' }}</strong>
                        </div>
                        <div class="cc-hero-side-item">
                            <span>Date proposee</span>
                            <strong>{{ !empty($selectedDateConsultation) ? \Carbon\Carbon::parse($selectedDateConsultation)->format('d/m/Y H:i') : 'A definir' }}</strong>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <form action="{{ route('consultations.store') }}" method="POST" id="consultationCreateForm">
        @csrf
        <input type="hidden" name="rendez_vous_id" value="{{ old('rendez_vous_id', $selectedRendezVousId ?? '') }}">

        <div class="cc-main-grid">
            <div>
                <section class="cc-section">
                    <div class="cc-section-head">
                        <h3><i class="fas fa-user-md"></i> {{ __('messages.consultations.select_patient_doctor') }}</h3>
                    </div>
                    <div class="cc-section-body">
                        <div class="cc-form-grid">
                            <div>
                                <label for="patient_id" class="cc-label required">Patient</label>
                                <select id="patient_id" name="patient_id" class="cc-select @error('patient_id') is-invalid @enderror" required>
                                    <option value="">Selectionner un patient...</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ (old('patient_id') ?? $selectedPatientId) == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->nom }} {{ $patient->prenom }}
                                            @if($patient->cin)
                                                ({{ $patient->cin }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="medecin_id" class="cc-label required">{{ __('messages.consultations.doctor') }}</label>
                                <select id="medecin_id" name="medecin_id" class="cc-select @error('medecin_id') is-invalid @enderror" required>
                                    <option value="">{{ __('messages.consultations.select_doctor') }}</option>
                                    @foreach($medecins as $medecin)
                                        <option value="{{ $medecin->id }}" {{ (old('medecin_id') ?? ($selectedMedecinId ?? null)) == $medecin->id ? 'selected' : '' }}>
                                            {{ $medecin->nom_complet }}
                                            @if($medecin->specialite)
                                                - {{ $medecin->specialite }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('medecin_id')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="date_consultation" class="cc-label required">Date et heure de consultation</label>
                                <input type="datetime-local" id="date_consultation" name="date_consultation" class="cc-input @error('date_consultation') is-invalid @enderror" value="{{ old('date_consultation', $selectedDateConsultation ?? now()->format('Y-m-d\\TH:i')) }}" required>
                                @error('date_consultation')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="cc-section">
                    <div class="cc-section-head">
                        <h3><i class="fas fa-heartbeat"></i> Signes Vitaux</h3>
                    </div>
                    <div class="cc-section-body">
                        <div class="cc-form-grid-3">
                            <div>
                                <label for="poids" class="cc-label">Poids (kg)</label>
                                <input id="poids" name="poids" type="number" step="0.1" class="cc-input @error('poids') is-invalid @enderror" value="{{ old('poids') }}" placeholder="Ex: 70.5">
                                @error('poids')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="taille" class="cc-label">Taille (cm)</label>
                                <input id="taille" name="taille" type="number" step="0.1" class="cc-input @error('taille') is-invalid @enderror" value="{{ old('taille') }}" placeholder="Ex: 175">
                                @error('taille')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="temperature" class="cc-label">{{ __('messages.consultations.temperature_c') }}</label>
                                <input id="temperature" name="temperature" type="number" step="0.1" class="cc-input @error('temperature') is-invalid @enderror" value="{{ old('temperature') }}" placeholder="Ex: 36.8">
                                @error('temperature')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="tas" class="cc-label">Tension Arterielle Systolique</label>
                                <input id="tas" name="tension_arterielle_systolique" type="number" class="cc-input @error('tension_arterielle_systolique') is-invalid @enderror" value="{{ old('tension_arterielle_systolique') }}" placeholder="Ex: 120">
                                @error('tension_arterielle_systolique')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="tad" class="cc-label">Tension Arterielle Diastolique</label>
                                <input id="tad" name="tension_arterielle_diastolique" type="number" class="cc-input @error('tension_arterielle_diastolique') is-invalid @enderror" value="{{ old('tension_arterielle_diastolique') }}" placeholder="Ex: 80">
                                @error('tension_arterielle_diastolique')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="bmiBox" class="cc-bmi-box">
                            <i class="fas fa-calculator"></i>
                            <span>IMC calcule: <strong id="bmiValue">--</strong></span>
                        </div>
                    </div>
                </section>

                <section class="cc-section">
                    <div class="cc-section-head">
                        <h3><i class="fas fa-notes-medical"></i> Informations Cliniques</h3>
                    </div>
                    <div class="cc-section-body">
                        <div class="cc-form-grid">
                            <div class="cc-field-full">
                                <label for="symptomes" class="cc-label">{{ __('messages.consultations.symptoms') }}</label>
                                <textarea id="symptomes" name="symptomes" rows="3" class="cc-textarea @error('symptomes') is-invalid @enderror" placeholder="{{ __('messages.consultations.describe_symptoms') }}">{{ old('symptomes') }}</textarea>
                                @error('symptomes')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="cc-field-full">
                                <label for="examen_clinique" class="cc-label">Examen Clinique</label>
                                <textarea id="examen_clinique" name="examen_clinique" rows="3" class="cc-textarea @error('examen_clinique') is-invalid @enderror" placeholder="Resultats de l'examen clinique...">{{ old('examen_clinique') }}</textarea>
                                @error('examen_clinique')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="cc-field-full">
                                <label for="diagnostic" class="cc-label">Diagnostic</label>
                                <textarea id="diagnostic" name="diagnostic" rows="3" class="cc-textarea @error('diagnostic') is-invalid @enderror" placeholder="Diagnostic pose...">{{ old('diagnostic') }}</textarea>
                                @error('diagnostic')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                @include('consultations.partials.ai_assistant', [
                    'consultation' => null,
                    'aiGenerations' => collect(),
                ])

                <section class="cc-section">
                    <div class="cc-section-head">
                        <h3><i class="fas fa-pills"></i> Traitement &amp; Recommandations</h3>
                    </div>
                    <div class="cc-section-body">
                        <div class="cc-form-grid">
                            <div class="cc-field-full">
                                <label for="traitement_prescrit" class="cc-label">Traitement Prescrit</label>
                                <textarea id="traitement_prescrit" name="traitement_prescrit" rows="3" class="cc-textarea @error('traitement_prescrit') is-invalid @enderror" placeholder="{{ __('messages.consultations.treatment_placeholder') }}">{{ old('traitement_prescrit') }}</textarea>
                                @error('traitement_prescrit')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="cc-field-full">
                                <label for="recommandations" class="cc-label">Recommandations</label>
                                <textarea id="recommandations" name="recommandations" rows="3" class="cc-textarea @error('recommandations') is-invalid @enderror" placeholder="Recommandations, conseils, suivi...">{{ old('recommandations') }}</textarea>
                                @error('recommandations')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="date_prochaine_visite" class="cc-label">Date de Prochaine Visite</label>
                                <input type="date" id="date_prochaine_visite" name="date_prochaine_visite" class="cc-input @error('date_prochaine_visite') is-invalid @enderror" value="{{ old('date_prochaine_visite') }}">
                                @error('date_prochaine_visite')
                                    <div class="cc-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="cc-side-sticky">
                <section class="cc-section cc-actions">
                    <div class="cc-section-head">
                        <h3><i class="fas fa-cogs"></i> Actions</h3>
                    </div>
                    <div class="cc-section-body">
                        <button type="submit" class="cc-btn cc-btn-primary">
                            <i class="fas fa-save"></i>
                            <span>Enregistrer la Consultation</span>
                        </button>

                        <a href="{{ route('consultations.index') }}" class="cc-btn cc-btn-secondary">
                            <i class="fas fa-times"></i>
                            <span>Annuler</span>
                        </a>

                        <label class="cc-check">
                            <input type="checkbox" id="create_ordonnance" name="create_ordonnance">
                            <span><i class="fas fa-prescription"></i> Creer une ordonnance</span>
                        </label>
                    </div>
                </section>

                <section class="cc-section">
                    <div class="cc-section-head">
                        <h3><i class="fas fa-info-circle"></i> Informations</h3>
                    </div>
                    <div class="cc-section-body">
                        <ul class="cc-info-list">
                            <li>Les champs marques d'une etoile (*) sont obligatoires.</li>
                            <li>Remplissez les signes vitaux pour un suivi complet.</li>
                            <li>Le diagnostic et le traitement sont essentiels pour la qualite des soins.</li>
                            <li>Vous pouvez creer une ordonnance directement apres cette consultation.</li>
                        </ul>
                    </div>
                </section>
            </aside>
        </div>
        </form>
    </div>
</div>
@endsection


