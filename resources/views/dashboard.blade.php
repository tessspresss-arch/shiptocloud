
@extends('layouts.app')

@section('title', 'Tableau de Bord Moderne')

@section('content')
<div class="container-fluid py-4" style="background: linear-gradient(135deg, #f8fafb 0%, #f0f4f8 100%); min-height: 100vh; border-radius: 0;">
    <div class="row">
        <!-- Sidebar compacte -->
        <div class="col-lg-2 d-none d-lg-flex flex-column align-items-center bg-white shadow-sm rounded-3 py-4 me-4" style="min-height: 80vh;">
            <a href="#" class="mb-4"><i class="fas fa-clinic-medical fa-2x text-primary"></i></a>
            <nav class="nav flex-column w-100">
                <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a class="nav-link" href="#"><i class="fas fa-user-injured me-2"></i>Patients</a>
                <a class="nav-link" href="#"><i class="fas fa-calendar-check me-2"></i>RDV</a>
                <a class="nav-link" href="#"><i class="fas fa-user-md me-2"></i>Médecins</a>
                <a class="nav-link" href="#"><i class="fas fa-file-invoice-dollar me-2"></i>Facturation</a>
                <a class="nav-link" href="#"><i class="fas fa-cogs me-2"></i>Paramètres</a>
            </nav>
        </div>
        <!-- Contenu principal -->
        <div class="col-lg-10">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Tableau de Bord</h2>
                    <div class="text-muted">Bienvenue, {{ Auth::user()->name ?? 'Utilisateur' }}</div>
                </div>
                <form class="d-none d-md-block">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Recherche rapide...">
                    </div>
                </form>
            </div>
            <!-- KPIs Modernes -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-users fa-2x text-primary"></i></div>
                        <div class="fw-bold fs-4">{{ number_format($total_patients) }}</div>
                        <div class="text-muted small">Patients</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-calendar-check fa-2x text-info"></i></div>
                        <div class="fw-bold fs-4">{{ $rdv_aujourdhui }}</div>
                        <div class="text-muted small">RDV aujourd'hui</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-user-md fa-2x text-success"></i></div>
                        <div class="fw-bold fs-4">{{ $total_medecins }}</div>
                        <div class="text-muted small">Médecins actifs</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-euro-sign fa-2x text-warning"></i></div>
                        <div class="fw-bold fs-4">{{ number_format($revenus_mois, 0, ',', ' ') }} DH</div>
                        <div class="text-muted small">Revenus/mois</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-user-plus fa-2x text-secondary"></i></div>
                        <div class="fw-bold fs-4">{{ $nouveaux_patients_mois }}</div>
                        <div class="text-muted small">Nouveaux patients</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-stethoscope fa-2x text-danger"></i></div>
                        <div class="fw-bold fs-4">{{ $total_consultations_mois }}</div>
                        <div class="text-muted small">Consultations</div>
                    </div>
                </div>
            </div>
            <!-- KPIs additionnels modernes -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-percentage fa-2x text-info"></i></div>
                        <div class="fw-bold fs-5">{{ $taux_occupation ?? '0%' }}</div>
                        <div class="text-muted small">Taux d'occupation</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-heartbeat fa-2x text-danger"></i></div>
                        <div class="fw-bold fs-5">{{ $rdv_annules_mois }}</div>
                        <div class="text-muted small">RDV annulés</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-exclamation-triangle fa-2x text-warning"></i></div>
                        <div class="fw-bold fs-5">{{ $factures_impayees }}</div>
                        <div class="text-muted small">Factures impayées</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="card shadow-sm border-0 text-center py-3">
                        <div class="mb-2"><i class="fas fa-clock fa-2x text-secondary"></i></div>
                        <div class="fw-bold fs-5">{{ $delai_moyen_rdv ?? '0j' }}</div>
                        <div class="text-muted small">Délai moyen RDV</div>
                    </div>
                </div>
            </div>
            <!-- Section agenda et notifications -->
            <div class="row g-3">
                <div class="col-lg-8 col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold text-gray-900"><i class="fas fa-calendar-alt text-primary me-2"></i>Agenda du Jour</h5>
                            <a href="{{ route('agenda.index') }}" class="btn btn-outline-primary btn-sm">Voir tout</a>
                        </div>
                        <div class="card-body">
                            @if($rdv_aujourdhui > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($rdvs_today as $rdv)
                                    <li class="list-group-item d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="fw-bold">{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</span>
                                            <span class="text-muted small ms-2"><i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($rdv->date_heure)->format('H:i') }}</span>
                                        </div>
                                        <span class="badge bg-{{ $rdv->statut == 'confirmé' ? 'success' : 'secondary' }}">{{ ucfirst($rdv->statut) }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-calendar-times fa-2x mb-2"></i><br>
                                    Aucun rendez-vous prévu aujourd'hui.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="fas fa-bell text-warning me-2"></i>Alertes & Notifications</h5>
                        </div>
                        <div class="card-body">
                            @if(count($alertes) > 0)
                                @foreach($alertes as $alerte)
                                <div class="alert alert-{{ $alerte['type'] }} alert-dismissible fade show mb-3" role="alert">
                                    <i class="fas fa-{{ $alerte['icon'] }} me-2"></i>
                                    <span>{{ $alerte['message'] }}</span>
                                    @if(isset($alerte['action']))
                                    <a href="{{ $alerte['action'] }}" class="btn btn-sm btn-outline-{{ $alerte['type'] }} ms-2">Voir</a>
                                    @endif
                                </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                                    Tout est en ordre !
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
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

/* Styles modernes pour le calendrier du dashboard */
.modern-calendar {
    min-height: 550px;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 0 0 12px 12px;
}

.modern-calendar .fc {
    background: transparent;
    font-family: inherit;
}

.modern-calendar .fc-header-toolbar {
    margin-bottom: 1.5rem !important;
    padding: 1rem 1.5rem 0;
    justify-content: space-between !important;
}

.modern-calendar .fc-toolbar-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.modern-calendar .fc-button-group {
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.modern-calendar .fc-button {
    background: white;
    border: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    font-weight: 600;
    transition: all 0.2s ease;
    border-radius: 0 !important;
    margin: 0 !important;
}

.modern-calendar .fc-button:hover {
    background: #f1f5f9;
    color: #334155;
    transform: translateY(-1px);
}

.modern-calendar .fc-button:not(:disabled).fc-button-active {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
}

.modern-calendar .fc-daygrid-day {
    transition: all 0.2s ease;
    cursor: pointer;
}

.modern-calendar .fc-daygrid-day:hover {
    background: rgba(13, 110, 253, 0.05);
    transform: scale(1.02);
}

.modern-calendar .fc-daygrid-day-number {
    padding: 0.75rem;
    font-weight: 600;
    color: #475569;
    font-size: 0.95rem;
    position: relative;
    z-index: 2;
}

.modern-calendar .fc-day-today {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%) !important;
    border: none !important;
    position: relative;
}

.modern-calendar .fc-day-today::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #0d6efd 0%, #3b82f6 100%);
    opacity: 0.1;
    border-radius: 8px;
    z-index: 1;
}

