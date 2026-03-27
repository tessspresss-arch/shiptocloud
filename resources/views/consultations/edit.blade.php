@extends('layouts.app')

@section('title', 'Modifier Consultation #' . $consultation->id)
@section('topbar_subtitle', 'Mise a jour de la consultation, enrichissement clinique et suivi assiste par IA.')

@push('styles')
<style>
    :root {
        --ce-border: #d8e5ef;
        --ce-text: #17324c;
        --ce-muted: #69839a;
        --ce-primary: #1b79c9;
        --ce-primary-strong: #145d98;
        --ce-success: #16956f;
        --ce-warning: #c98212;
        --ce-surface: rgba(255, 255, 255, 0.88);
        --ce-shadow: 0 24px 42px -34px rgba(15, 40, 65, 0.34);
    }

    .ce-page {
        width: 100%;
        max-width: 100%;
        padding: 10px 8px 90px;
    }

    .ce-shell {
        display: grid;
        gap: 18px;
    }

    .ce-hero,
    .ce-section {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        border: 1px solid var(--ce-border);
        box-shadow: var(--ce-shadow);
    }

    .ce-hero {
        padding: 22px;
        background:
            radial-gradient(circle at top right, rgba(27, 121, 201, 0.16) 0%, rgba(27, 121, 201, 0) 34%),
            radial-gradient(circle at left top, rgba(201, 130, 18, 0.09) 0%, rgba(201, 130, 18, 0) 30%),
            linear-gradient(180deg, #f5f9fd 0%, #eef6fc 100%);
    }

    .ce-section {
        background: var(--ce-surface);
        backdrop-filter: blur(10px);
    }

    .ce-hero::before,
    .ce-section::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .ce-hero > *,
    .ce-section > * {
        position: relative;
        z-index: 1;
    }

    .ce-hero-grid,
    .ce-main-grid {
        display: grid;
        gap: 18px;
        align-items: start;
    }

    .ce-hero-grid {
        grid-template-columns: minmax(0, 1.45fr) minmax(280px, 0.82fr);
    }

    .ce-main-grid {
        grid-template-columns: minmax(0, 1fr) 320px;
    }

    .ce-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(27, 121, 201, 0.14);
        color: var(--ce-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .ce-title-row {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .ce-back-btn,
    .ce-btn,
    .ce-hero-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: transform .2s ease, background .2s ease, border-color .2s ease, color .2s ease, box-shadow .2s ease;
    }

    .ce-back-btn {
        min-height: 42px;
        padding: 0 16px;
        border-radius: 12px;
        border: 1px solid #cbd9e8;
        background: rgba(255, 255, 255, 0.84);
        color: #47647f;
        font-weight: 800;
        white-space: nowrap;
    }

    .ce-back-btn:hover,
    .ce-btn:hover,
    .ce-hero-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .ce-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.4rem;
        background: linear-gradient(135deg, var(--ce-primary) 0%, var(--ce-primary-strong) 100%);
        box-shadow: 0 18px 28px -20px rgba(27, 121, 201, 0.58);
        flex-shrink: 0;
    }

    .ce-title {
        margin: 0;
        color: var(--ce-text);
        font-size: clamp(1.55rem, 2.5vw, 2.1rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .ce-subtitle {
        margin: 8px 0 0;
        color: var(--ce-muted);
        font-size: .97rem;
        line-height: 1.62;
        font-weight: 600;
        max-width: 70ch;
    }

    .ce-badges,
    .ce-actions,
    .ce-summary-list,
    .ce-sticky-body {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ce-badges {
        margin-top: 16px;
    }

    .ce-badge,
    .ce-chip,
    .ce-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        white-space: nowrap;
        font-weight: 700;
    }

    .ce-badge {
        background: linear-gradient(135deg, var(--ce-primary) 0%, var(--ce-primary-strong) 100%);
        color: #fff;
        box-shadow: 0 16px 24px -22px rgba(27, 121, 201, 0.92);
    }

    .ce-chip,
    .ce-status {
        border: 1px solid #d6e4ef;
        background: rgba(255, 255, 255, 0.82);
        color: #56718b;
        font-size: .85rem;
    }

    .ce-chip i,
    .ce-status i {
        color: var(--ce-primary);
    }

    .ce-status.completed {
        color: #0f6b51;
        border-color: rgba(22, 149, 111, 0.18);
        background: rgba(22, 149, 111, 0.12);
    }

    .ce-status.pending {
        color: #8c5b09;
        border-color: rgba(201, 130, 18, 0.18);
        background: rgba(201, 130, 18, 0.12);
    }

    .ce-status.scheduled {
        color: #0f5a96;
        border-color: rgba(27, 121, 201, 0.16);
        background: rgba(27, 121, 201, 0.12);
    }

    .ce-actions {
        margin-top: 22px;
    }

    .ce-hero-btn,
    .ce-btn {
        min-height: 48px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid transparent;
        font-size: .92rem;
        font-weight: 800;
    }

    .ce-hero-btn.secondary,
    .ce-btn.secondary {
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        border-color: #cfdeec;
        color: #48657f;
    }

    .ce-hero-btn.primary,
    .ce-btn.primary {
        background: linear-gradient(135deg, var(--ce-success) 0%, #117454 100%);
        color: #fff;
        box-shadow: 0 18px 28px -24px rgba(22, 149, 111, 0.84);
    }

    .ce-summary-card,
    .ce-sticky-card {
        padding: 18px;
        border-radius: 20px;
        border: 1px solid #d9e6f2;
        background: rgba(255, 255, 255, 0.8);
    }

    .ce-summary-label,
    .ce-section-kicker,
    .ce-label {
        color: #718aa3;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .ce-summary-value {
        margin: 8px 0 0;
        color: var(--ce-text);
        font-size: 1.9rem;
        line-height: 1;
        font-weight: 800;
    }

    .ce-summary-copy {
        margin: 10px 0 0;
        color: var(--ce-muted);
        font-size: .92rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .ce-summary-list {
        display: grid;
        gap: 10px;
        margin-top: 14px;
    }

    .ce-summary-item {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 11px 12px;
        border-radius: 14px;
        border: 1px solid #dbe8f3;
        background: #f7fbfe;
    }

    .ce-summary-item span {
        color: #5e7891;
        font-size: .88rem;
        font-weight: 600;
    }

    .ce-summary-item strong {
        color: var(--ce-text);
        font-size: .92rem;
        font-weight: 800;
        text-align: right;
    }

    .ce-section-head {
        padding: 16px 18px;
        border-bottom: 1px solid #e1ebf4;
        background: linear-gradient(180deg, #f8fbff 0%, #f3f7fd 100%);
    }

    .ce-section-title {
        margin: 4px 0 0;
        color: var(--ce-text);
        font-size: 1.06rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .ce-section-body {
        padding: 18px;
    }

    .ce-form-grid,
    .ce-form-grid-3 {
        display: grid;
        gap: 14px 16px;
    }

    .ce-form-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .ce-form-grid-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .ce-field-full {
        grid-column: 1 / -1;
    }

    .ce-label {
        display: inline-block;
        margin-bottom: 8px;
    }

    .ce-label.required::after {
        content: " *";
        color: #dc2626;
    }

    .ce-input,
    .ce-select,
    .ce-textarea {
        width: 100%;
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid #cbd9e8;
        background: rgba(255, 255, 255, 0.96);
        color: var(--ce-text);
        padding: .7rem .9rem;
        font-size: .95rem;
        font-weight: 600;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .ce-textarea {
        min-height: 118px;
        resize: vertical;
    }

    .ce-input:focus,
    .ce-select:focus,
    .ce-textarea:focus {
        outline: none;
        border-color: rgba(27, 121, 201, 0.42);
        box-shadow: 0 0 0 4px rgba(27, 121, 201, 0.1);
    }

    .ce-input.is-invalid,
    .ce-select.is-invalid,
    .ce-textarea.is-invalid {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .ce-bp-grid {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 10px;
        align-items: center;
    }

    .ce-bp-divider {
        color: #5c7791;
        font-weight: 800;
    }

    .ce-feedback {
        color: #dc2626;
        font-size: .82rem;
        margin-top: 6px;
    }

    .ce-bmi-box {
        margin-top: 14px;
        border-radius: 14px;
        background: rgba(14, 165, 233, 0.12);
        border: 1px solid rgba(14, 165, 233, 0.18);
        color: #0c4a6e;
        font-weight: 800;
        min-height: 46px;
        padding: 0 14px;
        display: none;
        align-items: center;
        gap: 10px;
    }

    .ce-side-sticky {
        position: sticky;
        top: 1rem;
        display: grid;
        gap: 16px;
    }

    .ce-sticky-body {
        display: grid;
        gap: 10px;
    }

    .ce-info-list {
        margin: 0;
        padding-left: 18px;
        color: #47637d;
        display: grid;
        gap: 8px;
        font-size: .9rem;
        font-weight: 600;
    }

    .ce-check {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #38516a;
        font-size: .92rem;
        font-weight: 700;
    }

    html.dark body .ce-hero,
    body.dark-mode .ce-hero {
        border-color: #294661;
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.12) 0%, rgba(56, 189, 248, 0) 36%),
            radial-gradient(circle at left top, rgba(15, 118, 110, 0.18) 0%, rgba(15, 118, 110, 0) 34%),
            linear-gradient(180deg, #0f2136 0%, #10263d 100%);
    }

    html.dark body .ce-section,
    html.dark body .ce-summary-card,
    html.dark body .ce-sticky-card,
    body.dark-mode .ce-section,
    body.dark-mode .ce-summary-card,
    body.dark-mode .ce-sticky-card {
        background: rgba(12, 27, 43, 0.92);
        border-color: #294661;
        box-shadow: 0 26px 48px -36px rgba(0, 0, 0, 0.62);
    }

    html.dark body .ce-eyebrow,
    html.dark body .ce-chip,
    html.dark body .ce-status,
    html.dark body .ce-summary-item,
    body.dark-mode .ce-eyebrow,
    body.dark-mode .ce-chip,
    body.dark-mode .ce-status,
    body.dark-mode .ce-summary-item {
        background: #11263b;
        border-color: #294661;
        color: #b8cce0;
    }

    html.dark body .ce-title,
    html.dark body .ce-summary-value,
    html.dark body .ce-section-title,
    body.dark-mode .ce-title,
    body.dark-mode .ce-summary-value,
    body.dark-mode .ce-section-title {
        color: #e4f1ff;
    }

    html.dark body .ce-subtitle,
    html.dark body .ce-summary-copy,
    html.dark body .ce-summary-item span,
    html.dark body .ce-summary-label,
    html.dark body .ce-section-kicker,
    html.dark body .ce-label,
    html.dark body .ce-info-list,
    html.dark body .ce-check,
    body.dark-mode .ce-subtitle,
    body.dark-mode .ce-summary-copy,
    body.dark-mode .ce-summary-item span,
    body.dark-mode .ce-summary-label,
    body.dark-mode .ce-section-kicker,
    body.dark-mode .ce-label,
    body.dark-mode .ce-info-list,
    body.dark-mode .ce-check {
        color: #a9c2dc;
    }

    html.dark body .ce-summary-item strong,
    body.dark-mode .ce-summary-item strong {
        color: #e4f1ff;
    }

    html.dark body .ce-input,
    html.dark body .ce-select,
    html.dark body .ce-textarea,
    body.dark-mode .ce-input,
    body.dark-mode .ce-select,
    body.dark-mode .ce-textarea {
        background: #0b1d34;
        border-color: #2a3e5b;
        color: #e2e8f0;
    }

    html.dark body .ce-input::placeholder,
    html.dark body .ce-textarea::placeholder,
    body.dark-mode .ce-input::placeholder,
    body.dark-mode .ce-textarea::placeholder {
        color: #94a3b8;
    }

    html.dark body .ce-section-head,
    body.dark-mode .ce-section-head {
        background: linear-gradient(180deg, #12243d 0%, #102035 100%);
        border-color: #23354d;
    }

    html.dark body .ce-btn.secondary,
    html.dark body .ce-back-btn,
    html.dark body .ce-hero-btn.secondary,
    body.dark-mode .ce-btn.secondary,
    body.dark-mode .ce-back-btn,
    body.dark-mode .ce-hero-btn.secondary {
        background: #173450;
        border-color: #3f6284;
        color: #d2e6fb;
    }

    html.dark body .ce-bmi-box,
    body.dark-mode .ce-bmi-box {
        background: rgba(14, 116, 144, 0.22);
        border-color: rgba(56, 189, 248, 0.38);
        color: #bae6fd;
    }

    html.dark body input[type="date"]::-webkit-calendar-picker-indicator,
    html.dark body input[type="datetime-local"]::-webkit-calendar-picker-indicator,
    body.dark-mode input[type="date"]::-webkit-calendar-picker-indicator,
    body.dark-mode input[type="datetime-local"]::-webkit-calendar-picker-indicator {
        filter: invert(0.9) sepia(0.1) saturate(0.8) hue-rotate(180deg);
        opacity: 0.9;
    }

    @media (max-width: 1199px) {
        .ce-hero-grid,
        .ce-main-grid {
            grid-template-columns: 1fr;
        }

        .ce-side-sticky {
            position: static;
        }
    }

    @media (max-width: 767px) {
        .ce-page {
            padding-left: 0;
            padding-right: 0;
        }

        .ce-form-grid,
        .ce-form-grid-3,
        .ce-bp-grid {
            grid-template-columns: 1fr;
        }

        .ce-bp-divider {
            display: none;
        }

        .ce-actions,
        .ce-sticky-body {
            flex-direction: column;
        }

        .ce-btn,
        .ce-hero-btn,
        .ce-back-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid ce-page">
    <div class="ce-shell">
        <section class="ce-hero">
            <div class="ce-hero-grid">
                <div>
                    <span class="ce-eyebrow">Edition clinique</span>
                    <div class="ce-title-row">
                        <a href="{{ route('consultations.index') }}" class="ce-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                        <span class="ce-icon"><i class="fas fa-pen-to-square"></i></span>
                        <div>
                            <h1 class="ce-title">Modifier Consultation #{{ $consultation->id }}</h1>
                            <p class="ce-subtitle">Mettez a jour les donnees medicales et le contenu clinique depuis une interface harmonisee, puis utilisez la fiche detail pour l assistant IA medical.</p>
                        </div>
                    </div>

                    <div class="ce-badges">
                        <span class="ce-badge"><i class="fas fa-file-medical"></i>Consultation #{{ $consultation->id }}</span>
                        <span class="ce-status {{ $statusClass }}"><i class="fas fa-circle"></i>{{ $statusLabel }}</span>
                        @if($consultation->rendezvous)
                            <span class="ce-chip"><i class="fas fa-calendar-check"></i>RDV lie #{{ $consultation->rendezvous->id }}</span>
                        @endif
                    </div>

                    <div class="ce-actions">
                        <a href="{{ route('consultations.show', $consultation->id) }}" class="ce-hero-btn secondary">
                            <i class="fas fa-eye"></i>
                            Voir le detail
                        </a>
                        <a href="{{ route('factures.create', ['consultation_id' => $consultation->id]) }}" class="ce-hero-btn secondary">
                            <i class="fas fa-file-invoice-dollar"></i>
                            Creer la facture
                        </a>
                        <a href="{{ route('ordonnances.create', ['consultation_id' => $consultation->id]) }}" class="ce-hero-btn primary">
                            <i class="fas fa-prescription"></i>
                            Nouvelle ordonnance
                        </a>
                    </div>
                </div>

                <aside class="ce-summary-card">
                    <p class="ce-summary-label">Resume patient</p>
                    <p class="ce-summary-value">{{ $consultation->patient ? strtoupper($consultation->patient->nom) : 'Patient' }}</p>
                    <p class="ce-summary-copy">{{ $consultation->patient ? trim(($consultation->patient->prenom ?? '') . ' ' . ($consultation->patient->nom ?? '')) : 'Patient non renseigne' }}</p>

                    <div class="ce-summary-list">
                        <div class="ce-summary-item">
                            <span>Medecin</span>
                            <strong>{{ $consultation->medecin ? trim(($consultation->medecin->prenom ?? '') . ' ' . ($consultation->medecin->nom ?? '')) : 'Non renseigne' }}</strong>
                        </div>
                        <div class="ce-summary-item">
                            <span>Date</span>
                            <strong>{{ $consultationDate ? $consultationDate->format('d/m/Y H:i') : 'Non planifiee' }}</strong>
                        </div>
                        <div class="ce-summary-item">
                            <span>Historique IA</span>
                            <strong>{{ $aiGenerationsCount }} entree{{ $aiGenerationsCount > 1 ? 's' : '' }}</strong>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <form action="{{ route('consultations.update', $consultation->id) }}" method="POST" id="consultationForm">
            @csrf
            @method('PUT')

            <div class="ce-main-grid">
                <div>
                    <section class="ce-section">
                        <div class="ce-section-head">
                            <span class="ce-section-kicker">Fiche de consultation</span>
                            <h2 class="ce-section-title"><i class="fas fa-user-md"></i> Informations generales</h2>
                        </div>
                        <div class="ce-section-body">
                            <div class="ce-form-grid">
                                <div>
                                    <label for="patient_id" class="ce-label required">Patient</label>
                                    <select name="patient_id" id="patient_id" class="ce-select @error('patient_id') is-invalid @enderror" required>
                                        <option value="">Selectionner un patient</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" {{ old('patient_id', $consultation->patient_id) == $patient->id ? 'selected' : '' }}>
                                                {{ strtoupper($patient->nom) }} {{ $patient->prenom }}
                                                @if($patient->date_naissance)
                                                    ({{ \Carbon\Carbon::parse($patient->date_naissance)->age }} ans)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="medecin_id" class="ce-label required">Medecin</label>
                                    <select name="medecin_id" id="medecin_id" class="ce-select @error('medecin_id') is-invalid @enderror" required>
                                        <option value="">Selectionner un medecin</option>
                                        @foreach($medecins as $medecin)
                                            <option value="{{ $medecin->id }}" {{ old('medecin_id', $consultation->medecin_id) == $medecin->id ? 'selected' : '' }}>
                                                Dr. {{ trim(($medecin->prenom ?? '') . ' ' . ($medecin->nom ?? '')) ?: ('Medecin #' . $medecin->id) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('medecin_id')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="date_consultation" class="ce-label required">Date et heure de consultation</label>
                                    <input type="datetime-local" name="date_consultation" id="date_consultation" class="ce-input @error('date_consultation') is-invalid @enderror" value="{{ old('date_consultation', $consultation->date_consultation ? \Carbon\Carbon::parse($consultation->date_consultation)->format('Y-m-d\TH:i') : '') }}" required>
                                    @error('date_consultation')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="ce-section">
                        <div class="ce-section-head">
                            <span class="ce-section-kicker">Constantes</span>
                            <h2 class="ce-section-title"><i class="fas fa-heartbeat"></i> Signes vitaux</h2>
                        </div>
                        <div class="ce-section-body">
                            <div class="ce-form-grid-3">
                                <div>
                                    <label for="temperature" class="ce-label">Temperature (Ã‚Â°C)</label>
                                    <input type="number" name="temperature" id="temperature" class="ce-input @error('temperature') is-invalid @enderror" step="0.1" min="35" max="42" value="{{ old('temperature', $consultation->temperature) }}" placeholder="36.5">
                                    @error('temperature')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="ce-field-full">
                                    <label class="ce-label">Tension arterielle (mmHg)</label>
                                    <div class="ce-bp-grid">
                                        <input type="number" name="tension_arterielle_systolique" id="tension_systolique" class="ce-input @error('tension_arterielle_systolique') is-invalid @enderror" placeholder="120" min="80" max="200" value="{{ old('tension_arterielle_systolique', $consultation->tension_arterielle_systolique) }}">
                                        <span class="ce-bp-divider">/</span>
                                        <input type="number" name="tension_arterielle_diastolique" id="tension_diastolique" class="ce-input @error('tension_arterielle_diastolique') is-invalid @enderror" placeholder="80" min="50" max="120" value="{{ old('tension_arterielle_diastolique', $consultation->tension_arterielle_diastolique) }}">
                                    </div>
                                    @error('tension_arterielle_systolique')<div class="ce-feedback">{{ $message }}</div>@enderror
                                    @error('tension_arterielle_diastolique')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="poids" class="ce-label">Poids (kg)</label>
                                    <input type="number" name="poids" id="poids" class="ce-input @error('poids') is-invalid @enderror" step="0.1" min="1" max="300" value="{{ old('poids', $consultation->poids) }}" placeholder="70.5">
                                    @error('poids')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="taille" class="ce-label">Taille (cm)</label>
                                    <input type="number" name="taille" id="taille" class="ce-input @error('taille') is-invalid @enderror" step="0.1" min="50" max="250" value="{{ old('taille', $consultation->taille) }}" placeholder="170">
                                    @error('taille')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="ce-bmi-box" id="bmiCalculator">
                                <i class="fas fa-calculator"></i>
                                <span>IMC calcule: <strong id="bmiResult">--</strong></span>
                            </div>
                        </div>
                    </section>

                    <section class="ce-section">
                        <div class="ce-section-head">
                            <span class="ce-section-kicker">Evaluation</span>
                            <h2 class="ce-section-title"><i class="fas fa-notes-medical"></i> Informations cliniques</h2>
                        </div>
                        <div class="ce-section-body">
                            <div class="ce-form-grid">
                                <div class="ce-field-full">
                                    <label for="symptomes" class="ce-label">Symptomes presentes</label>
                                    <textarea name="symptomes" id="symptomes" class="ce-textarea @error('symptomes') is-invalid @enderror" rows="4" placeholder="Decrivez les symptomes presentes par le patient...">{{ old('symptomes', $consultation->symptomes) }}</textarea>
                                    @error('symptomes')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="ce-field-full">
                                    <label for="examen_clinique" class="ce-label">Examen clinique</label>
                                    <textarea name="examen_clinique" id="examen_clinique" class="ce-textarea @error('examen_clinique') is-invalid @enderror" rows="4" placeholder="Resultats de l'examen clinique...">{{ old('examen_clinique', $consultation->examen_clinique) }}</textarea>
                                    @error('examen_clinique')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="ce-field-full">
                                    <label for="diagnostic" class="ce-label">Diagnostic</label>
                                    <textarea name="diagnostic" id="diagnostic" class="ce-textarea @error('diagnostic') is-invalid @enderror" rows="4" placeholder="Diagnostic pose...">{{ old('diagnostic', $consultation->diagnostic) }}</textarea>
                                    @error('diagnostic')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="ce-section">
                        <div class="ce-section-head">
                            <span class="ce-section-kicker">Conclusion et suivi</span>
                            <h2 class="ce-section-title"><i class="fas fa-pills"></i> Traitement et recommandations</h2>
                        </div>
                        <div class="ce-section-body">
                            <div class="ce-form-grid">
                                <div class="ce-field-full">
                                    <label for="traitement_prescrit" class="ce-label">Traitement prescrit</label>
                                    <textarea name="traitement_prescrit" id="traitement_prescrit" class="ce-textarea @error('traitement_prescrit') is-invalid @enderror" rows="4" placeholder="Traitement medicamenteux prescrit...">{{ old('traitement_prescrit', $consultation->traitement_prescrit) }}</textarea>
                                    @error('traitement_prescrit')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="ce-field-full">
                                    <label for="recommandations" class="ce-label">Recommandations</label>
                                    <textarea name="recommandations" id="recommandations" class="ce-textarea @error('recommandations') is-invalid @enderror" rows="4" placeholder="Recommandations et conseils...">{{ old('recommandations', $consultation->recommandations) }}</textarea>
                                    @error('recommandations')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="date_prochaine_visite" class="ce-label">Date de prochaine visite</label>
                                    <input type="date" name="date_prochaine_visite" id="date_prochaine_visite" class="ce-input @error('date_prochaine_visite') is-invalid @enderror" value="{{ old('date_prochaine_visite', $consultation->date_prochaine_visite ? \Carbon\Carbon::parse($consultation->date_prochaine_visite)->format('Y-m-d') : '') }}">
                                    @error('date_prochaine_visite')<div class="ce-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="ce-side-sticky">
                    <section class="ce-section ce-sticky-card">
                        <div class="ce-section-head">
                            <span class="ce-section-kicker">Actions</span>
                            <h2 class="ce-section-title"><i class="fas fa-cogs"></i> Finalisation</h2>
                        </div>
                        <div class="ce-section-body ce-sticky-body">
                            <button type="submit" class="ce-btn primary">
                                <i class="fas fa-save"></i>
                                Enregistrer les modifications
                            </button>

                            <a href="{{ route('consultations.show', $consultation->id) }}" class="ce-btn secondary">
                                <i class="fas fa-eye"></i>
                                Voir le detail
                            </a>

                            <a href="{{ route('consultations.index') }}" class="ce-btn secondary">
                                <i class="fas fa-arrow-left"></i>
                                Retour a la liste
                            </a>
                        </div>
                    </section>

                    <section class="ce-section ce-sticky-card">
                        <div class="ce-section-head">
                            <span class="ce-section-kicker">Rappel</span>
                            <h2 class="ce-section-title"><i class="fas fa-info-circle"></i> Bonnes pratiques</h2>
                        </div>
                        <div class="ce-section-body">
                            <ul class="ce-info-list">
                                <li>Les champs patient, medecin et date restent obligatoires.</li>
                                <li>Completez les constantes pour fiabiliser l'historique clinique.</li>
                                <li>L assistant IA est desormais disponible sur la fiche detail de consultation.</li>
                                <li>Le detail consultation reste accessible pendant l'edition.</li>
                            </ul>
                        </div>
                    </section>
                </aside>
            </div>
        </form>
    </div>
</div>
@endsection


