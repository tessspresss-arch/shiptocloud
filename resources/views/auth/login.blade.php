<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion | MEDISYS Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --login-bg: #f4f7fb;
            --login-surface: #ffffff;
            --login-surface-soft: rgba(255, 255, 255, 0.78);
            --login-border: rgba(181, 200, 221, 0.72);
            --login-text: #17324d;
            --login-muted: #667c95;
            --login-primary: #2c7be5;
            --login-primary-strong: #1f6fa3;
            --login-success: #00a389;
            --login-danger: #e5533d;
            --login-shadow: 0 28px 60px -34px rgba(18, 44, 74, 0.35);
            --login-radius-xl: 28px;
            --login-radius-lg: 22px;
            --login-radius-md: 16px;
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
            color: var(--login-text);
            background:
                radial-gradient(circle at top right, rgba(44, 123, 229, 0.08) 0%, rgba(44, 123, 229, 0) 28%),
                radial-gradient(circle at bottom left, rgba(0, 163, 137, 0.06) 0%, rgba(0, 163, 137, 0) 26%),
                linear-gradient(180deg, #f8fbfe 0%, #f2f6fb 100%);
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
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.58), rgba(255, 255, 255, 0));
            opacity: 0.62;
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                url("data:image/svg+xml,%3Csvg width='640' height='640' viewBox='0 0 640 640' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg opacity='0.42'%3E%3Cpath d='M84 186H124' stroke='%232c7be5' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M104 166V206' stroke='%232c7be5' stroke-width='4' stroke-linecap='round'/%3E%3Ccircle cx='494' cy='130' r='32' stroke='%231f6fa3' stroke-width='4'/%3E%3Cpath d='M462 130H526' stroke='%231f6fa3' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M494 98V162' stroke='%231f6fa3' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M132 444C176 444 184 390 218 390C244 390 248 432 274 432C302 432 302 342 334 342C364 342 364 458 398 458C428 458 434 406 474 406H540' stroke='%2300a389' stroke-width='5' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpath d='M460 498C491.48 498 517 472.48 517 441C517 409.52 491.48 384 460 384C428.52 384 403 409.52 403 441C403 472.48 428.52 498 460 498Z' stroke='%232c7be5' stroke-width='4'/%3E%3Cpath d='M501 481L548 528' stroke='%232c7be5' stroke-width='4' stroke-linecap='round'/%3E%3Ccircle cx='164' cy='116' r='10' fill='%2300a389'/%3E%3Ccircle cx='524' cy='248' r='8' fill='%232c7be5'/%3E%3Ccircle cx='144' cy='518' r='6' fill='%231f6fa3'/%3E%3C/g%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center right 12%;
            background-size: min(920px, 82vw) auto;
            opacity: 0.045;
            mix-blend-mode: multiply;
        }

        .login-shell {
            min-height: 100vh;
            padding: 22px 16px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            overflow: clip;
            isolation: isolate;
        }

        .login-shell::before,
        .login-shell::after {
            content: "";
            position: absolute;
            pointer-events: none;
            border-radius: 999px;
            z-index: -1;
        }

        .login-shell::before {
            width: min(420px, 42vw);
            height: min(420px, 42vw);
            top: 6%;
            right: 8%;
            background: radial-gradient(circle, rgba(44, 123, 229, 0.14) 0%, rgba(44, 123, 229, 0) 72%);
            filter: blur(16px);
        }

        .login-shell::after {
            width: min(360px, 36vw);
            height: min(360px, 36vw);
            left: 10%;
            bottom: 8%;
            background: radial-gradient(circle, rgba(0, 163, 137, 0.12) 0%, rgba(0, 163, 137, 0) 74%);
            filter: blur(18px);
        }

        .login-wrap {
            width: min(100%, 1048px);
            display: grid;
            grid-template-columns: minmax(0, 1.02fr) minmax(360px, 408px);
            gap: clamp(24px, 3vw, 34px);
            align-items: center;
        }

        .login-story {
            min-width: 0;
        }

        .login-story-surface {
            position: relative;
            min-height: clamp(540px, 60vh, 620px);
            padding: clamp(28px, 4vw, 36px);
            border-radius: 32px;
            border: 1px solid rgba(188, 205, 224, 0.76);
            background:
                radial-gradient(circle at top right, rgba(44, 123, 229, 0.14) 0%, rgba(44, 123, 229, 0) 30%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(245, 250, 255, 0.92) 100%);
            box-shadow: 0 30px 56px -38px rgba(18, 44, 74, 0.28);
            overflow: hidden;
            display: grid;
            gap: 18px;
            align-content: start;
            isolation: isolate;
        }

        .login-story-surface::before,
        .login-story-surface::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            z-index: -1;
        }

        .login-story-surface::before {
            width: 260px;
            height: 260px;
            top: -70px;
            right: -70px;
            background: radial-gradient(circle, rgba(44, 123, 229, 0.16) 0%, rgba(44, 123, 229, 0) 74%);
        }

        .login-story-surface::after {
            width: 220px;
            height: 220px;
            left: -50px;
            bottom: -60px;
            background: radial-gradient(circle, rgba(0, 163, 137, 0.14) 0%, rgba(0, 163, 137, 0) 74%);
        }

        .login-story-top {
            display: grid;
            gap: 12px;
        }

        .login-story-brand {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            justify-content: center;
            min-height: 54px;
            padding: 14px 18px;
            border-radius: 18px;
            border: 1px solid rgba(194, 212, 232, 0.72);
            background: linear-gradient(135deg, var(--login-primary) 0%, var(--login-primary-strong) 100%);
            box-shadow: 0 14px 24px -22px rgba(31, 111, 163, 0.38);
        }

        .login-story-brand .brand-textmark {
            display: inline-flex;
            position: relative;
            align-items: flex-start;
            justify-content: center;
            min-width: 0;
            padding: 6px 26px 4px 0;
            font-family: 'Manrope', 'Segoe UI', Roboto, Arial, sans-serif;
        }

        .login-story-brand .brand-text {
            display: inline-block;
            color: #ffffff;
            font-size: clamp(1.72rem, 3.2vw, 2.08rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.045em;
            white-space: nowrap;
        }

        .login-story-brand .brand-badge {
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

        .login-story-kicker {
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

        .login-story-title {
            margin: 0;
            max-width: 12ch;
            color: #0f3158;
            font-size: clamp(2rem, 4vw, 3.35rem);
            font-weight: 800;
            line-height: 0.98;
            letter-spacing: -0.05em;
        }

        .login-story-copy {
            margin: 0;
            max-width: 50ch;
            color: #617892;
            font-size: 0.98rem;
            font-weight: 600;
            line-height: 1.64;
        }

        .login-story-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .login-story-item {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            column-gap: 14px;
            row-gap: 2px;
            align-items: start;
            min-width: 0;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid rgba(194, 212, 232, 0.72);
            background: rgba(255, 255, 255, 0.68);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.52);
        }

        .login-story-item-icon {
            grid-row: 1 / span 2;
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0;
            background: rgba(44, 123, 229, 0.1);
            color: var(--login-primary-strong);
        }

        .login-story-item strong {
            display: block;
            grid-column: 2;
            color: #17324d;
            font-size: 0.9rem;
            font-weight: 800;
            line-height: 1.3;
        }

        .login-story-item p {
            grid-column: 2;
            margin: 0;
            color: #6e839a;
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.54;
        }

        .login-story-trust {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding-top: 2px;
        }

        .login-story-trust-item {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            min-height: 40px;
            padding: 0 13px;
            border-radius: 999px;
            border: 1px solid rgba(194, 212, 232, 0.72);
            background: rgba(255, 255, 255, 0.72);
            color: #4c6783;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .login-story-trust-item i {
            color: var(--login-primary-strong);
            font-size: 0.86rem;
        }

        .login-brand-copy {
            margin: 0 0 6px;
            color: #617892;
            font-size: 0.94rem;
            font-weight: 700;
        }

        .login-brand-support {
            margin: 0;
            color: #71869d;
            font-size: 0.86rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .login-card {
            position: relative;
            display: grid;
            align-content: start;
            align-self: center;
            justify-self: end;
            width: 100%;
            max-width: 408px;
            margin-top: 0;
            border-radius: var(--login-radius-xl);
            border: 1px solid var(--login-border);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92) 0%, rgba(247, 251, 255, 0.9) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 30px 58px -38px rgba(18, 44, 74, 0.32);
            padding: clamp(24px, 4vw, 30px);
            overflow: hidden;
        }

        .login-card::before {
            content: "";
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, rgba(44, 123, 229, 0.18) 0%, rgba(0, 163, 137, 0.22) 52%, rgba(44, 123, 229, 0.08) 100%);
        }

        .login-card-header {
            margin-bottom: 20px;
            text-align: left;
            display: grid;
            justify-items: flex-start;
            gap: 9px;
        }

        .login-panel-kicker {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 0 10px;
            border-radius: 999px;
            background: rgba(44, 123, 229, 0.1);
            color: var(--login-primary-strong);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .login-panel-title {
            margin: 0;
            color: #133553;
            font-size: clamp(1.72rem, 2.4vw, 2.05rem);
            font-weight: 800;
            line-height: 1.02;
            letter-spacing: -0.04em;
        }

        .login-panel-copy {
            margin: 0;
            color: #6c839a;
            font-size: 0.9rem;
            font-weight: 600;
            line-height: 1.62;
            max-width: 40ch;
        }

        .login-card-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            width: min(100%, 366px);
            margin-bottom: 14px;
            padding: 13px 18px;
            border-radius: 18px;
            border: 1px solid rgba(194, 212, 232, 0.72);
            background: linear-gradient(135deg, var(--login-primary) 0%, var(--login-primary-strong) 100%);
            box-shadow: 0 14px 24px -22px rgba(31, 111, 163, 0.38);
            text-align: center;
        }

        .login-card-brand .brand-textmark {
            display: inline-flex;
            position: relative;
            align-items: flex-start;
            justify-content: center;
            min-width: 0;
            padding: 6px 26px 4px 0;
            font-family: 'Manrope', 'Segoe UI', Roboto, Arial, sans-serif;
        }

        .login-card-brand .brand-text {
            display: inline-block;
            color: #ffffff;
            font-size: clamp(1.72rem, 4.4vw, 1.98rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.045em;
            white-space: nowrap;
        }

        .login-card-brand .brand-badge {
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

        .alert {
            border-radius: var(--login-radius-md);
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

        .login-form {
            display: grid;
            gap: 17px;
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
            border-radius: var(--login-radius-lg);
            border: 1px solid #c2d3e4;
            background: rgba(251, 253, 255, 0.98);
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
            color: var(--login-primary-strong);
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
            color: var(--login-text);
            font-size: 0.98rem;
            font-weight: 600;
        }

        .field-shell input::placeholder {
            color: #72879d;
            font-weight: 600;
        }

        .field-toggle {
            width: 40px;
            height: 40px;
            border: 1px solid #d9e5f1;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #617b95;
            background: #f3f7fb;
            transition: all 0.2s ease;
        }

        .field-toggle:hover {
            color: var(--login-primary-strong);
            background: #eaf2fb;
            border-color: #c7d8ea;
        }

        .invalid-feedback.d-block {
            margin-top: -2px;
            padding-left: 4px;
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--login-danger);
        }

        .login-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .remember-toggle {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #4d6782;
            font-size: 0.92rem;
            font-weight: 600;
            padding: 6px 8px;
            margin: -6px -8px;
            border-radius: 12px;
            cursor: pointer;
        }

        .remember-toggle .form-check-input {
            width: 19px;
            height: 19px;
            margin: 0;
            border-color: #adc4da;
            box-shadow: none;
        }

        .remember-toggle .form-check-input:checked {
            background-color: var(--login-primary);
            border-color: var(--login-primary);
        }

        .forgot-password {
            color: var(--login-primary-strong);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .forgot-password:hover {
            color: var(--login-primary);
        }

        .login-submit {
            width: 100%;
            min-height: 56px;
            border: none;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 4px;
            background: linear-gradient(135deg, #2473d8 0%, #1b629f 100%);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 800;
            box-shadow: 0 20px 32px -22px rgba(30, 95, 156, 0.52);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .login-submit:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 24px 36px -24px rgba(30, 95, 156, 0.58);
            filter: saturate(1.04);
        }

        .login-submit .submit-spinner {
            display: none;
        }

        .login-submit.is-loading {
            pointer-events: none;
            opacity: 0.96;
        }

        .login-submit.is-loading .submit-spinner {
            display: inline-flex;
        }

        .login-submit.is-loading .submit-icon {
            display: none;
        }

        .login-footer {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 4px;
            flex-wrap: wrap;
            padding: 2px 0 0;
            color: #7a8fa5;
            font-size: 0.84rem;
            text-align: center;
        }

        .login-footer strong {
            color: var(--login-primary-strong);
        }

        .login-footer span {
            font-size: 0.8rem;
            color: #8296ab;
            font-weight: 600;
        }

        .field-toggle:focus-visible,
        .forgot-password:focus-visible,
        .login-submit:focus-visible,
        .remember-toggle:focus-within,
        .alert-close:focus-visible {
            outline: none;
            box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.14);
        }

        @media (max-width: 991.98px) {
            .login-wrap {
                max-width: 780px;
                grid-template-columns: 1fr;
                align-items: stretch;
            }

            .login-story-surface {
                min-height: auto;
            }

            .login-card {
                max-width: none;
                justify-self: stretch;
                margin-top: 0;
            }

            .login-story-grid {
                grid-template-columns: 1fr;
            }

            .login-story-trust {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .login-footer {
                padding: 0;
            }
        }

        @media (max-width: 767.98px) {
            .login-story-title {
                max-width: none;
            }

            .login-story-trust {
                grid-template-columns: 1fr;
            }

            .login-story-trust {
                display: grid;
            }
        }

        @media (max-width: 575.98px) {
            body::after {
                background-size: 780px auto;
                background-position: center top;
                opacity: 0.1;
            }

            .login-shell {
                padding: 16px 12px;
                align-items: center;
            }

            .login-shell::before {
                width: 220px;
                height: 220px;
                top: 2%;
                right: -12%;
            }

            .login-shell::after {
                width: 190px;
                height: 190px;
                left: -8%;
                bottom: 4%;
            }

            .login-wrap {
                gap: 14px;
            }

            .login-story-surface {
                padding: 24px 20px;
                border-radius: 26px;
            }

            .login-card-brand {
                margin-bottom: 16px;
                width: 100%;
                padding: 13px 16px;
            }

            .login-card {
                padding: 20px 16px;
                border-radius: 22px;
            }

            .login-card-header {
                margin-bottom: 18px;
            }

            .login-actions {
                align-items: flex-start;
                flex-direction: column;
            }

            .remember-toggle,
            .forgot-password {
                width: 100%;
            }

            .field-shell {
                min-height: 56px;
                padding: 0 12px;
            }

            .field-shell input {
                height: 54px;
                padding: 0 10px;
            }

            .login-footer {
                padding-top: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $statusMessage = session('status');
        $errorMessage = $errors->first();
        $feedbackTone = null;
        $feedbackTitle = null;
        $feedbackMessage = null;
        $feedbackIcon = 'fa-circle-info';

        if ($statusMessage) {
            $feedbackTone = 'info';
            $feedbackMessage = $statusMessage;
            $feedbackTitle = str_contains(\Illuminate\Support\Str::lower($statusMessage), 'expire')
                ? 'Session expirée'
                : 'Information système';
        } elseif ($errorMessage) {
            $feedbackTone = 'danger';
            $feedbackMessage = $errorMessage;
            $feedbackIcon = 'fa-triangle-exclamation';
            $feedbackTitle = match (true) {
                str_contains(\Illuminate\Support\Str::lower($errorMessage), 'desactive'),
                str_contains(\Illuminate\Support\Str::lower($errorMessage), 'suspendu'),
                str_contains(\Illuminate\Support\Str::lower($errorMessage), 'autorise'),
                str_contains(\Illuminate\Support\Str::lower($errorMessage), 'expire') => 'Compte indisponible',
                $errors->has('email') && ! $errors->has('password') => 'Authentification refusée',
                default => 'Connexion impossible',
            };
        }
    @endphp
    <main class="login-shell">
        <section class="login-wrap" aria-label="Connexion MEDISYS Pro">
            <aside class="login-story" aria-label="Presentation MEDISYS Pro">
                <div class="login-story-surface">
                    <div class="login-story-top">
                        <div class="login-story-brand" aria-label="Identite MEDISYS Pro">
                            @include('partials.brand-logo', ['wrapperClass' => 'login-story-wordmark'])
                        </div>

                        <span class="login-story-kicker">Plateforme SaaS medicale</span>
                        <h1 class="login-story-title">Reconnectez-vous a votre espace clinique.</h1>
                        <p class="login-story-copy">
                            MEDISYS Pro centralise le suivi patient, les ordonnances, l'agenda et la facturation
                            dans une interface claire, fiable et rassurante pour votre equipe.
                        </p>
                    </div>

                    <div class="login-story-grid" aria-label="Atouts MEDISYS Pro">
                        <article class="login-story-item">
                            <span class="login-story-item-icon"><i class="fas fa-wave-square"></i></span>
                            <strong>Pilotage clinique</strong>
                            <p>Suivez les consultations, les dossiers et les priorites du jour sans dispersion.</p>
                        </article>

                        <article class="login-story-item">
                            <span class="login-story-item-icon"><i class="fas fa-shield-halved"></i></span>
                            <strong>Securite continue</strong>
                            <p>Acces reserve, sessions tracees et connexion plus maitrisée.</p>
                        </article>

                        <article class="login-story-item">
                            <span class="login-story-item-icon"><i class="fas fa-layer-group"></i></span>
                            <strong>Flux unifie</strong>
                            <p>Une seule plateforme pour soigner, suivre et administrer.</p>
                        </article>
                    </div>

                    <div class="login-story-trust" aria-label="Reassurance securite">
                        <div class="login-story-trust-item">
                            <i class="fas fa-lock"></i>
                            <span>Acces reserve aux equipes autorisees</span>
                        </div>

                        <div class="login-story-trust-item">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Tracabilite des connexions et des sessions</span>
                        </div>
                    </div>
                </div>
            </aside>

            <section class="login-card" aria-label="Formulaire de connexion">
                <div class="login-card-header">
                    <span class="login-panel-kicker">Connexion securisee</span>
                    <h2 class="login-panel-title">Bienvenue sur MEDISYS Pro</h2>
                    <p class="login-panel-copy">
                        Saisissez vos identifiants professionnels pour retrouver votre tableau de bord medical.
                    </p>
                </div>

                @if($feedbackMessage)
                    <div class="alert alert-{{ $feedbackTone }}" role="alert" aria-live="polite">
                        <i class="fas {{ $feedbackIcon }} me-2"></i>
                        <span class="alert-body">
                            <span class="alert-title">{{ $feedbackTitle }}</span>
                            <span class="alert-copy">{{ $feedbackMessage }}</span>
                        </span>
                        <button type="button" class="alert-close" data-dismiss-alert aria-label="Fermer le message">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="login-form" id="loginForm" novalidate data-security-ready="2fa timeout suspicious-login">
                    @csrf

                    <div class="field">
                        <label for="email" class="field-label">Adresse e-mail</label>
                        <div class="field-shell @error('email') has-error @enderror">
                            <span class="field-icon"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="email" name="email" class="@error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@medisys.pro" required autofocus autocomplete="username" aria-invalid="@error('email') true @else false @enderror">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password" class="field-label">Mot de passe</label>
                        <div class="field-shell @error('password') has-error @enderror">
                            <span class="field-icon"><i class="fas fa-key"></i></span>
                            <input type="password" id="password" name="password" class="@error('password') is-invalid @enderror" placeholder="Saisissez votre mot de passe" required autocomplete="current-password" aria-invalid="@error('password') true @else false @enderror">
                            <button class="field-toggle" type="button" id="togglePassword" aria-label="Afficher ou masquer le mot de passe">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-actions">
                        <label class="remember-toggle form-check" for="remember">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))>
                            <span>Se souvenir de moi</span>
                        </label>

                        <a href="{{ route('password.request') }}" class="forgot-password">{{ __('messages.auth.forgot_password') }}</a>
                    </div>

                    <button type="submit" class="login-submit" id="loginSubmit">
                        <span class="submit-spinner spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <i class="fas fa-right-to-bracket submit-icon"></i>
                        <span class="submit-label">Se connecter</span>
                    </button>
                </form>

            </section>

            <footer class="login-footer">
                <strong>MEDISYS Pro | Plateforme medicale premium pour les equipes de soin</strong>
                <span>&copy; {{ date('Y') }} Plateforme reservee aux environnements autorises</span>
            </footer>
        </section>
    </main>

    <script>
        const togglePasswordButton = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const loginForm = document.getElementById('loginForm');
        const loginSubmit = document.getElementById('loginSubmit');

        if (togglePasswordButton && passwordInput) {
            togglePasswordButton.addEventListener('click', function() {
                const icon = this.querySelector('i');
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);
            });
        }

        if (loginForm && loginSubmit) {
            loginForm.addEventListener('submit', function (event) {
                if (typeof loginForm.checkValidity === 'function' && !loginForm.checkValidity()) {
                    event.preventDefault();
                    if (typeof loginForm.reportValidity === 'function') {
                        loginForm.reportValidity();
                    }
                    return;
                }

                loginSubmit.classList.add('is-loading');
                loginSubmit.setAttribute('aria-busy', 'true');
                loginSubmit.disabled = true;
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
