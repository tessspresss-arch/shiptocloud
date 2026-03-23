@php
    $isTvMode = ($displayMode ?? 'default') === 'tv';
    $baseFilterParams = array_filter([
        'date' => optional($selectedDate ?? null)?->format('Y-m-d'),
        'medecin_id' => $selectedMedecinId ?? 'all',
        'status' => $selectedStatus ?? 'all',
        'motif' => $selectedMotif ?? '',
        'search' => $searchTerm ?? '',
    ], static fn ($value) => $value !== null && $value !== '');

    $tvModeUrl = route('agenda.waiting_room', array_merge($baseFilterParams, ['display' => 'tv']));
    $defaultModeUrl = route('agenda.waiting_room', $baseFilterParams);
@endphp

<div
    id="waiting-room-app"
    class="waiting-room-shell {{ $isTvMode ? 'is-tv-mode' : '' }}"
    data-endpoint="{{ route('agenda.waiting_room.data') }}"
    data-status-endpoint-base="{{ url('/rendezvous') }}"
    data-rendezvous-base="{{ url('/rendezvous') }}"
    data-patients-base="{{ url('/patients') }}"
    data-sms-create-url="{{ route('sms.create') }}"
    data-consultations-create-url="{{ route('consultations.create') }}"
    data-ordonnances-create-url="{{ route('ordonnances.create') }}"
    data-documents-url="{{ route('documents.upload') }}"
    data-live-interval-ms="{{ $isTvMode ? '4000' : '5000' }}"
    data-standard-consultation-minutes="30"
    data-tv-mode="{{ $isTvMode ? '1' : '0' }}"
