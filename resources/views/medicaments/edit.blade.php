@extends('layouts.app')

@section('title', 'Modifier un Médicament')

@push('styles')
@include('medicaments.partials.form-theme')
@endpush

@section('content')
@php
    $typeLabel = match ($medicament->type) {
        'otc' => 'OTC',
        'controlled' => 'Contrôlé',
        default => 'Prescription',
    };
    $statusLabel = ucfirst($medicament->statut ?? 'actif');
    $stockValue = number_format((float) $medicament->quantite_stock, 0, ',', ' ');
    $stockWorth = number_format((float) ($medicament->valeur_stock ?? 0), 2) . ' DH';
@endphp

<div class="container-fluid med-form-page">
    <div class="med-form-shell">
        <header class="med-form-hero">
            <div class="med-form-hero-head">
                <div class="med-form-hero-main">
                    <ol class="med-form-breadcrumbs" aria-label="Fil d'Ariane édition médicament">
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="med-form-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li><a href="{{ route('medicaments.index') }}">Médicaments</a></li>
                        <li class="med-form-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li><a href="{{ route('medicaments.show', $medicament) }}">{{ $medicament->nom_commercial }}</a></li>
                        <li class="med-form-breadcrumb-separator" aria-hidden="true"><i class="fas fa-chevron-right"></i></li>
                        <li aria-current="page">Modifier</li>
                    </ol>

                    <div class="med-form-title-row">
                        <span class="med-form-title-icon" aria-hidden="true"><i class="fas fa-pen-to-square"></i></span>
                        <div class="med-form-title-block">
                            <h1 class="med-form-title">Modifier le médicament</h1>
                            <p class="med-form-title-subtitle">Mettez à jour la fiche {{ $medicament->nom_commercial }} dans un formulaire plus structuré, plus lisible et aligné avec le nouveau standard premium du module.</p>
                        </div>
                    </div>

                    <div class="med-form-kpis" aria-label="Indicateurs du médicament">
                        <article class="med-form-kpi">
                            <span class="med-form-kpi-label">Type</span>
                            <span class="med-form-kpi-value">{{ $typeLabel }}</span>
                            <span class="med-form-kpi-meta">Référence catalogue</span>
                        </article>
                        <article class="med-form-kpi">
                            <span class="med-form-kpi-label">Stock actuel</span>
                            <span class="med-form-kpi-value">{{ $stockValue }}</span>
                            <span class="med-form-kpi-meta">Unités disponibles</span>
                        </article>
                        <article class="med-form-kpi">
                            <span class="med-form-kpi-label">Valeur stock</span>
                            <span class="med-form-kpi-value">{{ $stockWorth }}</span>
                            <span class="med-form-kpi-meta">Valorisation actuelle</span>
                        </article>
                    </div>
                </div>

                <div class="med-form-hero-tools">
                    <section class="med-form-panel">
                        <span class="med-form-panel-label">Contexte</span>
                        <p class="med-form-panel-copy">La logique métier et les champs restent identiques. Seule l’expérience d’édition est modernisée pour rendre les mises à jour plus rapides et plus fiables visuellement.</p>
                    </section>
                    <section class="med-form-panel">
                        <span class="med-form-panel-label">Actions</span>
                        <div class="med-form-actions">
                            <a href="{{ route('medicaments.show', $medicament) }}" class="med-form-btn med-form-btn-soft">
                                <span class="med-form-btn-icon"><i class="fas fa-arrow-left"></i></span>
                                <span>Retour</span>
                            </a>
                            <button type="submit" form="medicamentEditForm" class="med-form-btn med-form-btn-primary">
                                <span class="med-form-btn-icon"><i class="fas fa-save"></i></span>
                                <span>Mettre à jour</span>
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </header>

        <div class="med-form-layout">
            <aside class="med-form-card med-form-side">
                <div class="med-form-side-head">
                    <span class="med-form-avatar" aria-hidden="true">{{ strtoupper(substr($medicament->nom_commercial, 0, 2)) }}</span>
                    <div class="med-form-side-copy">
                        <h2 class="med-form-side-name">{{ $medicament->nom_commercial }}</h2>
                        <p class="med-form-side-subtitle">{{ $medicament->code_cip ?: 'Code CIP non renseigné' }}</p>
                    </div>
                </div>

                <div class="med-form-side-badges">
                    <span class="med-form-chip">Type: {{ $typeLabel }}</span>
                    <span class="med-form-chip">Statut: {{ $statusLabel }}</span>
                </div>

                <h2 class="med-form-side-title">Résumé du médicament</h2>
                <ul class="med-form-side-list">
                    <li>
                        <small>Catégorie</small>
                        <strong>{{ $medicament->categorie ?: 'Non renseignée' }}</strong>
                    </li>
                    <li>
                        <small>Laboratoire</small>
                        <strong>{{ $medicament->laboratoire ?: 'Non renseigné' }}</strong>
                    </li>
                    <li>
                        <small>Stock</small>
                        <strong>{{ $medicament->quantite_stock }} unité(s)</strong>
                    </li>
                    <li>
                        <small>Seuil</small>
                        <strong>{{ $medicament->quantite_seuil ?: 0 }}</strong>
                    </li>
                    <li>
                        <small>Prix de vente</small>
                        <strong>{{ number_format((float) $medicament->prix_vente, 2) }} DH</strong>
                    </li>
                    <li>
                        <small>Remboursement</small>
                        <strong>{{ $medicament->remboursable ? 'Activé' : 'Non activé' }}</strong>
                    </li>
                </ul>
            </aside>

            <section class="med-form-card med-form-main">
                <div class="med-form-main-head">
                    <div>
                        <h2 class="med-form-main-title">Formulaire d’édition structuré</h2>
                        <p class="med-form-title-subtitle">Mettez à jour les informations du médicament par grands blocs métiers, avec une hiérarchie plus nette pour la tarification, le stock et les données cliniques.</p>
                    </div>
                    <span class="med-form-badge">{{ $medicament->code_medicament ?: 'Référence' }}</span>
                </div>

                <form action="{{ route('medicaments.update', $medicament) }}" method="POST" id="medicamentEditForm">
                    @csrf
                    @method('PUT')
                    <div class="med-form-body">
                        @include('medicaments.partials.form-fields', ['medicament' => $medicament])
                    </div>

                    <div class="med-form-footer">
                        <a href="{{ route('medicaments.show', $medicament) }}" class="med-form-btn med-form-btn-soft">
                            <span class="med-form-btn-icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Annuler</span>
                        </a>
                        <button type="submit" class="med-form-btn med-form-btn-primary">
                            <span class="med-form-btn-icon"><i class="fas fa-floppy-disk"></i></span>
                            <span>Mettre à jour le médicament</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="med-form-mobile-actions">
        <a href="{{ route('medicaments.show', $medicament) }}" class="med-form-btn med-form-btn-soft">
            <span class="med-form-btn-icon"><i class="fas fa-arrow-left"></i></span>
            <span>Retour</span>
        </a>
        <button type="submit" form="medicamentEditForm" class="med-form-btn med-form-btn-primary">
            <span class="med-form-btn-icon"><i class="fas fa-save"></i></span>
            <span>Mettre à jour</span>
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