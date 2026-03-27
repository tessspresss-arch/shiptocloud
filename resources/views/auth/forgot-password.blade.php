<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mot de passe oublie | MEDISYS Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --auth-bg: #f4f7fb;
            --auth-surface: #ffffff;
            --auth-border: rgba(181, 200, 221, 0.72);
            --auth-text: #17324d;
            --auth-muted: #667c95;
            --auth-primary: #2c7be5;
            --auth-primary-strong: #1f6fa3;
            --auth-success: #00a389;
            --auth-danger: #e5533d;
            --auth-shadow: 0 28px 60px -34px rgba(18, 44, 74, 0.35);
            --auth-radius-xl: 28px;
            --auth-radius-lg: 22px;
            --auth-radius-md: 16px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            width: 100%;
            overflow-x: clip;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--auth-text);
            background: linear-gradient(135deg, #f8fafb 0%, #f0f4f8 100%);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.42), rgba(255, 255, 255, 0)),
                repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0, rgba(255, 255, 255, 0.18) 1px, transparent 1px, transparent 18px);
            opacity: 0.18;
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 18% 78%, rgba(0, 163, 137, 0.11) 0%, rgba(0, 163, 137, 0) 28%),
                radial-gradient(circle at 78% 24%, rgba(44, 123, 229, 0.12) 0%, rgba(44, 123, 229, 0) 32%);
        }

        .recovery-shell {
            min-height: 100vh;
            padding: 24px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            overflow: clip;
            isolation: isolate;
        }

        .recovery-shell::before,
        .recovery-shell::after {
            content: "";
            position: absolute;
            pointer-events: none;
            border-radius: 999px;
            z-index: -1;
        }

        .recovery-shell::before {
            width: min(420px, 42vw);
            height: min(420px, 42vw);
            top: 6%;
            right: 8%;
            background: radial-gradient(circle, rgba(44, 123, 229, 0.14) 0%, rgba(44, 123, 229, 0) 72%);
            filter: blur(16px);
        }

        .recovery-shell::after {
            width: min(360px, 36vw);
            height: min(360px, 36vw);
            left: 10%;
            bottom: 8%;
            background: radial-gradient(circle, rgba(0, 163, 137, 0.12) 0%, rgba(0, 163, 137, 0) 74%);
            filter: blur(18px);
        }

        .recovery-wrap {
            width: min(100%, 1088px);
            display: grid;
            grid-template-columns: minmax(0, 1.04fr) minmax(344px, 408px);
            gap: clamp(24px, 3vw, 40px);
            align-items: start;
        }

        .recovery-story {
            min-width: 0;
        }

        .recovery-story-surface {
            position: relative;
            min-height: clamp(680px, 72vh, 780px);
            padding: clamp(28px, 4vw, 40px);
            border-radius: 32px;
            border: 1px solid rgba(188, 205, 224, 0.76);
            background:
                radial-gradient(circle at top right, rgba(44, 123, 229, 0.16) 0%, rgba(44, 123, 229, 0) 34%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, rgba(244, 249, 255, 0.92) 100%);
            box-shadow: 0 34px 70px -40px rgba(18, 44, 74, 0.34);
            overflow: hidden;
            display: grid;
            gap: 22px;
            align-content: space-between;
            isolation: isolate;
        }

        .recovery-story-surface::before,
        .recovery-story-surface::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            z-index: -1;
        }

        .recovery-story-surface::before {
            width: 260px;
            height: 260px;
            top: -70px;
            right: -70px;
            background: radial-gradient(circle, rgba(44, 123, 229, 0.16) 0%, rgba(44, 123, 229, 0) 74%);
        }

        .recovery-story-surface::after {
            width: 220px;
            height: 220px;
            left: -50px;
            bottom: -60px;
            background: radial-gradient(circle, rgba(0, 163, 137, 0.14) 0%, rgba(0, 163, 137, 0) 74%);
        }

        .recovery-story-top {
            display: grid;
            gap: 14px;
        }

        .recovery-story-brand {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            justify-content: center;
            min-height: 54px;
            padding: 14px 18px;
            border-radius: 18px;
            border: 1px solid rgba(194, 212, 232, 0.72);
            background: linear-gradient(135deg, var(--auth-primary) 0%, var(--auth-primary-strong) 100%);
            box-shadow: 0 14px 24px -22px rgba(31, 111, 163, 0.38);
        }

        .recovery-story-brand .brand-textmark {
            display: inline-flex;
            position: relative;
            align-items: flex-start;
            justify-content: center;
            min-width: 0;
            padding: 6px 26px 4px 0;
            font-family: 'Manrope', 'Segoe UI', Roboto, Arial, sans-serif;
        }

        .recovery-story-brand .brand-text {
            display: inline-block;
            color: #ffffff;
            font-size: clamp(1.72rem, 3.2vw, 2.08rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.045em;
            white-space: nowrap;
        }

        .recovery-story-brand .brand-badge {
            position: absolute;
            top: -6px;
            right: -16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 18px;
            padding: 0 6px;
            border-radius: 8px;
            background: #ffffff;
            color: #1f6fa3;
            font-size: 10px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.01em;
            box-shadow: 0 10px 18px -16px rgba(4, 36, 77, 0.72);
        }

        .recovery-story-kicker {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid rgba(188, 205, 224, 0.8);
            background: rgba(255, 255, 255, 0.76);
            color: #2f5b85;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .recovery-story-title {
            margin: 0;
            max-width: 12ch;
            color: #0f3158;
            font-size: clamp(2rem, 4vw, 3.25rem);
            font-weight: 800;
            line-height: 0.98;
            letter-spacing: -0.05em;
        }

        .recovery-story-copy {
            margin: 0;
            max-width: 58ch;
            color: #617892;
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.75;
        }

        .recovery-story-grid,
        .recovery-story-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .recovery-story-item,
        .recovery-story-stat {
            min-width: 0;
            padding: 15px 16px;
            border-radius: 20px;
            border: 1px solid rgba(194, 212, 232, 0.72);
            background: rgba(255, 255, 255, 0.76);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
        }

        .recovery-story-item-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            background: rgba(44, 123, 229, 0.1);
            color: var(--auth-primary-strong);
        }

        .recovery-story-item strong,
        .recovery-story-stat strong {
            display: block;
            color: #17324d;
            font-size: 0.92rem;
            font-weight: 800;
            line-height: 1.35;
        }

        .recovery-story-item p,
        .recovery-story-stat span {
            margin: 6px 0 0;
            color: #6e839a;
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.58;
        }

        .recovery-card {
            position: relative;
            display: grid;
            align-content: start;
            align-self: start;
            justify-self: end;
            width: 100%;
            max-width: 408px;
            margin-top: clamp(106px, 12vh, 146px);
            border-radius: var(--auth-radius-xl);
            border: 1px solid var(--auth-border);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92) 0%, rgba(247, 251, 255, 0.9) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: var(--auth-shadow);
            padding: clamp(22px, 4vw, 34px);
            overflow: hidden;
        }

        .recovery-card::before {
            content: "";
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, rgba(44, 123, 229, 0.18) 0%, rgba(0, 163, 137, 0.22) 52%, rgba(44, 123, 229, 0.08) 100%);
        }

        .recovery-card-header {
            margin-bottom: 22px;
            text-align: left;
            display: grid;
            justify-items: flex-start;
            gap: 10px;
        }

        .recovery-panel-kicker {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 0 10px;
            border-radius: 999px;
            background: rgba(44, 123, 229, 0.1);
            color: var(--auth-primary-strong);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .recovery-panel-title {
            margin: 0;
            color: #133553;
            font-size: clamp(1.82rem, 2.5vw, 2.2rem);
            font-weight: 800;
            line-height: 1.04;
            letter-spacing: -0.04em;
        }

        .recovery-panel-copy {
            margin: 0;
            color: #6c839a;
            font-size: 0.92rem;
            font-weight: 600;
            line-height: 1.65;
            max-width: 42ch;
        }

        .alert {
            border-radius: var(--auth-radius-md);
            border: 1px solid transparent;
            padding: 14px 16px;
            margin-bottom: 16px;
            box-shadow: 0 14px 24px -26px rgba(15, 34, 56, 0.5);
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-body {
            min-width: 0;
            flex: 1;
        }

        .alert-title {
            display: block;
            margin-bottom: 2px;
            font-size: 0.9rem;
            font-weight: 800;
        }

        .alert-copy {
            display: block;
            font-size: 0.86rem;
            font-weight: 600;
            line-height: 1.5;
        }

        .alert-info {
            color: #165f73;
            border-color: rgba(19, 95, 114, 0.16);
            background: rgba(223, 242, 248, 0.96);
        }

        .alert-danger {
            color: #8f3122;
            border-color: rgba(229, 83, 61, 0.16);
            background: rgba(251, 228, 223, 0.96);
        }

        .alert-close {
            width: 34px;
            height: 34px;
            border: 1px solid rgba(167, 188, 211, 0.5);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-left: auto;
            background: rgba(255, 255, 255, 0.58);
            color: currentColor;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        }

        .alert-close:hover {
            background: rgba(255, 255, 255, 0.82);
            border-color: rgba(123, 148, 175, 0.58);
            transform: translateY(-1px);
        }

        .recovery-form {
            display: grid;
            gap: 18px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field-label {
            font-size: 0.91rem;
            font-weight: 800;
            color: #1f3f5b;
        }

        .field-shell {
            display: flex;
            align-items: center;
            min-height: 58px;
            padding: 0 14px;
            border-radius: var(--auth-radius-lg);
            border: 1px solid #c2d3e4;
            background: var(--auth-surface);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .field-shell:focus-within {
            border-color: rgba(44, 123, 229, 0.72);
            box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.15), 0 10px 22px -24px rgba(31, 111, 163, 0.44);
            transform: translateY(-1px);
        }

        .field-shell.has-error {
            border-color: rgba(229, 83, 61, 0.5);
            background: #fffdfd;
        }

        .field-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--auth-primary-strong);
            background: rgba(44, 123, 229, 0.1);
        }

        .field-icon i {
            font-size: 0.96rem;
        }

        .field-shell input {
            width: 100%;
            height: 56px;
            border: none;
            background: transparent;
            box-shadow: none !important;
            outline: none;
            padding: 0 12px;
            color: var(--auth-text);
            font-size: 0.98rem;
            font-weight: 600;
        }

        .field-shell input::placeholder {
            color: #72879d;
            font-weight: 600;
        }

        .invalid-feedback {
            margin-top: -2px;
            padding-left: 4px;
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--auth-danger);
        }

        .recovery-actions {
            display: grid;
            gap: 12px;
            margin-top: 2px;
        }

        .recovery-submit {
            width: 100%;
            min-height: 56px;
            border: none;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #2473d8 0%, #1b629f 100%);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 800;
            box-shadow: 0 20px 32px -22px rgba(30, 95, 156, 0.52);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .recovery-submit:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 24px 36px -24px rgba(30, 95, 156, 0.58);
            filter: saturate(1.04);
        }

        .submit-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #ffffff;
            border-radius: 999px;
            animation: spin 0.8s linear infinite;
        }

        .recovery-submit.is-loading {
            pointer-events: none;
            opacity: 0.96;
        }

        .recovery-submit.is-loading .submit-spinner {
            display: inline-block;
        }

        .recovery-submit.is-loading .submit-icon {
            display: none;
        }

        .back-to-login {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 14px;
            border: 1px solid rgba(194, 212, 232, 0.76);
            background: rgba(255, 255, 255, 0.76);
            color: var(--auth-primary-strong);
            font-size: 0.92rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        }

        .back-to-login:hover {
            color: var(--auth-primary);
            background: rgba(255, 255, 255, 0.94);
            border-color: rgba(177, 199, 222, 0.9);
            transform: translateY(-1px);
        }

        .recovery-note {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid rgba(194, 212, 232, 0.72);
            background: rgba(255, 255, 255, 0.72);
            color: var(--auth-muted);
            font-size: 0.87rem;
            font-weight: 600;
            line-height: 1.6;
        }

        .recovery-note strong {
            color: #1a3a56;
        }

        .recovery-footer {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 4px;
            padding: 4px 0 0;
            color: #7a8fa5;
            font-size: 0.84rem;
            text-align: center;
        }

        .recovery-footer strong {
            color: var(--auth-primary-strong);
        }

        .recovery-footer span {
            font-size: 0.8rem;
            color: #8296ab;
            font-weight: 600;
        }

        .alert-close:focus-visible,
        .recovery-submit:focus-visible,
        .back-to-login:focus-visible {
            outline: none;
            box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.14);
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 991.98px) {
            .recovery-wrap {
                max-width: 780px;
                grid-template-columns: 1fr;
                align-items: stretch;
            }

            .recovery-story-surface {
                min-height: auto;
            }

            .recovery-card {
                max-width: none;
                justify-self: stretch;
                margin-top: 0;
            }

            .recovery-story-grid,
            .recovery-story-meta {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .recovery-footer {
                padding-top: 0;
            }
        }

        @media (max-width: 767.98px) {
            .recovery-story-title {
                max-width: none;
            }

            .recovery-story-grid,
            .recovery-story-meta {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .recovery-shell {
                padding: 16px 12px;
            }

            .recovery-wrap {
                gap: 14px;
            }

            .recovery-story-surface {
                padding: 24px 20px;
                border-radius: 26px;
            }

            .recovery-card {
                padding: 20px 16px;
                border-radius: 22px;
            }

            .recovery-card-header {
                margin-bottom: 18px;
            }

            .field-shell {
                min-height: 56px;
                padding: 0 12px;
            }

            .field-shell input {
                height: 54px;
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>
    @php
        $statusMessage = session('status');
        $errorMessage = $errors->first('email') ?: $errors->first();
        $feedbackTone = null;
        $feedbackTitle = null;
        $feedbackMessage = null;
        $feedbackIcon = 'fa-circle-info';

        if ($statusMessage) {
            $feedbackTone = 'info';
            $feedbackMessage = $statusMessage;
            $feedbackTitle = 'Lien envoye';
        } elseif ($errorMessage) {
            $feedbackTone = 'danger';
            $feedbackMessage = $errorMessage;
            $feedbackIcon = 'fa-triangle-exclamation';
            $feedbackTitle = 'Demande impossible';
        }
    @endphp

    <main class="recovery-shell">
        <section class="recovery-wrap" aria-label="Recuperation de mot de passe MEDISYS Pro">
            <aside class="recovery-story" aria-label="Presentation reinitialisation MEDISYS Pro">
                <div class="recovery-story-surface">
                    <div class="recovery-story-top">
                        <div class="recovery-story-brand" aria-label="Identite MEDISYS Pro">
                            @include('partials.brand-logo', ['wrapperClass' => 'recovery-story-wordmark'])
                        </div>

                        <span class="recovery-story-kicker">Recuperation securisee</span>
                        <h1 class="recovery-story-title">Retrouvez rapidement l'acces a votre espace.</h1>
                        <p class="recovery-story-copy">
                            Saisissez votre adresse professionnelle pour recevoir un lien de reinitialisation.
                            Aucun changement n'est applique tant que vous ne confirmez pas la procedure.
                        </p>
                    </div>

                    <div class="recovery-story-grid" aria-label="Atouts du parcours de recuperation">
                        <article class="recovery-story-item">
                            <span class="recovery-story-item-icon"><i class="fas fa-envelope-open-text"></i></span>
                            <strong>Lien securise</strong>
                            <p>Le parcours commence par un e-mail reserve a l'adresse associee a votre compte.</p>
                        </article>

                        <article class="recovery-story-item">
                            <span class="recovery-story-item-icon"><i class="fas fa-bolt"></i></span>
                            <strong>Retour rapide</strong>
                            <p>Vous reprenez l'acces a MEDISYS Pro sans interrompre votre organisation clinique.</p>
                        </article>

                        <article class="recovery-story-item">
                            <span class="recovery-story-item-icon"><i class="fas fa-user-shield"></i></span>
                            <strong>Confidentialite</strong>
                            <p>Le formulaire confirme la demande sans exposer d'informations sensibles a l'ecran.</p>
                        </article>
                    </div>

                    <div class="recovery-story-meta" aria-label="Reassurance recuperation">
                        <div class="recovery-story-stat">
                            <strong>Adresse verifiee</strong>
                            <span>Utilisez la boite e-mail rattachee a votre profil professionnel.</span>
                        </div>

                        <div class="recovery-story-stat">
                            <strong>Demande tracee</strong>
                            <span>Chaque tentative de recuperation suit un flux controle et journalise.</span>
                        </div>

                        <div class="recovery-story-stat">
                            <strong>Retour au login</strong>
                            <span>Vous pouvez revenir a la connexion des que vous retrouvez vos identifiants.</span>
                        </div>
                    </div>
                </div>
            </aside>

            <section class="recovery-card" aria-label="Formulaire de recuperation">
                <div class="recovery-card-header">
                    <span class="recovery-panel-kicker">Mot de passe oublie</span>
                    <h2 class="recovery-panel-title">Reinitialisez votre acces MEDISYS Pro</h2>
                    <p class="recovery-panel-copy">
                        Indiquez votre adresse e-mail professionnelle. Si un compte existe, vous recevrez un lien
                        pour definir un nouveau mot de passe.
                    </p>
                </div>

                @if($feedbackMessage)
                    <div class="alert alert-{{ $feedbackTone }}" role="alert" aria-live="polite">
                        <i class="fas {{ $feedbackIcon }}"></i>
                        <span class="alert-body">
                            <span class="alert-title">{{ $feedbackTitle }}</span>
                            <span class="alert-copy">{{ $feedbackMessage }}</span>
                        </span>
                        <button type="button" class="alert-close" data-dismiss-alert aria-label="Fermer le message">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="recovery-form" id="recoveryForm" novalidate>
                    @csrf

                    <div class="field">
                        <label for="email" class="field-label">Adresse e-mail</label>
                        <div class="field-shell @error('email') has-error @enderror">
                            <span class="field-icon"><i class="fas fa-envelope"></i></span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="admin@medisys.pro"
                                required
                                autofocus
                                autocomplete="username"
                                aria-invalid="@error('email') true @else false @enderror"
                            >
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="recovery-actions">
                        <button type="submit" class="recovery-submit" id="recoverySubmit">
                            <span class="submit-spinner" aria-hidden="true"></span>
                            <i class="fas fa-paper-plane submit-icon"></i>
                            <span class="submit-label">Envoyer le lien de reinitialisation</span>
                        </button>

                        <a href="{{ route('login') }}" class="back-to-login">
                            <i class="fas fa-arrow-left"></i>
                            <span>Retour a la connexion</span>
                        </a>
                    </div>
                </form>

                <div class="recovery-note">
                    <strong>Besoin d'aide ?</strong> Si vous n'avez plus acces a votre e-mail professionnel, contactez
                    l'administrateur ou l'equipe support avant de multiplier les tentatives.
                </div>
            </section>

            <footer class="recovery-footer">
                <strong>MEDISYS Pro | Plateforme medicale premium pour les equipes de soin</strong>
                <span>&copy; {{ date('Y') }} Plateforme reservee aux environnements autorises</span>
            </footer>
        </section>
    </main>

    <script>
        const recoveryForm = document.getElementById('recoveryForm');
        const recoverySubmit = document.getElementById('recoverySubmit');

        if (recoveryForm && recoverySubmit) {
            recoveryForm.addEventListener('submit', function (event) {
                if (typeof recoveryForm.checkValidity === 'function' && !recoveryForm.checkValidity()) {
                    event.preventDefault();
                    if (typeof recoveryForm.reportValidity === 'function') {
                        recoveryForm.reportValidity();
                    }
                    return;
                }

                recoverySubmit.classList.add('is-loading');
                recoverySubmit.setAttribute('aria-busy', 'true');
                recoverySubmit.disabled = true;
            });
        }

        document.querySelectorAll('[data-dismiss-alert]').forEach(function(button) {
            button.addEventListener('click', function() {
                button.closest('.alert')?.remove();
            });
        });

        document.getElementById('email')?.focus();
    </script>
</body>
</html>
