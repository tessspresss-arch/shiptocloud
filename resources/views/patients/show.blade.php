@extends('layouts.app')

@section('title', 'Dossier Patient')

@push('styles')
<style>
    .patient-show-shell {
        width: 100%;
        max-width: none;
        padding: clamp(0.45rem, 1vw, 0.85rem) clamp(0.35rem, 0.9vw, 0.75rem);
    }

    .patient-show-wrap {
        width: 100%;
        max-width: none;
        margin: 0;
    }

    .patient-hero {
        background:
            radial-gradient(circle at top left, rgba(44, 123, 229, 0.12) 0%, rgba(44, 123, 229, 0) 26%),
            linear-gradient(135deg, #fbfdff 0%, #eef5ff 52%, #f6faff 100%);
        border: 1px solid rgba(200, 217, 239, 0.9);
        border-radius: 24px;
        box-shadow: 0 24px 44px -34px rgba(15, 23, 42, 0.2);
        padding: 1.25rem;
        backdrop-filter: blur(8px);
        position: relative;
        overflow: hidden;
    }

    .patient-hero::after {
        content: "";
        position: absolute;
        right: -90px;
        top: -90px;
        width: 240px;
        height: 240px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(96, 165, 250, 0.14) 0%, rgba(96, 165, 250, 0) 70%);
        pointer-events: none;
    }

    .patient-hero-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.1rem;
        position: relative;
        z-index: 1;
    }

    .patient-hero-intro {
        min-width: 0;
        flex: 1 1 420px;
    }

    .patient-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.42rem 0.72rem;
        border-radius: 999px;
        background: rgba(44, 123, 229, 0.1);
        color: #1f6fa3;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 0.65rem;
    }

    .patient-hero-title {
        margin: 0;
        color: #16324d;
        font-size: clamp(1.45rem, 2.6vw, 2.3rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
    }

    .patient-hero-title-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #1f6fa3;
        background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(226, 238, 255, 0.96) 100%);
        border: 1px solid rgba(191, 207, 223, 0.95);
        box-shadow: 0 14px 24px -22px rgba(44, 123, 229, 0.42);
        flex-shrink: 0;
    }

    .patient-hero-sub {
        margin: 0.42rem 0 0;
        color: #5b6b83;
        font-size: 0.98rem;
        max-width: 62ch;
    }

    .hero-actions {
        display: flex;
        gap: 0.65rem;
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .hero-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        min-height: 48px;
        padding: 0.72rem 1rem;
        border-radius: 16px;
        border: 1px solid transparent;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.92rem;
        transition: all 0.2s ease;
        letter-spacing: -0.01em;
    }

    .hero-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .hero-btn.primary {
        background: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%);
        color: #fff;
        box-shadow: 0 14px 26px -18px rgba(44, 123, 229, 0.52);
    }

    .hero-btn.primary .hero-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
    }

    .hero-btn.primary:hover {
        background: linear-gradient(135deg, #256fe0 0%, #1c628f 100%);
        color: #fff;
        transform: translateY(-1px);
    }

    .hero-btn.soft {
        background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(245,249,253,0.92) 100%);
        color: #16324d;
        border-color: rgba(191, 207, 223, 0.95);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.92), 0 16px 28px -26px rgba(15, 23, 42, 0.2);
    }

    .hero-btn.soft .hero-btn-icon {
        background: rgba(44, 123, 229, 0.1);
        color: #2c7be5;
    }

    .hero-btn.soft:hover {
        background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(236,244,251,0.98) 100%);
        color: #16324d;
        transform: translateY(-1px);
    }

    .patient-identity {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(260px, 0.95fr);
        gap: 1rem;
        align-items: stretch;
        position: relative;
        z-index: 1;
    }

    .patient-identity-card {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 1rem;
        align-items: center;
        padding: 1rem;
        border-radius: 22px;
        border: 1px solid rgba(214, 226, 241, 0.95);
        background: linear-gradient(180deg, rgba(255,255,255,0.92) 0%, rgba(247,251,255,0.96) 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.92);
    }

    .patient-id-line {
        margin-top: 0.45rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .record-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.42rem;
        min-height: 34px;
        padding: 0 0.82rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.04);
        color: #36506b;
        border: 1px solid rgba(203, 213, 225, 0.92);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .patient-avatar {
        width: 112px;
        height: 112px;
        border-radius: 999px;
        object-fit: cover;
        border: 4px solid rgba(255,255,255,0.95);
        box-shadow: 0 18px 30px -22px rgba(15, 23, 42, 0.26);
        background: #fff;
    }

    .patient-avatar-fallback {
        width: 112px;
        height: 112px;
        border-radius: 999px;
        background: linear-gradient(135deg, #e3f0ff 0%, #d5e8ff 100%);
        color: #1f6fa3;
        display: grid;
        place-items: center;
        font-size: 2.35rem;
        border: 4px solid rgba(255,255,255,0.95);
        box-shadow: 0 18px 30px -22px rgba(15, 23, 42, 0.22);
    }

    .patient-name {
        margin: 0;
        font-weight: 800;
        color: #0f172a;
        font-size: clamp(1.2rem, 1.95vw, 1.75rem);
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .patient-meta {
        margin: 0.25rem 0 0;
        color: #64748b;
        font-weight: 600;
        font-size: 0.94rem;
    }

    .patient-badges {
        display: flex;
        gap: 0.52rem;
        flex-wrap: wrap;
        margin-top: 0.72rem;
    }

    .patient-hero-highlights {
        display: grid;
        gap: 0.75rem;
        align-content: stretch;
    }

    .hero-highlight {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 0.8rem;
        align-items: center;
        min-height: 82px;
        padding: 0.92rem 0.95rem;
        border-radius: 18px;
        border: 1px solid rgba(214, 226, 241, 0.95);
        background: linear-gradient(180deg, rgba(255,255,255,0.94) 0%, rgba(244,249,255,0.96) 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.92);
    }

    .hero-highlight-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #c8daf2;
        color: #2563eb;
        flex-shrink: 0;
    }

    .hero-highlight-label {
        display: block;
        color: #6b7f99;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.28rem;
    }

    .hero-highlight-value {
        display: block;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .hero-highlight-note {
        display: block;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.18rem;
    }

    .meta-badge {
        border-radius: 999px;
        min-height: 34px;
        padding: 0 0.72rem;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.38rem;
        border: 1px solid transparent;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.42);
    }

    .meta-badge.blue {
        background: #dbeafe;
        color: #1e3a8a;
        border-color: #bfdbfe;
    }

    .meta-badge.green {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .meta-badge.red {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .patient-main-grid {
        margin-top: 1rem;
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
        gap: 1rem;
        align-items: start;
    }

    .info-stack {
        display: grid;
        gap: 1rem;
    }

    .panel {
        background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(250,252,255,0.95) 100%);
        border: 1px solid rgba(226, 236, 248, 0.95);
        border-radius: 20px;
        box-shadow: 0 20px 36px -32px rgba(15, 23, 42, 0.2);
        overflow: hidden;
        backdrop-filter: blur(8px);
    }

    .panel-head {
        padding: 0.95rem 1rem;
        border-bottom: 1px solid #eef3fb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.6rem;
        background: linear-gradient(180deg, #fbfdff 0%, #f4f8fd 100%);
    }

    .panel-title-wrap {
        min-width: 0;
    }

    .panel-title {
        margin: 0;
        font-size: 1.03rem;
        color: #163b67;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 0.48rem;
    }

    .panel-subtitle {
        margin: 0.28rem 0 0;
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 600;
    }

    .panel-body {
        padding: 1rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
    }

    .kv-list {
        display: grid;
        gap: 0.55rem;
    }

    .kv-row {
        display: grid;
        grid-template-columns: 136px minmax(0, 1fr);
        align-items: start;
        gap: 0.46rem;
        padding: 0.72rem 0.78rem;
        border-radius: 14px;
        border: 1px solid #ecf1f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .kv-key {
        color: #334155;
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .kv-val {
        color: #0f172a;
        font-size: 0.95rem;
        font-weight: 600;
        word-break: break-word;
    }

    .muted-empty {
        color: #94a3b8;
        font-style: italic;
    }

    .text-block {
        border: 1px solid #ecf1f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        border-radius: 16px;
        padding: 0.9rem;
        color: #334155;
        line-height: 1.65;
    }

    .text-block.empty {
        color: #94a3b8;
        font-style: italic;
    }

    .side-stack {
        display: grid;
        gap: 1rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.65rem;
    }

    .stat-item {
        text-align: center;
        border: 1px solid #e6eefc;
        border-radius: 16px;
        padding: 0.82rem 0.55rem;
        background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.92);
    }

    .stat-value {
        margin: 0;
        font-size: 1.55rem;
        font-weight: 800;
        color: #16324d;
        line-height: 1.1;
    }

    .stat-label {
        margin: 0.2rem 0 0;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .profile-meter {
        margin-top: 0.95rem;
        padding: 0.85rem;
        border-radius: 16px;
        background: linear-gradient(180deg, rgba(248,251,255,0.96) 0%, rgba(242,247,253,0.94) 100%);
        border: 1px solid #e6eef8;
    }

    .profile-meter-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.4rem;
        color: #334155;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .profile-meter-track {
        width: 100%;
        height: 10px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .profile-meter-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #2563eb, #22c55e);
    }

    .action-list {
        display: grid;
        gap: 0.62rem;
    }

    .action-btn {
        border-radius: 16px;
        padding: 0.82rem 0.85rem;
        text-decoration: none;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.6rem;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.14);
    }

    .action-btn-icon {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(255,255,255,0.16);
    }

    .action-btn:hover {
        transform: translateY(-1px);
    }

    .action-btn.green {
        background: #16a34a;
        color: #fff;
    }

    .action-btn.cyan {
        background: #0891b2;
        color: #fff;
    }

    .action-btn.amber {
        background: #f59e0b;
        color: #111827;
    }

    .action-btn.slate {
        background: #475569;
        color: #fff;
    }

    .history-list {
        display: grid;
        gap: 0.55rem;
    }

    .history-item {
        border: 1px solid #ecf1f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        border-radius: 14px;
        padding: 0.74rem 0.78rem;
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 0.65rem;
    }

    .history-label {
        color: #334155;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .history-value {
        color: #64748b;
        font-size: 0.86rem;
        font-weight: 600;
        text-align: right;
    }

    .draft-badge {
        margin-top: 0.48rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 0.32rem;
        padding: 0.27rem 0.56rem;
        font-size: 0.74rem;
        font-weight: 700;
        color: #854d0e;
        border: 1px solid #fcd34d;
        background: #fef9c3;
    }

    body.dark-mode .patient-hero,
    body.dark-mode .panel {
        background: rgba(17, 24, 39, 0.95);
        border-color: #374151;
        box-shadow: 0 10px 26px rgba(0, 0, 0, 0.35);
    }

    body.dark-mode .patient-hero-eyebrow {
        background: rgba(93, 165, 255, 0.12);
        color: #9ecbff;
    }

    body.dark-mode .patient-hero-title-icon,
    body.dark-mode .patient-identity-card,
    body.dark-mode .hero-highlight {
        border-color: #374151;
        background: linear-gradient(180deg, rgba(17, 24, 39, 0.94) 0%, rgba(20, 28, 45, 0.96) 100%);
        box-shadow: none;
    }

    body.dark-mode .panel-head {
        background: #0f172a;
        border-color: #374151;
    }

    body.dark-mode .patient-hero-title,
    body.dark-mode .panel-title,
    body.dark-mode .patient-name,
    body.dark-mode .kv-val,
    body.dark-mode .stat-value {
        color: #e5e7eb;
    }

    body.dark-mode .patient-hero-sub,
    body.dark-mode .patient-meta,
    body.dark-mode .kv-key,
    body.dark-mode .stat-label,
    body.dark-mode .history-value,
    body.dark-mode .hero-highlight-note,
    body.dark-mode .hero-highlight-label {
        color: #9ca3af;
    }

    body.dark-mode .hero-highlight-value {
        color: #e5e7eb;
    }

    body.dark-mode .hero-btn.soft {
        background: #0f172a;
        color: #cbd5e1;
        border-color: #334155;
    }

    body.dark-mode .hero-btn.soft .hero-btn-icon,
    body.dark-mode .record-chip,
    body.dark-mode .action-btn-icon,
    body.dark-mode .hero-highlight-icon {
        background: rgba(93, 165, 255, 0.14);
        color: #9ecbff;
    }

    body.dark-mode .kv-row,
    body.dark-mode .text-block,
    body.dark-mode .stat-item,
    body.dark-mode .history-item {
        background: #111827;
        border-color: #374151;
    }

    body.dark-mode .profile-meter {
        background: #111827;
        border-color: #374151;
    }

    body.dark-mode .panel-subtitle,
    body.dark-mode .record-chip {
        color: #9ca3af;
    }

    @media (max-width: 1200px) {
        .patient-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 860px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .patient-identity {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .patient-show-shell {
            padding: 0.35rem;
        }

        .patient-hero {
            border-radius: 14px;
        }

        .patient-hero-top {
            gap: 0.85rem;
        }

        .patient-hero-title {
            font-size: 1.25rem;
            line-height: 1.08;
        }

        .patient-hero-title-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
        }

        .patient-identity {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .patient-identity-card {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .patient-id-line {
            justify-content: center;
        }

        .patient-avatar,
        .patient-avatar-fallback {
            margin: 0 auto;
        }

        .patient-badges {
            justify-content: center;
        }

        .patient-hero-highlights {
            grid-template-columns: 1fr;
        }

        .hero-actions {
            width: 100%;
        }

        .hero-btn {
            flex: 1 1 100%;
        }

        .panel-body {
            padding: 0.75rem;
        }

        .panel-head {
            align-items: flex-start;
        }

        .kv-row {
            grid-template-columns: 1fr;
            gap: 0.25rem;
        }

        .history-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .history-value {
            text-align: left;
        }
    }
</style>
@endpush

@section('content')
<div class="patient-show-shell">
    <div class="patient-show-wrap">
        <section class="patient-hero">
            <div class="patient-hero-top">
                <div class="patient-hero-intro">
                    <span class="patient-hero-eyebrow"><i class="fas fa-folder-open"></i> Fiche patient premium</span>
                    <h1 class="patient-hero-title">
                        <span class="patient-hero-title-icon"><i class="fas fa-user-circle"></i></span>
                        Dossier patient
                    </h1>
                    <p class="patient-hero-sub">Vue complete du profil, des donnees medicales et des actions utiles.</p>
                </div>

                <div class="hero-actions">
                    <a href="{{ route('patients.edit', $patient) }}" class="hero-btn primary">
                        <span class="hero-btn-icon"><i class="fas fa-pen-to-square"></i></span>
                        <span>Modifier</span>
                    </a>
                    <a href="{{ route('patients.index') }}" class="hero-btn soft">
                        <span class="hero-btn-icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Retour</span>
                    </a>
                </div>
            </div>

            <div class="patient-identity">
                <div class="patient-identity-card">
                    @if($patient->photo)
                        <img src="{{ asset('storage/' . $patient->photo) }}" alt="Photo patient" class="patient-avatar">
                    @else
                        <div class="patient-avatar-fallback">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif

                    <div>
                        <p class="patient-name">{{ strtoupper($patient->prenom) }} {{ strtoupper($patient->nom) }}</p>
                        <div class="patient-id-line">
                            <span class="record-chip"><i class="fas fa-id-card"></i> Dossier {{ $patient->numero_dossier ?? 'N/A' }}</span>
                            <span class="patient-meta">{{ __('messages.patients.centralized_profile') }}</span>
                        </div>
                        <div class="patient-badges">
                            <span class="meta-badge blue"><i class="fas fa-venus-mars"></i> {{ $genreLabel }}</span>
                            @if($age)
                                <span class="meta-badge green"><i class="fas fa-cake-candles"></i> {{ $age }} ans</span>
                            @endif
                            @if($patient->groupe_sanguin)
                                <span class="meta-badge red"><i class="fas fa-droplet"></i> {{ $patient->groupe_sanguin }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="patient-hero-highlights">
                    <div class="hero-highlight">
                        <span class="hero-highlight-icon"><i class="fas fa-chart-line"></i></span>
                        <div>
                            <span class="hero-highlight-label">Completude</span>
                            <span class="hero-highlight-value">{{ $profileCompletion }}%</span>
                            <span class="hero-highlight-note">Dossier patient renseigne</span>
                        </div>
                    </div>

                    <div class="hero-highlight">
                        <span class="hero-highlight-icon"><i class="fas fa-clock-rotate-left"></i></span>
                        <div>
                            <span class="hero-highlight-label">Derniere consultation</span>
                            <span class="hero-highlight-value">
                                @if($lastConsultationAt)
                                    {{ \Carbon\Carbon::parse($lastConsultationAt)->format('d/m/Y') }}
                                @else
                                    {{ __('messages.patients.none') }}
                                @endif
                            </span>
                            <span class="hero-highlight-note">Dernier contact clinique</span>
                        </div>
                    </div>

                    <div class="hero-highlight">
                        <span class="hero-highlight-icon"><i class="fas fa-shield-heart"></i></span>
                        <div>
                            <span class="hero-highlight-label">Couverture</span>
                            <span class="hero-highlight-value">{{ $patient->assurance ?: __('messages.common.not_provided_feminine') }}</span>
                            <span class="hero-highlight-note">Organisme declare pour ce patient</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="patient-main-grid">
            <div class="info-stack">
                <article class="panel">
                    <header class="panel-head">
                        <div class="panel-title-wrap">
                            <h2 class="panel-title"><i class="fas fa-circle-info"></i> Informations principales</h2>
                            <p class="panel-subtitle">Identite, contact et donnees administratives de reference.</p>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="info-grid">
                            <div class="kv-list">
                                <div class="kv-row">
                                    <span class="kv-key">CIN</span>
                                    <span class="kv-val">{{ $patient->cin ?: __('messages.common.not_provided') }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="kv-key">Date naissance</span>
                                    <span class="kv-val">
                                        @if($birthDate)
                                            {{ $birthDate->format('d/m/Y') }} ({{ $age }} ans)
                                        @else
                                            <span class="muted-empty">{{ __('messages.common.not_provided_feminine') }}</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="kv-row">
                                    <span class="kv-key">Genre</span>
                                    <span class="kv-val">{{ $genreLabel }}</span>
                                </div>
                            </div>

                            <div class="kv-list">
                                <div class="kv-row">
                                    <span class="kv-key">Telephone</span>
                                    <span class="kv-val">{{ $patient->telephone ?: __('messages.common.not_provided') }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="kv-key">Email</span>
                                    <span class="kv-val">{{ $patient->email ?: __('messages.common.not_provided') }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="kv-key">Ville</span>
                                    <span class="kv-val">{{ $patient->ville ?: __('messages.common.not_provided_feminine') }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="kv-key">Code postal</span>
                                    <span class="kv-val">{{ $patient->code_postal ?: __('messages.common.not_provided') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="panel">
                    <header class="panel-head">
                        <div class="panel-title-wrap">
                            <h2 class="panel-title"><i class="fas fa-notes-medical"></i> {{ __('messages.patients.medical_information') }}</h2>
                            <p class="panel-subtitle">Vue rapide sur les informations cliniques et logistiques du patient.</p>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="info-grid">
                            <div class="kv-list">
                                <div class="kv-row">
                                    <span class="kv-key">Groupe sanguin</span>
                                    <span class="kv-val">{{ $patient->groupe_sanguin ?: __('messages.common.not_provided') }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="kv-key">Assurance</span>
                                    <span class="kv-val">{{ $patient->assurance ?: __('messages.common.not_provided_feminine') }}</span>
                                </div>
                            </div>

                            <div class="kv-list">
                                <div class="kv-row">
                                    <span class="kv-key">Adresse</span>
                                    <span class="kv-val">{{ $patient->adresse ?: __('messages.common.not_provided_feminine') }}</span>
                                </div>
                                <div class="kv-row">
                                    <span class="kv-key">Contact urgence</span>
                                    <span class="kv-val">{{ $patient->contact_urgence ?: __('messages.common.not_provided') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="panel">
                    <header class="panel-head">
                        <div class="panel-title-wrap">
                            <h2 class="panel-title"><i class="fas fa-file-medical"></i> {{ __('messages.patients.medical_history') }}</h2>
                            <p class="panel-subtitle">Historique medical pertinent documente dans le dossier.</p>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="text-block {{ $patient->antecedents ? '' : 'empty' }}">
                            {!! $patient->antecedents ? nl2br(e($patient->antecedents)) : __('messages.patients.no_history') !!}
                        </div>
                    </div>
                </article>

                <article class="panel">
                    <header class="panel-head">
                        <div class="panel-title-wrap">
                            <h2 class="panel-title"><i class="fas fa-note-sticky"></i> {{ __('messages.patients.internal_notes') }}</h2>
                            <p class="panel-subtitle">Observations libres utiles a l equipe clinique et administrative.</p>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="text-block {{ $patient->notes ? '' : 'empty' }}">
                            {!! $patient->notes ? nl2br(e($patient->notes)) : __('messages.patients.no_note') !!}
                        </div>
                    </div>
                </article>
            </div>

            <aside class="side-stack">
                <article class="panel">
                    <header class="panel-head">
                        <div class="panel-title-wrap">
                            <h2 class="panel-title"><i class="fas fa-chart-column"></i> Statistiques</h2>
                            <p class="panel-subtitle">Indicateurs essentiels du suivi patient et de l activite recente.</p>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <p class="stat-value">{{ $consultationsCount }}</p>
                                <p class="stat-label">Consultations</p>
                            </div>
                            <div class="stat-item">
                                <p class="stat-value">{{ $upcomingRendezVousCount }}</p>
                                <p class="stat-label">RDV a venir</p>
                            </div>
                            <div class="stat-item">
                                <p class="stat-value" data-prescriptions-count aria-live="polite">{{ $prescriptionsCount }}</p>
                                <p class="stat-label">Ordonnances</p>
                            </div>
                        </div>

                        <div class="profile-meter">
                            <div class="profile-meter-head">
                                <span>Completude du dossier</span>
                                <strong>{{ $profileCompletion }}%</strong>
                            </div>
                            <div class="profile-meter-track">
                                <div class="profile-meter-fill" style="width: {{ $profileCompletion }}%;"></div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="panel">
                    <header class="panel-head">
                        <div class="panel-title-wrap">
                            <h2 class="panel-title"><i class="fas fa-bolt"></i> Actions rapides</h2>
                            <p class="panel-subtitle">Raccourcis metier pour agir rapidement depuis la fiche patient.</p>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="action-list">
                            <a href="{{ route('consultations.create', ['patient_id' => $patient->id]) }}" class="action-btn green">
                                <span class="action-btn-icon"><i class="fas fa-calendar-plus"></i></span>
                                <span>Nouvelle consultation</span>
                            </a>
                            <a
                                href="{{ route('ordonnances.create', ['patient_id' => $patient->id]) }}"
                                class="action-btn cyan"
                                x-data
                                @click.prevent="$dispatch('open-modal', 'modal-ordonnance')"
                            >
                                <span class="action-btn-icon"><i class="fas fa-prescription"></i></span>
                                <span>Nouvelle ordonnance</span>
                            </a>
                            <a href="{{ route('examens.create', ['patient_id' => $patient->id]) }}" class="action-btn amber">
                                <span class="action-btn-icon"><i class="fas fa-vial-circle-check"></i></span>
                                <span>Ajouter un examen</span>
                            </a>
                            <a href="#" onclick="window.print(); return false;" class="action-btn slate">
                                <span class="action-btn-icon"><i class="fas fa-print"></i></span>
                                <span>Imprimer le dossier</span>
                            </a>
                        </div>
                    </div>
                </article>

                <article class="panel">
                    <header class="panel-head">
                        <div class="panel-title-wrap">
                            <h2 class="panel-title"><i class="fas fa-clock-rotate-left"></i> Historique</h2>
                            <p class="panel-subtitle">Repere chronologique des principaux jalons du dossier.</p>
                        </div>
                    </header>
                    <div class="panel-body">
                        <div class="history-list">
                            <div class="history-item">
                                <span class="history-label">Cree le</span>
                                <span class="history-value">{{ optional($patient->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="history-item">
                                <span class="history-label">Modifie le</span>
                                <span class="history-value">{{ optional($patient->updated_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="history-item">
                                <span class="history-label">Derniere consultation</span>
                                <span class="history-value">
                                    @if($lastConsultationAt)
                                        {{ \Carbon\Carbon::parse($lastConsultationAt)->format('d/m/Y') }}
                                    @else
                                        {{ __('messages.patients.none') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        @if($patient->is_draft)
                            <span class="draft-badge"><i class="fas fa-file-lines"></i> Brouillon</span>
                        @endif
                    </div>
                </article>
            </aside>
        </section>
    </div>
</div>

<x-modal-ordonnance
    :patient="$patient"
    :medecins="$medecins"
    :current-medecin="$currentMedecin"
    :medicament-catalog-data="$medicamentCatalogData"
/>
@endsection
