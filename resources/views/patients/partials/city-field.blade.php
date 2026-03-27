@props([
    'selectedCity' => '',
    'label' => 'Ville',
    'selectId' => 'patientVilleSelection',
    'otherId' => 'patientVilleAutre',
    'selectClass' => 'form-select',
    'otherInputClass' => 'form-control',
    'feedbackClass' => 'invalid-feedback d-block mt-2',
    'helperClass' => 'form-text text-muted mt-2',
    'otherLabel' => 'Autre ville',
    'otherPlaceholder' => 'Saisir la ville',
    'otherHelperText' => "Utilisez cette option uniquement si la ville n'est pas dans la liste.",
])

@php
    $cityOptions = collect(config('patients.moroccan_cities', []))
        ->map(function ($city) {
            if (is_array($city)) {
                return [
                    'value' => (string) ($city['value'] ?? ''),
                    'label' => (string) ($city['label'] ?? ($city['value'] ?? '')),
                ];
            }

            return [
                'value' => (string) $city,
                'label' => (string) $city,
            ];
        })
        ->filter(fn (array $city) => $city['value'] !== '')
        ->values();

    $initialCity = trim((string) $selectedCity);
    $postedCitySelection = old('ville_selection');
    $postedCityOther = old('ville_autre');
    $hasPostedCityState = $postedCitySelection !== null || $postedCityOther !== null;

    $effectiveCity = $hasPostedCityState
        ? trim((string) ($postedCitySelection === 'Autre' ? $postedCityOther : $postedCitySelection))
        : $initialCity;

    $matchingCity = $cityOptions->first(function (array $city) use ($effectiveCity) {
        return $effectiveCity !== ''
            && ($effectiveCity === $city['value'] || $effectiveCity === $city['label']);
    });

    $selectionValue = $effectiveCity === ''
        ? ''
        : ($matchingCity['value'] ?? 'Autre');

    $otherValue = $matchingCity ? '' : $effectiveCity;

    $resolvedSelectClass = trim($selectClass . (($errors->has('ville') || $errors->has('ville_selection')) ? ' is-invalid' : ''));
    $resolvedOtherInputClass = trim($otherInputClass . ($errors->has('ville_autre') ? ' is-invalid' : ''));
@endphp

<div data-city-field>
    <label for="{{ $selectId }}" class="form-label">{{ $label }}</label>
    <select
        id="{{ $selectId }}"
        name="ville_selection"
        class="{{ $resolvedSelectClass }}"
        data-city-select
        aria-label="Choisir une ville"
    >
        <option value="">-- Sélectionner une ville --</option>
        @foreach($cityOptions as $city)
            <option value="{{ $city['value'] }}" @selected($selectionValue === $city['value'])>{{ $city['label'] }}</option>
        @endforeach
        <option value="Autre" @selected($selectionValue === 'Autre')>Autre</option>
    </select>

    @if($errors->has('ville_selection'))
        <div class="{{ $feedbackClass }}"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('ville_selection') }}</div>
    @elseif($errors->has('ville'))
        <div class="{{ $feedbackClass }}"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('ville') }}</div>
    @endif

    <div class="mt-3 {{ $selectionValue === 'Autre' ? '' : 'd-none' }}" data-city-other-wrap>
        <label for="{{ $otherId }}" class="form-label">{{ $otherLabel }}</label>
        <input
            type="text"
            id="{{ $otherId }}"
            name="ville_autre"
            value="{{ $otherValue }}"
            class="{{ $resolvedOtherInputClass }}"
            data-city-other-input
            placeholder="{{ $otherPlaceholder }}"
            {{ $selectionValue === 'Autre' ? 'required' : '' }}
        >
        @if($otherHelperText !== '')
            <div class="{{ $helperClass }}">{{ $otherHelperText }}</div>
        @endif
        @error('ville_autre')
            <div class="{{ $feedbackClass }}"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
        @enderror
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-city-field]').forEach(function (field) {
                    if (field.dataset.cityFieldBound === 'true') {
                        return;
                    }

                    const select = field.querySelector('[data-city-select]');
                    const otherWrap = field.querySelector('[data-city-other-wrap]');
                    const otherInput = field.querySelector('[data-city-other-input]');

                    if (!select || !otherWrap || !otherInput) {
                        return;
                    }

                    const syncCityField = function () {
                        const isOther = select.value === 'Autre';

                        otherWrap.classList.toggle('d-none', !isOther);
                        otherInput.required = isOther;
                        otherInput.disabled = !isOther;

                        if (!isOther) {
                            otherInput.value = '';
                        }
                    };

                    select.addEventListener('change', syncCityField);
                    syncCityField();

                    field.dataset.cityFieldBound = 'true';
                });
            });
        </script>
    @endpush
@endonce
