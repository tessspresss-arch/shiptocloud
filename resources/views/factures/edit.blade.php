@extends('layouts.app')

@section('title', 'Modifier la facture ' . $facture->numero_facture)

@php
    $factureDate = $facture->date_facture ? \Carbon\Carbon::parse($facture->date_facture) : null;
    $factureDueDate = $facture->date_echeance ? \Carbon\Carbon::parse($facture->date_echeance) : null;
@endphp

@push('styles')
<style>
    /* Styles pour la modification de facture */
    :root {
        --primary-color: #1e3a8a;
        --primary-light: #3b82f6;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
    }

    .facturation-container {
        background: white;
        border-radius: 12px;
        padding: clamp(16px, 2.2vw, 2.5rem) clamp(12px, 2.4vw, 3rem);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: clamp(14px, 1.8vw, 2.5rem);
        border: 1px solid #e2e8f0;
        width: min(100%, 1500px);
        margin-left: auto;
        margin-right: auto;
    }

    .facture-section {
        margin-bottom: clamp(14px, 1.6vw, 2.2rem);
        padding-bottom: clamp(10px, 1.2vw, 1.2rem);
        border-bottom: 1px solid #e2e8f0;
    }

    .facture-section h3 {
        color: var(--primary-color);
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-row {
        display: flex;
        gap: clamp(10px, 1.2vw, 1.2rem);
        align-items: flex-end;
        margin-bottom: clamp(8px, 1vw, 1.1rem);
        flex-wrap: wrap;
    }

    .form-group {
        flex: 1;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #374151;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-custom {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        font-size: 0.95rem;
        text-decoration: none;
    }

    .btn-primary-custom {
        background-color: var(--primary-light);
        color: white;
    }

    .btn-primary-custom:hover {
        background-color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-custom {
        background-color: #f1f5f9;
        color: #475569;
    }

    .btn-secondary-custom:hover {
        background-color: #e2e8f0;
        color: #475569;
        text-decoration: none;
    }

    .btn-success-custom {
        background-color: var(--success-color);
        color: white;
    }

    .btn-success-custom:hover {
        background-color: #059669;
        color: white;
        text-decoration: none;
    }

    .btn-danger-custom {
        background-color: var(--danger-color);
        color: white;
    }

    .btn-danger-custom:hover {
        background-color: #dc2626;
        color: white;
        text-decoration: none;
    }

    /* Table des prestations */
    .table-container {
        overflow-x: auto;
        margin: 20px 0;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .prestations-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .prestations-table thead {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .prestations-table th {
        padding: clamp(10px, 1.2vw, 15px);
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .prestations-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .prestations-table td {
        padding: clamp(8px, 1vw, 15px);
        vertical-align: middle;
    }

    .prestations-table input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .prestations-table input:focus {
        outline: none;
        border-color: var(--primary-light);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .total-ligne {
        font-weight: 600;
        color: var(--primary-color);
        text-align: right;
    }

    /* Récapitulatif */
    .recap-facture {
        background: #f8fafc;
        padding: clamp(14px, 1.6vw, 25px);
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .recap-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 12px 0;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .recap-item:last-child {
        border-bottom: none;
        border-top: 2px solid var(--primary-color);
        margin-top: 20px;
        padding-top: 15px;
    }

    .recap-item label {
        font-weight: 600;
        color: #374151;
    }

    .recap-item span {
        font-weight: 600;
        color: #374151;
    }

    .total-final {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    /* Actions */
    .action-buttons {
        display: flex;
        gap: clamp(8px, 1vw, 1.2rem);
        justify-content: flex-end;
        margin-top: clamp(14px, 1.6vw, 2.2rem);
        padding-top: clamp(10px, 1.2vw, 1.2rem);
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .facturation-container {
            padding: 10px 8px;
        }

        .form-row {
            flex-direction: column;
            gap: 10px;
        }

        .action-buttons {
            flex-direction: column;
            align-items: stretch;
        }

        .table-container {
            border-radius: 6px;
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
            border: 1px solid #dbe7f1;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
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
            color: #718aa3;
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
    }

    @media (min-width: 1700px) {
        .facturation-container {
            width: min(100%, 1680px);
        }
    }

    .facturation-container {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 24px;
        border-color: #d9e5f1;
        box-shadow: 0 24px 40px -34px rgba(15, 23, 42, 0.22);
    }

    .facture-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 1.6rem;
        padding-bottom: 1.1rem;
        border-bottom: 1px solid #dfe8f2;
    }

    .facture-head-main {
        min-width: 0;
        flex: 1 1 auto;
    }

    .facture-head-copy {
        display: grid;
        gap: 8px;
    }

    .facture-head-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d5e0ec;
        background: rgba(239, 246, 255, 0.9);
        color: #1f6fa3;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .facture-head-title {
        color: #173454;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: clamp(1.7rem, 2.4vw, 2.15rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.08;
    }

    .facture-head-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 0.98rem;
        line-height: 1.6;
        font-weight: 600;
        max-width: 72ch;
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

    .btn-custom {
        min-height: 48px;
        border-radius: 14px;
        font-weight: 800;
        box-shadow: 0 16px 26px -24px rgba(15, 23, 42, 0.22);
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
    }

    .btn-primary-custom:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
    }

    .btn-secondary-custom {
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        border: 1px solid #d7e1ec;
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
    }

    .btn-success-custom:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
    }

    .table-container,
    .recap-facture {
        border-radius: 18px;
        border-color: #d9e5f1;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    body.dark-mode .facturation-container,
    html.dark .facturation-container {
        background: linear-gradient(180deg, #14253a 0%, #112033 100%);
        border-color: #2f4c69;
    }

    body.dark-mode .facture-head-title,
    html.dark .facture-head-title,
    body.dark-mode .facture-section h3,
    html.dark .facture-section h3 {
        color: #e7f0ff;
    }

    body.dark-mode .facture-head-subtitle,
    html.dark .facture-head-subtitle {
        color: #a9bfd8;
    }

    body.dark-mode .facture-head-eyebrow,
    html.dark .facture-head-eyebrow {
        background: rgba(93, 165, 255, 0.12);
        border-color: #37618c;
        color: #9ecbff;
    }

    body.dark-mode .facture-back-btn,
    html.dark .facture-back-btn {
        border-color: #365b7d;
        color: #d2e6fb;
        background: linear-gradient(150deg, #183552 0%, #14304b 100%);
    }

    body.dark-mode .facture-back-btn-icon,
    html.dark .facture-back-btn-icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    @media (max-width: 768px) {
        .facture-head {
            align-items: stretch;
        }

        .facture-back-btn,
        .btn-custom {
            width: 100%;
            justify-content: center;
        }

        body.dark-mode .prestations-table tbody tr,
        html.dark .prestations-table tbody tr {
            background: #11273d;
            border-color: #26435d;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 px-2 px-md-3">
    <div class="facturation-container">
        <form id="factureForm" method="POST" action="{{ route('factures.update', $facture) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="consultation_id" value="{{ old('consultation_id', $facture->consultation_id) }}">

            <!-- En-tête -->
            <div class="facture-head">
                <div class="facture-head-main">
                    <div class="facture-head-copy">
                        <span class="facture-head-eyebrow"><i class="fas fa-file-pen"></i> Facturation cabinet</span>
                        <h2 class="facture-head-title">Modifier la facture {{ $facture->numero_facture }}</h2>
                        <p class="facture-head-subtitle">Ajustez les prestations, les montants et les informations de règlement avec une vue plus claire et plus cohérente avec le module Factures.</p>
                    </div>
                </div>
                <div class="facture-head-actions">
                    <a href="{{ route('factures.index') }}" class="facture-back-btn">
                        <span class="facture-back-btn-icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Retour à la liste</span>
                    </a>
                </div>
            </div>
            @if($facture->consultation)
                <div class="alert alert-info mb-4" role="alert">
                    <strong>Facture liée à la consultation #{{ $facture->consultation->id }}</strong>
                    <span class="d-block mt-1">Patient : {{ $facture->consultation->patient?->nom }} {{ $facture->consultation->patient?->prenom }}</span>
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
                        <label>Patient :</label>
                        <select name="patient_id" id="selectPatient" class="form-control" required>
                            <option value="">Sélectionner un patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ $facture->patient_id == $patient->id ? 'selected' : '' }}>
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
                                <option value="{{ $medecin->id }}" {{ $facture->medecin_id == $medecin->id ? 'selected' : '' }}>
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
                        @php($invoiceLines = $facture->ligneFactures ?? collect())
                        <tbody id="prestationsBody">
                            @forelse($invoiceLines as $index => $ligne)
                                <tr class="ligne-prestation">
                                    <td data-label="Description">
                                        <input type="text"
                                               name="prestations[{{ $index }}][description]"
                                               class="form-control"
                                               value="{{ $ligne->description }}"
                                               required>
                                    </td>
                                    <td data-label="Quantité">
                                        <input type="number"
                                               name="prestations[{{ $index }}][quantite]"
                                               class="form-control quantite"
                                               value="{{ $ligne->quantite }}"
                                               min="1"
                                               step="1"
                                               required>
                                    </td>
                                    <td data-label="Prix unitaire (DH)">
                                        <input type="number"
                                               name="prestations[{{ $index }}][prix_unitaire]"
                                               class="form-control prix-unitaire"
                                               value="{{ $ligne->prix_unitaire }}"
                                               min="0"
                                               step="0.01"
                                               required>
                                    </td>
                                    <td class="total-ligne" data-label="Total (DH)">{{ number_format((float) ($ligne->total_ligne ?? $ligne->montant_total ?? 0), 2, '.', '') }}</td>
                                    <td data-label="Action">
                                        <button type="button" class="btn-custom btn-danger-custom btn-sm supprimer-ligne">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="ligne-prestation">
                                    <td data-label="Description">
                                        <input type="text"
                                               name="prestations[0][description]"
                                               class="form-control"
                                               placeholder="Description de la prestation"
                                               required>
                                    </td>
                                    <td data-label="Quantité">
                                        <input type="number"
                                               name="prestations[0][quantite]"
                                               class="form-control quantite"
                                               value="1"
                                               min="1"
                                               step="1"
                                               required>
                                    </td>
                                    <td data-label="Prix unitaire (DH)">
                                        <input type="number"
                                               name="prestations[0][prix_unitaire]"
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
                                </tr>
                            @endforelse
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
                        <span id="sousTotal">{{ number_format((float) $facture->montant_total, 2) }} DH</span>
                    </div>
                    <div class="recap-item">
                        <label for="remise">Remise (DH) :</label>
                        <input type="number"
                               id="remise"
                               name="remise"
                               value="{{ $facture->remise }}"
                               min="0"
                               step="0.01"
                               class="form-control"
                               style="width: 120px;">
                    </div>
                    <div class="recap-item">
                        <span>Total net :</span>
                        <span id="totalFacture" class="total-final">{{ number_format($facture->montant_net, 2) }} DH</span>
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
                               value="{{ $factureDate?->format('Y-m-d') }}"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="date_echeance">Date d'échéance (optionnel) :</label>
                        <input type="date"
                               id="date_echeance"
                               name="date_echeance"
                               class="form-control"
                               value="{{ $factureDueDate?->format('Y-m-d') ?? '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes">Notes (optionnel) :</label>
                    <textarea id="notes"
                              name="notes"
                              class="form-control"
                              rows="3"
                              placeholder="Notes ou commentaires sur la facture...">{{ $facture->notes }}</textarea>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="action-buttons">
                <a href="{{ route('factures.show', $facture) }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn-custom btn-primary-custom">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let ligneIndex = {{ count($invoiceLines) }};

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
