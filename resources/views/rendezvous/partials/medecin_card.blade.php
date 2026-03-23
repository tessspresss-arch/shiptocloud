<div class="rdv-doctor-grid" id="doctorGrid">
    @foreach($medecins as $medecin)
        @php
            $fullName = trim('Dr. ' . ($medecin->prenom ?? '') . ' ' . ($medecin->nom ?? ''));
            $initials = strtoupper(substr((string) ($medecin->prenom ?? 'M'), 0, 1) . substr((string) ($medecin->nom ?? 'D'), 0, 1));
            $isSelected = (int) $selectedMedecinId === (int) $medecin->id;
        @endphp
        <label class="rdv-doctor-card {{ $isSelected ? 'selected' : '' }}" data-doctor-card>
            <input
                type="radio"
                class="rdv-doctor-input @error('medecin_id') is-invalid @enderror"
                name="medecin_id"
                value="{{ $medecin->id }}"
                data-doctor-name="{{ $fullName }}"
                {{ $isSelected ? 'checked' : '' }}
                required
            >
            <span class="rdv-doctor-avatar">{{ $initials }}</span>
            <span class="rdv-doctor-body">
                <strong>{{ $fullName }}</strong>
                <small>{{ $medecin->specialite ?? 'Médecine générale' }}</small>
            </span>
            <span class="rdv-doctor-state"><i class="fas fa-circle"></i> Disponible</span>
        </label>
    @endforeach
</div>

@error('medecin_id')
    <div class="rdv-field-error">{{ $message }}</div>
@enderror
