@extends('layouts.app')

@section('title', 'Examen - ' . ($examen->type_examen ?? 'Detail'))
@section('topbar_subtitle', 'Lecture detaillee de l examen, resultats et actions cliniques dans une vue premium harmonisee.')

@section('content')
@php
    $patientName = $examen->patient?->nom_complet ?: trim(($examen->patient->nom ?? '') . ' ' . ($examen->patient->prenom ?? '')) ?: 'Patient inconnu';
    $statusLabel = ucfirst(str_replace('_', ' ', $examen->statut ?: 'inconnu'));
    $statusClass = in_array($examen->statut, ['demande', 'en_cours', 'en_attente', 'termine', 'annule'], true) ? $examen->statut : 'default';
    $resultsCount = $examen->resultatsExamens?->count() ?? 0;
@endphp

<style>
    .exam-show-page {
        --exam-primary: #1760a5;
        --exam-primary-strong: #0f4c84;
        --exam-accent: #0ea5e9;
        --exam-success: #0f9f77;
        --exam-warning: #c57d10;
        --exam-danger: #cb4d58;
        --exam-surface: linear-gradient(180deg, #f5f9fd 0%, #eef5fb 100%);
        --exam-card: #ffffff;
        --exam-border: #d8e4f1;
        --exam-text: #15314d;
        --exam-muted: #5f7896;
        width: 100%;
        max-width: none;
        padding: 10px 8px 36px;
    }
    .exam-show-shell { display: grid; gap: 18px; }
    .exam-show-hero { position: relative; overflow: hidden; border: 1px solid var(--exam-border); border-radius: 28px; padding: 22px; background: radial-gradient(circle at top right, rgba(23, 96, 165, 0.18) 0%, rgba(23, 96, 165, 0) 30%), radial-gradient(circle at left top, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 34%), var(--exam-surface); box-shadow: 0 28px 48px -40px rgba(20, 52, 84, 0.42); }
    .exam-show-hero::before { content: ""; position: absolute; inset: 0; pointer-events: none; background: linear-gradient(180deg, rgba(255,255,255,.5) 0%, rgba(255,255,255,0) 100%); }
    .exam-show-hero > * { position: relative; z-index: 1; }
    .exam-show-hero-grid { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(300px, 0.9fr); gap: 18px; align-items: start; }
    .exam-show-eyebrow, .exam-show-side-label, .exam-show-section-kicker { display: inline-flex; align-items: center; gap: 8px; min-height: 34px; padding: 0 14px; border-radius: 999px; background: rgba(23, 96, 165, .1); color: var(--exam-primary); font-size: .78rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .exam-show-title-row { display: flex; align-items: flex-start; gap: 14px; margin-top: 14px; }
    .exam-show-title-icon { width: 56px; height: 56px; border-radius: 18px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; color: #fff; font-size: 1.35rem; background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-strong) 100%); box-shadow: 0 16px 28px -18px rgba(23,96,165,.58); }
    .exam-show-title { margin: 0; color: var(--exam-text); font-size: clamp(1.6rem, 2.8vw, 2.2rem); line-height: 1.04; letter-spacing: -.04em; font-weight: 900; }
    .exam-show-subtitle { margin: 10px 0 0; max-width: 72ch; color: var(--exam-muted); font-size: .98rem; line-height: 1.65; font-weight: 600; }
    .exam-show-chip-row, .exam-show-actions, .exam-show-table-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .exam-show-chip, .exam-show-status { display: inline-flex; align-items: center; gap: 8px; min-height: 36px; padding: 0 14px; border-radius: 999px; border: 1px solid #d4e2f2; background: rgba(255,255,255,.76); color: #1a4d86; font-size: .82rem; font-weight: 800; }
    .exam-show-status.demande { background: rgba(183,121,31,.14); border-color: rgba(183,121,31,.18); color: #9a6817; }
    .exam-show-status.en_cours, .exam-show-status.en_attente { background: rgba(8,145,178,.14); border-color: rgba(8,145,178,.18); color: #0c7994; }
    .exam-show-status.termine { background: rgba(15,159,119,.13); border-color: rgba(15,159,119,.18); color: #0f7e5f; }
    .exam-show-status.annule, .exam-show-status.default { background: rgba(100,116,139,.12); border-color: rgba(100,116,139,.18); color: #51606f; }
    .exam-show-btn { min-height: 46px; border-radius: 14px; border: 1px solid transparent; padding: 0 18px; display: inline-flex; align-items: center; justify-content: center; gap: 10px; font-size: .92rem; font-weight: 800; text-decoration: none; transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease; white-space: nowrap; }
    .exam-show-btn:hover, .exam-show-btn:focus { transform: translateY(-1px); text-decoration: none; }
    .exam-show-btn.soft { border-color: #cfdef0; background: linear-gradient(180deg, #ffffff 0%, #f5f9fd 100%); color: #385674; }
    .exam-show-btn.primary { color: #fff; background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-strong) 100%); box-shadow: 0 18px 30px -22px rgba(23,96,165,.58); }
    .exam-show-btn-icon { width: 28px; height: 28px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: rgba(23,96,165,.1); color: var(--exam-primary); }
    .exam-show-btn.primary .exam-show-btn-icon { background: rgba(255,255,255,.16); color: inherit; }
    .exam-show-side, .exam-show-kpi, .exam-show-card, .exam-show-result-card { background: var(--exam-card); border: 1px solid var(--exam-border); border-radius: 24px; box-shadow: 0 24px 36px -34px rgba(15,23,42,.4); }
    .exam-show-side { padding: 18px; display: grid; gap: 18px; }
    .exam-show-side-value { margin: 0; color: var(--exam-text); font-size: clamp(2rem, 3.3vw, 2.5rem); line-height: 1; font-weight: 900; letter-spacing: -.05em; }
    .exam-show-side-copy { margin: 10px 0 0; color: var(--exam-muted); font-size: .93rem; line-height: 1.6; font-weight: 600; }
    .exam-show-side-metrics, .exam-show-patient-list { display: grid; gap: 10px; }
    .exam-show-side-metric, .exam-show-patient-list li { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 12px 14px; border-radius: 16px; border: 1px solid #dce8f4; background: linear-gradient(180deg, #fbfdff 0%, #f6fafe 100%); }
    .exam-show-side-metric span, .exam-show-patient-list small { color: var(--exam-muted); font-size: .86rem; font-weight: 700; }
    .exam-show-side-metric strong, .exam-show-patient-list strong { color: var(--exam-text); font-size: 1rem; font-weight: 900; text-align: right; }
    .exam-show-kpi-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .exam-show-kpi { padding: 18px; display: grid; gap: 14px; }
    .exam-show-kpi-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .exam-show-kpi-label { margin: 0; color: var(--exam-muted); font-size: .82rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
    .exam-show-kpi-value { margin: 8px 0 0; color: var(--exam-text); font-size: clamp(1.5rem, 2.5vw, 2rem); line-height: 1; font-weight: 900; letter-spacing: -.04em; }
    .exam-show-kpi-copy { margin: 0; color: var(--exam-muted); font-size: .9rem; line-height: 1.55; font-weight: 600; }
    .exam-show-kpi-icon { width: 48px; height: 48px; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .exam-show-kpi-icon.type { color: #1d6fc0; background: rgba(29,111,192,.12); }
    .exam-show-kpi-icon.status { color: #0c7994; background: rgba(8,145,178,.14); }
    .exam-show-kpi-icon.results { color: #0f9f77; background: rgba(15,159,119,.14); }
    .exam-show-kpi-icon.finance { color: #b7791f; background: rgba(183,121,31,.15); }
    .exam-show-layout { display: grid; grid-template-columns: minmax(0, 1.3fr) 340px; gap: 16px; align-items: start; }
    .exam-show-card { padding: 20px; }
    .exam-show-card-title { margin: 0; color: var(--exam-text); font-size: 1.24rem; font-weight: 900; letter-spacing: -.03em; }
    .exam-show-card-copy { margin: 8px 0 0; color: var(--exam-muted); font-size: .93rem; line-height: 1.6; font-weight: 600; }
    .exam-show-info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-top: 18px; }
    .exam-show-info-item { padding: 14px; border-radius: 18px; border: 1px solid #dde8f2; background: linear-gradient(180deg, #fbfdff 0%, #f7fbff 100%); }
    .exam-show-info-item.full { grid-column: 1 / -1; }
    .exam-show-info-label { color: var(--exam-muted); font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
    .exam-show-info-value { margin-top: 8px; color: var(--exam-text); font-size: .96rem; font-weight: 700; line-height: 1.6; }
    .exam-show-result-card { padding: 20px; }
    .exam-show-result-list { display: grid; gap: 12px; margin-top: 18px; }
    .exam-show-result-item { padding: 14px; border-radius: 18px; border: 1px solid #dde8f2; background: linear-gradient(180deg, #fbfdff 0%, #f7fbff 100%); }
    .exam-show-result-name { margin: 0; color: var(--exam-text); font-size: .96rem; font-weight: 800; }
    .exam-show-result-value { margin-top: 6px; color: var(--exam-text); font-size: .92rem; font-weight: 700; }
    .exam-show-result-meta { margin-top: 6px; color: var(--exam-muted); font-size: .82rem; font-weight: 600; }
    .exam-show-empty { padding: 16px; border-radius: 18px; border: 1px dashed #d2dfed; background: linear-gradient(180deg, #fcfdff 0%, #f7fbff 100%); color: var(--exam-muted); font-size: .92rem; font-weight: 600; text-align: center; }
    html.dark body .exam-show-page, body.dark-mode .exam-show-page, body.theme-dark .exam-show-page { --exam-surface: linear-gradient(180deg, #152233 0%, #122032 100%); --exam-card: #162332; --exam-border: #2f4358; --exam-text: #e6edf6; --exam-muted: #9eb1c7; }
    html.dark body .exam-show-side, html.dark body .exam-show-kpi, html.dark body .exam-show-card, html.dark body .exam-show-result-card, html.dark body .exam-show-side-metric, html.dark body .exam-show-patient-list li, html.dark body .exam-show-info-item, html.dark body .exam-show-result-item, html.dark body .exam-show-empty, body.dark-mode .exam-show-side, body.dark-mode .exam-show-kpi, body.dark-mode .exam-show-card, body.dark-mode .exam-show-result-card, body.dark-mode .exam-show-side-metric, body.dark-mode .exam-show-patient-list li, body.dark-mode .exam-show-info-item, body.dark-mode .exam-show-result-item, body.dark-mode .exam-show-empty, body.theme-dark .exam-show-side, body.theme-dark .exam-show-kpi, body.theme-dark .exam-show-card, body.theme-dark .exam-show-result-card, body.theme-dark .exam-show-side-metric, body.theme-dark .exam-show-patient-list li, body.theme-dark .exam-show-info-item, body.theme-dark .exam-show-result-item, body.theme-dark .exam-show-empty { background: rgba(17, 34, 54, 0.9); border-color: #35506a; }
    html.dark body .exam-show-chip, body.dark-mode .exam-show-chip, body.theme-dark .exam-show-chip { background: #14273e; border-color: #305173; color: #cde2ff; }
    html.dark body .exam-show-btn.soft, body.dark-mode .exam-show-btn.soft, body.theme-dark .exam-show-btn.soft { border-color: #365b7d; background: linear-gradient(150deg, #183552 0%, #14304b 100%); color: #d2e6fb; }
    html.dark body .exam-show-btn-icon, body.dark-mode .exam-show-btn-icon, body.theme-dark .exam-show-btn-icon { background: rgba(119,183,255,.16); color: #9fd0ff; }
    @media (max-width: 1200px) { .exam-show-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 992px) { .exam-show-hero-grid, .exam-show-layout { grid-template-columns: 1fr; } }
    @media (max-width: 767px) { .exam-show-page { padding-left: 0; padding-right: 0; } .exam-show-kpi-grid, .exam-show-info-grid { grid-template-columns: 1fr; } .exam-show-actions, .exam-show-table-actions { flex-direction: column; align-items: stretch; } .exam-show-btn { width: 100%; } }
</style>

<div class="container-fluid exam-show-page">
    <div class="exam-show-shell">
        <section class="exam-show-hero">
            <div class="exam-show-hero-grid">
                <div>
                    <span class="exam-show-eyebrow">Fiche examen</span>
                    <div class="exam-show-title-row">
                        <span class="exam-show-title-icon"><i class="fas fa-flask-vial"></i></span>
                        <div>
                            <h1 class="exam-show-title">{{ $examen->type_examen ?: 'Examen medical' }}</h1>
                            <p class="exam-show-subtitle">Consultez les informations cliniques, techniques et financieres de l examen avec une vue plus lisible et mieux structuree.</p>
                        </div>
                    </div>

                    <div class="exam-show-chip-row" style="margin-top:18px;">
                        <span class="exam-show-chip"><i class="fas fa-user"></i>{{ $patientName }}</span>
                        <span class="exam-show-chip"><i class="fas fa-calendar-day"></i>{{ $examen->date_demande?->format('d/m/Y') ?: '-' }}</span>
                        <span class="exam-show-status {{ $statusClass }}"><i class="fas fa-circle-info"></i>{{ $statusLabel }}</span>
                    </div>

                    <div class="exam-show-actions" style="margin-top:18px;">
                        <a href="{{ route('examens.index') }}" class="exam-show-btn soft"><span class="exam-show-btn-icon"><i class="fas fa-arrow-left"></i></span><span>Retour</span></a>
                        <a href="{{ route('examens.edit', $examen) }}" class="exam-show-btn primary"><span class="exam-show-btn-icon"><i class="fas fa-pen"></i></span><span>Modifier</span></a>
                    </div>
                </div>

                <aside class="exam-show-side">
                    <div>
                        <div class="exam-show-side-label">Vue rapide</div>
                        <p class="exam-show-side-value">{{ $resultsCount }}</p>
                        <p class="exam-show-side-copy">Resultat{{ $resultsCount > 1 ? 's' : '' }} detaille{{ $resultsCount > 1 ? 's' : '' }} rattache{{ $resultsCount > 1 ? 's' : '' }} a cet examen.</p>
                    </div>

                    <div class="exam-show-side-metrics">
                        <div class="exam-show-side-metric"><span>Paiement</span><strong>{{ $examen->payee ? 'Paye' : 'Non paye' }}</strong></div>
                        <div class="exam-show-side-metric"><span>Coût</span><strong>{{ $examen->cout ? number_format($examen->cout, 2, ',', ' ') . ' DH' : '-' }}</strong></div>
                        <div class="exam-show-side-metric"><span>Date realisee</span><strong>{{ $examen->date_realisation?->format('d/m/Y') ?: '-' }}</strong></div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="exam-show-kpi-grid">
            <article class="exam-show-kpi">
                <div class="exam-show-kpi-top"><div><p class="exam-show-kpi-label">Type technique</p><p class="exam-show-kpi-value">{{ ucfirst($examen->type ?: '-') }}</p></div><span class="exam-show-kpi-icon type"><i class="fas fa-vials"></i></span></div>
                <p class="exam-show-kpi-copy">Nature technique ou famille d examen rattachee a la demande.</p>
            </article>
            <article class="exam-show-kpi">
                <div class="exam-show-kpi-top"><div><p class="exam-show-kpi-label">Statut</p><p class="exam-show-kpi-value">{{ $statusLabel }}</p></div><span class="exam-show-kpi-icon status"><i class="fas fa-wave-square"></i></span></div>
                <p class="exam-show-kpi-copy">Etat courant d avancement de l examen dans le parcours de soin.</p>
            </article>
            <article class="exam-show-kpi">
                <div class="exam-show-kpi-top"><div><p class="exam-show-kpi-label">Resultats detailles</p><p class="exam-show-kpi-value">{{ $resultsCount }}</p></div><span class="exam-show-kpi-icon results"><i class="fas fa-notes-medical"></i></span></div>
                <p class="exam-show-kpi-copy">Nombre de parametres ou resultats detailles enregistres sur la fiche.</p>
            </article>
            <article class="exam-show-kpi">
                <div class="exam-show-kpi-top"><div><p class="exam-show-kpi-label">Paiement</p><p class="exam-show-kpi-value">{{ $examen->payee ? 'Confirme' : 'En attente' }}</p></div><span class="exam-show-kpi-icon finance"><i class="fas fa-wallet"></i></span></div>
                <p class="exam-show-kpi-copy">Visibilite immediate sur la situation financiere de cet examen.</p>
            </article>
        </section>

        <div class="exam-show-layout">
            <section class="exam-show-card">
                <div class="exam-show-section-kicker">Informations de l examen</div>
                <h2 class="exam-show-card-title">Synthese clinique et technique</h2>
                <p class="exam-show-card-copy">Retrouvez les donnees utiles au suivi: type, dates, praticien, description et observations associees.</p>

                <div class="exam-show-info-grid">
                    <div class="exam-show-info-item"><div class="exam-show-info-label">Type technique</div><div class="exam-show-info-value">{{ ucfirst($examen->type ?: '-') }}</div></div>
                    <div class="exam-show-info-item"><div class="exam-show-info-label">Statut</div><div class="exam-show-info-value">{{ $statusLabel }}</div></div>
                    <div class="exam-show-info-item"><div class="exam-show-info-label">Date demandee</div><div class="exam-show-info-value">{{ $examen->date_demande?->format('d/m/Y H:i') ?: '-' }}</div></div>
                    <div class="exam-show-info-item"><div class="exam-show-info-label">Date realisee</div><div class="exam-show-info-value">{{ $examen->date_realisation?->format('d/m/Y H:i') ?: '-' }}</div></div>
                    <div class="exam-show-info-item"><div class="exam-show-info-label">Localisation</div><div class="exam-show-info-value">{{ $examen->localisation ?: '-' }}</div></div>
                    <div class="exam-show-info-item"><div class="exam-show-info-label">Medecin prescripteur</div><div class="exam-show-info-value">{{ $examen->medecin?->nom_complet ?: $examen->medecin?->name ?: '-' }}</div></div>
                    <div class="exam-show-info-item full"><div class="exam-show-info-label">Description</div><div class="exam-show-info-value">{{ $examen->description ?: 'Aucune description.' }}</div></div>
                    <div class="exam-show-info-item full"><div class="exam-show-info-label">Observations</div><div class="exam-show-info-value">{{ $examen->observations ?: 'Aucune observation.' }}</div></div>
                    @if(!empty($examen->resultats))
                        <div class="exam-show-info-item full"><div class="exam-show-info-label">Resultats saisis</div><div class="exam-show-info-value">{{ $examen->resultats }}</div></div>
                    @endif
                    @if(!empty($examen->recommandations))
                        <div class="exam-show-info-item full"><div class="exam-show-info-label">Recommandations</div><div class="exam-show-info-value">{{ $examen->recommandations }}</div></div>
                    @endif
                </div>
            </section>

            <div class="exam-show-shell" style="gap:16px;">
                <aside class="exam-show-card">
                    <div class="exam-show-section-kicker">Patient</div>
                    <h2 class="exam-show-card-title">Resume patient</h2>
                    <p class="exam-show-card-copy">Acces rapide a l identite du patient et au dossier lie a l examen.</p>
                    <ul class="exam-show-patient-list" style="margin-top:18px; list-style:none; padding:0;">
                        <li><small>Patient</small><strong>{{ $patientName }}</strong></li>
                        <li><small>Dossier</small><strong>{{ $examen->patient?->numero_dossier ?: '-' }}</strong></li>
                        <li><small>Email</small><strong>{{ $examen->patient?->email ?: '-' }}</strong></li>
                    </ul>
                </aside>

                <section class="exam-show-result-card">
                    <div class="exam-show-section-kicker">Resultats detailles</div>
                    <h2 class="exam-show-card-title">Analyse parametrique</h2>
                    <p class="exam-show-card-copy">Consultez les mesures, valeurs et interpretations rattachees a cet examen.</p>

                    @if($resultsCount > 0)
                        <div class="exam-show-result-list">
                            @foreach($examen->resultatsExamens as $resultat)
                                <article class="exam-show-result-item">
                                    <h3 class="exam-show-result-name">{{ $resultat->parametre }}</h3>
                                    <div class="exam-show-result-value">{{ $resultat->valeur }} {{ $resultat->unite }}</div>
                                    <div class="exam-show-result-meta">Interpretation: {{ ucfirst($resultat->interpretation) }}</div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="exam-show-empty">Aucun resultat detaille n est encore disponible pour cet examen.</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</div>
@endsection