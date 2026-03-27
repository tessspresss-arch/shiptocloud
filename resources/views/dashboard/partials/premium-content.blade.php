@php
    $revenueAmount = (float) ($financialSummary['revenus'] ?? 0);
    $expenseAmount = (float) ($financialSummary['depenses'] ?? 0);
    $benefitAmount = (float) ($financialSummary['benefice'] ?? 0);
    $unpaidAmount = (float) ($financialSummary['factures_impayees'] ?? 0);

    $appointmentsToday = (int) ($stats['rdv_aujourd_hui'] ?? 0);
    $completedToday = (int) ($stats['consultations_terminees_aujourdhui'] ?? 0);
    $waitingRoomToday = (int) ($stats['patients_salle_attente'] ?? 0);
    $absentToday = (int) ($stats['patients_absents_aujourdhui'] ?? 0);
    $consultationDuration = (int) ($stats['temps_moyen_consultation'] ?? 0);

    $totalDoctors = (int) (($medecinActivity['disponible'] ?? 0) + ($medecinActivity['en_consultation'] ?? 0) + ($medecinActivity['absent'] ?? 0));
    $completionRate = $appointmentsToday > 0 ? min(100, (int) round(($completedToday / max(1, $appointmentsToday)) * 100)) : 0;
    $queueRate = $appointmentsToday > 0 ? min(100, (int) round(($waitingRoomToday / max(1, $appointmentsToday)) * 100)) : ($waitingRoomToday > 0 ? 100 : 0);
    $collectionRate = $revenueAmount > 0 ? min(100, max(0, (int) round((($revenueAmount - $unpaidAmount) / max(1, $revenueAmount)) * 100))) : 100;
    $activeDoctorRate = $totalDoctors > 0 ? min(100, (int) round(($medecinsActifs / max(1, $totalDoctors)) * 100)) : 0;

    $heroQuickActions = collect($quickActions ?? [])->take(3);
    $consultationSum = (int) collect($consultationDaily ?? [])->sum('value');
    $patientAcquisitionSum = (int) collect($patientEvolution ?? [])->sum('value');
@endphp

