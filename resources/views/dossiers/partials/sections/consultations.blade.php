@if($dossier->consultations->count() > 0)
    <div class="timeline-list">
        @foreach($dossier->consultations as $consultation)
            <article class="timeline-item" data-dossier-search-item="consultation {{ $consultation->created_at }} {{ optional($consultation->medecin)->nom }} {{ $consultation->motif }}">
                <div class="timeline-dot" aria-hidden="true"></div>
                <div class="timeline-content">
                    <div class="timeline-head">
                        <strong>{{ $consultation->created_at ? $consultation->created_at->format('d/m/Y H:i') : 'Date non disponible' }}</strong>
                        <span>{{ optional($consultation->medecin)->nom ?? 'Médecin non spécifié' }}</span>
                    </div>
                    <p>{{ \Illuminate\Support\Str::limit($consultation->motif ?? 'Sans motif renseigné', 120) }}</p>
                    <div class="timeline-body-actions">
                        <a href="{{ route('consultations.show', $consultation) }}" class="dossier-btn dossier-btn-soft">
                            <span class="dossier-btn-icon"><i class="fas fa-eye"></i></span>
                            <span>Ouvrir</span>
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-notes-medical"></i>
        <p>Aucune consultation enregistrée pour ce dossier.</p>
    </div>
@endif
