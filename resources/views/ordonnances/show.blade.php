@extends('layouts.app')

@section('title', 'Ordonnance')

@section('content')
@php
    $rows = collect(is_array($ordonnance->medicaments) ? $ordonnance->medicaments : []);
    $patient = optional($ordonnance->patient);
    $medecin = $displayMedecin ?? optional($ordonnance->consultation)->medecin;
    $datePrescription = optional($ordonnance->date_prescription)->format('d/m/Y');
    $medicamentCount = $rows->count();
@endphp

<div class="ordonnance-show-page-modern">
    <div class="rx-shell">
        <div class="rx-toolbar no-print">
            <a href="{{ route('ordonnances.index') }}" class="rx-btn rx-btn-light">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                <span>Retour</span>
            </a>
            <button type="button" class="rx-btn rx-btn-primary" onclick="window.print()">
                <i class="fas fa-print" aria-hidden="true"></i>
                <span>Imprimer</span>
            </button>
            <a href="{{ route('ordonnances.pdf', $ordonnance) }}" class="rx-btn rx-btn-light">
                <i class="fas fa-file-pdf" aria-hidden="true"></i>
                <span>PDF</span>
            </a>
        </div>

        <section class="rx-hero rx-anim">
            <div class="rx-chip">Ordonnance medicale</div>
            <h1 class="rx-title">{{ $ordonnance->numero_ordonnance }}</h1>
            <p class="rx-subtitle">Edition du {{ $datePrescription ?: '-' }} | Consultation #{{ $ordonnance->consultation_id ?? '-' }}</p>

            <div class="rx-hero-grid">
                <div class="rx-kpi">
                    <span class="rx-kpi-label">Patient</span>
                    <span class="rx-kpi-value">{{ trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? '')) ?: '-' }}</span>
                </div>
                <div class="rx-kpi">
                    <span class="rx-kpi-label">{{ __('messages.prescriptions.doctor') }}</span>
                    <span class="rx-kpi-value">
                        @if($medecin)
                            Dr. {{ trim(($medecin->prenom ?? '') . ' ' . ($medecin->nom ?? '')) }}
                        @else
                            {{ __('messages.common.not_provided') }}
                        @endif
                    </span>
                </div>
                <div class="rx-kpi">
                    <span class="rx-kpi-label">{{ __('messages.prescriptions.medications') }}</span>
                    <span class="rx-kpi-value">{{ $medicamentCount }}</span>
                </div>
                <div class="rx-kpi">
                    <span class="rx-kpi-label">Date</span>
                    <span class="rx-kpi-value">{{ $datePrescription ?: '-' }}</span>
                </div>
            </div>
        </section>

        <section class="rx-grid">
            <article class="rx-card rx-anim delay-1">
                <h2 class="rx-card-title"><i class="fas fa-user-injured"></i> {{ __('messages.prescriptions.patient_identity') }}</h2>
                <div class="rx-kv">
                    <span>Nom complet</span>
                    <strong>{{ trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? '')) ?: '-' }}</strong>
                </div>
                <div class="rx-kv">
                    <span>ID patient</span>
                    <strong>{{ $patient->id ?? '-' }}</strong>
                </div>
                <div class="rx-kv">
                    <span>Consultation</span>
                    <strong>#{{ $ordonnance->consultation_id ?? '-' }}</strong>
                </div>
            </article>

            <article class="rx-card rx-anim delay-2">
                <h2 class="rx-card-title"><i class="fas fa-user-md"></i> Praticien</h2>
                <div class="rx-kv">
                    <span>{{ __('messages.prescriptions.doctor') }}</span>
                    <strong>
                        @if($medecin)
                            Dr. {{ trim(($medecin->prenom ?? '') . ' ' . ($medecin->nom ?? '')) }}
                        @else
                            {{ __('messages.common.not_provided') }}
                        @endif
                    </strong>
                </div>
                <div class="rx-kv">
                    <span>{{ __('messages.prescriptions.specialty') }}</span>
                    <strong>{{ $medecin->specialite ?? '-' }}</strong>
                </div>
                <div class="rx-kv">
                    <span>{{ __('messages.prescriptions.prescription_date') }}</span>
                    <strong>{{ $datePrescription ?: '-' }}</strong>
                </div>
            </article>
        </section>

        <section class="rx-grid">
            <article class="rx-card rx-anim delay-2">
                <h2 class="rx-card-title"><i class="fas fa-stethoscope"></i> Diagnostic</h2>
                <p class="rx-text-block">{{ $ordonnance->diagnostic ?: __('messages.prescriptions.no_diagnosis') }}</p>
            </article>

            <article class="rx-card rx-anim delay-3">
                <h2 class="rx-card-title"><i class="fas fa-notes-medical"></i> {{ __('messages.prescriptions.general_instructions') }}</h2>
                <p class="rx-text-block">{{ $ordonnance->instructions ?: __('messages.prescriptions.no_general_instructions') }}</p>
            </article>
        </section>

        <section class="rx-card rx-anim delay-3 rx-table-card">
            <div class="rx-table-head">
                <h2 class="rx-card-title mb-0"><i class="fas fa-capsules"></i> {{ __('messages.prescriptions.therapeutic_plan') }}</h2>
                <span class="rx-count">{{ $medicamentCount }} ligne{{ $medicamentCount > 1 ? 's' : '' }}</span>
            </div>

            <div class="table-responsive rx-table-wrap">
                <table class="rx-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.prescriptions.medication') }}</th>
                            <th>Posologie</th>
                            <th>{{ __('messages.prescriptions.duration') }}</th>
                            <th>{{ __('messages.prescriptions.quantity') }}</th>
                            <th>Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $medicamentNames[$row['medicament_id'] ?? null] ?? ('ID: ' . ($row['medicament_id'] ?? '-')) }}</td>
                                <td>{{ $row['posologie'] ?? '-' }}</td>
                                <td>{{ $row['duree'] ?? '-' }}</td>
                                <td>{{ $row['quantite'] ?? '-' }}</td>
                                <td>{{ $row['instructions'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="rx-empty">{{ __('messages.prescriptions.no_medication_registered') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<style>
    .ordonnance-show-page-modern {
        --rx-bg-start: #f5fbff;
        --rx-bg-end: #eef4ff;
        --rx-surface: #ffffff;
        --rx-border: #dbe6f5;
        --rx-title: #112e52;
        --rx-text: #304a67;
        --rx-muted: #6a819b;
        --rx-primary: #1f6fe5;
        --rx-primary-dark: #0d56bf;
        --rx-shadow: 0 24px 44px -34px rgba(15, 42, 84, 0.35);
        padding: 16px;
        font-family: "Plus Jakarta Sans", "Segoe UI", Roboto, Arial, sans-serif;
    }

    .ordonnance-show-page-modern .rx-shell {
        max-width: 1220px;
        margin: 0 auto;
        display: grid;
        gap: 18px;
    }

    .ordonnance-show-page-modern .rx-toolbar {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .ordonnance-show-page-modern .rx-btn {
        border: 1px solid transparent;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.92rem;
        padding: 10px 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all .2s ease;
    }

    .ordonnance-show-page-modern .rx-btn-light {
        background: #ffffff;
        border-color: #cad7ea;
        color: #294a72;
    }

    .ordonnance-show-page-modern .rx-btn-light:hover {
        background: #f3f8ff;
        border-color: #aec4e3;
        color: #163e6f;
    }

    .ordonnance-show-page-modern .rx-btn-primary {
        background: linear-gradient(135deg, var(--rx-primary), #2785ff);
        color: #fff;
        box-shadow: 0 16px 30px -20px rgba(31, 111, 229, 0.8);
    }

    .ordonnance-show-page-modern .rx-btn-primary:hover {
        background: linear-gradient(135deg, var(--rx-primary-dark), var(--rx-primary));
    }

    .ordonnance-show-page-modern .rx-hero {
        border: 1px solid var(--rx-border);
        border-radius: 18px;
        padding: 24px;
        background: radial-gradient(130% 120% at 0% 0%, var(--rx-bg-start), var(--rx-bg-end));
        box-shadow: var(--rx-shadow);
    }

    .ordonnance-show-page-modern .rx-chip {
        display: inline-flex;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #1b4f87;
        background: #e8f2ff;
        border: 1px solid #c7ddfb;
        border-radius: 999px;
        padding: 5px 10px;
        margin-bottom: 12px;
    }

    .ordonnance-show-page-modern .rx-title {
        font-size: clamp(1.2rem, 2vw, 1.7rem);
        margin: 0;
        color: var(--rx-title);
        font-weight: 800;
    }

    .ordonnance-show-page-modern .rx-subtitle {
        margin: 8px 0 18px;
        color: var(--rx-muted);
        font-weight: 400;
    }

    .ordonnance-show-page-modern .rx-hero-grid,
    .ordonnance-show-page-modern .rx-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .ordonnance-show-page-modern .rx-kpi,
    .ordonnance-show-page-modern .rx-card {
        background: var(--rx-surface);
        border: 1px solid var(--rx-border);
        border-radius: 14px;
        padding: 16px;
    }

    .ordonnance-show-page-modern .rx-kpi-label {
        display: block;
        color: var(--rx-muted);
        font-size: .92rem;
        letter-spacing: 0;
        text-transform: none;
        margin-bottom: 6px;
        font-weight: 500;
        line-height: 1.4;
    }

    .ordonnance-show-page-modern .rx-kpi-value {
        color: var(--rx-title);
        font-size: 1.04rem;
        font-weight: 600;
        word-break: break-word;
        line-height: 1.45;
    }

    .ordonnance-show-page-modern .rx-card {
        box-shadow: 0 18px 34px -32px rgba(21, 54, 95, .45);
    }

    .ordonnance-show-page-modern .rx-card-title {
        margin: 0 0 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--rx-title);
        font-size: 1rem;
        font-weight: 600;
    }

    .ordonnance-show-page-modern .rx-kv {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 16px;
        padding: 9px 0;
        border-bottom: 1px dashed #e2eaf6;
        color: var(--rx-text);
        font-size: .93rem;
    }

    .ordonnance-show-page-modern .rx-kv:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .ordonnance-show-page-modern .rx-kv span {
        color: var(--rx-muted);
        font-weight: 500;
        text-transform: none;
    }

    .ordonnance-show-page-modern .rx-kv strong {
        color: var(--rx-title);
        font-weight: 600;
        text-align: right;
    }

    .ordonnance-show-page-modern .rx-text-block {
        margin: 0;
        white-space: pre-wrap;
        color: var(--rx-text);
        line-height: 1.6;
        font-weight: 400;
    }

    .ordonnance-show-page-modern .rx-table-card {
        padding: 0;
        overflow: hidden;
    }

    .ordonnance-show-page-modern .rx-table-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        border-bottom: 1px solid #e0eaf8;
    }

    .ordonnance-show-page-modern .rx-count {
        font-size: .82rem;
        font-weight: 500;
        color: #1b4f87;
        background: #edf4ff;
        border: 1px solid #d3e1f7;
        border-radius: 999px;
        padding: 4px 10px;
    }

    .ordonnance-show-page-modern .rx-table-wrap {
        padding: 0 16px 16px;
    }

    .ordonnance-show-page-modern .rx-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 740px;
        border: 1px solid #d9e5f5;
        border-radius: 12px;
        overflow: hidden;
    }

    .ordonnance-show-page-modern .rx-table thead th {
        background: #f1f6ff;
        color: #1b4878;
        font-size: .82rem;
        letter-spacing: .02em;
        text-transform: none;
        font-weight: 500;
        padding: 12px;
        border-bottom: 1px solid #d9e5f5;
    }

    .ordonnance-show-page-modern .rx-table tbody td {
        padding: 11px 12px;
        color: #173a60;
        border-bottom: 1px solid #e7eef9;
        font-weight: 400;
    }

    .ordonnance-show-page-modern .rx-table tbody tr:nth-child(even) {
        background: #fbfdff;
    }

    .ordonnance-show-page-modern .rx-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .ordonnance-show-page-modern .rx-empty {
        text-align: center;
        color: var(--rx-muted);
        font-weight: 700;
    }

    .ordonnance-show-page-modern .rx-anim {
        opacity: 0;
        transform: translateY(10px);
        animation: rxFade .35s ease forwards;
    }

    .ordonnance-show-page-modern .delay-1 { animation-delay: .06s; }
    .ordonnance-show-page-modern .delay-2 { animation-delay: .12s; }
    .ordonnance-show-page-modern .delay-3 { animation-delay: .18s; }

    @keyframes rxFade {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    body.theme-dark .ordonnance-show-page-modern,
    body.dark-mode .ordonnance-show-page-modern {
        --rx-bg-start: #1c2836;
        --rx-bg-end: #152033;
        --rx-surface: #1c2736;
        --rx-border: #2d4058;
        --rx-title: #e4ecf8;
        --rx-text: #c4d3e7;
        --rx-muted: #92a7c1;
        --rx-primary: #3b82f6;
        --rx-primary-dark: #2563eb;
        --rx-shadow: 0 20px 40px -34px rgba(0, 0, 0, 0.9);
    }

    body.theme-dark .ordonnance-show-page-modern .rx-btn-light,
    body.dark-mode .ordonnance-show-page-modern .rx-btn-light {
        background: #1d2a3b;
        border-color: #2e425d;
        color: #d6e3f4;
    }

    body.theme-dark .ordonnance-show-page-modern .rx-table thead th,
    body.dark-mode .ordonnance-show-page-modern .rx-table thead th {
        background: #223247;
        color: #cfe0f5;
        border-bottom-color: #314861;
    }

    body.theme-dark .ordonnance-show-page-modern .rx-table tbody td,
    body.dark-mode .ordonnance-show-page-modern .rx-table tbody td {
        color: #d4e2f4;
        border-bottom-color: #2c4159;
    }

    body.theme-dark .ordonnance-show-page-modern .rx-table tbody tr:nth-child(even),
    body.dark-mode .ordonnance-show-page-modern .rx-table tbody tr:nth-child(even) {
        background: #1b2738;
    }

    @media (max-width: 1024px) {
        .ordonnance-show-page-modern .rx-grid,
        .ordonnance-show-page-modern .rx-hero-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .ordonnance-show-page-modern {
            padding: 8px;
        }

        .ordonnance-show-page-modern .rx-toolbar {
            justify-content: stretch;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .ordonnance-show-page-modern .rx-btn {
            justify-content: center;
        }

        .ordonnance-show-page-modern .rx-hero,
        .ordonnance-show-page-modern .rx-card {
            padding: 14px;
            border-radius: 12px;
        }

        .ordonnance-show-page-modern .rx-table-head,
        .ordonnance-show-page-modern .rx-table-wrap {
            padding-left: 10px;
            padding-right: 10px;
        }
    }

    @page {
        size: A4;
        margin: 10mm;
    }

    @media print {
        .no-print,
        nav,
        aside,
        .sidebar,
        .navbar,
        .app-topbar {
            display: none !important;
        }

        body,
        .main-content,
        .content-wrapper {
            background: #fff !important;
        }

        .ordonnance-show-page-modern {
            padding: 0 !important;
        }

        .ordonnance-show-page-modern .rx-shell {
            max-width: 100% !important;
            gap: 10px;
        }

        .ordonnance-show-page-modern .rx-hero,
        .ordonnance-show-page-modern .rx-card,
        .ordonnance-show-page-modern .rx-kpi,
        .ordonnance-show-page-modern .rx-table {
            box-shadow: none !important;
            border-color: #d4dbe6 !important;
            background: #fff !important;
        }

        .ordonnance-show-page-modern .rx-anim {
            opacity: 1 !important;
            transform: none !important;
            animation: none !important;
        }
    }
</style>

@if(request()->boolean('print'))
<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
@endif
@endsection
