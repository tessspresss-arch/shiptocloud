@extends('layouts.app')

@section('title', 'Medecin - ' . $medecin->nom_complet)

@include('medecins.partials.page-theme')

@section('content')
<div class="container-fluid py-3 py-xl-4">
    <div class="medecin-workspace">
        <section class="medecin-hero mb-4">
            <div class="medecin-show-grid">
                <div>
                    <span class="medecin-eyebrow"><i class="fas fa-user-doctor"></i> Fiche praticien</span>
                    <div class="medecin-title-row">
                        <span class="medecin-avatar-shell">
                            <img src="{{ $medecin->avatar_url }}" alt="{{ $medecin->nom_complet }}">
                        </span>
                        <div>
                            <h1 class="medecin-title">{{ $medecin->nom_complet }}</h1>
                            <p class="medecin-subtitle">{{ $medecin->specialite ?: 'Medecine generale' }} · {{ $medecin->matricule }}</p>
                        </div>
                    </div>

                    <div class="medecin-chip-row">
                        <span class="medecin-status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                        <span class="medecin-chip"><i class="fas fa-phone"></i>{{ $medecin->telephone ?: 'Telephone non renseigne' }}</span>
                        <span class="medecin-chip"><i class="fas fa-envelope"></i>{{ $medecin->email ?: 'Email non renseigne' }}</span>
                    </div>
                </div>

                <aside class="medecin-action-box">
                    <span class="medecin-side-label">Actions rapides</span>
                    <p class="medecin-side-copy mt-3">Cette fiche centralise le profil, les volumes d'activite et les informations administratives du praticien.</p>

                    <div class="medecin-hero-actions mt-3">
                        <a href="{{ route('medecins.edit', $medecin) }}" class="medecin-btn primary">
                            <i class="fas fa-pen"></i>Modifier la fiche
                        </a>
                        <a href="{{ route('medecins.index') }}" class="medecin-btn secondary">
                            <i class="fas fa-arrow-left"></i>Retour a la liste
                        </a>
                    </div>

                    <div class="medecin-divider"></div>

                    <div class="medecin-summary-list">
                        <span class="medecin-summary-pill"><i class="fas fa-calendar-day"></i>Embauche: {{ $medecin->date_embauche?->format('d/m/Y') ?: 'N/A' }}</span>
                        <span class="medecin-summary-pill"><i class="fas fa-money-bill-wave"></i>Tarif: {{ $medecin->tarif_consultation ? number_format($medecin->tarif_consultation, 2, ',', ' ') . ' DH' : 'N/A' }}</span>
                    </div>
                </aside>
            </div>
        </section>

        <section class="medecin-kpi-grid mb-4">
            <article class="medecin-kpi">
                <span class="medecin-stat-label">Consultations</span>
                <strong class="medecin-stat-value">{{ $medecin->consultations_count }}</strong>
                <p class="medecin-meta-copy">Historique clinique rattache a ce praticien.</p>
            </article>
            <article class="medecin-kpi">
                <span class="medecin-stat-label">Rendez-vous</span>
                <strong class="medecin-stat-value">{{ $medecin->rendezvous_count }}</strong>
                <p class="medecin-meta-copy">Volume de planification enregistre.</p>
            </article>
            <article class="medecin-kpi">
                <span class="medecin-stat-label">Ordonnances</span>
                <strong class="medecin-stat-value">{{ $medecin->ordonnances_count }}</strong>
                <p class="medecin-meta-copy">Documents medicaux emis via la plateforme.</p>
            </article>
        </section>

        <div class="medecin-show-grid">
            <div class="d-grid gap-4">
                <section class="medecin-card">
                    <div class="medecin-card-head">
                        <div>
                            <span class="medecin-section-kicker">Profil</span>
                            <h5 class="mt-2">Informations professionnelles</h5>
                            <p class="mt-1">Vue complete de la fiche administrative et operationnelle du medecin.</p>
                        </div>
                    </div>

                    <div class="medecin-card-body">
                        <div class="medecin-detail-grid">
                            <div class="medecin-detail-item">
                                <div class="medecin-detail-label">Numero d'ordre</div>
                                <div class="medecin-detail-value">{{ $medecin->numero_ordre ?: 'Non renseigne' }}</div>
                            </div>
                            <div class="medecin-detail-item">
                                <div class="medecin-detail-label">Tarif consultation</div>
                                <div class="medecin-detail-value">{{ $medecin->tarif_consultation ? number_format($medecin->tarif_consultation, 2, ',', ' ') . ' DH' : 'Non defini' }}</div>
                            </div>
                            <div class="medecin-detail-item">
                                <div class="medecin-detail-label">Telephone</div>
                                <div class="medecin-detail-value">{{ $medecin->telephone ?: 'Non renseigne' }}</div>
                            </div>
                            <div class="medecin-detail-item">
                                <div class="medecin-detail-label">Email</div>
                                <div class="medecin-detail-value">{{ $medecin->email ?: 'Non renseigne' }}</div>
                            </div>
                            <div class="medecin-detail-item full">
                                <div class="medecin-detail-label">Adresse du cabinet</div>
                                <div class="medecin-detail-value">
                                    {{ $medecin->adresse_cabinet ?: 'Non renseignee' }}
                                    @if($medecin->ville || $medecin->code_postal)
                                        <br>{{ trim(($medecin->code_postal ? $medecin->code_postal . ' ' : '') . ($medecin->ville ?: '')) }}
                                    @endif
                                </div>
                            </div>
                            <div class="medecin-detail-item">
                                <div class="medecin-detail-label">Date d'embauche</div>
                                <div class="medecin-detail-value">{{ $medecin->date_embauche?->format('d/m/Y') ?: 'Non renseignee' }}</div>
                            </div>
                            <div class="medecin-detail-item">
                                <div class="medecin-detail-label">Date de depart</div>
                                <div class="medecin-detail-value">{{ $medecin->date_depart?->format('d/m/Y') ?: 'Non renseignee' }}</div>
                            </div>
                            <div class="medecin-detail-item full">
                                <div class="medecin-detail-label">Notes internes</div>
                                <div class="medecin-detail-value">{{ $medecin->notes ?: 'Aucune note interne enregistree.' }}</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="d-grid gap-4">
                <aside class="medecin-side-card">
                    <span class="medecin-side-label">Pieces</span>
                    <h6 class="medecin-side-title">Documents visuels</h6>
                    <div class="medecin-file-preview">
                        @if($medecin->photo_path)
                            <img src="{{ asset('storage/' . $medecin->photo_path) }}" alt="Photo de {{ $medecin->nom_complet }}">
                        @else
                            <i class="fas fa-image"></i>
                            <span>Pas de photo enregistree</span>
                        @endif
                    </div>
                    <div class="medecin-file-preview signature">
                        @if($medecin->signature_path)
                            <img src="{{ asset('storage/' . $medecin->signature_path) }}" alt="Signature de {{ $medecin->nom_complet }}">
                        @else
                            <i class="fas fa-signature"></i>
                            <span>Pas de signature enregistree</span>
                        @endif
                    </div>
                </aside>

                <aside class="medecin-side-card">
                    <span class="medecin-side-label">Disponibilites</span>
                    @if($medecin->horaires_travail)
                        <ul class="medecin-list">
                            @foreach($medecin->horaires_travail as $jour => $horaire)
                                <li>
                                    <i class="fas fa-clock"></i>
                                    <span>{{ ucfirst($jour) }}: {{ $horaire['debut'] ?? '09:00' }} - {{ $horaire['fin'] ?? '17:00' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="medecin-empty">
                            <p class="medecin-empty-copy">Aucun horaire detaille enregistre. La configuration standard reste appliquee.</p>
                        </div>
                    @endif
                </aside>

                <aside class="medecin-side-card">
                    <span class="medecin-side-label">Conges</span>
                    @if($medecin->jours_conges && count($medecin->jours_conges) > 0)
                        <ul class="medecin-list">
                            @foreach($medecin->jours_conges as $jour)
                                <li><i class="fas fa-calendar-times"></i><span>{{ \Carbon\Carbon::parse($jour)->format('d/m/Y') }}</span></li>
                            @endforeach
                        </ul>
                    @else
                        <div class="medecin-empty">
                            <p class="medecin-empty-copy">Aucun jour de conge n'est configure sur cette fiche.</p>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection
