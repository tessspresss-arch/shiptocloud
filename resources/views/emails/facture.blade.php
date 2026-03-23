<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->numero_facture }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
            border-radius: 3px;
        }
        .info-row {
            margin: 8px 0;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Facture {{ $facture->numero_facture }}</h1>
    </div>

    <div class="content">
        <p>Bonjour {{ $facture->patient->nom }} {{ $facture->patient->prenom }},</p>
        
        <p>Veuillez trouver ci-joint votre facture <strong>{{ $facture->numero_facture }}</strong> du {{ $facture->date_facture->format('d/m/Y') }}.</p>

        <div class="info-box">
            <div class="info-row">
                <span class="label">Numéro de facture :</span> {{ $facture->numero_facture }}
            </div>
            <div class="info-row">
                <span class="label">Date :</span> {{ $facture->date_facture->format('d/m/Y') }}
            </div>
            @if($facture->date_echeance)
            <div class="info-row">
                <span class="label">Date d'échéance :</span> {{ $facture->date_echeance->format('d/m/Y') }}
            </div>
            @endif
            <div class="info-row">
                <span class="label">Statut :</span> <strong>{{ ucfirst($facture->statut) }}</strong>
            </div>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #007bff;">Détails de la facture</h3>
            @foreach($facture->ligneFactures as $ligne)
            <div class="info-row">
                {{ $ligne->description }} - {{ $ligne->quantite }} x {{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} €
            </div>
            @endforeach
            
            @if($facture->remise > 0)
            <div class="info-row" style="margin-top: 15px;">
                <span class="label">Sous-total :</span> {{ number_format($facture->montant_total, 2, ',', ' ') }} €
            </div>
            <div class="info-row">
                <span class="label">Remise :</span> - {{ number_format($facture->remise, 2, ',', ' ') }} €
            </div>
            @endif
            
            <div class="total">
                Total : {{ number_format($facture->montant_net, 2, ',', ' ') }} €
            </div>
        </div>

        @if($facture->notes)
        <div class="info-box">
            <h3 style="margin-top: 0; color: #007bff;">Notes</h3>
            <p>{{ $facture->notes }}</p>
        </div>
        @endif

        <p>Le document PDF est joint à cet email pour vos archives.</p>

        <p>Pour toute question concernant cette facture, n'hésitez pas à nous contacter.</p>

        <div class="footer">
            <p><strong>Cabinet Médical</strong></p>
            <p>Merci de votre confiance</p>
            <p style="font-size: 10px; color: #999;">
                Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>
