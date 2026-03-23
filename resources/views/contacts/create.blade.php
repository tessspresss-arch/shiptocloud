@extends('layouts.app')

@section('title', 'Nouveau Contact')

@section('content')
<style>
    :root {
        --primary: #0284c7;
        --primary-dark: #0369a1;
        --danger: #ef4444;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-500: #6b7280;
        --gray-700: #374151;
        --gray-900: #111827;
    }

    .contact-create-page {
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

    .contact-create-shell {
        width: 100%;
        max-width: none;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 20px;
        background: #ffffffd1;
        border: 1px solid #dfe9f5;
        border-radius: 14px;
        padding: 16px 18px;
        backdrop-filter: blur(2px);
    }

    .page-header h1 {
        font-size: clamp(1.95rem, 2.6vw, 2.4rem);
        font-weight: 800;
        color: var(--gray-900);
        margin: 0 0 6px;
        letter-spacing: .15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .page-header p {
        color: var(--gray-500);
        margin: 0;
        font-size: 15px;
    }

    .contact-form-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #d9e4f2;
        box-shadow: 0 20px 26px -30px rgba(17, 57, 104, 0.72);
        overflow: hidden;
    }

    .form-section {
        border-bottom: 1px solid var(--gray-200);
        padding: clamp(16px, 1.9vw, 26px);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: calc(-1 * clamp(16px, 1.9vw, 26px)) calc(-1 * clamp(16px, 1.9vw, 26px)) 18px;
        padding: 0.82rem clamp(16px, 1.9vw, 26px);
        border-bottom: 1px solid #eef3fb;
        background: #f8fbff;
    }

    .section-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        border: 1px solid #c9def6;
        background: #e7f3ff;
        color: #153b84;
        flex-shrink: 0;
    }

    .section-header h2 {
        font-size: 1.03rem;
        letter-spacing: 0;
        margin: 0;
        color: #153b84;
        font-weight: 800;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: clamp(12px, 1.6vw, 20px);
        margin-bottom: clamp(12px, 1.4vw, 18px);
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--gray-700);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.55px;
    }

    .required {
        color: var(--danger);
        margin-left: 4px;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #c7d5e8;
        border-radius: 10px;
        font-size: 14px;
        color: var(--gray-700);
        transition: all .2s ease;
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

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: #94a3b8;
    }

    .form-textarea {
        resize: vertical;
        min-height: 110px;
    }

    .form-input.error,
    .form-select.error,
    .form-textarea.error {
        border-color: #ef4444;
        background: #fff4f4;
    }

    .error-message {
        color: #dc2626;
        font-size: 12px;
        margin-top: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .button-group {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding: clamp(14px, 1.5vw, 20px) clamp(16px, 1.9vw, 26px);
        border-top: 1px solid #dce8f5;
        background: #f8fbff;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        min-height: 44px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff;
        box-shadow: 0 10px 18px -14px rgba(2, 132, 199, .7);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
    }

    .btn-secondary {
        background: #eef4fb;
        border: 1px solid #cad9ec;
        color: #2d4a69;
    }

    .btn-secondary:hover {
        background: #e6eef8;
    }

    /* Dark mode */
    body.dark-mode .contact-create-page {
        background:
            radial-gradient(circle at right top, rgba(56, 189, 248, .08) 0%, transparent 34%),
            radial-gradient(circle at left bottom, rgba(16, 185, 129, .07) 0%, transparent 30%),
            linear-gradient(135deg, #0f1b2d 0%, #0d1727 100%);
        border-color: #2e4c6d;
        box-shadow: 0 18px 34px -30px rgba(0, 0, 0, 0.9);
    }

    body.dark-mode .page-header,
    body.dark-mode .contact-form-card {
        background: #112338;
        border-color: #31506f;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.32);
    }

    body.dark-mode .page-header h1,
    body.dark-mode .section-header h2,
    body.dark-mode .form-label {
        color: #e6efff;
    }

    body.dark-mode .page-header p {
        color: #9fb7d4;
    }

    body.dark-mode .section-header {
        background: rgba(18, 49, 79, 0.7);
        border-bottom-color: #2b4562;
    }

    body.dark-mode .section-icon {
        background: #16324f;
        color: #8ec5ff;
        border-color: #2f4f72;
    }

    body.dark-mode .form-input,
    body.dark-mode .form-select,
    body.dark-mode .form-textarea {
        background: #0d1a2b;
        border-color: #3b5d81;
        color: #e6efff;
    }

    body.dark-mode .form-input::placeholder,
    body.dark-mode .form-textarea::placeholder {
        color: #8fa9c8;
    }

    body.dark-mode .form-input:focus,
    body.dark-mode .form-select:focus,
    body.dark-mode .form-textarea:focus {
        border-color: #63a9ff;
        box-shadow: 0 0 0 3px rgba(99, 169, 255, 0.22);
    }

    body.dark-mode .form-input.error,
    body.dark-mode .form-select.error,
    body.dark-mode .form-textarea.error {
        background: rgba(127, 29, 29, .25);
        border-color: #ef4444;
    }

    body.dark-mode .error-message {
        color: #fca5a5;
    }

    body.dark-mode .button-group {
        background: #102132;
        border-top-color: #2f4a66;
    }

    body.dark-mode .btn-secondary {
        background: #1d3654;
        border-color: #36577b;
        color: #d8e8fd;
    }

    body.dark-mode .btn-secondary:hover {
        background: #2b486c;
        color: #fff;
    }

    @media (max-width: 768px) {
        .contact-create-page {
            padding: 10px;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .button-group {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
        }
    }

    @media (min-width: 1400px) {
        .contact-create-page {
            padding-inline: clamp(16px, 1.8vw, 30px);
        }
    }
</style>

<div class="contact-create-page">
    <div class="contact-create-shell">
        <div class="page-header">
            <h1><i class="fas fa-user-plus"></i> Nouveau Contact</h1>
            <p>Ajouter un nouveau contact</p>
        </div>

        <div class="contact-form-card">
            <form action="{{ route('contacts.store') }}" method="POST" novalidate>
                @csrf

                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon"><i class="fas fa-id-card"></i></span>
                        <h2>Informations Personnelles</h2>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom" class="form-label">
                                Nom <span class="required">*</span>
                            </label>
                            <input type="text" id="nom" name="nom" class="form-input {{ $errors->has('nom') ? 'error' : '' }}" placeholder="Nom de famille" value="{{ old('nom') }}" required>
                            @if($errors->has('nom'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('nom') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="prenom" class="form-label">Prenom</label>
                            <input type="text" id="prenom" name="prenom" class="form-input {{ $errors->has('prenom') ? 'error' : '' }}" placeholder="Prenom" value="{{ old('prenom') }}">
                            @if($errors->has('prenom'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('prenom') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="type" class="form-label">
                                Type de Contact <span class="required">*</span>
                            </label>
                            <select id="type" name="type" class="form-select {{ $errors->has('type') ? 'error' : '' }}" required>
                                <option value="">-- Selectionner un type --</option>
                                <option value="patient" {{ old('type') === 'patient' ? 'selected' : '' }}>Patient</option>
                                <option value="laboratoire" {{ old('type') === 'laboratoire' ? 'selected' : '' }}>Laboratoire</option>
                                <option value="fournisseur" {{ old('type') === 'fournisseur' ? 'selected' : '' }}>Fournisseur</option>
                                <option value="hopital" {{ old('type') === 'hopital' ? 'selected' : '' }}>Hopital</option>
                                <option value="assurance" {{ old('type') === 'assurance' ? 'selected' : '' }}>Assurance</option>
                                <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @if($errors->has('type'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('type') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="entreprise" class="form-label">Entreprise / Structure</label>
                            <input type="text" id="entreprise" name="entreprise" class="form-input {{ $errors->has('entreprise') ? 'error' : '' }}" placeholder="Nom de l'entreprise" value="{{ old('entreprise') }}">
                            @if($errors->has('entreprise'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('entreprise') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="fonction" class="form-label">Fonction / Poste</label>
                            <input type="text" id="fonction" name="fonction" class="form-input {{ $errors->has('fonction') ? 'error' : '' }}" placeholder="Ex: Directeur, Pharmacien..." value="{{ old('fonction') }}">
                            @if($errors->has('fonction'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('fonction') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon"><i class="fas fa-phone"></i></span>
                        <h2>Coordonnees</h2>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}" placeholder="email@example.com" value="{{ old('email') }}">
                            @if($errors->has('email'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="telephone" class="form-label">Telephone</label>
                            <input type="tel" id="telephone" name="telephone" class="form-input {{ $errors->has('telephone') ? 'error' : '' }}" placeholder="+212 6 XX XX XX XX" value="{{ old('telephone') }}">
                            @if($errors->has('telephone'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('telephone') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="telephone_secondaire" class="form-label">Telephone Secondaire</label>
                            <input type="tel" id="telephone_secondaire" name="telephone_secondaire" class="form-input {{ $errors->has('telephone_secondaire') ? 'error' : '' }}" placeholder="+212 6 XX XX XX XX" value="{{ old('telephone_secondaire') }}">
                            @if($errors->has('telephone_secondaire'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('telephone_secondaire') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon"><i class="fas fa-location-dot"></i></span>
                        <h2>Adresse</h2>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" id="adresse" name="adresse" class="form-input {{ $errors->has('adresse') ? 'error' : '' }}" placeholder="Adresse complete" value="{{ old('adresse') }}">
                            @if($errors->has('adresse'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('adresse') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ville" class="form-label">Ville</label>
                            <input type="text" id="ville" name="ville" class="form-input {{ $errors->has('ville') ? 'error' : '' }}" placeholder="Ville" value="{{ old('ville') }}">
                            @if($errors->has('ville'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('ville') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="codepostal" class="form-label">Code Postal</label>
                            <input type="text" id="codepostal" name="codepostal" class="form-input {{ $errors->has('codepostal') ? 'error' : '' }}" placeholder="Code postal" value="{{ old('codepostal') }}">
                            @if($errors->has('codepostal'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('codepostal') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon"><i class="fas fa-note-sticky"></i></span>
                        <h2>Notes</h2>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="notes" class="form-label">Notes Additionnelles</label>
                            <textarea id="notes" name="notes" class="form-textarea {{ $errors->has('notes') ? 'error' : '' }}" placeholder="Notes ou remarques supplementaires...">{{ old('notes') }}</textarea>
                            @if($errors->has('notes'))
                                <div class="error-message"><i class="fas fa-circle-exclamation"></i> {{ $errors->first('notes') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="button-group">
                    <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour a la liste
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-circle-check"></i> Creer le Contact
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const errorField = document.querySelector('.form-input.error, .form-select.error, .form-textarea.error');
        if (errorField) {
            errorField.focus();
            errorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endsection

