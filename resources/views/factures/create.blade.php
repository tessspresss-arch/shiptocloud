@extends('layouts.app')

@section('title', 'Créer une facture')

@push('styles')
<style>
    :root {
        --primary-color: #2563eb;
        --primary-light: #3b82f6;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --fact-bg: #ffffff;
        --fact-surface: #f8fafc;
        --fact-border: #e2e8f0;
        --fact-text: #1f2937;
        --fact-muted: #475569;
    }

    html.dark,
    body.dark-mode,
    body.theme-dark {
        --fact-bg: #162133;
        --fact-surface: #0f1b2d;
        --fact-border: #2d3b52;
        --fact-text: #e5edf8;
        --fact-muted: #9fb0c7;
    }
    .facturation-container {
        background: var(--fact-bg);
        border-radius: 1.5rem;
        padding: clamp(16px, 2.2vw, 2.5rem) clamp(12px, 2.4vw, 3rem);
        box-shadow: 0 8px 32px rgba(37,99,235,0.07), 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: clamp(14px, 1.8vw, 2.5rem);
        border: 1px solid var(--fact-border);
        width: min(100%, 1500px);
        margin-left: auto;
        margin-right: auto;
    }
    .facture-section {
        margin-bottom: clamp(14px, 1.6vw, 2.2rem);
        padding-bottom: clamp(10px, 1.2vw, 1.2rem);
        border-bottom: 1px solid var(--fact-border);
    }
    .facture-section h3 {
        color: var(--primary-color);
        font-size: 1.18rem;
        font-weight: 700;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.5px;
    }
    .facture-head {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: start;
        gap: 18px;
        margin-bottom: 1.6rem;
    }
    .facture-head-main {
        min-width: 0;
    }
    .facture-head-copy {
        display: grid;
        gap: 10px;
    }
    .facture-head-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d5e0ec;
        background: rgba(239, 246, 255, 0.92);
        color: #245f96;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .facture-head-subtitle {
        margin: 0;
        color: var(--fact-muted);
        font-size: 0.97rem;
        line-height: 1.55;
        font-weight: 500;
        max-width: 70ch;
    }
    .facture-head-title {
        color: #16345f;
        margin: 0;
        font-size: clamp(2rem, 3vw, 2.65rem);
        font-weight: 800;
        letter-spacing: -0.04em;
        line-height: 1.02;
    }
    .facture-head-actions {
        display: flex;
        justify-content: flex-end;
        align-items: flex-start;
    }
    .facture-back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 48px;
        padding: 0 18px 0 14px;
        border-radius: 16px;
        border: 1px solid rgba(191, 207, 223, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(245, 249, 253, 0.92) 100%);
        color: #385674;
        font-weight: 700;
        letter-spacing: -0.01em;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92), 0 16px 28px -26px rgba(15, 23, 42, 0.28);
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .facture-back-btn:hover,
    .facture-back-btn:focus {
        color: #1f6fa3;
        border-color: rgba(44, 123, 229, 0.3);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 244, 251, 0.98) 100%);
        transform: translateY(-1px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.96), 0 18px 32px -24px rgba(31, 111, 163, 0.22);
        text-decoration: none;
    }
    .facture-back-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(44, 123, 229, 0.1);
        color: #2c7be5;
        flex-shrink: 0;
    }
    .form-row {
        display: flex;
        gap: clamp(10px, 1.2vw, 1.2rem);
        align-items: flex-end;
        margin-bottom: clamp(8px, 1vw, 1.1rem);
        flex-wrap: wrap;
    }
    .form-group {
        flex: 1 1 220px;
        min-width: 180px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: var(--fact-text);
        font-size: 0.97rem;
    }
    .form-control, select.form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--fact-border);
        border-radius: 0.9rem;
        font-size: 1rem;
        transition: border 0.3s, box-shadow 0.3s;
        background: var(--fact-surface);
        color: var(--fact-text);
    }
    .form-control:focus, select.form-control:focus {
        outline: none;
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.13);
        background: var(--fact-bg);
    }

    .form-control::placeholder,
    textarea.form-control::placeholder {
        color: var(--fact-muted);
    }
    .btn-custom {
        padding: 12px 24px;
        border: none;
        border-radius: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.22s;
        font-size: 1rem;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(37,99,235,0.07);
    }
    .btn-primary-custom {
        background: linear-gradient(90deg, #2563eb 60%, #3b82f6 100%);
        color: #fff;
    }
    .btn-primary-custom:hover {
        background: linear-gradient(90deg, #3b82f6 60%, #2563eb 100%);
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 6px 18px rgba(37,99,235,0.13);
        color: #fff;
    }
    .btn-secondary-custom {
        background: var(--fact-surface);
        color: var(--fact-muted);
        border: 1px solid var(--fact-border);
    }
    .btn-secondary-custom:hover {
        background: color-mix(in srgb, var(--primary-color) 10%, var(--fact-surface));
        color: #2563eb;
    }
    .btn-success-custom {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #fff;
    }
    .btn-success-custom:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        color: #fff;
    }
    .btn-danger-custom {
        background: #ef4444;
        color: #fff;
    }
    .btn-danger-custom:hover {
        background: #dc2626;
        color: #fff;
    }
    .table-container {
        overflow-x: auto;
        margin: 1.2rem 0;
        border-radius: 1rem;
        border: 1px solid var(--fact-border);
        background: var(--fact-bg);
        box-shadow: 0 2px 8px rgba(37,99,235,0.04);
    }
    .prestations-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--fact-bg);
    }
    .prestations-table thead {
        background: var(--fact-surface);
        border-bottom: 2px solid var(--fact-border);
    }
    .prestations-table th {
        padding: clamp(10px, 1.2vw, 15px);
        text-align: left;
        font-weight: 700;
        color: #2563eb;
        font-size: 0.98rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: var(--fact-surface);
    }
    .prestations-table tbody tr {
        border-bottom: 1px solid var(--fact-border);
        transition: background 0.18s;
    }
    .prestations-table tbody tr:hover {
        background: color-mix(in srgb, var(--primary-color) 8%, var(--fact-surface));
    }
    .prestations-table td {
        padding: clamp(8px, 1vw, 15px);
        vertical-align: middle;
        color: var(--fact-text);
    }
    .prestations-table input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--fact-border);
        border-radius: 0.7rem;
        font-size: 0.97rem;
        background: var(--fact-surface);
        color: var(--fact-text);
        transition: border 0.2s, box-shadow 0.2s;
    }
    .prestations-table input:focus {
        outline: none;
        border-color: var(--primary-light);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.10);
        background: var(--fact-bg);
    }
    .total-ligne {
        font-weight: 700;
        color: var(--primary-color);
        text-align: right;
    }
    .recap-facture {
        background: var(--fact-surface);
        padding: 1.5rem;
        border-radius: 1rem;
        border: 1px solid var(--fact-border);
        box-shadow: 0 2px 8px rgba(37,99,235,0.04);
    }
    .recap-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 12px 0;
        padding: 8px 0;
        border-bottom: 1px solid var(--fact-border);
    }
    .recap-item:last-child {
        border-bottom: none;
        border-top: 2px solid var(--primary-color);
        margin-top: 20px;
        padding-top: 15px;
    }
    .recap-item label, .recap-item span {
        font-weight: 600;
        color: var(--fact-text);
    }
    .total-final {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    .action-buttons {
        display: flex;
        gap: clamp(8px, 1vw, 1.2rem);
        justify-content: flex-end;
        margin-top: clamp(14px, 1.6vw, 2.2rem);
        padding-top: clamp(10px, 1.2vw, 1.2rem);
        border-top: 1px solid var(--fact-border);
        flex-wrap: wrap;
    }

    body.dark-mode .facturation-container,
    html.dark .facturation-container {
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.34);
    }

    body.dark-mode .btn-secondary-custom,
    html.dark .btn-secondary-custom {
        color: var(--fact-text);
    }

    body.dark-mode .facture-back-btn,
    html.dark .facture-back-btn {
        border-color: #365b7d;
        color: #d2e6fb;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04), 0 16px 28px -26px rgba(3, 12, 24, 0.85);
    }

    body.dark-mode .facture-back-btn:hover,
    body.dark-mode .facture-back-btn:focus,
    html.dark .facture-back-btn:hover,
    html.dark .facture-back-btn:focus {
        border-color: #4c7094;
        color: #ffffff;
        background: linear-gradient(150deg, #1d4166 0%, #153654 100%);
    }

    body.dark-mode .facture-back-btn-icon,
    html.dark .facture-back-btn-icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    body.dark-mode .prestations-table input::-webkit-outer-spin-button,
    body.dark-mode .prestations-table input::-webkit-inner-spin-button,
    html.dark .prestations-table input::-webkit-outer-spin-button,
    html.dark .prestations-table input::-webkit-inner-spin-button {
        filter: invert(1);
    }
    @media (max-width: 1200px) {
        .facturation-container {
            width: min(100%, 98vw);
            padding: 14px 10px;
        }
    }
    @media (max-width: 700px) {
        .facturation-container {
            padding: 10px 8px;
            border-radius: 1rem;
        }

        .facture-head {
            grid-template-columns: 1fr;
        }
        .facture-head-actions {
            justify-content: flex-start;
        }

        .facture-back-btn {
            width: 100%;
            justify-content: center;
        }

        .action-buttons {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .btn-custom {
            width: 100%;
            justify-content: center;
        }

        .table-container {
            overflow: visible;
            border: 0;
            background: transparent;
            box-shadow: none;
        }

        .prestations-table {
            min-width: 0;
            display: block;
            background: transparent;
        }

        .prestations-table thead {
            display: none;
        }

        .prestations-table tbody {
            display: grid;
            gap: 14px;
        }

        .prestations-table tbody tr {
            display: grid;
            gap: 12px;
            padding: 16px;
            border: 1px solid var(--fact-border);
            border-radius: 18px;
            background: var(--fact-bg);
            box-shadow: 0 16px 28px -28px rgba(15, 40, 65, 0.3);
        }

        .prestations-table td {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
            padding: 0;
            border: 0;
        }

        .prestations-table td::before {
            content: attr(data-label);
            color: var(--fact-muted);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .prestations-table input,
        .supprimer-ligne {
            width: 100%;
        }

        .total-ligne {
            text-align: left;
        }
    }

    @media (min-width: 1700px) {
        .facturation-container {
            width: min(100%, 1680px);
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 px-2 px-md-3">
    <div class="facturation-container">
        <form id="factureForm" method="POST" action="{{ route('factures.store') }}">
            @csrf
            <input type="hidden" name="consultation_id" value="{{ old('consultation_id', $selectedConsultation?->id ?? '') }}">

            <!-- En-tête -->
            <div class="facture-head">
                <div class="facture-head-main">
                    <div class="facture-head-copy">
                        <span class="facture-head-eyebrow"><i class="fas fa-file-invoice-dollar"></i> Facturation cabinet</span>
                        <h2 class="facture-head-title">Nouvelle facture</h2>
                        <p class="facture-head-subtitle">Créez une facture avec une lecture claire des prestations, du total net et des informations de règlement.</p>
                    </div>
                </div>
                <div class="facture-head-actions">
                    <a href="{{ route('factures.index') }}" class="facture-back-btn">
                        <span class="facture-back-btn-icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Retour à la liste</span>
                    </a>
                </div>
            </div>

            @if(!empty($selectedConsultation))
                <div class="alert alert-info mb-4" role="alert">
                    <strong>Facture liée à la consultation #{{ $selectedConsultation->id }}</strong>
                    <span class="d-block mt-1">Patient : {{ $selectedConsultation->patient?->nom }} {{ $selectedConsultation->patient?->prenom }} | Médecin : {{ $selectedConsultation->medecin?->nom }} {{ $selectedConsultation->medecin?->prenom }}</span>
                </div>
            @endif

            <!-- Étape 1 : Informations Patient -->
            <div class="facture-section">
                <h3>
                    <i class="fas fa-user"></i>
                    1. Informations Patient
                </h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Rechercher Patient :</label>
                        <select name="patient_id" id="selectPatient" class="form-control" required>
                            <option value="">Sélectionner un patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ (string) old('patient_id', $selectedPatientId ?? '') === (string) $patient->id ? 'selected' : '' }}>
                                    {{ $patient->nom }} {{ $patient->prenom }} - {{ $patient->telephone ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Médecin (optionnel) :</label>
                        <select name="medecin_id" id="selectMedecin" class="form-control">
                            <option value="">Sélectionner un médecin</option>
                            @foreach($medecins as $medecin)
                                <option value="{{ $medecin->id }}" {{ (string) old('medecin_id', $selectedMedecinId ?? '') === (string) $medecin->id ? 'selected' : '' }}>
                                    Dr. {{ $medecin->nom }} {{ $medecin->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Étape 2 : Prestations -->
            <div class="facture-section">
                <h3>
                    <i class="fas fa-list"></i>
                    2. Prestations
                </h3>

                <div class="table-container">
                    <table class="prestations-table" id="tablePrestations">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Quantité</th>
                                <th>Prix Unitaire (DH)</th>
                                <th>Total (DH)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="prestationsBody">
                            <tr class="ligne-prestation">
                                <td data-label="Description">
                                    <input type="text"
                                           name="prestations[0][description]"
                                           class="form-control"
                                           placeholder="Consultation générale"
                                         value="{{ old('prestations.0.description', $defaultPrestationDescription ?? 'Consultation générale') }}"
                                           required>
                                </td>
                                <td data-label="Quantité">
                                    <input type="number"
                                           name="prestations[0][quantite]"
                                           class="form-control quantite"
                                         value="{{ old('prestations.0.quantite', '1') }}"
                                           min="1"
                                           step="1"
                                           required>
                                </td>
                                <td data-label="Prix unitaire (DH)">
                                    <input type="number"
                                           name="prestations[0][prix_unitaire]"
                                           class="form-control prix-unitaire"
                                         value="{{ old('prestations.0.prix_unitaire', '300') }}"
                                           min="0"
                                           step="0.01"
                                           required>
                                </td>
                                <td class="total-ligne" data-label="Total (DH)">300.00</td>
                                <td data-label="Action">
                                    <button type="button" class="btn-custom btn-danger-custom btn-sm supprimer-ligne">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn-custom btn-success-custom" id="ajouterLigne">
                    <i class="fas fa-plus"></i> Ajouter une prestation
                </button>
            </div>

            <!-- Étape 3 : Récapitulatif -->
            <div class="facture-section">
                <h3>
                    <i class="fas fa-calculator"></i>
                    3. Récapitulatif
                </h3>
                <div class="recap-facture">
                    <div class="recap-item">
                        <span>Sous-total :</span>
                        <span id="sousTotal">300.00 DH</span>
                    </div>
                    <div class="recap-item">
                        <label for="remise">Remise (DH) :</label>
                        <input type="number"
                               id="remise"
                               name="remise"
                               value="0"
                               min="0"
                               step="0.01"
                               class="form-control"
                               style="width: 120px;">
                    </div>
                    <div class="recap-item">
                        <span>Total net :</span>
                        <span id="totalFacture" class="total-final">300.00 DH</span>
                    </div>
                </div>
            </div>

            <!-- Étape 4 : Informations supplémentaires -->
            <div class="facture-section">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    4. Informations supplémentaires
                </h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_facture">Date de facture :</label>
                        <input type="date"
                               id="date_facture"
                               name="date_facture"
                               class="form-control"
                               value="{{ old('date_facture', date('Y-m-d')) }}"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="date_echeance">Date d'échéance (optionnel) :</label>
                        <input type="date"
                               id="date_echeance"
                               name="date_echeance"
                               class="form-control"
                               value="{{ old('date_echeance') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes">Notes (optionnel) :</label>
                    <textarea id="notes"
                              name="notes"
                              class="form-control"
                              rows="3"
                              placeholder="Notes ou commentaires sur la facture...">{{ $defaultFactureNotes ?? '' }}</textarea>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="action-buttons">
                <a href="{{ route('factures.index') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" name="action" value="brouillon" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-save"></i> Enregistrer le brouillon
                </button>
                <button type="submit" name="action" value="en_attente" class="btn-custom btn-success-custom">
                    <i class="fas fa-paper-plane"></i> Créer la facture
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let ligneIndex = 1;

        // Ajouter une ligne
        document.getElementById('ajouterLigne').addEventListener('click', function() {
            ajouterLigne();
        });

        // Calcul automatique des totaux
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantite') || e.target.classList.contains('prix-unitaire')) {
                calculerLigne(e.target.closest('tr'));
                calculerTotal();
            }
        });

        // Calcul de la remise
        document.getElementById('remise').addEventListener('input', function() {
            calculerTotal();
        });

        // Supprimer une ligne
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('supprimer-ligne') || e.target.closest('.supprimer-ligne')) {
                e.preventDefault();
                const ligne = e.target.closest('tr');
                if (document.querySelectorAll('.ligne-prestation').length > 1) {
                    ligne.remove();
                    calculerTotal();
                } else {
                    alert('Vous devez avoir au moins une prestation.');
                }
            }
        });

        function ajouterLigne() {
            const tbody = document.getElementById('prestationsBody');
            const newRow = document.createElement('tr');
            newRow.className = 'ligne-prestation';

            newRow.innerHTML = `
                <td data-label="Description">
                    <input type="text"
                           name="prestations[${ligneIndex}][description]"
                           class="form-control"
                           placeholder="Description de la prestation"
                           required>
                </td>
                <td data-label="Quantité">
                    <input type="number"
                           name="prestations[${ligneIndex}][quantite]"
                           class="form-control quantite"
                           value="1"
                           min="1"
                           step="1"
                           required>
                </td>
                <td data-label="Prix unitaire (DH)">
                    <input type="number"
                           name="prestations[${ligneIndex}][prix_unitaire]"
                           class="form-control prix-unitaire"
                           value="0"
                           min="0"
                           step="0.01"
                           required>
                </td>
                <td class="total-ligne" data-label="Total (DH)">0.00</td>
                <td data-label="Action">
                    <button type="button" class="btn-custom btn-danger-custom btn-sm supprimer-ligne">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(newRow);
            ligneIndex++;
        }

        function calculerLigne(ligne) {
            const quantite = parseFloat(ligne.querySelector('.quantite').value) || 0;
            const prixUnitaire = parseFloat(ligne.querySelector('.prix-unitaire').value) || 0;
            const total = quantite * prixUnitaire;

            ligne.querySelector('.total-ligne').textContent = total.toFixed(2);
        }

        function calculerTotal() {
            let sousTotal = 0;
            document.querySelectorAll('.ligne-prestation').forEach(ligne => {
                const totalLigne = parseFloat(ligne.querySelector('.total-ligne').textContent) || 0;
                sousTotal += totalLigne;
            });

            const remise = parseFloat(document.getElementById('remise').value) || 0;
            const totalNet = sousTotal - remise;

            document.getElementById('sousTotal').textContent = sousTotal.toFixed(2) + ' DH';
            document.getElementById('totalFacture').textContent = totalNet.toFixed(2) + ' DH';
        }

        // Calcul initial
        calculerTotal();
    });
</script>
@endpush


