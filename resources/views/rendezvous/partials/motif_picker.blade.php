<div class="rdv-motif-grid" id="motifGrid">
    @foreach($motifs as $motif)
        @php
            if (is_array($motif)) {
                $label = $motif['label'] ?? $motif['value'] ?? ($motif['text'] ?? '');
                $icon = $motif['icon'] ?? ($motif['icon_class'] ?? 'fas fa-notes-medical');
            } else {
                $label = (string) $motif;
                $icon = 'fas fa-notes-medical';
            }
            $isSelected = isset($selectedMotif) && $selectedMotif === $label;
        @endphp
        <label class="rdv-motif-card {{ $isSelected ? 'selected' : '' }}" data-motif-card>
            <input
                type="radio"
                name="motif"
                value="{{ $label }}"
                class="rdv-motif-input @error('motif') is-invalid @enderror"
                {{ $isSelected ? 'checked' : '' }}
                required
            >
            <span class="rdv-motif-icon"><i class="{{ $icon }}"></i></span>
            <span class="rdv-motif-text">{{ $label }}</span>
        </label>
    @endforeach
</div>

@error('motif')
    <div class="rdv-field-error">{{ $message }}</div>
@enderror
