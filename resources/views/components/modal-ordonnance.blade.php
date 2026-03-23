@props([
    'patient',
    'medecins' => collect(),
    'currentMedecin' => null,
    'medicamentCatalogData' => [],
])

@php
    $patientFullName = trim((string) $patient->prenom . ' ' . (string) $patient->nom) ?: 'Patient';
    $todayValue = now()->format('Y-m-d');
    $todayLabel = now()->format('d/m/Y');
    $defaultMedecinId = (string) old('medecin_id', $currentMedecin?->id);
    $quickStoreUrl = route('ordonnances.store.quick');
    $modalPayload = [
        'storeUrl' => $quickStoreUrl,
        'modalName' => 'modal-ordonnance',
        'patientId' => (int) $patient->id,
        'defaultMedecinId' => $defaultMedecinId,
        'todayValue' => $todayValue,
        'prescriptionsCount' => (int) ($patient->ordonnances_count ?? 0),
        'medicamentCatalog' => $medicamentCatalogData,
    ];
@endphp

@once
    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            .ord-quick-shell {
                padding: 1.25rem;
            }

            .ord-quick-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.58);
                backdrop-filter: blur(4px);
            }

            .ord-quick-window {
                position: relative;
                z-index: 2;
                width: min(100%, 1080px);
                margin: 0 auto;
                border-radius: 30px;
                border: 1px solid rgba(203, 213, 225, 0.9);
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 251, 255, 0.98) 100%);
                box-shadow: 0 40px 90px -42px rgba(15, 23, 42, 0.5);
                overflow: hidden;
            }

            .ord-quick-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                padding: 1.4rem 1.5rem 1.1rem;
                border-bottom: 1px solid rgba(226, 232, 240, 0.95);
                background:
                    radial-gradient(circle at top right, rgba(14, 165, 233, 0.16) 0%, rgba(14, 165, 233, 0) 36%),
                    linear-gradient(135deg, #fcfeff 0%, #edf7ff 100%);
            }

            .ord-quick-kicker {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.42rem 0.75rem;
                border-radius: 999px;
                background: rgba(8, 145, 178, 0.12);
                color: #0f5e77;
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .ord-quick-title {
                margin: 0.8rem 0 0;
                color: #0f2741;
                font-size: clamp(1.45rem, 2vw, 2rem);
                font-weight: 900;
                letter-spacing: -0.03em;
            }

            .ord-quick-subtitle {
                margin: 0.5rem 0 0;
                color: #5d738b;
                line-height: 1.65;
                max-width: 70ch;
            }

            .ord-quick-head-aside {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                flex-shrink: 0;
            }

            .ord-quick-date-chip {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                min-height: 44px;
                padding: 0 0.95rem;
                border-radius: 16px;
                border: 1px solid rgba(191, 219, 254, 0.95);
                background: rgba(255, 255, 255, 0.92);
                color: #1e3a5f;
                font-weight: 700;
            }

            .ord-quick-close {
                width: 44px;
                height: 44px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 14px;
                border: 1px solid rgba(203, 213, 225, 0.95);
                background: rgba(255, 255, 255, 0.9);
                color: #334155;
                transition: transform 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            }

            .ord-quick-close:hover {
                transform: translateY(-1px);
                border-color: rgba(8, 145, 178, 0.55);
                color: #0f5e77;
            }

            .ord-quick-form {
                display: grid;
                gap: 1.1rem;
                padding: 1.35rem 1.5rem 1.5rem;
            }

            .ord-quick-top-grid {
                display: grid;
                grid-template-columns: minmax(260px, 0.9fr) minmax(0, 1.1fr);
                gap: 1rem;
            }

            .ord-quick-patient-card,
            .ord-quick-section,
            .ord-quick-footer {
                border: 1px solid rgba(226, 232, 240, 0.95);
                border-radius: 24px;
                background: rgba(255, 255, 255, 0.96);
                box-shadow: 0 18px 34px -28px rgba(15, 23, 42, 0.25);
            }

            .ord-quick-patient-card {
                display: grid;
                gap: 0.85rem;
                padding: 1.1rem 1.15rem;
            }

            .ord-quick-card-label {
                color: #64748b;
                font-size: 0.74rem;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .ord-quick-patient-name {
                margin: 0;
                color: #0f2741;
                font-size: 1.2rem;
                font-weight: 900;
            }

            .ord-quick-patient-meta {
                color: #5d738b;
                line-height: 1.7;
            }

            .ord-quick-field-stack {
                display: grid;
                gap: 1rem;
            }

            .ord-quick-field {
                display: grid;
                gap: 0.55rem;
            }

            .ord-quick-label {
                color: #1e3a5f;
                font-size: 0.8rem;
                font-weight: 800;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .ord-quick-input,
            .ord-quick-select,
            .ord-quick-textarea {
                width: 100%;
                border-radius: 18px;
                border: 1px solid rgba(203, 213, 225, 0.95);
                background: #f8fbff;
                color: #0f2741;
                transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
            }

            .ord-quick-input,
            .ord-quick-select {
                min-height: 52px;
                padding: 0 0.95rem;
            }

            .ord-quick-textarea {
                min-height: 110px;
                padding: 0.9rem 0.95rem;
                resize: vertical;
            }

            .ord-quick-input:focus,
            .ord-quick-select:focus,
            .ord-quick-textarea:focus {
                outline: none;
                border-color: rgba(8, 145, 178, 0.65);
                box-shadow: 0 0 0 4px rgba(34, 211, 238, 0.15);
                transform: translateY(-1px);
            }

            .ord-quick-input.is-invalid,
            .ord-quick-select.is-invalid,
            .ord-quick-textarea.is-invalid {
                border-color: rgba(244, 63, 94, 0.72);
                box-shadow: 0 0 0 4px rgba(251, 113, 133, 0.14);
            }

            .ord-quick-feedback {
                display: grid;
                gap: 0.4rem;
                padding: 0.9rem 1rem;
                border-radius: 18px;
                font-size: 0.95rem;
                line-height: 1.55;
            }

            .ord-quick-feedback.hidden {
                display: none;
            }

            .ord-quick-feedback-error {
                border: 1px solid rgba(251, 191, 202, 0.9);
                background: #fff1f2;
                color: #be123c;
            }

            .ord-quick-feedback-success {
                border: 1px solid rgba(167, 243, 208, 0.9);
                background: #ecfdf5;
                color: #047857;
            }

            .ord-quick-section {
                overflow: hidden;
            }

            .ord-quick-section-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1rem 1.15rem;
                border-bottom: 1px solid rgba(226, 232, 240, 0.95);
                background: #f8fbff;
            }

            .ord-quick-section-title {
                margin: 0;
                color: #0f2741;
                font-size: 1rem;
                font-weight: 900;
            }

            .ord-quick-section-copy {
                margin: 0.25rem 0 0;
                color: #5d738b;
                line-height: 1.6;
            }

            .ord-quick-add {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                min-height: 46px;
                padding: 0 1rem;
                border-radius: 16px;
                border: 1px solid rgba(16, 185, 129, 0.25);
                background: #10b981;
                color: #ffffff;
                font-weight: 800;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .ord-quick-add:hover {
                transform: translateY(-1px);
                box-shadow: 0 18px 26px -22px rgba(16, 185, 129, 0.68);
            }

            .ord-quick-rows {
                display: grid;
                gap: 1rem;
                padding: 1rem 1.15rem 1.15rem;
            }

            .ord-quick-row {
                padding: 1rem;
                border-radius: 22px;
                border: 1px solid rgba(219, 234, 254, 0.95);
                background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            }

            .ord-quick-row-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                margin-bottom: 0.95rem;
            }

            .ord-quick-row-title {
                margin: 0;
                color: #0f2741;
                font-size: 0.98rem;
                font-weight: 900;
            }

            .ord-quick-remove {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                min-height: 40px;
                padding: 0 0.8rem;
                border-radius: 14px;
                border: 1px solid rgba(251, 191, 202, 0.95);
                background: #fff1f2;
                color: #be123c;
                font-weight: 800;
            }

            .ord-quick-row-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.8fr) repeat(3, minmax(140px, 1fr));
                gap: 0.85rem;
            }

            .ord-quick-search-shell {
                position: relative;
            }

            .ord-quick-results {
                position: absolute;
                top: calc(100% + 0.45rem);
                left: 0;
                right: 0;
                z-index: 30;
                display: none;
                padding: 0.45rem;
                border-radius: 18px;
                border: 1px solid rgba(203, 213, 225, 0.95);
                background: #ffffff;
                box-shadow: 0 22px 38px -28px rgba(15, 23, 42, 0.38);
            }

            .ord-quick-results.is-open {
                display: grid;
                gap: 0.35rem;
            }

            .ord-quick-result {
                display: grid;
                gap: 0.15rem;
                width: 100%;
                padding: 0.75rem 0.8rem;
                border: 0;
                border-radius: 14px;
                background: #f8fbff;
                color: #0f2741;
                text-align: left;
                transition: background 0.2s ease, transform 0.2s ease;
            }

            .ord-quick-result:hover,
            .ord-quick-result:focus {
                background: #ecfeff;
                transform: translateY(-1px);
                outline: none;
            }

            .ord-quick-result small,
            .ord-quick-meta {
                color: #64748b;
            }

            .ord-quick-empty-search {
                padding: 0.7rem 0.8rem;
                color: #64748b;
            }

            .ord-quick-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1rem 1.15rem;
            }

            .ord-quick-footer-copy {
                color: #5d738b;
                line-height: 1.65;
            }

            .ord-quick-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 0.7rem;
                flex-wrap: wrap;
            }

            .ord-quick-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.55rem;
                min-height: 48px;
                padding: 0 1rem;
                border-radius: 16px;
                font-weight: 800;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .ord-quick-btn:hover {
                transform: translateY(-1px);
            }

            .ord-quick-btn-muted {
                border: 1px solid rgba(203, 213, 225, 0.95);
                background: #ffffff;
                color: #334155;
            }

            .ord-quick-btn-primary {
                border: 1px solid transparent;
                background: linear-gradient(135deg, #0891b2 0%, #2563eb 100%);
                color: #ffffff;
                box-shadow: 0 20px 34px -24px rgba(37, 99, 235, 0.72);
            }

            .ord-quick-btn[disabled] {
                opacity: 0.65;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }

            @media (max-width: 900px) {
                .ord-quick-top-grid,
                .ord-quick-row-grid,
                .ord-quick-footer {
                    grid-template-columns: 1fr;
                }

                .ord-quick-footer {
                    display: grid;
                }

                .ord-quick-head {
                    padding-inline: 1.1rem;
                }

                .ord-quick-form {
                    padding-inline: 1.1rem;
                }

                .ord-quick-section-head {
                    align-items: flex-start;
                    flex-direction: column;
                }
            }

            @media (max-width: 640px) {
                .ord-quick-shell {
                    padding: 0.8rem;
                }

                .ord-quick-head {
                    flex-direction: column;
                }

                .ord-quick-head-aside,
                .ord-quick-actions {
                    width: 100%;
                }

                .ord-quick-head-aside {
                    justify-content: space-between;
                }

                .ord-quick-actions {
                    justify-content: stretch;
                }

                .ord-quick-actions .ord-quick-btn {
                    width: 100%;
                }
            }

            body.dark-mode .ord-quick-window,
            body.dark-mode .ord-quick-patient-card,
            body.dark-mode .ord-quick-section,
            body.dark-mode .ord-quick-footer,
            body.dark-mode .ord-quick-row,
            body.dark-mode .ord-quick-results {
                border-color: rgba(51, 65, 85, 0.92);
                background: #0f172a;
                box-shadow: 0 24px 46px -34px rgba(2, 6, 23, 0.75);
            }

            body.dark-mode .ord-quick-head {
                border-color: rgba(51, 65, 85, 0.9);
                background:
                    radial-gradient(circle at top right, rgba(8, 145, 178, 0.18) 0%, rgba(8, 145, 178, 0) 36%),
                    linear-gradient(135deg, #132238 0%, #0f172a 100%);
            }

            body.dark-mode .ord-quick-title,
            body.dark-mode .ord-quick-patient-name,
            body.dark-mode .ord-quick-section-title,
            body.dark-mode .ord-quick-row-title,
            body.dark-mode .ord-quick-result {
                color: #e2e8f0;
            }

            body.dark-mode .ord-quick-subtitle,
            body.dark-mode .ord-quick-patient-meta,
            body.dark-mode .ord-quick-meta,
            body.dark-mode .ord-quick-footer-copy,
            body.dark-mode .ord-quick-section-copy,
            body.dark-mode .ord-quick-result small,
            body.dark-mode .ord-quick-card-label,
            body.dark-mode .ord-quick-label,
            body.dark-mode .ord-quick-empty-search {
                color: #94a3b8;
            }

            body.dark-mode .ord-quick-input,
            body.dark-mode .ord-quick-select,
            body.dark-mode .ord-quick-textarea,
            body.dark-mode .ord-quick-close,
            body.dark-mode .ord-quick-date-chip,
            body.dark-mode .ord-quick-btn-muted {
                border-color: rgba(51, 65, 85, 0.95);
                background: #111c30;
                color: #e2e8f0;
            }

            body.dark-mode .ord-quick-section-head {
                border-color: rgba(51, 65, 85, 0.95);
                background: #111c30;
            }

            body.dark-mode .ord-quick-result {
                background: #111c30;
            }

            body.dark-mode .ord-quick-remove {
                border-color: rgba(136, 19, 55, 0.8);
                background: rgba(136, 19, 55, 0.18);
                color: #fecdd3;
            }
        </style>
    @endpush
@endonce

<div
    id="modal-ordonnance"
    x-data="{ open: false }"
    x-cloak
    x-on:open-modal.window="if ($event.detail === 'modal-ordonnance') { open = true; $nextTick(() => { document.getElementById('ordonnanceQuickMedecin')?.focus(); $el.dispatchEvent(new CustomEvent('ordonnance-quick:opened')); }); }"
    x-on:close-modal.window="if ($event.detail === 'modal-ordonnance') { open = false; $el.dispatchEvent(new CustomEvent('ordonnance-quick:closed')); }"
    x-on:keydown.escape.window="if (open) { open = false; $el.dispatchEvent(new CustomEvent('ordonnance-quick:closed')); }"
    x-show="open"
    class="fixed inset-0 z-50 overflow-y-auto ord-quick-shell"
    style="display: none;"
    aria-modal="true"
    role="dialog"
    aria-labelledby="modal-ordonnance-title"
>
    <div class="ord-quick-backdrop" x-show="open" x-transition.opacity x-on:click="open = false; $el.closest('#modal-ordonnance').dispatchEvent(new CustomEvent('ordonnance-quick:closed'))"></div>

    <div
        class="ord-quick-window"
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
        x-on:click.stop
    >
        <header class="ord-quick-head">
            <div>
                <span class="ord-quick-kicker"><i class="fas fa-prescription"></i> Ordonnance rapide</span>
                <h2 id="modal-ordonnance-title" class="ord-quick-title">{{ $patientFullName }}</h2>
                <p class="ord-quick-subtitle">
                    Creez une ordonnance depuis la fiche patient, sans quitter le dossier. Le formulaire reste compatible avec le flux standard des ordonnances.
                </p>
            </div>

            <div class="ord-quick-head-aside">
                <span class="ord-quick-date-chip"><i class="fas fa-calendar-day"></i> {{ $todayLabel }}</span>
                <button type="button" class="ord-quick-close" x-on:click="open = false; $el.closest('#modal-ordonnance').dispatchEvent(new CustomEvent('ordonnance-quick:closed'))" aria-label="Fermer la modale ordonnance">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </header>

        <form id="patientOrdonnanceModalForm" class="ord-quick-form" action="{{ $quickStoreUrl }}" method="POST">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            <input type="hidden" name="date_prescription" value="{{ $todayValue }}">

            <div id="ordonnanceQuickErrors" class="ord-quick-feedback ord-quick-feedback-error hidden" role="alert" aria-live="assertive"></div>
            <div id="ordonnanceQuickSuccess" class="ord-quick-feedback ord-quick-feedback-success hidden" role="status" aria-live="polite"></div>

            <div class="ord-quick-top-grid">
                <article class="ord-quick-patient-card">
                    <span class="ord-quick-card-label">Patient concerne</span>
                    <h3 class="ord-quick-patient-name">{{ $patientFullName }}</h3>
                    <div class="ord-quick-patient-meta">
                        <div><strong>Dossier :</strong> {{ $patient->numero_dossier ?: 'Non renseigne' }}</div>
                        <div><strong>Telephone :</strong> {{ $patient->telephone ?: 'Non renseigne' }}</div>
                        <div><strong>Allergies :</strong> {{ $patient->allergies ?: 'Aucune allergie documentee' }}</div>
                    </div>
                </article>

                <div class="ord-quick-field-stack">
                    <div class="ord-quick-field">
                        <label for="ordonnanceQuickMedecin" class="ord-quick-label">Medecin prescripteur</label>
                        <select id="ordonnanceQuickMedecin" name="medecin_id" class="ord-quick-select" required>
                            <option value="">Selectionner un medecin</option>
                            @foreach(collect($medecins) as $medecin)
                                <option value="{{ $medecin->id }}" @selected($defaultMedecinId === (string) $medecin->id)>
                                    {{ trim(($medecin->civilite ?? 'Dr.') . ' ' . $medecin->prenom . ' ' . $medecin->nom) }}@if($medecin->specialite) - {{ $medecin->specialite }}@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ord-quick-field">
                        <label for="ordonnanceQuickInstructions" class="ord-quick-label">Notes et instructions generales</label>
                        <textarea id="ordonnanceQuickInstructions" name="instructions" class="ord-quick-textarea" placeholder="Conseils, recommandations, point d attention pour le patient..."></textarea>
                    </div>
                </div>
            </div>

            <section class="ord-quick-section">
                <div class="ord-quick-section-head">
                    <div>
                        <h3 class="ord-quick-section-title">Traitement prescrit</h3>
                        <p class="ord-quick-section-copy">Ajoutez les lignes medicaments avec recherche, dosage, frequence et duree.</p>
                    </div>

                    <button type="button" id="ordonnanceQuickAddRow" class="ord-quick-add">
                        <i class="fas fa-plus"></i> Ajouter un medicament
                    </button>
                </div>

                <div id="ordonnanceQuickRows" class="ord-quick-rows"></div>
            </section>

            <footer class="ord-quick-footer">
                <div class="ord-quick-footer-copy">
                    <strong>Validation :</strong> au moins une ligne medicament est requise. La fiche patient reste ouverte et le compteur d ordonnances est mis a jour des l enregistrement.
                </div>

                <div class="ord-quick-actions">
                    <button type="button" class="ord-quick-btn ord-quick-btn-muted" x-on:click="open = false; $el.closest('#modal-ordonnance').dispatchEvent(new CustomEvent('ordonnance-quick:closed'))">
                        <i class="fas fa-ban"></i> Annuler
                    </button>
                    <button type="submit" id="ordonnanceQuickSubmit" class="ord-quick-btn ord-quick-btn-primary">
                        <i class="fas fa-save"></i> <span id="ordonnanceQuickSubmitLabel">Enregistrer</span>
                    </button>
                </div>
            </footer>
        </form>
    </div>

    <template id="ordonnanceQuickRowTemplate">
        <article class="ord-quick-row" data-row-index="__INDEX__">
            <div class="ord-quick-row-head">
                <h4 class="ord-quick-row-title">Medicament <span data-role="row-number">#__NUMBER__</span></h4>
                <button type="button" class="ord-quick-remove" data-remove-row>
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>

            <div class="ord-quick-row-grid">
                <div class="ord-quick-field">
                    <label class="ord-quick-label" for="ordonnanceQuickMedicament___INDEX__">Medicament</label>
                    <div class="ord-quick-search-shell">
                        <input type="hidden" name="medicaments[__INDEX__][medicament_id]" data-role="medication-id">
                        <input type="hidden" name="medicaments[__INDEX__][posologie]" data-role="posologie">
                        <input type="hidden" name="medicaments[__INDEX__][quantite]" data-role="dosage-hidden">
                        <input type="hidden" name="medicaments[__INDEX__][instructions]" data-role="frequency-hidden">
                        <input
                            id="ordonnanceQuickMedicament___INDEX__"
                            type="text"
                            name="medicaments[__INDEX__][medicament_label]"
                            class="ord-quick-input"
                            data-role="medication-label"
                            data-error-proxy="medicaments[__INDEX__][medicament_id]"
                            placeholder="Nom commercial, DCI, presentation..."
                            autocomplete="off"
                            required
                        >
                        <div class="ord-quick-results" data-role="medication-results"></div>
                    </div>
                    <div class="ord-quick-meta" data-role="medication-meta">Saisissez un libelle libre ou selectionnez un medicament du catalogue.</div>
                </div>

                <div class="ord-quick-field">
                    <label class="ord-quick-label" for="ordonnanceQuickDosage___INDEX__">Dosage</label>
                    <input id="ordonnanceQuickDosage___INDEX__" type="text" class="ord-quick-input" data-role="dosage" data-error-proxy="medicaments[__INDEX__][posologie]" placeholder="Ex : 500mg" required>
                </div>

                <div class="ord-quick-field">
                    <label class="ord-quick-label" for="ordonnanceQuickFrequency___INDEX__">Frequence</label>
                    <input id="ordonnanceQuickFrequency___INDEX__" type="text" class="ord-quick-input" data-role="frequency" data-error-proxy="medicaments[__INDEX__][posologie]" placeholder="Ex : 3x/jour" required>
                </div>

                <div class="ord-quick-field">
                    <label class="ord-quick-label" for="ordonnanceQuickDuration___INDEX__">Duree</label>
                    <input
                        id="ordonnanceQuickDuration___INDEX__"
                        type="text"
                        name="medicaments[__INDEX__][duree]"
                        class="ord-quick-input"
                        data-role="duration"
                        placeholder="Ex : 7 jours"
                        required
                    >
                </div>
            </div>
        </article>
    </template>

    <script type="application/json" id="patientOrdonnanceModalPayload">
        {{ \Illuminate\Support\Js::from($modalPayload) }}
    </script>
</div>
