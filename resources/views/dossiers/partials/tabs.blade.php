@php
    $patient = $dossier->patient;
    $fullName = trim(($patient->prenom ?? '') . ' ' . ($patient->nom ?? '')) ?: 'Patient';
@endphp

<div class="dossier-card dossier-main-card">
    <div class="dossier-main-head">
        <div>
            <span class="dossier-main-kicker">Vue clinique</span>
            <h2 class="dossier-main-title">{{ $fullName }}</h2>
            <p class="dossier-main-copy">{{ $dossier->numero_dossier }} - navigation entre les principales composantes du dossier.</p>
        </div>
        <span class="dossier-main-status"><i class="fas fa-shield-heart"></i> Dossier {{ ucfirst($dossier->statut ?? 'actif') }}</span>
    </div>

    <div class="tabs-scroll-wrap" role="tablist" aria-label="Sections du dossier medical">
        <button class="dossier-tab-link active" data-bs-toggle="tab" data-bs-target="#tab-antecedents" type="button" role="tab" aria-controls="tab-antecedents" aria-selected="true">
            <i class="fas fa-notes-medical me-2"></i>Antecedents
        </button>
        <button class="dossier-tab-link" data-bs-toggle="tab" data-bs-target="#tab-consultations" type="button" role="tab" aria-controls="tab-consultations" aria-selected="false">
            <i class="fas fa-stethoscope me-2"></i>Consultations
        </button>
        <button class="dossier-tab-link" data-bs-toggle="tab" data-bs-target="#tab-ordonnances" type="button" role="tab" aria-controls="tab-ordonnances" aria-selected="false">
            <i class="fas fa-prescription-bottle-medical me-2"></i>Ordonnances
        </button>
        <button class="dossier-tab-link" data-bs-toggle="tab" data-bs-target="#tab-vaccinations" type="button" role="tab" aria-controls="tab-vaccinations" aria-selected="false">
            <i class="fas fa-syringe me-2"></i>Vaccinations
        </button>
    </div>

    <div class="tab-content dossier-tab-content">
        <section class="tab-pane fade show active" id="tab-antecedents" role="tabpanel">
            @include('dossiers.partials.sections.antecedents', ['dossier' => $dossier])
        </section>

        <section class="tab-pane fade" id="tab-consultations" role="tabpanel">
            @include('dossiers.partials.sections.consultations', ['dossier' => $dossier])
        </section>

        <section class="tab-pane fade" id="tab-ordonnances" role="tabpanel">
            @include('dossiers.partials.sections.ordonnances', ['dossier' => $dossier])
        </section>

        <section class="tab-pane fade" id="tab-vaccinations" role="tabpanel">
            @include('dossiers.partials.sections.vaccinations', ['dossier' => $dossier])
        </section>
    </div>
</div>
