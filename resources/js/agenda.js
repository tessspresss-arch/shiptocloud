// Agenda JavaScript functionality - Version Moderne
class ModernMedicalAgenda {
    constructor() {
        this.currentDate = new Date();
        this.currentView = 'day';
        this.filters = {
            doctor: 'all',
            status: 'all',
            search: ''
        };
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadAppointments();
        this.updateDateDisplay();
        this.showWelcomeAnimation();
    }

    bindEvents() {
        // Navigation de vue moderne
        document.querySelectorAll('.view-btn-modern').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchView(e.target.closest('.view-btn-modern').dataset.view);
            });
        });

        // Navigation de date moderne
        document.querySelector('.nav-prev')?.addEventListener('click', () => this.navigateDate(-1));
        document.querySelector('.nav-next')?.addEventListener('click', () => this.navigateDate(1));
        document.querySelector('.nav-today')?.addEventListener('click', () => this.goToToday());

        // Filtres
        document.getElementById('doctorFilter')?.addEventListener('change', (e) => {
            this.filters.doctor = e.target.value;
            this.applyFilters();
        });

        document.getElementById('statusFilter')?.addEventListener('change', (e) => {
            this.filters.status = e.target.value;
            this.applyFilters();
        });

        document.getElementById('searchFilter')?.addEventListener('input', (e) => {
            this.filters.search = e.target.value.toLowerCase();
            this.applyFilters();
        });

        // Bouton d'actualisation
        document.querySelector('.btn-modern.btn-secondary')?.addEventListener('click', () => {
            this.refreshAgenda();
        });

        // Mini calendrier
        document.querySelectorAll('.day-cell').forEach(cell => {
            cell.addEventListener('click', (e) => {
                const dateString = e.target.closest('.day-cell').dataset.date ||
                                 e.target.textContent;
                this.selectDate(dateString);
            });
        });
    }

    switchView(view) {
        // Animation de transition
        this.fadeOutContent(() => {
            this.currentView = view;

            // Mettre à jour les boutons actifs
            document.querySelectorAll('.view-btn-modern').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.view-btn-modern[data-view="${view}"]`).classList.add('active');

            // Recharger avec la nouvelle vue
            this.loadAppointments();
            this.fadeInContent();
        });
    }

    navigateDate(direction) {
        this.animateNavigation(() => {
            if (this.currentView === 'day') {
                this.currentDate.setDate(this.currentDate.getDate() + direction);
            } else if (this.currentView === 'week') {
                this.currentDate.setDate(this.currentDate.getDate() + (direction * 7));
            } else if (this.currentView === 'month') {
                this.currentDate.setMonth(this.currentDate.getMonth() + direction);
            }
            this.updateDateDisplay();
            this.loadAppointments();
        });
    }

    goToToday() {
        this.animateNavigation(() => {
            this.currentDate = new Date();
            this.updateDateDisplay();
            this.loadAppointments();
        });
    }

    updateDateDisplay() {
        const dateElement = document.querySelector('.current-date-text');
        const weekElement = document.querySelector('.current-week');

        if (dateElement) {
            dateElement.textContent = this.currentDate.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        if (weekElement) {
            const weekNumber = this.getWeekNumber(this.currentDate);
            weekElement.textContent = `${weekNumber}e semaine`;
        }
    }

    loadAppointments() {
        const startDate = this.getStartDate();
        const endDate = this.getEndDate();

        // Afficher le loader
        this.showLoading();

        fetch(`/api/rendezvous?start=${this.formatDate(startDate)}&end=${this.formatDate(endDate)}`)
            .then(res => res.json())
            .then(data => {
                this.hideLoading();
                this.renderAppointments(data);
                this.updateStats(data);
            })
            .catch(error => {
                this.hideLoading();
                this.showError('Erreur lors du chargement des rendez-vous');
                console.error('Error loading appointments:', error);
            });
    }

    renderAppointments(appointments) {
        // Appliquer les filtres
        let filteredAppointments = this.applyFiltersToData(appointments);

        // Rendre selon la vue actuelle
        if (this.currentView === 'day') {
            this.renderDayView(filteredAppointments);
        } else if (this.currentView === 'week') {
            this.renderWeekView(filteredAppointments);
        } else if (this.currentView === 'month') {
            this.renderMonthView(filteredAppointments);
        }
    }

    renderDayView(appointments) {
        const timelineBody = document.querySelector('.timeline-body');
        if (!timelineBody) return;

        // Grouper par heure
        const appointmentsByHour = {};
        for (let hour = 8; hour <= 19; hour++) {
            appointmentsByHour[hour] = appointments.filter(apt => {
                const aptHour = new Date(apt.start).getHours();
                return aptHour === hour;
            });
        }

        // Générer le HTML
        let html = '';
        for (let hour = 8; hour <= 19; hour++) {
            const hourAppointments = appointmentsByHour[hour] || [];
            const isEmpty = hourAppointments.length === 0;

            html += `
                <div class="time-slot" data-hour="${hour}">
                    <div class="time-label">${this.formatHour(hour)}</div>
                    <div class="slot-content ${isEmpty ? 'empty' : ''}">
                        ${hourAppointments.map(apt => this.renderAppointmentCard(apt)).join('')}
                        ${isEmpty ? this.renderQuickAddSlot(hour) : ''}
                    </div>
                </div>
            `;
        }

        timelineBody.innerHTML = html;
    }

    renderAppointmentCard(appointment) {
        const startTime = new Date(appointment.start);
        const endTime = new Date(appointment.end);
        const duration = (endTime - startTime) / (1000 * 60); // minutes
        const height = (duration / 30) * 60; // pixels based on 30min = 60px
        const statusClass = this.safeStatusClass(appointment.extendedProps?.statut);
        const patient = this.escapeHtml(appointment.extendedProps?.patient || '');
        const motif = this.escapeHtml(appointment.extendedProps?.motif || 'Consultation');
        const patientInitial = this.escapeHtml((appointment.extendedProps?.patient || '?').charAt(0));
        const medecinInitial = this.escapeHtml((appointment.extendedProps?.medecin || '?').charAt(0));
        const appointmentId = Number.parseInt(appointment.id, 10) || 0;

        return `
            <div class="appointment-card ${statusClass}"
                 style="height: ${height}px;"
                 onclick="agenda.openAppointmentModal(${appointmentId})">
                <div class="card-time">
                    ${startTime.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})} -
                    ${endTime.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}
                </div>
                <div class="card-patient">
                    <div class="patient-avatar">
                        ${patientInitial}${medecinInitial}
                    </div>
                    <div class="patient-details">
                        <strong>${patient}</strong>
                        <span class="motif">${motif}</span>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="btn-action" onclick="event.stopPropagation(); agenda.quickAction('confirm', ${appointmentId})">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        `;
    }

    renderQuickAddSlot(hour) {
        return `
            <a href="/rendezvous/create?date=${this.formatDate(this.currentDate)}&heure=${this.formatHour(hour)}"
               class="quick-add-slot always-visible">
                <i class="fas fa-plus"></i> Nouveau RDV
            </a>
        `;
    }

    renderWeekView(appointments) {
        // Implémentation de la vue semaine (simplifiée pour l'instant)
        this.renderDayView(appointments); // Fallback à la vue jour
    }

    renderMonthView(appointments) {
        // Implémentation de la vue mois (simplifiée pour l'instant)
        this.renderDayView(appointments); // Fallback à la vue jour
    }

    openAppointmentModal(appointmentId) {
        // Animation d'ouverture
        fetch(`/rendezvous/${appointmentId}`)
            .then(res => res.json())
            .then(data => {
                this.showAppointmentModal(data);
            })
            .catch(error => {
                this.showError('Erreur lors du chargement du rendez-vous');
            });
    }

    showAppointmentModal(data) {
        const patientNom = this.escapeHtml(data.patient?.nom || '');
        const patientPrenom = this.escapeHtml(data.patient?.prenom || '');
        const medecinNom = this.escapeHtml(data.medecin?.nom || '');
        const motif = this.escapeHtml(data.motif || '');
        const safeId = Number.parseInt(data.id, 10) || 0;
        const safeDate = this.escapeHtml(new Date(data.date_heure).toLocaleString('fr-FR'));
        // Créer et afficher une modal moderne
        const modalHtml = `
            <div class="modal fade modern-modal" id="appointmentDetailModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-calendar-check text-primary me-2"></i>
                                Détails du rendez-vous
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="appointment-details">
                                <div class="detail-row">
                                    <span class="label">Patient:</span>
                                    <span class="value">${patientNom} ${patientPrenom}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Médecin:</span>
                                    <span class="value">Dr. ${medecinNom}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Date & Heure:</span>
                                    <span class="value">${safeDate}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Motif:</span>
                                    <span class="value">${motif}</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <a href="/rendezvous/${safeId}/edit" class="btn btn-primary">Modifier</a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Supprimer la modal existante si elle existe
        const existingModal = document.getElementById('appointmentDetailModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Ajouter la nouvelle modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Afficher la modal
        const modal = new bootstrap.Modal(document.getElementById('appointmentDetailModal'));
        modal.show();
    }

    quickAction(action, id) {
        if (action === 'confirm') {
            this.showLoading();
            fetch(`/rendezvous/${id}/confirm`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.hideLoading();
                if (data.success) {
                    this.showToast('Rendez-vous confirmé avec succès', 'success');
                    this.loadAppointments();
                } else {
                    this.showToast('Erreur lors de la confirmation', 'error');
                }
            })
            .catch(error => {
                this.hideLoading();
                this.showToast('Erreur réseau', 'error');
            });
        }
    }

    applyFilters() {
        // Recharger les rendez-vous avec les filtres
        this.loadAppointments();
    }

    applyFiltersToData(appointments) {
        return appointments.filter(apt => {
            // Filtre médecin
            if (this.filters.doctor !== 'all' && apt.extendedProps.medecin_id != this.filters.doctor) {
                return false;
            }

            // Filtre statut
            if (this.filters.status !== 'all' && apt.extendedProps.statut !== this.filters.status) {
                return false;
            }

            // Filtre recherche
            if (this.filters.search) {
                const searchText = `${apt.extendedProps.patient} ${apt.extendedProps.medecin} ${apt.extendedProps.motif}`.toLowerCase();
                if (!searchText.includes(this.filters.search)) {
                    return false;
                }
            }

            return true;
        });
    }

    refreshAgenda() {
        // Animation de rotation du bouton
        const btn = document.querySelector('.btn-modern.btn-secondary');
        btn.style.transform = 'rotate(360deg)';

        setTimeout(() => {
            btn.style.transform = 'rotate(0deg)';
            this.loadAppointments();
            this.showToast('Agenda actualisé', 'success');
        }, 500);
    }

    selectDate(dateString) {
        this.currentDate = new Date(dateString);
        this.updateDateDisplay();
        this.loadAppointments();
    }

    escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    safeStatusClass(status) {
        const normalized = String(status || '').toLowerCase().trim();
        const map = {
            programme: 'programme',
            confirme: 'confirme',
            en_attente: 'en_attente',
            en_soins: 'en_soins',
            vu: 'vu',
            absent: 'absent',
            annule: 'annule',
            'annulé': 'annule',
            a_venir: 'a_venir',
            'à_venir': 'a_venir',
            termine: 'termine',
            'terminé': 'termine',
        };

        return map[normalized] || 'programme';
    }

    // Utilitaires
    getStartDate() {
        const date = new Date(this.currentDate);
        if (this.currentView === 'week') {
            const day = date.getDay();
            const diff = date.getDate() - day;
            date.setDate(diff);
        } else if (this.currentView === 'month') {
            date.setDate(1);
        }
        return date;
    }

    getEndDate() {
        const date = new Date(this.currentDate);
        if (this.currentView === 'week') {
            const day = date.getDay();
            const diff = date.getDate() + (6 - day);
            date.setDate(diff);
        } else if (this.currentView === 'month') {
            date.setMonth(date.getMonth() + 1, 0);
        }
        return date;
    }

    formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    formatHour(hour) {
        return `${hour.toString().padStart(2, '0')}:00`;
    }

    getWeekNumber(date) {
        const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        const dayNum = d.getUTCDay() || 7;
        d.setUTCDate(d.getUTCDate() + 4 - dayNum);
        const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
        return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
    }

    // Animations et UI
    fadeOutContent(callback) {
        const content = document.querySelector('.agenda-main');
        if (content) {
            content.style.opacity = '0';
            content.style.transform = 'translateY(10px)';
            setTimeout(callback, 200);
        } else {
            callback();
        }
    }

    fadeInContent() {
        const content = document.querySelector('.agenda-main');
        if (content) {
            setTimeout(() => {
                content.style.opacity = '1';
                content.style.transform = 'translateY(0)';
            }, 100);
        }
    }

    animateNavigation(callback) {
        const nav = document.querySelector('.date-navigation-modern');
        if (nav) {
            nav.style.transform = 'scale(0.95)';
            setTimeout(() => {
                callback();
                nav.style.transform = 'scale(1)';
            }, 150);
        } else {
            callback();
        }
    }

    showLoading() {
        const loader = document.createElement('div');
        loader.id = 'agenda-loader';
        loader.innerHTML = `
            <div class="loading-overlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
        `;
        loader.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        document.body.appendChild(loader);
    }

    hideLoading() {
        const loader = document.getElementById('agenda-loader');
        if (loader) {
            loader.remove();
        }
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        const safeMessage = this.escapeHtml(message);
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${safeMessage}</span>
            </div>
        `;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    showError(message) {
        this.showToast(message, 'error');
    }

    showWelcomeAnimation() {
        setTimeout(() => {
            const header = document.querySelector('.agenda-header-modern');
            if (header) {
                header.style.opacity = '0';
                header.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    header.style.transition = 'all 0.5s ease';
                    header.style.opacity = '1';
                    header.style.transform = 'translateY(0)';
                }, 100);
            }
        }, 200);
    }

    updateStats(appointments) {
        // Mettre à jour les statistiques dans le header
        const todayCount = appointments.filter(apt => {
            const aptDate = new Date(apt.start);
            const today = new Date();
            return aptDate.toDateString() === today.toDateString();
        }).length;

        const badge = document.querySelector('.stats-badge');
        if (badge) {
            badge.innerHTML = `<i class="fas fa-calendar-check"></i> ${todayCount} RDV aujourd'hui`;
        }
    }
}

// Styles pour les toasts
const toastStyles = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }

    .toast-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .toast-content i {
        font-size: 1.1rem;
    }
`;

// Ajouter les styles des toasts
const styleSheet = document.createElement('style');
styleSheet.textContent = toastStyles;
document.head.appendChild(styleSheet);

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.agenda = new ModernMedicalAgenda();
});
