<div class="section-actions mb-3">
    <a href="{{ route('ordonnances.create') }}?patient_id={{ $dossier->patient_id }}" class="dossier-btn dossier-btn-primary">
        <span class="dossier-btn-icon"><i class="fas fa-file-prescription"></i></span>
        <span>Créer ordonnance</span>
    </a>
</div>

@if(($dossier->ordonnances ?? collect())->count() > 0)
    <div class="responsive-table">
        <table class="table dossier-table mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Médecin</th>
                    <th>Résumé</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dossier->ordonnances as $ordonnance)
                    <tr data-dossier-search-item="ordonnance {{ $ordonnance->date_prescription }} {{ optional($ordonnance->medecin)->nom }} {{ ($ordonnance->ligneOrdonnances ?? collect())->count() }} medicaments">
                        <td>{{ $ordonnance->date_prescription ? \Illuminate\Support\Carbon::parse($ordonnance->date_prescription)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ optional($ordonnance->medecin)->nom ?? 'Non spécifié' }}</td>
                        <td>{{ ($ordonnance->ligneOrdonnances ?? collect())->count() }} médicament(s)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-prescription-bottle"></i>
        <p>Aucune ordonnance enregistrée pour ce dossier.</p>
    </div>
@endif
