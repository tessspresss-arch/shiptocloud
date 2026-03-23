@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #0284c7;
        --secondary: #10b981;
        --accent: #06b6d4;
        --danger: #ef4444;
        --warning: #f59e0b;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: #f9fafb;
    }

    .form-wrapper {
        width: min(100%, 1320px);
        margin: 0 auto;
        padding: 16px clamp(10px, 2vw, 22px);
    }

    .form-header {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-top: 4px solid var(--primary);
        padding: clamp(20px, 2.4vw, 32px);
        border-radius: 12px;
        margin-bottom: clamp(16px, 2vw, 28px);
        text-align: center;
    }

    .form-header h1 {
        font-size: 32px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .form-header p {
        color: #6b7280;
        margin: 0;
        font-size: 15px;
    }

    .form-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #d9e4f2;
        box-shadow: 0 20px 26px -30px rgba(17, 57, 104, 0.72);
        overflow: hidden;
    }

    .form-section {
        border-bottom: 1px solid #e5e7eb;
        padding: clamp(16px, 2vw, 28px);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fbff;
        padding: 0.82rem clamp(16px, 2vw, 28px);
        border-bottom: 1px solid #eef3fb;
        margin: calc(-1 * clamp(16px, 2vw, 28px)) calc(-1 * clamp(16px, 2vw, 28px)) clamp(14px, 1.6vw, 22px) calc(-1 * clamp(16px, 2vw, 28px));
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
        font-weight: 800;
        color: #153b84;
        letter-spacing: 0;
        margin: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-label .required {
        color: var(--danger);
        margin-left: 2px;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s ease;
        background: white;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        background: #f0f9ff;
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: #9ca3af;
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
        font-family: inherit;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: clamp(12px, 1.5vw, 20px);
        margin-bottom: clamp(12px, 1.5vw, 20px);
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    .help-text {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 4px;
    }

    .error-message {
        color: var(--danger);
        font-size: 12px;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .form-input.error,
    .form-select.error,
    .form-textarea.error {
        border-color: var(--danger);
        background-color: #fef2f2;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .checkbox-group input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary);
    }

    .checkbox-group label {
        margin: 0;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        font-size: 14px;
    }

    .button-group {
        display: flex;
        gap: 12px;
        justify-content: space-between;
        padding: clamp(14px, 1.5vw, 20px) clamp(16px, 2vw, 28px);
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        flex-wrap: wrap;
    }

    .btn {
        padding: 11px 24px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #0369a1;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
    }

    .btn-primary:active {
        transform: scale(0.98);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-wrapper {
            padding: 10px;
        }

        .form-header {
            padding: 18px;
            margin-bottom: 16px;
        }

        .form-header h1 {
            font-size: 24px;
            flex-direction: column;
        }

        .form-section {
            padding: 14px;
        }

        .section-header {
            margin: -14px -14px 12px -14px;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .button-group {
            flex-direction: column-reverse;
            padding: 12px 14px;
        }

        .button-group > div {
            width: 100%;
            display: flex !important;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (min-width: 1400px) {
        .form-wrapper {
            width: min(100%, 1450px);
        }
    }

    /* Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-card {
        animation: slideIn 0.3s ease-out;
    }

    /* Print styles */
    @media print {
        .form-header,
        .button-group {
            display: none;
        }

        .form-card {
            box-shadow: none;
            border: 1px solid #d1d5db;
        }
    }
</style>

<div class="form-wrapper">
    <!-- Header -->
    <div class="form-header">
        <h1>✏️ Modifier le Contact</h1>
        <p>Mettre à jour les informations du contact</p>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('contacts.update', $contact->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <!-- Section 1: Informations Personnelles -->
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
                        <input type="text" id="nom" name="nom" class="form-input {{ $errors->has('nom') ? 'error' : '' }}" placeholder="Nom de famille" value="{{ $contact->nom }}" required>
                        @if($errors->has('nom'))
                            <div class="error-message">❌ {{ $errors->first('nom') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" id="prenom" name="prenom" class="form-input {{ $errors->has('prenom') ? 'error' : '' }}" placeholder="Prénom" value="{{ $contact->prenom }}">
                        @if($errors->has('prenom'))
                            <div class="error-message">❌ {{ $errors->first('prenom') }}</div>
                        @endif
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="type" class="form-label">
                            Type de Contact <span class="required">*</span>
                        </label>
                        <select id="type" name="type" class="form-select {{ $errors->has('type') ? 'error' : '' }}" required>
                            <option value="">— Sélectionner un type —</option>
                            <option value="patient" {{ $contact->type === 'patient' ? 'selected' : '' }}>👥 Patient</option>
                            <option value="laboratoire" {{ $contact->type === 'laboratoire' ? 'selected' : '' }}>🧪 Laboratoire</option>
                            <option value="fournisseur" {{ $contact->type === 'fournisseur' ? 'selected' : '' }}>📦 Fournisseur</option>
                            <option value="hopital" {{ $contact->type === 'hopital' ? 'selected' : '' }}>🏥 Hôpital</option>
                            <option value="assurance" {{ $contact->type === 'assurance' ? 'selected' : '' }}>📄 Assurance</option>
                            <option value="autre" {{ $contact->type === 'autre' ? 'selected' : '' }}>📌 Autre</option>
                        </select>
                        @if($errors->has('type'))
                            <div class="error-message">❌ {{ $errors->first('type') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="entreprise" class="form-label">Entreprise / Structure</label>
                        <input type="text" id="entreprise" name="entreprise" class="form-input {{ $errors->has('entreprise') ? 'error' : '' }}" placeholder="Nom de l'entreprise" value="{{ $contact->entreprise }}">
                        @if($errors->has('entreprise'))
                            <div class="error-message">❌ {{ $errors->first('entreprise') }}</div>
                        @endif
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fonction" class="form-label">Fonction / Poste</label>
                        <input type="text" id="fonction" name="fonction" class="form-input {{ $errors->has('fonction') ? 'error' : '' }}" placeholder="Ex: Directeur, Pharmacien..." value="{{ $contact->fonction }}">
                        @if($errors->has('fonction'))
                            <div class="error-message">❌ {{ $errors->first('fonction') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 2: Coordonnées -->
            <div class="form-section">
                <div class="section-header">
                    <span class="section-icon"><i class="fas fa-phone"></i></span>
                    <h2>Coordonnees</h2>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}" placeholder="email@example.com" value="{{ $contact->email }}">
                        @if($errors->has('email'))
                            <div class="error-message">❌ {{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="form-input {{ $errors->has('telephone') ? 'error' : '' }}" placeholder="+212 6 XX XX XX XX" value="{{ $contact->telephone }}">
                        @if($errors->has('telephone'))
                            <div class="error-message">❌ {{ $errors->first('telephone') }}</div>
                        @endif
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone_secondaire" class="form-label">Téléphone Secondaire</label>
                        <input type="tel" id="telephone_secondaire" name="telephone_secondaire" class="form-input {{ $errors->has('telephone_secondaire') ? 'error' : '' }}" placeholder="+212 6 XX XX XX XX" value="{{ $contact->telephone_secondaire }}">
                        @if($errors->has('telephone_secondaire'))
                            <div class="error-message">❌ {{ $errors->first('telephone_secondaire') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 3: Adresse -->
            <div class="form-section">
                <div class="section-header">
                    <span class="section-icon"><i class="fas fa-location-dot"></i></span>
                    <h2>Adresse</h2>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" id="adresse" name="adresse" class="form-input {{ $errors->has('adresse') ? 'error' : '' }}" placeholder="Adresse complète" value="{{ $contact->adresse }}">
                        @if($errors->has('adresse'))
                            <div class="error-message">❌ {{ $errors->first('adresse') }}</div>
                        @endif
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ville" class="form-label">Ville</label>
                        <input type="text" id="ville" name="ville" class="form-input {{ $errors->has('ville') ? 'error' : '' }}" placeholder="Ville" value="{{ $contact->ville }}">
                        @if($errors->has('ville'))
                            <div class="error-message">❌ {{ $errors->first('ville') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="codepostal" class="form-label">Code Postal</label>
                        <input type="text" id="codepostal" name="codepostal" class="form-input {{ $errors->has('codepostal') ? 'error' : '' }}" placeholder="Code postal" value="{{ $contact->codepostal }}">
                        @if($errors->has('codepostal'))
                            <div class="error-message">❌ {{ $errors->first('codepostal') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 4: Notes -->
            <div class="form-section">
                <div class="section-header">
                    <span class="section-icon"><i class="fas fa-note-sticky"></i></span>
                    <h2>Notes</h2>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label for="notes" class="form-label">Notes Additionnelles</label>
                        <textarea id="notes" name="notes" class="form-textarea {{ $errors->has('notes') ? 'error' : '' }}" placeholder="Notes ou remarques supplémentaires...">{{ $contact->notes }}</textarea>
                        @if($errors->has('notes'))
                            <div class="error-message">❌ {{ $errors->first('notes') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('contacts.index') }}" class="btn btn-secondary">← Retour à la liste</a>
                    <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous certain de vouloir supprimer ce contact ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                    </form>
                </div>
                <button type="submit" class="btn btn-primary">✓ Mettre à Jour</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-focus sur le premier champ d'erreur
    document.addEventListener('DOMContentLoaded', function() {
        const errorField = document.querySelector('.form-input.error, .form-select.error, .form-textarea.error');
        if (errorField) {
            errorField.focus();
            errorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>

@endsection
