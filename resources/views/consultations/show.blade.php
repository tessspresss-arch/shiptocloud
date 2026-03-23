@extends('layouts.app')

@section('title', 'Details Consultation #' . $consultation->id)
@section('topbar_subtitle', 'Vue detaillee de la consultation, constantes, conclusions cliniques et actions rapides.')

@push('styles')
<style>
    :root {
        --cs-bg: #f4f7fc;
        --cs-card: #ffffff;
        --cs-border: #dbe5f2;
        --cs-ink: #102a43;
        --cs-muted: #5f7188;
        --cs-primary: #1d4ed8;
        --cs-primary-soft: #dbeafe;
        --cs-success: #10b981;
        --cs-warning: #f59e0b;
        --cs-danger: #ef4444;
        --cs-info: #0ea5e9;
        --cs-shadow: 0 14px 30px -26px rgba(15, 23, 42, 0.72);
    }

    .consultation-page {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: clamp(0.5rem, 1.1vw, 1rem) clamp(0.45rem, 1.1vw, 1rem) 1rem;
    }

    .cs-shell {
        width: 100%;
        max-width: none;
        display: grid;
        gap: 0.9rem;
    }

    .cs-header {
        background: linear-gradient(130deg, #f7fbff 0%, #eff5ff 54%, #eef8ff 100%);
        border: 1px solid var(--cs-border);
        border-radius: 18px;
        box-shadow: var(--cs-shadow);
        padding: clamp(0.8rem, 1.8vw, 1.25rem);
        display: grid;
        gap: 0.85rem;
    }

    .cs-header-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.9rem;
        flex-wrap: wrap;
    }

    .cs-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(29, 78, 216, 0.14);
        background: rgba(255, 255, 255, 0.78);
        color: #1d4ed8;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        width: fit-content;
    }

    .cs-title-wrap {
        min-width: 0;
        display: grid;
        gap: 0.34rem;
    }

    .cs-title {
        margin: 0;
        color: #123a7d;
        font-size: clamp(1.35rem, 2.4vw, 2rem);
        line-height: 1.12;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 0.58rem;
        flex-wrap: wrap;
    }

    .cs-title i {
        color: #2b6adf;
        font-size: 0.95em;
    }

    .cs-subtitle {
        margin: 0;
        color: var(--cs-muted);
        font-size: 0.94rem;
        font-weight: 600;
    }

    .cs-chip-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .cs-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid #d7e4f4;
        color: #4f6781;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .cs-chip i {
        color: #2b6adf;
    }

    .cs-status {
        border-radius: 999px;
        padding: 0.3rem 0.72rem;
        font-size: 0.8rem;
        font-weight: 800;
        border: 1px solid transparent;
        white-space: nowrap;
        letter-spacing: 0.01em;
    }

    .cs-status.completed {
        color: #166534;
        background: #dcfce7;
        border-color: #86efac;
    }

    .cs-status.scheduled {
        color: #1d4ed8;
        background: #dbeafe;
        border-color: #93c5fd;
    }

    .cs-status.pending {
        color: #92400e;
        background: #fef3c7;
        border-color: #fcd34d;
    }

    .cs-status.in-progress {
        color: #0c4a6e;
        background: #e0f2fe;
        border-color: #7dd3fc;
    }

    .cs-header-meta {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.62rem;
    }

    .cs-meta-chip {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid #d7e4f4;
        border-radius: 12px;
        padding: 0.56rem 0.66rem;
        min-height: 66px;
    }

    .cs-meta-label {
        margin: 0;
        color: #58708e;
        font-size: 0.73rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .cs-meta-value {
        margin: 0.2rem 0 0;
        color: #122b4f;
        font-size: 0.96rem;
        font-weight: 700;
        line-height: 1.26;
        word-break: break-word;
    }

    .cs-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.54rem;
        flex-wrap: wrap;
    }

    .cs-btn {
        min-height: 41px;
        border-radius: 11px;
        padding: 0.56rem 0.92rem;
        border: 1px solid transparent;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.46rem;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
        cursor: pointer;
    }

    .cs-btn:hover {
        text-decoration: none;
        transform: translateY(-1px);
    }

    .cs-btn-soft {
        background: #eef2f7;
        border-color: #d9e2ee;
        color: #334e68;
    }

    .cs-btn-soft:hover {
        background: #e2eaf3;
        color: #243b53;
    }

    .cs-btn-primary {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        border-color: #1d4ed8;
        color: #fff;
        box-shadow: 0 10px 20px -16px rgba(37, 99, 235, 0.8);
    }

    .cs-btn-primary:hover {
        color: #fff;
    }

    .cs-btn-success {
        background: linear-gradient(135deg, #0ea271, #10b981);
        border-color: #0ea271;
        color: #fff;
        box-shadow: 0 10px 20px -16px rgba(16, 185, 129, 0.84);
    }

    .cs-btn-success:hover {
        color: #fff;
    }

    .cs-btn-danger {
        background: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }

    .cs-btn-danger:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }

    .cs-btn-print {
        background: #fff;
        color: #1e40af;
        border-color: #bfd3f8;
    }

    .cs-btn-print:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .cs-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr);
        gap: 0.9rem;
        align-items: start;
    }

    .cs-stack {
        display: grid;
        gap: 0.9rem;
    }

    .cs-card {
        background: var(--cs-card);
        border: 1px solid var(--cs-border);
        border-radius: 16px;
        box-shadow: var(--cs-shadow);
        overflow: hidden;
    }

    .cs-card-head {
        padding: 0.84rem 0.95rem;
        border-bottom: 1px solid #e5edf7;
        background: linear-gradient(180deg, #f8fbff 0%, #f3f8ff 100%);
    }

    .cs-card-title {
        margin: 0;
        color: #123a7d;
        font-weight: 800;
        font-size: 1.06rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cs-card-body {
        padding: 0.95rem;
    }

    .cs-identity-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .cs-mini-card {
        border: 1px solid #e7eef8;
        border-radius: 13px;
        padding: 0.72rem;
        background: #fbfdff;
        display: grid;
        gap: 0.45rem;
    }

    .cs-mini-head {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: #315a88;
        font-size: 0.84rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .cs-mini-main {
        color: #102a43;
        font-weight: 800;
        font-size: 1.03rem;
        line-height: 1.24;
    }

    .cs-mini-sub {
        margin: 0;
        color: #53657f;
        font-size: 0.89rem;
        line-height: 1.35;
        word-break: break-word;
    }

    .cs-kv-list {
        display: grid;
        gap: 0.45rem;
    }

    .cs-kv {
        display: grid;
        grid-template-columns: 124px minmax(0, 1fr);
        gap: 0.48rem;
        border: 1px solid #e9f0f8;
        background: #fcfdff;
        border-radius: 10px;
        padding: 0.48rem 0.58rem;
    }

    .cs-kv-label {
        color: #4f657f;
        font-size: 0.83rem;
        font-weight: 700;
    }

    .cs-kv-value {
        color: #102a43;
        font-size: 0.89rem;
        font-weight: 700;
        word-break: break-word;
    }

    .cs-empty {
        color: #8ca0b8;
        font-style: italic;
    }

    .cs-vitals-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0.62rem;
    }

    .cs-vital {
        border: 1px solid #e8eff8;
        border-radius: 12px;
        padding: 0.74rem 0.58rem;
        background: #fbfdff;
        text-align: center;
    }

    .cs-vital-icon {
        color: #2b6adf;
        font-size: 1.08rem;
        margin-bottom: 0.28rem;
    }

    .cs-vital-value {
        margin: 0;
        color: #0f172a;
        font-size: clamp(1.05rem, 1.75vw, 1.34rem);
        font-weight: 800;
        line-height: 1.1;
    }

    .cs-vital-label {
        margin: 0.24rem 0 0;
        color: #53657f;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .cs-text-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.7rem;
    }

    .cs-text-card {
        border: 1px solid #e6eef8;
        border-radius: 12px;
        background: #fbfdff;
        padding: 0.68rem 0.72rem;
        min-height: 128px;
    }

    .cs-text-head {
        margin: 0 0 0.42rem;
        color: #214a81;
        font-size: 0.9rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .cs-text-body {
        margin: 0;
        color: #1f334d;
        font-size: 0.9rem;
        line-height: 1.5;
        white-space: pre-line;
        word-break: break-word;
    }

    .cs-prescriptions {
        display: grid;
        gap: 0.56rem;
    }

    .cs-prescription-item {
        border: 1px solid #e6eef8;
        border-radius: 12px;
        padding: 0.62rem 0.7rem;
        background: #fbfdff;
        display: grid;
        gap: 0.45rem;
    }

    .cs-prescription-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .cs-prescription-title {
        margin: 0;
        color: #103564;
        font-size: 0.95rem;
        font-weight: 800;
    }

    .cs-prescription-meta {
        margin: 0;
        color: #567192;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .cs-pill {
        border-radius: 999px;
        padding: 0.2rem 0.6rem;
        font-size: 0.72rem;
        font-weight: 800;
        border: 1px solid #c7d7ef;
        color: #254b7f;
        background: #eef4ff;
        white-space: nowrap;
    }

    .cs-pill.success {
        color: #166534;
        border-color: #86efac;
        background: #dcfce7;
    }

    .cs-pill.warning {
        color: #92400e;
        border-color: #fcd34d;
        background: #fef3c7;
    }

    .cs-side-grid {
        display: grid;
        gap: 0.9rem;
        position: sticky;
        top: 0.8rem;
    }

    .cs-print-only {
        display: none;
    }

    body.dark-mode .cs-header,
    body.dark-mode .cs-card,
    body.dark-mode .cs-mini-card,
    body.dark-mode .cs-vital,
    body.dark-mode .cs-text-card,
    body.dark-mode .cs-prescription-item,
    body.dark-mode .cs-kv {
        background: #0f1d31;
        border-color: #2f4865;
        box-shadow: 0 14px 28px -24px rgba(0, 0, 0, 0.72);
    }

    body.dark-mode .cs-card-head {
        background: linear-gradient(180deg, #13243b 0%, #102035 100%);
        border-color: #2f4865;
    }

    body.dark-mode .cs-title,
    body.dark-mode .cs-card-title,
    body.dark-mode .cs-mini-main,
    body.dark-mode .cs-vital-value,
    body.dark-mode .cs-text-body,
    body.dark-mode .cs-kv-value,
    body.dark-mode .cs-prescription-title {
        color: #e2ebf7;
    }

    body.dark-mode .cs-subtitle,
    body.dark-mode .cs-mini-sub,
    body.dark-mode .cs-meta-label,
    body.dark-mode .cs-vital-label,
    body.dark-mode .cs-kv-label,
    body.dark-mode .cs-prescription-meta,
    body.dark-mode .cs-text-head,
    body.dark-mode .cs-meta-value {
        color: #abc0da;
    }

    body.dark-mode .cs-meta-chip {
        background: #132742;
        border-color: #355578;
    }

    body.dark-mode .cs-eyebrow,
    body.dark-mode .cs-chip {
        background: #132742;
        border-color: #355578;
        color: #cfe1f6;
    }

    body.dark-mode .cs-chip i {
        color: #8ec5ff;
    }

    body.dark-mode .cs-btn-soft {
        background: #1b314f;
        border-color: #335376;
        color: #d2e4f8;
    }

    body.dark-mode .cs-btn-soft:hover {
        background: #244266;
        color: #eaf4ff;
    }

    body.dark-mode .cs-btn-print {
        background: #132742;
        border-color: #3c6291;
        color: #cde1ff;
    }

    body.dark-mode .cs-btn-print:hover {
        background: #1b3456;
        color: #e2eeff;
    }

    body.dark-mode .cs-pill {
        border-color: #3a5d84;
        background: #173456;
        color: #cfe4ff;
    }

    body.dark-mode .cs-pill.success {
        color: #bef3cf;
        border-color: #2f7f5a;
        background: #133525;
    }

    body.dark-mode .cs-pill.warning {
        color: #fed7aa;
        border-color: #8f5f2b;
        background: #3d2a12;
    }

    @media (max-width: 1400px) {
        .cs-header-meta {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 1200px) {
        .cs-main-grid {
            grid-template-columns: 1fr;
        }

        .cs-side-grid {
            position: static;
        }

        .cs-vitals-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .cs-identity-grid {
            grid-template-columns: 1fr;
        }

        .cs-text-grid {
            grid-template-columns: 1fr;
        }

        .cs-vitals-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .consultation-page {
            padding: 0.4rem 0.3rem 0.8rem;
        }

        .cs-header {
            border-radius: 14px;
        }

        .cs-header-top {
            align-items: flex-start;
        }

        .cs-header-meta {
            grid-template-columns: 1fr;
        }

        .cs-card,
        .cs-mini-card,
        .cs-vital,
        .cs-text-card {
            border-radius: 12px;
        }

        .cs-kv {
            grid-template-columns: 1fr;
            gap: 0.22rem;
        }
    }

    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }

        html,
        body {
            background: #fff !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        #mobileMenuBtn,
        .sidebar,
        .sidebar-overlay,
        .app-topbar,
        .no-print {
            display: none !important;
            visibility: hidden !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .consultation-page {
            padding: 0 !important;
        }

        .cs-header,
        .cs-card,
        .cs-mini-card,
        .cs-vital,
        .cs-text-card,
        .cs-prescription-item,
        .cs-kv {
            background: #fff !important;
            border: 1px solid #d0d7e2 !important;
            box-shadow: none !important;
        }

        .cs-card-head {
            background: #f4f6fa !important;
            border-bottom: 1px solid #d0d7e2 !important;
        }

        .cs-main-grid {
            grid-template-columns: 1fr !important;
        }

        .cs-side-grid {
            position: static !important;
        }

        .cs-card,
        .cs-mini-card,
        .cs-vital,
        .cs-text-card,
        .cs-prescription-item {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .cs-print-only {
            display: block !important;
            margin-bottom: 8mm;
            border: 1px solid #d0d7e2;
            border-radius: 8px;
            padding: 7mm;
        }

        .cs-print-head-title {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }

        .cs-print-head-sub {
            margin: 4px 0 0;
            color: #334155;
            font-size: 12px;
            line-height: 1.4;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid consultation-page">
    <div class="cs-shell">
        <div class="cs-print-only">
            <p class="cs-print-head-title">{{ $cabinetName }}</p>
            <p class="cs-print-head-sub">
                Dossier de consultation #{{ $consultation->id }}<br>
                Date impression: <span data-print-datetime></span>
            </p>
        </div>

        <header class="cs-header">
            <div class="cs-header-top">
                <div class="cs-title-wrap">
                    <span class="cs-eyebrow">Dossier de consultation</span>
                    <h1 class="cs-title">
                        <i class="fas fa-stethoscope"></i>
                        Consultation #{{ $consultation->id }}
                        <span class="cs-status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </h1>
                    <p class="cs-subtitle">Fiche complete de suivi medical, signes vitaux et conclusions de la consultation.</p>
                    <div class="cs-chip-row">
                        <span class="cs-chip"><i class="fas fa-user"></i>{{ $patientName }}</span>
                        <span class="cs-chip"><i class="fas fa-user-md"></i>{{ $medecinName }}</span>
                        @if($consultation->rendezvous)
                            <span class="cs-chip"><i class="fas fa-calendar-check"></i>RDV #{{ $consultation->rendezvous->id }}</span>
                        @endif
                    </div>
                </div>

                <div class="cs-actions no-print d-none d-md-flex">
                    <a href="{{ route('consultations.index') }}" class="cs-btn cs-btn-soft">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button type="button" class="cs-btn cs-btn-print" data-action="print">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                    <a href="{{ route('consultations.edit', $consultation->id) }}" class="cs-btn cs-btn-primary">
                        <i class="fas fa-pen-to-square"></i> Modifier
                    </a>
                    <a href="{{ route('factures.create', ['consultation_id' => $consultation->id]) }}" class="cs-btn cs-btn-soft">
                        <i class="fas fa-file-invoice-dollar"></i> Facturer
                    </a>
                    <a href="{{ route('ordonnances.create', ['consultation_id' => $consultation->id]) }}" class="cs-btn cs-btn-success">
                        <i class="fas fa-prescription"></i> Ordonnance
                    </a>
                    <form action="{{ route('consultations.destroy', $consultation->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette consultation ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="cs-btn cs-btn-danger">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                </div>

                <div class="dropdown d-md-none no-print">
                    <button class="cs-btn cs-btn-soft dropdown-toggle" type="button" id="consultationActionsMobile" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bars"></i> Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="consultationActionsMobile">
                        <li>
                            <a class="dropdown-item" href="{{ route('consultations.index') }}">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('consultations.edit', $consultation->id) }}">
                                <i class="fas fa-pen-to-square me-2"></i>Modifier
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('factures.create', ['consultation_id' => $consultation->id]) }}">
                                <i class="fas fa-file-invoice-dollar me-2"></i>Facturer
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('ordonnances.create', ['consultation_id' => $consultation->id]) }}">
                                <i class="fas fa-prescription me-2"></i>Ordonnance
                            </a>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item" data-action="print">
                                <i class="fas fa-print me-2"></i>Imprimer
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('consultations.destroy', $consultation->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette consultation ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="cs-header-meta">
                <div class="cs-meta-chip">
                    <p class="cs-meta-label">Date consultation</p>
                    <p class="cs-meta-value">{{ $consultationDate ? $consultationDate->format('d/m/Y') : __('messages.common.not_scheduled_feminine') }}</p>
                </div>
                <div class="cs-meta-chip">
                    <p class="cs-meta-label">Prochaine visite</p>
                    <p class="cs-meta-value">{{ $nextVisitDate ? $nextVisitDate->format('d/m/Y') : __('messages.common.not_scheduled_feminine') }}</p>
                </div>
                <div class="cs-meta-chip">
                    <p class="cs-meta-label">Patient</p>
                    <p class="cs-meta-value">{{ $patientName }}</p>
                </div>
                <div class="cs-meta-chip">
                    <p class="cs-meta-label">Medecin</p>
                    <p class="cs-meta-value">{{ $medecinName }}</p>
                </div>
            </div>
        </header>

        <div class="cs-main-grid">
            <div class="cs-stack">
                <section class="cs-card">
                    <div class="cs-card-head">
                        <h2 class="cs-card-title"><i class="fas fa-address-card"></i> Informations de la consultation</h2>
                    </div>
                    <div class="cs-card-body">
                        <div class="cs-identity-grid">
                            <article class="cs-mini-card">
                                <div class="cs-mini-head"><i class="fas fa-user"></i> Patient</div>
                                <div class="cs-mini-main">{{ $patientName }}</div>
                                <p class="cs-mini-sub">
                                    @if($patientAge !== null){{ $patientAge }} ans @endif
                                    @if($patient && $patient->genre) | {{ $patient->genre === 'M' ? 'Masculin' : 'Feminin' }} @endif
                                </p>
                                <div class="cs-kv-list">
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">ID</span>
                                        <span class="cs-kv-value">{{ $patient ? $patient->id : '-' }}</span>
                                    </div>
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">Telephone</span>
                                        <span class="cs-kv-value">{{ $patient && $patient->telephone ? $patient->telephone : '-' }}</span>
                                    </div>
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">Email</span>
                                        <span class="cs-kv-value">{{ $patient && $patient->email ? $patient->email : '-' }}</span>
                                    </div>
                                </div>
                            </article>

                            <article class="cs-mini-card">
                                <div class="cs-mini-head"><i class="fas fa-user-doctor"></i> {{ __('messages.consultations.doctor') }}</div>
                                <div class="cs-mini-main">{{ $medecinName }}</div>
                                <p class="cs-mini-sub">{{ $medecin && $medecin->specialite ? $medecin->specialite : __('messages.consultations.specialty_not_provided') }}</p>
                                <div class="cs-kv-list">
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">Telephone</span>
                                        <span class="cs-kv-value">{{ $medecin && $medecin->telephone ? $medecin->telephone : '-' }}</span>
                                    </div>
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">Email</span>
                                        <span class="cs-kv-value">{{ $medecin && $medecin->email ? $medecin->email : '-' }}</span>
                                    </div>
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">Ville</span>
                                        <span class="cs-kv-value">{{ $medecin && $medecin->ville ? $medecin->ville : '-' }}</span>
                                    </div>
                                </div>
                            </article>

                            <article class="cs-mini-card">
                                <div class="cs-mini-head"><i class="fas fa-circle-info"></i> {{ __('messages.consultations.general_information') }}</div>
                                <div class="cs-kv-list">
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">{{ __('messages.consultations.consultation_date') }}</span>
                                        <span class="cs-kv-value">{{ $consultationDate ? $consultationDate->format('d/m/Y') : '-' }}</span>
                                    </div>
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">Prochaine visite</span>
                                        <span class="cs-kv-value">{{ $nextVisitDate ? $nextVisitDate->format('d/m/Y') : '-' }}</span>
                                    </div>
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">{{ __('messages.consultations.linked_appointment') }}</span>
                                        <span class="cs-kv-value">
                                            @if($consultation->rendezvous)
                                                #{{ $consultation->rendezvous->id }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div class="cs-kv">
                                        <span class="cs-kv-label">Consultation ID</span>
                                        <span class="cs-kv-value">#{{ $consultation->id }}</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="cs-card">
                    <div class="cs-card-head">
                        <h2 class="cs-card-title"><i class="fas fa-heart-pulse"></i> Signes vitaux</h2>
                    </div>
                    <div class="cs-card-body">
                        <div class="cs-vitals-grid">
                            <article class="cs-vital">
                                <div class="cs-vital-icon"><i class="fas fa-temperature-half"></i></div>
                                <p class="cs-vital-value">{{ $temperature ?? '--' }}</p>
                                <p class="cs-vital-label">{{ __('messages.consultations.temperature_c') }}</p>
                            </article>
                            <article class="cs-vital">
                                <div class="cs-vital-icon"><i class="fas fa-stethoscope"></i></div>
                                <p class="cs-vital-value">{{ $tension ?? '--' }}</p>
                                <p class="cs-vital-label">Tension (mmHg)</p>
                            </article>
                            <article class="cs-vital">
                                <div class="cs-vital-icon"><i class="fas fa-weight-scale"></i></div>
                                <p class="cs-vital-value">{{ $poids ?? '--' }}</p>
                                <p class="cs-vital-label">Poids (kg)</p>
                            </article>
                            <article class="cs-vital">
                                <div class="cs-vital-icon"><i class="fas fa-ruler-vertical"></i></div>
                                <p class="cs-vital-value">{{ $taille ?? '--' }}</p>
                                <p class="cs-vital-label">Taille (cm)</p>
                            </article>
                            <article class="cs-vital">
                                <div class="cs-vital-icon"><i class="fas fa-calculator"></i></div>
                                <p class="cs-vital-value">{{ $imc ?? '--' }}</p>
                                <p class="cs-vital-label">IMC</p>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="cs-card">
                    <div class="cs-card-head">
                        <h2 class="cs-card-title"><i class="fas fa-notes-medical"></i> Observation, diagnostic et traitement</h2>
                    </div>
                    <div class="cs-card-body">
                        <div class="cs-text-grid">
                            <article class="cs-text-card">
                                <h3 class="cs-text-head"><i class="fas fa-clipboard"></i> {{ __('messages.consultations.observations_symptoms') }}</h3>
                                <p class="cs-text-body {{ filled($consultation->symptomes) ? '' : 'cs-empty' }}">
                                    {!! filled($consultation->symptomes) ? nl2br(e($consultation->symptomes)) : __('messages.consultations.no_observation') !!}
                                </p>
                            </article>
                            <article class="cs-text-card">
                                <h3 class="cs-text-head"><i class="fas fa-magnifying-glass"></i> Examen clinique</h3>
                                <p class="cs-text-body {{ filled($consultation->examen_clinique) ? '' : 'cs-empty' }}">
                                    {!! filled($consultation->examen_clinique) ? nl2br(e($consultation->examen_clinique)) : __('messages.consultations.no_exam') !!}
                                </p>
                            </article>
                            <article class="cs-text-card">
                                <h3 class="cs-text-head"><i class="fas fa-stethoscope"></i> Diagnostic</h3>
                                <p class="cs-text-body {{ filled($consultation->diagnostic) ? '' : 'cs-empty' }}">
                                    {!! filled($consultation->diagnostic) ? nl2br(e($consultation->diagnostic)) : __('messages.consultations.no_diagnosis') !!}
                                </p>
                            </article>
                            <article class="cs-text-card">
                                <h3 class="cs-text-head"><i class="fas fa-pills"></i> Traitement</h3>
                                <p class="cs-text-body {{ filled($consultation->traitement_prescrit) ? '' : 'cs-empty' }}">
                                    {!! filled($consultation->traitement_prescrit) ? nl2br(e($consultation->traitement_prescrit)) : 'Aucun traitement prescrit.' !!}
                                </p>
                            </article>
                            <article class="cs-text-card">
                                <h3 class="cs-text-head"><i class="fas fa-list-check"></i> Recommandations</h3>
                                <p class="cs-text-body {{ filled($consultation->recommandations) ? '' : 'cs-empty' }}">
                                    {!! filled($consultation->recommandations) ? nl2br(e($consultation->recommandations)) : __('messages.consultations.no_recommendation') !!}
                                </p>
                            </article>
                            <article class="cs-text-card">
                                <h3 class="cs-text-head"><i class="fas fa-calendar-check"></i> Prochaine visite</h3>
                                <p class="cs-text-body {{ $nextVisitDate ? '' : 'cs-empty' }}">
                                    @if($nextVisitDate)
                                        {{ __('messages.consultations.followup_scheduled') }} {{ $nextVisitDate->format('d/m/Y') }}.
                                    @else
                                        {{ __('messages.consultations.no_followup') }}
                                    @endif
                                </p>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="cs-card">
                    <div class="cs-card-head">
                        <h2 class="cs-card-title"><i class="fas fa-prescription"></i> {{ __('messages.consultations.associated_prescriptions') }}</h2>
                    </div>
                    <div class="cs-card-body">
                        <div class="cs-prescriptions">
                            @forelse($consultation->prescriptions as $prescription)
                                <article class="cs-prescription-item">
                                    <div class="cs-prescription-head">
                                        <p class="cs-prescription-title">
                                            {{ $prescription->numero_prescription ? $prescription->numero_prescription : ('Prescription #' . $prescription->id) }}
                                        </p>
                                        <span class="cs-pill {{ $prescription->status_pill_class }}">{{ $prescription->display_status }}</span>
                                    </div>
                                    <p class="cs-prescription-meta">
                                        @if($prescription->display_date)
                                            Date: {{ $prescription->display_date->format('d/m/Y H:i') }}
                                        @else
                                            {{ __('messages.consultations.date_not_provided') }}
                                        @endif
                                        | Type: {{ $prescription->type_prescription ?: 'Standard' }}
                                        | {{ __('messages.consultations.medications') }}: {{ $prescription->med_count }}
                                    </p>
                                    <p class="cs-text-body {{ filled($prescription->recommandations) ? '' : 'cs-empty' }}">
                                        {!! filled($prescription->recommandations) ? nl2br(e($prescription->recommandations)) : __('messages.consultations.no_extra_recommendation') !!}
                                    </p>
                                </article>
                            @empty
                                <article class="cs-prescription-item">
                                    <p class="cs-text-body cs-empty">{{ __('messages.consultations.no_associated_prescription') }}</p>
                                </article>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>

            <aside class="cs-side-grid">
                <section class="cs-card">
                    <div class="cs-card-head">
                        <h2 class="cs-card-title"><i class="fas fa-file-medical"></i> {{ __('messages.consultations.medical_summary') }}</h2>
                    </div>
                    <div class="cs-card-body">
                        <div class="cs-kv-list">
                            <div class="cs-kv">
                                <span class="cs-kv-label">Consultation</span>
                                <span class="cs-kv-value">#{{ $consultation->id }}</span>
                            </div>
                            <div class="cs-kv">
                                <span class="cs-kv-label">Statut</span>
                                <span class="cs-kv-value">{{ $statusLabel }}</span>
                            </div>
                            <div class="cs-kv">
                                <span class="cs-kv-label">Patient</span>
                                <span class="cs-kv-value">{{ $patientName }}</span>
                            </div>
                            <div class="cs-kv">
                                <span class="cs-kv-label">Medecin</span>
                                <span class="cs-kv-value">{{ $medecinName }}</span>
                            </div>
                            <div class="cs-kv">
                                <span class="cs-kv-label">Date consultation</span>
                                <span class="cs-kv-value">{{ $consultationDate ? $consultationDate->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div class="cs-kv">
                                <span class="cs-kv-label">Prochaine visite</span>
                                <span class="cs-kv-value">{{ $nextVisitDate ? $nextVisitDate->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div class="cs-kv">
                                <span class="cs-kv-label">Derniere impression</span>
                                <span class="cs-kv-value" data-print-datetime>--</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="cs-card">
                    <div class="cs-card-head">
                        <h2 class="cs-card-title"><i class="fas fa-clock-rotate-left"></i> Traceabilite</h2>
                    </div>
                    <div class="cs-card-body">
                        <div class="cs-kv-list">
                            <div class="cs-kv">
                                <span class="cs-kv-label">Cree le</span>
                                <span class="cs-kv-value">{{ optional($consultation->created_at)->format('d/m/Y H:i') ?: '-' }}</span>
                            </div>
                            <div class="cs-kv">
                                <span class="cs-kv-label">Modifie le</span>
                                <span class="cs-kv-value">{{ optional($consultation->updated_at)->format('d/m/Y H:i') ?: '-' }}</span>
                            </div>
                            <div class="cs-kv">
                                <span class="cs-kv-label">Nombre ordonnances</span>
                                <span class="cs-kv-value">{{ $prescriptionsCount }}</span>
                            </div>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const printTargets = document.querySelectorAll('[data-print-datetime]');
        const printButtons = document.querySelectorAll('[data-action="print"]');

        const updatePrintDate = function () {
            const value = new Date().toLocaleString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            printTargets.forEach(function (target) {
                target.textContent = value;
            });
        };

        updatePrintDate();

        window.addEventListener('beforeprint', updatePrintDate);

        printButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                updatePrintDate();
                window.print();
            });
        });
    });
</script>
@endpush
