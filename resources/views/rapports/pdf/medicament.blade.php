<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Médicaments - {{ $data['periode'] }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #333; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .stats-grid { display: table; width: 100%; margin: 20px 0; }
        .stat-item { display: table-cell; padding: 10px; text-align: center; border: 1px solid #ddd; }
        .stat-value { font-size: 24px; font-weight: bold; color: #fd7e14; }
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
        <h1>Rapport des Médicaments</h1>
        <p>Période: {{ $data['periode'] }}</p>
        <p>Généré le: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>&Eacute;tat du Stock</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $data['stock']['total_medicaments'] }}</div>
                <div class="stat-label">Total Médicaments</div>
            </div>
            <div class="stat-item">
                <div class="stat-value warning">{{ $data['stock']['stock_faible'] }}</div>
                <div class="stat-label">Stock Faible</div>
            </div>
            <div class="stat-item">
                <div class="stat-value danger">{{ $data['stock']['rupture_stock'] }}</div>
                <div class="stat-label">Rupture de Stock</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($data['stock']['valeur_totale_stock'], 2, ',', ' ') }} DH</div>
                <div class="stat-label">Valeur Totale Stock</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Mouvements de Stock</h2>
        <table>
            <thead>
                <tr>
                    <th>Type de Mouvement</th>
                    <th>Quantité</th>
                    <th>Valeur (DH)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Entrées</td>
                    <td class="success">+{{ $data['mouvements']['entrees'] }}</td>
                    <td class="success">{{ number_format($data['mouvements']['valeur_entrees'], 2, ',', ' ') }} DH</td>
                </tr>
                <tr>
                    <td>Sorties</td>
                    <td class="warning">-{{ $data['mouvements']['sorties'] }}</td>
                    <td class="warning">{{ number_format($data['mouvements']['valeur_sorties'], 2, ',', ' ') }} DH</td>
                </tr>
                <tr>
                    <td><strong>Variation Nette</strong></td>
                    <td class="{{ $data['mouvements']['entrees'] - $data['mouvements']['sorties'] >= 0 ? 'success' : 'warning' }}">
                        <strong>{{ $data['mouvements']['entrees'] - $data['mouvements']['sorties'] >= 0 ? '+' : '' }}{{ $data['mouvements']['entrees'] - $data['mouvements']['sorties'] }}</strong>
                    </td>
                    <td class="{{ $data['mouvements']['valeur_entrees'] - $data['mouvements']['valeur_sorties'] >= 0 ? 'success' : 'warning' }}">
                        <strong>{{ number_format($data['mouvements']['valeur_entrees'] - $data['mouvements']['valeur_sorties'], 2, ',', ' ') }} DH</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Top 10 Médicaments les Plus Utilisés</h2>
        <table>
            <thead>
                <tr>
                    <th>Médicament</th>
                    <th>Quantité Distribuée</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['top_medicaments'] as $medicament)
                    <tr>
                        <td>{{ $medicament['medicament'] }}</td>
                        <td>{{ $medicament['quantite'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Alertes et Anomalies</h2>
        <table>
            <thead>
                <tr>
                    <th>Type d'Alerte</th>
                    <th>Nombre</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="danger">Médicaments Périmés</td>
                    <td class="danger">{{ $data['alertes']['perimes'] }}</td>
                    <td>Médicaments dont la date de péremption est dépassée</td>
                </tr>
                <tr>
                    <td class="warning">Péremption Prochaine</td>
                    <td class="warning">{{ $data['alertes']['peremption_proche'] }}</td>
                    <td>Médicaments expirant dans moins de 30 jours</td>
                </tr>
                <tr>
                    <td class="warning">Stock Faible</td>
                    <td class="warning">{{ $data['stock']['stock_faible'] }}</td>
                    <td>Médicaments en dessous du seuil de réapprovisionnement</td>
                </tr>
                <tr>
                    <td class="danger">Rupture de Stock</td>
                    <td class="danger">{{ $data['stock']['rupture_stock'] }}</td>
                    <td>Médicaments complètement épuisés</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Recommandations</h2>
        <ul>
            @if($data['stock']['stock_faible'] > 0)
                <li><strong>Réapprovisionnement nécessaire:</strong> {{ $data['stock']['stock_faible'] }} médicaments sont en stock faible</li>
            @endif
            @if($data['stock']['rupture_stock'] > 0)
                <li><strong>Urgence - Rupture de stock:</strong> {{ $data['stock']['rupture_stock'] }} médicaments sont en rupture</li>
            @endif
            @if($data['alertes']['perimes'] > 0)
                <li><strong>Action requise - Péremption:</strong> {{ $data['alertes']['perimes'] }} médicaments sont périmés</li>
            @endif
            @if($data['alertes']['peremption_proche'] > 0)
                <li><strong>Surveillance - Péremption proche:</strong> {{ $data['alertes']['peremption_proche'] }} médicaments expirent bientôt</li>
            @endif
            @if($data['mouvements']['entrees'] - $data['mouvements']['sorties'] < 0)
                <li><strong>Tendance à surveiller:</strong> Le stock global diminue sur la période</li>
            @endif
        </ul>
    </div>

    <div class="footer">
        <p>Rapport généré automatiquement par SCABINET - {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>



