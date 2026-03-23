<style>
    .med-form-page {
        --med-form-primary: #2c7be5;
        --med-form-primary-strong: #1f5ea8;
        --med-form-accent: #0ea5e9;
        --med-form-success: #0f9f77;
        --med-form-warning: #d97706;
        --med-form-surface: linear-gradient(180deg, #f4f8fd 0%, #eef5fb 100%);
        --med-form-card: #ffffff;
        --med-form-border: #d8e4f2;
        --med-form-text: #15314d;
        --med-form-muted: #5f7896;
        width: 100%;
        max-width: none;
        padding: 10px 8px 92px;
    }

    .med-form-shell {
        display: grid;
        gap: 16px;
    }

    .med-form-breadcrumbs {
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0 0 12px;
        padding: 0;
        list-style: none;
        font-size: .8rem;
        color: var(--med-form-muted);
        font-weight: 700;
    }

    .med-form-breadcrumbs a {
        color: inherit;
        text-decoration: none;
    }

    .med-form-breadcrumbs a:hover {
        color: var(--med-form-primary);
    }

    .med-form-breadcrumb-separator {
        color: #98abc0;
    }

    .med-form-title {
        margin: 0;
        font-size: clamp(1.45rem, 2.5vw, 2.1rem);
        font-weight: 800;
        line-height: 1.06;
        letter-spacing: -0.04em;
        color: var(--med-form-text);
    }

    .med-form-title-subtitle {
        margin: 10px 0 0;
        max-width: 72ch;
        color: var(--med-form-muted);
        font-size: .97rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .med-form-hero {
        position: relative;
        overflow: hidden;
        display: grid;
        gap: 16px;
        padding: 18px;
        border-radius: 22px;
        border: 1px solid var(--med-form-border);
        background:
            radial-gradient(circle at top right, rgba(44, 123, 229, 0.16) 0%, rgba(44, 123, 229, 0) 32%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%),
            var(--med-form-surface);
        box-shadow: 0 24px 48px -38px rgba(20, 52, 84, 0.42);
    }

    .med-form-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .med-form-hero > * {
        position: relative;
        z-index: 1;
    }

    .med-form-hero-head {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.95fr);
        gap: 16px;
        align-items: start;
    }

    .med-form-hero-main {
        min-width: 0;
    }

    .med-form-title-row {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .med-form-title-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
        font-size: 1.3rem;
        background: linear-gradient(135deg, var(--med-form-primary) 0%, var(--med-form-primary-strong) 100%);
        box-shadow: 0 16px 26px -18px rgba(44, 123, 229, 0.58);
    }

    .med-form-title-block {
        min-width: 0;
    }

    .med-form-hero-tools {
        display: grid;
        gap: 12px;
    }

    .med-form-panel {
        border: 1px solid rgba(208, 221, 237, 0.96);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.78);
        padding: 14px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.68);
    }

    .med-form-panel-label {
        display: block;
        margin-bottom: 10px;
        color: var(--med-form-muted);
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 800;
    }

    .med-form-panel-copy {
        margin: 0;
        color: var(--med-form-muted);
        font-size: .88rem;
        line-height: 1.55;
    }

    .med-form-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .med-form-btn {
        min-height: 44px;
        border-radius: 14px;
        border: 1px solid transparent;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: .92rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
        white-space: nowrap;
    }

    .med-form-btn:hover,
    .med-form-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .med-form-btn-soft {
        border-color: #cfdef0;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%);
        color: #385674;
        box-shadow: 0 12px 20px -24px rgba(15, 23, 42, 0.42);
    }

    .med-form-btn-soft:hover,
    .med-form-btn-soft:focus {
        color: #1f6fa3;
        border-color: rgba(44, 123, 229, 0.3);
        background: linear-gradient(180deg, #ffffff 0%, #edf5fb 100%);
    }

    .med-form-btn-primary {
        background: linear-gradient(135deg, var(--med-form-primary) 0%, var(--med-form-primary-strong) 100%);
        color: #fff;
        box-shadow: 0 18px 28px -22px rgba(44, 123, 229, 0.55);
    }

    .med-form-btn-primary:hover,
    .med-form-btn-primary:focus {
        color: #fff;
    }

    .med-form-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(44, 123, 229, 0.1);
        color: var(--med-form-primary);
    }

    .med-form-btn-primary .med-form-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: inherit;
    }

    .med-form-kpis {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-top: 14px;
    }

    .med-form-kpi {
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid rgba(206, 221, 238, 0.96);
        background: rgba(255, 255, 255, 0.72);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.62);
    }

    .med-form-kpi-label {
        display: block;
        margin-bottom: 6px;
        color: var(--med-form-muted);
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 800;
    }

    .med-form-kpi-value {
        display: block;
        color: var(--med-form-text);
        font-size: 1.2rem;
        font-weight: 900;
        line-height: 1;
    }

    .med-form-kpi-meta {
        display: block;
        margin-top: 5px;
        color: #7290b0;
        font-size: .82rem;
        font-weight: 600;
    }

    .med-form-layout {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .med-form-card {
        background: var(--med-form-card);
        border: 1px solid var(--med-form-border);
        border-radius: 22px;
        box-shadow: 0 22px 34px -34px rgba(15, 23, 42, 0.44);
    }

    .med-form-side {
        overflow: hidden;
        position: sticky;
        top: 92px;
        padding: 18px;
    }

    .med-form-side::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 128px;
        pointer-events: none;
        background:
            radial-gradient(circle at top right, rgba(44, 123, 229, 0.18) 0%, rgba(44, 123, 229, 0) 44%),
            linear-gradient(180deg, rgba(244, 249, 255, 0.92) 0%, rgba(244, 249, 255, 0) 100%);
    }

    .med-form-side > * {
        position: relative;
        z-index: 1;
    }

    .med-form-side-head {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .med-form-avatar {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
        font-size: 1.05rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--med-form-primary) 0%, var(--med-form-accent) 100%);
        box-shadow: 0 18px 28px -20px rgba(44, 123, 229, 0.56);
    }

    .med-form-side-copy {
        min-width: 0;
    }

    .med-form-side-name {
        margin: 0;
        font-size: 1.22rem;
        line-height: 1.08;
        font-weight: 800;
        color: var(--med-form-text);
    }

    .med-form-side-subtitle {
        margin: 5px 0 0;
        color: var(--med-form-muted);
        font-size: .88rem;
        font-weight: 700;
    }

    .med-form-side-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .med-form-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 32px;
        border-radius: 999px;
        border: 1px solid #d4e2f2;
        background: #f6fafe;
        color: #1d4f91;
        padding: 0 12px;
        font-size: .77rem;
        font-weight: 800;
    }

    .med-form-side-title {
        margin: 0 0 12px;
        font-size: .92rem;
        font-weight: 800;
        color: var(--med-form-text);
    }

    .med-form-side-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .med-form-side-list li {
        border: 1px solid #e2ebf6;
        border-radius: 16px;
        background: #fbfdff;
        padding: 12px;
    }

    .med-form-side-list small {
        color: var(--med-form-muted);
        display: block;
        font-size: .68rem;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 800;
    }

    .med-form-side-list strong {
        font-size: .92rem;
        line-height: 1.45;
        color: var(--med-form-text);
    }

    .med-form-main {
        overflow: hidden;
    }

    .med-form-main-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 18px 18px 0;
        background: transparent;
        border-bottom: 0;
    }

    .med-form-main-title {
        margin: 0;
        font-size: 1.16rem;
        font-weight: 800;
        color: var(--med-form-text);
    }

    .med-form-badge {
        background: #eef6ff;
        border: 1px solid #d4e2f2;
        color: var(--med-form-primary-strong);
        border-radius: 999px;
        padding: 5px 12px;
        font-size: .76rem;
        font-weight: 800;
    }

    .med-form-body {
        padding: 18px;
        display: grid;
        gap: 16px;
    }

    .med-form-section {
        border: 1px solid #dfe9f5;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 16px 28px -30px rgba(15, 23, 42, 0.32);
        overflow: hidden;
    }

    .med-form-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid #e6eef8;
        background: linear-gradient(180deg, #f7fbff 0%, #eff6fd 100%);
    }

    .med-form-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .med-form-section-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(44, 123, 229, 0.1);
        color: var(--med-form-primary);
    }

    .med-form-section-head h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: var(--med-form-text);
    }

    .med-form-section-help {
        margin: 3px 0 0;
        color: var(--med-form-muted);
        font-size: .84rem;
        line-height: 1.45;
    }

    .med-form-section-tag {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 10px;
        border-radius: 999px;
        background: #eef6ff;
        color: var(--med-form-primary-strong);
        font-size: .75rem;
        font-weight: 800;
    }

    .med-form-section-body {
        padding: 16px;
    }

    .med-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .med-field {
        display: flex;
        flex-direction: column;
    }

    .med-field.full {
        grid-column: 1 / -1;
    }

    .med-field label {
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--med-form-muted);
        margin-bottom: 8px;
    }

    .med-field label .required {
        color: #dc2626;
    }

    .med-field .form-control,
    .med-field .form-select,
    .med-field textarea {
        min-height: 52px;
        border-radius: 14px;
        border: 1px solid #d4e1ee;
        background: #fff;
        color: var(--med-form-text);
        padding: 13px 14px;
        font-size: .95rem;
        font-weight: 600;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.78), 0 10px 24px -28px rgba(15, 23, 42, 0.28);
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease, transform .2s ease;
    }

    .med-field .form-control:focus,
    .med-field .form-select:focus,
    .med-field textarea:focus {
        border-color: rgba(44, 123, 229, 0.46);
        box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.12), 0 14px 28px -26px rgba(31, 111, 163, 0.34);
        transform: translateY(-1px);
    }

    .med-field textarea {
        min-height: 132px;
        resize: vertical;
    }

    .med-field-hint {
        margin-top: 8px;
        color: var(--med-form-muted);
        font-size: .83rem;
        line-height: 1.45;
    }

    .med-field .invalid-feedback,
    .med-form-checks .invalid-feedback {
        margin-top: 8px;
        font-size: .84rem;
        font-weight: 700;
        display: block;
    }

    .med-form-checks {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .med-check-card {
        position: relative;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        min-height: 88px;
        padding: 14px;
        border: 1px solid #dfe9f5;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        cursor: pointer;
    }

    .med-check-card .form-check-input {
        margin-top: 2px;
        flex-shrink: 0;
    }

    .med-check-body {
        display: grid;
        gap: 4px;
        color: var(--med-form-text);
    }

    .med-check-body strong {
        font-size: .94rem;
    }

    .med-check-body span {
        color: var(--med-form-muted);
        font-size: .84rem;
        line-height: 1.45;
        font-weight: 600;
    }

    .med-form-footer {
        position: sticky;
        bottom: 0;
        z-index: 2;
        padding: 14px 18px 18px;
        border-top: 1px solid #e6eef8;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        background: linear-gradient(180deg, rgba(255,255,255,.82) 0%, rgba(255,255,255,.96) 100%);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .med-form-mobile-actions {
        display: none;
    }

    body.dark-mode .med-form-page,
    body.theme-dark .med-form-page {
        --med-form-surface: linear-gradient(180deg, #152233 0%, #122032 100%);
        --med-form-card: #162332;
        --med-form-border: #2f4358;
        --med-form-text: #e6edf6;
        --med-form-muted: #9eb1c7;
    }

    body.dark-mode .med-form-side-list li,
    body.dark-mode .med-form-kpi,
    body.dark-mode .med-form-panel,
    body.dark-mode .med-check-card,
    body.theme-dark .med-form-side-list li,
    body.theme-dark .med-form-kpi,
    body.theme-dark .med-form-panel,
    body.theme-dark .med-check-card {
        background: rgba(17, 34, 54, 0.88);
        border-color: #35506a;
    }

    body.dark-mode .med-form-btn-soft,
    body.theme-dark .med-form-btn-soft {
        border-color: #365b7d;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
        color: #d2e6fb;
    }

    body.dark-mode .med-form-btn-soft:hover,
    body.dark-mode .med-form-btn-soft:focus,
    body.theme-dark .med-form-btn-soft:hover,
    body.theme-dark .med-form-btn-soft:focus {
        border-color: #4c7094;
        background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
        color: #ffffff;
    }

    body.dark-mode .med-form-btn-icon,
    body.theme-dark .med-form-btn-icon,
    body.dark-mode .med-form-section-icon,
    body.theme-dark .med-form-section-icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    body.dark-mode .med-form-section,
    body.theme-dark .med-form-section {
        background: #0f1a28;
        border-color: #2f4358;
    }

    body.dark-mode .med-form-section-head,
    body.theme-dark .med-form-section-head {
        background: #16273d;
        border-color: #294055;
    }

    body.dark-mode .med-form-main-title,
    body.dark-mode .med-form-side-title,
    body.dark-mode .med-form-section-head h3,
    body.dark-mode .med-form-side-name,
    body.theme-dark .med-form-main-title,
    body.theme-dark .med-form-side-title,
    body.theme-dark .med-form-section-head h3,
    body.theme-dark .med-form-side-name {
        color: #eef5ff;
    }

    body.dark-mode .med-field .form-control,
    body.dark-mode .med-field .form-select,
    body.dark-mode .med-field textarea,
    body.theme-dark .med-field .form-control,
    body.theme-dark .med-field .form-select,
    body.theme-dark .med-field textarea {
        background: #13263f;
        border-color: #355985;
        color: #deebf9;
    }

    body.dark-mode .med-form-footer,
    body.theme-dark .med-form-footer {
        background: linear-gradient(180deg, rgba(18, 35, 52, 0.84) 0%, rgba(18, 35, 52, 0.98) 100%);
        border-color: #294055;
    }

    @media (max-width: 1199.98px) {
        .med-form-hero-head {
            grid-template-columns: 1fr;
        }

        .med-form-layout {
            grid-template-columns: 300px minmax(0, 1fr);
        }
    }

    @media (max-width: 991.98px) {
        .med-form-layout {
            grid-template-columns: 1fr;
        }

        .med-form-side {
            position: static;
        }

        .med-form-grid,
        .med-form-kpis,
        .med-form-side-list,
        .med-form-checks {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .med-form-page {
            padding: 6px 0 88px;
        }

        .med-form-actions,
        .med-form-footer {
            display: none;
        }

        .med-form-mobile-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            position: fixed;
            left: 8px;
            right: 8px;
            bottom: calc(10px + env(safe-area-inset-bottom));
            z-index: 1050;
            background: var(--med-form-card);
            border: 1px solid var(--med-form-border);
            border-radius: 18px;
            padding: 8px;
            box-shadow: 0 16px 24px -20px rgba(0, 0, 0, .46);
        }

        .med-form-mobile-actions .med-form-btn {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .med-form-hero {
            padding: 14px;
            border-radius: 18px;
        }

        .med-form-title-row {
            align-items: flex-start;
        }

        .med-form-title-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .med-form-main-head,
        .med-form-body {
            padding-left: 14px;
            padding-right: 14px;
        }

        .med-form-mobile-actions {
            grid-template-columns: 1fr;
        }
    }
</style>