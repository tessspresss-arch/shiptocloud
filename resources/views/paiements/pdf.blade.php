<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Registre des paiements</title>
    <style>
        body {
            margin: 24px;
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 2px solid #dbe7f1;
        }
        .header > div {
            display: table-cell;
            vertical-align: top;
        }
        .header-right {
            text-align: right;
        }
        .kicker {
            margin: 0 0 6px;
            color: #2563eb;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        h1 {
            margin: 0 0 6px;
            font-size: 26px;
            line-height: 1.1;
        }
        .copy {
            margin: 0;
            color: #64748b;
            line-height: 1.5;
        }
        .stats {
            width: 100%;
            margin: 18px 0 20px;
            border-collapse: separate;
            border-spacing: 0;
        }
        .stats td {
            width: 33.33%;
            padding: 14px 16px;
            border: 1px solid #dbe7f1;
            background: #f8fbff;
        }
        .stats .label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .stats .value {
            font-size: 18px;
            font-weight: bold;
            line-height: 1.1;
        }
        .stats .value.income { color: #047857; }
        .stats .value.expense { color: #b45309; }
        table.sheet {
            width: 100%;
            border-collapse: collapse;
        }
        table.sheet thead th {
            padding: 10px 12px;
            border-bottom: 1px solid #cfdbe7;
            background: #f8fbff;
            color: #64748b;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .08em;
            text-align: left;
        }
        table.sheet tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5edf5;
            vertical-align: top;
            line-height: 1.45;
        }
        .main {
            font-weight: bold;
            color: #0f172a;
        }
        .sub {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 11px;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .amount.income { color: #047857; }
        .amount.expense { color: #b45309; }
        .footer {
            margin-top: 16px;
            color: #64748b;
            font-size: 11px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <p class="kicker">MEDISYS Pro</p>
            <h1>Registre des paiements</h1>
            <p class="copy">Export consolidé des encaissements et décaissements liés à la facturation, aux dépenses et aux examens.</p>
        </div>
        <div class="header-right">
            <p class="copy">Généré le {{ $generatedAt->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <table class="stats">
        <tr>
            <td>
                <span class="label">Encaissements</span>
                <span class="value income">{{ number_format($stats['encaissements'], 2, ',', ' ') }} DH</span>
            </td>
            <td>
                <span class="label">Décaissements</span>
                <span class="value expense">{{ number_format($stats['decaissements'], 2, ',', ' ') }} DH</span>
            </td>
            <td>
                <span class="label">Opérations</span>
                <span class="value">{{ $stats['operations'] }}</span>
            </td>
        </tr>
    </table>

    <table class="sheet">
        <thead>
            <tr>
                <th>Source</th>
                <th>Référence</th>
                <th>Tiers</th>
                <th>Médecin</th>
                <th>Date</th>
                <th>Mode</th>
                <th>Statut</th>
                <th style="text-align: right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td><span class="main">{{ $entry['source_label'] }}</span></td>
                    <td><span class="main">{{ $entry['reference'] }}</span></td>
                    <td>
                        <span class="main">{{ $entry['tiers'] }}</span>
                        @if(!empty($entry['patient']))
                            <span class="sub">{{ $entry['patient'] }}</span>
                        @endif
                    </td>
                    <td>{{ $entry['medecin'] ?: '-' }}</td>
                    <td>
                        <span class="main">{{ optional($entry['date_operation'])->format('d/m/Y') ?: '-' }}</span>
                        <span class="sub">{{ optional($entry['date_operation'])->format('H:i') ?: '' }}</span>
                    </td>
                    <td>{{ $entry['mode_paiement'] }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $entry['statut'])) }}</td>
                    <td class="amount {{ $entry['montant'] < 0 ? 'expense' : 'income' }}">{{ number_format((float) $entry['montant'], 2, ',', ' ') }} DH</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Aucune opération de paiement disponible.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">MEDISYS Pro • Registre des paiements</div>
</body>
</html>
