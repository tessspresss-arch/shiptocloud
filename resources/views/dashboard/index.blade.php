@extends('layouts.app')

@section('title', 'Tableau de Bord Médical')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-gray-900 mb-1">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>
                        Tableau de Bord Médical
                    </h1>
                    <p class="text-muted mb-0">{{ $mois_courant }}</p>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Dernière mise à jour</small>
                    <small class="text-muted">{{ now()->format('H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row g-4 mb-5">
        <!-- Total Patients -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Patients</h6>
                            <h3 class="mb-0 text-primary">{{ number_format($total_patients) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                +{{ $croissance_patients }}% ce mois
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rendez-vous Aujourd'hui -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-check fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">RDV Aujourd'hui</h6>
                            <h3 class="mb-0 text-info">{{ $rdv_aujourdhui }}</h3>
                            <small class="text-muted">Programmés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Médecins Actifs -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user-md fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Médecins Actifs</h6>
                            <h3 class="mb-0 text-success">{{ $total_medecins }}</h3>
                            <small class="text-muted">En service</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus du Mois -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-euro-sign fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Revenus Mois</h6>
                            <h3 class="mb-0 text-warning">{{ number_format($revenus_mois, 0, ',', ' ') }} DH</h3>
                            <small class="text-muted">Facturé</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Row -->
    <div class="row g-4 mb-5">
        <!-- Nouveaux Patients -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-light rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-plus fa-2x text-primary"></i>
                    </div>
                    <h4 class="mb-1">{{ $nouveaux_patients_mois }}</h4>
                    <small class="text-muted">Nouveaux patients</small>
                </div>
            </div>
        </div>

        <!-- Consultations du Mois -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-light rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-stethoscope fa-2x text-success"></i>
                    </div>
                    <h4 class="mb-1">{{ $total_consultations_mois }}</h4>
                    <small class="text-muted">Consultations</small>
                </div>
            </div>
        </div>

        <!-- RDV Annulés -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-light rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                    <h4 class="mb-1">{{ $rdv_annules_mois }}</h4>
                    <small class="text-muted">RDV annulés</small>
                </div>
            </div>
        </div>

        <!-- Factures Impayées -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-light rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <h4 class="mb-1">{{ $factures_impayees }}</h4>
                    <small class="text-muted">Factures impayées</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-5">
        <!-- Patient Growth Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Évolution Patients (6 derniers mois)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="patientGrowthChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-success me-2"></i>
                        Revenus Annuels
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Appointment Status Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-info me-2"></i>
                        Statut RDV (Année)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="appointmentChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Agenda Médical -->
        <div class="col-xl-9 col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            Agenda Médical
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Filtre Médecin -->
                            <select id="medecinFilter" class="form-select form-select-sm" style="width: auto; min-width: 150px;">
                                <option value="all">Tous les médecins</option>
                                @foreach($tous_medecins as $medecin)
                                    <option value="{{ $medecin->id }}">Dr. {{ $medecin->nom }}</option>
                                @endforeach
                            </select>
                            <a href="{{ route('agenda.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Voir tout
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="dashboardCalendar"></div>
                </div>
            </div>
        </div>

        <!-- Notifications et Alertes -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-bell text-warning me-2"></i>
                        Notifications
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($alertes) > 0)
                        @foreach($alertes as $alerte)
                        <div class="alert alert-{{ $alerte['type'] }} alert-dismissible fade show mb-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-{{ $alerte['icon'] }} me-2"></i>
                                <div class="flex-grow-1">
                                    <small>{{ $alerte['message'] }}</small>
                                </div>
                            </div>
                            @if(isset($alerte['action']))
                            <a href="{{ $alerte['action'] }}" class="btn btn-sm btn-outline-{{ $alerte['type'] }} ms-2">
                                Voir
                            </a>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted small mb-0">Tout est en ordre !</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Prochains RDV -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt text-info me-2"></i>
                        Prochains RDV
                    </h5>
                </div>
                <div class="card-body">
                    @if($prochains_rdv->count() > 0)
                        @foreach($prochains_rdv as $rdv)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0 me-3">
                                <div class="text-center">
                                    <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($rdv->date_heure)->format('d') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($rdv->date_heure)->format('M') }}</small>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</h6>
                                <p class="mb-0 small text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($rdv->date_heure)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar-times text-muted mb-2"></i>
                            <small class="text-muted">Aucun RDV à venir</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="row g-4 mt-4">
        <!-- Activité Récente -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-secondary me-2"></i>
                        Activité Récente
                    </h5>
                </div>
                <div class="card-body">
                    @if($consultations_recentes->count() > 0)
                        <div class="timeline">
                            @foreach($consultations_recentes as $consultation)
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1 small">
                                        Consultation - {{ $consultation->patient->nom }} {{ $consultation->patient->prenom }}
                                    </h6>
                                    <p class="mb-1 small text-muted">
                                        <i class="fas fa-user-md me-1"></i>Dr. {{ $consultation->medecin->nom }}
                                    </p>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($consultation->created_at)->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox text-muted mb-2"></i>
                            <small class="text-muted">Aucune activité récente</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques Rapides -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-success me-2"></i>
                        Statistiques Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">{{ $total_consultations_mois }}</h4>
                                <small class="text-muted">Consultations<br>cette année</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-success mb-1">{{ $nouveaux_patients_mois }}</h4>
                                <small class="text-muted">Nouveaux<br>patients</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-info mb-1">{{ $rdv_aujourdhui }}</h4>
                                <small class="text-muted">RDV<br>aujourd'hui</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-warning mb-1">{{ number_format($revenus_mois / 1000, 1) }}k€</h4>
                                <small class="text-muted">Revenus<br>du mois</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Message -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-shield-alt text-info me-3 fa-lg"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Sécurité des Données Médicales</h6>
                        <p class="mb-0 small">
                            Toutes les données patient sont chiffrées et stockées de manière sécurisée conformément aux normes RGPD.
                            Dernière sauvegarde: {{ now()->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 12px 16px;
    border-radius: 8px;
    border-left: 4px solid #0d6efd;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important;
}

.alert {
    border-radius: 10px;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

/* Styles pour le calendrier du dashboard - Vue mensuelle */
#dashboardCalendar {
    min-height: 500px;
}

#dashboardCalendar .fc-header-toolbar {
    margin-bottom: 1rem !important;
    padding: 0 1rem;
}

#dashboardCalendar .fc-toolbar-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

#dashboardCalendar .fc-button {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #495057;
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    font-weight: 500;
}

#dashboardCalendar .fc-button:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#dashboardCalendar .fc-button:not(:disabled).fc-button-active,
#dashboardCalendar .fc-button:not(:disabled):active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
}

