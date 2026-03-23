@extends('layouts.app')

@section('title', 'Nouvelle Depense')

@section('content')
<style>
    :root {
        --dep-primary: #1f78c8;
        --dep-primary-strong: #145d99;
        --dep-accent: #0ea5e9;
        --dep-success: #0f9f77;
        --dep-danger: #dc2626;
        --dep-text: #17324c;
        --dep-muted: #64809b;
        --dep-border: #d8e4f0;
        --dep-border-strong: #cad9eb;
        --dep-bg: linear-gradient(180deg, #f4f9ff 0%, #eef5ff 100%);
        --dep-surface: rgba(255, 255, 255, 0.84);
        --dep-shadow: 0 24px 48px -38px rgba(15, 40, 65, 0.38);
    }

    .depense-form-page {
        width: 100%;
        max-width: none;
        padding: 10px 8px 104px;
    }

    .form-container {
        width: 100%;
        max-width: none;
        margin: 0 auto;
        display: grid;
        gap: 18px;
    }

    .dep-hero,
    .form-wrapper,
    .form-section,
    .dep-alert {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--dep-border);
        border-radius: 24px;
        box-shadow: var(--dep-shadow);
    }

    .dep-hero {
        padding: 20px;
        background:
            radial-gradient(circle at top right, rgba(31, 120, 200, 0.16) 0%, rgba(31, 120, 200, 0) 34%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 32%),
            var(--dep-bg);
    }

    .dep-hero::before,
    .form-wrapper::before,
    .form-section::before,
    .dep-alert::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .dep-hero > *,
    .form-wrapper > *,
    .form-section > *,
    .dep-alert > * {
        position: relative;
        z-index: 1;
    }

    .dep-hero-top {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
        gap: 18px;
        align-items: start;
    }

    .dep-breadcrumbs {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        color: #6e88a3;
        font-size: .82rem;
        font-weight: 700;
    }

    .dep-breadcrumbs a {
        color: var(--dep-primary);
        text-decoration: none;
    }

    .dep-breadcrumbs a:hover {
        color: var(--dep-primary-strong);
        text-decoration: none;
    }

    .dep-kicker {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        margin-top: 12px;
        border-radius: 999px;
        border: 1px solid rgba(31, 120, 200, 0.16);
        background: rgba(255, 255, 255, 0.64);
        color: var(--dep-primary-strong);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dep-title-row {
        display: flex;
        gap: 14px;
        align-items: center;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .dep-title-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.35rem;
        background: linear-gradient(135deg, var(--dep-primary) 0%, var(--dep-primary-strong) 100%);
        box-shadow: 0 18px 28px -20px rgba(31, 120, 200, 0.58);
        flex-shrink: 0;
    }

    .dep-title {
        margin: 0;
        color: var(--dep-text);
        font-size: clamp(1.7rem, 2.5vw, 2.35rem);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.04em;
    }

    .dep-subtitle {
        margin: 8px 0 0;
        max-width: 70ch;
        color: var(--dep-muted);
        font-size: .98rem;
        line-height: 1.62;
        font-weight: 600;
    }

    .dep-hero-side {
        display: grid;
        gap: 12px;
    }

    .dep-summary-card,
    .dep-actions-card {
        padding: 16px;
        border-radius: 20px;
        border: 1px solid rgba(202, 217, 235, 0.82);
        background: rgba(255, 255, 255, 0.72);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.74);
    }

    .dep-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .dep-summary-item {
        padding: 14px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid #dbe6f3;
    }

    .dep-summary-label {
        display: block;
        color: var(--dep-muted);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dep-summary-value {
        display: block;
        margin-top: 6px;
        color: var(--dep-text);
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.4;
    }

    .dep-actions-card h2 {
        margin: 0;
        color: var(--dep-text);
        font-size: 1rem;
        font-weight: 800;
    }

    .dep-actions-card p {
        margin: 8px 0 0;
        color: var(--dep-muted);
        font-size: .9rem;
        line-height: 1.56;
        font-weight: 600;
    }

    .dep-actions-inline {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    .dep-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #d7e3f1;
        background: #f8fbff;
        color: #55708d;
        font-size: .8rem;
        font-weight: 800;
    }

    .form-wrapper {
        background: var(--dep-surface);
        padding: 18px;
    }

    .form-section {
        margin-bottom: 18px;
        background: rgba(255, 255, 255, 0.86);
    }

    .form-section-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 0;
        padding: 16px 18px;
        border-bottom: 1px solid #eef3fb;
        background: rgba(248, 251, 255, 0.88);
    }

    .form-section-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e9f4ff;
        color: #153b84;
        border: 1px solid #c6dcf8;
        flex-shrink: 0;
    }

    .form-section-title {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .form-section-title h2 {
        font-size: 1.03rem;
        font-weight: 800;
        color: #153b84;
        margin: 0;
    }

    .form-section-title p {
        margin: 6px 0 0;
        color: var(--dep-muted);
        font-size: .9rem;
        line-height: 1.56;
        font-weight: 600;
    }

    .form-section-tag {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d7e4f8;
        background: #f7fbff;
        color: #587292;
        font-size: .78rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .form-section > .form-grid {
        padding: 18px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: clamp(14px, 1.8vw, 24px);
    }

    .form-group {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .form-label {
        font-size: .8rem;
        font-weight: 800;
        color: #4f6983;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-label .required {
        color: var(--dep-danger);
        margin-left: 4px;
    }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 0 16px;
        border: 1px solid var(--dep-border-strong);
        border-radius: 16px;
        font-size: .95rem;
        color: var(--dep-text);
        transition: all 0.2s;
        font-family: inherit;
        background: rgba(251, 253, 255, 0.96);
        min-height: 54px;
        width: 100%;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--dep-primary);
        box-shadow: 0 0 0 4px rgba(31, 120, 200, 0.1);
        transform: translateY(-.5px);
    }

    .form-textarea {
        padding: 14px 16px;
        resize: vertical;
        min-height: 132px;
    }

    .dep-form {
        display: grid;
        gap: 18px;
    }

    .dep-alert {
        width: fit-content;
        max-width: min(100%, 520px);
        background: rgba(243, 249, 255, 0.9);
        padding: 12px 14px;
    }

    .dep-alert .form-info {
        background: transparent;
        border: 0;
        padding: 0;
        margin: 0;
    }

    .form-info-text {
        color: var(--dep-primary-strong);
        font-size: .84rem;
        margin: 0;
        font-weight: 700;
        line-height: 1.55;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-error {
        background: #fff1f2;
        border-left: 4px solid var(--dep-danger);
        padding: 16px 18px;
        border-radius: 18px;
        margin-bottom: 0;
    }

    .form-error-title {
        color: var(--dep-danger);
        font-weight: 800;
        margin-bottom: 8px;
    }

    .form-error-list {
        margin: 0;
        padding-left: 20px;
        color: var(--dep-text);
    }

    .dep-footer-bar {
        position: sticky;
        bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 16px 18px;
        border: 1px solid var(--dep-border);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(14px);
        box-shadow: 0 22px 40px rgba(15, 23, 42, 0.08);
    }

    .dep-footer-meta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .dep-footer-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .depense-form-page .btn {
        min-height: 50px;
        padding: 0 18px;
        border: 1px solid transparent;
        border-radius: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        font-size: .92rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .depense-form-page .btn-submit {
        background: linear-gradient(135deg, var(--dep-primary) 0%, var(--dep-primary-strong) 100%);
        color: white;
        box-shadow: 0 18px 28px -22px rgba(31, 120, 200, 0.55);
    }

    .depense-form-page .btn-submit:hover {
        transform: translateY(-1px);
        color: white;
        box-shadow: 0 18px 30px rgba(15, 23, 42, 0.12);
    }

    .depense-form-page .btn-secondary {
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        border-color: #cfdef0;
        color: #3b5976;
    }

    .depense-form-page .btn-secondary:hover {
        color: var(--dep-primary-strong);
        border-color: rgba(31, 120, 200, 0.3);
        background: linear-gradient(180deg, #ffffff 0%, #ebf4fb 100%);
    }

    .depense-form-page .btn-ghost {
        background: #f7fbff;
        border-color: #d7e3f1;
        color: #55708d;
    }

    .depense-form-page .btn-ghost:hover {
        color: var(--dep-primary-strong);
        border-color: rgba(31, 120, 200, 0.26);
        background: #eef6ff;
    }

    .dep-stack-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: clamp(14px, 1.8vw, 24px);
    }

    .span-full {
        grid-column: 1 / -1;
    }

    body.dark-mode {
        --dep-text: #ebf4ff;
        --dep-muted: #a9c4df;
        --dep-border: #294863;
        --dep-border-strong: #355273;
    }

    body.dark-mode .depense-form-page {
        background: transparent;
    }

    body.dark-mode .dep-hero {
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.16) 0%, rgba(56, 189, 248, 0) 34%),
            radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 32%),
            linear-gradient(180deg, #11253b 0%, #0e2033 100%);
        border-color: #2a4660;
    }

    body.dark-mode .dep-hero::before,
    body.dark-mode .form-wrapper::before,
    body.dark-mode .form-section::before,
    body.dark-mode .dep-alert::before {
        background: linear-gradient(180deg, rgba(8, 18, 30, 0.12) 0%, rgba(8, 18, 30, 0) 100%);
    }

    body.dark-mode .form-wrapper,
    body.dark-mode .form-section,
    body.dark-mode .dep-alert,
    body.dark-mode .dep-summary-card,
    body.dark-mode .dep-actions-card,
    body.dark-mode .dep-summary-item,
    body.dark-mode .dep-footer-bar {
        background: linear-gradient(180deg, rgba(16, 33, 54, 0.94) 0%, rgba(13, 28, 46, 0.96) 100%);
        border-color: #294863;
        box-shadow: 0 20px 40px -32px rgba(0, 0, 0, 0.52);
    }

    body.dark-mode .form-section-header {
        border-bottom-color: #2b4562;
        background: rgba(18, 49, 79, 0.7);
    }

    body.dark-mode .form-section-icon {
        background: #16324f;
        color: #8ec5ff;
        border-color: #2f4f72;
    }

    body.dark-mode .dep-title,
    body.dark-mode .dep-actions-card h2,
    body.dark-mode .dep-summary-value,
    body.dark-mode .form-section-title h2,
    body.dark-mode .form-label,
    body.dark-mode .form-error-list {
        color: #ebf4ff;
    }

    body.dark-mode .dep-subtitle,
    body.dark-mode .dep-breadcrumbs,
    body.dark-mode .dep-actions-card p,
    body.dark-mode .dep-summary-label,
    body.dark-mode .form-section-title p,
    body.dark-mode .form-info-text,
    body.dark-mode .dep-footer-meta,
    body.dark-mode .form-section-tag {
        color: #a9c4df;
    }

    body.dark-mode .dep-kicker,
    body.dark-mode .dep-chip,
    body.dark-mode .form-section-tag,
    body.dark-mode .dep-summary-item {
        border-color: #355879;
        background: linear-gradient(180deg, rgba(23, 48, 76, 0.92) 0%, rgba(18, 38, 60, 0.94) 100%);
        color: #cfe5ff;
    }

    body.dark-mode .dep-breadcrumbs a {
        color: #8ec5ff;
    }

    body.dark-mode .form-input,
    body.dark-mode .form-select,
    body.dark-mode .form-textarea {
        background: #0d1a2b;
        border-color: #3b5d81;
        color: #e6efff;
    }

    body.dark-mode .form-input::placeholder,
    body.dark-mode .form-select::placeholder,
    body.dark-mode .form-textarea::placeholder {
        color: #8fa9c8;
    }

    body.dark-mode .form-input:focus,
    body.dark-mode .form-select:focus,
    body.dark-mode .form-textarea:focus {
        border-color: #63a9ff;
        box-shadow: 0 0 0 3px rgba(99, 169, 255, 0.22);
    }

    body.dark-mode .form-error {
        background: rgba(153, 27, 27, 0.3);
        border-left-color: #ef4444;
    }

    body.dark-mode .form-error-title {
        color: #fca5a5;
    }

    body.dark-mode .depense-form-page .btn-secondary,
    body.dark-mode .depense-form-page .btn-ghost {
        background: linear-gradient(180deg, #17304c 0%, #12253d 100%);
        border-color: #355273;
        color: #dce9f9;
    }

    body.dark-mode .depense-form-page .btn-secondary:hover,
    body.dark-mode .depense-form-page .btn-ghost:hover {
        background: linear-gradient(180deg, #1b3857 0%, #15304d 100%);
        border-color: #4a739a;
        color: #ffffff;
    }

    @media (max-width: 768px) {
        .depense-form-page {
            padding: 10px;
        }

        .dep-hero-top,
        .dep-summary-grid,
        .form-grid,
        .dep-stack-2 {
            grid-template-columns: 1fr;
        }

        .form-wrapper {
            padding: 16px;
        }

        .dep-footer-bar,
        .form-section-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .dep-footer-actions,
        .dep-footer-actions .btn,
        .dep-actions-inline,
        .dep-actions-inline .dep-chip,
        .depense-form-page .btn {
            width: 100%;
        }
    }
</style>

<div class="depense-form-page">
    <div class="form-container">
        <section class="dep-hero">
            <div class="dep-hero-top">
                <div>
                    <div class="dep-breadcrumbs">
                        <a href="{{ route('depenses.index') }}">D&eacute;penses</a>
                        <span><i class="fas fa-angle-right"></i></span>
                        <span>Nouvelle d&eacute;pense</span>
                    </div>
                    <span class="dep-kicker"><i class="fas fa-wallet"></i> Gestion financi&egrave;re</span>
                    <div class="dep-title-row">
                        <span class="dep-title-icon"><i class="fas fa-plus-circle"></i></span>
                        <div>
                            <h1 class="dep-title">Nouvelle D&eacute;pense</h1>
                            <p class="dep-subtitle">Enregistrez une d&eacute;pense du cabinet dans une interface plus fluide, mieux structur&eacute;e et plus coh&eacute;rente avec un SaaS m&eacute;dical premium, sans modifier vos champs ni votre logique existante.</p>
                        </div>
                    </div>
                </div>

                <div class="dep-hero-side">
                    <div class="dep-summary-card">
                        <div class="dep-summary-grid">
                            <div class="dep-summary-item">
                                <span class="dep-summary-label">Structure</span>
                                <span class="dep-summary-value">3 sections claires</span>
                            </div>
                            <div class="dep-summary-item">
                                <span class="dep-summary-label">Disposition</span>
                                <span class="dep-summary-value">Lecture rapide</span>
                            </div>
                            <div class="dep-summary-item">
                                <span class="dep-summary-label">Saisie</span>
                                <span class="dep-summary-value">Champs align&eacute;s</span>
                            </div>
                            <div class="dep-summary-item">
                                <span class="dep-summary-label">Action</span>
                                <span class="dep-summary-value">Footer sticky</span>
                            </div>
                        </div>
                    </div>
                    <div class="dep-actions-card">
                        <h2>Parcours de saisie simplifi&eacute;</h2>
                        <p>Compl&eacute;tez les informations g&eacute;n&eacute;rales, classez la d&eacute;pense puis renseignez les d&eacute;tails de paiement dans un flux plus a&eacute;r&eacute; et plus professionnel.</p>
                        <div class="dep-actions-inline">
                            <span class="dep-chip"><i class="fas fa-align-left"></i> Description prioritaire</span>
                            <span class="dep-chip"><i class="fas fa-table-columns"></i> Champs mieux group&eacute;s</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="form-wrapper">
            @if ($errors->any())
                <div class="form-error">
                    <div class="form-error-title">
                        <i class="fas fa-exclamation-circle"></i> Erreurs de validation
                    </div>
                    <ul class="form-error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('depenses.store') }}" class="dep-form">
                @csrf

                <div class="dep-alert">
                    <div class="form-info">
                        <p class="form-info-text">
                            <i class="fas fa-circle-info"></i>
                            <span>Tous les champs marqu&eacute;s d'un ast&eacute;risque (*) sont obligatoires.</span>
                        </p>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <div class="form-section-title">
                            <span class="form-section-icon"><i class="fas fa-clipboard-list"></i></span>
                            <div>
                                <h2>Informations G&eacute;n&eacute;rales</h2>
                                <p>Posez les bases de la d&eacute;pense avec une description claire, des d&eacute;tails complets et les donn&eacute;es cl&eacute;s de date et de montant.</p>
                            </div>
                        </div>
                        <span class="form-section-tag">Essentiel</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-group span-full">
                            <label class="form-label">
                                Description <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="description" 
                                class="form-input" 
                                placeholder="Ex: Fournitures m&eacute;dicales"
                                value="{{ old('description') }}"
                                required
                            >
                        </div>

                        <div class="form-group span-full">
                            <label class="form-label">D&eacute;tails</label>
                            <textarea 
                                name="details" 
                                class="form-textarea" 
                                placeholder="D&eacute;tails suppl&eacute;mentaires (optionnel)"
                            >{{ old('details') }}</textarea>
                        </div>

                        <div class="dep-stack-2 span-full">
                            <div class="form-group">
                                <label class="form-label">
                                    Date <span class="required">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="date_depense" 
                                    class="form-input"
                                    value="{{ old('date_depense', today()) }}"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Montant (DH) <span class="required">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    name="montant" 
                                    class="form-input" 
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0.01"
                                    value="{{ old('montant') }}"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <div class="form-section-title">
                            <span class="form-section-icon"><i class="fas fa-tags"></i></span>
                            <div>
                                <h2>Classification</h2>
                                <p>Classez la d&eacute;pense rapidement pour faciliter le suivi administratif, comptable et analytique du cabinet.</p>
                            </div>
                        </div>
                        <span class="form-section-tag">Organisation</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                Cat&eacute;gorie <span class="required">*</span>
                            </label>
                            <select name="categorie" class="form-select" required>
                                <option value="">-- S&eacute;lectionner --</option>
                                <option value="fournitures" {{ old('categorie') == 'fournitures' ? 'selected' : '' }}>Fournitures</option>
                                <option value="medicaments" {{ old('categorie') == 'medicaments' ? 'selected' : '' }}>M&eacute;dicaments</option>
                                <option value="loyer" {{ old('categorie') == 'loyer' ? 'selected' : '' }}>Loyer</option>
                                <option value="personnel" {{ old('categorie') == 'personnel' ? 'selected' : '' }}>Personnel</option>
                                <option value="utilites" {{ old('categorie') == 'utilites' ? 'selected' : '' }}>Utilit&eacute;s</option>
                                <option value="maintenance" {{ old('categorie') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="formation" {{ old('categorie') == 'formation' ? 'selected' : '' }}>Formation</option>
                                <option value="autre" {{ old('categorie') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Statut <span class="required">*</span>
                            </label>
                            <select name="statut" class="form-select" required>
                                <option value="">-- S&eacute;lectionner --</option>
                                <option value="enregistre" {{ old('statut') == 'enregistre' ? 'selected' : '' }}>Enregistr&eacute;e</option>
                                <option value="payee" {{ old('statut') == 'payee' ? 'selected' : '' }}>Pay&eacute;e</option>
                                <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En Attente</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <div class="form-section-title">
                            <span class="form-section-icon"><i class="fas fa-credit-card"></i></span>
                            <div>
                                <h2>D&eacute;tails de Paiement</h2>
                                <p>Compl&eacute;tez les informations utiles au suivi du r&egrave;glement, du b&eacute;n&eacute;ficiaire et des pi&egrave;ces justificatives.</p>
                            </div>
                        </div>
                        <span class="form-section-tag">Paiement</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">B&eacute;n&eacute;ficiaire</label>
                            <input 
                                type="text" 
                                name="beneficiaire" 
                                class="form-input" 
                                placeholder="Ex: Fournisseur ABC"
                                value="{{ old('beneficiaire') }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Num&eacute;ro de Facture</label>
                            <input 
                                type="text" 
                                name="facture_numero" 
                                class="form-input" 
                                placeholder="Ex: FAC-2026-001"
                                value="{{ old('facture_numero') }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mode de Paiement</label>
                            <input 
                                type="text" 
                                name="mode_paiement" 
                                class="form-input" 
                                placeholder="Ex: Ch&egrave;que, Virement, Esp&egrave;ces"
                                value="{{ old('mode_paiement') }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Date de Paiement</label>
                            <input 
                                type="date" 
                                name="date_paiement" 
                                class="form-input"
                                value="{{ old('date_paiement') }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="dep-footer-bar">
                    <div class="dep-footer-meta">
                        <span class="dep-chip"><i class="fas fa-shield-check"></i> Saisie structur&eacute;e</span>
                        <span class="dep-chip"><i class="fas fa-mobile-screen-button"></i> Responsive optimis&eacute;</span>
                    </div>

                    <div class="dep-footer-actions">
                        <a href="javascript:history.length > 1 ? history.back() : '{{ route('depenses.index') }}';" class="btn btn-ghost">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <a href="{{ route('depenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Enregistrer la D&eacute;pense
                    </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

