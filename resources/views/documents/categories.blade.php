@extends('layouts.app')

@section('title', 'Catégories de documents')

@section('content')
<style>
    .doc-categories-page {
        --dc-bg: linear-gradient(180deg, #f3f8ff 0%, #f8fbff 100%);
        --dc-card: #ffffff;
        --dc-border: #d9e6f5;
        --dc-title: #102b4b;
        --dc-text: #405a7d;
        --dc-muted: #6e83a0;
        width: 100%;
        max-width: none;
        padding: 14px 16px 24px;
        background: var(--dc-bg);
        border: 1px solid #dfeaf7;
        border-radius: 18px;
        box-shadow: 0 20px 30px -32px rgba(16, 57, 104, .9);
    }

    .doc-categories-shell {
        width: 100%;
        max-width: none;
    }

    .doc-categories-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        padding: 2px 0 18px;
        border-bottom: 1px solid #dce8f5;
    }

    .doc-cat-head-main {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .doc-cat-back-btn {
        padding: 7px 14px;
        white-space: nowrap;
        border-color: #c8d7e7;
        color: #456281;
        font-weight: 700;
    }

    .doc-cat-back-btn:hover {
        border-color: #b8cce1;
        background: #edf4fb;
        color: #1f3d5e;
    }

    .doc-cat-head-title {
        min-width: 0;
    }

    .doc-cat-title-row {
        display: flex;
        align-items: center;
        gap: 11px;
        flex-wrap: wrap;
    }

    .doc-cat-title-row i {
        font-size: 1.6rem;
        color: #3b82f6;
    }

    .doc-cat-title-row h1 {
        margin: 0;
        color: #1e3a8a;
        font-size: clamp(1.45rem, 2.2vw, 1.95rem);
        font-weight: 700;
        line-height: 1.1;
    }

    .doc-cat-head-title p {
        margin: 7px 0 0;
        color: #5f7896;
        font-size: .95rem;
        font-weight: 600;
    }

    .doc-cat-count-badge {
        background: linear-gradient(90deg, #3b82f6 60%, #1e3a8a 100%);
        color: #fff;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: .9rem;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(59, 130, 246, .12);
        white-space: nowrap;
    }

    .doc-cat-head-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .doc-cat-head-btn {
        height: 40px;
        border-radius: 10px;
        padding: 0 16px;
        border: 1px solid transparent;
        font-size: .92rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        transition: all .2s ease;
    }

    .doc-cat-head-btn.secondary {
        background: #eef2f7;
        border-color: #dbe5f1;
        color: #486482;
    }

    .doc-cat-head-btn.secondary:hover {
        background: #e3ebf4;
        color: #2c4b6c;
    }

    .doc-cat-head-btn.success {
        background: #11b47a;
        border-color: #11b47a;
        color: #fff;
    }

    .doc-cat-head-btn.success:hover {
        background: #0fa06d;
        border-color: #0fa06d;
        color: #fff;
    }

    .doc-cat-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .doc-cat-stat {
        background: var(--dc-card);
        border: 1px solid var(--dc-border);
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 16px 22px -30px rgba(16, 57, 104, .9);
        position: relative;
        overflow: hidden;
    }

    .doc-cat-stat::before {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 4px;
        background: #0b7ac7;
    }

    .doc-cat-stat.warning::before { background: #f59e0b; }
    .doc-cat-stat.success::before { background: #10b981; }

    .doc-cat-stat-value {
        margin: 2px 0 0;
        color: var(--dc-title);
        font-size: clamp(1.8rem, 2.4vw, 2.2rem);
        font-weight: 900;
        line-height: 1;
    }

    .doc-cat-stat-label {
        margin: 10px 0 0;
        color: var(--dc-text);
        font-size: .98rem;
        font-weight: 600;
    }

    .doc-cat-tools {
        margin-bottom: 14px;
        background: var(--dc-card);
        border: 1px solid var(--dc-border);
        border-radius: 16px;
        box-shadow: 0 18px 24px -30px rgba(16, 57, 104, .9);
        overflow: hidden;
    }

    .doc-cat-tools-head {
        padding: 14px 16px;
        border-bottom: 1px solid var(--dc-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: linear-gradient(180deg, #f6faff 0%, #eef6ff 100%);
    }

    .doc-cat-tools-head h2 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 900;
        color: var(--dc-title);
    }

    .doc-cat-tools-head p {
        margin: 4px 0 0;
        color: var(--dc-text);
        font-size: .92rem;
        font-weight: 600;
    }

    .doc-cat-tools-body {
        padding: 16px;
    }

    .doc-cat-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .doc-cat-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .doc-cat-field.span-2 {
        grid-column: span 2;
    }

    .doc-cat-field label {
        color: var(--dc-title);
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .doc-cat-input,
    .doc-cat-select,
    .doc-cat-textarea {
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid #d7e5f5;
        background: #f9fbff;
        color: #143454;
        font-size: .95rem;
    }

    .doc-cat-textarea {
        min-height: 92px;
        resize: vertical;
    }

    .doc-cat-toggles {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 2px;
    }

    .doc-cat-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 12px;
        border: 1px solid #dce8f5;
        background: #f8fbff;
        color: #36516f;
        font-size: .86rem;
        font-weight: 700;
    }

    .doc-cat-toggle input {
        margin: 0;
    }

    .doc-cat-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    .doc-cat-btn {
        min-height: 42px;
        padding: 0 16px;
        border: 1px solid transparent;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .9rem;
        font-weight: 800;
        text-decoration: none;
    }

    .doc-cat-btn.primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
    }

    .doc-cat-btn.secondary {
        background: #f2f6fb;
        border-color: #d9e6f5;
        color: #3b5b7f;
    }

    .doc-cat-card-form {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px dashed #dbe6f3;
    }

    .doc-cat-panel {
        background: var(--dc-card);
        border: 1px solid var(--dc-border);
        border-radius: 16px;
        box-shadow: 0 20px 24px -30px rgba(16, 57, 104, .9);
        overflow: hidden;
    }

    .doc-cat-panel-head {
        padding: 14px 16px;
        border-bottom: 1px solid var(--dc-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: linear-gradient(180deg, #f6faff 0%, #eef6ff 100%);
    }

    .doc-cat-panel-head h2 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 900;
        color: var(--dc-title);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .doc-cat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 12px;
        padding: 14px 16px 16px;
    }

    .doc-cat-card {
        border: 1px solid #d7e6f6;
        border-radius: 13px;
        background: #fff;
        padding: 14px;
        box-shadow: 0 12px 22px -28px rgba(16, 57, 104, .9);
    }

    .doc-cat-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .doc-cat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }

    .doc-cat-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 9px;
        font-size: .74rem;
        font-weight: 800;
    }

    .doc-cat-status.on {
        color: #0f7d4f;
        background: #d8f7e8;
    }

    .doc-cat-status.off {
        color: #b4212f;
        background: #ffe0e3;
    }

    .doc-cat-name {
        margin: 0;
        color: var(--dc-title);
        font-size: 1rem;
        font-weight: 800;
    }

    .doc-cat-desc {
        margin: 6px 0 0;
        color: var(--dc-muted);
        font-size: .86rem;
    }

    .doc-cat-meta {
        margin-top: 11px;
        padding-top: 10px;
        border-top: 1px dashed #d6e5f5;
        display: grid;
        gap: 6px;
    }

    .doc-cat-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        color: #466587;
        font-size: .82rem;
        font-weight: 700;
    }

    .doc-cat-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 5px 9px;
        font-size: .72rem;
        font-weight: 800;
        color: #8a6510;
        background: #fff1cc;
    }

    .doc-cat-empty {
        text-align: center;
        padding: 44px 18px 50px;
        color: var(--dc-muted);
    }

    .doc-cat-empty i {
        font-size: 2.2rem;
        color: #91aac7;
        margin-bottom: 10px;
    }

    .doc-cat-empty p {
        margin: 0;
    }

    .doc-cat-empty a {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 11px;
        text-decoration: none;
        color: #0b7ac7;
        font-weight: 800;
    }

    @media (max-width: 992px) {
        .doc-cat-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .doc-categories-page {
            padding: 10px 10px 20px;
        }

        .doc-categories-head {
            flex-direction: column;
            align-items: stretch;
            padding-bottom: 14px;
        }

        .doc-cat-head-main {
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .doc-cat-back-btn {
            width: 100%;
            justify-content: center;
        }

        .doc-cat-head-actions,
        .doc-cat-head-btn {
            width: 100%;
        }

        .doc-cat-panel-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    body.dark-mode .doc-categories-page {
        --dc-bg: linear-gradient(180deg, #0f1f31 0%, #0d1a2b 100%);
        --dc-card: #12243b;
        --dc-border: #2c4f79;
        --dc-title: #d5e7ff;
        --dc-text: #aec7e2;
        --dc-muted: #8ea9c6;
    }

    body.dark-mode .doc-categories-head { border-bottom-color: #365a7b; }

    body.dark-mode .doc-cat-back-btn {
        border-color: #3f6284;
        color: #d2e6fb;
        background: #173450;
    }

    body.dark-mode .doc-cat-back-btn:hover {
        border-color: #4d7499;
        color: #fff;
        background: #214666;
    }

    body.dark-mode .doc-cat-title-row i { color: #77b7ff; }
    body.dark-mode .doc-cat-title-row h1 { color: #e4f1ff; }
    body.dark-mode .doc-cat-head-title p { color: #a9c2dc; }

    body.dark-mode .doc-cat-count-badge {
        background: linear-gradient(90deg, #1f5fb3 60%, #123771 100%);
    }

    body.dark-mode .doc-cat-head-btn.secondary {
        color: #d2e6fb;
        border-color: #3c5f81;
        background: #1a3855;
    }

    body.dark-mode .doc-cat-head-btn.secondary:hover {
        color: #fff;
        background: #234a6d;
    }

    body.dark-mode .doc-cat-card {
        border-color: #2f4f73;
        background: #132a43;
    }

    body.dark-mode .doc-cat-meta {
        border-top-color: #30557d;
    }

    body.dark-mode .doc-cat-meta-row {
        color: #c8def6;
    }

    body.dark-mode .doc-cat-empty i {
        color: #9ab6d6;
    }
</style>

<div class="doc-categories-page">
    <div class="doc-categories-shell">
        <div class="doc-categories-head">
            <div class="doc-cat-head-main">
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center doc-cat-back-btn">
                    <i class="fas fa-arrow-left me-2"></i>
                    <span class="d-none d-sm-inline">Retour</span>
                </a>
                <div class="doc-cat-head-title">
                    <div class="doc-cat-title-row">
                        <i class="fas fa-folder-tree"></i>
                        <h1>Catégories de documents</h1>
                        <span class="doc-cat-count-badge">{{ $categories->count() }} catégories</span>
                    </div>
                    <p>Gestion centralisée des catégories de documents médicaux.</p>
                </div>
            </div>
            <div class="doc-cat-head-actions">
                <a href="{{ route('documents.index') }}" class="doc-cat-head-btn secondary">
                    <i class="fas fa-folder-open"></i> Voir documents
                </a>
                <a href="{{ route('documents.upload') }}" class="doc-cat-head-btn success">
                    <i class="fas fa-upload"></i> Téléverser un document
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-3">
                <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-3">
                <i class="fas fa-circle-exclamation me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <div class="doc-cat-stats">
            <div class="doc-cat-stat">
                <div class="doc-cat-stat-value">{{ number_format($categories->count()) }}</div>
                <p class="doc-cat-stat-label">Catégories totales</p>
            </div>
            <div class="doc-cat-stat warning">
                <div class="doc-cat-stat-value">{{ number_format($totalDocuments) }}</div>
                <p class="doc-cat-stat-label">Documents classes</p>
            </div>
            <div class="doc-cat-stat success">
                <div class="doc-cat-stat-value">{{ number_format($activeCategories) }}</div>
                <p class="doc-cat-stat-label">Catégories actives</p>
            </div>
            <div class="doc-cat-stat">
                <div class="doc-cat-stat-value">{{ number_format($patientCategories) }}</div>
                <p class="doc-cat-stat-label">Catégories patient</p>
            </div>
        </div>

        <div class="doc-cat-tools">
            <div class="doc-cat-tools-head">
                <div>
                    <h2>Ajouter une catégorie</h2>
                    <p>Créez vos catégories patient ou administratives directement depuis ce module.</p>
                </div>
                <span class="doc-cat-tag"><i class="fas fa-plus-circle"></i> Gestion dynamique</span>
            </div>
            <div class="doc-cat-tools-body">
                <form action="{{ route('documents.categories.store') }}" method="POST">
                    @csrf
                    <div class="doc-cat-form-grid">
                        <div class="doc-cat-field">
                            <label for="nom">Nom</label>
                            <input id="nom" type="text" name="nom" class="doc-cat-input" value="{{ old('nom') }}" placeholder="Ex : Consentement opératoire" required>
                        </div>
                        <div class="doc-cat-field span-2">
                            <label for="description">Description</label>
                            <input id="description" type="text" name="description" class="doc-cat-input" value="{{ old('description') }}" placeholder="Usage de la categorie et type de document attendu.">
                        </div>
                        <div class="doc-cat-field">
                            <label for="couleur">Couleur</label>
                            <input id="couleur" type="text" name="couleur" class="doc-cat-input" value="{{ old('couleur', '#3b82f6') }}" placeholder="#3b82f6">
                        </div>
                        <div class="doc-cat-field">
                            <label for="icone">Icône</label>
                            <input id="icone" type="text" name="icone" class="doc-cat-input" value="{{ old('icone', 'fas fa-folder') }}" placeholder="fas fa-folder">
                        </div>
                        <div class="doc-cat-field">
                            <label for="duree_conservation_ans">Conservation (ans)</label>
                            <input id="duree_conservation_ans" type="number" min="0" max="99" name="duree_conservation_ans" class="doc-cat-input" value="{{ old('duree_conservation_ans', 10) }}">
                        </div>
                        <div class="doc-cat-field">
                            <label for="ordre">Ordre</label>
                            <input id="ordre" type="number" min="0" max="999" name="ordre" class="doc-cat-input" value="{{ old('ordre', 0) }}">
                        </div>
                    </div>

                    <div class="doc-cat-actions" style="justify-content: space-between;">
                        <div class="doc-cat-toggles">
                            <label class="doc-cat-toggle">
                                <input type="checkbox" name="est_document_patient" value="1" {{ old('est_document_patient', '1') ? 'checked' : '' }}>
                                Catégorie de document patient
                            </label>
                            <label class="doc-cat-toggle">
                                <input type="checkbox" name="confidentiel" value="1" {{ old('confidentiel') ? 'checked' : '' }}>
                                Confidentiel
                            </label>
                            <label class="doc-cat-toggle">
                                <input type="checkbox" name="actif" value="1" {{ old('actif', '1') ? 'checked' : '' }}>
                                Activée
                            </label>
                        </div>
                        <button type="submit" class="doc-cat-btn primary">
                            <i class="fas fa-plus"></i> Ajouter la catégorie
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="doc-cat-panel">
            <div class="doc-cat-panel-head">
                <h2><i class="fas fa-tags"></i> Liste des catégories</h2>
                <span class="doc-cat-tag">
                    <i class="fas fa-shield-alt"></i> {{ number_format($confidentialCategories) }} confidentielles
                </span>
            </div>

            @if($categories->count() > 0)
                <div class="doc-cat-grid">
                    @foreach($categories as $category)
                        @php
                            $catColor = $category->couleur ?: '#3b82f6';
                            $catIcon = $category->icone ?: 'fas fa-folder';
                            $docCount = (int) ($documentsByCategory[$category->id] ?? 0);
                        @endphp
                        <article class="doc-cat-card" style="border-top: 3px solid {{ $catColor }};">
                            <div class="doc-cat-card-head">
                                <span class="doc-cat-icon" style="background: {{ $catColor }};">
                                    <i class="{{ $catIcon }}"></i>
                                </span>
                                <span class="doc-cat-status {{ $category->actif ? 'on' : 'off' }}">
                                    {{ $category->actif ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <h3 class="doc-cat-name">{{ $category->nom }}</h3>
                            <p class="doc-cat-desc">{{ $category->description ?: 'Aucune description fournie.' }}</p>
                            <div class="doc-cat-meta">
                                <div class="doc-cat-meta-row">
                                    <span>Documents</span>
                                    <span>{{ number_format($docCount) }}</span>
                                </div>
                                <div class="doc-cat-meta-row">
                                    <span>Conservation</span>
                                    <span>{{ (int) ($category->duree_conservation_ans ?? 0) }} ans</span>
                                </div>
                                <div class="doc-cat-meta-row">
                                    <span>Sécurité</span>
                                    <span>{{ $category->confidentiel ? 'Confidentiel' : 'Standard' }}</span>
                                </div>
                                <div class="doc-cat-meta-row">
                                    <span>Usage</span>
                                    <span>{{ $category->est_document_patient ? 'Dossier patient' : 'Général' }}</span>
                                </div>
                            </div>
                            <form action="{{ route('documents.categories.update', $category) }}" method="POST" class="doc-cat-card-form">
                                @csrf
                                @method('PUT')
                                <div class="doc-cat-form-grid">
                                    <div class="doc-cat-field">
                                        <label for="nom_{{ $category->id }}">Nom</label>
                                        <input id="nom_{{ $category->id }}" type="text" name="nom" class="doc-cat-input" value="{{ $category->nom }}" required>
                                    </div>
                                    <div class="doc-cat-field span-2">
                                        <label for="description_{{ $category->id }}">Description</label>
                                        <input id="description_{{ $category->id }}" type="text" name="description" class="doc-cat-input" value="{{ $category->description }}">
                                    </div>
                                    <div class="doc-cat-field">
                                        <label for="couleur_{{ $category->id }}">Couleur</label>
                                        <input id="couleur_{{ $category->id }}" type="text" name="couleur" class="doc-cat-input" value="{{ $category->couleur }}">
                                    </div>
                                    <div class="doc-cat-field">
                                        <label for="icone_{{ $category->id }}">Icone</label>
                                        <input id="icone_{{ $category->id }}" type="text" name="icone" class="doc-cat-input" value="{{ $category->icone }}">
                                    </div>
                                    <div class="doc-cat-field">
                                        <label for="duree_{{ $category->id }}">Conservation</label>
                                        <input id="duree_{{ $category->id }}" type="number" min="0" max="99" name="duree_conservation_ans" class="doc-cat-input" value="{{ (int) ($category->duree_conservation_ans ?? 0) }}">
                                    </div>
                                    <div class="doc-cat-field">
                                        <label for="ordre_{{ $category->id }}">Ordre</label>
                                        <input id="ordre_{{ $category->id }}" type="number" min="0" max="999" name="ordre" class="doc-cat-input" value="{{ (int) ($category->ordre ?? 0) }}">
                                    </div>
                                </div>
                                <div class="doc-cat-actions" style="justify-content: space-between;">
                                    <div class="doc-cat-toggles">
                                        <label class="doc-cat-toggle">
                                            <input type="checkbox" name="est_document_patient" value="1" {{ $category->est_document_patient ? 'checked' : '' }}>
                                            Dossier patient
                                        </label>
                                        <label class="doc-cat-toggle">
                                            <input type="checkbox" name="confidentiel" value="1" {{ $category->confidentiel ? 'checked' : '' }}>
                                            Confidentiel
                                        </label>
                                        <label class="doc-cat-toggle">
                                            <input type="checkbox" name="actif" value="1" {{ $category->actif ? 'checked' : '' }}>
                                            Activée
                                        </label>
                                    </div>
                                    <button type="submit" class="doc-cat-btn secondary">
                                        <i class="fas fa-save"></i> Enregistrer
                                    </button>
                                </div>
                            </form>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="doc-cat-empty">
                    <i class="fas fa-inbox"></i>
                    <p>Aucune catégorie disponible.</p>
                    <a href="{{ route('documents.upload') }}">
                        <i class="fas fa-upload"></i> Téléverser un document
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
