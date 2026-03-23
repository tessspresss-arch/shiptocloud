@php
    $patient = $dossier->patient;
    $fullName = trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? '')) ?: 'Patient';
    $initials = collect(preg_split('/\s+/', $fullName) ?: [])
        ->filter()
        ->take(2)
        ->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))
        ->implode('');

    if ($initials === '') {
        $initials = 'P';
    }

    $age = $patient?->date_naissance ? \Illuminate\Support\Carbon::parse($patient->date_naissance)->age : null;
@endphp

<div class="dossier-card patient-summary-card">
    <div class="patient-summary-head">
        <span class="summary-avatar" aria-hidden="true">{{ $initials }}</span>
        <div class="summary-head-copy">
            <h2 class="summary-name">{{ $fullName }}</h2>
            <p class="summary-sub">{{ $dossier->numero_dossier }}</p>
        </div>
    </div>

    <div class="summary-badges">
        <span class="summary-chip">Statut: {{ ucfirst($dossier->statut ?? 'actif') }}</span>
        @if($patient?->groupe_sanguin)
            <span class="summary-chip summary-chip-strong">Groupe {{ $patient->groupe_sanguin }}</span>
        @endif
        @if($age !== null)
            <span class="summary-chip">{{ $age }} ans</span>
        @endif
    </div>

    <dl class="summary-meta">
        <div data-dossier-search-item="date de naissance {{ $patient?->date_naissance }} {{ $patient?->date_naissance ? \Illuminate\Support\Carbon::parse($patient->date_naissance)->format('d/m/Y') : 'non renseignee' }}">
            <dt>Date de naissance</dt>
            <dd>{{ $patient?->date_naissance ? \Illuminate\Support\Carbon::parse($patient->date_naissance)->format('d/m/Y') : 'Non renseignée' }}</dd>
        </div>
        <div data-dossier-search-item="telephone {{ $patient?->telephone ?: 'non renseigne' }}">
            <dt>Téléphone</dt>
            <dd>{{ $patient?->telephone ?: 'Non renseigné' }}</dd>
        </div>
        <div data-dossier-search-item="assurance {{ $patient?->assurance ?: 'non renseignee' }}">
            <dt>Assurance</dt>
            <dd>{{ $patient?->assurance ?: 'Non renseignée' }}</dd>
        </div>
        <div data-dossier-search-item="ouverture dossier {{ $dossier->date_ouverture }} {{ $dossier->created_at }}">
            <dt>Ouverture dossier</dt>
            <dd>{{ $dossier->date_ouverture ? \Illuminate\Support\Carbon::parse($dossier->date_ouverture)->format('d/m/Y') : ($dossier->created_at ? $dossier->created_at->format('d/m/Y') : 'Non renseignée') }}</dd>
        </div>
    </dl>

    <div class="summary-quick-actions">
        <a href="{{ route('patients.show', $patient) }}" class="dossier-btn dossier-btn-soft dossier-btn-block">
            <span class="dossier-btn-icon"><i class="fas fa-user"></i></span>
            <span>Fiche patient</span>
        </a>
        <a href="{{ route('consultations.index') }}" class="dossier-btn dossier-btn-primary dossier-btn-block">
            <span class="dossier-btn-icon"><i class="fas fa-stethoscope"></i></span>
            <span>Consultations</span>
        </a>
    </div>

    <nav class="summary-nav" aria-label="Navigation interne du dossier">
        <a href="#tab-antecedents" class="summary-nav-link" data-bs-toggle="tab" data-bs-target="#tab-antecedents" role="tab">
            <span><i class="fas fa-notes-medical"></i> Antécédents</span>
            <i class="fas fa-arrow-right"></i>
        </a>
        <a href="#tab-consultations" class="summary-nav-link" data-bs-toggle="tab" data-bs-target="#tab-consultations" role="tab">
            <span><i class="fas fa-stethoscope"></i> Consultations</span>
            <i class="fas fa-arrow-right"></i>
        </a>
        <a href="#tab-ordonnances" class="summary-nav-link" data-bs-toggle="tab" data-bs-target="#tab-ordonnances" role="tab">
            <span><i class="fas fa-prescription-bottle-medical"></i> Ordonnances</span>
            <i class="fas fa-arrow-right"></i>
        </a>
        <a href="#tab-vaccinations" class="summary-nav-link" data-bs-toggle="tab" data-bs-target="#tab-vaccinations" role="tab">
            <span><i class="fas fa-syringe"></i> Vaccinations</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </nav>
</div>
