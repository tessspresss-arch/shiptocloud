<article class="section-card section-card-full" data-dossier-search-item="vaccinations {{ $dossier->vaccinations ?? '' }}">
    <div class="section-card-head">
        <div class="d-flex align-items-center gap-2">
            <span class="section-card-icon"><i class="fas fa-syringe"></i></span>
            <h3>Carnet de vaccination</h3>
        </div>
        <span class="section-card-note">Prévention</span>
    </div>
    <p>{{ $dossier->vaccinations ?? 'Aucune vaccination enregistrée.' }}</p>
</article>
