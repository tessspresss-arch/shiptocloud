<div class="section-grid-two">
    <article class="section-card" data-dossier-search-item="antécédents médicaux {{ $dossier->antecedents ?? $dossier->diagnostic ?? '' }}">
        <div class="section-card-head">
            <div class="d-flex align-items-center gap-2">
                <span class="section-card-icon"><i class="fas fa-notes-medical"></i></span>
                <h3>Antécédents médicaux</h3>
            </div>
            <span class="section-card-note">Clinique</span>
        </div>
        <p>{{ $dossier->antecedents ?? $dossier->diagnostic ?? 'Aucun antécédent enregistré.' }}</p>
    </article>

    <article class="section-card" data-dossier-search-item="allergies {{ $dossier->allergies ?? '' }}">
        <div class="section-card-head">
            <div class="d-flex align-items-center gap-2">
                <span class="section-card-icon"><i class="fas fa-triangle-exclamation"></i></span>
                <h3>Allergies</h3>
            </div>
            <span class="section-card-note">Vigilance</span>
        </div>
        <p>{{ $dossier->allergies ?? 'Aucune allergie déclarée.' }}</p>
    </article>
</div>

<article class="section-card section-card-full mt-3" data-dossier-search-item="traitements courants {{ $dossier->traitements_courants ?? $dossier->traitement ?? '' }}">
    <div class="section-card-head">
        <div class="d-flex align-items-center gap-2">
            <span class="section-card-icon"><i class="fas fa-capsules"></i></span>
            <h3>Traitements courants</h3>
        </div>
        <span class="section-card-note">Suivi</span>
    </div>
    <p>{{ $dossier->traitements_courants ?? $dossier->traitement ?? 'Aucun traitement en cours.' }}</p>
</article>

<div class="section-actions mt-3">
    <a href="{{ route('dossiers.edit', $dossier) }}" class="dossier-btn dossier-btn-primary">
        <span class="dossier-btn-icon"><i class="fas fa-pen"></i></span>
        <span>Ajouter / Modifier</span>
    </a>
</div>
