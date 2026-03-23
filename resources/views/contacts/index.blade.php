@extends('layouts.app')

@section('title', 'Contacts')

@section('content')
<style>
    .contacts-page {
        --c-primary: #2563eb;
        --c-primary-dark: #1d4fbe;
        --c-accent: #2f7b99;
        --c-success: #0f8a63;
        --c-warning: #c7791f;
        --c-bg: radial-gradient(circle at top right, rgba(37, 99, 235, .1) 0%, rgba(37, 99, 235, 0) 26%), linear-gradient(180deg, #f4f8fc 0%, #f9fbff 100%);
        --c-card: #ffffff;
        --c-border: #d8e4f0;
        --c-border-strong: #c7d7e8;
        --c-title: #173454;
        --c-text: #4b6481;
        --c-muted: #7086a2;
        --c-shadow: 0 22px 46px -34px rgba(15, 45, 82, .28);
        --c-shadow-hover: 0 28px 52px -32px rgba(15, 45, 82, .36);
        width: 100%;
        max-width: none;
        padding: 18px 18px 28px;
        background: var(--c-bg);
        border: 1px solid #dbe6f1;
        border-radius: 22px;
        box-shadow: var(--c-shadow);
    }

    .contacts-shell {
        width: 100%;
        max-width: none;
    }

    .contacts-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
        padding: 2px 0 22px;
        border-bottom: 1px solid #dbe6f1;
    }

    .contacts-head-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }

    .contacts-head-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-grid;
        place-items: center;
        flex: 0 0 auto;
        color: var(--c-primary);
        background: linear-gradient(145deg, #eff5ff 0%, #ddeafe 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .6);
        font-size: 1.18rem;
    }

    .contacts-head-copy {
        min-width: 0;
        display: grid;
        gap: 8px;
    }

    .contacts-head-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d9e6f3;
        background: rgba(255, 255, 255, .82);
        color: var(--c-primary-dark);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .contacts-head h1 {
        margin: 0;
        color: var(--c-title);
        font-size: clamp(1.7rem, 2.6vw, 2.2rem);
        font-weight: 800;
        line-height: 1.02;
        letter-spacing: -.04em;
    }

    .contacts-head p {
        margin: 0;
        color: var(--c-muted);
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.6;
    }

    .contacts-head-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .contacts-head-btn {
        min-height: 44px;
        border-radius: 14px;
        padding: 0 18px;
        border: 1px solid transparent;
        background: linear-gradient(180deg, #ffffff 0%, #f3f7fb 100%);
        color: #486482;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 800;
        white-space: nowrap;
        box-shadow: 0 14px 22px -24px rgba(15, 45, 82, .3);
        transition: all .2s ease;
    }

    .contacts-head-btn:hover {
        background: linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
        color: #2c4b6c;
        transform: translateY(-1px);
        box-shadow: 0 18px 26px -24px rgba(15, 45, 82, .38);
    }

    .contacts-head-btn.primary {
        background: linear-gradient(135deg, var(--c-primary) 0%, var(--c-primary-dark) 100%);
        color: #fff;
        border-color: transparent;
    }

    .contacts-head-btn.primary:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        color: #fff;
        box-shadow: 0 20px 30px -24px rgba(37, 99, 235, .55);
    }

    .contacts-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .contact-stat {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: start;
        gap: 12px;
        background: var(--c-card);
        border: 1px solid var(--c-border);
        border-radius: 18px;
        padding: 18px;
        min-height: 132px;
        box-shadow: 0 20px 28px -30px rgba(16, 57, 104, .28);
        position: relative;
        overflow: hidden;
        transition: all .2s ease;
    }

    .contact-stat::before {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 4px;
        background: linear-gradient(90deg, rgba(37, 99, 235, .18) 0%, rgba(37, 99, 235, .6) 100%);
    }

    .contact-stat.accent::before { background: linear-gradient(90deg, rgba(47, 123, 153, .18) 0%, rgba(47, 123, 153, .58) 100%); }
    .contact-stat.success::before { background: linear-gradient(90deg, rgba(15, 138, 99, .18) 0%, rgba(15, 138, 99, .58) 100%); }
    .contact-stat.warning::before { background: linear-gradient(90deg, rgba(199, 121, 31, .18) 0%, rgba(199, 121, 31, .58) 100%); }

    .contact-stat:hover {
        transform: translateY(-2px);
        border-color: var(--c-border-strong);
        box-shadow: var(--c-shadow-hover);
    }

    .contact-stat-main {
        min-width: 0;
    }

    .contact-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-grid;
        place-items: center;
        font-size: 1rem;
        color: var(--c-primary);
        background: linear-gradient(145deg, #eff5ff 0%, #ddeafe 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .58);
    }

    .contact-stat.accent .contact-stat-icon {
        color: var(--c-accent);
        background: linear-gradient(145deg, #eef8fb 0%, #dceef5 100%);
    }

    .contact-stat.success .contact-stat-icon {
        color: var(--c-success);
        background: linear-gradient(145deg, #eef9f4 0%, #dff1e9 100%);
    }

    .contact-stat.warning .contact-stat-icon {
        color: var(--c-warning);
        background: linear-gradient(145deg, #fff5e8 0%, #fdebd7 100%);
    }

    .contact-stat-value {
        margin: 2px 0 0;
        color: var(--c-title);
        font-size: clamp(1.7rem, 2.2vw, 2rem);
        font-weight: 900;
        line-height: 1;
    }

    .contact-stat-label {
        margin: 10px 0 0;
        color: var(--c-text);
        font-size: .95rem;
        font-weight: 700;
    }

    .contacts-filter {
        background: var(--c-card);
        border: 1px solid var(--c-border);
        border-radius: 18px;
        padding: 14px;
        margin-bottom: 14px;
        box-shadow: var(--c-shadow);
    }

    .contacts-filter form {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 10px;
    }

    .contacts-filter input,
    .contacts-filter select {
        min-height: 44px;
        border: 1px solid #d6e2ee;
        border-radius: 12px;
        background: #fbfdff;
        padding: 10px 13px;
        color: #23415f;
        font-weight: 600;
        transition: all .2s ease;
    }

    .contacts-filter input:focus,
    .contacts-filter select:focus {
        outline: none;
        border-color: #88aee0;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, .14);
    }

    .contacts-filter button {
        min-height: 44px;
        border-radius: 12px;
        border: 1px solid transparent;
        background: linear-gradient(135deg, var(--c-primary) 0%, var(--c-primary-dark) 100%);
        color: #fff;
        font-weight: 800;
        padding: 9px 16px;
        box-shadow: 0 18px 28px -24px rgba(37, 99, 235, .48);
        transition: all .2s ease;
    }

    .contacts-filter button:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
    }

    .contacts-panel {
        background: var(--c-card);
        border: 1px solid var(--c-border);
        border-radius: 20px;
        box-shadow: var(--c-shadow);
        overflow: hidden;
    }

    .contacts-panel-head {
        padding: 16px 18px;
        border-bottom: 1px solid var(--c-border);
        background: linear-gradient(180deg, #fbfdff 0%, #f3f7fb 100%);
        color: var(--c-title);
        font-size: 1.18rem;
        font-weight: 900;
        letter-spacing: -.02em;
    }

    .contacts-table-wrap {
        overflow-x: auto;
    }

    .contacts-table {
        width: 100%;
        border-collapse: collapse;
    }

    .contacts-table thead {
        background: #f8fbfe;
        border-bottom: 2px solid #dce8f6;
    }

    .contacts-table th {
        padding: 11px 12px;
        text-transform: uppercase;
        font-size: .78rem;
        letter-spacing: .25px;
        color: #4f6581;
        text-align: left;
        font-weight: 800;
        white-space: nowrap;
    }

    .contacts-table td {
        padding: 12px;
        border-bottom: 1px solid #e5edf5;
        color: #243d5b;
        font-weight: 600;
        font-size: .92rem;
        vertical-align: middle;
    }

    .contacts-table tbody tr:hover {
        background: #f6faff;
    }

    .type-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        background: #eef4ff;
        color: #335f9a;
        border: 1px solid #d3e0f3;
    }

    .state-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        border: 1px solid;
    }

    .state-chip.active {
        color: #0d8a54;
        background: #e9f9f1;
        border-color: #b8ebd2;
    }

    .state-chip.inactive {
        color: #b24040;
        background: #fff1f1;
        border-color: #f5c7c7;
    }

    .favorite {
        color: #f59e0b;
    }

    .contact-actions {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .contact-btn {
        min-height: 36px;
        border-radius: 11px;
        border: 1px solid #d7e1ec;
        background: linear-gradient(180deg, #ffffff 0%, #f6faff 100%);
        color: #365272;
        text-decoration: none;
        padding: 6px 10px;
        font-size: .8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        line-height: 1;
        box-shadow: 0 12px 18px -24px rgba(15, 45, 82, .32);
        transition: all .2s ease;
    }

    .contact-btn:hover {
        background: linear-gradient(180deg, #ffffff 0%, #eef5fd 100%);
        color: #274666;
        transform: translateY(-1px);
    }

    .contact-btn.danger {
        border-color: #f4c8c8;
        background: #fff2f2;
        color: #b33e3e;
    }

    .contact-btn.danger:hover {
        background: #ffe9e9;
    }

    .contact-empty {
        display: grid;
        justify-items: center;
        gap: 12px;
        min-height: 320px;
        padding: 54px 20px;
        text-align: center;
        color: var(--c-muted);
        background: linear-gradient(180deg, #fbfdff 0%, #f7fbff 100%);
    }

    .contact-empty i {
        width: 78px;
        height: 78px;
        display: inline-grid;
        place-items: center;
        font-size: 2rem;
        color: var(--c-primary);
        border-radius: 24px;
        background: linear-gradient(145deg, #eff5ff 0%, #ddeafe 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .6);
        margin-bottom: 2px;
    }

    .contact-empty h3 {
        margin: 0;
        color: var(--c-title);
        font-size: clamp(1.18rem, 2vw, 1.42rem);
        font-weight: 800;
        letter-spacing: -.03em;
    }

    .contact-empty p {
        margin: 0;
        max-width: 32ch;
        color: var(--c-muted);
        font-size: .98rem;
        font-weight: 600;
        line-height: 1.7;
    }

    .contact-empty .contact-btn {
        min-width: auto;
        min-height: 50px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid transparent;
        background: linear-gradient(135deg, var(--c-primary) 0%, var(--c-primary-dark) 100%);
        color: #fff;
        box-shadow: 0 18px 28px -22px rgba(37, 99, 235, .55);
        justify-content: center;
        margin-top: 2px;
        gap: 9px;
        font-size: .92rem;
        font-weight: 800;
    }

    .contact-empty .contact-btn:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        color: #fff;
        box-shadow: 0 20px 30px -24px rgba(37, 99, 235, .55);
    }

    .contact-pagination {
        padding: 12px 16px;
        border-top: 1px solid var(--c-border);
        background: #fdfbff;
    }

    .contact-notice {
        border: 1px solid #cfe0f4;
        border-radius: 14px;
        background: #edf6ff;
        color: #1b4e84;
        padding: 11px 13px;
        margin-bottom: 14px;
        font-weight: 600;
        box-shadow: 0 14px 22px -28px rgba(15, 45, 82, .32);
    }

    .contact-notice.error {
        border-color: #f5c0c0;
        background: #fff0f0;
        color: #a73030;
    }

    .inline-form {
        display: inline;
    }

    .inline-form button {
        all: unset;
        cursor: pointer;
    }

    @media (max-width: 1240px) {
        .contacts-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .contacts-filter form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 760px) {
        .contacts-page {
            padding: 12px 12px 22px;
        }

        .contacts-head {
            flex-direction: column;
            align-items: stretch;
            padding-bottom: 18px;
        }

        .contacts-head-main {
            flex-wrap: wrap;
        }

        .contacts-head-actions {
            width: 100%;
            justify-content: stretch;
        }

        .contacts-head-btn {
            width: 100%;
            justify-content: center;
        }

        .contacts-stats {
            grid-template-columns: 1fr;
        }

        .contacts-filter form {
            grid-template-columns: 1fr;
        }

        .contact-empty {
            min-height: 280px;
            padding: 42px 18px;
        }

        .contact-empty .contact-btn {
            width: 100%;
            min-width: 0;
        }
    }

    body.dark-mode .contacts-page {
        --c-bg: linear-gradient(180deg, #0f1f31 0%, #0d1a2b 100%);
        --c-card: #12243b;
        --c-border: #2c4f79;
        --c-title: #d5e7ff;
        --c-text: #aec7e2;
        --c-muted: #8ea9c6;
    }

    body.dark-mode .contacts-head {
        border-bottom-color: #365a7b;
    }

    body.dark-mode .contacts-head-icon {
        color: #77b7ff;
        background: linear-gradient(145deg, #173251 0%, #16304c 100%);
    }

    body.dark-mode .contacts-head-eyebrow {
        border-color: #355978;
        background: rgba(19, 43, 69, .72);
        color: #d4e7fb;
    }

    body.dark-mode .contacts-head-btn {
        color: #d2e6fb;
        border-color: #3c5f81;
        background: linear-gradient(180deg, #183554 0%, #17324d 100%);
    }

    body.dark-mode .contacts-head-btn:hover {
        color: #fff;
        background: #234a6d;
    }

    body.dark-mode .contacts-head-btn.primary {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        border-color: transparent;
        color: #fff;
    }

    body.dark-mode .contacts-filter input,
    body.dark-mode .contacts-filter select {
        background: #132c49;
        border-color: #3c6290;
        color: #d6e9fe;
    }

    body.dark-mode .contacts-table thead {
        background: #173251;
        border-bottom-color: #335b86;
    }

    body.dark-mode .contacts-table th {
        color: #b9d3ee;
    }

    body.dark-mode .contacts-table td {
        color: #d6e9fe;
        border-bottom-color: #29476f;
    }

    body.dark-mode .contacts-table tbody tr:hover {
        background: #1b2f4d;
    }

    body.dark-mode .contacts-panel-head {
        background: linear-gradient(180deg, #173251 0%, #132b45 100%);
        border-bottom-color: #335b86;
        color: #e8f2ff;
    }

    body.dark-mode .contact-stat-icon {
        background: linear-gradient(145deg, #173251 0%, #16304c 100%);
        color: #92c5ff;
    }

    body.dark-mode .contact-stat.accent .contact-stat-icon {
        background: linear-gradient(145deg, #163847 0%, #154355 100%);
        color: #9ad5eb;
    }

    body.dark-mode .contact-stat.success .contact-stat-icon {
        background: linear-gradient(145deg, #16372d 0%, #154031 100%);
        color: #8be0c1;
    }

    body.dark-mode .contact-stat.warning .contact-stat-icon {
        background: linear-gradient(145deg, #44311d 0%, #573b22 100%);
        color: #f9c88f;
    }

    body.dark-mode .type-chip {
        background: #1a3656;
        border-color: #345b84;
        color: #cfe4ff;
    }

    body.dark-mode .contact-btn {
        background: linear-gradient(180deg, #173456 0%, #15314f 100%);
        border-color: #3f6795;
        color: #d3e8ff;
    }

    body.dark-mode .contact-empty {
        background: linear-gradient(180deg, #12243b 0%, #102033 100%);
    }

    body.dark-mode .contact-empty i {
        color: #9ac8ff;
        background: linear-gradient(145deg, #173251 0%, #16304c 100%);
    }

    body.dark-mode .contact-empty .contact-btn {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        border-color: transparent;
        color: #fff;
    }

    .contacts-panel-tools {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .contacts-mode-compact .contacts-table td {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .contacts-mode-compact .contact-actions {
        gap: 6px;
    }

    .contacts-mode-cards .contacts-table thead {
        display: none;
    }

    .contacts-mode-cards .contacts-table,
    .contacts-mode-cards .contacts-table tbody {
        display: grid;
        gap: 12px;
        width: 100%;
    }

    .contacts-mode-cards .contacts-table tbody tr {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 14px;
        padding: 16px;
        border-radius: 18px;
        border: 1px solid #dbe5ef;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 16px 22px -26px rgba(15, 23, 42, .16);
    }

    .contacts-mode-cards .contacts-table td {
        display: grid;
        gap: 4px;
        padding: 0;
        border: none;
    }

    .contacts-mode-cards .contacts-table td::before {
        content: attr(data-label);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #7a8ea5;
    }

    .contacts-mode-cards .contacts-table td:last-child {
        grid-column: 1 / -1;
    }
</style>

@php($displayMode = request('display', 'table'))
<div class="contacts-page contacts-mode-{{ $displayMode }}">
    <div class="contacts-shell">
        <div class="contacts-head">
            <div class="contacts-head-main">
                <span class="contacts-head-icon"><i class="fas fa-address-book"></i></span>
                <div class="contacts-head-copy">
                <span class="contacts-head-eyebrow"><i class="fas fa-address-card"></i> Carnet du cabinet</span>
                <h1>Contacts</h1>
                <p>Liste centralisee des contacts importants du cabinet</p>
                </div>
            </div>

            <div class="contacts-head-actions">
                <a href="{{ route('contacts.export') }}" class="contacts-head-btn">
                    <i class="fas fa-file-export"></i> Export
                </a>
                <a href="{{ route('contacts.create') }}" class="contacts-head-btn primary">
                    <i class="fas fa-plus"></i> Nouveau contact
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="contact-notice">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="contact-notice error">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            </div>
        @endif

        <div class="contacts-stats">
            <div class="contact-stat">
                <div class="contact-stat-main">
                    <div class="contact-stat-value">{{ number_format($totalContacts) }}</div>
                    <p class="contact-stat-label">Total contacts</p>
                </div>
                <span class="contact-stat-icon"><i class="fas fa-users"></i></span>
            </div>
            <div class="contact-stat accent">
                <div class="contact-stat-main">
                    <div class="contact-stat-value">{{ number_format($activeContacts) }}</div>
                    <p class="contact-stat-label">Contacts actifs</p>
                </div>
                <span class="contact-stat-icon"><i class="fas fa-user-check"></i></span>
            </div>
            <div class="contact-stat success">
                <div class="contact-stat-main">
                    <div class="contact-stat-value">{{ number_format($favoriteContacts) }}</div>
                    <p class="contact-stat-label">Favoris</p>
                </div>
                <span class="contact-stat-icon"><i class="fas fa-star"></i></span>
            </div>
            <div class="contact-stat warning">
                <div class="contact-stat-main">
                    <div class="contact-stat-value">{{ number_format($typeCount) }}</div>
                    <p class="contact-stat-label">Types utilises</p>
                </div>
                <span class="contact-stat-icon"><i class="fas fa-layer-group"></i></span>
            </div>
        </div>

        <div class="contacts-filter">
            <form method="GET" action="{{ route('contacts.index') }}">
                <input type="hidden" name="display" value="{{ $displayMode }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom, prenom, entreprise, email ou telephone...">

                <select name="type">
                    <option value="">Tous les types</option>
                    @foreach($typesList as $typeValue => $typeLabel)
                        <option value="{{ $typeValue }}" {{ request('type') === (string) $typeValue ? 'selected' : '' }}>
                            {{ $typeLabel }}
                        </option>
                    @endforeach
                </select>

                <select name="actif">
                    <option value="">Tous les statuts</option>
                    <option value="oui" {{ request('actif') === 'oui' ? 'selected' : '' }}>Actifs</option>
                    <option value="non" {{ request('actif') === 'non' ? 'selected' : '' }}>Inactifs</option>
                </select>

                <button type="submit"><i class="fas fa-filter me-1"></i> Appliquer</button>
            </form>
        </div>

        <div class="contacts-panel">
            <div class="contacts-panel-head contacts-panel-tools">
                <span>Liste des contacts</span>
                <div class="display-mode-switch" role="group" aria-label="Mode d affichage">
                    <a href="{{ request()->fullUrlWithQuery(['display' => 'table', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'table' ? 'active' : '' }}">Mode tableau</a>
                    <a href="{{ request()->fullUrlWithQuery(['display' => 'compact', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'compact' ? 'active' : '' }}">Mode compact</a>
                    <a href="{{ request()->fullUrlWithQuery(['display' => 'cards', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'cards' ? 'active' : '' }}">Mode cartes</a>
                </div>
            </div>

            @if($contactsList->count() > 0)
                <div class="contacts-table-wrap">
                    <table class="contacts-table">
                        <thead>
                            <tr>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Coordonnees</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contactsList as $contact)
                                <tr>
                                    <td data-label="Contact">
                                        <div style="font-weight: 800; color: var(--c-title);">
                                            {{ trim(($contact->nom ?? '') . ' ' . ($contact->prenom ?? '')) ?: ($contact->nom ?? 'N/A') }}
                                            @if($contact->is_favorite)
                                                <i class="fas fa-star favorite ms-1"></i>
                                            @endif
                                        </div>
                                        @if($contact->entreprise)
                                            <div style="color: var(--c-muted); font-size: .83rem;">{{ $contact->entreprise }}</div>
                                        @endif
                                    </td>
                                    <td data-label="Type">
                                        <span class="type-chip">{{ $typesMap[$contact->type] ?? ucfirst((string) $contact->type) }}</span>
                                    </td>
                                    <td data-label="Coordonnees">
                                        <div>{{ $contact->telephone ?: '-' }}</div>
                                        <div style="color: var(--c-muted); font-size: .83rem;">{{ $contact->email ?: '-' }}</div>
                                    </td>
                                    <td data-label="Statut">
                                        <span class="state-chip {{ $contact->is_actif ? 'active' : 'inactive' }}">
                                            {{ $contact->is_actif ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="contact-actions">
                                            <a href="{{ route('contacts.show', $contact) }}" class="contact-btn action-tone-view">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                            <a href="{{ route('contacts.edit', $contact) }}" class="contact-btn action-tone-edit">
                                                <i class="fas fa-pen"></i> Editer
                                            </a>

                                            <form class="inline-form" action="{{ route('contacts.toggle-favorite', $contact) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="contact-btn" title="Favori">
                                                    <i class="fas {{ $contact->is_favorite ? 'fa-star' : 'fa-star-half-alt' }}"></i>
                                                </button>
                                            </form>

                                            <form class="inline-form" action="{{ route('contacts.toggle-active', $contact) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="contact-btn" title="Activer / Desactiver">
                                                    <i class="fas {{ $contact->is_actif ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </button>
                                            </form>

                                            <form class="inline-form" action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Supprimer ce contact ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="contact-btn danger action-tone-delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="contact-pagination">
                    {{ $contactsList->links() }}
                </div>
            @else
                <div class="contact-empty">
                    <i class="fas fa-user-plus"></i>
                    <h3>Aucun contact pour le moment</h3>
                    <p>Ajoutez votre premier contact pour centraliser les échanges clés du cabinet dans un espace clair et structuré.</p>
                    <a href="{{ route('contacts.create') }}" class="contact-btn mt-2">
                        <i class="fas fa-plus"></i> Ajouter le premier contact
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


