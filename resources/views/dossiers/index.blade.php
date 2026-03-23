@extends('layouts.app')

@section('title', 'Gestion des Dossiers Medicaux')
@section('topbar_subtitle', 'Pilotage des dossiers, recherche rapide et suivi patient dans une interface premium unifiee.')

@push('styles')
<style>
    :root {
        --dossier-bg: linear-gradient(180deg, #f4f9fd 0%, #edf5fb 100%);
        --dossier-surface: rgba(255, 255, 255, 0.88);
        --dossier-border: #d8e5ef;
        --dossier-text: #17324c;
        --dossier-muted: #68829a;
        --dossier-primary: #1b79c9;
        --dossier-primary-strong: #145d98;
        --dossier-success: #16956f;
        --dossier-warning: #c98212;
        --dossier-danger: #d74d5d;
        --dossier-shadow: 0 26px 48px -36px rgba(15, 40, 65, 0.38);
    }

    .dossiers-page {
        width: 100%;
        max-width: none;
        padding: 8px 8px 96px;
    }

    .dossiers-shell {
        display: grid;
        gap: 18px;
    }

    .dossiers-hero,
    .dossiers-kpi,
    .dossiers-filter-card,
    .dossiers-table-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--dossier-border);
        border-radius: 24px;
        box-shadow: var(--dossier-shadow);
    }

    .dossiers-hero {
        padding: 22px;
        background:
            radial-gradient(circle at top right, rgba(27, 121, 201, 0.16) 0%, rgba(27, 121, 201, 0) 32%),
            radial-gradient(circle at left top, rgba(22, 149, 111, 0.12) 0%, rgba(22, 149, 111, 0) 36%),
            var(--dossier-bg);
    }

    .dossiers-kpi,
    .dossiers-filter-card,
    .dossiers-table-card {
        background: var(--dossier-surface);
        backdrop-filter: blur(10px);
    }

    .dossiers-hero::before,
    .dossiers-kpi::before,
    .dossiers-filter-card::before,
    .dossiers-table-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .dossiers-hero > *,
    .dossiers-kpi > *,
    .dossiers-filter-card > *,
    .dossiers-table-card > * {
        position: relative;
        z-index: 1;
    }

    .dossiers-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
        align-items: stretch;
    }

    .dossiers-hero-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .dossiers-eyebrow,
    .dossiers-kicker {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(27, 121, 201, 0.16);
        background: rgba(255, 255, 255, 0.72);
        color: var(--dossier-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dossiers-title-row {
        display: flex;
        gap: 14px;
        align-items: center;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    .dossiers-title-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.4rem;
        background: linear-gradient(135deg, var(--dossier-primary) 0%, var(--dossier-primary-strong) 100%);
        box-shadow: 0 18px 28px -20px rgba(27, 121, 201, 0.58);
        flex-shrink: 0;
    }

    .dossiers-title {
        margin: 0;
        color: var(--dossier-text);
        font-size: clamp(1.6rem, 2.6vw, 2.3rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .dossiers-subtitle,
    .dossiers-copy,
    .dossiers-meta-copy,
    .dossiers-empty-copy,
    .dossiers-pagination-copy {
        margin: 0;
        color: var(--dossier-muted);
        font-size: .96rem;
        line-height: 1.64;
        font-weight: 600;
    }

    .dossiers-chip-row,
    .dossiers-hero-actions,
    .dossiers-meta-row,
    .dossiers-actions,
    .dossiers-patient-meta {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .dossiers-chip-row {
        margin-top: 16px;
    }

    .dossiers-hero-actions {
        margin-top: 0;
        justify-content: flex-end;
        flex: 0 0 auto;
    }

    .dossiers-badge,
    .dossiers-chip,
    .dossiers-pill,
    .dossiers-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        white-space: nowrap;
        font-weight: 700;
    }

    .dossiers-badge {
        background: linear-gradient(135deg, var(--dossier-primary) 0%, #0e4f8f 100%);
        color: #fff;
        box-shadow: 0 16px 24px -22px rgba(27, 121, 201, 0.92);
    }

    .dossiers-chip,
    .dossiers-pill {
        border: 1px solid #d7e4ef;
        background: #f6fafe;
        color: #57728c;
        font-size: .84rem;
    }

    .dossiers-chip i,
    .dossiers-pill i {
        color: var(--dossier-primary);
    }

    .dossiers-btn,
    .dossiers-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .dossiers-btn {
        min-height: 50px;
        border-radius: 16px;
        border: 1px solid transparent;
        padding: 0 18px;
        font-size: .92rem;
        font-weight: 800;
    }

    .dossiers-btn:hover,
    .dossiers-btn:focus,
    .dossiers-icon-btn:hover,
    .dossiers-icon-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .dossiers-btn.primary {
        background: linear-gradient(135deg, var(--dossier-success) 0%, #117454 100%);
        color: #fff;
        box-shadow: 0 18px 28px -24px rgba(22, 149, 111, 0.85);
    }

    .dossiers-btn.secondary {
        border-color: #cfdeec;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        color: #48657f;
        box-shadow: 0 14px 22px -24px rgba(15, 23, 42, 0.42);
    }

    .dossiers-btn.filter {
        background: linear-gradient(135deg, var(--dossier-primary) 0%, #0b7ac7 100%);
        color: #fff;
        box-shadow: 0 18px 28px -24px rgba(27, 121, 201, 0.72);
    }

    .dossiers-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }

    .dossiers-kpi {
        padding: 18px;
        display: grid;
        gap: 12px;
    }

    .dossiers-kpi-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .dossiers-kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .dossiers-kpi-icon.primary { background: rgba(27, 121, 201, 0.14); color: var(--dossier-primary); }
    .dossiers-kpi-icon.success { background: rgba(22, 149, 111, 0.14); color: var(--dossier-success); }
    .dossiers-kpi-icon.warning { background: rgba(201, 130, 18, 0.14); color: var(--dossier-warning); }
    .dossiers-kpi-icon.danger { background: rgba(215, 77, 93, 0.14); color: var(--dossier-danger); }

    .dossiers-kpi-value {
        margin: 0;
        color: var(--dossier-text);
        font-size: clamp(1.85rem, 2.4vw, 2.35rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .dossiers-kpi-label {
        margin: 0;
        color: var(--dossier-muted);
        font-size: .9rem;
        font-weight: 700;
    }

    .dossiers-filter-head,
    .dossiers-table-head,
    .dossiers-table-footer {
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--dossier-border);
        background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
    }

    .dossiers-table-footer {
        align-items: center;
        border-bottom: 0;
        border-top: 1px solid var(--dossier-border);
    }

    .dossiers-filter-form {
        padding: 18px 20px 20px;
    }

    .dossiers-filter-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.8fr) repeat(2, minmax(180px, .7fr)) auto auto;
        gap: 12px;
        align-items: end;
    }

    .dossiers-field {
        display: grid;
        gap: 8px;
    }

    .dossiers-field-label {
        color: #203b5e;
        font-size: .85rem;
        font-weight: 700;
    }

    .dossiers-search-wrap {
        position: relative;
    }

    .dossiers-search-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #8aa1b9;
        pointer-events: none;
    }

    .dossiers-page .form-control,
    .dossiers-page .form-select {
        min-height: 48px;
        border-radius: 14px;
        border-color: #cfdded;
        background: #fbfdff;
        color: var(--dossier-text);
        font-weight: 500;
    }

    .dossiers-page .form-control {
        padding-left: 42px;
    }

    .dossiers-page .form-control:focus,
    .dossiers-page .form-select:focus {
        border-color: #67a6eb;
        box-shadow: 0 0 0 .2rem rgba(29, 111, 220, 0.16);
        transform: translateY(-.5px);
    }

    .dossiers-table-wrap {
        overflow-x: auto;
    }

    .dossiers-table {
        width: 100%;
        min-width: 1080px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .dossiers-table thead th {
        padding: 16px 20px;
        border-bottom: 1px solid var(--dossier-border);
        color: #6a839c;
        font-size: .77rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
        background: rgba(248, 251, 255, 0.88);
    }

    .dossiers-table tbody tr:hover {
        background: rgba(244, 249, 255, 0.78);
    }

    .dossiers-table tbody td {
        padding: 18px 20px;
        border-bottom: 1px solid #e7eff7;
        vertical-align: middle;
        color: var(--dossier-text);
    }

    .dossiers-id-stack,
    .dossiers-patient-stack,
    .dossiers-date-stack {
        display: grid;
        gap: 4px;
    }

    .dossiers-row-id {
        color: var(--dossier-primary-strong);
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .dossiers-row-number,
    .dossiers-patient-name,
    .dossiers-date-main {
        color: var(--dossier-text);
        font-size: .96rem;
        font-weight: 800;
    }

    .dossiers-row-meta,
    .dossiers-patient-meta,
    .dossiers-date-meta {
        color: var(--dossier-muted);
        font-size: .84rem;
        font-weight: 600;
    }

    .dossiers-pill.is-general { background: rgba(27, 121, 201, 0.12); border-color: rgba(27, 121, 201, 0.18); color: #0f5d9e; }
    .dossiers-pill.is-specialise { background: rgba(22, 149, 111, 0.12); border-color: rgba(22, 149, 111, 0.18); color: #117454; }
    .dossiers-pill.is-urgence { background: rgba(201, 130, 18, 0.12); border-color: rgba(201, 130, 18, 0.18); color: #9a640e; }
    .dossiers-status-pill.is-active { background: rgba(22, 149, 111, 0.12); border-color: rgba(22, 149, 111, 0.18); color: #117454; }
    .dossiers-status-pill.is-archive { background: rgba(106, 131, 156, 0.14); border-color: rgba(106, 131, 156, 0.2); color: #556b80; }

    .dossiers-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1px solid #d7e4ef;
        background: #f8fbff;
        color: #64809b;
        box-shadow: 0 14px 18px -24px rgba(15, 40, 65, 0.6);
    }

    .dossiers-icon-btn:hover,
    .dossiers-icon-btn:focus {
        border-color: rgba(27, 121, 201, 0.24);
        background: #eef6ff;
        color: var(--dossier-primary);
    }

    .dossiers-icon-btn.view:hover,
    .dossiers-icon-btn.view:focus { color: var(--dossier-success); }
    .dossiers-icon-btn.edit:hover,
    .dossiers-icon-btn.edit:focus { color: var(--dossier-warning); }
    .dossiers-icon-btn.archive:hover,
    .dossiers-icon-btn.archive:focus { color: var(--dossier-danger); }

    .dossiers-empty {
        padding: 38px 24px;
        text-align: center;
    }

    .dossiers-empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 14px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(27, 121, 201, 0.14) 0%, rgba(22, 149, 111, 0.14) 100%);
        color: var(--dossier-primary);
        font-size: 1.45rem;
    }

    .dossiers-empty-title {
        margin: 0 0 8px;
        color: var(--dossier-text);
        font-size: 1.05rem;
        font-weight: 800;
    }

    .dossiers-table-footer .pagination { margin-bottom: 0; }
    .dossiers-table-footer .page-link { border-radius: 10px; border-color: #d7e4ef; color: #48657f; min-width: 38px; text-align: center; }
    .dossiers-table-footer .page-item.active .page-link { background: linear-gradient(135deg, var(--dossier-primary) 0%, var(--dossier-primary-strong) 100%); border-color: transparent; color: #fff; }

    html.dark body .dossiers-page,
    body.dark-mode .dossiers-page {
        --dossier-bg: linear-gradient(180deg, #0d1a29 0%, #0a1522 52%, #09131d 100%);
        --dossier-surface: rgba(14, 29, 46, 0.92);
        --dossier-border: #2d4966;
        --dossier-text: #e3efff;
        --dossier-muted: #9ab2cf;
    }

    html.dark body .dossiers-hero,
    html.dark body .dossiers-kpi,
    html.dark body .dossiers-filter-card,
    html.dark body .dossiers-table-card,
    html.dark body .dossiers-filter-head,
    html.dark body .dossiers-table-head,
    html.dark body .dossiers-table-footer,
    body.dark-mode .dossiers-hero,
    body.dark-mode .dossiers-kpi,
    body.dark-mode .dossiers-filter-card,
    body.dark-mode .dossiers-table-card,
    body.dark-mode .dossiers-filter-head,
    body.dark-mode .dossiers-table-head,
    body.dark-mode .dossiers-table-footer {
        background: #102136;
        border-color: #2e4966;
    }

    html.dark body .dossiers-eyebrow,
    html.dark body .dossiers-kicker,
    body.dark-mode .dossiers-eyebrow,
    body.dark-mode .dossiers-kicker {
        background: rgba(15, 31, 50, 0.82);
        border-color: #365579;
        color: #9fd1ff;
    }

    html.dark body .dossiers-chip,
    html.dark body .dossiers-pill,
    html.dark body .dossiers-icon-btn,
    body.dark-mode .dossiers-chip,
    body.dark-mode .dossiers-pill,
    body.dark-mode .dossiers-icon-btn {
        background: #13283e;
        border-color: #31506f;
        color: #bdd2ea;
    }

    html.dark body .dossiers-page .form-control,
    html.dark body .dossiers-page .form-select,
    body.dark-mode .dossiers-page .form-control,
    body.dark-mode .dossiers-page .form-select {
        background: #0d1b2b;
        border-color: #3a5a7e;
        color: #e5efff;
    }

    html.dark body .dossiers-table thead th,
    body.dark-mode .dossiers-table thead th {
        background: rgba(13, 27, 43, 0.9);
        border-bottom-color: #334c68;
        color: #a8bed7;
    }

    html.dark body .dossiers-table tbody td,
    body.dark-mode .dossiers-table tbody td {
        border-bottom-color: #253d57;
        color: var(--dossier-text);
    }

    html.dark body .dossiers-table tbody tr:hover,
    body.dark-mode .dossiers-table tbody tr:hover {
        background: rgba(19, 40, 62, 0.84);
    }

    html.dark body .dossiers-btn.secondary,
    body.dark-mode .dossiers-btn.secondary {
        background: linear-gradient(180deg, #16283e 0%, #112235 100%);
        border-color: #3a5a7e;
        color: #dceaff;
    }

    @media (max-width: 1199px) {
        .dossiers-kpi-grid,
        .dossiers-filter-grid {
            grid-template-columns: 1fr;
        }

        .dossiers-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dossiers-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .dossiers-page {
            padding-left: 4px;
            padding-right: 4px;
        }

        .dossiers-kpi-grid,
        .dossiers-filter-grid {
            grid-template-columns: 1fr;
        }

        .dossiers-hero-head {
            flex-direction: column;
            align-items: stretch;
        }

        .dossiers-hero-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .dossiers-btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .dossiers-title {
            font-size: 1.55rem;
        }

        .dossiers-table {
            min-width: 940px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid dossiers-page">
    <div class="dossiers-shell">
        <section class="dossiers-hero">
            <div class="dossiers-hero-grid">
                <div>
                    <div class="dossiers-hero-head">
                        <span class="dossiers-eyebrow"><i class="fas fa-folder-open"></i> Gestion clinique</span>
                        <div class="dossiers-hero-actions">
                            <a href="{{ route('dossiers.create') }}" class="dossiers-btn primary">
                                <i class="fas fa-folder-plus"></i>Nouveau Dossier
                            </a>
                            <a href="{{ route('dossiers.archives') }}" class="dossiers-btn secondary">
                                <i class="fas fa-box-archive"></i>Archives
                            </a>
                        </div>
                    </div>
                    <div class="dossiers-title-row">
                        <span class="dossiers-title-icon"><i class="fas fa-notes-medical"></i></span>
                        <div>
                            <h1 class="dossiers-title">Gestion des Dossiers Medicaux</h1>
                            <p class="dossiers-subtitle">Retrouvez les dossiers patients, appliquez vos filtres rapidement et gardez une lecture claire des cas suivis par le cabinet.</p>
                        </div>
                    </div>

                    <div class="dossiers-chip-row">
                        <span class="dossiers-badge"><i class="fas fa-folder"></i>{{ $dossiers->total() }} dossiers affiches</span>
                        <span class="dossiers-chip"><i class="fas fa-users"></i>{{ $stats['patients'] }} patients concernes</span>
                        <span class="dossiers-chip"><i class="fas fa-stethoscope"></i>{{ $stats['consultations'] }} consultations liees</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="dossiers-kpi-grid">
            <article class="dossiers-kpi">
                <div class="dossiers-kpi-head">
                    <div>
                        <p class="dossiers-kpi-label">Dossiers actifs</p>
                        <h2 class="dossiers-kpi-value">{{ $stats['actifs'] }}</h2>
                    </div>
                    <span class="dossiers-kpi-icon primary"><i class="fas fa-folder-open"></i></span>
                </div>
                <p class="dossiers-meta-copy">Vision immediate des dossiers operationnels suivis par l'equipe.</p>
            </article>

            <article class="dossiers-kpi">
                <div class="dossiers-kpi-head">
                    <div>
                        <p class="dossiers-kpi-label">Patients concernes</p>
                        <h2 class="dossiers-kpi-value">{{ $stats['patients'] }}</h2>
                    </div>
                    <span class="dossiers-kpi-icon success"><i class="fas fa-user-group"></i></span>
                </div>
                <p class="dossiers-meta-copy">Nombre de patients distincts couverts par la selection courante.</p>
            </article>

            <article class="dossiers-kpi">
                <div class="dossiers-kpi-head">
                    <div>
                        <p class="dossiers-kpi-label">Consultations</p>
                        <h2 class="dossiers-kpi-value">{{ $stats['consultations'] }}</h2>
                    </div>
                    <span class="dossiers-kpi-icon warning"><i class="fas fa-file-waveform"></i></span>
                </div>
                <p class="dossiers-meta-copy">Volume de consultations rattachees aux dossiers affiches.</p>
            </article>

            <article class="dossiers-kpi">
                <div class="dossiers-kpi-head">
                    <div>
                        <p class="dossiers-kpi-label">Cas urgents</p>
                        <h2 class="dossiers-kpi-value">{{ $stats['urgents'] }}</h2>
                    </div>
                    <span class="dossiers-kpi-icon danger"><i class="fas fa-triangle-exclamation"></i></span>
                </div>
                <p class="dossiers-meta-copy">Detection rapide des dossiers comportant une mention d'urgence.</p>
            </article>
        </section>

        <section class="dossiers-filter-card">
            <div class="dossiers-filter-head">
                <div>
                    <span class="dossiers-kicker">Recherche et filtres</span>
                    <p class="dossiers-copy mt-2">Affinez la liste par mot-cle, type de dossier et volume affiche, avec une disposition compacte et coherente.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('dossiers.index') }}" class="dossiers-filter-form">
                <div class="dossiers-filter-grid">
                    <div class="dossiers-field">
                        <label for="search" class="dossiers-field-label">Recherche</label>
                        <div class="dossiers-search-wrap">
                            <i class="fas fa-magnifying-glass"></i>
                            <input id="search" type="text" name="search" class="form-control" placeholder="Numero, patient, diagnostic, observations..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="dossiers-field">
                        <label for="type" class="dossiers-field-label">Type</label>
                        <select id="type" name="type" class="form-select">
                            <option value="">Tous les types</option>
                            @foreach($typeOptions as $typeOption)
                                <option value="{{ $typeOption }}" @selected(request('type') === $typeOption)>{{ ucfirst($typeOption) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="dossiers-field">
                        <label for="per_page" class="dossiers-field-label">Lignes</label>
                        <select id="per_page" name="per_page" class="form-select">
                            @foreach([10, 20, 50, 100] as $perPageOption)
                                <option value="{{ $perPageOption }}" @selected((int) request('per_page', 10) === $perPageOption)>{{ $perPageOption }} / page</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="dossiers-btn filter">
                        <i class="fas fa-filter"></i>Appliquer
                    </button>

                    @if(request()->hasAny(['search', 'type', 'per_page']))
                        <a href="{{ route('dossiers.index') }}" class="dossiers-btn secondary">
                            <i class="fas fa-rotate-left"></i>Reinitialiser
                        </a>
                    @else
                        <span></span>
                    @endif
                </div>
            </form>
        </section>

        <section class="dossiers-table-card">
            <div class="dossiers-table-head">
                <div>
                    <span class="dossiers-kicker">Registre</span>
                    <h2 class="dossiers-title mt-2" style="font-size: 1.35rem;">Liste des dossiers</h2>
                    <p class="dossiers-copy mt-2">Acces direct au dossier, a l'edition et a la creation d'une nouvelle consultation, dans un tableau plus lisible et plus respirant.</p>
                </div>
                <div class="dossiers-meta-row">
                    <span class="dossiers-chip"><i class="fas fa-table-list"></i>{{ $dossiers->count() }} lignes</span>
                    <span class="dossiers-chip"><i class="fas fa-arrows-left-right-to-line"></i>Scroll mobile</span>
                </div>
            </div>

            <div class="dossiers-table-wrap">
                <table class="dossiers-table">
                    <thead>
                        <tr>
                            <th>ID / Numero</th>
                            <th>Patient</th>
                            <th>Type</th>
                            <th>Date ouverture</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dossiers as $dossier)
                            <tr>
                                <td>
                                    <div class="dossiers-id-stack">
                                        <span class="dossiers-row-id">#{{ $dossier->id }}</span>
                                        <span class="dossiers-row-number">{{ $dossier->display_numero_dossier }}</span>
                                        <span class="dossiers-row-meta">{{ $dossier->display_reference_meta }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="dossiers-patient-stack">
                                        <span class="dossiers-patient-name">
                                            @if($dossier->patient)
                                                {{ strtoupper($dossier->patient->nom) }} {{ $dossier->patient->prenom }}
                                            @else
                                                Patient inconnu
                                            @endif
                                        </span>
                                        <div class="dossiers-patient-meta">
                                            <span>ID patient: {{ $dossier->patient_id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="dossiers-pill {{ $dossier->display_type_class }}">{{ $dossier->display_type_label }}</span>
                                </td>
                                <td>
                                    <div class="dossiers-date-stack">
                                        <span class="dossiers-date-main">{{ $dossier->display_open_date }}</span>
                                        <span class="dossiers-date-meta">{{ $dossier->display_open_date_human }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="dossiers-status-pill dossiers-pill {{ $dossier->display_status_class }}">{{ ucfirst($dossier->statut ?? 'actif') }}</span>
                                </td>
                                <td>
                                    <div class="dossiers-actions justify-content-end">
                                        <a href="{{ route('dossiers.show', $dossier->id) }}" class="dossiers-icon-btn view" title="Voir le dossier" aria-label="Voir le dossier {{ $dossier->numero_dossier }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('dossiers.edit', $dossier->id) }}" class="dossiers-icon-btn edit" title="Modifier le dossier" aria-label="Modifier le dossier {{ $dossier->numero_dossier }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="{{ route('consultations.create', ['dossier_id' => $dossier->id]) }}" class="dossiers-icon-btn" title="Nouvelle consultation" aria-label="Creer une consultation pour le dossier {{ $dossier->numero_dossier }}">
                                            <i class="fas fa-stethoscope"></i>
                                        </a>
                                        <form action="{{ route('dossiers.archive', $dossier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment archiver ce dossier medical ?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dossiers-icon-btn archive" title="Archiver" aria-label="Archiver le dossier {{ $dossier->numero_dossier }}">
                                                <i class="fas fa-box-archive"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="dossiers-empty">
                                        <div class="dossiers-empty-icon"><i class="fas fa-folder-open"></i></div>
                                        <h3 class="dossiers-empty-title">Aucun dossier medical trouve</h3>
                                        <p class="dossiers-empty-copy">Ajustez vos filtres ou creez un nouveau dossier pour demarrer le suivi patient.</p>
                                        <a href="{{ route('dossiers.create') }}" class="dossiers-btn primary mt-3">
                                            <i class="fas fa-folder-plus"></i>Creer un dossier
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="dossiers-table-footer">
                <p class="dossiers-pagination-copy">Affichage de {{ $dossiers->firstItem() ?? 0 }} a {{ $dossiers->lastItem() ?? 0 }} sur {{ $dossiers->total() }} dossiers</p>
                @if($dossiers->hasPages())
                    <div>{{ $dossiers->links() }}</div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection
