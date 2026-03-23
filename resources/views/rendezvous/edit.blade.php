@extends('layouts.app')

@section('title', 'Modifier rendez-vous')

@push('styles')
<style>
    .rdv-edit-page {
        --rdv-edit-bg: radial-gradient(circle at top right, rgba(37, 99, 235, 0.08) 0%, rgba(37, 99, 235, 0) 28%), linear-gradient(180deg, #f4f8fc 0%, #f9fbff 100%);
        --rdv-edit-surface: #ffffff;
        --rdv-edit-surface-soft: #f7fbff;
        --rdv-edit-border: #d8e4f0;
        --rdv-edit-border-strong: #c7d7e8;
        --rdv-edit-title: #173454;
        --rdv-edit-text: #45617d;
        --rdv-edit-muted: #6c829b;
        --rdv-edit-primary: #2563eb;
        --rdv-edit-primary-strong: #1d4fbe;
        --rdv-edit-shadow: 0 22px 46px -34px rgba(15, 45, 82, 0.28);
        --rdv-edit-shadow-hover: 0 28px 52px -32px rgba(15, 45, 82, 0.36);
        padding: clamp(0.35rem, 0.8vw, 0.7rem);
        width: 100%;
        min-height: calc(100vh - 120px);
    }

    .rdv-edit-wrap {
        width: 100%;
        max-width: none;
        margin: 0;
    }

    .rdv-edit-head {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--rdv-edit-border);
        border-radius: 24px;
        background: var(--rdv-edit-bg);
        box-shadow: var(--rdv-edit-shadow);
        padding: 1.35rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .rdv-edit-head::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.55) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .rdv-edit-head > * {
        position: relative;
        z-index: 1;
    }

    .rdv-edit-head-main {
        display: grid;
        gap: 0.9rem;
        min-width: 0;
        flex: 1 1 520px;
    }

    .rdv-edit-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        width: fit-content;
        min-height: 32px;
        padding: 0 0.85rem;
        border-radius: 999px;
        border: 1px solid #d6e3f1;
        background: rgba(255, 255, 255, 0.78);
        color: var(--rdv-edit-primary-strong);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .rdv-edit-title-wrap {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .rdv-edit-title-icon {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        display: inline-grid;
        place-items: center;
        flex: 0 0 auto;
        background: linear-gradient(135deg, var(--rdv-edit-primary) 0%, var(--rdv-edit-primary-strong) 100%);
        color: #fff;
        font-size: 1.35rem;
        box-shadow: 0 20px 30px -24px rgba(37, 99, 235, 0.55);
    }

    .rdv-edit-copy {
        display: grid;
        gap: 0.45rem;
        min-width: 0;
    }

    .rdv-edit-title {
        margin: 0;
        color: var(--rdv-edit-title);
        font-size: clamp(1.55rem, 2.7vw, 2.3rem);
        font-weight: 800;
        line-height: 1.04;
        letter-spacing: -0.04em;
    }

    .rdv-edit-sub {
        margin: 0;
        color: var(--rdv-edit-muted);
        font-size: 0.98rem;
        line-height: 1.68;
        font-weight: 600;
        max-width: 76ch;
    }

    .rdv-edit-badges {
        display: flex;
        gap: 0.7rem;
        flex-wrap: wrap;
    }

    .rdv-edit-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        min-height: 36px;
        padding: 0 0.85rem;
        border-radius: 999px;
        border: 1px solid #d5e2f1;
        background: rgba(255, 255, 255, 0.82);
        color: #547089;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .rdv-edit-actions {
        display: flex;
        gap: 0.7rem;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
    }

    .rdv-btn {
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid transparent;
        padding: 0.72rem 1.05rem;
        font-size: 0.94rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        transition: all 0.2s ease;
        cursor: pointer;
        white-space: nowrap;
    }

    .rdv-btn.primary {
        background: linear-gradient(135deg, var(--rdv-edit-primary) 0%, var(--rdv-edit-primary-strong) 100%);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 18px 28px -24px rgba(37, 99, 235, 0.48);
    }

    .rdv-btn.primary:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 22px 32px -24px rgba(37, 99, 235, 0.55);
    }

    .rdv-btn.soft {
        background: linear-gradient(180deg, #ffffff 0%, #f3f7fb 100%);
        color: #486482;
        border-color: #d7e1ec;
        box-shadow: 0 14px 22px -24px rgba(15, 45, 82, 0.3);
    }

    .rdv-btn.soft:hover {
        background: linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
        color: #2c4b6c;
        transform: translateY(-1px);
    }

    .rdv-edit-grid {
        margin-top: 1.15rem;
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
        gap: 1rem;
        align-items: start;
    }

    .rdv-panel {
        background: var(--rdv-edit-surface);
        border: 1px solid var(--rdv-edit-border);
        border-radius: 22px;
        box-shadow: var(--rdv-edit-shadow);
        overflow: hidden;
    }

    .rdv-panel-head {
        background: linear-gradient(180deg, #fbfdff 0%, #f3f8fd 100%);
        border-bottom: 1px solid #e8f0f8;
        padding: 1rem 1.15rem;
    }

    .rdv-panel-title {
        margin: 0;
        font-size: 1.06rem;
        color: var(--rdv-edit-title);
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
    }

    .rdv-panel-subtitle {
        margin: 0.45rem 0 0;
        color: var(--rdv-edit-muted);
        font-size: 0.88rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .rdv-panel-body {
        padding: 1.15rem;
    }

    .rdv-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .rdv-field {
        display: grid;
        gap: 0.48rem;
    }

    .rdv-field.full {
        grid-column: 1 / -1;
    }

    .rdv-label {
        color: #334155;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .rdv-label .required {
        color: #ef4444;
    }

    .rdv-input,
    .rdv-select,
    .rdv-textarea {
        width: 100%;
        border: 1px solid #d6e2ee;
        border-radius: 14px;
        min-height: 52px;
        padding: 0.86rem 0.92rem;
        background: #fbfdff;
        color: #12304f;
        font-size: 0.94rem;
        font-weight: 600;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, transform 0.2s ease;
    }

    .rdv-input::placeholder,
    .rdv-textarea::placeholder {
        color: #8aa0ba;
        font-weight: 500;
    }

    .rdv-select {
        appearance: none;
        background-image: linear-gradient(45deg, transparent 50%, #5b7694 50%), linear-gradient(135deg, #5b7694 50%, transparent 50%);
        background-position: calc(100% - 18px) calc(50% - 3px), calc(100% - 12px) calc(50% - 3px);
        background-size: 6px 6px, 6px 6px;
        background-repeat: no-repeat;
        padding-right: 2.7rem;
    }

    .rdv-input-icon-wrap {
        position: relative;
    }

    .rdv-input-icon {
        position: absolute;
        top: 50%;
        left: 0.95rem;
        transform: translateY(-50%);
        color: #7390ae;
        pointer-events: none;
        font-size: 0.95rem;
    }

    .rdv-input-icon-wrap .rdv-input {
        padding-left: 2.7rem;
    }

    .rdv-textarea {
        min-height: 132px;
        padding-top: 0.92rem;
        resize: vertical;
    }

    .rdv-input:focus,
    .rdv-select:focus,
    .rdv-textarea:focus {
        outline: none;
        border-color: var(--rdv-edit-primary);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .rdv-input.is-invalid,
    .rdv-select.is-invalid,
    .rdv-textarea.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
    }

    .rdv-invalid {
        color: #dc2626;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .rdv-panel-foot {
        border-top: 1px solid #e8f0f8;
        background: linear-gradient(180deg, #fbfdff 0%, #f4f8fc 100%);
        padding: 1rem 1.15rem;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.7rem;
        flex-wrap: wrap;
    }

    .rdv-side-stack {
        display: grid;
        gap: 1rem;
    }

    .rdv-person {
        display: grid;
        grid-template-columns: 64px minmax(0, 1fr);
        gap: 0.9rem;
        align-items: start;
    }

    .rdv-avatar {
        width: 64px;
        height: 64px;
        border-radius: 22px;
        background: linear-gradient(135deg, var(--rdv-edit-primary) 0%, #4c8af7 100%);
        color: #fff;
        font-weight: 800;
        font-size: 1.08rem;
        display: grid;
        place-items: center;
        letter-spacing: 0.04em;
        box-shadow: 0 18px 26px -22px rgba(37, 99, 235, 0.48);
    }

    .rdv-person-copy {
        display: grid;
        gap: 0.38rem;
        min-width: 0;
    }

    .rdv-person-name {
        margin: 0;
        color: var(--rdv-edit-title);
        font-size: 1.02rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .rdv-person-sub {
        margin: 0;
        color: var(--rdv-edit-muted);
        font-size: 0.88rem;
        line-height: 1.55;
        font-weight: 600;
        word-break: break-word;
    }

    .rdv-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        border: 1px solid #d4e1f1;
        background: #f5f9ff;
        color: #2b5f97;
        padding: 0.38rem 0.72rem;
        font-size: 0.76rem;
        font-weight: 800;
    }

    .rdv-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        margin-top: 0.85rem;
    }

    .rdv-side-link {
        margin-top: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        min-height: 40px;
        padding: 0 0.88rem;
        border-radius: 999px;
        border: 1px solid #d8e5f2;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #365271;
        font-size: 0.85rem;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .rdv-side-link:hover {
        color: #1d4fbe;
        background: linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
        transform: translateY(-1px);
    }

    .rdv-side-link.ghost {
        background: transparent;
        color: var(--rdv-edit-muted);
        border-style: dashed;
    }

    .rdv-side-link.ghost:hover {
        color: #365271;
        background: #f7fbff;
    }

    .rdv-meta-list {
        display: grid;
        gap: 0.85rem;
    }

    .rdv-meta-item {
        position: relative;
        border: 1px solid #e7eef7;
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9fd 100%);
        border-radius: 16px;
        padding: 0.9rem 0.9rem 0.9rem 1.25rem;
        display: grid;
        gap: 0.22rem;
    }

    .rdv-meta-item::before {
        content: "";
        position: absolute;
        left: 0.62rem;
        top: 1rem;
        bottom: -0.95rem;
        width: 2px;
        border-radius: 999px;
        background: linear-gradient(180deg, rgba(37, 99, 235, 0.28) 0%, rgba(37, 99, 235, 0.06) 100%);
    }

    .rdv-meta-item:last-child::before {
        bottom: 1rem;
    }

    .rdv-meta-item::after {
        content: "";
        position: absolute;
        left: 0.37rem;
        top: 0.96rem;
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #ffffff;
        border: 3px solid var(--rdv-edit-primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
    }

    .rdv-meta-key {
        color: var(--rdv-edit-muted);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .rdv-meta-val {
        color: var(--rdv-edit-title);
        font-size: 0.92rem;
        font-weight: 800;
    }

    .rdv-meta-copy {
        color: var(--rdv-edit-text);
        font-size: 0.84rem;
        font-weight: 600;
        line-height: 1.55;
    }

    .rdv-inline-note {
        margin-top: 0.8rem;
        border-radius: 16px;
        border: 1px solid #e5edf7;
        background: linear-gradient(180deg, #fbfdff 0%, #f6faff 100%);
        padding: 0.85rem 0.95rem;
        color: var(--rdv-edit-text);
        font-size: 0.87rem;
        line-height: 1.6;
        font-weight: 600;
    }

    body.dark-mode .rdv-edit-page {
        --rdv-edit-bg: linear-gradient(180deg, #0f1f31 0%, #0d1a2b 100%);
        --rdv-edit-surface: #102137;
        --rdv-edit-surface-soft: #13263f;
        --rdv-edit-border: #304b69;
        --rdv-edit-border-strong: #395a7a;
        --rdv-edit-title: #e5eefc;
        --rdv-edit-text: #d3e3f7;
        --rdv-edit-muted: #a5bbd4;
    }

    body.dark-mode .rdv-edit-head,
    body.dark-mode .rdv-panel {
        background: var(--rdv-edit-surface);
        border-color: var(--rdv-edit-border);
        box-shadow: 0 10px 26px rgba(0, 0, 0, 0.35);
    }

    body.dark-mode .rdv-panel-head,
    body.dark-mode .rdv-panel-foot {
        background: #0f1d2f;
        border-color: #304b69;
    }

    body.dark-mode .rdv-edit-title,
    body.dark-mode .rdv-panel-title,
    body.dark-mode .rdv-person-name,
    body.dark-mode .rdv-meta-val,
    body.dark-mode .rdv-edit-badge {
        color: #e5e7eb;
    }

    body.dark-mode .rdv-edit-eyebrow,
    body.dark-mode .rdv-edit-badge {
        background: rgba(19, 43, 69, 0.72);
        border-color: #355978;
        color: #d4e7fb;
    }

    body.dark-mode .rdv-edit-sub,
    body.dark-mode .rdv-label,
    body.dark-mode .rdv-person-sub,
    body.dark-mode .rdv-meta-key,
    body.dark-mode .rdv-meta-copy {
        color: #9ca3af;
    }

    body.dark-mode .rdv-btn.soft {
        color: #d2e6fb;
        border-color: #3c5f81;
        background: linear-gradient(180deg, #183554 0%, #17324d 100%);
    }

    body.dark-mode .rdv-input,
    body.dark-mode .rdv-select,
    body.dark-mode .rdv-textarea,
    body.dark-mode .rdv-meta-item {
        background: #111827;
        border-color: #374151;
        color: #e5e7eb;
    }

    body.dark-mode .rdv-side-link {
        background: linear-gradient(180deg, #173456 0%, #15314f 100%);
        border-color: #3f6795;
        color: #d3e8ff;
    }

    body.dark-mode .rdv-side-link.ghost {
        background: transparent;
        border-color: #355978;
        color: #a5bbd4;
    }

    body.dark-mode .rdv-chip,
    body.dark-mode .rdv-inline-note {
        background: #13263f;
        border-color: #35506a;
        color: #d8e8fb;
    }

    body.dark-mode .rdv-input::placeholder,
    body.dark-mode .rdv-textarea::placeholder,
    body.dark-mode .rdv-input-icon {
        color: #89a3c2;
    }

    @media (max-width: 1200px) {
        .rdv-edit-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .rdv-edit-page {
            padding: 0.4rem;
            min-height: auto;
        }

        .rdv-edit-head {
            border-radius: 18px;
            padding: 1rem;
        }

        .rdv-edit-actions {
            width: 100%;
            justify-content: stretch;
        }

        .rdv-btn {
            flex: 1 1 100%;
        }

        .rdv-edit-title-wrap {
            gap: 0.85rem;
        }

        .rdv-edit-title-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            font-size: 1.15rem;
        }

        .rdv-form-grid {
            grid-template-columns: 1fr;
        }

        .rdv-panel-body {
            padding: 0.9rem;
        }

        .rdv-panel-foot {
            justify-content: stretch;
        }

        .rdv-person {
            grid-template-columns: 56px minmax(0, 1fr);
        }

        .rdv-avatar {
            width: 56px;
            height: 56px;
            border-radius: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="rdv-edit-page">
    <div class="rdv-edit-wrap">
        <section class="rdv-edit-head">
            <div class="rdv-edit-head-main">
                <span class="rdv-edit-eyebrow"><i class="fas fa-pen-to-square"></i> Planning cabinet</span>
                <div class="rdv-edit-title-wrap">
                    <span class="rdv-edit-title-icon"><i class="fas fa-calendar-pen"></i></span>
                    <div class="rdv-edit-copy">
                        <h1 class="rdv-edit-title">Modifier rendez-vous</h1>
                        <p class="rdv-edit-sub">Ajustez la date, le praticien, le motif et le statut dans une interface plus lisible, plus aeree et coherente avec le reste du produit.</p>
                    </div>
                </div>
                <div class="rdv-edit-badges">
                    <span class="rdv-edit-badge"><i class="fas fa-hashtag"></i> Rendez-vous #{{ $rendezvous->id }}</span>
                    <span class="rdv-edit-badge"><i class="fas fa-clock"></i> {{ $rendezvous->duree }} min</span>
                </div>
            </div>

            <div class="rdv-edit-actions">
                <a href="{{ route('rendezvous.show', $rendezvous->id) }}" class="rdv-btn soft">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
            </div>
        </section>

        <section class="rdv-edit-grid">
            <form action="{{ route('rendezvous.update', $rendezvous->id) }}" method="POST" class="rdv-panel">
                @csrf
                @method('PUT')

                <header class="rdv-panel-head">
                    <h2 class="rdv-panel-title"><i class="fas fa-clipboard-list"></i> Informations du rendez-vous</h2>
                    <p class="rdv-panel-subtitle">Reprenez les informations cles du rendez-vous avec des champs plus confortables, une hierarchie plus nette et des interactions plus explicites.</p>
                </header>

                <div class="rdv-panel-body">
                    <div class="rdv-form-grid">
                        <div class="rdv-field">
                            <label for="patient_id" class="rdv-label">Patient <span class="required">*</span></label>
                            <select name="patient_id" id="patient_id" class="rdv-select @error('patient_id') is-invalid @enderror" required>
                                <option value="">Choisir le patient concerne</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id', $rendezvous->patient_id) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->nom }} {{ $patient->prenom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <div class="rdv-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="rdv-field">
                            <label for="duree" class="rdv-label">Duree (minutes) <span class="required">*</span></label>
                            <select name="duree" id="duree" class="rdv-select @error('duree') is-invalid @enderror" required>
                                @for($i = 15; $i <= 240; $i += 15)
                                    <option value="{{ $i }}" {{ old('duree', $rendezvous->duree) == $i ? 'selected' : '' }}>
                                        {{ $i }} minutes
                                    </option>
                                @endfor
                            </select>
                            @error('duree')
                                <div class="rdv-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="rdv-field">
                            <label for="medecin_id" class="rdv-label">Medecin <span class="required">*</span></label>
                            <select name="medecin_id" id="medecin_id" class="rdv-select @error('medecin_id') is-invalid @enderror" required>
                                <option value="">Choisir le praticien responsable</option>
                                @foreach($medecins as $medecin)
                                    <option value="{{ $medecin->id }}" {{ old('medecin_id', $rendezvous->medecin_id) == $medecin->id ? 'selected' : '' }}>
                                        Dr. {{ $medecin->nom }} - {{ $medecin->specialite }}
                                    </option>
                                @endforeach
                            </select>
                            @error('medecin_id')
                                <div class="rdv-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="rdv-field">
                            <label for="type" class="rdv-label">Type de rendez-vous <span class="required">*</span></label>
                            <select name="type" id="type" class="rdv-select @error('type') is-invalid @enderror" required>
                                <option value="">Choisir le type de prise en charge</option>
                                <option value="Consultation" {{ old('type', $rendezvous->type) == 'Consultation' ? 'selected' : '' }}>Consultation generale</option>
                                <option value="Suivi" {{ old('type', $rendezvous->type) == 'Suivi' ? 'selected' : '' }}>Suivi</option>
                                <option value="Urgence" {{ old('type', $rendezvous->type) == 'Urgence' ? 'selected' : '' }}>Urgence</option>
                                <option value="Controle" {{ old('type', $rendezvous->type) == 'Controle' ? 'selected' : '' }}>Controle</option>
                                <option value="Autre" {{ old('type', $rendezvous->type) == 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('type')
                                <div class="rdv-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="rdv-field">
                            <label for="date_heure" class="rdv-label">Date et heure <span class="required">*</span></label>
                            <div class="rdv-input-icon-wrap">
                                <span class="rdv-input-icon"><i class="fas fa-calendar-day"></i></span>
                                <input type="datetime-local" name="date_heure" id="date_heure"
                                       class="rdv-input @error('date_heure') is-invalid @enderror"
                                       value="{{ old('date_heure', optional($rendezvous->date_heure)->format('Y-m-d\\TH:i')) }}" required>
                            </div>
                            @error('date_heure')
                                <div class="rdv-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="rdv-field">
                            <label for="statut" class="rdv-label">Statut <span class="required">*</span></label>
                            <select name="statut" id="statut" class="rdv-select @error('statut') is-invalid @enderror" required>
                                <option value="{{ $statusAvenir }}" {{ old('statut', $rendezvous->statut) === $statusAvenir ? 'selected' : '' }}>A venir</option>
                                <option value="en_attente" {{ old('statut', $rendezvous->statut) === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_soins" {{ old('statut', $rendezvous->statut) === 'en_soins' ? 'selected' : '' }}>En soins</option>
                                <option value="vu" {{ old('statut', $rendezvous->statut) === 'vu' ? 'selected' : '' }}>Vu</option>
                                <option value="absent" {{ old('statut', $rendezvous->statut) === 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="{{ $statusAnnule }}" {{ old('statut', $rendezvous->statut) === $statusAnnule ? 'selected' : '' }}>Annule</option>
                            </select>
                            @error('statut')
                                <div class="rdv-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="rdv-field full">
                            <label for="motif" class="rdv-label">Motif <span class="required">*</span></label>
                            <textarea name="motif" id="motif" rows="3"
                                      class="rdv-textarea @error('motif') is-invalid @enderror"
                                      placeholder="Precisez la raison de la consultation, le contexte ou la demande du patient..." required>{{ old('motif', $rendezvous->motif) }}</textarea>
                            @error('motif')
                                <div class="rdv-invalid">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="rdv-field full">
                            <label for="notes" class="rdv-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="rdv-textarea @error('notes') is-invalid @enderror"
                                      placeholder="Ajoutez des informations internes utiles pour l'equipe ou le suivi de ce rendez-vous...">{{ old('notes', $rendezvous->notes) }}</textarea>
                            @error('notes')
                                <div class="rdv-invalid">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <footer class="rdv-panel-foot">
                    <button type="submit" class="rdv-btn primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('rendezvous.show', $rendezvous->id) }}" class="rdv-btn soft">Annuler</a>
                </footer>
            </form>

            <aside class="rdv-side-stack">
                <article class="rdv-panel">
                    <header class="rdv-panel-head">
                        <h2 class="rdv-panel-title"><i class="fas fa-user"></i> Patient actuel</h2>
                        <p class="rdv-panel-subtitle">Acces rapide au dossier et aux coordonnees du patient lie a ce rendez-vous.</p>
                    </header>
                    <div class="rdv-panel-body">
                        <div class="rdv-person">
                            <div class="rdv-avatar">{{ $patientInitials !== '' ? $patientInitials : 'PA' }}</div>
                            <div class="rdv-person-copy">
                                <p class="rdv-person-name">{{ $patientName }}</p>
                                <p class="rdv-person-sub">{{ $rendezvous->patient->email ?? 'Email non renseigne' }}</p>
                                <p class="rdv-person-sub">{{ $rendezvous->patient->telephone ?? 'Telephone non renseigne' }}</p>
                            </div>
                        </div>
                        <div class="rdv-chip-row">
                            <span class="rdv-chip"><i class="fas fa-folder-open"></i> Dossier lie</span>
                            <span class="rdv-chip"><i class="fas fa-id-card"></i> Fiche patient active</span>
                        </div>
                        <a href="{{ route('patients.show', $rendezvous->patient->id) }}" class="rdv-side-link">
                            <i class="fas fa-arrow-up-right-from-square"></i> Ouvrir le dossier patient
                        </a>
                    </div>
                </article>

                <article class="rdv-panel">
                    <header class="rdv-panel-head">
                        <h2 class="rdv-panel-title"><i class="fas fa-user-doctor"></i> Medecin actuel</h2>
                        <p class="rdv-panel-subtitle">Retrouvez en un coup d'oeil le praticien actuellement assigne a ce rendez-vous.</p>
                    </header>
                    <div class="rdv-panel-body">
                        <div class="rdv-person">
                            <div class="rdv-avatar">{{ $medecinInitials !== '' ? $medecinInitials : 'DR' }}</div>
                            <div class="rdv-person-copy">
                                <p class="rdv-person-name">Dr. {{ $medecinName }}</p>
                                <p class="rdv-person-sub">{{ $rendezvous->medecin->specialite ?? 'Specialite non renseignee' }}</p>
                                <p class="rdv-person-sub">{{ $rendezvous->medecin->email ?? 'Email non renseigne' }}</p>
                            </div>
                        </div>
                        <div class="rdv-chip-row">
                            <span class="rdv-chip"><i class="fas fa-stethoscope"></i> Planning connecte</span>
                            <span class="rdv-chip"><i class="fas fa-briefcase-medical"></i> Profil praticien</span>
                        </div>
                        <a href="{{ route('medecins.show', $rendezvous->medecin->id) }}" class="rdv-side-link ghost">
                            <i class="fas fa-user-md"></i> Voir le profil medecin
                        </a>
                    </div>
                </article>

                <article class="rdv-panel">
                    <header class="rdv-panel-head">
                        <h2 class="rdv-panel-title"><i class="fas fa-clock-rotate-left"></i> Historique</h2>
                        <p class="rdv-panel-subtitle">Reperez rapidement les temps forts du rendez-vous grace a une lecture type timeline.</p>
                    </header>
                    <div class="rdv-panel-body">
                        <div class="rdv-meta-list">
                            <div class="rdv-meta-item">
                                <span class="rdv-meta-key">Cree le</span>
                                <span class="rdv-meta-val">{{ optional($rendezvous->created_at)->format('d/m/Y H:i') }}</span>
                                <span class="rdv-meta-copy">Creation initiale du rendez-vous dans le planning du cabinet.</span>
                            </div>
                            <div class="rdv-meta-item">
                                <span class="rdv-meta-key">Mis a jour</span>
                                <span class="rdv-meta-val">{{ optional($rendezvous->updated_at)->format('d/m/Y H:i') }}</span>
                                <span class="rdv-meta-copy">Derniere synchronisation des informations affichees sur cette fiche.</span>
                            </div>
                        </div>
                        <div class="rdv-inline-note">
                            <i class="fas fa-circle-info"></i>
                            Les ajustements enregistres ici restent alignes avec l'agenda et la fiche detail du rendez-vous.
                        </div>
                    </div>
                </article>
            </aside>
        </section>
    </div>
</div>
@endsection