<div class="dashboard-shell dashboard-shell--headerless">
    <div class="dashboard-wrap">
        {{--
            Bloc hero premium masque pour alleger visuellement le dashboard.
            Conserver ce markup commente permet une reactivation rapide plus tard.
        <section class="dashboard-header">
            <div class="dashboard-header-grid dashboard-premium-header-grid">
                <div class="dashboard-brand">
                    <div class="dashboard-brand-icon">
                        <i class="fas fa-wave-square"></i>
                    </div>
                    <div class="dashboard-brand-meta">
                        <span class="dashboard-brand-badge">Cockpit SaaS</span>
                        <h2 class="dashboard-hero-title">Pilotage du cabinet</h2>
                        <p class="dashboard-hero-lead">Vue unifiee de l'activite clinique, des revenus et des actions prioritaires pour piloter la journee sans changer d'ecran.</p>

                        <div class="dashboard-hero-chips">
                            <span class="dashboard-hero-chip">
                                <i class="fas fa-calendar-day"></i>{{ now()->translatedFormat('l d F Y') }}
                            </span>
                            <span class="dashboard-hero-chip">
                                <i class="fas fa-user-doctor"></i>{{ $medecinsActifs }} praticiens actifs
                            </span>
                            <span class="dashboard-hero-chip {{ $urgentConsultations->count() > 0 ? 'is-warm' : '' }}">
                                <i class="fas fa-triangle-exclamation"></i>{{ $urgentConsultations->count() }} urgence(s) aujourd'hui
                            </span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-hero-spotlight">
                    <div class="dashboard-spotlight-card is-primary">
                        <span class="dashboard-spotlight-label">Revenus du mois</span>
                        <strong>{{ number_format($revenueAmount, 0, ',', ' ') }} <small>DH</small></strong>
                        <p>Benefice net : {{ number_format($benefitAmount, 0, ',', ' ') }} DH | Encaissement {{ $collectionRate }}%</p>
                    </div>

                    <div class="dashboard-spotlight-card">
                        <span class="dashboard-spotlight-label">Flux clinique</span>
                        <strong>{{ $appointmentsToday }} <small>RDV</small></strong>
                        <p>{{ $completedToday }} consultation(s) cloturee(s) | {{ $waitingRoomToday }} patient(s) en attente</p>
                    </div>
                </div>

                @if($heroQuickActions->isNotEmpty())
                    <div class="dashboard-hero-actions">
                        @foreach($heroQuickActions as $action)
                            <a href="{{ $action['route'] }}" class="dashboard-hero-action dashboard-hero-action-{{ $action['tone'] ?? 'blue' }}">
                                <span class="dashboard-hero-action-main">
                                    <i class="fas {{ $action['icon'] ?? 'fa-arrow-right' }}"></i>
                                    <span>{{ $action['label'] }}</span>
                                </span>
                                <span class="dashboard-hero-action-arrow"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
        --}}

        <section class="dashboard-kpi-grid">
            <article class="kpi-card" style="--kpi-meter: linear-gradient(90deg, #2563eb, #38bdf8);">
                <div class="kpi-top">
                    <div>
                        <p class="kpi-title">Patients actifs</p>
                        <p class="kpi-value">{{ $stats['patients_total'] ?? 0 }}</p>
                    </div>
                    <div class="kpi-icon kpi-tone-blue">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="kpi-meta-row">
                    <span class="kpi-meta-chip">+{{ $stats['patients_nouveaux_mois'] ?? 0 }} ce mois</span>
                    <span class="kpi-meta-helper">{{ $patientsProgress }}% objectif</span>
                </div>
                <div class="kpi-meter"><span style="width:{{ $patientsProgress > 0 ? max(10, $patientsProgress) : 0 }}%"></span></div>
            </article>

            <article class="kpi-card" style="--kpi-meter: linear-gradient(90deg, #0ea5e9, #22c55e);">
                <div class="kpi-top">
                    <div>
                        <p class="kpi-title">Rendez-vous du jour</p>
                        <p class="kpi-value">{{ $appointmentsToday }}</p>
                    </div>
                    <div class="kpi-icon kpi-tone-cyan">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="kpi-meta-row">
                    <span class="kpi-meta-chip">{{ $stats['rdv_semaine'] ?? 0 }} programmes cette semaine</span>
                    <span class="kpi-meta-helper">{{ $rdvProgress }}% objectif</span>
                </div>
                <div class="kpi-meter"><span style="width:{{ $rdvProgress > 0 ? max(10, $rdvProgress) : 0 }}%"></span></div>
            </article>

            <article class="kpi-card" style="--kpi-meter: linear-gradient(90deg, #f59e0b, #f97316);">
                <div class="kpi-top">
                    <div>
                        <p class="kpi-title">Revenus encaisses</p>
                        <p class="kpi-value">{{ $revenuMois }} <span class="kpi-value-unit">DH</span></p>
                    </div>
                    <div class="kpi-icon kpi-tone-amber">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="kpi-meta-row">
                    <span class="kpi-meta-chip">Impayees : {{ number_format($unpaidAmount, 0, ',', ' ') }} DH</span>
                    <span class="kpi-meta-helper">{{ $collectionRate }}% collecte</span>
                </div>
                <div class="kpi-meter"><span style="width:{{ $revenuProgress > 0 ? max(10, $revenuProgress) : 0 }}%"></span></div>
            </article>

            <article class="kpi-card" style="--kpi-meter: linear-gradient(90deg, #f97316, #ef4444);">
                <div class="kpi-top">
                    <div>
                        <p class="kpi-title">Salle d'attente</p>
                        <p class="kpi-value">{{ $waitingRoomToday }}</p>
                    </div>
                    <div class="kpi-icon kpi-tone-amber">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
                <div class="kpi-meta-row">
                    <span class="kpi-meta-chip">{{ $absentToday }} patient(s) absent(s)</span>
                    <span class="kpi-meta-helper">{{ $queueRate }}% de pression</span>
                </div>
                <div class="kpi-meter"><span style="width:{{ $queueRate > 0 ? max(10, $queueRate) : 0 }}%"></span></div>
            </article>

            <article class="kpi-card" style="--kpi-meter: linear-gradient(90deg, #10b981, #14b8a6);">
                <div class="kpi-top">
                    <div>
                        <p class="kpi-title">Consultations closes</p>
                        <p class="kpi-value">{{ $completedToday }}</p>
                    </div>
                    <div class="kpi-icon kpi-tone-green">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
                <div class="kpi-meta-row">
                    <span class="kpi-meta-chip">{{ $consultationDuration }} min en moyenne</span>
                    <span class="kpi-meta-helper">{{ $completionRate }}% du flux traite</span>
                </div>
                <div class="kpi-meter"><span style="width:{{ $completionRate > 0 ? max(10, $completionRate) : 0 }}%"></span></div>
            </article>

            <article class="kpi-card" style="--kpi-meter: linear-gradient(90deg, #8b5cf6, #2563eb);">
                <div class="kpi-top">
                    <div>
                        <p class="kpi-title">Equipe medicale</p>
                        <p class="kpi-value">{{ $medecinsActifs }}</p>
                    </div>
                    <div class="kpi-icon kpi-tone-blue">
                        <i class="fas fa-user-doctor"></i>
                    </div>
                </div>
                <div class="kpi-meta-row">
                    <span class="kpi-meta-chip">{{ $totalDoctors }} praticien(s) suivis</span>
                    <span class="kpi-meta-helper">{{ $activeDoctorRate }}% mobilises</span>
                </div>
                <div class="kpi-meter"><span style="width:{{ $activeDoctorRate > 0 ? max(10, $activeDoctorRate) : 0 }}%"></span></div>
            </article>
        </section>

        <section class="dashboard-main-grid dashboard-main-grid-premium">
            <div class="dashboard-stack">
                <div class="chart-grid dashboard-chart-grid">
                    <article class="chart-card">
                        <div class="chart-head">
                            <div>
                                <h3 class="chart-title">Performance financiere</h3>
                                <p class="chart-subtitle">Revenus mensuels {{ now()->year }}</p>
                            </div>
                            <span class="chart-pill">{{ now()->year }}</span>
                        </div>
                        <div class="chart-summary">
                            <strong>{{ number_format($benefitAmount, 0, ',', ' ') }} DH</strong>
                            <span>benefice net actuel</span>
                        </div>

                        @if(!empty($monthlyRevenue))
                            <div class="bar-chart">
                                @foreach($monthlyRevenue as $point)
                                    @php
                                        $value = (float) ($point['montant'] ?? 0);
                                        $height = $value > 0 ? max(10, (int) round(($value / max(1, $revenueChartMax)) * 100)) : 0;
                                    @endphp
                                    <div class="bar-chart-item">
                                        <div class="bar-chart-track">
                                            <div class="bar-chart-fill" style="height: {{ $height }}%; background: linear-gradient(180deg, #60a5fa 0%, #2563eb 100%);"></div>
                                        </div>
                                        <span class="bar-chart-value">{{ number_format($value, 0, ',', ' ') }}</span>
                                        <span class="bar-chart-label">{{ $point['mois'] ?? '-' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="dashboard-empty">
                                <i class="fas fa-chart-column"></i>
                                <p>Aucune donnee financiere exploitable pour le moment.</p>
                            </div>
                        @endif
                    </article>

                    <article class="chart-card">
                        <div class="chart-head">
                            <div>
                                <h3 class="chart-title">Rythme des consultations</h3>
                                <p class="chart-subtitle">Derniers jours cliniques</p>
                            </div>
                            <span class="chart-pill">7 jours</span>
                        </div>
                        <div class="chart-summary">
                            <strong>{{ $consultationSum }}</strong>
                            <span>acte(s) cumule(s)</span>
                        </div>

                        @if(!empty($consultationDaily))
                            <div class="bar-chart">
                                @foreach($consultationDaily as $point)
                                    @php
                                        $value = (int) ($point['value'] ?? 0);
                                        $height = $value > 0 ? max(10, (int) round(($value / max(1, $consultationChartMax)) * 100)) : 0;
                                    @endphp
                                    <div class="bar-chart-item">
                                        <div class="bar-chart-track">
                                            <div class="bar-chart-fill" style="height: {{ $height }}%; background: linear-gradient(180deg, #2dd4bf 0%, #0f8a63 100%);"></div>
                                        </div>
                                        <span class="bar-chart-value">{{ $value }}</span>
                                        <span class="bar-chart-label">{{ $point['label'] ?? '-' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="dashboard-empty">
                                <i class="fas fa-chart-line"></i>
                                <p>Aucune consultation sur la periode selectionnee.</p>
                            </div>
                        @endif
                    </article>

                    <article class="chart-card">
                        <div class="chart-head">
                            <div>
                                <h3 class="chart-title">Acquisition patients</h3>
                                <p class="chart-subtitle">Tendance des inscriptions recentes</p>
                            </div>
                            <span class="chart-pill">6 mois</span>
                        </div>
                        <div class="chart-summary">
                            <strong>{{ $patientAcquisitionSum }}</strong>
                            <span>nouveaux dossiers</span>
                        </div>

                        @if(!empty($patientEvolution))
                            <div class="bar-chart">
                                @foreach($patientEvolution as $point)
                                    @php
                                        $value = (int) ($point['value'] ?? 0);
                                        $height = $value > 0 ? max(10, (int) round(($value / max(1, $patientChartMax)) * 100)) : 0;
                                    @endphp
                                    <div class="bar-chart-item">
                                        <div class="bar-chart-track">
                                            <div class="bar-chart-fill" style="height: {{ $height }}%; background: linear-gradient(180deg, #c084fc 0%, #7c3aed 100%);"></div>
                                        </div>
                                        <span class="bar-chart-value">{{ $value }}</span>
                                        <span class="bar-chart-label">{{ $point['label'] ?? '-' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="dashboard-empty">
                                <i class="fas fa-user-plus"></i>
                                <p>Aucune inscription recente a afficher.</p>
                            </div>
                        @endif
                    </article>
                </div>

                <div class="dashboard-split-grid">
                    <article class="dashboard-card">
                        <div class="dashboard-card-head">
                            <div>
                                <h2 class="dashboard-card-title"><i class="fas fa-stethoscope"></i>Flux clinique du jour</h2>
                                <p class="dashboard-card-subtitle">Patients, horaires, contexte et actions rapides en lecture immediate.</p>
                            </div>
                            <a class="dashboard-card-link" href="{{ route('consultations.index') }}">Voir toutes les consultations</a>
                        </div>

                        @if(($rdvToday->count() ?? 0) > 0)
                            <div class="dashboard-table-wrap">
                                <table class="dashboard-table">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Heure</th>
                                            <th>Motif</th>
                                            <th>Medecin</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rdvToday->take(6) as $rdv)
                                            <tr>
                                                <td>
                                                    <div class="dashboard-table-person">
                                                        @if($rdv->dashboard_patient_avatar)
                                                            <img src="{{ $rdv->dashboard_patient_avatar }}" alt="{{ $rdv->dashboard_patient_name }}" class="dashboard-table-avatar">
                                                        @else
                                                            <span class="dashboard-table-avatar">{{ $rdv->dashboard_patient_initials }}</span>
                                                        @endif
                                                        <div class="dashboard-table-copy">
                                                            <strong>{{ $rdv->dashboard_patient_name }}</strong>
                                                            <span>{{ $rdv->patient?->telephone ?: 'Telephone non renseigne' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="dashboard-table-time">{{ optional($rdv->date_rdv ?? $rdv->date_heure)->format('H:i') ?? '-' }}</span></td>
                                                <td><span class="dashboard-table-note">{{ $rdv->motif ?? 'Consultation generale' }}</span></td>
                                                <td class="dashboard-table-cell-muted">Dr {{ trim(($rdv->medecin->prenom ?? '') . ' ' . ($rdv->medecin->nom ?? '')) ?: __('messages.common.not_provided') }}</td>
                                                <td><span class="status-pill {{ $rdv->dashboard_status['class'] }}">{{ $rdv->dashboard_status['label'] }}</span></td>
                                                <td>
                                                    <div class="dashboard-table-actions">
                                                        @if(($rdv->statut ?? null) !== 'en_soins')
                                                            <form method="POST" action="{{ $rdv->dashboard_start_url }}" data-dashboard-status-form>
                                                                @csrf
                                                                <input type="hidden" name="statut" value="en_soins">
                                                                <button type="submit" class="mini-action mini-action-play" title="Commencer la consultation">
                                                                    <i class="fas fa-play"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <a class="mini-action mini-action-folder" href="{{ $rdv->dashboard_patient_url }}" title="Ouvrir le dossier patient">
                                                            <i class="fas fa-folder-open"></i>
                                                        </a>
                                                        <a class="mini-action mini-action-note" href="{{ $rdv->dashboard_ordonnance_url }}" title="Ajouter une ordonnance">
                                                            <i class="fas fa-file-prescription"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="dashboard-empty">
                                <i class="fas fa-calendar-xmark"></i>
                                <p>Aucune consultation planifiee pour aujourd'hui.</p>
                            </div>
                        @endif
                    </article>

                    <article class="dashboard-card">
                        <div class="dashboard-card-head">
                            <div>
                                <h2 class="dashboard-card-title"><i class="fas fa-bolt"></i>Centre d'action</h2>
                                <p class="dashboard-card-subtitle">Les taches qui meritent une decision ou un suivi rapide.</p>
                            </div>
                        </div>

                        @if(($actionCenter->count() ?? 0) > 0)
                            <div class="action-center-list">
                                @foreach($actionCenter as $action)
                                    <a href="{{ $action['route'] ?? '#' }}" class="action-center-item action-{{ $action['tone'] ?? 'neutral' }}">
                                        <div class="action-center-main">
                                            <span class="action-center-icon"><i class="fas {{ $action['icon'] ?? 'fa-bell' }}"></i></span>
                                            <div>
                                                <p class="action-center-title">{{ $action['title'] ?? 'Action' }}</p>
                                                <p class="action-center-sub">{{ $action['subtitle'] ?? '' }}</p>
                                            </div>
                                        </div>
                                        <div class="action-center-right">
                                            <span class="action-center-count">{{ $action['count'] ?? 0 }}</span>
                                            <span class="action-center-badge">{{ $action['badge'] ?? 'A suivre' }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="dashboard-empty">
                                <i class="fas fa-check-circle"></i>
                                <p>Aucune action bloquante a traiter pour le moment.</p>
                            </div>
                        @endif
                    </article>
                </div>

                <article class="dashboard-card">
                    <div class="dashboard-card-head">
                        <div>
                            <h2 class="dashboard-card-title"><i class="fas fa-calendar-alt"></i>Agenda a venir</h2>
                            <p class="dashboard-card-subtitle">Lecture compacte des prochains rendez-vous avec acces direct au dossier et a la replanification.</p>
                        </div>
                        <a class="dashboard-card-link" href="{{ route('rendezvous.index') }}">Voir le calendrier</a>
                    </div>

                    @if(($upcomingRDV->count() ?? 0) > 0)
                        <div class="dashboard-table-wrap">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Patient</th>
                                        <th>Motif</th>
                                        <th>Medecin</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingRDV->take(8) as $rdv)
                                        <tr>
                                            <td><span class="dashboard-table-time">{{ optional($rdv->date_rdv ?? $rdv->date_heure)->format('d/m H:i') ?? '-' }}</span></td>
                                            <td>
                                                <div class="dashboard-table-person">
                                                    @if($rdv->dashboard_patient_avatar)
                                                        <img src="{{ $rdv->dashboard_patient_avatar }}" alt="{{ $rdv->dashboard_patient_name }}" class="dashboard-table-avatar">
                                                    @else
                                                        <span class="dashboard-table-avatar">{{ $rdv->dashboard_patient_initials }}</span>
                                                    @endif
                                                    <div class="dashboard-table-copy">
                                                        <strong>{{ $rdv->dashboard_patient_name }}</strong>
                                                        <span>{{ $rdv->patient?->telephone ?: 'Telephone non renseigne' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="dashboard-table-note">{{ $rdv->motif ?? 'Rendez-vous' }}</span></td>
                                            <td class="dashboard-table-cell-muted">Dr {{ trim(($rdv->medecin->prenom ?? '') . ' ' . ($rdv->medecin->nom ?? '')) ?: __('messages.common.not_provided') }}</td>
                                            <td><span class="status-pill {{ $rdv->dashboard_status['class'] }}">{{ $rdv->dashboard_status['label'] }}</span></td>
                                            <td>
                                                <div class="dashboard-table-actions">
                                                    @if(($rdv->statut ?? null) !== 'en_soins')
                                                        <form method="POST" action="{{ $rdv->dashboard_start_url }}" data-dashboard-status-form>
                                                            @csrf
                                                            <input type="hidden" name="statut" value="en_soins">
                                                            <button type="submit" class="mini-action mini-action-play" title="Commencer la consultation">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a class="mini-action mini-action-edit" href="{{ $rdv->dashboard_edit_url }}" title="Modifier le rendez-vous">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <a class="mini-action mini-action-folder" href="{{ $rdv->dashboard_patient_url }}" title="Ouvrir le dossier patient">
                                                        <i class="fas fa-folder-open"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="dashboard-empty">
                            <i class="fas fa-calendar-day"></i>
                            <p>Aucun rendez-vous a venir sur la periode observee.</p>
                        </div>
                    @endif
                </article>

                <article class="dashboard-card">
                    <div class="dashboard-card-head">
                        <div>
                            <h2 class="dashboard-card-title"><i class="fas fa-user-doctor"></i>Equipe medicale</h2>
                            <p class="dashboard-card-subtitle">Disponibilite des praticiens et prochaine plage utile pour garder une vue staff tres lisible.</p>
                        </div>
                        <a class="dashboard-card-link" href="{{ route('medecins.index') }}">Voir tous les medecins</a>
                    </div>

                    <div class="doctor-widget">
                        <div class="doctor-summary">
                            <div class="doctor-summary-card doctor-summary-available">
                                <span>Disponibles</span>
                                <strong>{{ $medecinActivity['disponible'] ?? 0 }}</strong>
                            </div>
                            <div class="doctor-summary-card doctor-summary-busy">
                                <span>En consultation</span>
                                <strong>{{ $medecinActivity['en_consultation'] ?? 0 }}</strong>
                            </div>
                            <div class="doctor-summary-card doctor-summary-away">
                                <span>Absents</span>
                                <strong>{{ $medecinActivity['absent'] ?? 0 }}</strong>
                            </div>
                        </div>

                        @if(!empty($medecinActivity['items']) && ($medecinActivity['items_count'] ?? 0) > 0)
                            <div class="doctor-list">
                                @foreach($medecinActivity['items'] as $doctor)
                                    <div class="doctor-item">
                                        <div class="doctor-item-main">
                                            <div class="doctor-avatar">
                                                @if(!empty($doctor['avatar_url']))
                                                    <img src="{{ $doctor['avatar_url'] }}" alt="{{ $doctor['name'] }}">
                                                @else
                                                    <div class="dashboard-list-icon" style="width:38px;height:38px;margin:0;background:#dbeafe;color:#2563eb;">{{ $doctor['display_initials'] }}</div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="doctor-item-name">{{ $doctor['name'] }}</p>
                                                <p class="doctor-item-meta">{{ $doctor['display_specialite'] }}</p>
                                            </div>
                                        </div>
                                        <div class="dashboard-list-actions">
                                            <span class="status-pill {{ $doctor['display_status']['class'] }}">{{ $doctor['display_status']['label'] }}</span>
                                            @if(!empty($doctor['next_slot']))
                                                <span class="text-muted small">{{ __('messages.dashboard.next') }} {{ $doctor['next_slot'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="dashboard-empty">
                                <i class="fas fa-user-doctor"></i>
                                <p>{{ __('messages.dashboard.no_doctor') }}</p>
                            </div>
                        @endif
                    </div>
                </article>
            </div>

            <div class="dashboard-stack dashboard-stack-side">
                <article class="dashboard-card dashboard-card-side" data-urgent-consultations-widget data-refresh-url="{{ route('dashboard.urgent-consultations') }}">
                    <div class="dashboard-card-head">
                        <div>
                            <h2 class="dashboard-card-title"><i class="fas fa-triangle-exclamation"></i>Consultations urgentes</h2>
                            <p class="dashboard-card-subtitle">Patients a prioriser immediatement avec acces direct au dossier.</p>
                        </div>
                        <a class="dashboard-card-link" href="{{ route('rendezvous.index', ['date' => now()->toDateString(), 'type' => 'urgence']) }}">Voir les urgences</a>
                    </div>

                    <div class="urgent-widget">
                        <span class="urgent-widget-count" data-urgent-count>
                            <i class="fas fa-bolt"></i>{{ $urgentConsultations->count() }}
                        </span>

                        <div class="urgent-list" data-urgent-list>
                            @forelse($urgentConsultations as $rdv)
                                <div class="urgent-item">
                                    <div class="urgent-item-main">
                                        <div class="urgent-avatar">
                                            @if($rdv->dashboard_patient_avatar)
                                                <img src="{{ $rdv->dashboard_patient_avatar }}" alt="{{ $rdv->dashboard_patient_name }}">
                                            @else
                                                {{ $rdv->dashboard_patient_initials }}
                                            @endif
                                        </div>
                                        <div class="urgent-item-text">
                                            <div class="urgent-name-row">
                                                <p class="urgent-patient-name">{{ $rdv->dashboard_patient_name }}</p>
                                                <span class="urgent-time"><i class="fas fa-clock"></i>{{ optional($rdv->date_rdv ?? $rdv->date_heure)->format('H:i') ?? '-' }}</span>
                                            </div>
                                            <p class="urgent-meta">{{ 'Dr ' . (trim(($rdv->medecin->prenom ?? '') . ' ' . ($rdv->medecin->nom ?? '')) ?: __('messages.common.not_provided')) }}</p>
                                        </div>
                                    </div>
                                    <div class="dashboard-list-actions">
                                        <span class="status-pill {{ $rdv->dashboard_status['class'] }}">{{ $rdv->dashboard_status['label'] }}</span>
                                        <a class="mini-action mini-action-folder" href="{{ $rdv->dashboard_patient_url }}" title="Ouvrir le dossier patient">
                                            <i class="fas fa-folder-open"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="urgent-empty" data-urgent-empty>Aucune consultation urgente aujourd'hui.</div>
                            @endforelse
                        </div>
                    </div>
                </article>

                <article class="dashboard-card dashboard-card-side">
                    <h3 class="bottom-card-title"><i class="fas fa-sack-dollar"></i>Snapshot financier</h3>
                    <div class="dashboard-stat-grid">
                        <div class="dashboard-stat-tile">
                            <span>Revenus</span>
                            <strong>{{ number_format($revenueAmount, 0, ',', ' ') }} DH</strong>
                        </div>
                        <div class="dashboard-stat-tile">
                            <span>Depenses</span>
                            <strong>{{ number_format($expenseAmount, 0, ',', ' ') }} DH</strong>
                        </div>
                        <div class="dashboard-stat-tile {{ $benefitAmount >= 0 ? 'is-success' : 'is-danger' }}">
                            <span>Benefice net</span>
                            <strong>{{ number_format($benefitAmount, 0, ',', ' ') }} DH</strong>
                        </div>
                        <div class="dashboard-stat-tile is-warning">
                            <span>Impayees</span>
                            <strong>{{ number_format($unpaidAmount, 0, ',', ' ') }} DH</strong>
                        </div>
                    </div>
                </article>

                <article class="dashboard-card dashboard-card-side">
                    <h2 class="dashboard-card-title mb-3"><i class="fas fa-circle-exclamation"></i>Alertes</h2>

                    @if(!empty($alerts) && count($alerts) > 0)
                        <div class="dashboard-list">
                            @foreach($alerts as $alert)
                                <a href="{{ $alert['route'] ?? '#' }}" class="alert-tile {{ $alert['tile_class'] ?? 'alert-info' }}" style="text-decoration:none;display:block;">
                                    <p class="alert-tile-text">
                                        <span class="alert-tile-icon"><i class="fas {{ $alert['icon'] ?? 'fa-bell' }}"></i></span>
                                        <span class="alert-tile-label">{{ $alert['message'] ?? 'Alerte' }}</span>
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert-tile alert-success">
                            <p class="alert-tile-text">
                                <span class="alert-tile-icon"><i class="fas fa-check"></i></span>
                                <span class="alert-tile-label">Aucune alerte. Tout est en ordre.</span>
                            </p>
                        </div>
                    @endif
                </article>

                <article class="dashboard-card dashboard-card-side">
                    <h2 class="dashboard-card-title mb-3"><i class="fas fa-bolt"></i>Actions rapides</h2>

                    @forelse($quickActions as $action)
                        <a href="{{ $action['route'] }}" class="quick-link quick-link-{{ $action['tone'] ?? 'blue' }} {{ $loop->first ? '' : 'mt-2' }}">
                            <span class="quick-link-main">
                                <i class="fas {{ $action['icon'] ?? 'fa-arrow-right' }} quick-link-glyph"></i>
                                <span class="quick-link-label">{{ $action['label'] }}</span>
                            </span>
                            <span class="quick-link-arrow"><i class="fas fa-arrow-right"></i></span>
                        </a>
                    @empty
                        <div class="dashboard-empty">
                            <i class="fas fa-wand-magic-sparkles"></i>
                            <p>Aucune action rapide disponible pour votre profil.</p>
                        </div>
                    @endforelse
                </article>

                <article class="dashboard-card dashboard-card-side">
                    <h3 class="bottom-card-title"><i class="fas fa-clock-rotate-left"></i>Historique recent</h3>

                    @if(($recentActivities->count() ?? 0) > 0)
                        <div class="activity-feed">
                            @foreach($recentActivities->take(6) as $activity)
                                <a href="{{ $activity['url'] ?? '#' }}" class="activity-item">
                                    <span class="activity-icon"><i class="fas {{ $activity['icon'] ?? 'fa-circle' }}"></i></span>
                                    <div class="activity-body">
                                        <div class="activity-head">
                                            <span class="activity-title">{{ $activity['title'] ?? 'Activite' }}</span>
                                            <span class="activity-time">{{ $activity['time'] ?? '-' }}</span>
                                        </div>
                                        <p class="activity-text">{{ $activity['description'] ?? '-' }}</p>
                                        <p class="activity-meta">{{ $activity['meta'] ?? '' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="dashboard-empty">
                            <i class="fas fa-clock"></i>
                            <p>Aucune activite recente.</p>
                        </div>
                    @endif
                </article>

                <article class="dashboard-card dashboard-card-side">
                    <h3 class="bottom-card-title"><i class="fas fa-bullseye"></i>Objectifs du mois</h3>
                    <div class="goal-list">
                        <div class="goal-row">
                            <div class="goal-head"><span>Revenus</span><strong>{{ $revenuProgress }}%</strong></div>
                            <div class="goal-track"><div class="goal-fill" style="width:{{ $revenuProgress }}%;background:linear-gradient(90deg,#2563eb,#1d4ed8);"></div></div>
                        </div>
                        <div class="goal-row">
                            <div class="goal-head"><span>Nouveaux patients</span><strong>{{ $patientsProgress }}%</strong></div>
                            <div class="goal-track"><div class="goal-fill" style="width:{{ $patientsProgress }}%;background:linear-gradient(90deg,#059669,#10b981);"></div></div>
                        </div>
                        <div class="goal-row">
                            <div class="goal-head"><span>Rendez-vous semaine</span><strong>{{ $rdvProgress }}%</strong></div>
                            <div class="goal-track"><div class="goal-fill" style="width:{{ $rdvProgress }}%;background:linear-gradient(90deg,#d97706,#f59e0b);"></div></div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <footer class="dashboard-footer">
            <span>Copyright {{ now()->year }} Medisys Pro.</span>
            <span>Dashboard optimise desktop, tablette et mobile.</span>
        </footer>
    </div>
</div>