.modern-calendar .fc-day-today .fc-daygrid-day-number {
    color: #0d6efd;
    position: relative;
    z-index: 3;
}

.modern-calendar .fc-event {
    border-radius: 8px;
    border: none;
    font-size: 0.75rem;
    font-weight: 600;
    margin: 3px 2px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    cursor: pointer;
    padding: 0;
    overflow: hidden;
}

.modern-calendar .fc-event:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}

.modern-calendar .fc-event-main {
    padding: 4px 8px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.modern-calendar .fc-event-title {
    font-size: 0.7rem;
    line-height: 1.2;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.modern-calendar .fc-more-link {
    font-size: 0.75rem;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.modern-calendar .fc-more-link:hover {
    color: #0d6efd;
    background: rgba(13, 110, 253, 0.1);
}

/* Statuts des rendez-vous */
.modern-calendar .fc-event.status-confirme {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.modern-calendar .fc-event.status-en_attente {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.modern-calendar .fc-event.status-programme {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.modern-calendar .fc-event.status-annule {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

/* Indicateurs de statut */
.modern-calendar .fc-event::after {
    content: '';
    position: absolute;
    top: 2px;
    right: 2px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255,255,255,0.8);
}

/* Responsive design */
@media (max-width: 1200px) {
    .modern-calendar .fc-toolbar-title {
        font-size: 1.25rem;
    }
}

@media (max-width: 992px) {
    .modern-calendar {
        min-height: 450px;
    }

    .modern-calendar .fc-header-toolbar {
        flex-direction: column !important;
        gap: 1rem;
        padding: 1rem;
    }

    .modern-calendar .fc-toolbar-title {
        font-size: 1.1rem;
        text-align: center;
    }

    .modern-calendar .fc-button {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
}

@media (max-width: 768px) {
    .modern-calendar {
        min-height: 400px;
    }

    .modern-calendar .fc-toolbar-title {
        font-size: 1rem;
    }

    .modern-calendar .fc-button {
        font-size: 0.75rem;
        padding: 0.35rem 0.7rem;
    }

    .modern-calendar .fc-daygrid-day-number {
        font-size: 0.85rem;
        padding: 0.5rem;
    }

    .modern-calendar .fc-event {
        font-size: 0.7rem;
        margin: 2px 1px;
    }

    .modern-calendar .fc-event-title {
        font-size: 0.65rem;
    }
}

@media (max-width: 576px) {
    .modern-calendar {
        min-height: 350px;
    }

    .modern-calendar .fc-header-toolbar {
        padding: 0.5rem;
    }

    .modern-calendar .fc-toolbar-title {
        font-size: 0.95rem;
    }

    .modern-calendar .fc-button {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
    }

    .modern-calendar .fc-daygrid-day-number {
        font-size: 0.8rem;
        padding: 0.4rem;
    }
}
</style>

@push('scripts')
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
            // Get selected doctor from dropdown
            const selectedItem = document.querySelector('#medecinFilter .dropdown-item.active');
            const medecinId = selectedItem ? selectedItem.getAttribute('data-value') : 'all';

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
        dayMaxEvents: 4,
        moreLinkClick: 'popover',
        height: 'auto',
        eventDisplay: 'block',
        eventContent: function(arg) {
            const event = arg.event;
            const extendedProps = event.extendedProps;

            // Créer un contenu moderne et compact
            return {
                html: `
                    <div class="d-flex align-items-center gap-1">
                        <div class="event-patient" style="font-size: 0.7rem; font-weight: 600; line-height: 1.1;">
                            ${extendedProps.patient}
                        </div>
                        <div class="event-doctor" style="font-size: 0.6rem; opacity: 0.8; line-height: 1;">
                            ${extendedProps.medecin}
                        </div>
                    </div>
                `
            };
        },
        eventDidMount: function(info) {
            // Style basé sur le statut avec design moderne
            const statut = info.event.extendedProps.statut;
            const eventEl = info.el;
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

            // Ajouter la classe de statut
            eventEl.classList.add('status-' + statusKey);

            // Style de base
            eventEl.style.borderRadius = '6px';
            eventEl.style.border = 'none';
            eventEl.style.fontSize = '0.7rem';
            eventEl.style.cursor = 'pointer';
            eventEl.style.transition = 'all 0.3s ease';
            const statusColors = {
                a_venir: '#3b82f6',
                en_attente: '#f59e0b',
                en_soins: '#10b981',
                vu: '#64748b',
                absent: '#f97316',
                annule: '#ef4444'
            };
            eventEl.style.backgroundColor = statusColors[statusKey] || statusColors.a_venir;
            eventEl.style.color = '#ffffff';

            // Hover effect amélioré
            eventEl.addEventListener('mouseenter', function() {
                eventEl.style.transform = 'translateY(-2px) scale(1.02)';
                eventEl.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
                eventEl.style.zIndex = '10';
            });

            eventEl.addEventListener('mouseleave', function() {
                eventEl.style.transform = 'translateY(0) scale(1)';
                eventEl.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
                eventEl.style.zIndex = 'auto';
            });
        },
        eventClick: function(info) {
            // Rediriger vers la page de détails du rendez-vous avec animation
            info.el.style.transform = 'scale(0.95)';
            setTimeout(() => {
                window.location.href = '/rendezvous/' + info.event.id;
            }, 150);
        },
        datesSet: function() {
            // Animation lors du changement de mois
            const calendarEl = document.querySelector('.modern-calendar .fc-view-harness');
            calendarEl.style.opacity = '0';
            calendarEl.style.transform = 'translateY(10px)';

            setTimeout(() => {
                calendarEl.style.transition = 'all 0.3s ease';
                calendarEl.style.opacity = '1';
                calendarEl.style.transform = 'translateY(0)';
            }, 50);
        }
    });

    dashboardCalendar.render();

    // Gestion du filtre médecin avec dropdown
    const dropdownItems = document.querySelectorAll('#medecinFilter .dropdown-item');
    const selectedDoctorSpan = document.getElementById('selectedDoctor');

    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            // Retirer la classe active de tous les items
            dropdownItems.forEach(i => i.classList.remove('active'));
            // Ajouter la classe active à l'item cliqué
            this.classList.add('active');

            // Mettre à jour le texte du bouton
            selectedDoctorSpan.textContent = this.textContent;

            // Recharger les événements
            dashboardCalendar.refetchEvents();

            // Animation de feedback
            this.style.backgroundColor = 'rgba(13, 110, 253, 0.1)';
            setTimeout(() => {
                this.style.backgroundColor = '';
            }, 200);
        });
    });

    // Animation d'entrée pour le calendrier
    setTimeout(() => {
        dashboardCalendarEl.style.opacity = '0';
        dashboardCalendarEl.style.transform = 'translateY(20px)';

        setTimeout(() => {
            dashboardCalendarEl.style.transition = 'all 0.5s ease';
            dashboardCalendarEl.style.opacity = '1';
            dashboardCalendarEl.style.transform = 'translateY(0)';
        }, 100);
    }, 200);
});
</script>
@endpush
@endsection
