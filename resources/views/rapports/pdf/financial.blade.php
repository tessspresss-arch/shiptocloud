<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Financier - {{ $data['periode'] }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #333; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .stats-grid { display: table; width: 100%; margin: 20px 0; }
        .stat-item { display: table-cell; padding: 10px; text-align: center; border: 1px solid #ddd; }
        .stat-value { font-size: 24px; font-weight: bold; color: #28a745; }
        .stat-label { font-size: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .danger { color: #dc3545; }
        .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport Financier</h1>
        <p>Période: {{ $data['periode'] }}</p>
        <p>Généré le: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Vue d'ensemble</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $data['factures']['total'] }}</div>
                <div class="stat-label">Total Factures</div>
            </div>
            <div class="stat-item">
                <div class="stat-value success">{{ $data['factures']['payees'] }}</div>
                <div class="stat-label">Factures Payées</div>
            </div>
            <div class="stat-item">
                <div class="stat-value warning">{{ $data['factures']['impayees'] }}</div>
                <div class="stat-label">Factures Impayées</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($data['revenus']['total'], 2, ',', ' ') }} DH</div>
                <div class="stat-label">Revenus Totaux</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Détail des Revenus</h2>
        <table>
            <thead>
                <tr>
                    <th>Métrique</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Revenus Totaux</td>
                    <td class="success">{{ number_format($data['revenus']['total'], 2, ',', ' ') }} DH</td>
                </tr>
                <tr>
                    <td>Revenus Moyens par Facture</td>
                    <td>{{ number_format($data['revenus']['moyenne'], 2, ',', ' ') }} DH</td>
                </tr>
                <tr>
                    <td>Taux de Paiement</td>
                    <td>
                        @if($data['factures']['total'] > 0)
                            <span class="{{ $data['factures']['payees'] / $data['factures']['total'] >= 0.8 ? 'success' : 'warning' }}">
                                {{ round(($data['factures']['payees'] / $data['factures']['total']) * 100, 1) }}%
                            </span>
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Créances et Impayés</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Pourcentage du Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Impayés Totaux</td>
                    <td class="warning">{{ number_format($data['impayes']['montant_total'], 2, ',', ' ') }} DH</td>
                    <td>
                        @if($data['revenus']['total'] + $data['impayes']['montant_total'] > 0)
                            {{ round(($data['impayes']['montant_total'] / ($data['revenus']['total'] + $data['impayes']['montant_total'])) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Impayés Échus</td>
                    <td class="danger">{{ number_format($data['impayes']['anciens'], 2, ',', ' ') }} DH</td>
                    <td>
                        @if($data['impayes']['montant_total'] > 0)
                            {{ round(($data['impayes']['anciens'] / $data['impayes']['montant_total']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Évolution Mensuelle des Revenus</h2>
        <table>
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Revenus (DH)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['evolution_mensuelle'] as $month)
                    <tr>
                        <td>{{ $month['mois'] }}</td>
                        <td>{{ number_format($month['revenus'], 2, ',', ' ') }} DH</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Rapport généré automatiquement par SCABINET - {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>