>
    <section class="wr-hero-card wr-hero-card-actions-only" aria-label="Actions salle d'attente">
        <div class="wr-hero-actions">
            @if($isTvMode)
                <a href="{{ $defaultModeUrl }}" class="wr-action-btn wr-action-btn-soft">
                    <i class="fas fa-arrow-left"></i>
                    Retour mode gestion
                </a>
            @else
                <a href="{{ $tvModeUrl }}" class="wr-action-btn wr-action-btn-soft" target="_blank" rel="noopener">
                    <i class="fas fa-tv"></i>
                    Mode ecran salle
                </a>
            @endif
            <button type="button" class="wr-action-btn wr-action-btn-primary" id="wr-refresh">
                <i class="fas fa-rotate"></i>
                Actualiser
            </button>
        </div>
    </section>

    <section class="wr-filter-card" aria-label="Filtres et actions">
        <div class="wr-filter-head">
            <div class="wr-filter-head-copy">
                <h2>Filtres de supervision</h2>
                <p>Affinez l'affichage par praticien, date, statut, motif ou patient.</p>
            </div>
        </div>

        <div class="wr-filter-grid">
            <label class="wr-control">
                <span>Praticien</span>
                <select id="wr-medecin" aria-label="Filtrer par praticien">
                    <option value="all">Tous les praticiens</option>
                    @foreach($medecins as $m)
                        <option value="{{ $m->id }}" {{ (string)($selectedMedecinId ?? '') === (string)$m->id ? 'selected' : '' }}>
                            Dr. {{ trim(($m->prenom ? $m->prenom . ' ' : '') . $m->nom) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="wr-control">
                <span>Date</span>
                <input type="date" id="wr-date" value="{{ optional($selectedDate ?? null)?->format('Y-m-d') }}" aria-label="Filtrer par date">
            </label>

            <label class="wr-control">
                <span>Statut</span>
                <select id="wr-status" aria-label="Filtrer par statut">
                    <option value="all" {{ ($selectedStatus ?? 'all') === 'all' ? 'selected' : '' }}>Tous les statuts</option>
                    <option value="a_venir" {{ ($selectedStatus ?? '') === 'a_venir' ? 'selected' : '' }}>{{ __('messages.waiting_room.patients_coming') }}</option>
                    <option value="en_attente" {{ ($selectedStatus ?? '') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="en_soins" {{ ($selectedStatus ?? '') === 'en_soins' ? 'selected' : '' }}>En consultation</option>
                    <option value="vu" {{ ($selectedStatus ?? '') === 'vu' ? 'selected' : '' }}>Consultation terminée</option>
                    <option value="absent" {{ ($selectedStatus ?? '') === 'absent' ? 'selected' : '' }}>Absents</option>
                </select>
            </label>

            <label class="wr-control">
                <span>Motif</span>
                <input type="text" id="wr-motif" value="{{ $selectedMotif ?? '' }}" placeholder="{{ __('messages.waiting_room.example_motif') }}" aria-label="Filtrer par motif">
            </label>

            <label class="wr-control">
                <span>Recherche patient</span>
                <input type="text" id="wr-search" value="{{ $searchTerm ?? '' }}" placeholder="{{ __('messages.waiting_room.search_patient_placeholder') }}" aria-label="Recherche patient">
            </label>

            <button type="button" class="wr-action-btn wr-action-btn-soft wr-reset-btn" id="wr-clear-filters">
                <i class="fas fa-eraser"></i>
                {{ __('messages.common.reset') }}
            </button>
        </div>

        <p class="wr-live-text">
            <span class="wr-live-dot"></span>
            {{ __('messages.waiting_room.adaptive_refresh') }} <strong>5 secondes</strong> {{ __('messages.waiting_room.in_active_window') }}
            <span id="wr-last-sync" class="wr-last-sync">{{ __('messages.waiting_room.sync') }}</span>
        </p>
    </section>

    <section class="wr-board" role="list" aria-label="Colonnes de suivi patients">
        <article class="wr-column wr-col-avenir" data-status="a_venir" role="listitem">
            <header class="wr-column-header">
                <h3><i class="fas fa-calendar-plus"></i> {{ __('messages.waiting_room.patients_coming') }}</h3>
                <span class="wr-column-count">0</span>
            </header>
            <div class="wr-list" data-status="a_venir" aria-live="polite"></div>
        </article>

        <article class="wr-column wr-col-attente" data-status="en_attente" role="listitem">
            <header class="wr-column-header">
                <h3><i class="fas fa-bell-concierge"></i> Salle d'attente</h3>
                <span class="wr-column-count">0</span>
            </header>
            <div class="wr-list" data-status="en_attente" aria-live="polite"></div>
        </article>

        <article class="wr-column wr-col-soins" data-status="en_soins" role="listitem">
            <header class="wr-column-header">
                <h3><i class="fas fa-user-doctor"></i> En consultation</h3>
                <span class="wr-column-count">0</span>
            </header>
            <div class="wr-list" data-status="en_soins" aria-live="polite"></div>
        </article>

        <article class="wr-column wr-col-vu" data-status="vu" role="listitem">
            <header class="wr-column-header">
                <h3><i class="fas fa-circle-check"></i> Patients vus</h3>
                <span class="wr-column-count">0</span>
            </header>
            <div class="wr-list" data-status="vu" aria-live="polite"></div>
        </article>

        <article class="wr-column wr-col-absent" data-status="absent" role="listitem">
            <header class="wr-column-header">
                <h3><i class="fas fa-user-slash"></i> Patients absents</h3>
                <span class="wr-column-count">0</span>
            </header>
            <div class="wr-list" data-status="absent" aria-live="polite"></div>
        </article>
    </section>

    <section class="wr-tv-board" aria-label="Affichage salle d'attente">
        <div class="wr-tv-header">
            <h3><i class="fas fa-display"></i> {{ __('messages.waiting_room.tv_screen') }}</h3>
            <p>{{ __('messages.waiting_room.live_display') }}</p>
        </div>
        <div class="wr-tv-next" id="wr-tv-next" aria-live="polite">
            <div class="wr-tv-next-label">{{ __('messages.waiting_room.called_patient') }}</div>
            <div class="wr-tv-next-name" id="wr-tv-next-name">{{ __('messages.waiting_room.no_patient_selected') }}</div>
            <div class="wr-tv-next-meta" id="wr-tv-next-meta">{{ __('messages.waiting_room.next_call_here') }}</div>
        </div>
        <div class="wr-tv-table-wrap">
            <table class="wr-tv-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Patient</th>
                        <th>Heure</th>
                        <th>Médecin</th>
                        <th>Salle</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody id="wr-tv-body"></tbody>
            </table>
        </div>
    </section>

    <div id="wr-toast" class="wr-toast" role="status" aria-live="polite"></div>

    @once
        <link rel="stylesheet" href="{{ asset('css/waiting_room.css') }}">
        <script src="{{ asset('js/waiting_room.js') }}" defer></script>
    @endonce
</div>
