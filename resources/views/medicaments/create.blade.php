@extends('layouts.app')

@section('title', 'Ajouter un Médicament')

@push('styles')
@include('medicaments.partials.form-theme')
@endpush

@section('content')
@php
    $typeValue = old('type', 'prescription');
    $typeLabel = match ($typeValue) {
        'otc' => 'OTC',
        'controlled' => 'Contrôlé',
        default => 'Prescription',
    };
    $statusLabel = ucfirst(old('statut', 'actif'));
    $prixVente = old('prix_vente') !== null && old('prix_vente') !== '' ? number_format((float) old('prix_vente'), 2) . ' DH' : 'À définir';
@endphp

<div class="container-fluid med-form-page">
    <div class="med-form-shell">
        <header class="med-form-hero">
            <div class="med-form-hero-head">
                <div class="med-form-hero-main">
                    <ol class="med-form-breadcrumbs" aria-label="Fil d'Ariane création médicament">
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="med-form-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li><a href="{{ route('medicaments.index') }}">Médicaments</a></li>
                        <li class="med-form-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li aria-current="page">Nouveau</li>
                    </ol>

                    <div class="med-form-title-row">
                        <span class="med-form-title-icon" aria-hidden="true"><i class="fas fa-capsules"></i></span>
                        <div class="med-form-title-block">
                            <h1 class="med-form-title">Créer un médicament</h1>
                            <p class="med-form-title-subtitle">Ajoutez une nouvelle référence au catalogue avec une saisie plus claire, plus structurée et cohérente avec l’interface premium du module pharmacie.</p>
                        </div>
                    </div>

                    <div class="med-form-kpis" aria-label="Indicateurs du formulaire médicament">
                        <article class="med-form-kpi">
                            <span class="med-form-kpi-label">Type initial</span>
                            <span class="med-form-kpi-value">{{ $typeLabel }}</span>
                            <span class="med-form-kpi-meta">Configuration métier au démarrage</span>
                        </article>
                        <article class="med-form-kpi">
                            <span class="med-form-kpi-label">Statut</span>
                            <span class="med-form-kpi-value">{{ $statusLabel }}</span>
                            <span class="med-form-kpi-meta">Disponibilité administrative initiale</span>
                        </article>
                        <article class="med-form-kpi">
                            <span class="med-form-kpi-label">Prix de vente</span>
                            <span class="med-form-kpi-value">{{ $prixVente }}</span>
                            <span class="med-form-kpi-meta">Visible dès l’enregistrement</span>
                        </article>
                    </div>
                </div>

                <div class="med-form-hero-tools">
                    <section class="med-form-panel">
                        <span class="med-form-panel-label">Contexte</span>
                        <p class="med-form-panel-copy">Tous les champs existants sont conservés. L’interface est simplement réorganisée pour améliorer la vitesse de saisie, la lisibilité et la qualité perçue du module.</p>
                    </section>
                    <section class="med-form-panel">
                        <span class="med-form-panel-label">Actions</span>
                        <div class="med-form-actions">
                            <a href="{{ route('medicaments.index') }}" class="med-form-btn med-form-btn-soft">
                                <span class="med-form-btn-icon"><i class="fas fa-arrow-left"></i></span>
                                <span>Retour</span>
                            </a>
                            <button type="submit" form="medicamentCreateForm" class="med-form-btn med-form-btn-primary">
                                <span class="med-form-btn-icon"><i class="fas fa-save"></i></span>
                                <span>Enregistrer</span>
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </header>

        <div class="med-form-layout">
            <aside class="med-form-card med-form-side">
                <div class="med-form-side-head">
                    <span class="med-form-avatar" aria-hidden="true">RX</span>
                    <div class="med-form-side-copy">
                        <h2 class="med-form-side-name">Nouvelle référence</h2>
                        <p class="med-form-side-subtitle">Préparation du catalogue pharmacie</p>
                    </div>
                </div>

                <div class="med-form-side-badges">
                    <span class="med-form-chip">Type: {{ $typeLabel }}</span>
                    <span class="med-form-chip">Statut: {{ $statusLabel }}</span>
                </div>

                <h2 class="med-form-side-title">Résumé de préparation</h2>
                <ul class="med-form-side-list">
                    <li>
                        <small>Nom commercial</small>
                        <strong>{{ old('nom_commercial', 'À renseigner') }}</strong>
                    </li>
                    <li>
                        <small>Code CIP</small>
                        <strong>{{ old('code_cip', 'Non renseigné') }}</strong>
                    </li>
                    <li>
                        <small>Catégorie</small>
                        <strong>{{ old('categorie', 'À définir') }}</strong>
                    </li>
                    <li>
                        <small>Stock initial</small>
                        <strong>{{ old('quantite_stock', 0) }} unité(s)</strong>
                    </li>
                    <li>
                        <small>Prix de vente</small>
                        <strong>{{ $prixVente }}</strong>
                    </li>
                    <li>
                        <small>Remboursement</small>
                        <strong>{{ old('remboursable') ? 'Activé' : 'Non activé' }}</strong>
                    </li>
                </ul>
            </aside>

            <section class="med-form-card med-form-main">
                <div class="med-form-main-head">
                    <div>
                        <h2 class="med-form-main-title">Formulaire de création structuré</h2>
                        <p class="med-form-title-subtitle">Renseignez les informations essentielles, tarifaires, logistiques et cliniques dans une séquence plus naturelle pour le travail quotidien.</p>
                    </div>
                    <span class="med-form-badge">Nouveau médicament</span>
                </div>

                <form action="{{ route('medicaments.store') }}" method="POST" id="medicamentCreateForm">
                    @csrf
                    <div class="med-form-body">
                        @include('medicaments.partials.form-fields')
                    </div>

                    <div class="med-form-footer">
                        <a href="{{ route('medicaments.index') }}" class="med-form-btn med-form-btn-soft">
                            <span class="med-form-btn-icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Annuler</span>
                        </a>
                        <button type="submit" class="med-form-btn med-form-btn-primary">
                            <span class="med-form-btn-icon"><i class="fas fa-floppy-disk"></i></span>
                            <span>Enregistrer le médicament</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="med-form-mobile-actions">
        <a href="{{ route('medicaments.index') }}" class="med-form-btn med-form-btn-soft">
            <span class="med-form-btn-icon"><i class="fas fa-arrow-left"></i></span>
            <span>Retour</span>
        </a>
        <button type="submit" form="medicamentCreateForm" class="med-form-btn med-form-btn-primary">
            <span class="med-form-btn-icon"><i class="fas fa-save"></i></span>
            <span>Enregistrer</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const prixVenteInput = document.getElementById('prix_vente');
        const tauxRemboursementInput = document.getElementById('taux_remboursement');
        const prixRemboursementInput = document.getElementById('prix_remboursement');
        const datePeremptionInput = document.getElementById('date_peremption');

        function calculateRefundPrice() {
            if (!prixVenteInput || !tauxRemboursementInput || !prixRemboursementInput) {
                return;
            }

            const prixVente = parseFloat(prixVenteInput.value) || 0;
            const tauxRemboursement = parseFloat(tauxRemboursementInput.value) || 0;

            if (prixVente > 0 && tauxRemboursement > 0) {
                prixRemboursementInput.value = (prixVente * tauxRemboursement / 100).toFixed(2);
            }
        }

        prixVenteInput?.addEventListener('input', calculateRefundPrice);
        tauxRemboursementInput?.addEventListener('input', calculateRefundPrice);

        datePeremptionInput?.addEventListener('change', function () {
            if (!this.value) {
                return;
            }

            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate <= today) {
                alert('La date de péremption doit être dans le futur.');
                this.value = '';
            }
        });
    });
</script>
@endpush