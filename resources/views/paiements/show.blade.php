@extends('layouts.app')

@section('title', 'Paiement ' . $entry['reference'])

@push('styles')
<style>
    .payment-show-page {
        width: 100%;
        max-width: none;
        padding: 8px 8px 92px;
    }

    .payment-show-shell {
        display: grid;
        gap: 18px;
    }

    .payment-show-hero,
    .payment-show-card,
    .payment-show-summary,
    .payment-show-timeline {
        border: 1px solid #dbe7f1;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 18px 34px -32px rgba(15, 40, 65, 0.28);
    }

    .payment-show-hero {
        padding: 24px;
        background:
            radial-gradient(circle at top right, rgba(37, 99, 235, 0.10) 0%, rgba(37, 99, 235, 0) 34%),
            linear-gradient(180deg, #f8fbff 0%, #f4f8fc 100%);
    }

    .payment-show-head,
    .payment-show-grid,
    .payment-show-summary-grid {
        display: grid;
        gap: 16px;
    }

    .payment-show-head {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: start;
    }

    .payment-show-eyebrow {
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

    .payment-show-title {
        margin: 14px 0 8px;
        color: #0f172a;
        font-size: clamp(1.9rem, 2.5vw, 2.5rem);
        line-height: 1.08;
        font-weight: 600;
        letter-spacing: -0.04em;
    }

    .payment-show-copy {
        margin: 0;
        color: #64748b;
        font-size: 15px;
        font-weight: 400;
        line-height: 1.65;
        max-width: 72ch;
    }

    .payment-show-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .payment-show-btn {
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
    }

    .payment-show-btn.primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
    }

    .payment-show-btn.secondary {
        background: linear-gradient(180deg, #ffffff 0%, #f5f8fc 100%);
        border-color: #d7e3ef;
        color: #334155;
    }

    .payment-show-summary,
    .payment-show-card,
    .payment-show-timeline {
        padding: 20px;
    }

    .payment-show-summary-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .payment-show-kicker {
        margin: 0 0 8px;
        color: #718096;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .payment-show-amount {
        margin: 0;
        font-size: clamp(2rem, 3vw, 2.8rem);
        line-height: 1.02;
        font-weight: 700;
        letter-spacing: -0.05em;
        color: #0f172a;
    }

    .payment-show-amount.income { color: #047857; }
    .payment-show-amount.expense { color: #b45309; }

    .payment-show-note {
        margin-top: 8px;
        color: #64748b;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
    }

    .payment-show-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .payment-show-card h2,
    .payment-show-timeline h2 {
        margin: 0 0 14px;
        color: #0f172a;
        font-size: 18px;
        line-height: 1.2;
        font-weight: 600;
    }

    .payment-show-list {
        display: grid;
        gap: 12px;
    }

    .payment-show-row {
        display: grid;
        grid-template-columns: minmax(120px, 160px) minmax(0, 1fr);
        gap: 12px;
        align-items: start;
        padding-bottom: 12px;
        border-bottom: 1px solid #ecf2f7;
    }

    .payment-show-row:last-child { border-bottom: 0; padding-bottom: 0; }

    .payment-show-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.45;
    }

    .payment-show-value {
        color: #0f172a;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.55;
    }

    .payment-show-sub {
        color: #64748b;
        font-size: 13px;
        font-weight: 400;
        line-height: 1.45;
        margin-top: 4px;
    }

    .payment-show-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .payment-show-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .payment-show-badge::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
        opacity: .75;
    }

    .payment-show-badge.mode-cash,
    .payment-show-badge.mode-especes { color: #b45309; background: #fff7ed; border-color: #fed7aa; }
    .payment-show-badge.mode-card,
    .payment-show-badge.mode-carte,
    .payment-show-badge.mode-carte-bancaire { color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe; }
    .payment-show-badge.mode-virement,
    .payment-show-badge.mode-transfer { color: #0f766e; background: #f0fdfa; border-color: #99f6e4; }
    .payment-show-badge.mode-cheque { color: #7c3aed; background: #f5f3ff; border-color: #ddd6fe; }
    .payment-show-badge.mode-non-defini,
    .payment-show-badge.mode-regle,
    .payment-show-badge.mode-a-definir { color: #475569; background: #f8fafc; border-color: #cbd5e1; }

    .payment-show-badge.status-payee,
    .payment-show-badge.status-reglee,
    .payment-show-badge.status-paye { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
    .payment-show-badge.status-impayee,
    .payment-show-badge.status-en-retard { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }
    .payment-show-badge.status-en-attente,
    .payment-show-badge.status-a-faire,
    .payment-show-badge.status-enregistre { color: #b45309; background: #fff7ed; border-color: #fed7aa; }

    @media (max-width: 992px) {
        .payment-show-head,
        .payment-show-summary-grid,
        .payment-show-grid {
            grid-template-columns: 1fr;
        }

        .payment-show-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767px) {
        .payment-show-page {
            padding-left: 0;
            padding-right: 0;
        }

        .payment-show-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .payment-show-btn {
            width: 100%;
        }

        .payment-show-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }
    }
</style>
@endpush

@section('content')
@php($modeKey = \Illuminate\Support\Str::of($entry['mode_paiement'])->lower()->ascii()->replace([' ', '/', '_'], '-'))
@php($statusKey = \Illuminate\Support\Str::of($entry['statut'])->lower()->ascii()->replace([' ', '/', '_'], '-'))
<div class="payment-show-page">
    <div class="payment-show-shell">
        <section class="payment-show-hero">
            <div class="payment-show-head">
                <div>
                    <span class="payment-show-eyebrow">Paiement {{ ucfirst($entry['source_label']) }}</span>
                    <h1 class="payment-show-title">{{ $entry['reference'] }}</h1>
                    <p class="payment-show-copy">Détail consolidé de l’opération financière avec source, tiers, mode de règlement, statut et accès au module d’origine.</p>
                    <div class="payment-show-badges">
                        <span class="payment-show-badge mode-{{ $modeKey }}">{{ $entry['mode_paiement'] }}</span>
                        <span class="payment-show-badge status-{{ $statusKey }}">{{ ucfirst(str_replace('_', ' ', $entry['statut'])) }}</span>
                    </div>
                </div>
                <div class="payment-show-actions">
                    <a href="{{ route('paiements.index') }}" class="payment-show-btn secondary"><i class="fas fa-arrow-left"></i>Retour à la liste</a>
                    <a href="{{ $entry['source_detail_url'] }}" class="payment-show-btn primary"><i class="fas fa-up-right-from-square"></i>Ouvrir la source</a>
                </div>
            </div>
        </section>

        <section class="payment-show-summary">
            <div class="payment-show-summary-grid">
                <article>
                    <p class="payment-show-kicker">Montant</p>
                    <p class="payment-show-amount {{ $entry['montant'] < 0 ? 'expense' : 'income' }}">{{ number_format((float) $entry['montant'], 2, ',', ' ') }} DH</p>
                    <p class="payment-show-note">{{ $entry['montant'] < 0 ? 'Sortie de trésorerie' : 'Encaissement confirmé' }}</p>
                </article>
                <article>
                    <p class="payment-show-kicker">Source</p>
                    <p class="payment-show-amount" style="font-size: 1.7rem; letter-spacing: -0.03em;">{{ $entry['source_label'] }}</p>
                    <p class="payment-show-note">Enregistrement rattaché au module d’origine.</p>
                </article>
                <article>
                    <p class="payment-show-kicker">Date opération</p>
                    <p class="payment-show-amount" style="font-size: 1.7rem; letter-spacing: -0.03em;">{{ optional($entry['date_operation'])->format('d/m/Y') ?: '-' }}</p>
                    <p class="payment-show-note">{{ optional($entry['date_operation'])->format('H:i') ?: 'Heure non disponible' }}</p>
                </article>
            </div>
        </section>

        <div class="payment-show-grid">
            <section class="payment-show-card">
                <h2>Références métier</h2>
                <div class="payment-show-list">
                    <div class="payment-show-row">
                        <div class="payment-show-label">Référence</div>
                        <div class="payment-show-value">{{ $entry['reference'] }}</div>
                    </div>
                    <div class="payment-show-row">
                        <div class="payment-show-label">Source</div>
                        <div class="payment-show-value">{{ $entry['source_label'] }}</div>
                    </div>
                    <div class="payment-show-row">
                        <div class="payment-show-label">Statut</div>
                        <div class="payment-show-value">{{ ucfirst(str_replace('_', ' ', $entry['statut'])) }}</div>
                    </div>
                    <div class="payment-show-row">
                        <div class="payment-show-label">Mode de paiement</div>
                        <div class="payment-show-value">{{ $entry['mode_paiement'] }}</div>
                    </div>
                </div>
            </section>

            <section class="payment-show-card">
                <h2>Acteurs concernés</h2>
                <div class="payment-show-list">
                    <div class="payment-show-row">
                        <div class="payment-show-label">Tiers</div>
                        <div class="payment-show-value">{{ $entry['tiers'] ?: '-' }}</div>
                    </div>
                    <div class="payment-show-row">
                        <div class="payment-show-label">Patient</div>
                        <div class="payment-show-value">{{ $entry['patient'] ?: '-' }}</div>
                    </div>
                    <div class="payment-show-row">
                        <div class="payment-show-label">Médecin</div>
                        <div class="payment-show-value">{{ $entry['medecin'] ?: '-' }}</div>
                    </div>
                </div>
            </section>
        </div>

        <section class="payment-show-timeline">
            <h2>Résumé d’exploitation</h2>
            <div class="payment-show-list">
                <div class="payment-show-row">
                    <div class="payment-show-label">Lecture</div>
                    <div class="payment-show-value">
                        {{ $entry['montant'] < 0 ? 'Cette opération correspond à un décaissement.' : 'Cette opération correspond à un encaissement.' }}
                        <div class="payment-show-sub">Le registre conserve une lecture uniforme quel que soit le module source.</div>
                    </div>
                </div>
                <div class="payment-show-row">
                    <div class="payment-show-label">Navigation</div>
                    <div class="payment-show-value">
                        Le détail consolidé reste lisible ici.
                        <div class="payment-show-sub">Le bouton d’ouverture renvoie vers la facture, la dépense ou l’examen d’origine.</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
