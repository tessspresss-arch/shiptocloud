<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->numero_facture }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-section h3 {
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 40%;
            padding: 5px 0;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals table td {
            padding: 8px;
        }
        .totals .total-final {
            font-weight: bold;
            font-size: 16px;
            background-color: #007bff;
            color: white;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            clear: both;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .status-en_attente {
            background-color: #ffc107;
            color: #000;
        }
        .status-payee {
            background-color: #28a745;
            color: white;
        }
        .status-brouillon {
            background-color: #6c757d;
            color: white;
        }
        .status-annulee {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    @php
        $statusClass = str($facture->statut)->lower()->replace(['é', 'è', 'ê', 'à', 'ù', ' '], ['e', 'e', 'e', 'a', 'u', '_']);
    @endphp
    <div class="header">
        <h1>FACTURE</h1>
        <p style="margin: 5px 0;">{{ $facture->numero_facture }}</p>
        <span class="status-badge status-{{ $statusClass }}">{{ ucfirst($facture->statut) }}</span>
    </div>

    <div style="margin-bottom: 30px;">
        <div style="width: 48%; float: left;">
            <div class="info-section">
                <h3>Informations Patient</h3>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nom :</div>
                        <div class="info-value">{{ $facture->patient->nom }} {{ $facture->patient->prenom }}</div>
                    </div>
                    @if($facture->patient->email)
                    <div class="info-row">
                        <div class="info-label">Email :</div>
                        <div class="info-value">{{ $facture->patient->email }}</div>
                    </div>
                    @endif
                    @if($facture->patient->telephone)
                    <div class="info-row">
                        <div class="info-label">Telephone :</div>
                        <div class="info-value">{{ $facture->patient->telephone }}</div>
                    </div>
                    @endif
                    @if($facture->patient->adresse)
                    <div class="info-row">
                        <div class="info-label">Adresse :</div>
                        <div class="info-value">{{ $facture->patient->adresse }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div style="width: 48%; float: right;">
            <div class="info-section">
                <h3>Details de la Facture</h3>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Date :</div>
                        <div class="info-value">{{ optional($facture->date_facture)->format('d/m/Y') }}</div>
                    </div>
                    @if($facture->date_echeance)
                    <div class="info-row">
                        <div class="info-label">Echeance :</div>
                        <div class="info-value">{{ optional($facture->date_echeance)->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    @if($facture->medecin)
                    <div class="info-row">
                        <div class="info-label">Medecin :</div>
                        <div class="info-value">Dr. {{ $facture->medecin->nom }} {{ $facture->medecin->prenom }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="info-section">
        <h3>Prestations</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Quantite</th>
                    <th class="text-right">Prix Unitaire (DH)</th>
                    <th class="text-right">Total (DH)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facture->ligneFactures as $ligne)
                <tr>
                    <td>{{ $ligne->description }}</td>
                    <td class="text-right">{{ $ligne->quantite }}</td>
                    <td class="text-right">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} DH</td>
                    <td class="text-right">{{ number_format($ligne->total_ligne, 2, ',', ' ') }} DH</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals">
        <table>
            <tr>
                <td>Sous-total :</td>
                <td class="text-right">{{ number_format($facture->montant_total, 2, ',', ' ') }} DH</td>
            </tr>
            @if($facture->remise > 0)
            <tr>
                <td>Remise :</td>
                <td class="text-right">- {{ number_format($facture->remise, 2, ',', ' ') }} DH</td>
            </tr>
            @endif
            <tr class="total-final">
                <td>TOTAL :</td>
                <td class="text-right">{{ number_format($facture->montant_net, 2, ',', ' ') }} DH</td>
            </tr>
        </table>
    </div>

    @if($facture->notes)
    <div class="info-section" style="clear: both; margin-top: 30px;">
        <h3>Notes</h3>
        <p>{{ $facture->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Facture generee le {{ now()->format('d/m/Y a H:i') }}</p>
        <p>Cabinet Medical - Merci de votre confiance</p>
    </div>
</body>
</html>