#dashboardCalendar .fc-daygrid-day {
    transition: all 0.2s ease;
}

#dashboardCalendar .fc-daygrid-day:hover {
    background-color: #f8f9fa;
    transform: scale(1.02);
}

#dashboardCalendar .fc-daygrid-day-number {
    padding: 0.5rem;
    font-weight: 500;
    color: #495057;
    font-size: 0.9rem;
}

#dashboardCalendar .fc-day-today {
    background-color: #e7f3ff !important;
    border: 2px solid #0d6efd !important;
}

#dashboardCalendar .fc-day-today .fc-daygrid-day-number {
    background-color: #0d6efd;
    color: white;
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0.25rem auto;
    font-weight: 600;
}

#dashboardCalendar .fc-event {
    border-radius: 6px;
    border: none;
    font-size: 0.75rem;
    font-weight: 500;
    margin: 2px 1px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
    cursor: pointer;
    padding: 2px 4px;
}

#dashboardCalendar .fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

#dashboardCalendar .fc-event-main {
    padding: 2px 6px;
}

#dashboardCalendar .fc-more-link {
    font-size: 0.75rem;
    color: #6c757d;
    text-decoration: none;
    font-weight: 500;
}

#dashboardCalendar .fc-more-link:hover {
    color: #495057;
    text-decoration: underline;
}

/* Responsive pour le calendrier */
@media (max-width: 992px) and (min-width: 769px) {
    /* Tablet */
    #dashboardCalendar .fc-toolbar-title {
        font-size: 1.1rem;
    }

    #dashboardCalendar .fc-button {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
    }

    #dashboardCalendar .fc-daygrid-day-number {
        font-size: 0.85rem;
        padding: 0.4rem;
    }

    #dashboardCalendar .fc-event {
        font-size: 0.7rem;
    }
}

@media (max-width: 768px) {
    #dashboardCalendar {
        min-height: 400px;
    }

    #dashboardCalendar .fc-toolbar-title {
        font-size: 1rem;
    }

    #dashboardCalendar .fc-button {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    #dashboardCalendar .fc-header-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }

    #dashboardCalendar .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }

    #dashboardCalendar .fc-daygrid-day-number {
        font-size: 0.8rem;
        padding: 0.3rem;
    }

    #dashboardCalendar .fc-event {
        font-size: 0.65rem;
        margin: 1px;
    }
}
</style>

