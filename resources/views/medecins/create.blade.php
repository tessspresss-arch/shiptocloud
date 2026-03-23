@extends('layouts.app')

@section('title', 'Ajouter un Medecin')

@include('medecins.partials.page-theme')

@section('content')
<div class="container-fluid py-3 py-xl-4">
    <div class="medecin-workspace">
        <section class="medecin-hero mb-4">
            <div class="medecin-hero-grid">
                <div>
                    <span class="medecin-eyebrow"><i class="fas fa-user-doctor"></i> Gestion des medecins</span>
                    <div class="medecin-title-row">
                        <span class="medecin-title-icon"><i class="fas fa-stethoscope"></i></span>
                        <div>
                            <h1 class="medecin-title">Nouveau medecin</h1>
                            <p class="medecin-subtitle">Creez une fiche complete, exploitable pour la planification, la consultation et la facturation.</p>
                        </div>
                    </div>

                    <div class="medecin-chip-row">
                        <span class="medecin-chip"><i class="fas fa-id-badge"></i> Identite et contacts</span>
                        <span class="medecin-chip"><i class="fas fa-briefcase-medical"></i> Profil et statut</span>
                        <span class="medecin-chip"><i class="fas fa-file-signature"></i> Documents et notes</span>
                    </div>
                </div>

                <div class="medecin-hero-aside">
                    <div class="medecin-hero-actions">
                        <a href="{{ route('medecins.index') }}" class="medecin-btn secondary">
                            <i class="fas fa-arrow-left"></i>Retour a la liste
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <div class="medecin-form-grid">
            <div class="medecin-card">
                <div class="medecin-card-head">
                    <div>
                        <span class="medecin-section-kicker">Creation</span>
                        <h5 class="mt-2">Informations du medecin</h5>
                        <p class="mt-1">Les champs ci-dessous structurent la fiche administrative, le profil professionnel et les pieces visuelles.</p>
                    </div>
                </div>

                <form action="{{ route('medecins.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="medecin-card-body">
                        @include('medecins.partials.form-fields', [
                            'specialites' => $specialites,
                            'statusOptions' => $statusOptions,
                        ])
                    </div>

                    <div class="medecin-card-foot d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <p class="medecin-meta-copy"><span class="text-danger">*</span> Les champs obligatoires garantissent une fiche exploitable dans tout le parcours patient.</p>
                        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                            <a href="{{ route('medecins.index') }}" class="medecin-btn secondary">Annuler</a>
                            <button type="submit" class="medecin-btn primary">
                                <i class="fas fa-save"></i>Enregistrer le medecin
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-grid gap-3">
                <aside class="medecin-side-card">
                    <span class="medecin-side-label">Parcours</span>
                    <h6 class="medecin-side-title">Deploiement progressif</h6>
                    <p class="medecin-side-copy">La fiche creee servira de base pour les rendez-vous, consultations et ordonnances associees.</p>

                    <div class="medecin-side-metric">
                        <span>Disponibilites</span>
                        <strong>Apres creation</strong>
                    </div>
                    <div class="medecin-side-metric">
                        <span>Conges</span>
                        <strong>A configurer</strong>
                    </div>
                    <div class="medecin-side-metric">
                        <span>Signature PNG</span>
                        <strong>Optionnelle</strong>
                    </div>
                </aside>

                <aside class="medecin-side-card">
                    <span class="medecin-side-label">Bonnes pratiques</span>
                    <ul class="medecin-list">
                        <li><i class="fas fa-check-circle"></i><span>Renseignez un email valide pour les echanges et rappels internes.</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Ajoutez une specialite claire pour ameliorer les filtres et l'orientation patient.</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Privilegiez une signature PNG sur fond transparent pour les documents edites.</span></li>
                    </ul>
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection
