<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Mensuel - {{ $data['periode'] }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #333; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .stats-grid { display: table; width: 100%; margin: 20px 0; }
        .stat-item { display: table-cell; padding: 10px; text-align: center; border: 1px solid #ddd; }
        .stat-value { font-size: 24px; font-weight: bold; color: #007bff; }
        .stat-label { font-size: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport Mensuel d'Activité</h1>
        <p>Période: {{ $data['periode'] }}</p>
        <p>Généré le: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Statistiques Générales</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $data['consultations']['total'] }}</div>
                <div class="stat-label">Consultations</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $data['patients']['nouveaux'] }}</div>
                <div class="stat-label">Nouveaux Patients</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $data['rendez_vous']['total'] }}</div>
                <div class="stat-label">Rendez-vous</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($data['revenus']['total'], 2, ',', ' ') }} DH</div>
                <div class="stat-label">Revenus</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Consultations par Médecin</h2>
        <table>
            <thead>
                <tr>
                    <th>Médecin</th>
                    <th>Nombre de Consultations</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['consultations']['par_medecin'] as $stat)
                    <tr>
                        <td>{{ $stat['medecin']['nom'] ?? 'N/A' }} {{ $stat['medecin']['prenom'] ?? '' }}</td>
                        <td>{{ $stat['count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Rendez-vous</h2>
        <table>
            <thead>
                <tr>
                    <th>Total</th>
                    <th>Confirmés</th>
                    <th>Taux de Confirmation</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data['rendez_vous']['total'] }}</td>
                    <td>{{ $data['rendez_vous']['confirme'] }}</td>
                    <td>
                        @if($data['rendez_vous']['total'] > 0)
                            {{ round(($data['rendez_vous']['confirme'] / $data['rendez_vous']['total']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Revenus</h2>
        <table>
            <thead>
                <tr>
                    <th>Total</th>
                    <th>Moyenne par Consultation</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ number_format($data['revenus']['total'], 2, ',', ' ') }} DH</td>
                    <td>{{ number_format($data['revenus']['moyenne_par_consultation'], 2, ',', ' ') }} DH</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Rapport généré automatiquement par SCABINET - {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>



