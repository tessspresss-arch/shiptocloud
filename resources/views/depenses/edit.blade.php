@extends('layouts.app')

@section('title', 'Modifier Depense')

@section('content')
<style>
    :root {
        --primary: #0284c7;
        --primary-dark: #0369a1;
        --secondary: #10b981;
        --danger: #ef4444;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-500: #6b7280;
        --gray-700: #374151;
        --gray-900: #111827;
    }

    .depense-form-page {
        padding: 16px clamp(10px, 1.7vw, 22px) 24px;
        background:
            radial-gradient(circle at right top, rgba(2, 132, 199, .10) 0%, transparent 34%),
            radial-gradient(circle at left bottom, rgba(16, 185, 129, .08) 0%, transparent 30%),
            linear-gradient(135deg, #f5f8fc 0%, #f9fbff 100%);
        min-height: 100%;
        border-radius: 18px;
        border: 1px solid #e3ecf7;
        box-shadow: 0 18px 30px -30px rgba(16, 57, 104, .85);
    }

    .form-container {
        width: 100%;
        max-width: none;
        margin: 0 auto;
    }

    .form-header {
        margin-bottom: 20px;
        background: #ffffffd1;
        border: 1px solid #dfe9f5;
        border-radius: 14px;
        padding: 16px 18px;
        backdrop-filter: blur(2px);
    }

    .form-header h1 {
        font-size: clamp(1.95rem, 2.6vw, 2.4rem);
        font-weight: 800;
        color: var(--gray-900);
        margin: 0;
        margin-bottom: 6px;
        letter-spacing: .15px;
    }

    .form-header p {
        color: var(--gray-500);
        margin: 0;
        font-size: 15px;
    }

    .form-wrapper {
        background: white;
        border-radius: 16px;
        border: 1px solid #d9e4f2;
        padding: clamp(18px, 2vw, 32px);
        box-shadow: 0 20px 26px -30px rgba(17, 57, 104, 0.72);
    }

    .form-section {
        margin-bottom: 32px;
        border: 1px solid #e5edf8;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .form-section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 0;
        padding: 0.82rem 0.95rem;
        border-bottom: 1px solid #eef3fb;
        background: #f8fbff;
    }

    .form-section-icon {
        font-size: 24px;
    }

    .form-section-icon {
        font-size: 20px;
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e9f4ff;
        color: #153b84;
        border: 1px solid #c6dcf8;
        flex-shrink: 0;
    }

    .form-section-header h2 {
        font-size: 1.03rem;
        font-weight: 800;
        color: #153b84;
        margin: 0;
    }

    .form-section > .form-grid {
        padding: 20px 0 0;
    }

    .form-grid {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: clamp(14px, 1.8vw, 24px);
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-label .required {
        color: var(--danger);
        margin-left: 4px;
    }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 12px 14px;
        border: 1px solid #c7d5e8;
        border-radius: 10px;
        font-size: 14px;
        color: var(--gray-700);
        transition: all 0.2s;
        font-family: inherit;
        background: #fbfdff;
        min-height: 45px;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.14);
        transform: translateY(-.5px);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .depense-form-page .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .depense-form-page .btn-submit {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 10px 18px -14px rgba(2, 132, 199, .7);
    }

    .depense-form-page .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
    }

    .depense-form-page .btn-cancel {
        background: #eef4fb;
        border: 1px solid #cad9ec;
        color: #2d4a69;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .depense-form-page .btn-cancel:hover {
        background: #e6eef8;
    }

    .form-error {
        background: #fee2e2;
        border-left: 4px solid var(--danger);
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
    }

    .form-error-title {
        color: var(--danger);
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-error-list {
        margin: 0;
        padding-left: 20px;
        color: var(--gray-700);
    }

    .form-error-list li {
        margin-bottom: 4px;
    }

    .form-info {
        background: #dbeafe;
        border-left: 4px solid var(--primary);
        padding: 14px 16px;
        border-radius: 10px;
        margin-bottom: 24px;
    }

    .form-info-text {
        color: var(--primary);
        font-size: 13px;
        margin: 0;
    }

    /* Dark mode */
    body.dark-mode .depense-form-page {
        background:
            radial-gradient(circle at right top, rgba(56, 189, 248, .08) 0%, transparent 34%),
            radial-gradient(circle at left bottom, rgba(16, 185, 129, .07) 0%, transparent 30%),
            linear-gradient(135deg, #0f1b2d 0%, #0d1727 100%);
        border-color: #2e4c6d;
        box-shadow: 0 18px 34px -30px rgba(0, 0, 0, 0.9);
    }

    body.dark-mode .form-header,
    body.dark-mode .form-wrapper {
        background: #112338;
        border-color: #31506f;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.32);
    }

    body.dark-mode .form-header h1,
    body.dark-mode .form-section-header h2,
    body.dark-mode .form-label {
        color: #e6efff;
    }

    body.dark-mode .form-header p,
    body.dark-mode .form-error-list,
    body.dark-mode .form-info-text {
        color: #9fb7d4;
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

    body.dark-mode .form-section {
        background: #112338;
        border-color: #31506f;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.24);
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

    body.dark-mode .form-info {
        background: #15314d;
        border-left-color: #3ea4ff;
    }

    body.dark-mode .form-error {
        background: rgba(153, 27, 27, 0.3);
        border-left-color: #ef4444;
    }

    body.dark-mode .form-error-title {
        color: #fca5a5;
    }

    body.dark-mode .form-actions {
        border-top-color: #2a455f;
    }

    body.dark-mode .depense-form-page .btn-cancel {
        background: #1d3654;
        border-color: #36577b;
        color: #d8e8fd;
    }

    body.dark-mode .depense-form-page .btn-cancel:hover {
        background: #2b486c;
        color: #ffffff;
    }

    @media (max-width: 768px) {
        .depense-form-page {
            padding: 10px;
        }

        .form-wrapper {
            padding: 16px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-section > .form-grid {
            padding-top: 16px;
        }

        .form-actions {
            flex-direction: column;
        }

        .depense-form-page .btn {
            width: 100%;
        }
    }

    @media (min-width: 1400px) {
        .depense-form-page {
            padding-inline: clamp(16px, 1.8vw, 30px);
        }
    }
</style>

<div class="depense-form-page">
    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <h1><i class="fas fa-pen-to-square"></i> Modifier D&eacute;pense</h1>
            <p>Mettre &agrave; jour les informations de la d&eacute;pense</p>
        </div>

        <!-- Form -->
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

            <form method="POST" action="{{ route('depenses.update', $depense->id) }}">
                @csrf
                @method('PUT')

                <!-- Info -->
                <div class="form-info">
                    <p class="form-info-text">
                        <i class="fas fa-info-circle"></i> Tous les champs marqu&eacute;s d'un ast&eacute;risque (*) sont obligatoires
                    </p>
                </div>

                <!-- Section 1: Informations generales -->
                <div class="form-section">
                    <div class="form-section-header">
                        <span class="form-section-icon"><i class="fas fa-clipboard-list"></i></span>
                        <h2>Informations G&eacute;n&eacute;rales</h2>
                    </div>

                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">
                                Description <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="description" 
                                class="form-input" 
                                placeholder="Ex: Fournitures m&eacute;dicales"
                                value="{{ old('description', $depense->description) }}"
                                required
                            >
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">D&eacute;tails</label>
                            <textarea 
                                name="details" 
                                class="form-textarea" 
                                placeholder="D&eacute;tails suppl&eacute;mentaires (optionnel)"
                            >{{ old('details', $depense->details) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Date <span class="required">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="date_depense" 
                                class="form-input"
                                value="{{ old('date_depense', $depense->date_depense->format('Y-m-d')) }}"
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
                                value="{{ old('montant', $depense->montant) }}"
                                required
                            >
                        </div>
                    </div>
                </div>

                <!-- Section 2: Classification -->
                <div class="form-section">
                    <div class="form-section-header">
                        <span class="form-section-icon"><i class="fas fa-tags"></i></span>
                        <h2>Classification</h2>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                Cat&eacute;gorie <span class="required">*</span>
                            </label>
                            <select name="categorie" class="form-select" required>
                                <option value="">-- S&eacute;lectionner --</option>
                                <option value="fournitures" {{ old('categorie', $depense->categorie) == 'fournitures' ? 'selected' : '' }}>Fournitures</option>
                                <option value="medicaments" {{ old('categorie', $depense->categorie) == 'medicaments' ? 'selected' : '' }}>M&eacute;dicaments</option>
                                <option value="loyer" {{ old('categorie', $depense->categorie) == 'loyer' ? 'selected' : '' }}>Loyer</option>
                                <option value="personnel" {{ old('categorie', $depense->categorie) == 'personnel' ? 'selected' : '' }}>Personnel</option>
                                <option value="utilites" {{ old('categorie', $depense->categorie) == 'utilites' ? 'selected' : '' }}>Utilit&eacute;s</option>
                                <option value="maintenance" {{ old('categorie', $depense->categorie) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="formation" {{ old('categorie', $depense->categorie) == 'formation' ? 'selected' : '' }}>Formation</option>
                                <option value="autre" {{ old('categorie', $depense->categorie) == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Statut <span class="required">*</span>
                            </label>
                            <select name="statut" class="form-select" required>
                                <option value="">-- S&eacute;lectionner --</option>
                                <option value="enregistre" {{ old('statut', $depense->statut) == 'enregistre' ? 'selected' : '' }}>Enregistr&eacute;e</option>
                                <option value="payee" {{ old('statut', $depense->statut) == 'payee' ? 'selected' : '' }}>Pay&eacute;e</option>
                                <option value="en_attente" {{ old('statut', $depense->statut) == 'en_attente' ? 'selected' : '' }}>En Attente</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Details de Paiement -->
                <div class="form-section">
                    <div class="form-section-header">
                        <span class="form-section-icon"><i class="fas fa-credit-card"></i></span>
                        <h2>D&eacute;tails de Paiement</h2>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">B&eacute;n&eacute;ficiaire</label>
                            <input 
                                type="text" 
                                name="beneficiaire" 
                                class="form-input" 
                                placeholder="Ex: Fournisseur ABC"
                                value="{{ old('beneficiaire', $depense->beneficiaire) }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Num&eacute;ro de Facture</label>
                            <input 
                                type="text" 
                                name="facture_numero" 
                                class="form-input" 
                                placeholder="Ex: FAC-2026-001"
                                value="{{ old('facture_numero', $depense->facture_numero) }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mode de Paiement</label>
                            <input 
                                type="text" 
                                name="mode_paiement" 
                                class="form-input" 
                                placeholder="Ex: Ch&egrave;que, Virement, Esp&egrave;ces"
                                value="{{ old('mode_paiement', $depense->mode_paiement) }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Date de Paiement</label>
                            <input 
                                type="date" 
                                name="date_paiement" 
                                class="form-input"
                                value="{{ old('date_paiement', $depense->date_paiement?->format('Y-m-d')) }}"
                            >
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Mettre &agrave; jour
                    </button>
                    <a href="{{ route('depenses.index') }}" class="btn btn-cancel">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

