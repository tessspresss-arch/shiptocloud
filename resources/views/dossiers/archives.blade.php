@extends('layouts.app')

@section('title', 'Archives des Dossiers Médicaux')
@section('topbar_subtitle', 'Consultation des dossiers archives, recherche rapide et historique de conservation dans une interface harmonisee.')

@push('styles')
<style>
    :root {
        --archive-bg: linear-gradient(180deg, #f4f9fd 0%, #edf5fb 100%);
        --archive-surface: rgba(255, 255, 255, 0.88);
        --archive-border: #d8e5ef;
        --archive-text: #17324c;
        --archive-muted: #68829a;
        --archive-primary: #1b79c9;
        --archive-primary-strong: #145d98;
        --archive-warning: #c98212;
        --archive-neutral: #6a839c;
        --archive-shadow: 0 26px 48px -36px rgba(15, 40, 65, 0.38);
    }

    .dossiers-archives-page {
        width: 100%;
        max-width: none;
        padding: 8px 8px 96px;
    }

    .archives-shell {
        display: grid;
        gap: 18px;
    }

    .archives-hero,
    .archives-kpi,
    .archives-filter-card,
    .archives-table-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--archive-border);
        border-radius: 24px;
        box-shadow: var(--archive-shadow);
    }

    .archives-hero {
        padding: 22px;
        background:
            radial-gradient(circle at top right, rgba(201, 130, 18, 0.16) 0%, rgba(201, 130, 18, 0) 30%),
            radial-gradient(circle at left top, rgba(27, 121, 201, 0.1) 0%, rgba(27, 121, 201, 0) 34%),
            var(--archive-bg);
    }

    .archives-kpi,
    .archives-filter-card,
    .archives-table-card {
        background: var(--archive-surface);
        backdrop-filter: blur(10px);
    }

    .archives-hero::before,
    .archives-kpi::before,
    .archives-filter-card::before,
    .archives-table-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .archives-hero > *,
    .archives-kpi > *,
    .archives-filter-card > *,
    .archives-table-card > * {
        position: relative;
        z-index: 1;
    }

    .archives-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(280px, 0.82fr);
        gap: 18px;
        align-items: stretch;
    }

    .archives-eyebrow,
    .archives-kicker {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(201, 130, 18, 0.16);
        background: rgba(255, 255, 255, 0.72);
        color: #8f5a0b;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .archives-title-row {
        display: flex;
        gap: 14px;
        align-items: center;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    .archives-title-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.4rem;
        background: linear-gradient(135deg, #c98212 0%, #9a640e 100%);
        box-shadow: 0 18px 28px -20px rgba(201, 130, 18, 0.54);
        flex-shrink: 0;
    }

    .archives-title {
        margin: 0;
        color: var(--archive-text);
        font-size: clamp(1.6rem, 2.6vw, 2.3rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .archives-subtitle,
    .archives-copy,
    .archives-meta-copy,
    .archives-empty-copy,
    .archives-pagination-copy {
        margin: 0;
        color: var(--archive-muted);
        font-size: .96rem;
        line-height: 1.64;
        font-weight: 600;
    }

    .archives-chip-row,
    .archives-hero-actions,
    .archives-meta-row,
    .archives-patient-meta,
    .archives-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .archives-chip-row {
        margin-top: 16px;
    }

    .archives-badge,
    .archives-chip,
    .archives-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        white-space: nowrap;
        font-weight: 700;
    }

    .archives-badge {
        background: linear-gradient(135deg, #c98212 0%, #9a640e 100%);
        color: #fff;
        box-shadow: 0 16px 24px -22px rgba(201, 130, 18, 0.84);
    }

    .archives-chip,
    .archives-pill {
        border: 1px solid #d7e4ef;
        background: #f6fafe;
        color: #57728c;
        font-size: .84rem;
    }

    .archives-action-box {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px;
        border-radius: 20px;
        border: 1px solid #d6e4f2;
        background: rgba(255, 255, 255, 0.8);
        box-shadow: 0 16px 24px -28px rgba(15, 40, 65, 0.4);
    }

    .archives-btn,
    .archives-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .archives-btn {
        min-height: 50px;
        border-radius: 16px;
        border: 1px solid transparent;
        padding: 0 18px;
        font-size: .92rem;
        font-weight: 800;
    }

    .archives-btn:hover,
    .archives-btn:focus,
    .archives-icon-btn:hover,
    .archives-icon-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .archives-btn.primary {
        background: linear-gradient(135deg, var(--archive-primary) 0%, var(--archive-primary-strong) 100%);
        color: #fff;
    }

    .archives-btn.secondary {
        border-color: #cfdeec;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        color: #48657f;
    }

    .archives-btn.filter {
        background: linear-gradient(135deg, #c98212 0%, #9a640e 100%);
        color: #fff;
    }

    .archives-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .archives-kpi {
        padding: 18px;
        display: grid;
        gap: 12px;
    }

    .archives-kpi-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .archives-kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        background: rgba(201, 130, 18, 0.14);
        color: #9a640e;
    }

    .archives-kpi-value {
        margin: 0;
        color: var(--archive-text);
        font-size: clamp(1.85rem, 2.4vw, 2.35rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .archives-kpi-label {
        margin: 0;
        color: var(--archive-muted);
        font-size: .9rem;
        font-weight: 700;
    }

    .archives-filter-head,
    .archives-table-head,
    .archives-table-footer {
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--archive-border);
        background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
    }

    .archives-table-footer {
        align-items: center;
        border-bottom: 0;
        border-top: 1px solid var(--archive-border);
    }

    .archives-filter-form {
        padding: 18px 20px 20px;
    }

    .archives-filter-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.8fr) minmax(180px, .7fr) auto auto;
        gap: 12px;
        align-items: end;
    }

    .archives-field {
        display: grid;
        gap: 8px;
    }

    .archives-field-label {
        color: #203b5e;
        font-size: .85rem;
        font-weight: 700;
    }

    .archives-search-wrap {
        position: relative;
    }

    .archives-search-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #8aa1b9;
        pointer-events: none;
    }

    .dossiers-archives-page .form-control,
    .dossiers-archives-page .form-select {
        min-height: 48px;
        border-radius: 14px;
        border-color: #cfdded;
        background: #fbfdff;
        color: var(--archive-text);
        font-weight: 500;
    }

    .dossiers-archives-page .form-control {
        padding-left: 42px;
    }

    .dossiers-archives-page .form-control:focus,
    .dossiers-archives-page .form-select:focus {
        border-color: #67a6eb;
        box-shadow: 0 0 0 .2rem rgba(29, 111, 220, 0.16);
    }

    .archives-table-wrap {
        overflow-x: auto;
    }

    .archives-table {
        width: 100%;
        min-width: 960px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .archives-table thead th {
        padding: 16px 20px;
        border-bottom: 1px solid var(--archive-border);
        color: #6a839c;
        font-size: .77rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
        background: rgba(248, 251, 255, 0.88);
    }

    .archives-table tbody tr:hover {
        background: rgba(247, 250, 255, 0.78);
    }

    .archives-table tbody td {
        padding: 18px 20px;
        border-bottom: 1px solid #e7eff7;
        vertical-align: middle;
        color: var(--archive-text);
    }

    .archives-id-stack,
    .archives-patient-stack,
    .archives-date-stack {
        display: grid;
        gap: 4px;
    }

    .archives-row-id {
        color: #9a640e;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .archives-row-number,
    .archives-patient-name,
    .archives-date-main {
        color: var(--archive-text);
        font-size: .96rem;
        font-weight: 800;
    }

    .archives-row-meta,
    .archives-patient-meta,
    .archives-date-meta {
        color: var(--archive-muted);
        font-size: .84rem;
        font-weight: 600;
    }

    .archives-pill.archived {
        background: rgba(106, 131, 156, 0.14);
        border-color: rgba(106, 131, 156, 0.2);
        color: #556b80;
    }

    .archives-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1px solid #d7e4ef;
        background: #f8fbff;
        color: #64809b;
    }

    .archives-icon-btn:hover,
    .archives-icon-btn:focus {
        border-color: rgba(27, 121, 201, 0.24);
        background: #eef6ff;
        color: var(--archive-primary);
    }

    .archives-empty {
        padding: 38px 24px;
        text-align: center;
    }

    .archives-empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 14px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(201, 130, 18, 0.14) 0%, rgba(27, 121, 201, 0.14) 100%);
        color: #9a640e;
        font-size: 1.45rem;
    }

    .archives-empty-title {
        margin: 0 0 8px;
        color: var(--archive-text);
        font-size: 1.05rem;
        font-weight: 800;
    }

    .archives-table-footer .pagination { margin-bottom: 0; }
    .archives-table-footer .page-link { border-radius: 10px; border-color: #d7e4ef; color: #48657f; min-width: 38px; text-align: center; }
    .archives-table-footer .page-item.active .page-link { background: linear-gradient(135deg, #c98212 0%, #9a640e 100%); border-color: transparent; color: #fff; }

    html.dark body .dossiers-archives-page,
    body.dark-mode .dossiers-archives-page {
        --archive-bg: linear-gradient(180deg, #0d1a29 0%, #0a1522 52%, #09131d 100%);
        --archive-surface: rgba(14, 29, 46, 0.92);
        --archive-border: #2d4966;
        --archive-text: #e3efff;
        --archive-muted: #9ab2cf;
    }

    html.dark body .archives-hero,
    html.dark body .archives-kpi,
    html.dark body .archives-filter-card,
    html.dark body .archives-table-card,
    html.dark body .archives-filter-head,
    html.dark body .archives-table-head,
    html.dark body .archives-table-footer,
    body.dark-mode .archives-hero,
    body.dark-mode .archives-kpi,
    body.dark-mode .archives-filter-card,
    body.dark-mode .archives-table-card,
    body.dark-mode .archives-filter-head,
    body.dark-mode .archives-table-head,
    body.dark-mode .archives-table-footer {
        background: #102136;
        border-color: #2e4966;
    }

    html.dark body .archives-eyebrow,
    html.dark body .archives-kicker,
    body.dark-mode .archives-eyebrow,
    body.dark-mode .archives-kicker {
        background: rgba(15, 31, 50, 0.82);
        border-color: #5c4b2e;
        color: #f4cf92;
    }

    html.dark body .archives-chip,
    html.dark body .archives-pill,
    html.dark body .archives-action-box,
    html.dark body .archives-icon-btn,
    body.dark-mode .archives-chip,
    body.dark-mode .archives-pill,
    body.dark-mode .archives-action-box,
    body.dark-mode .archives-icon-btn {
        background: #13283e;
        border-color: #31506f;
        color: #bdd2ea;
    }

    html.dark body .dossiers-archives-page .form-control,
    html.dark body .dossiers-archives-page .form-select,
    body.dark-mode .dossiers-archives-page .form-control,
    body.dark-mode .dossiers-archives-page .form-select {
        background: #0d1b2b;
        border-color: #3a5a7e;
        color: #e5efff;
    }

    html.dark body .archives-table thead th,
    body.dark-mode .archives-table thead th {
        background: rgba(13, 27, 43, 0.9);
        border-bottom-color: #334c68;
        color: #a8bed7;
    }

    html.dark body .archives-table tbody td,
    body.dark-mode .archives-table tbody td {
        border-bottom-color: #253d57;
        color: var(--archive-text);
    }

    html.dark body .archives-table tbody tr:hover,
    body.dark-mode .archives-table tbody tr:hover {
        background: rgba(19, 40, 62, 0.84);
    }

    @media (max-width: 1199px) {
        .archives-hero-grid,
        .archives-kpi-grid,
        .archives-filter-grid {
            grid-template-columns: 1fr;
        }

        .archives-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .dossiers-archives-page {
            padding-left: 4px;
            padding-right: 4px;
        }

        .archives-kpi-grid,
        .archives-filter-grid {
            grid-template-columns: 1fr;
        }

        .archives-hero-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .archives-btn {
            width: 100%;
            justify-content: center;
        }

        .archives-table {
            min-width: 860px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid dossiers-archives-page">
    <div class="archives-shell">
        <section class="archives-hero">
            <div class="archives-hero-grid">
                <div>
                    <span class="archives-eyebrow"><i class="fas fa-box-archive"></i> Conservation</span>
                    <div class="archives-title-row">
                        <span class="archives-title-icon"><i class="fas fa-box-archive"></i></span>
                        <div>
                            <h1 class="archives-title">Archives des Dossiers Médicaux</h1>
                            <p class="archives-subtitle">Consultez les dossiers archivés, retrouvez rapidement un patient ou une référence, et gardez une lecture claire de l’historique conservé.</p>
                        </div>
                    </div>

                    <div class="archives-chip-row">
                        <span class="archives-badge"><i class="fas fa-box-archive"></i>{{ $dossiersArchives->total() }} archives</span>
                        <span class="archives-chip"><i class="fas fa-users"></i>{{ $stats['patients'] }} patients concernés</span>
                        <span class="archives-chip"><i class="fas fa-clock-rotate-left"></i>{{ $stats['anciennete'] }} jours d’historique</span>
                    </div>
                </div>

                <aside class="archives-action-box">
                    <span class="archives-kicker">Navigation</span>
                    <p class="archives-copy">La vue archives reste séparée des dossiers actifs pour préserver la lisibilité opérationnelle et l’historique.</p>
                    <div class="archives-hero-actions">
                        <a href="{{ route('dossiers.index') }}" class="archives-btn primary">
                            <i class="fas fa-folder-open"></i>Retour aux dossiers actifs
                        </a>
                    </div>
                    <div class="archives-meta-row">
                        <span class="archives-chip"><i class="fas fa-magnifying-glass"></i>Recherche serveur</span>
                        <span class="archives-chip"><i class="fas fa-mobile-screen-button"></i>Responsive</span>
                    </div>
                </aside>
            </div>
        </section>

        <section class="archives-kpi-grid">
            <article class="archives-kpi">
                <div class="archives-kpi-head">
                    <div>
                        <p class="archives-kpi-label">Dossiers archivés</p>
                        <h2 class="archives-kpi-value">{{ $stats['archives'] }}</h2>
                    </div>
                    <span class="archives-kpi-icon"><i class="fas fa-box-archive"></i></span>
                </div>
                <p class="archives-meta-copy">Volume de dossiers conservés hors du flux actif.</p>
            </article>

            <article class="archives-kpi">
                <div class="archives-kpi-head">
                    <div>
                        <p class="archives-kpi-label">Patients concernés</p>
                        <h2 class="archives-kpi-value">{{ $stats['patients'] }}</h2>
                    </div>
                    <span class="archives-kpi-icon"><i class="fas fa-user-group"></i></span>
                </div>
                <p class="archives-meta-copy">Nombre de patients distincts dans l’historique archivé.</p>
            </article>

            <article class="archives-kpi">
                <div class="archives-kpi-head">
                    <div>
                        <p class="archives-kpi-label">Ancienneté</p>
                        <h2 class="archives-kpi-value">{{ $stats['anciennete'] }}</h2>
                    </div>
                    <span class="archives-kpi-icon"><i class="fas fa-calendar-days"></i></span>
                </div>
                <p class="archives-meta-copy">Nombre de jours depuis le plus ancien dossier archivé détecté.</p>
            </article>
        </section>

        <section class="archives-filter-card">
            <div class="archives-filter-head">
                <div>
                    <span class="archives-kicker">Recherche</span>
                    <p class="archives-copy mt-2">Filtrez les archives par référence dossier, patient, diagnostic ou observations, avec un affichage compact.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('dossiers.archives') }}" class="archives-filter-form">
                <div class="archives-filter-grid">
                    <div class="archives-field">
                        <label for="search" class="archives-field-label">Recherche</label>
                        <div class="archives-search-wrap">
                            <i class="fas fa-magnifying-glass"></i>
                            <input id="search" type="text" name="search" class="form-control" placeholder="Numéro, patient, diagnostic, observations..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="archives-field">
                        <label for="per_page" class="archives-field-label">Lignes</label>
                        <select id="per_page" name="per_page" class="form-select">
                            @foreach([10, 20, 50, 100] as $perPageOption)
                                <option value="{{ $perPageOption }}" @selected((int) request('per_page', 20) === $perPageOption)>{{ $perPageOption }} / page</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="archives-btn filter">
                        <i class="fas fa-filter"></i>Appliquer
                    </button>

                    @if(request()->hasAny(['search', 'per_page']))
                        <a href="{{ route('dossiers.archives') }}" class="archives-btn secondary">
                            <i class="fas fa-rotate-left"></i>Réinitialiser
                        </a>
                    @else
                        <span></span>
                    @endif
                </div>
            </form>
        </section>

        <section class="archives-table-card">
            <div class="archives-table-head">
                <div>
                    <span class="archives-kicker">Historique</span>
                    <h2 class="archives-title mt-2" style="font-size: 1.35rem;">Liste des archives</h2>
                    <p class="archives-copy mt-2">Consultez l’historique des dossiers archivés avec une hiérarchie claire entre numéro, patient, date et statut.</p>
                </div>
                <div class="archives-meta-row">
                    <span class="archives-chip"><i class="fas fa-table-list"></i>{{ $dossiersArchives->count() }} lignes</span>
                </div>
            </div>

            <div class="archives-table-wrap">
                <table class="archives-table">
                    <thead>
                        <tr>
                            <th>ID / Numéro</th>
                            <th>Patient</th>
                            <th>Date d’archivage</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dossiersArchives as $dossier)
                            <tr>
                                <td>
                                    <div class="archives-id-stack">
                                        <span class="archives-row-id">#{{ $dossier->id }}</span>
                                        <span class="archives-row-number">{{ $dossier->numero_dossier ?: 'Numéro non renseigné' }}</span>
                                        <span class="archives-row-meta">Référence archivée</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="archives-patient-stack">
                                        <span class="archives-patient-name">
                                            @if($dossier->patient)
                                                {{ strtoupper($dossier->patient->nom) }} {{ $dossier->patient->prenom }}
                                            @else
                                                Patient inconnu
                                            @endif
                                        </span>
                                        <div class="archives-patient-meta">
                                            <span>ID patient: {{ $dossier->patient_id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="archives-date-stack">
                                        <span class="archives-date-main">{{ optional($dossier->updated_at)->format('d/m/Y') ?: 'Date indisponible' }}</span>
                                        <span class="archives-date-meta">{{ optional($dossier->updated_at)->diffForHumans() ?: 'Historique indisponible' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="archives-pill archived"><i class="fas fa-box-archive"></i>Archivé</span>
                                </td>
                                <td>
                                    <div class="archives-actions justify-content-end">
                                        <a href="{{ route('dossiers.show', $dossier) }}" class="archives-icon-btn" title="Voir le dossier archivé" aria-label="Voir le dossier archivé {{ $dossier->numero_dossier }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="archives-empty">
                                        <div class="archives-empty-icon"><i class="fas fa-box-open"></i></div>
                                        <h3 class="archives-empty-title">Aucune archive trouvée</h3>
                                        <p class="archives-empty-copy">Ajustez vos filtres ou revenez aux dossiers actifs pour poursuivre le suivi clinique.</p>
                                        <a href="{{ route('dossiers.index') }}" class="archives-btn primary mt-3">
                                            <i class="fas fa-folder-open"></i>Retour aux dossiers actifs
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="archives-table-footer">
                <p class="archives-pagination-copy">Affichage de {{ $dossiersArchives->firstItem() ?? 0 }} à {{ $dossiersArchives->lastItem() ?? 0 }} sur {{ $dossiersArchives->total() }} dossiers archivés</p>
                @if($dossiersArchives->hasPages())
                    <div>{{ $dossiersArchives->links() }}</div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection