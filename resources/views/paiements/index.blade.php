@extends('layouts.app')

@section('title', 'Paiements')

@push('styles')
<style>
    .payments-page {
        width: 100%;
        max-width: none;
        padding: 8px 8px 92px;
    }

    .payments-shell {
        display: grid;
        gap: 18px;
    }

    .payments-hero,
    .payments-kpi-card,
    .payments-filter-card,
    .payments-table-card {
        position: relative;
        overflow: hidden;
        border: 1px solid #dbe7f1;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 18px 34px -32px rgba(15, 40, 65, 0.28);
    }

    .payments-hero {
        padding: 24px;
        background:
            radial-gradient(circle at top right, rgba(37, 99, 235, 0.10) 0%, rgba(37, 99, 235, 0) 34%),
            linear-gradient(180deg, #f8fbff 0%, #f4f8fc 100%);
    }

    .payments-kpi-card,
    .payments-filter-card,
    .payments-table-card {
        padding: 20px;
    }

    .payments-hero-grid,
    .payments-table-head {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
    }

    .payments-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(37, 99, 235, 0.16);
        background: rgba(255, 255, 255, 0.82);
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .payments-title {
        margin: 14px 0 8px;
        color: #0f172a;
        font-size: clamp(1.9rem, 2.5vw, 2.5rem);
        line-height: 1.08;
        font-weight: 600;
        letter-spacing: -0.04em;
    }

    .payments-subtitle,
    .payments-table-copy {
        margin: 0;
        max-width: 72ch;
        color: #64748b;
        font-size: 15px;
        font-weight: 400;
        line-height: 1.65;
    }

    .payments-actions,
    .payments-table-tools,
    .payments-table-meta,
    .payments-mode-cell,
    .payments-actions-cell {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .payments-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 46px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid transparent;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        line-height: 1;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease;
    }

    .payments-btn:hover,
    .payments-btn:focus {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .payments-btn.primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
        box-shadow: 0 18px 30px -24px rgba(37, 99, 235, 0.58);
    }

    .payments-btn.secondary {
        background: linear-gradient(180deg, #ffffff 0%, #f5f8fc 100%);
        border-color: #d7e3ef;
        color: #334155;
    }

    .payments-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .payments-breakdown-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .payments-kpi-label {
        margin: 0 0 10px;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .payments-kpi-value {
        margin: 0;
        color: #0f172a;
        font-size: clamp(2rem, 3vw, 2.8rem);
        line-height: 1.02;
        font-weight: 700;
        letter-spacing: -0.05em;
    }

    .payments-kpi-value.is-income {
        color: #047857;
    }

    .payments-kpi-value.is-expense {
        color: #b45309;
    }

    .payments-kpi-foot {
        margin-top: 10px;
        color: #64748b;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
    }

    .payments-breakdown-list {
        display: grid;
        gap: 12px;
        margin-top: 14px;
    }

    .payments-breakdown-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 14px 16px;
        border: 1px solid #e2eaf2;
        border-radius: 18px;
        background: #fbfdff;
    }

    .payments-breakdown-name {
        color: #0f172a;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.45;
    }

    .payments-breakdown-meta {
        color: #64748b;
        font-size: 13px;
        font-weight: 400;
        line-height: 1.45;
    }

    .payments-breakdown-amount {
        text-align: right;
        color: #0f172a;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.1;
    }

    .payments-breakdown-amount.income {
        color: #047857;
    }

    .payments-breakdown-amount.expense {
        color: #b45309;
    }

    .payments-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) repeat(3, minmax(180px, .8fr)) auto;
        gap: 16px;
        align-items: end;
    }

    .payments-field {
        display: grid;
        gap: 8px;
    }

    .payments-field label {
        margin: 0;
        color: #475569;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.3;
    }

    .payments-field .form-control,
    .payments-field .form-select {
        min-height: 46px;
        border-radius: 15px;
        border-color: #d7e3ef;
        font-size: 15px;
        font-weight: 400;
        color: #1f2937;
        box-shadow: none;
    }

    .payments-field .form-control::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .payments-filter-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .payments-table-kicker {
        margin: 0 0 8px;
        color: #718096;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .payments-table-title {
        margin: 0 0 6px;
        color: #0f172a;
        font-size: 24px;
        line-height: 1.15;
        font-weight: 600;
    }

    .payments-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #d7e3ef;
        background: #f8fbff;
        color: #57728c;
        font-size: 13px;
        font-weight: 500;
    }

    .payments-meta-chip i {
        color: #2563eb;
    }

    .payments-table-wrap {
        overflow: hidden;
        border: 1px solid #e2eaf2;
        border-radius: 22px;
        background: #fff;
    }

    .payments-table {
        margin: 0;
        width: 100%;
        font-family: "Segoe UI", Tahoma, Arial, sans-serif;
    }

    .payments-table thead th {
        padding: 18px 22px;
        border-bottom: 1px solid #dbe7f1;
        color: #111827;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0;
        text-transform: none;
        vertical-align: middle;
        background: #f8fbff;
    }

    .payments-table tbody td {
        padding: 18px 22px;
        border-bottom: 1px solid #ecf2f7;
        vertical-align: middle;
        color: #111827;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
    }

    .payments-table tbody tr:hover {
        background: #f8fafc;
    }

    .payments-main-text {
        color: #111827;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.45;
    }

    .payments-sub-text {
        color: #6b7280;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.45;
    }

    .payments-amount {
        display: inline-flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 3px;
    }

    .payments-amount strong {
        color: #111827;
        font-size: 15px;
        line-height: 1.15;
        font-weight: 600;
        letter-spacing: 0;
    }

    .payments-amount.income strong {
        color: #047857;
    }

    .payments-amount.expense strong {
        color: #b45309;
    }

    .payments-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        line-height: 1;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .payments-badge::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
        opacity: .75;
    }

    .payments-badge.mode-cash,
    .payments-badge.mode-especes {
        color: #b45309;
        background: #fff7ed;
        border-color: #fed7aa;
    }

    .payments-badge.mode-card,
    .payments-badge.mode-carte,
    .payments-badge.mode-carte-bancaire {
        color: #1d4ed8;
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .payments-badge.mode-virement,
    .payments-badge.mode-transfer {
        color: #0f766e;
        background: #f0fdfa;
        border-color: #99f6e4;
    }

    .payments-badge.mode-cheque {
        color: #7c3aed;
        background: #f5f3ff;
        border-color: #ddd6fe;
    }

    .payments-badge.mode-non-defini,
    .payments-badge.mode-regle,
    .payments-badge.mode-a-definir {
        color: #475569;
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .payments-status {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .payments-status.status-payee,
    .payments-status.status-reglee,
    .payments-status.status-paye {
        color: #047857;
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .payments-status.status-impayee,
    .payments-status.status-en-retard {
        color: #b91c1c;
        background: #fef2f2;
        border-color: #fecaca;
    }

    .payments-status.status-en-attente,
    .payments-status.status-a-faire {
        color: #b45309;
        background: #fff7ed;
        border-color: #fed7aa;
    }

    .payments-empty {
        padding: 56px 24px;
        text-align: center;
        color: #64748b;
        font-size: 15px;
        font-weight: 400;
    }

    @media (max-width: 1200px) {
        .payments-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .payments-breakdown-grid {
            grid-template-columns: 1fr;
        }

        .payments-filter-form {
            grid-template-columns: 1fr 1fr;
        }

        .payments-filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 992px) {
        .payments-hero-grid,
        .payments-table-head {
            flex-direction: column;
            align-items: stretch;
        }

        .payments-actions,
        .payments-table-tools,
        .payments-table-meta {
            width: 100%;
            justify-content: flex-start;
        }

        .payments-table-wrap {
            overflow: visible;
            border: 0;
            border-radius: 0;
            background: transparent;
        }

        .payments-table {
            display: block;
            background: transparent;
        }

        .payments-table thead {
            display: none;
        }

        .payments-table tbody {
            display: grid;
            gap: 14px;
        }

        .payments-table tbody tr {
            display: grid;
            gap: 12px;
            padding: 18px;
            border: 1px solid #dbe7f1;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 16px 28px -28px rgba(15, 40, 65, 0.3);
        }

        .payments-table tbody td {
        padding: 18px 22px;
        border-bottom: 1px solid #ecf2f7;
        vertical-align: middle;
        color: #111827;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
    }

        .payments-table tbody td::before {
            content: attr(data-label);
            color: #718aa3;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .payments-table tbody td[data-label="Actions"] {
            grid-template-columns: 1fr;
        }

        .payments-actions-cell {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767px) {
        .payments-page {
            padding-left: 0;
            padding-right: 0;
        }

        .payments-kpi-grid,
        .payments-breakdown-grid,
        .payments-filter-form {
            grid-template-columns: 1fr;
        }

        .payments-filter-actions,
        .payments-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .payments-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="payments-page">
    <div class="payments-shell">
        <section class="payments-hero">
            <div class="payments-hero-grid">
                <div>
                    <span class="payments-eyebrow">Registre des paiements</span>
                    <h1 class="payments-title">Paiements</h1>
                    <p class="payments-subtitle">Vue unifiée des encaissements et décaissements liés à la facturation, aux dépenses et aux examens.</p>
                </div>
                <div class="payments-actions">
                    <a href="{{ route('paiements.export', request()->query()) }}" class="payments-btn secondary">
                        <i class="fas fa-file-csv"></i>Exporter CSV
                    </a>
                    <a href="{{ route('paiements.export-pdf', request()->query()) }}" class="payments-btn secondary">
                        <i class="fas fa-file-pdf"></i>Exporter PDF
                    </a>
                    <a href="{{ route('factures.index') }}" class="payments-btn primary">
                        <i class="fas fa-file-invoice-dollar"></i>Factures
                    </a>
                </div>
            </div>
        </section>

        <section class="payments-kpi-grid">
            <article class="payments-kpi-card">
                <p class="payments-kpi-label">Encaissements</p>
                <p class="payments-kpi-value is-income">{{ number_format($stats['encaissements'], 2, ',', ' ') }} DH</p>
                <p class="payments-kpi-foot">Montants réellement encaissés sur les opérations positives.</p>
            </article>
            <article class="payments-kpi-card">
                <p class="payments-kpi-label">Décaissements</p>
                <p class="payments-kpi-value is-expense">{{ number_format($stats['decaissements'], 2, ',', ' ') }} DH</p>
                <p class="payments-kpi-foot">Sorties de trésorerie liées aux dépenses et règlements sortants.</p>
            </article>
            <article class="payments-kpi-card">
                <p class="payments-kpi-label">Opérations</p>
                <p class="payments-kpi-value">{{ $stats['operations'] }}</p>
                <p class="payments-kpi-foot">Volume total d’opérations remontées dans le registre unifié.</p>
            </article>
        </section>

        <section class="payments-filter-card">
            <form method="GET" class="payments-filter-form">
                <div class="payments-field">
                    <label for="paymentSearch">Recherche</label>
                    <input id="paymentSearch" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Référence, patient, bénéficiaire...">
                </div>
                <div class="payments-field">
                    <label for="paymentSource">Source</label>
                    <select id="paymentSource" name="source" class="form-select">
                        <option value="">Toutes les sources</option>
                        <option value="factures" @selected($selectedSource === 'factures')>Factures</option>
                        <option value="depenses" @selected($selectedSource === 'depenses')>Dépenses</option>
                        <option value="examens" @selected($selectedSource === 'examens')>Examens</option>
                    </select>
                </div>
                <div class="payments-field">
                    <label for="paymentMode">Mode</label>
                    <select id="paymentMode" name="mode" class="form-select">
                        <option value="">Tous les modes</option>
                        @foreach($modeOptions as $modeKey => $modeLabel)
                            <option value="{{ $modeKey }}" @selected($selectedMode === $modeKey)>{{ $modeLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="payments-field">
                    <label for="paymentStatus">Statut</label>
                    <select id="paymentStatus" name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        @foreach($statusOptions as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" @selected($selectedStatus === $statusKey)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="payments-field">
                    <label for="paymentDateFrom">Du</label>
                    <input id="paymentDateFrom" type="date" name="date_from" value="{{ $selectedDateFrom }}" class="form-control">
                </div>
                <div class="payments-field">
                    <label for="paymentDateTo">Au</label>
                    <input id="paymentDateTo" type="date" name="date_to" value="{{ $selectedDateTo }}" class="form-control">
                </div>
                <div class="payments-filter-actions">
                    <button type="submit" class="payments-btn primary">Appliquer</button>
                    <a href="{{ route('paiements.index') }}" class="payments-btn secondary">Réinitialiser</a>
                </div>
            </form>
        </section>

        <section class="payments-breakdown-grid">
            <article class="payments-kpi-card">
                <p class="payments-table-kicker">Regroupement</p>
                <h2 class="payments-table-title">Par source</h2>
                <div class="payments-breakdown-list">
                    @forelse($sourceBreakdown as $group)
                        <div class="payments-breakdown-item">
                            <div>
                                <div class="payments-breakdown-name">{{ $group['label'] }}</div>
                                <div class="payments-breakdown-meta">{{ $group['count'] }} opération{{ $group['count'] > 1 ? 's' : '' }}</div>
                            </div>
                            <div class="payments-breakdown-amount {{ $group['amount'] < 0 ? 'expense' : 'income' }}">
                                {{ number_format((float) $group['amount'], 2, ',', ' ') }} DH
                            </div>
                        </div>
                    @empty
                        <div class="payments-breakdown-item">
                            <div class="payments-breakdown-meta">Aucune donnée disponible.</div>
                        </div>
                    @endforelse
                </div>
            </article>
            <article class="payments-kpi-card">
                <p class="payments-table-kicker">Regroupement</p>
                <h2 class="payments-table-title">Par mode de paiement</h2>
                <div class="payments-breakdown-list">
                    @forelse($modeBreakdown as $group)
                        <div class="payments-breakdown-item">
                            <div>
                                <div class="payments-breakdown-name">{{ $group['label'] }}</div>
                                <div class="payments-breakdown-meta">{{ $group['count'] }} opération{{ $group['count'] > 1 ? 's' : '' }}</div>
                            </div>
                            <div class="payments-breakdown-amount {{ $group['amount'] < 0 ? 'expense' : 'income' }}">
                                {{ number_format((float) $group['amount'], 2, ',', ' ') }} DH
                            </div>
                        </div>
                    @empty
                        <div class="payments-breakdown-item">
                            <div class="payments-breakdown-meta">Aucune donnée disponible.</div>
                        </div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="payments-table-card">
            <div class="payments-table-head">
                <div>
                    <p class="payments-table-kicker">Finance</p>
                    <h2 class="payments-table-title">Registre des opérations</h2>
                    <p class="payments-table-copy">Lecture consolidée des règlements entrants et sortants avec source, mode, statut et montant.</p>
                </div>
                <div class="payments-table-tools">
                    <div class="payments-table-meta">
                        <span class="payments-meta-chip"><i class="fas fa-table"></i>{{ $entries->count() }} affichée{{ $entries->count() > 1 ? 's' : '' }}</span>
                        <span class="payments-meta-chip"><i class="fas fa-scale-balanced"></i>Lecture financière unifiée</span>
                    </div>
                </div>
            </div>

            <div class="payments-table-wrap">
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Référence</th>
                            <th>Tiers</th>
                            <th>Médecin</th>
                            <th>Date</th>
                            <th>Mode</th>
                            <th>Statut</th>
                            <th class="text-end">Montant</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $entry)
                            <tr>
                                <td data-label="Source">
                                    <span class="payments-main-text">{{ $entry['source_label'] }}</span>
                                </td>
                                <td data-label="Référence">
                                    <span class="payments-main-text">{{ $entry['reference'] }}</span>
                                </td>
                                <td data-label="Tiers">
                                    <div class="payments-main-text">{{ $entry['tiers'] }}</div>
                                    @if($entry['patient'])
                                        <div class="payments-sub-text">{{ $entry['patient'] }}</div>
                                    @endif
                                </td>
                                <td data-label="Médecin">
                                    <span class="payments-main-text">{{ $entry['medecin'] ?: '-' }}</span>
                                </td>
                                <td data-label="Date">
                                    <span class="payments-main-text">{{ optional($entry['date_operation'])->format('d/m/Y') ?: '-' }}</span>
                                    <div class="payments-sub-text">{{ optional($entry['date_operation'])->format('H:i') ?: '' }}</div>
                                </td>
                                <td data-label="Mode">
                                    @php($modeKey = \Illuminate\Support\Str::of($entry['mode_paiement'])->lower()->ascii()->replace([' ', '/', '_'], '-'))
                                    <div class="payments-mode-cell">
                                        <span class="payments-badge mode-{{ $modeKey }}">{{ $entry['mode_paiement'] }}</span>
                                    </div>
                                </td>
                                <td data-label="Statut">
                                    @php($statusKey = \Illuminate\Support\Str::of($entry['statut'])->lower()->ascii()->replace([' ', '/', '_'], '-'))
                                    <span class="payments-status status-{{ $statusKey }}">{{ ucfirst(str_replace('_', ' ', $entry['statut'])) }}</span>
                                </td>
                                <td data-label="Montant" class="text-end">
                                    <span class="payments-amount {{ $entry['montant'] < 0 ? 'expense' : 'income' }}">
                                        <strong>{{ number_format((float) $entry['montant'], 2, ',', ' ') }} DH</strong>
                                        <span class="payments-sub-text">{{ $entry['montant'] < 0 ? 'Sortie' : 'Entrée' }}</span>
                                    </span>
                                </td>
                                <td data-label="Actions" class="text-end">
                                    <div class="payments-actions-cell justify-content-end">
                                        <a href="{{ $entry['detail_url'] }}" class="action-btn action-tone-view" title="Voir" aria-label="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="payments-empty">Aucune opération de paiement disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $entries->links() }}
            </div>
        </section>
    </div>
</div>
@endsection






