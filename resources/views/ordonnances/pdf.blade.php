<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ordonnance</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 28px; color: #173761; font-size: 12px; }
        .header { border-bottom: 2px solid #dbe7f8; padding-bottom: 14px; margin-bottom: 18px; }
        .eyebrow { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c86a8; }
        h1 { margin: 6px 0 4px; font-size: 24px; }
        .subtitle { color: #5e7899; margin: 0; }
        .grid { width: 100%; margin: 18px 0; border-collapse: separate; border-spacing: 12px 0; }
        .card { border: 1px solid #dce7f6; border-radius: 12px; padding: 12px 14px; background: #f9fbff; vertical-align: top; }
        .label { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6f88a9; margin-bottom: 6px; }
        .value { color: #173761; font-size: 13px; font-weight: 700; line-height: 1.45; white-space: pre-line; }
        .section { margin-top: 20px; }
        .section h2 { font-size: 15px; margin: 0 0 8px; color: #173761; }
        .section p { margin: 0; color: #385577; line-height: 1.7; white-space: pre-line; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #dbe7f6; padding: 10px 12px; text-align: left; vertical-align: top; }
        th { background: #eff5ff; color: #1f4f86; font-size: 10px; letter-spacing: .08em; text-transform: uppercase; }
        td { color: #2d4c70; line-height: 1.5; }
        .muted { color: #6a85a6; }
        .footer-note { margin-top: 24px; font-size: 11px; color: #6b86a7; }
    </style>
</head>
<body>
    <div class="header">
        <div class="eyebrow">{{ $isPreview ? 'Apercu d ordonnance' : 'Ordonnance medicale' }}</div>
        <h1>{{ $ordonnanceNumber }}</h1>
        <p class="subtitle">Date de prescription : {{ $datePrescription ? \Illuminate\Support\Carbon::parse($datePrescription)->format('d/m/Y') : '-' }}</p>
    </div>

    <table class="grid">
        <tr>
            <td class="card" width="50%">
                <div class="label">Patient</div>
                <div class="value">{{ $patientName }}</div>
                <div class="muted">{{ $patientIdentifier }}</div>
            </td>
            <td class="card" width="50%">
                <div class="label">Prescripteur</div>
                <div class="value">{{ $doctorName }}</div>
                <div class="muted">{{ $doctorSpeciality ?: 'Specialite non renseignee' }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <h2>Diagnostic / contexte</h2>
        <p>{{ $diagnostic ?: 'Aucun diagnostic renseigne.' }}</p>
    </div>

    <div class="section">
        <h2>Instructions generales</h2>
        <p>{{ $instructions ?: 'Aucune instruction generale.' }}</p>
    </div>

    <div class="section">
        <h2>Traitement prescrit</h2>
        <table>
            <thead>
                <tr>
                    <th>Medicament</th>
                    <th>Posologie</th>
                    <th>Duree</th>
                    <th>Quantite</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicationRows as $row)
                    <tr>
                        <td>{{ $row['medicament'] ?? '-' }}</td>
                        <td>{{ $row['posologie'] ?? '-' }}</td>
                        <td>{{ $row['duree'] ?? '-' }}</td>
                        <td>{{ $row['quantite'] ?? '-' }}</td>
                        <td>{{ $row['instructions'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Aucun medicament renseigne.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="footer-note">
        {{ $isPreview ? 'Document genere a partir du formulaire en cours, sans enregistrement automatique.' : 'Document genere depuis l ordonnance enregistree dans MEDISYS Pro.' }}
    </p>
</body>
</html>
