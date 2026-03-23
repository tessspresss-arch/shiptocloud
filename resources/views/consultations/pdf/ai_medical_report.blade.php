<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte rendu IA consultation</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; line-height: 1.5; }
        .header { margin-bottom: 24px; border-bottom: 2px solid #2563eb; padding-bottom: 12px; }
        .header h1 { margin: 0 0 6px; font-size: 22px; color: #1e3a8a; }
        .meta { color: #475569; font-size: 11px; }
        .card { border: 1px solid #dbe5f1; border-radius: 10px; padding: 14px; margin-bottom: 16px; }
        .card h2 { margin: 0 0 10px; font-size: 15px; color: #1e3a8a; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { padding: 4px 0; vertical-align: top; }
        .label { width: 160px; font-weight: bold; color: #334155; }
        .content { white-space: pre-wrap; }
        .footer { margin-top: 20px; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Compte rendu IA de consultation</h1>
        <div class="meta">Consultation #{{ $consultation->id }} - Genere le {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="card">
        <h2>Informations de consultation</h2>
        <table class="grid">
            <tr>
                <td class="label">Patient</td>
                <td>{{ trim(($consultation->patient->prenom ?? '') . ' ' . ($consultation->patient->nom ?? '')) ?: 'Patient #' . $consultation->patient_id }}</td>
            </tr>
            <tr>
                <td class="label">Medecin</td>
                <td>{{ trim(($consultation->medecin->prenom ?? '') . ' ' . ($consultation->medecin->nom ?? '')) ?: 'Medecin #' . $consultation->medecin_id }}</td>
            </tr>
            <tr>
                <td class="label">Date</td>
                <td>{{ optional($consultation->date_consultation)->format('d/m/Y') ?: now()->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>Compte rendu</h2>
        <div class="content">{{ $content }}</div>
    </div>

    <div class="footer">
        Document genere avec l Assistant IA MEDISYS Pro. Validation medicale requise avant diffusion ou archivage definitif.
    </div>
</body>
</html>
