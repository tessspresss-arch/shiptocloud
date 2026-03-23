@once
    @push('styles')
        <style>
            .medecin-workspace {
                --med-bg: linear-gradient(180deg, #f4f9ff 0%, #eef5ff 48%, #f7fafd 100%);
                --med-surface: rgba(255, 255, 255, 0.9);
                --med-surface-solid: #ffffff;
                --med-border: #d8e5f0;
                --med-title: #0f2b4d;
                --med-text: #19324c;
                --med-muted: #64809b;
                --med-primary: #1f74dd;
                --med-primary-strong: #145fb8;
                --med-success: #159c71;
                --med-warning: #d68617;
                --med-danger: #cf4d5f;
                --med-shadow: 0 24px 36px -34px rgba(18, 62, 112, 0.45);
                background: var(--med-bg);
                border-radius: 24px;
                border: 1px solid #dfeaf7;
                box-shadow: var(--med-shadow);
                padding: clamp(.85rem, 1.4vw, 1.15rem);
                position: relative;
                overflow: hidden;
            }

            .medecin-workspace::before,
            .medecin-workspace::after {
                content: "";
                position: absolute;
                pointer-events: none;
                border-radius: 999px;
            }

            .medecin-workspace::before {
                width: 380px;
                height: 380px;
                top: -210px;
                right: -120px;
                background: radial-gradient(circle, rgba(52, 143, 239, .16) 0%, transparent 72%);
            }

            .medecin-workspace::after {
                width: 320px;
                height: 320px;
                left: -110px;
                bottom: -180px;
                background: radial-gradient(circle, rgba(38, 173, 137, .12) 0%, transparent 72%);
            }

            .medecin-workspace > * {
                position: relative;
                z-index: 1;
            }

            .medecin-hero,
            .medecin-card,
            .medecin-kpi,
            .medecin-side-card {
                border: 1px solid var(--med-border);
                border-radius: 22px;
                background: var(--med-surface);
                box-shadow: var(--med-shadow);
                backdrop-filter: blur(12px);
                overflow: hidden;
                position: relative;
            }

            .medecin-hero {
                padding: 1.3rem;
                background:
                    radial-gradient(circle at top right, rgba(31, 116, 221, .16) 0%, transparent 32%),
                    radial-gradient(circle at left top, rgba(21, 156, 113, .08) 0%, transparent 28%),
                    var(--med-bg);
            }

            .medecin-hero-grid,
            .medecin-detail-grid,
            .medecin-form-grid,
            .medecin-kpi-grid,
            .medecin-show-grid {
                display: grid;
                gap: 1rem;
            }

            .medecin-hero-grid,
            .medecin-show-grid,
            .medecin-form-grid {
                grid-template-columns: minmax(0, 1.5fr) minmax(280px, .86fr);
                align-items: start;
            }

            .medecin-eyebrow,
            .medecin-kicker,
            .medecin-side-label,
            .medecin-section-kicker {
                display: inline-flex;
                align-items: center;
                gap: .45rem;
                min-height: 30px;
                padding: 0 .8rem;
                border-radius: 999px;
                background: rgba(255, 255, 255, .78);
                border: 1px solid rgba(31, 116, 221, .14);
                color: var(--med-primary-strong);
                font-size: .75rem;
                font-weight: 800;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .medecin-title-row {
                display: flex;
                align-items: center;
                gap: .95rem;
                flex-wrap: wrap;
                margin-top: .9rem;
            }

            .medecin-title-icon,
            .medecin-avatar-shell {
                width: 62px;
                height: 62px;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                color: #fff;
                background: linear-gradient(135deg, var(--med-primary) 0%, var(--med-primary-strong) 100%);
                box-shadow: 0 18px 28px -20px rgba(31, 116, 221, .55);
                font-size: 1.4rem;
                overflow: hidden;
            }

            .medecin-avatar-shell img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .medecin-title {
                margin: 0;
                color: var(--med-title);
                font-size: clamp(1.7rem, 2.5vw, 2.35rem);
                line-height: 1.02;
                font-weight: 800;
                letter-spacing: -.04em;
            }

            .medecin-subtitle,
            .medecin-copy,
            .medecin-side-copy,
            .medecin-field-help,
            .medecin-empty-copy,
            .medecin-meta-copy {
                margin: 0;
                color: var(--med-muted);
                font-size: .95rem;
                line-height: 1.6;
                font-weight: 600;
            }

            .medecin-chip-row,
            .medecin-hero-actions,
            .medecin-summary-list,
            .medecin-doc-list,
            .medecin-inline-list {
                display: flex;
                flex-wrap: wrap;
                gap: .75rem;
                align-items: center;
            }

            .medecin-hero-aside {
                display: flex;
                justify-content: flex-end;
                align-items: flex-start;
            }

            .medecin-chip-row {
                margin-top: 1rem;
            }

            .medecin-chip,
            .medecin-status-pill,
            .medecin-inline-pill,
            .medecin-summary-pill {
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                min-height: 36px;
                padding: 0 .95rem;
                border-radius: 999px;
                border: 1px solid #d7e4ef;
                background: #f5faff;
                color: #4f6f8d;
                font-size: .86rem;
                font-weight: 700;
                white-space: nowrap;
            }

            .medecin-chip i,
            .medecin-inline-pill i,
            .medecin-summary-pill i {
                color: var(--med-primary);
            }

            .medecin-status-pill.status-actif {
                background: rgba(21, 156, 113, .12);
                border-color: rgba(21, 156, 113, .18);
                color: #107457;
            }

            .medecin-status-pill.status-en_conge {
                background: rgba(214, 134, 23, .12);
                border-color: rgba(214, 134, 23, .18);
                color: #9a640e;
            }

            .medecin-status-pill.status-inactif,
            .medecin-status-pill.status-retraite {
                background: rgba(98, 118, 143, .12);
                border-color: rgba(98, 118, 143, .18);
                color: #546678;
            }

            .medecin-action-box,
            .medecin-side-card,
            .medecin-card-body,
            .medecin-card-head,
            .medecin-card-foot {
                position: relative;
                z-index: 1;
            }

            .medecin-action-box,
            .medecin-side-card {
                padding: 1rem;
            }

            .medecin-action-box {
                margin-top: 1.1rem;
                border-radius: 20px;
                border: 1px solid #d6e4f2;
                background: rgba(255, 255, 255, .8);
                box-shadow: 0 16px 24px -28px rgba(15, 40, 65, .4);
            }

            .medecin-btn,
            .medecin-icon-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: .55rem;
                text-decoration: none;
                transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
            }

            .medecin-btn {
                min-height: 48px;
                padding: 0 1rem;
                border-radius: 16px;
                border: 1px solid transparent;
                font-size: .92rem;
                font-weight: 800;
            }

            .medecin-btn:hover,
            .medecin-btn:focus,
            .medecin-icon-btn:hover,
            .medecin-icon-btn:focus {
                transform: translateY(-1px);
                text-decoration: none;
            }

            .medecin-btn.primary {
                background: linear-gradient(135deg, var(--med-primary) 0%, var(--med-primary-strong) 100%);
                color: #fff;
                box-shadow: 0 20px 28px -24px rgba(31, 116, 221, .84);
            }

            .medecin-btn.success {
                background: linear-gradient(135deg, var(--med-success) 0%, #117454 100%);
                color: #fff;
                box-shadow: 0 20px 28px -24px rgba(21, 156, 113, .84);
            }

            .medecin-btn.secondary {
                border-color: #cfdeec;
                background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
                color: #4a6883;
            }

            .medecin-btn.danger-soft {
                border-color: rgba(207, 77, 95, .2);
                background: rgba(207, 77, 95, .08);
                color: var(--med-danger);
            }

            .medecin-card-head,
            .medecin-card-foot {
                padding: 1rem 1.2rem;
                border-bottom: 1px solid var(--med-border);
                background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            }

            .medecin-card-head {
                display: flex;
                justify-content: space-between;
                align-items: start;
                gap: 1rem;
            }

            .medecin-card-foot {
                border-bottom: 0;
                border-top: 1px solid var(--med-border);
            }

            .medecin-card-head h5,
            .medecin-card-head h6,
            .medecin-section-title,
            .medecin-side-title,
            .medecin-stat-value,
            .medecin-detail-value {
                margin: 0;
                color: var(--med-title);
                font-weight: 800;
            }

            .medecin-card-head p,
            .medecin-side-note,
            .medecin-stat-label,
            .medecin-detail-label {
                margin: 0;
                color: var(--med-muted);
                font-weight: 600;
            }

            .medecin-card-body,
            .medecin-side-body {
                padding: 1.2rem;
            }

            .medecin-form-section + .medecin-form-section,
            .medecin-block + .medecin-block {
                margin-top: 1.3rem;
                padding-top: 1.2rem;
                border-top: 1px dashed #dbe8f4;
            }

            .medecin-section-title {
                display: inline-flex;
                align-items: center;
                gap: .55rem;
                margin-bottom: .9rem;
                font-size: .98rem;
            }

            .medecin-section-title::before {
                content: "";
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: linear-gradient(180deg, var(--med-primary) 0%, #20a3dc 100%);
                box-shadow: 0 0 0 4px rgba(32, 126, 227, .12);
            }

            .medecin-workspace .form-control,
            .medecin-workspace .form-select,
            .medecin-workspace .form-control:disabled,
            .medecin-workspace .form-select:disabled {
                min-height: 46px;
                border-radius: 12px;
                border-color: #cfdded;
                background: #fbfdff;
                color: var(--med-text);
                font-weight: 500;
                transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
            }

            .medecin-workspace textarea.form-control {
                min-height: auto;
            }

            .medecin-workspace .form-control:focus,
            .medecin-workspace .form-select:focus {
                border-color: #67a6eb;
                box-shadow: 0 0 0 .2rem rgba(29, 111, 220, .16);
                transform: translateY(-.5px);
            }

            .medecin-workspace .form-label {
                color: #203b5e;
                font-size: .9rem;
                font-weight: 700;
                margin-bottom: .4rem;
            }

            .medecin-workspace .form-text {
                color: #6a87a3 !important;
                font-size: .82rem;
            }

            .medecin-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .medecin-kpi {
                padding: 1rem 1.1rem;
                display: grid;
                gap: .45rem;
            }

            .medecin-stat-value {
                font-size: clamp(1.9rem, 2.2vw, 2.35rem);
                line-height: 1;
                letter-spacing: -.04em;
            }

            .medecin-detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .medecin-detail-item {
                padding: .95rem 1rem;
                border-radius: 16px;
                border: 1px solid #dbe8f4;
                background: linear-gradient(180deg, #fbfdff 0%, #f5f9fd 100%);
            }

            .medecin-detail-item.full {
                grid-column: 1 / -1;
            }

            .medecin-detail-value {
                margin-top: .3rem;
                font-size: .96rem;
                line-height: 1.55;
                font-weight: 700;
                word-break: break-word;
            }

            .medecin-side-card {
                display: grid;
                gap: .95rem;
                background: linear-gradient(180deg, rgba(244, 249, 255, .96) 0%, rgba(255, 255, 255, .96) 100%);
            }

            .medecin-side-metric {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: .75rem;
                padding: .85rem .95rem;
                border-radius: 16px;
                background: #f6fafe;
                border: 1px solid #dbe8f4;
            }

            .medecin-side-metric span {
                color: #59748d;
                font-weight: 600;
            }

            .medecin-side-metric strong {
                color: var(--med-title);
                font-weight: 800;
            }

            .medecin-list {
                display: grid;
                gap: .7rem;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .medecin-list li {
                display: flex;
                gap: .7rem;
                align-items: start;
                padding: .8rem .9rem;
                border-radius: 14px;
                border: 1px solid #dbe8f4;
                background: linear-gradient(180deg, #f8fbff 0%, #f2f8ff 100%);
                color: #2e5078;
                font-size: .92rem;
                font-weight: 600;
            }

            .medecin-list i {
                color: var(--med-primary);
                margin-top: .2rem;
            }

            .medecin-file-preview {
                min-height: 90px;
                border: 1px dashed #a8c2e4;
                background: linear-gradient(180deg, #f8fbff 0%, #f0f7ff 100%);
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: .55rem;
                color: #4a678f;
                font-size: .9rem;
                overflow: hidden;
            }

            .medecin-file-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .medecin-file-preview.signature img {
                object-fit: contain;
                padding: .5rem;
                background: #fff;
            }

            .medecin-empty {
                padding: 1rem;
                border-radius: 16px;
                border: 1px dashed #c9d9ea;
                background: rgba(255, 255, 255, .66);
            }

            .medecin-divider {
                height: 1px;
                background: linear-gradient(90deg, transparent 0%, rgba(124, 152, 184, .35) 18%, rgba(124, 152, 184, .35) 82%, transparent 100%);
                margin: .25rem 0;
            }

            html.dark body .medecin-workspace,
            body.dark-mode .medecin-workspace {
                --med-bg: linear-gradient(180deg, #0d1a29 0%, #0a1522 52%, #09131d 100%);
                --med-surface: rgba(14, 29, 46, .92);
                --med-surface-solid: #102136;
                --med-border: #2d4966;
                --med-title: #e3efff;
                --med-text: #dceaff;
                --med-muted: #9ab2cf;
                border-color: #2d4765;
                box-shadow: 0 26px 38px -30px rgba(0, 0, 0, .62);
            }

            html.dark body .medecin-eyebrow,
            html.dark body .medecin-kicker,
            html.dark body .medecin-side-label,
            html.dark body .medecin-section-kicker,
            body.dark-mode .medecin-eyebrow,
            body.dark-mode .medecin-kicker,
            body.dark-mode .medecin-side-label,
            body.dark-mode .medecin-section-kicker {
                background: rgba(15, 31, 50, .82);
                border-color: #365579;
                color: #9fd1ff;
            }

            html.dark body .medecin-card,
            html.dark body .medecin-kpi,
            html.dark body .medecin-side-card,
            html.dark body .medecin-hero,
            html.dark body .medecin-card-head,
            html.dark body .medecin-card-foot,
            body.dark-mode .medecin-card,
            body.dark-mode .medecin-kpi,
            body.dark-mode .medecin-side-card,
            body.dark-mode .medecin-hero,
            body.dark-mode .medecin-card-head,
            body.dark-mode .medecin-card-foot {
                background: var(--med-surface-solid);
                border-color: var(--med-border);
            }

            html.dark body .medecin-chip,
            html.dark body .medecin-inline-pill,
            html.dark body .medecin-summary-pill,
            html.dark body .medecin-side-metric,
            html.dark body .medecin-detail-item,
            html.dark body .medecin-list li,
            html.dark body .medecin-action-box,
            html.dark body .medecin-empty,
            body.dark-mode .medecin-chip,
            body.dark-mode .medecin-inline-pill,
            body.dark-mode .medecin-summary-pill,
            body.dark-mode .medecin-side-metric,
            body.dark-mode .medecin-detail-item,
            body.dark-mode .medecin-list li,
            body.dark-mode .medecin-action-box,
            body.dark-mode .medecin-empty {
                background: #13283e;
                border-color: #31506f;
                color: #bdd2ea;
            }

            html.dark body .medecin-workspace .form-control,
            html.dark body .medecin-workspace .form-select,
            body.dark-mode .medecin-workspace .form-control,
            body.dark-mode .medecin-workspace .form-select {
                background: #0d1b2b;
                border-color: #3a5a7e;
                color: #e5efff;
            }

            html.dark body .medecin-workspace .form-control::placeholder,
            body.dark-mode .medecin-workspace .form-control::placeholder {
                color: #98afcb;
            }

            html.dark body .medecin-file-preview,
            body.dark-mode .medecin-file-preview {
                background: linear-gradient(180deg, #11263d 0%, #0f2134 100%);
                border-color: #4a6f99;
                color: #b8d1ee;
            }

            html.dark body .medecin-file-preview.signature img,
            body.dark-mode .medecin-file-preview.signature img {
                background: #0c1624;
            }

            html.dark body .medecin-divider,
            body.dark-mode .medecin-divider {
                background: linear-gradient(90deg, transparent 0%, rgba(103, 144, 189, .38) 18%, rgba(103, 144, 189, .38) 82%, transparent 100%);
            }

            @media (max-width: 1199px) {
                .medecin-hero-grid,
                .medecin-show-grid,
                .medecin-form-grid,
                .medecin-kpi-grid,
                .medecin-detail-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 576px) {
                .medecin-workspace {
                    border-radius: 16px;
                    padding: .65rem;
                }

                .medecin-hero,
                .medecin-card-head,
                .medecin-card-body,
                .medecin-card-foot,
                .medecin-side-card {
                    padding-left: .95rem;
                    padding-right: .95rem;
                }

                .medecin-title {
                    font-size: 1.55rem;
                }

                .medecin-hero-actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .medecin-btn {
                    width: 100%;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('input[type="file"][data-preview-target]').forEach(function (input) {
                    input.addEventListener('change', function (event) {
                        const previewId = event.target.getAttribute('data-preview-target');
                        const preview = document.getElementById(previewId);
                        const file = event.target.files && event.target.files[0];

                        if (!preview || !file) {
                            return;
                        }

                        const reader = new FileReader();

                        reader.onload = function (loadEvent) {
                            preview.innerHTML = '<img src="' + loadEvent.target.result + '" alt="Aperçu">';
                        };

                        reader.readAsDataURL(file);
                    });
                });
            });
        </script>
    @endpush
@endonce