@push('scripts')
<!-- Chart.js -->
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le calendrier du dashboard
    const dashboardCalendarEl = document.getElementById('dashboardCalendar');
    const dashboardCalendar = new FullCalendar.Calendar(dashboardCalendarEl, {
        initialView: 'dayGridMonth',
        initialDate: new Date(),
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            const medecinId = document.getElementById('medecinFilter').value;
            let url = '/api/rendezvous?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr;
            if (medecinId && medecinId !== 'all') {
                url += '&medecin_id=' + medecinId;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        editable: false,
        selectable: false,
        dayMaxEvents: 3,
        moreLinkClick: 'popover',
        height: 'auto',
        eventDisplay: 'block',
        eventContent: function(arg) {
            const event = arg.event;
            const extendedProps = event.extendedProps;

            // Créer un contenu compact pour le mois
            return {
                html: `
                    <div class="text-truncate" style="font-size: 0.7rem; line-height: 1.2; font-weight: 500; padding: 1px 2px;">
                        <div style="font-weight: 600; color: #1f2937;">${extendedProps.patient}</div>
                        <div style="font-size: 0.6rem; color: #6b7280;">${extendedProps.medecin}</div>
                    </div>
                `
            };
        },
        eventDidMount: function(info) {
            // Style basé sur le statut avec design compact
            const statut = info.event.extendedProps.statut;
            const normalizeStatus = (value) => {
                const raw = (value || '').toString().toLowerCase();
                if (['programmé', 'programme', 'a_venir', 'à_venir', 'confirme', 'confirmé'].includes(raw)) return 'a_venir';
                if (raw === 'en_attente') return 'en_attente';
                if (raw === 'en_soins') return 'en_soins';
                if (['vu', 'termine', 'terminé'].includes(raw)) return 'vu';
                if (raw === 'absent') return 'absent';
                if (['annule', 'annulé'].includes(raw)) return 'annule';
                return 'a_venir';
            };
            const statusKey = normalizeStatus(statut);
            info.el.style.borderRadius = '4px';
            info.el.style.border = 'none';
            info.el.style.boxShadow = '0 1px 2px rgba(0,0,0,0.1)';
            info.el.style.fontSize = '0.7rem';
            info.el.style.margin = '1px 0';
            info.el.style.cursor = 'pointer';

            if (statusKey === 'en_soins') {
                info.el.style.backgroundColor = '#10b981';
                info.el.style.color = '#ffffff';
            } else if (statusKey === 'en_attente') {
                info.el.style.backgroundColor = '#f59e0b';
                info.el.style.color = '#ffffff';
            } else if (statusKey === 'a_venir') {
                info.el.style.backgroundColor = '#3b82f6';
                info.el.style.color = '#ffffff';
            } else if (statusKey === 'vu') {
                info.el.style.backgroundColor = '#64748b';
                info.el.style.color = '#ffffff';
            } else if (statusKey === 'absent') {
                info.el.style.backgroundColor = '#f97316';
                info.el.style.color = '#ffffff';
            } else {
                info.el.style.backgroundColor = '#ef4444';
                info.el.style.color = '#ffffff';
            }

            // Hover effect
            info.el.addEventListener('mouseenter', function() {
                info.el.style.transform = 'translateY(-1px)';
                info.el.style.boxShadow = '0 2px 4px rgba(0,0,0,0.15)';
            });
            info.el.addEventListener('mouseleave', function() {
                info.el.style.transform = 'translateY(0)';
                info.el.style.boxShadow = '0 1px 2px rgba(0,0,0,0.1)';
            });
        },
        eventClick: function(info) {
            // Rediriger vers la page de détails du rendez-vous
            window.location.href = '/rendezvous/' + info.event.id;
        }
    });

    dashboardCalendar.render();

    // Gestion du filtre médecin
    document.getElementById('medecinFilter').addEventListener('change', function() {
        dashboardCalendar.refetchEvents();
    });

    // Initialiser les graphiques
    initializeCharts();
});

function initializeCharts() {
    // Graphique d'évolution des patients
    const patientGrowthCtx = document.getElementById('patientGrowthChart').getContext('2d');
    const patientGrowthChart = new Chart(patientGrowthCtx, {
        type: 'line',
        data: {
            labels: [
                '{{ now()->subMonths(5)->format('M Y') }}',
                '{{ now()->subMonths(4)->format('M Y') }}',
                '{{ now()->subMonths(3)->format('M Y') }}',
                '{{ now()->subMonths(2)->format('M Y') }}',
                '{{ now()->subMonths(1)->format('M Y') }}',
                '{{ now()->format('M Y') }}'
            ],
            datasets: [{
                label: 'Nouveaux patients',
                data: @json($patient_growth_data),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 6
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Graphique des revenus
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Revenus (DH)',
                data: @json($revenue_data),
                backgroundColor: 'rgba(25, 135, 84, 0.8)',
                borderColor: '#198754',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 6,
                    callbacks: {
                        label: function(context) {
                            return 'Revenus: ' + context.parsed.y.toLocaleString() + ' DH';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' DH';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Graphique des statuts de rendez-vous
    const appointmentCtx = document.getElementById('appointmentChart').getContext('2d');
    const appointmentChart = new Chart(appointmentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Confirmé', 'Programmé', 'Annulé', 'Terminé'],
            datasets: [{
                data: [
                    @json($appointment_stats['confirmé']),
                    @json($appointment_stats['programmé']),
                    @json($appointment_stats['annulé']),
                    @json($appointment_stats['terminé'])
                ],
                backgroundColor: [
                    '#198754', // Confirmé - vert
                    '#0d6efd', // Programmé - bleu
                    '#dc3545', // Annulé - rouge
                    '#6c757d'  // Terminé - gris
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 6,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
}
</script>
@endpush
@endsection
