@php
    $currentPatient = $selectedPatientModel ?? null;
@endphp

<div class="rdv-field-block">
    <label class="rdv-label" for="patientSelect">Sélection du patient</label>
    <input type="search" id="patientSearch" class="rdv-input" placeholder="Rechercher par nom, téléphone ou CIN..." autocomplete="off">

    <select id="patientSelect" name="patient_id" class="rdv-select @error('patient_id') is-invalid @enderror" required>
        <option value="">Choisir un patient...</option>
        @foreach($patients as $patient)
            @php
                $age = optional($patient->date_naissance)->age;
                $fullName = trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? ''));
            @endphp
            <option
                value="{{ $patient->id }}"
                data-full-name="{{ $fullName }}"
                data-phone="{{ $patient->telephone ?? '' }}"
                data-cin="{{ $patient->cin ?? '' }}"
                data-age="{{ $age ?? '' }}"
                {{ (int) $patient->id === (int) $selectedPatientId ? 'selected' : '' }}
            >
                {{ $fullName }}{{ $patient->telephone ? ' - ' . $patient->telephone : '' }}
            </option>
        @endforeach
    </select>

    @error('patient_id')
        <div class="rdv-field-error">{{ $message }}</div>
    @enderror

    <a href="{{ route('patients.create') }}" target="_blank" class="rdv-link-btn">
        <i class="fas fa-user-plus"></i>
        Créer un nouveau patient
    </a>

    <div class="rdv-patient-mini {{ $currentPatient ? '' : 'is-hidden' }}" id="patientMiniCard">
        <h4>Patient sélectionné</h4>
        <div class="rdv-mini-grid">
            <div>
                <span class="k">Nom</span>
                <span class="v" data-patient-field="name">{{ $currentPatient ? trim(($currentPatient->prenom ?? '') . ' ' . ($currentPatient->nom ?? '')) : '-' }}</span>
            </div>
            <div>
                <span class="k">Téléphone</span>
                <span class="v" data-patient-field="phone">{{ $currentPatient->telephone ?? '-' }}</span>
            </div>
            <div>
                <span class="k">CIN</span>
                <span class="v" data-patient-field="cin">{{ $currentPatient->cin ?? '-' }}</span>
            </div>
            <div>
                <span class="k">Âge</span>
                <span class="v" data-patient-field="age">{{ optional($currentPatient?->date_naissance)->age ? optional($currentPatient->date_naissance)->age . ' ans' : '-' }}</span>
            </div>
        </div>
    </div>
</div>
