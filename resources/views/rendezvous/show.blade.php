@extends('layouts.app')

@section('title', 'Details du rendez-vous')

@push('styles')
<style>
    :root {
        --rdv-primary: #2563eb;
        --rdv-primary-deep: #153b84;
        --rdv-primary-soft: #e9f2ff;
        --rdv-surface: #ffffff;
        --rdv-surface-soft: #f8fbff;
        --rdv-surface-accent: #f3f7ff;
        --rdv-border: #dbe7f6;
        --rdv-border-strong: #c8d9f0;
        --rdv-text: #0f172a;
        --rdv-text-soft: #5f6f86;
        --rdv-text-muted: #7b8aa2;
        --rdv-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        --rdv-shadow-soft: 0 10px 28px rgba(15, 23, 42, 0.06);
    }

    .rdv-show-page {
        padding: clamp(0.55rem, 1.4vw, 1rem);
        width: 100%;
        min-height: calc(100vh - 120px);
        background:
            radial-gradient(circle at top right, rgba(37, 99, 235, 0.09), transparent 26%),
            linear-gradient(180deg, #f6faff 0%, #f3f7fc 100%);
    }

    .rdv-show-wrap {
        width: 100%;
        max-width: none;
        margin: 0;
    }

    .rdv-show-head {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f8fbff 0%, #edf4ff 52%, #f4f8ff 100%);
        border: 1px solid var(--rdv-border);
        border-radius: 28px;
        padding: clamp(1.2rem, 2.8vw, 2rem);
        box-shadow: var(--rdv-shadow);
        display: grid;
        gap: 1.35rem;
    }

    .rdv-show-head::before,
    .rdv-show-head::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
        opacity: 0.9;
    }

    .rdv-show-head::before {
        width: 220px;
        height: 220px;
        right: -72px;
        top: -96px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.18) 0%, rgba(59, 130, 246, 0) 72%);
    }

    .rdv-show-head::after {
        width: 180px;
        height: 180px;
        left: -54px;
        bottom: -96px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 74%);
    }

    .rdv-head-top {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .rdv-head-intro {
        display: grid;
        gap: 0.8rem;
        max-width: 880px;
    }

    .rdv-head-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        width: fit-content;
        border-radius: 999px;
        padding: 0.42rem 0.78rem;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(201, 217, 241, 0.95);
        color: var(--rdv-primary-deep);
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .rdv-show-head-title {
        margin: 0;
        color: var(--rdv-primary-deep);
        font-size: clamp(1.65rem, 3vw, 2.45rem);
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.7rem;
        line-height: 1.1;
    }

    .rdv-show-head-sub {
        margin: 0;
        color: var(--rdv-text-soft);
        font-size: 1rem;
        line-height: 1.7;
        max-width: 760px;
    }

    .rdv-head-meta {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.85rem;
    }

    .rdv-meta-card {
        border-radius: 18px;
        border: 1px solid rgba(207, 223, 245, 0.95);
        background: rgba(255, 255, 255, 0.88);
        padding: 0.95rem 1rem;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.04);
    }

    .rdv-meta-label {
        margin: 0;
        color: var(--rdv-text-muted);
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .rdv-meta-value {
        margin: 0.4rem 0 0;
        color: var(--rdv-text);
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .rdv-head-actions {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        flex-wrap: wrap;
    }

    .rdv-btn {
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid transparent;
        padding: 0.72rem 1.08rem;
        font-size: 0.94rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }

    .rdv-btn-icon {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        display: inline-grid;
        place-items: center;
        background: rgba(255, 255, 255, 0.18);
    }

    .rdv-btn.primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
        border-color: #2563eb;
        box-shadow: 0 14px 28px rgba(37, 99, 235, 0.24);
    }

    .rdv-btn.primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        border-color: #1d4ed8;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 16px 34px rgba(37, 99, 235, 0.28);
    }

    .rdv-btn.ghost {
        background: rgba(255, 255, 255, 0.84);
        color: var(--rdv-primary-deep);
        border-color: #cadbf0;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55);
    }

    .rdv-btn.ghost .rdv-btn-icon {
        background: rgba(37, 99, 235, 0.08);
        color: var(--rdv-primary);
    }

    .rdv-btn.ghost:hover {
        background: #f8fbff;
        border-color: var(--rdv-border-strong);
        color: var(--rdv-primary-deep);
        transform: translateY(-1px);
    }

    .rdv-show-grid {
        margin-top: 1.15rem;
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
        gap: 1rem;
        align-items: start;
    }

    .rdv-stack {
        display: grid;
        gap: 1rem;
    }

    .rdv-panel {
        background: var(--rdv-surface);
        border: 1px solid #e4eef9;
        border-radius: 24px;
        box-shadow: var(--rdv-shadow-soft);
        overflow: hidden;
    }

    .rdv-panel-head {
        background: linear-gradient(180deg, #fbfdff 0%, #f4f8fd 100%);
        border-bottom: 1px solid #ebf2fb;
        padding: 1rem 1.15rem 0.95rem;
    }

    .rdv-panel-head-wrap {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.9rem;
    }

    .rdv-panel-title {
        margin: 0;
        font-size: 1.08rem;
        color: var(--rdv-primary-deep);
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .rdv-panel-subtitle {
        margin: 0.35rem 0 0;
        color: var(--rdv-text-muted);
        font-size: 0.9rem;
        line-height: 1.55;
    }

    .rdv-panel-body {
        padding: 1.15rem;
    }

    .rdv-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.95rem;
    }

    .rdv-info-card {
        position: relative;
        overflow: hidden;
        border: 1px solid #e7eff8;
        background: linear-gradient(180deg, var(--rdv-surface-soft) 0%, #ffffff 100%);
        border-radius: 20px;
        padding: 1rem;
        display: grid;
        gap: 0.65rem;
        min-height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .rdv-info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        border-color: var(--rdv-border-strong);
    }

    .rdv-info-card.full {
        grid-column: 1 / -1;
    }

    .rdv-info-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .rdv-info-card-copy {
        display: grid;
        gap: 0.35rem;
    }

    .rdv-kv-label {
        color: var(--rdv-text-muted);
        font-size: 0.73rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .rdv-kv-value {
        color: var(--rdv-text);
        font-size: 1.08rem;
        font-weight: 800;
        line-height: 1.4;
        word-break: break-word;
    }

    .rdv-kv-help {
        color: var(--rdv-text-soft);
        font-size: 0.89rem;
        line-height: 1.55;
    }

    .rdv-info-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-grid;
        place-items: center;
        background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
        color: var(--rdv-primary);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        flex-shrink: 0;
    }

    .rdv-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        padding: 0.42rem 0.8rem;
        font-size: 0.82rem;
        font-weight: 800;
        border: 1px solid transparent;
        width: fit-content;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    .rdv-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.34);
    }

    .rdv-status-chip.primary {
        background: #e8f0ff;
        border-color: #cadeff;
        color: #1e40af;
    }

    .rdv-status-chip.warning {
        background: #fff4d8;
        border-color: #ffe4a6;
        color: #92400e;
    }

    .rdv-status-chip.info {
        background: #dcf7ff;
        border-color: #b2edfb;
        color: #0e7490;
    }

    .rdv-status-chip.secondary {
        background: #eef2f8;
        border-color: #d7e0eb;
        color: #334155;
    }

    .rdv-status-chip.dark {
        background: #e8edf5;
        border-color: #d3dbe6;
        color: #1f2937;
    }

    .rdv-status-chip.danger {
        background: #fee8e8;
        border-color: #ffd1d1;
        color: #991b1b;
    }

    .rdv-note-card {
        border-radius: 22px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #e6eef8;
        padding: 1rem;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.75);
    }

    .rdv-note {
        border: 1px solid #e8eef8;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 16px;
        padding: 1rem 1.05rem;
        color: #314155;
        line-height: 1.8;
        white-space: pre-wrap;
        word-break: break-word;
        font-size: 0.95rem;
    }

    .rdv-note.empty {
        color: #8ea0b8;
        font-style: italic;
    }

    .rdv-person-card {
        display: grid;
        gap: 1rem;
    }

    .rdv-person {
        display: grid;
        grid-template-columns: 62px minmax(0, 1fr);
        gap: 0.9rem;
        align-items: start;
    }

    .rdv-person-avatar {
        width: 62px;
        height: 62px;
        border-radius: 999px;
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 55%, #60a5fa 100%);
        color: #fff;
        font-weight: 900;
        font-size: 1.05rem;
        display: grid;
        place-items: center;
        letter-spacing: 0.04em;
        box-shadow: 0 14px 24px rgba(37, 99, 235, 0.22);
        border: 4px solid rgba(255, 255, 255, 0.9);
    }

    .rdv-person-name {
        margin: 0;
        color: var(--rdv-text);
        font-size: 1.08rem;
        font-weight: 900;
        line-height: 1.3;
    }

    .rdv-person-sub {
        margin: 0.22rem 0 0;
        color: var(--rdv-text-soft);
        font-size: 0.9rem;
        word-break: break-word;
    }

    .rdv-person-detail-list {
        display: grid;
        gap: 0.7rem;
    }

    .rdv-person-detail {
        border: 1px solid #e7eef8;
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9ff 100%);
        border-radius: 16px;
        padding: 0.8rem 0.9rem;
        display: grid;
        gap: 0.24rem;
    }

    .rdv-person-detail-label {
        color: var(--rdv-text-muted);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .rdv-person-detail-value {
        color: var(--rdv-text);
        font-size: 0.95rem;
        font-weight: 800;
        line-height: 1.45;
        word-break: break-word;
    }

    .rdv-side-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        min-height: 44px;
        border-radius: 14px;
        border: 1px solid #cfe0f7;
        background: linear-gradient(180deg, #f8fbff 0%, #edf5ff 100%);
        color: #1d4ed8;
        font-size: 0.9rem;
        font-weight: 800;
        text-decoration: none;
        padding: 0.68rem 0.92rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .rdv-side-link:hover {
        background: #dfeeff;
        color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.12);
    }

    .rdv-side-link.disabled {
        pointer-events: none;
        opacity: 0.6;
    }

    .rdv-timeline {
        position: relative;
        display: grid;
        gap: 0.95rem;
    }

    .rdv-timeline::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 0.4rem;
        bottom: 0.4rem;
        width: 2px;
        background: linear-gradient(180deg, #bfdbfe 0%, #dbeafe 100%);
    }

    .rdv-timeline-item {
        position: relative;
        display: grid;
        grid-template-columns: 24px minmax(0, 1fr);
        gap: 0.95rem;
        align-items: start;
    }

    .rdv-timeline-dot {
        position: relative;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: #ffffff;
        border: 2px solid #93c5fd;
        box-shadow: 0 0 0 5px #eff6ff;
        margin-top: 0.2rem;
    }

    .rdv-timeline-card {
        border: 1px solid #e6eef9;
        background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
        border-radius: 18px;
        padding: 0.9rem 0.95rem;
        display: grid;
        gap: 0.28rem;
    }

    .rdv-meta-key {
        color: var(--rdv-text-muted);
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .rdv-meta-val {
        color: var(--rdv-text);
        font-size: 0.98rem;
        font-weight: 800;
        line-height: 1.45;
    }

    .rdv-meta-caption {
        color: var(--rdv-text-soft);
        font-size: 0.88rem;
        line-height: 1.5;
    }

    body.dark-mode .rdv-show-head,
    body.dark-mode .rdv-panel {
        background: rgba(17, 24, 39, 0.95);
        border-color: #374151;
        box-shadow: 0 10px 26px rgba(0, 0, 0, 0.35);
    }

    body.dark-mode .rdv-show-page {
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, 0.12), transparent 22%),
            linear-gradient(180deg, #0b1120 0%, #111827 100%);
    }

    body.dark-mode .rdv-head-eyebrow,
    body.dark-mode .rdv-meta-card,
    body.dark-mode .rdv-info-card,
    body.dark-mode .rdv-note-card,
    body.dark-mode .rdv-note,
    body.dark-mode .rdv-person-detail,
    body.dark-mode .rdv-timeline-card {
        background: #111827;
        border-color: #334155;
    }

    body.dark-mode .rdv-panel-head {
        background: #0f172a;
        border-color: #374151;
    }

    body.dark-mode .rdv-show-head-title,
    body.dark-mode .rdv-panel-title,
    body.dark-mode .rdv-person-name,
    body.dark-mode .rdv-kv-value,
    body.dark-mode .rdv-meta-value,
    body.dark-mode .rdv-meta-val,
    body.dark-mode .rdv-person-detail-value {
        color: #e5e7eb;
    }

    body.dark-mode .rdv-show-head-sub,
    body.dark-mode .rdv-kv-label,
    body.dark-mode .rdv-kv-help,
    body.dark-mode .rdv-person-sub,
    body.dark-mode .rdv-person-detail-label,
    body.dark-mode .rdv-meta-label,
    body.dark-mode .rdv-meta-key,
    body.dark-mode .rdv-meta-caption,
    body.dark-mode .rdv-note,
    body.dark-mode .rdv-panel-subtitle {
        color: #9ca3af;
    }

    body.dark-mode .rdv-btn.ghost {
        background: #0f172a;
        color: #cbd5e1;
        border-color: #334155;
    }

    body.dark-mode .rdv-btn.ghost .rdv-btn-icon {
        background: rgba(59, 130, 246, 0.16);
        color: #93c5fd;
    }

    body.dark-mode .rdv-info-icon {
        background: rgba(37, 99, 235, 0.18);
        color: #93c5fd;
    }

    body.dark-mode .rdv-side-link {
        background: #172554;
        border-color: #3b82f6;
        color: #dbeafe;
    }

    body.dark-mode .rdv-side-link:hover {
        background: #1d4ed8;
    }

    body.dark-mode .rdv-timeline::before {
        background: linear-gradient(180deg, rgba(96, 165, 250, 0.75) 0%, rgba(59, 130, 246, 0.22) 100%);
    }

    body.dark-mode .rdv-timeline-dot {
        background: #0f172a;
        border-color: #60a5fa;
        box-shadow: 0 0 0 5px rgba(30, 64, 175, 0.18);
    }

    @media (max-width: 1200px) {
        .rdv-show-grid {
            grid-template-columns: 1fr;
        }

        .rdv-head-meta {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .rdv-head-top {
            flex-direction: column;
        }

        .rdv-head-actions {
            width: 100%;
        }
    }

    @media (max-width: 760px) {
        .rdv-show-page {
            padding: 0.45rem;
            min-height: auto;
        }

        .rdv-show-head {
            border-radius: 22px;
            padding: 1rem;
        }

        .rdv-head-meta,
        .rdv-info-grid {
            grid-template-columns: 1fr;
        }

        .rdv-btn {
            flex: 1 1 100%;
        }

        .rdv-panel-body {
            padding: 0.95rem;
        }

        .rdv-panel-head {
            padding: 0.95rem 1rem 0.9rem;
        }

        .rdv-person {
            grid-template-columns: 1fr;
        }

        .rdv-person-avatar {
            width: 58px;
            height: 58px;
        }

        .rdv-timeline-item {
            gap: 0.8rem;
        }
    }
</style>
@endpush

@section('content')
<div class="rdv-show-page">
    <div class="rdv-show-wrap">
        <section class="rdv-show-head">
            <div class="rdv-head-top">
                <div class="rdv-head-intro">
                    <span class="rdv-head-eyebrow"><i class="fas fa-calendar-check"></i> Detail rendez-vous premium</span>
                    <div>
                        <h1 class="rdv-show-head-title">
                            <i class="fas fa-calendar-day"></i>
                            Rendez-vous #{{ $rendezvous->id }}
                            <span class="rdv-status-chip {{ $badgeClass }}">
                                <span class="rdv-status-dot"></span>
                                <i class="fas {{ $statusIcon }}"></i>
                                {{ $statusLabel }}
                            </span>
                        </h1>
                        <p class="rdv-show-head-sub">Lecture plus claire du planning, du contexte clinique et des interlocuteurs lies au rendez-vous, dans le meme langage premium que les autres modules.</p>
                    </div>
                </div>

                <div class="rdv-head-actions">
                    <a href="{{ route('rendezvous.index') }}" class="rdv-btn ghost">
                        <span class="rdv-btn-icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Retour a la liste</span>
                    </a>
                    <a href="{{ route('rendezvous.edit', $rendezvous->id) }}" class="rdv-btn primary">
                        <span class="rdv-btn-icon"><i class="fas fa-pen-to-square"></i></span>
                        <span>Modifier</span>
                    </a>
                </div>
            </div>

            <div class="rdv-head-meta">
                <div class="rdv-meta-card">
                    <p class="rdv-meta-label">Patient</p>
                    <p class="rdv-meta-value">{{ $patientName }}</p>
                </div>
                <div class="rdv-meta-card">
                    <p class="rdv-meta-label">Medecin</p>
                    <p class="rdv-meta-value">Dr. {{ $medecinName }}</p>
                </div>
                <div class="rdv-meta-card">
                    <p class="rdv-meta-label">Date planifiee</p>
                    <p class="rdv-meta-value">{{ $appointmentDate->format('d/m/Y H:i') }}</p>
                </div>
                <div class="rdv-meta-card">
                    <p class="rdv-meta-label">Format</p>
                    <p class="rdv-meta-value">{{ $rendezvous->type ?? 'Non precise' }}</p>
                </div>
            </div>
        </section>

        <section class="rdv-show-grid">
            <div class="rdv-stack">
                <article class="rdv-panel">
                    <header class="rdv-panel-head">
                        <div class="rdv-panel-head-wrap">
                            <div>
                                <h2 class="rdv-panel-title"><i class="fas fa-circle-info"></i> Informations du rendez-vous</h2>
                                <p class="rdv-panel-subtitle">Donnees essentielles organisees en blocs visuels pour une lecture immediate.</p>
                            </div>
                        </div>
                    </header>
                    <div class="rdv-panel-body">
                        <div class="rdv-info-grid">
                            <article class="rdv-info-card">
                                <div class="rdv-info-card-head">
                                    <div class="rdv-info-card-copy">
                                        <span class="rdv-kv-label">Date et heure</span>
                                        <span class="rdv-kv-value">{{ $appointmentDate->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <span class="rdv-info-icon"><i class="fas fa-clock"></i></span>
                                </div>
                                <span class="rdv-kv-help">Creneau confirme dans l agenda du cabinet.</span>
                            </article>

                            <article class="rdv-info-card">
                                <div class="rdv-info-card-head">
                                    <div class="rdv-info-card-copy">
                                        <span class="rdv-kv-label">Statut</span>
                                        <span class="rdv-kv-value">
                                            <span class="rdv-status-chip {{ $badgeClass }}">
                                                <span class="rdv-status-dot"></span>
                                                <i class="fas {{ $statusIcon }}"></i>
                                                {{ $statusLabel }}
                                            </span>
                                        </span>
                                    </div>
                                    <span class="rdv-info-icon"><i class="fas fa-signal"></i></span>
                                </div>
                                <span class="rdv-kv-help">Etat actuel du rendez-vous dans le parcours patient.</span>
                            </article>

                            <article class="rdv-info-card">
                                <div class="rdv-info-card-head">
                                    <div class="rdv-info-card-copy">
                                        <span class="rdv-kv-label">Duree</span>
                                        <span class="rdv-kv-value">{{ (int) ($rendezvous->duree ?? 0) }} minutes</span>
                                    </div>
                                    <span class="rdv-info-icon"><i class="fas fa-stopwatch"></i></span>
                                </div>
                                <span class="rdv-kv-help">Temps reserve pour la prise en charge.</span>
                            </article>

                            <article class="rdv-info-card">
                                <div class="rdv-info-card-head">
                                    <div class="rdv-info-card-copy">
                                        <span class="rdv-kv-label">Type</span>
                                        <span class="rdv-kv-value">{{ $rendezvous->type ?? 'Non precise' }}</span>
                                    </div>
                                    <span class="rdv-info-icon"><i class="fas fa-layer-group"></i></span>
                                </div>
                                <span class="rdv-kv-help">Categorie de rendez-vous pour le suivi administratif.</span>
                            </article>

                            <article class="rdv-info-card full">
                                <div class="rdv-info-card-head">
                                    <div class="rdv-info-card-copy">
                                        <span class="rdv-kv-label">Motif</span>
                                        <span class="rdv-kv-value">{{ $rendezvous->motif ?: 'Aucun motif enregistre' }}</span>
                                    </div>
                                    <span class="rdv-info-icon"><i class="fas fa-comment-medical"></i></span>
                                </div>
                                <span class="rdv-kv-help">Contexte ou demande exprimee au moment de la planification.</span>
                            </article>
                        </div>
                    </div>
                </article>

                <article class="rdv-panel">
                    <header class="rdv-panel-head">
                        <div class="rdv-panel-head-wrap">
                            <div>
                                <h2 class="rdv-panel-title"><i class="fas fa-note-sticky"></i> Notes</h2>
                                <p class="rdv-panel-subtitle">Espace de contexte libre pour les remarques utiles avant ou apres la consultation.</p>
                            </div>
                        </div>
                    </header>
                    <div class="rdv-panel-body">
                        <div class="rdv-note-card">
                            <div class="rdv-note {{ $rendezvous->notes ? '' : 'empty' }}">
                                {!! $rendezvous->notes ? nl2br(e($rendezvous->notes)) : 'Aucune note enregistree pour ce rendez-vous.' !!}
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="rdv-stack">
                <article class="rdv-panel">
                    <header class="rdv-panel-head">
                        <div class="rdv-panel-head-wrap">
                            <div>
                                <h2 class="rdv-panel-title"><i class="fas fa-user"></i> Patient</h2>
                                <p class="rdv-panel-subtitle">Coordonnees et acces rapide au dossier pour garder le contexte clinique a portee.</p>
                            </div>
                        </div>
                    </header>
                    <div class="rdv-panel-body">
                        <div class="rdv-person-card">
                            <div class="rdv-person">
                                <div class="rdv-person-avatar">{{ $patientInitials !== '' ? $patientInitials : 'PA' }}</div>
                                <div>
                                    <p class="rdv-person-name">{{ $patientName }}</p>
                                    <p class="rdv-person-sub">Dossier patient rattache au rendez-vous #{{ $rendezvous->id }}</p>
                                    <p class="rdv-person-sub">Suivi administratif et clinique centralise.</p>
                                </div>
                            </div>

                            <div class="rdv-person-detail-list">
                                <div class="rdv-person-detail">
                                    <span class="rdv-person-detail-label">Email</span>
                                    <span class="rdv-person-detail-value">{{ $patientEmail }}</span>
                                </div>
                                <div class="rdv-person-detail">
                                    <span class="rdv-person-detail-label">Telephone</span>
                                    <span class="rdv-person-detail-value">{{ $patientTelephone }}</span>
                                </div>
                            </div>

                            @if($rendezvous->patient)
                                <a href="{{ route('patients.show', $rendezvous->patient->id) }}" class="rdv-side-link">
                                    <i class="fas fa-folder-open"></i> Voir le dossier
                                </a>
                            @else
                                <span class="rdv-side-link disabled">
                                    <i class="fas fa-folder-open"></i> Dossier indisponible
                                </span>
                            @endif
                        </div>
                    </div>
                </article>

                <article class="rdv-panel">
                    <header class="rdv-panel-head">
                        <div class="rdv-panel-head-wrap">
                            <div>
                                <h2 class="rdv-panel-title"><i class="fas fa-user-doctor"></i> Medecin</h2>
                                <p class="rdv-panel-subtitle">Meme logique de lecture que la fiche patient pour une colonne droite plus coherente.</p>
                            </div>
                        </div>
                    </header>
                    <div class="rdv-panel-body">
                        <div class="rdv-person-card">
                            <div class="rdv-person">
                                <div class="rdv-person-avatar">{{ $medecinInitials !== '' ? $medecinInitials : 'DR' }}</div>
                                <div>
                                    <p class="rdv-person-name">Dr. {{ $medecinName }}</p>
                                    <p class="rdv-person-sub">{{ $medecinSpecialite }}</p>
                                    <p class="rdv-person-sub">Professionnel reference pour ce creneau.</p>
                                </div>
                            </div>

                            <div class="rdv-person-detail-list">
                                <div class="rdv-person-detail">
                                    <span class="rdv-person-detail-label">Email</span>
                                    <span class="rdv-person-detail-value">{{ $medecinEmail }}</span>
                                </div>
                                <div class="rdv-person-detail">
                                    <span class="rdv-person-detail-label">Telephone</span>
                                    <span class="rdv-person-detail-value">{{ $medecinTelephone }}</span>
                                </div>
                            </div>

                            @if($rendezvous->medecin)
                                <a href="{{ route('medecins.show', $rendezvous->medecin->id) }}" class="rdv-side-link">
                                    <i class="fas fa-user-md"></i> Voir le profil
                                </a>
                            @else
                                <span class="rdv-side-link disabled">
                                    <i class="fas fa-user-md"></i> Profil indisponible
                                </span>
                            @endif
                        </div>
                    </div>
                </article>

                <article class="rdv-panel">
                    <header class="rdv-panel-head">
                        <div class="rdv-panel-head-wrap">
                            <div>
                                <h2 class="rdv-panel-title"><i class="fas fa-clock-rotate-left"></i> Historique</h2>
                                <p class="rdv-panel-subtitle">Lecture chronologique legere pour distinguer creation et mise a jour au premier regard.</p>
                            </div>
                        </div>
                    </header>
                    <div class="rdv-panel-body">
                        <div class="rdv-timeline">
                            <div class="rdv-timeline-item">
                                <span class="rdv-timeline-dot"></span>
                                <div class="rdv-timeline-card">
                                    <span class="rdv-meta-key">Creation</span>
                                    <span class="rdv-meta-val">{{ $createdAt->format('d/m/Y H:i') }}</span>
                                    <span class="rdv-meta-caption">Le rendez-vous a ete enregistre dans le planning a cette date.</span>
                                </div>
                            </div>

                            <div class="rdv-timeline-item">
                                <span class="rdv-timeline-dot"></span>
                                <div class="rdv-timeline-card">
                                    <span class="rdv-meta-key">Mise a jour</span>
                                    <span class="rdv-meta-val">{{ $updatedAt->format('d/m/Y H:i') }}</span>
                                    <span class="rdv-meta-caption">Derniere modification visible sur cette fiche et son etat associe.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </aside>
        </section>
    </div>
</div>
@endsection
