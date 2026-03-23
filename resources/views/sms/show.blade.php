@extends('layouts.app')

@section('title', 'Detail rappel SMS')
@section('topbar_subtitle', 'Consultez les informations detaillees du rappel SMS.')

@section('content')
<style>
    :root {
        --sms-primary: #1274d8;
        --sms-primary-dark: #0f5cad;
        --sms-success: #1c9b74;
        --sms-danger: #cf4d5d;
        --sms-warning: #c88414;
        --sms-ink: #17324d;
        --sms-muted: #637b94;
        --sms-line: #d9e6f2;
        --sms-soft: #eff6fc;
        --sms-surface: rgba(255, 255, 255, 0.92);
        --sms-shadow: 0 24px 42px -34px rgba(15, 36, 64, 0.42);
    }

    .sms-show-page {
        min-height: 100%;
        padding: 18px clamp(12px, 1.8vw, 26px) 30px;
        border-radius: 22px;
        border: 1px solid #e0ebf5;
        background:
            radial-gradient(circle at top right, rgba(18, 116, 216, 0.14) 0%, transparent 32%),
            radial-gradient(circle at bottom left, rgba(28, 155, 116, 0.08) 0%, transparent 28%),
            linear-gradient(140deg, #f5f9fd 0%, #fbfdff 100%);
        box-shadow: 0 24px 42px -36px rgba(15, 36, 64, 0.72);
    }

    .sms-alert-stack {
        display: grid;
        gap: 10px;
        margin-bottom: 16px;
    }

    .sms-show-hero,
    .sms-show-side,
    .sms-show-card,
    .sms-message-card,
    .sms-error-card,
    .sms-summary-card {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        border: 1px solid var(--sms-line);
        background: var(--sms-surface);
        box-shadow: var(--sms-shadow);
        backdrop-filter: blur(10px);
    }

    .sms-show-hero,
    .sms-show-side,
    .sms-show-card,
    .sms-message-card,
    .sms-error-card,
    .sms-summary-card {
        padding: clamp(20px, 2.3vw, 30px);
    }

    .sms-show-hero::before,
    .sms-show-side::before,
    .sms-show-card::before,
    .sms-message-card::before,
    .sms-summary-card::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), transparent 55%);
    }

    .sms-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .sms-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 12px;
        border: 1px solid #cfdeee;
        background: rgba(255, 255, 255, 0.72);
        color: #4a6682;
        font-size: 0.9rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .sms-back-btn:hover {
        color: #1f4d7a;
        border-color: #bdd2e7;
        background: #f4f9fe;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-back-icon {
        width: 24px;
        height: 24px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(18, 116, 216, 0.10);
        color: var(--sms-primary);
    }

    .sms-badge-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sms-badge,
    .sms-chip,
    .sms-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        font-weight: 700;
        white-space: nowrap;
    }

    .sms-badge {
        background: linear-gradient(135deg, var(--sms-primary) 0%, #0f4f93 100%);
        color: #fff;
        box-shadow: 0 14px 24px -22px rgba(18, 116, 216, 0.9);
    }

    .sms-chip {
        border: 1px solid #d5e5f5;
        background: #f5f9fe;
        color: #53718d;
        font-size: 0.85rem;
    }

    .sms-chip i {
        color: var(--sms-primary);
    }

    .sms-status-pill.is-planifie {
        background: rgba(18, 116, 216, 0.10);
        color: var(--sms-primary-dark);
    }

    .sms-status-pill.is-envoye {
        background: rgba(28, 155, 116, 0.12);
        color: #167657;
    }

    .sms-status-pill.is-echec {
        background: rgba(207, 77, 93, 0.12);
        color: #a73d4a;
    }

    .sms-status-pill.is-desactive {
        background: rgba(200, 132, 20, 0.12);
        color: #9b680f;
    }

    .sms-title-row {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .sms-title-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(18, 116, 216, 0.14), rgba(18, 116, 216, 0.04));
        color: var(--sms-primary);
        font-size: 1.35rem;
        box-shadow: inset 0 0 0 1px rgba(18, 116, 216, 0.08);
    }

    .sms-title-block h1 {
        margin: 0;
        color: #123355;
        font-size: clamp(1.7rem, 2.5vw, 2.2rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .sms-title-block p {
        margin: 8px 0 0;
        max-width: 760px;
        color: var(--sms-muted);
        font-size: 0.98rem;
        line-height: 1.6;
        font-weight: 500;
    }

    .sms-head-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-end;
        margin-top: 22px;
    }

    .sms-head-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 13px;
        font-size: 0.94rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .sms-head-btn.secondary {
        background: #eff4fa;
        border-color: #d9e5f1;
        color: #46637d;
    }

    .sms-head-btn.secondary:hover {
        background: #e5eef8;
        color: #274c72;
        text-decoration: none;
    }

    .sms-head-btn.primary {
        background: linear-gradient(135deg, var(--sms-success) 0%, #168062 100%);
        color: #fff;
        box-shadow: 0 14px 24px -22px rgba(28, 155, 116, 0.92);
    }

    .sms-head-btn.primary:hover {
        color: #fff;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sms-head-btn.danger {
        background: rgba(207, 77, 93, 0.10);
        border-color: rgba(207, 77, 93, 0.18);
        color: #b74152;
    }

    .sms-head-btn.danger:hover {
        background: var(--sms-danger);
        border-color: var(--sms-danger);
        color: #fff;
        text-decoration: none;
    }

    .sms-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(300px, 0.85fr);
        gap: 18px;
        margin-top: 18px;
    }

    .sms-main-column,
    .sms-side-column {
        display: grid;
        gap: 18px;
        align-content: start;
    }

    .sms-show-side {
        background: linear-gradient(180deg, rgba(243, 248, 253, 0.96) 0%, rgba(255, 255, 255, 0.96) 100%);
    }

    .sms-section-kicker {
        margin: 0 0 8px;
        color: #6f8ba7;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .sms-summary-card h2,
    .sms-show-card h2,
    .sms-message-card h2,
    .sms-error-card h2 {
        margin: 0;
        color: var(--sms-ink);
        font-size: 1.16rem;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .sms-summary-text,
    .sms-card-subtitle {
        margin: 10px 0 0;
        color: var(--sms-muted);
        font-size: 0.94rem;
        line-height: 1.6;
    }

    .sms-summary-grid,
    .sms-meta-grid {
        margin-top: 16px;
        display: grid;
        gap: 10px;
    }

    .sms-summary-item,
    .sms-meta-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid #dce8f3;
        background: #f6fafe;
    }

    .sms-summary-item span,
    .sms-meta-item span {
        color: #5b7690;
        font-size: 0.88rem;
        font-weight: 600;
    }

    .sms-summary-item strong,
    .sms-meta-item strong {
        color: var(--sms-ink);
        font-size: 0.92rem;
        font-weight: 800;
        text-align: right;
    }

    .sms-facts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 18px;
    }

    .sms-fact {
        padding: 16px;
        border-radius: 16px;
        border: 1px solid #dce8f3;
        background: #f9fbfe;
    }

    .sms-fact-label {
        display: block;
        color: #6c89a5;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .sms-fact-value {
        color: var(--sms-ink);
        font-size: 1rem;
        line-height: 1.55;
        font-weight: 700;
    }

    .sms-message-bubble {
        margin-top: 18px;
        padding: 18px 20px;
        border-radius: 22px 22px 22px 10px;
        background: linear-gradient(135deg, var(--sms-primary) 0%, var(--sms-primary-dark) 100%);
        color: #fff;
        font-size: 0.98rem;
        line-height: 1.72;
        white-space: pre-wrap;
        box-shadow: 0 22px 32px -26px rgba(18, 116, 216, 0.82);
    }

    .sms-message-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    .sms-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d9e6f2;
        background: #fff;
        color: #47637e;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .sms-error-card {
        border-color: #f0c7cd;
        background: linear-gradient(180deg, #fff7f8 0%, #fff1f3 100%);
    }

    .sms-error-card p {
        margin: 14px 0 0;
        color: #973544;
        font-size: 0.95rem;
        line-height: 1.65;
    }

    html.dark .sms-show-page,
    body.dark-mode .sms-show-page {
        --sms-ink: #e5effb;
        --sms-muted: #a8bed4;
        border-color: #284763;
        background:
            radial-gradient(circle at top right, rgba(87, 156, 255, 0.14) 0%, transparent 30%),
            radial-gradient(circle at bottom left, rgba(28, 155, 116, 0.10) 0%, transparent 28%),
            linear-gradient(140deg, #0e1a2b 0%, #102033 100%);
        box-shadow: 0 24px 42px -34px rgba(0, 0, 0, 0.9);
    }

    html.dark .sms-show-hero,
    html.dark .sms-show-side,
    html.dark .sms-show-card,
    html.dark .sms-message-card,
    html.dark .sms-error-card,
    html.dark .sms-summary-card,
    body.dark-mode .sms-show-hero,
    body.dark-mode .sms-show-side,
    body.dark-mode .sms-show-card,
    body.dark-mode .sms-message-card,
    body.dark-mode .sms-error-card,
    body.dark-mode .sms-summary-card {
        background: rgba(17, 34, 53, 0.92);
        border-color: #2f4f6e;
        box-shadow: 0 22px 36px -28px rgba(0, 0, 0, 0.55);
    }

    html.dark .sms-show-side,
    body.dark-mode .sms-show-side {
        background: linear-gradient(180deg, rgba(19, 38, 60, 0.96) 0%, rgba(17, 34, 53, 0.96) 100%);
    }

    html.dark .sms-title-block h1,
    html.dark .sms-summary-card h2,
    html.dark .sms-show-card h2,
    html.dark .sms-message-card h2,
    html.dark .sms-error-card h2,
    html.dark .sms-summary-item strong,
    html.dark .sms-meta-item strong,
    html.dark .sms-fact-value,
    body.dark-mode .sms-title-block h1,
    body.dark-mode .sms-summary-card h2,
    body.dark-mode .sms-show-card h2,
    body.dark-mode .sms-message-card h2,
    body.dark-mode .sms-error-card h2,
    body.dark-mode .sms-summary-item strong,
    body.dark-mode .sms-meta-item strong,
    body.dark-mode .sms-fact-value {
        color: #e5effb;
    }

    html.dark .sms-title-block p,
    html.dark .sms-section-kicker,
    html.dark .sms-summary-text,
    html.dark .sms-card-subtitle,
    html.dark .sms-summary-item span,
    html.dark .sms-meta-item span,
    html.dark .sms-fact-label,
    body.dark-mode .sms-title-block p,
    body.dark-mode .sms-section-kicker,
    body.dark-mode .sms-summary-text,
    body.dark-mode .sms-card-subtitle,
    body.dark-mode .sms-summary-item span,
    body.dark-mode .sms-meta-item span,
    body.dark-mode .sms-fact-label {
        color: #a8bed4;
    }

    html.dark .sms-chip,
    html.dark .sms-summary-item,
    html.dark .sms-meta-item,
    html.dark .sms-fact,
    html.dark .sms-meta-chip,
    body.dark-mode .sms-chip,
    body.dark-mode .sms-summary-item,
    body.dark-mode .sms-meta-item,
    body.dark-mode .sms-fact,
    body.dark-mode .sms-meta-chip {
        background: #17314c;
        border-color: #355777;
        color: #d5e7fb;
    }

    html.dark .sms-back-btn,
    body.dark-mode .sms-back-btn {
        background: #17314d;
        border-color: #355777;
        color: #d5e7fb;
    }

    html.dark .sms-back-icon,
    body.dark-mode .sms-back-icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    html.dark .sms-back-btn:hover,
    html.dark .sms-head-btn.secondary:hover,
    body.dark-mode .sms-back-btn:hover,
    body.dark-mode .sms-head-btn.secondary:hover {
        background: #21486f;
        border-color: #4d7aa5;
        color: #fff;
    }

    html.dark .sms-error-card,
    body.dark-mode .sms-error-card {
        border-color: #6d3a44;
        background: linear-gradient(180deg, rgba(82, 28, 37, 0.42) 0%, rgba(58, 20, 30, 0.42) 100%);
    }

    html.dark .sms-error-card p,
    body.dark-mode .sms-error-card p {
        color: #fec9d0;
    }

    @media (max-width: 1199px) {
        .sms-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 992px) {
        .sms-facts {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .sms-show-page {
            padding: 12px;
            border-radius: 18px;
        }

        .sms-show-hero,
        .sms-show-side,
        .sms-show-card,
        .sms-message-card,
        .sms-error-card,
        .sms-summary-card {
            border-radius: 18px;
        }

        .sms-top,
        .sms-title-row,
        .sms-badge-row,
        .sms-head-actions {
            align-items: stretch;
        }

        .sms-back-btn,
        .sms-head-btn {
            width: 100%;
        }

        .sms-head-actions {
            display: grid;
        }
    }
</style>

<div class="sms-show-page">
    <div class="sms-alert-stack">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-0">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-0">{{ session('error') }}</div>
        @endif
    </div>

    <section class="sms-show-hero">
        <div class="sms-top">
            <a href="{{ route('sms.index') }}" class="sms-back-btn">
                <span class="sms-back-icon"><i class="fas fa-arrow-left"></i></span>
                <span>Retour</span>
            </a>

            <div class="sms-badge-row">
                <span class="sms-badge">
                    <i class="fas fa-message"></i>
                    Rappel #{{ $reminder->id }}
                </span>
                <span class="sms-status-pill {{ $reminder->show_status_class }}">
                    <i class="fas fa-circle"></i>
                    {{ ucfirst($reminder->statut) }}
                </span>
            </div>
        </div>

        <div class="sms-title-row">
            <span class="sms-title-icon"><i class="fas fa-comment-medical"></i></span>
            <div class="sms-title-block">
                <h1>Detail du rappel SMS</h1>
                <p>Consultez le statut, le contexte de rendez-vous et le contenu exact du message dans une vue plus claire et plus professionnelle.</p>
            </div>
        </div>

        <div class="sms-head-actions">
            <a href="{{ route('sms.edit', $reminder) }}" class="sms-head-btn secondary">
                <i class="fas fa-pen"></i>
                Modifier
            </a>
            <a href="{{ route('sms.index') }}" class="sms-head-btn primary">
                <i class="fas fa-list"></i>
                Liste rappels
            </a>
            <form method="POST" action="{{ route('sms.resend', $reminder) }}">
                @csrf
                <button type="submit" class="sms-head-btn danger" onclick="return confirm('Confirmer le renvoi de ce SMS ?')">
                    <i class="fas fa-rotate-right"></i>
                    Renvoyer
                </button>
            </form>
        </div>
    </section>

    <div class="sms-layout">
        <div class="sms-main-column">
            <section class="sms-show-card">
                <p class="sms-section-kicker">Informations principales</p>
                <h2>Contexte du rappel</h2>
                <p class="sms-card-subtitle">Le rappel est rattache au patient, au rendez-vous et au canal d envoi ci-dessous.</p>

                <div class="sms-facts">
                    <div class="sms-fact">
                        <span class="sms-fact-label">Patient</span>
                        <div class="sms-fact-value">{{ $patientName ?: 'Patient inconnu' }}</div>
                    </div>
                    <div class="sms-fact">
                        <span class="sms-fact-label">Telephone</span>
                        <div class="sms-fact-value">{{ $reminder->telephone ?? '--' }}</div>
                    </div>
                    <div class="sms-fact">
                        <span class="sms-fact-label">Rendez-vous</span>
                        <div class="sms-fact-value">
                            {{ optional($reminder->rendezvous?->date_heure)->format('d/m/Y H:i') ?: '--' }}
                            @if($reminder->display_doctor_name)
                                <br>Dr. {{ $reminder->display_doctor_name }}
                            @endif
                        </div>
                    </div>
                    <div class="sms-fact">
                        <span class="sms-fact-label">Provider</span>
                        <div class="sms-fact-value">{{ $reminder->provider ?: '--' }}</div>
                    </div>
                </div>
            </section>

            <section class="sms-message-card">
                <p class="sms-section-kicker">Contenu envoye</p>
                <h2>Message SMS</h2>
                <p class="sms-card-subtitle">Le texte ci-dessous correspond au message enregistre pour ce rappel.</p>

                <div class="sms-message-meta">
                    <span class="sms-meta-chip">
                        <i class="fas fa-phone"></i>
                        {{ $reminder->telephone ?? 'Numero non renseigne' }}
                    </span>
                    <span class="sms-meta-chip">
                        <i class="fas fa-text-width"></i>
                        {{ mb_strlen($reminder->message_template ?? '') }} caracteres
                    </span>
                </div>

                <div class="sms-message-bubble">{{ $reminder->message_template ?: 'Aucun message personnalise' }}</div>
            </section>

            @if($reminder->erreur_message)
                <section class="sms-error-card">
                    <p class="sms-section-kicker">Incident d envoi</p>
                    <h2>Erreur retournee</h2>
                    <p>{{ $reminder->erreur_message }}</p>
                </section>
            @endif
        </div>

        <div class="sms-side-column">
            <section class="sms-show-side">
                <p class="sms-section-kicker">Resume operatoire</p>
                <h2>Suivi du rappel</h2>
                <p class="sms-summary-text">Gardez une lecture rapide de l etat actuel, du planning d envoi et de l historique de traitement.</p>

                <div class="sms-summary-grid">
                    <div class="sms-summary-item">
                        <span>Statut</span>
                        <strong>{{ ucfirst($reminder->statut) }}</strong>
                    </div>
                    <div class="sms-summary-item">
                        <span>Envoi prevu</span>
                        <strong>{{ optional($reminder->date_envoi_prevue)->format('d/m/Y H:i') ?: '--' }}</strong>
                    </div>
                    <div class="sms-summary-item">
                        <span>Envoi reel</span>
                        <strong>{{ optional($reminder->date_envoi_reelle)->format('d/m/Y H:i') ?: '--' }}</strong>
                    </div>
                    <div class="sms-summary-item">
                        <span>Provider</span>
                        <strong>{{ $reminder->provider ?: '--' }}</strong>
                    </div>
                </div>
            </section>

            <section class="sms-summary-card">
                <p class="sms-section-kicker">Identifiants</p>
                <h2>Trace du rappel</h2>
                <p class="sms-card-subtitle">Ce bloc centralise les references utiles pour le support ou le suivi administratif.</p>

                <div class="sms-meta-grid">
                    <div class="sms-meta-item">
                        <span>Rappel</span>
                        <strong>#{{ $reminder->id }}</strong>
                    </div>
                    <div class="sms-meta-item">
                        <span>Patient</span>
                        <strong>{{ $reminder->patient_id ?? '--' }}</strong>
                    </div>
                    <div class="sms-meta-item">
                        <span>Rendez-vous</span>
                        <strong>{{ $reminder->rendezvous_id ?? '--' }}</strong>
                    </div>
                    <div class="sms-meta-item">
                        <span>Heures avant</span>
                        <strong>{{ $reminder->heures_avant ?? '--' }}</strong>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

