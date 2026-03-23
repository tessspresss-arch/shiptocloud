<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Patients - {{ $data['periode'] }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 20px; color: #243b53; }
        .header { text-align: center; border-bottom: 2px solid #1f4f82; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #1f4f82; margin: 0; }
        .header p { color: #52667a; margin: 5px 0; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #1f4f82; border-bottom: 1px solid #d7e3f1; padding-bottom: 6px; margin-bottom: 14px; }
        .stats-grid { display: table; width: 100%; margin: 20px 0; }
        .stat-item { display: table-cell; padding: 12px; text-align: center; border: 1px solid #d7e3f1; background: #f8fbff; }
        .stat-value { font-size: 24px; font-weight: bold; color: #17a2b8; }
        .stat-label { font-size: 12px; color: #5d7285; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #d7e3f1; padding: 8px; text-align: left; }
        th { background-color: #f4f8fc; font-weight: bold; }
        .summary-box { border: 1px solid #d7e3f1; background: #f8fbff; padding: 18px; color: #52667a; }
        .summary-box p { margin: 0 0 8px; }
        .summary-box p:last-child { margin-bottom: 0; }
        .footer { margin-top: 50px; text-align: center; color: #66788a; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport des Patients</h1>
        <p>Periode : {{ $data['periode'] }}</p>
        <p>Genere le : {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Demographie generale</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $data['demographie']['total_patients'] }}</div>
                <div class="stat-label">Total patients</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $data['demographie']['nouveaux_patients'] }}</div>
                <div class="stat-label">Nouveaux patients</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $data['activite']['consultations_total'] }}</div>
                <div class="stat-label">Total consultations</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $data['activite']['patients_actifs'] }}</div>
                <div class="stat-label">Patients actifs</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Repartition par genre</h2>
        <table>
            <thead>
                <tr>
                    <th>Genre</th>
                    <th>Nombre</th>
                    <th>Pourcentage</th>
                </tr>
            </thead>
            <tbody>
                @php $totalGenre = array_sum($data['demographie']['par_genre']->toArray()) @endphp
                @foreach($data['demographie']['par_genre'] as $genre => $count)
                    <tr>
                        <td>{{ ucfirst($genre) }}</td>
                        <td>{{ $count }}</td>
                        <td>
                            @if($totalGenre > 0)
                                {{ round(($count / $totalGenre) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Repartition par age</h2>
        <table>
            <thead>
                <tr>
                    <th>Tranche d'age</th>
                    <th>Nombre</th>
                    <th>Pourcentage</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAge = array_sum($data['demographie']['par_age']->toArray()) @endphp
                @foreach($data['demographie']['par_age'] as $ageGroup => $count)
                    <tr>
                        <td>{{ $ageGroup }}</td>
                        <td>{{ $count }}</td>
                        <td>
                            @if($totalAge > 0)
                                {{ round(($count / $totalAge) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Activite des patients</h2>
        <table>
            <thead>
                <tr>
                    <th>Metrique</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nombre de patients actifs</td>
                    <td>{{ $data['activite']['patients_actifs'] }}</td>
                </tr>
                <tr>
                    <td>Total des consultations</td>
                    <td>{{ $data['activite']['consultations_total'] }}</td>
                </tr>
                <tr>
                    <td>Moyenne de consultations par patient actif</td>
                    <td>{{ number_format($data['activite']['moyenne_consultations_par_patient'], 1, ',', ' ') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Top 10 pathologies</h2>
        <table>
            <thead>
                <tr>
                    <th>Pathologie</th>
                    <th>Nombre de cas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['top_pathologies'] as $pathology)
                    <tr>
                        <td>{{ $pathology->diagnostic }}</td>
                        <td>{{ $pathology->count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Synthese</h2>
        <div class="summary-box">
            <p>Ce rapport presente les principaux indicateurs du portefeuille patients sur la periode selectionnee.</p>
            <p>Il peut etre utilise comme support de suivi d'activite, sans modifier les donnees du dossier patient.</p>
            <p>Les visualisations graphiques detaillees seront ajoutees dans une version ulterieure du module Rapports.</p>
        </div>
    </div>

    <div class="footer">
        <p>Rapport genere automatiquement par SCABINET - {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
