@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<style>
    .documents-page {
        --doc-primary: #2563eb;
        --doc-primary-dark: #1d4fbe;
        --doc-accent: #c7791f;
        --doc-success: #0f8a63;
        --doc-bg: radial-gradient(circle at top right, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0) 28%), linear-gradient(180deg, #f4f8fc 0%, #f8fbff 100%);
        --doc-card: #ffffff;
        --doc-surface-soft: #f7fbff;
        --doc-border: #d8e4f0;
        --doc-border-strong: #c7d7e8;
        --doc-title: #173454;
        --doc-text: #4b6481;
        --doc-muted: #7086a2;
        --doc-shadow: 0 22px 46px -34px rgba(15, 45, 82, .28);
        --doc-shadow-hover: 0 28px 52px -32px rgba(15, 45, 82, .36);
        width: 100%;
        max-width: none;
        padding: 18px 18px 28px;
        background: var(--doc-bg);
        border: 1px solid #dbe6f1;
        border-radius: 22px;
        box-shadow: var(--doc-shadow);
    }

    .documents-shell {
        width: 100%;
        max-width: none;
    }

    .documents-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 22px;
        padding: 2px 0 22px;
        border-bottom: 1px solid #dbe6f1;
    }

    .doc-head-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }

    .doc-back-btn {
        min-height: 42px;
        padding: 8px 15px;
        white-space: nowrap;
        border-color: #d4dfeb;
        color: #456281;
        font-weight: 700;
        border-radius: 12px;
        background: rgba(255, 255, 255, .76);
        box-shadow: 0 10px 18px -22px rgba(15, 45, 82, .42);
        transition: all .2s ease;
    }

    .doc-back-btn:hover {
        border-color: #b8cce1;
        background: #edf4fb;
        color: #1f3d5e;
    }

    .doc-head-title {
        min-width: 0;
        display: grid;
        gap: 8px;
    }

    .doc-head-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d9e6f3;
        background: rgba(255, 255, 255, .82);
        color: var(--doc-primary-dark);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .doc-title-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .doc-title-row i {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-grid;
        place-items: center;
        font-size: 1.18rem;
        color: var(--doc-primary);
        background: linear-gradient(145deg, #eff5ff 0%, #ddeafe 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .6);
    }

    .doc-title-row h1 {
        margin: 0;
        color: var(--doc-title);
        font-size: clamp(1.7rem, 2.6vw, 2.2rem);
        font-weight: 800;
        line-height: 1.02;
        letter-spacing: -.04em;
    }

    .doc-head-title p {
        margin: 0;
        color: #5f7896;
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.6;
    }

    .doc-count-badge {
        background: linear-gradient(90deg, #eff5ff 0%, #e3eefc 100%);
        color: var(--doc-primary-dark);
        border-radius: 999px;
        padding: 8px 14px;
        font-size: .88rem;
        font-weight: 800;
        border: 1px solid #d4e1f4;
        box-shadow: 0 10px 16px -18px rgba(37, 99, 235, .32);
        white-space: nowrap;
    }

    .doc-head-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .doc-head-btn {
        min-height: 44px;
        border-radius: 14px;
        padding: 0 18px;
        border: 1px solid transparent;
        font-size: .92rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        box-shadow: 0 14px 22px -24px rgba(15, 45, 82, .3);
        transition: all .2s ease;
    }

    .doc-head-btn.secondary {
        background: linear-gradient(180deg, #ffffff 0%, #f3f7fb 100%);
        border-color: #d7e1ec;
        color: #486482;
    }

    .doc-head-btn.secondary:hover {
        background: linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
        color: #2c4b6c;
        transform: translateY(-1px);
        box-shadow: 0 18px 26px -24px rgba(15, 45, 82, .38);
    }

    .doc-head-btn.success {
        background: linear-gradient(135deg, var(--doc-primary) 0%, var(--doc-primary-dark) 100%);
        border-color: transparent;
        color: #fff;
    }

    .doc-head-btn.success:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        transform: translateY(-1px);
        box-shadow: 0 20px 30px -24px rgba(37, 99, 235, .55);
        color: #fff;
    }

    .doc-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .doc-stat {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: start;
        gap: 12px;
        background: var(--doc-card);
        border: 1px solid var(--doc-border);
        border-radius: 18px;
        padding: 18px;
        min-height: 132px;
        box-shadow: 0 20px 28px -30px rgba(16, 57, 104, .28);
        position: relative;
        overflow: hidden;
        transition: all .2s ease;
    }

    .doc-stat::before {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 4px;
        background: linear-gradient(90deg, rgba(37, 99, 235, .18) 0%, rgba(37, 99, 235, .6) 100%);
    }

    .doc-stat.accent::before {
        background: linear-gradient(90deg, rgba(199, 121, 31, .18) 0%, rgba(199, 121, 31, .58) 100%);
    }

    .doc-stat.success::before {
        background: linear-gradient(90deg, rgba(15, 138, 99, .18) 0%, rgba(15, 138, 99, .58) 100%);
    }

    .doc-stat:hover {
        transform: translateY(-2px);
        border-color: var(--doc-border-strong);
        box-shadow: var(--doc-shadow-hover);
    }

    .doc-stat-main {
        min-width: 0;
    }

    .doc-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-grid;
        place-items: center;
        font-size: 1rem;
        color: var(--doc-primary);
        background: linear-gradient(145deg, #eff5ff 0%, #ddeafe 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .58);
    }

    .doc-stat.accent .doc-stat-icon {
        color: var(--doc-accent);
        background: linear-gradient(145deg, #fff5e8 0%, #fdebd7 100%);
    }

    .doc-stat.success .doc-stat-icon {
        color: var(--doc-success);
        background: linear-gradient(145deg, #eef9f4 0%, #dff1e9 100%);
    }

    .doc-stat-value {
        margin: 2px 0 0;
        color: var(--doc-title);
        font-size: clamp(1.8rem, 2.4vw, 2.2rem);
        font-weight: 900;
        line-height: 1;
    }

    .doc-stat-label {
        margin: 10px 0 0;
        color: var(--doc-text);
        font-size: .96rem;
        font-weight: 700;
    }

    .doc-panel {
        background: var(--doc-card);
        border: 1px solid var(--doc-border);
        border-radius: 20px;
        box-shadow: var(--doc-shadow);
        overflow: hidden;
    }

    .doc-panel-head {
        padding: 16px 18px;
        border-bottom: 1px solid var(--doc-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5fb 100%);
    }

    .doc-panel-title {
        display: grid;
        gap: 4px;
    }

    .doc-panel-head h2 {
        margin: 0;
        font-size: 1.26rem;
        font-weight: 900;
        color: var(--doc-title);
        letter-spacing: -.02em;
    }

    .doc-panel-head p {
        margin: 0;
        color: var(--doc-muted);
        font-size: .9rem;
        font-weight: 600;
    }

    .doc-table-wrap {
        overflow-x: auto;
    }

    .doc-table {
        width: 100%;
        border-collapse: collapse;
    }

    .doc-table thead {
        background: #f8fbfe;
        border-bottom: 2px solid #dce8f6;
    }

    .doc-table th {
        text-transform: uppercase;
        font-size: .79rem;
        letter-spacing: .25px;
        color: #375273;
        font-weight: 800;
        padding: 11px 12px;
        text-align: left;
        white-space: nowrap;
    }

    .doc-table td {
        padding: 12px;
        border-bottom: 1px solid #e2ebf6;
        color: #203a59;
        font-weight: 600;
        font-size: .93rem;
        vertical-align: middle;
    }

    .doc-table tbody tr:hover {
        background: #f6faff;
    }

    .doc-chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        background: #edf5ff;
        color: #245288;
        border: 1px solid #cde0f5;
    }

    .doc-name {
        font-weight: 800;
        color: var(--doc-title);
    }

    .doc-desc {
        color: var(--doc-muted);
        font-size: .82rem;
    }

    .doc-actions {
        display: inline-flex;
        gap: 6px;
        align-items: center;
    }

    .doc-btn {
        min-height: 40px;
        border-radius: 12px;
        border: 1px solid #d5e1ec;
        background: linear-gradient(180deg, #ffffff 0%, #f6faff 100%);
        color: #2d4a69;
        text-decoration: none;
        padding: 7px 12px;
        font-size: .82rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 12px 18px -24px rgba(15, 45, 82, .32);
        transition: all .2s ease;
    }

    .doc-btn:hover {
        background: linear-gradient(180deg, #ffffff 0%, #eef5fd 100%);
        color: #223f60;
        transform: translateY(-1px);
    }

    .doc-btn.primary {
        border-color: transparent;
        background: linear-gradient(135deg, var(--doc-primary) 0%, var(--doc-primary-dark) 100%);
        color: #fff;
        box-shadow: 0 18px 28px -24px rgba(37, 99, 235, .48);
    }

    .doc-btn.primary:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        color: #fff;
    }

    .doc-btn.danger {
        border-color: #f6c4c4;
        background: linear-gradient(180deg, #fff7f7 0%, #fff0f0 100%);
        color: #b83232;
    }

    .doc-btn.danger:hover {
        background: #ffeaea;
    }

    .doc-empty {
        display: grid;
        justify-items: center;
        gap: 12px;
        min-height: 340px;
        padding: 56px 22px;
        text-align: center;
        color: var(--doc-muted);
        background: linear-gradient(180deg, #fbfdff 0%, #f7fbff 100%);
    }

    .doc-empty i {
        width: 78px;
        height: 78px;
        display: inline-grid;
        place-items: center;
        font-size: 2rem;
        color: var(--doc-primary);
        border-radius: 24px;
        background: linear-gradient(145deg, #eef5ff 0%, #ddeafe 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .6);
        margin-bottom: 2px;
    }

    .doc-empty h3 {
        margin: 0;
        color: var(--doc-title);
        font-size: clamp(1.2rem, 2vw, 1.45rem);
        font-weight: 800;
        letter-spacing: -.03em;
    }

    .doc-empty p {
        margin: 0;
        max-width: 34ch;
        color: var(--doc-muted);
        font-size: .98rem;
        font-weight: 600;
        line-height: 1.7;
    }

    .doc-empty .doc-btn {
        min-width: auto;
        min-height: 50px;
        padding: 0 18px;
        border-radius: 16px;
        justify-content: center;
        margin-top: 2px;
        gap: 9px;
        font-size: .92rem;
        font-weight: 800;
    }

    .doc-empty .doc-btn.primary {
        border: 1px solid transparent;
        background: linear-gradient(135deg, var(--doc-primary) 0%, var(--doc-primary-dark) 100%);
        color: #fff;
        box-shadow: 0 18px 28px -22px rgba(37, 99, 235, .55);
    }

    .doc-empty .doc-btn.primary:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        color: #fff;
        box-shadow: 0 20px 30px -24px rgba(37, 99, 235, .55);
    }

    .doc-empty .doc-btn.primary i {
        width: auto;
        height: auto;
        display: inline-flex;
        place-items: unset;
        border-radius: 0;
        background: transparent;
        color: inherit;
        box-shadow: none;
    }

    .doc-pagination {
        padding: 12px 16px;
        border-top: 1px solid var(--doc-border);
        background: #fbfdff;
    }

    .doc-pagination nav {
        margin: 0;
    }

    .doc-notice {
        border: 1px solid #bcd9f8;
        border-radius: 14px;
        background: #edf6ff;
        color: #1b4e84;
        padding: 11px 13px;
        margin-bottom: 14px;
        font-weight: 600;
        box-shadow: 0 14px 22px -28px rgba(15, 45, 82, .32);
    }

    .doc-notice.error {
        border-color: #f5c0c0;
        background: #fff0f0;
        color: #a73030;
    }

    @media (max-width: 992px) {
        .doc-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .documents-page {
            padding: 12px 12px 22px;
        }

        .documents-head {
            flex-direction: column;
            align-items: stretch;
            padding-bottom: 18px;
        }

        .doc-head-main {
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .doc-back-btn {
            width: 100%;
            justify-content: center;
        }

        .doc-head-actions,
        .doc-head-btn {
            width: 100%;
        }

        .doc-title-row i {
            width: 46px;
            height: 46px;
        }

        .doc-count-badge {
            width: 100%;
            justify-content: center;
            text-align: center;
        }

        .doc-panel-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .doc-empty {
            min-height: 300px;
            padding: 42px 18px;
        }

        .doc-empty .doc-btn {
            width: 100%;
            min-width: 0;
        }

        .doc-notice {
            padding: 12px;
            line-height: 1.55;
        }
    }

    body.dark-mode .documents-page {
        --doc-bg: linear-gradient(180deg, #0f1f31 0%, #0d1a2b 100%);
        --doc-card: #12243b;
        --doc-surface-soft: #17304c;
        --doc-border: #2c4f79;
        --doc-border-strong: #40668f;
        --doc-title: #d5e7ff;
        --doc-text: #aec7e2;
        --doc-muted: #8ea9c6;
    }

    body.dark-mode .documents-head { border-bottom-color: #365a7b; }

    body.dark-mode .doc-head-eyebrow {
        border-color: #355978;
        background: rgba(19, 43, 69, .72);
        color: #d4e7fb;
    }

    body.dark-mode .doc-back-btn {
        border-color: #3f6284;
        color: #d2e6fb;
        background: #173450;
    }

    body.dark-mode .doc-back-btn:hover {
        border-color: #4d7499;
        color: #fff;
        background: #214666;
    }

    body.dark-mode .doc-title-row i {
        color: #77b7ff;
        background: linear-gradient(145deg, #173251 0%, #16304c 100%);
    }

    body.dark-mode .doc-title-row h1 { color: #e4f1ff; }

    body.dark-mode .doc-head-title p { color: #a9c2dc; }

    body.dark-mode .doc-count-badge {
        background: linear-gradient(90deg, #173251 0%, #123771 100%);
        color: #d8e9ff;
        border-color: #355978;
    }

    body.dark-mode .doc-head-btn.secondary {
        color: #d2e6fb;
        border-color: #3c5f81;
        background: linear-gradient(180deg, #183554 0%, #17324d 100%);
    }

    body.dark-mode .doc-head-btn.secondary:hover {
        color: #fff;
        background: #234a6d;
    }

    body.dark-mode .doc-table thead {
        background: #173251;
        border-bottom-color: #335b86;
    }

    body.dark-mode .doc-table th {
        color: #b9d3ee;
    }

    body.dark-mode .doc-table td {
        color: #c8def6;
        border-bottom-color: #264770;
    }

    body.dark-mode .doc-table tbody tr:hover {
        background: #173356;
    }

    body.dark-mode .doc-btn {
        background: linear-gradient(180deg, #173456 0%, #15314f 100%);
        border-color: #3f6795;
        color: #d3e8ff;
    }

    body.dark-mode .doc-btn:hover {
        background: #1d3f66;
        color: #f0f7ff;
    }
    body.dark-mode .doc-btn.primary {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        border-color: transparent;
        color: #fff;
    }

    body.dark-mode .doc-empty .doc-btn.primary {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        border-color: transparent;
        color: #fff;
    }

    body.dark-mode .doc-empty .doc-btn.primary i {
        background: transparent;
        color: inherit;
    }

    body.dark-mode .doc-btn.danger {
        background: #3a2327;
        border-color: #7b3b46;
        color: #ffc8d1;
    }

    body.dark-mode .doc-panel-head {
        background: linear-gradient(180deg, #173251 0%, #132b45 100%);
        border-bottom-color: #335b86;
    }

    body.dark-mode .doc-panel-head h2 {
        color: #e8f2ff;
    }

    body.dark-mode .doc-chip {
        background: #1a3656;
        border-color: #345b84;
        color: #cfe4ff;
    }

    body.dark-mode .doc-stat-icon {
        background: linear-gradient(145deg, #173251 0%, #16304c 100%);
        color: #92c5ff;
    }

    body.dark-mode .doc-stat.accent .doc-stat-icon {
        background: linear-gradient(145deg, #44311d 0%, #573b22 100%);
        color: #f9c88f;
    }

    body.dark-mode .doc-stat.success .doc-stat-icon {
        background: linear-gradient(145deg, #16372d 0%, #154031 100%);
        color: #8be0c1;
    }

    body.dark-mode .doc-pagination {
        background: #11263e;
        border-top-color: #2c4f79;
    }

    body.dark-mode .doc-notice {
        border-color: #3e6793;
        background: #173356;
        color: #cfe6ff;
    }

    body.dark-mode .doc-notice.error {
        border-color: #7b3b46;
        background: #3a2327;
        color: #ffd1d8;
    }

    body.dark-mode .doc-empty i {
        color: #9ac8ff;
        background: linear-gradient(145deg, #173251 0%, #16304c 100%);
    }

    body.dark-mode .doc-empty {
        background: linear-gradient(180deg, #12243b 0%, #102033 100%);
    }
</style>

<div class="documents-page">
    <div class="documents-shell">
        <div class="documents-head">
            <div class="doc-head-main">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center doc-back-btn">
                    <i class="fas fa-arrow-left me-2"></i>
                    <span class="d-none d-sm-inline">Retour</span>
                </a>
                <div class="doc-head-title">
                    <span class="doc-head-eyebrow"><i class="fas fa-shield-heart"></i> Archivage medical</span>
                    <div class="doc-title-row">
                        <i class="fas fa-folder-open"></i>
                        <h1>Documents</h1>
                        <span class="doc-count-badge">{{ number_format($totalDocuments ?? 0) }} documents</span>
                    </div>
                    <p>Stockage et organisation des documents medicaux</p>
                </div>
            </div>
            <div class="doc-head-actions">
                <a href="{{ route('documents.categories') }}" class="doc-head-btn secondary">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="{{ route('documents.upload') }}" class="doc-head-btn success">
                    <i class="fas fa-upload"></i> Televerser un document
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="doc-notice">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="doc-notice error">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            </div>
        @endif

        <div class="doc-stats">
            <div class="doc-stat accent">
                <div class="doc-stat-main">
                    <div class="doc-stat-value">{{ number_format($totalDocuments ?? 0) }}</div>
                    <p class="doc-stat-label">Documents stockes</p>
                </div>
                <span class="doc-stat-icon"><i class="fas fa-folder-open"></i></span>
            </div>
            <div class="doc-stat">
                <div class="doc-stat-main">
                    <div class="doc-stat-value">{{ number_format($totalCategories ?? 0) }}</div>
                    <p class="doc-stat-label">Categories actives</p>
                    <div class="doc-desc mt-2">{{ number_format($usedCategories ?? 0) }} utilisee(s)</div>
                </div>
                <span class="doc-stat-icon"><i class="fas fa-tags"></i></span>
            </div>
            <div class="doc-stat success">
                <div class="doc-stat-main">
                    <div class="doc-stat-value">{{ $totalBytesLabel }}</div>
                    <p class="doc-stat-label">Espace utilise</p>
                </div>
                <span class="doc-stat-icon"><i class="fas fa-database"></i></span>
            </div>
        </div>

        <div class="doc-panel">
            <div class="doc-panel-head">
                <div class="doc-panel-title">
                    <h2>Documents</h2>
                    <p>Centralisez les pieces medicales et administratives dans un espace clair et maitrise.</p>
                </div>
                <a href="{{ route('documents.upload') }}" class="doc-btn primary">
                    <i class="fas fa-plus"></i> Nouveau document
                </a>
            </div>

            @if($documents->count() > 0)
                <div class="doc-table-wrap">
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Patient</th>
                                <th>Categorie</th>
                                <th>Source</th>
                                <th>Type</th>
                                <th>Taille</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td>
                                        <div class="doc-name">{{ $document->nom_original }}</div>
                                        @if(!empty($document->description))
                                            <div class="doc-desc">{{ \Illuminate\Support\Str::limit($document->description, 70) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($document->display_patient_name)
                                            <div class="doc-name">{{ $document->display_patient_name }}</div>
                                            @if(!empty($document->display_patient_dossier))
                                                <div class="doc-desc">{{ $document->display_patient_dossier }}</div>
                                            @endif
                                        @else
                                            <span class="doc-chip">Non associe</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="doc-chip">{{ $document->categorie->nom ?? 'Non classe' }}</span>
                                    </td>
                                    <td>
                                        <span class="doc-chip">
                                            {{ ($document->source_document ?? 'telechargement') === 'scan_cabinet' ? 'Scan cabinet' : 'Televersement' }}
                                        </span>
                                    </td>
                                    <td>{{ strtoupper($document->extension ?? pathinfo($document->nom_original, PATHINFO_EXTENSION)) }}</td>
                                    <td>{{ $document->display_size_label }}</td>
                                    <td>{{ optional($document->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="doc-actions">
                                            <a href="{{ route('documents.show', $document) }}" class="doc-btn download" title="Télécharger" aria-label="Télécharger {{ $document->nom_original }}">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="doc-btn danger delete" title="Supprimer" aria-label="Supprimer {{ $document->nom_original }}">
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

                <div class="doc-pagination">
                    {{ $documents->links() }}
                </div>
            @else
                <div class="doc-empty">
                    <i class="fas fa-file-circle-plus"></i>
                    <h3>Aucun document pour le moment</h3>
                    <p>Commencez par televerser votre premier document pour structurer votre archivage medical et administratif.</p>
                    <a href="{{ route('documents.upload') }}" class="doc-btn primary">
                        <i class="fas fa-upload"></i> Ajouter le premier document
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
