@extends('layouts.app')

@section('title', 'Facture ' . $facture->numero_facture)

@push('styles')
<style>
    /* Styles pour l'affichage de facture */
    :root {
        --primary-color: #1e3a8a;
        --primary-light: #3b82f6;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
    }

    .facture-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }

    .facture-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--primary-color);
    }

    .facture-title {
        flex: 1;
    }

    .facture-title h1 {
        color: var(--primary-color);
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
    }

    .facture-status {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
    }

    .status-brouillon {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .status-en_attente {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-payee {
        background-color: #d1fae5;
        color: #059669;
    }

    .status-annulee {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .facture-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-section {
        background: #f8fafc;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .info-section h3 {
        color: var(--primary-color);
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        padding: 5px 0;
    }

    .info-label {
        font-weight: 600;
        color: #374151;
    }

    .info-value {
        color: #6b7280;
    }

    .prestations-section {
        margin-bottom: 30px;
    }

    .prestations-section h3 {
        color: var(--primary-color);
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-container {
        overflow-x: auto;
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
        padding: 15px;
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
        padding: 15px;
        vertical-align: middle;
        color: #374151;
    }

    .total-ligne {
        font-weight: 600;
        color: var(--primary-color);
        text-align: right;
    }

    .recap-section {
        background: #f8fafc;
        padding: 25px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-bottom: 30px;
    }

    .recap-section h3 {
        color: var(--primary-color);
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recap-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .recap-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .recap-item:last-child {
        border-bottom: none;
        border-top: 2px solid var(--primary-color);
        margin-top: 10px;
        padding-top: 15px;
    }

    .recap-label {
        font-weight: 600;
        color: #374151;
    }

    .recap-value {
        font-weight: 600;
        color: #6b7280;
    }

    .total-final {
        font-size: 1.5rem;
        color: var(--primary-color);
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        flex-wrap: wrap;
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

    .btn-warning-custom {
        background-color: var(--warning-color);
        color: white;
    }

    .btn-warning-custom:hover {
        background-color: #d97706;
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

    .notes-section {
        background: #f8fafc;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-bottom: 30px;
    }

    .notes-section h3 {
        color: var(--primary-color);
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .notes-content {
        background: white;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        color: #6b7280;
        line-height: 1.6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .facture-container {
            padding: 20px;
        }

        .facture-header {
            flex-direction: column;
            gap: 15px;
        }

        .facture-title h1 {
            font-size: 1.5rem;
        }

        .facture-info {
            grid-template-columns: 1fr;
        }

        .recap-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .table-container {
            border-radius: 6px;
        }

        .table-container {
            overflow: visible;
            border: 0;
            background: transparent;
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

        .total-ligne {
            text-align: left;
        }
        }
    }

    .facture-container {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 24px;
        border-color: #d9e5f1;
        box-shadow: 0 24px 40px -34px rgba(15, 23, 42, 0.22);
    }

    .facture-header {
        align-items: center;
        gap: 18px;
        margin-bottom: 28px;
        padding-bottom: 22px;
        border-bottom: 1px solid #dfe8f2;
    }

    .facture-head-copy {
        display: grid;
        gap: 8px;
    }

    .facture-head-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .facture-eyebrow {
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

    .facture-title h1 {
        font-size: clamp(1.7rem, 2.5vw, 2.2rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.06;
    }

    .facture-title p {
        color: #64748b !important;
        font-weight: 600;
    }

    .facture-status {
        min-height: 38px;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        font-weight: 800;
        letter-spacing: .04em;
        box-shadow: 0 12px 20px -24px rgba(15, 23, 42, .22);
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

    .info-section,
    .recap-section,
    .notes-section,
    .table-container {
        border-radius: 18px;
        border-color: #d9e5f1;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .btn-custom {
        min-height: 48px;
        border-radius: 14px;
        font-weight: 800;
        box-shadow: 0 16px 26px -24px rgba(15, 23, 42, 0.22);
    }

    .btn-primary-custom,
    .btn-success-custom {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
    }

    .btn-primary-custom:hover,
    .btn-success-custom:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
    }

    .btn-secondary-custom {
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        border: 1px solid #d7e1ec;
    }

    body.dark-mode .facture-container,
    html.dark .facture-container {
        background: linear-gradient(180deg, #14253a 0%, #112033 100%);
        border-color: #2f4c69;
    }

    body.dark-mode .facture-title h1,
    html.dark .facture-title h1,
    body.dark-mode .info-section h3,
    html.dark .info-section h3,
    body.dark-mode .prestations-section h3,
    html.dark .prestations-section h3,
    body.dark-mode .recap-section h3,
    html.dark .recap-section h3,
    body.dark-mode .notes-section h3,
    html.dark .notes-section h3 {
        color: #e7f0ff;
    }

    body.dark-mode .facture-title p,
    html.dark .facture-title p,
    body.dark-mode .info-value,
    html.dark .info-value,
    body.dark-mode .recap-value,
    html.dark .recap-value,
    body.dark-mode .notes-content,
    html.dark .notes-content {
        color: #a9bfd8 !important;
    }

    body.dark-mode .facture-eyebrow,
    html.dark .facture-eyebrow {
        background: rgba(93, 165, 255, 0.12);
        border-color: #37618c;
        color: #9ecbff;
    }

    body.dark-mode .info-section,
    body.dark-mode .recap-section,
    body.dark-mode .notes-section,
    body.dark-mode .prestations-table,
    html.dark .info-section,
    html.dark .recap-section,
    html.dark .notes-section,
    html.dark .prestations-table {
        background: #13263f;
        border-color: #35506a;
    }

    @media (max-width: 768px) {
        .facture-header {
            flex-direction: column;
            align-items: stretch;
        }

        .facture-head-actions {
            justify-content: flex-start;
        }

        .facture-back-btn {
            width: 100%;
            justify-content: center;
        }

        .facture-status,
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
<div class="container-fluid py-4">
    <div class="facture-container">
        <!-- En-tête -->
        <div class="facture-header">
            <div class="facture-title">
                <div class="facture-head-copy">
                    <span class="facture-eyebrow"><i class="fas fa-file-invoice-dollar"></i> Facturation cabinet</span>
                    <h1>Facture {{ $facture->numero_facture }}</h1>
                </div>
                <p style="color: #6b7280; margin: 0; font-size: 1rem;">
                    Facture créée le {{ $facture->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>

            <div class="facture-head-actions">
                <a href="{{ route('factures.index') }}" class="facture-back-btn">
                    <span class="facture-back-btn-icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Retour à la liste</span>
                </a>
                <div class="facture-status status-{{ $facture->statut }}">
                    @switch($facture->statut)
                        @case('brouillon')
                            Brouillon
                            @break
                        @case('en_attente')
                            En attente
                            @break
                        @case('payée')
                            Payée
                            @break
                        @case('annulée')
                            Annulée
                            @break
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Informations générales -->
        <div class="facture-info">
            <!-- Informations patient -->
            <div class="info-section">
                <h3>
                    <i class="fas fa-user"></i>
                    Patient
                </h3>
                <div class="info-item">
                    <span class="info-label">Nom :</span>
                    <span class="info-value">{{ $facture->patient->nom }} {{ $facture->patient->prenom }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value">{{ $facture->patient->telephone ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email :</span>
                    <span class="info-value">{{ $facture->patient->email ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Informations facture -->
            <div class="info-section">
                <h3>
                    <i class="fas fa-file-invoice"></i>
                    Facture
                </h3>
                <div class="info-item">
                    <span class="info-label">Date :</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}</span>
                </div>
                @if($facture->date_echeance)
                    <div class="info-item">
                        <span class="info-label">Échéance :</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($facture->date_echeance)->format('d/m/Y') }}</span>
                    </div>
                @endif
                @if($facture->medecin)
                    <div class="info-item">
                        <span class="info-label">Médecin :</span>
                        <span class="info-value">Dr. {{ $facture->medecin->nom }} {{ $facture->medecin->prenom }}</span>
                    </div>
                @endif
                @if($facture->consultation)
                    <div class="info-item">
                        <span class="info-label">Consultation liée :</span>
                        <span class="info-value">#{{ $facture->consultation->id }} - {{ $facture->consultation->patient?->nom }} {{ $facture->consultation->patient?->prenom }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Prestations -->
        <div class="prestations-section">
            <h3>
                <i class="fas fa-list"></i>
                Prestations
            </h3>

            <div class="table-container">
                <table class="prestations-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facture->ligneFactures as $ligne)
                            <tr>
                                <td data-label="Description">{{ $ligne->description }}</td>
                                <td data-label="Quantité">{{ $ligne->quantite }}</td>
                                <td data-label="Prix unitaire">{{ number_format($ligne->prix_unitaire, 2) }} DH</td>
                                <td class="total-ligne" data-label="Total">{{ number_format($ligne->total_ligne, 2) }} DH</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Récapitulatif -->
        <div class="recap-section">
            <h3>
                <i class="fas fa-calculator"></i>
                Récapitulatif
            </h3>
            <div class="recap-grid">
                <div class="recap-item">
                    <span class="recap-label">Sous-total :</span>
                    <span class="recap-value">{{ number_format($facture->montant_total, 2) }} DH</span>
                </div>
                @if($facture->remise > 0)
                    <div class="recap-item">
                        <span class="recap-label">Remise :</span>
                        <span class="recap-value">-{{ number_format($facture->remise, 2) }} DH</span>
                    </div>
                @endif
                <div class="recap-item">
                    <span class="recap-label">Total net :</span>
                    <span class="recap-value total-final">{{ number_format($facture->montant_net, 2) }} DH</span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($facture->notes)
            <div class="notes-section">
                <h3>
                    <i class="fas fa-sticky-note"></i>
                    Notes
                </h3>
                <div class="notes-content">
                    {{ nl2br($facture->notes) }}
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="action-buttons">
            <a href="{{ route('factures.index') }}" class="btn-custom btn-secondary-custom">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>

            @if($facture->statut !== 'payée')
                <a href="{{ route('factures.edit', $facture) }}" class="btn-custom btn-warning-custom">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            @endif

            <a href="{{ route('factures.pdf', $facture) }}" class="btn-custom btn-primary-custom" target="_blank">
                <i class="fas fa-print"></i> Imprimer PDF
            </a>

            <form action="{{ route('factures.envoyer', $facture) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-custom btn-success-custom">
                    <i class="fas fa-envelope"></i> Envoyer par Email
                </button>
            </form>

            @if($facture->statut === 'en_attente')
                <form action="{{ route('factures.update-statut', $facture) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="statut" value="payée">
                    <button type="submit" class="btn-custom btn-success-custom">
                        <i class="fas fa-check"></i> Marquer comme payée
                    </button>
                </form>
            @endif

            @if($facture->statut !== 'payée')
                <form action="{{ route('factures.destroy', $facture) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Voulez-vous vraiment supprimer cette facture ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-custom btn-danger-custom">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

