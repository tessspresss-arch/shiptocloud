@extends('layouts.app')

@section('title', 'Modifier ' . $medecin->nom_complet)

@include('medecins.partials.page-theme')

@section('content')
<div class="container-fluid py-3 py-xl-4">
    <div class="medecin-workspace">
        <section class="medecin-hero mb-4">
            <div class="medecin-hero-grid">
                <div>
                    <span class="medecin-eyebrow"><i class="fas fa-pen-ruler"></i> Mise a jour du profil</span>
                    <div class="medecin-title-row">
                        <span class="medecin-avatar-shell">
                            <img src="{{ $medecin->avatar_url }}" alt="{{ $medecin->nom_complet }}">
                        </span>
                        <div>
                            <h1 class="medecin-title">Modifier {{ $medecin->nom_complet }}</h1>
                            <p class="medecin-subtitle">Ajustez les informations d'identite, de facturation et de presence sans casser la coherence de la fiche praticien.</p>
                        </div>
                    </div>

                    <div class="medecin-chip-row">
                        <span class="medecin-chip"><i class="fas fa-fingerprint"></i>{{ $medecin->matricule }}</span>
                        <span class="medecin-chip"><i class="fas fa-user-doctor"></i>{{ $medecin->specialite ?: 'Medecine generale' }}</span>
                        <span class="medecin-status-pill {{ $statusClass }}">{{ $statusOptions[$medecin->statut] ?? ucfirst(str_replace('_', ' ', $medecin->statut ?? 'Inactif')) }}</span>
                    </div>
                </div>

                <aside class="medecin-action-box">
                    <span class="medecin-side-label">Actions rapides</span>
                    <p class="medecin-side-copy mt-3">Vous modifiez la fiche active du praticien. Les changements seront visibles dans les modules lies.</p>

                    <div class="medecin-hero-actions mt-3">
                        <a href="{{ route('medecins.show', $medecin) }}" class="medecin-btn primary">
                            <i class="fas fa-eye"></i>Voir la fiche
                        </a>
                        <a href="{{ route('medecins.index') }}" class="medecin-btn secondary">
                            <i class="fas fa-arrow-left"></i>Retour liste
                        </a>
                    </div>

                    <div class="medecin-divider"></div>

                    <div class="medecin-side-metric">
                        <span>Date d'embauche</span>
                        <strong>{{ $medecin->date_embauche?->format('d/m/Y') ?: 'Non renseignee' }}</strong>
                    </div>
                    <div class="medecin-side-metric">
                        <span>Dernier statut</span>
                        <strong>{{ $statusOptions[$medecin->statut] ?? ucfirst(str_replace('_', ' ', $medecin->statut ?? 'Inactif')) }}</strong>
                    </div>
                </aside>
            </div>
        </section>

        <div class="medecin-form-grid">
            <div class="medecin-card">
                <div class="medecin-card-head">
                    <div>
                        <span class="medecin-section-kicker">Edition</span>
                        <h5 class="mt-2">Mettre a jour les informations</h5>
                        <p class="mt-1">Conservez une fiche propre pour fiabiliser la planification, les exports et les documents generes.</p>
                    </div>
                </div>

                <form action="{{ route('medecins.update', $medecin) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="medecin-card-body">
                        @include('medecins.partials.form-fields', [
                            'medecin' => $medecin,
                            'specialites' => $specialites,
                            'statusOptions' => $statusOptions,
                        ])
                    </div>

                    <div class="medecin-card-foot d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <p class="medecin-meta-copy">Les images remplacees ecraseront les fichiers existants stockes pour ce praticien.</p>
                        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                            <a href="{{ route('medecins.show', $medecin) }}" class="medecin-btn secondary">Annuler</a>
                            <button type="submit" class="medecin-btn success">
                                <i class="fas fa-save"></i>Mettre a jour le medecin
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-grid gap-3">
                <aside class="medecin-side-card">
                    <span class="medecin-side-label">Reperes</span>
                    <h6 class="medecin-side-title">Etat actuel de la fiche</h6>
                    <div class="medecin-side-metric">
                        <span>Email</span>
                        <strong>{{ $medecin->email ?: 'Non renseigne' }}</strong>
                    </div>
                    <div class="medecin-side-metric">
                        <span>Telephone</span>
                        <strong>{{ $medecin->telephone ?: 'Non renseigne' }}</strong>
                    </div>
                    <div class="medecin-side-metric">
                        <span>Tarif</span>
                        <strong>{{ $medecin->tarif_consultation ? number_format($medecin->tarif_consultation, 2, ',', ' ') . ' DH' : 'Non defini' }}</strong>
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
                            <p class="medecin-empty-copy">Aucun horaire specifique enregistre. Le systeme considere le creneau standard 09:00 - 17:00 en semaine.</p>
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
                            <p class="medecin-empty-copy">Aucun conge defini pour le moment.</p>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection
