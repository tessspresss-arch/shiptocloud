function select(root, selector) {
    return root.querySelector(selector);
}

let agendaAbortController = null;

function parsePayload(root) {
    const node = root.querySelector('#agendaPagePayload');
    if (!node) return null;
    try { return JSON.parse(node.textContent || '{}'); } catch { return null; }
}

export function initAgendaPage(root = document) {
    const payload = parsePayload(root);
    const agendaShell = root.querySelector('.agenda-shell');
    if (!payload || !agendaShell || agendaShell.dataset.medisysBound === '1') return;
    agendaShell.dataset.medisysBound = '1';

    if (agendaAbortController) agendaAbortController.abort();
    agendaAbortController = new AbortController();
    const signal = agendaAbortController.signal;

    let currentView = payload.currentView;
    let currentWeekLayout = payload.weekLayout || 'standard';
    const initialDate = String(payload.selectedDate || '').split('-').map(Number);
    let currentDate = new Date(initialDate[0], (initialDate[1] || 1) - 1, initialDate[2] || 1);
    const agendaBaseUrl = payload.agendaBaseUrl;
    const createBaseUrl = payload.createBaseUrl;
    const eventDates = new Set(payload.daysWithAppointments || []);
    const selectedMedecinId = String(payload.selectedMedecinId || '');
    const selectedStatut = String(payload.selectedStatut || '');
    const searchTerm = String(payload.searchTerm || '');
    const monthNames = ['janvier','fevrier','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','decembre'];
    const weekNames = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];

    const currentDateLabel = select(root, '#currentDate');
    const miniMonthLabel = select(root, '#miniMonthLabel');
    const miniCalendarDays = select(root, '#miniCalendarDays');
    const todayBtn = select(root, '#todayBtn');
    const newRdvBtn = select(root, '#newRdvBtn');

    const formatIsoDate = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

    function formatDateLabel(date) {
        if (currentView === 'month') {
            return monthNames[date.getMonth()].charAt(0).toUpperCase() + monthNames[date.getMonth()].slice(1) + ' ' + date.getFullYear();
        }
        if (currentView === 'week') {
            const start = new Date(date);
            const day = start.getDay() || 7;
            start.setDate(start.getDate() - day + 1);
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            return (currentWeekLayout === 'dense' ? 'Planning dense du ' : 'Semaine du ')
                + String(start.getDate()).padStart(2, '0') + ' ' + monthNames[start.getMonth()]
                + ' au ' + String(end.getDate()).padStart(2, '0') + ' ' + monthNames[end.getMonth()] + ' ' + end.getFullYear();
        }
        const dayName = weekNames[date.getDay()];
        return dayName.charAt(0).toUpperCase() + dayName.slice(1) + ' ' + date.getDate() + ' ' + monthNames[date.getMonth()] + ' ' + date.getFullYear();
    }

    function buildAgendaUrl(date = currentDate) {
        const url = new URL(agendaBaseUrl, window.location.origin);
        url.searchParams.set('date', formatIsoDate(date));
        url.searchParams.set('view', currentView);
        if (currentView === 'week' && currentWeekLayout === 'dense') url.searchParams.set('layout', 'dense');
        if (selectedMedecinId) url.searchParams.set('medecin_id', selectedMedecinId);
        if (selectedStatut) url.searchParams.set('statut', selectedStatut);
        if (searchTerm) url.searchParams.set('search', searchTerm);
        return url.toString();
    }

    function navigateToDate(date = currentDate) { window.location.href = buildAgendaUrl(date); }
    function buildCreateUrl(heure = '09:00', dateValue = currentDate) {
        const dateParam = dateValue instanceof Date ? formatIsoDate(dateValue) : dateValue;
        const url = new URL(createBaseUrl, window.location.origin);
        url.searchParams.set('date', dateParam);
        url.searchParams.set('heure', heure);
        return url.toString();
    }
    function redirectToCreate(heure = '09:00', dateValue = currentDate) { window.location.href = buildCreateUrl(heure, dateValue); }
    function updateDateDisplay() { if (currentDateLabel) currentDateLabel.textContent = formatDateLabel(currentDate); syncCreateLinks(); }

    function renderMiniCalendar() {
        if (!miniMonthLabel || !miniCalendarDays) return;
        const y = currentDate.getFullYear();
        const m = currentDate.getMonth();
        const first = new Date(y, m, 1);
        const last = new Date(y, m + 1, 0);
        miniMonthLabel.textContent = monthNames[m].charAt(0).toUpperCase() + monthNames[m].slice(1) + ' ' + y;
        miniCalendarDays.innerHTML = '';
        let start = first.getDay();
        start = start === 0 ? 6 : start - 1;
        for (let i = 0; i < start; i += 1) {
            const pad = document.createElement('div');
            pad.className = 'mini-day pad';
            miniCalendarDays.appendChild(pad);
        }
        for (let d = 1; d <= last.getDate(); d += 1) {
            const day = document.createElement('div');
            const compare = new Date(y, m, d);
            const dateKey = formatIsoDate(compare);
            day.className = 'mini-day'
                + (compare.toDateString() === new Date().toDateString() ? ' today' : '')
                + (compare.toDateString() === currentDate.toDateString() ? ' selected' : '')
                + (eventDates.has(dateKey) ? ' has-event' : '');
            day.textContent = d;
            day.addEventListener('click', () => { currentDate = compare; navigateToDate(currentDate); });
            miniCalendarDays.appendChild(day);
        }
    }

    function syncCreateLinks() {
        if (newRdvBtn) newRdvBtn.setAttribute('href', buildCreateUrl('09:00'));
        root.querySelectorAll('.quick-add-link').forEach((link) => {
            link.setAttribute('href', buildCreateUrl(link.dataset.hour || '09:00', link.dataset.date || formatIsoDate(currentDate)));
        });
    }

    root.querySelectorAll('.view-btn').forEach((btn) => btn.addEventListener('click', function () {
        currentView = this.dataset.view;
        if (currentView !== 'week') currentWeekLayout = 'standard';
        navigateToDate(currentDate);
    }));
    todayBtn?.addEventListener('click', () => { currentDate = new Date(); navigateToDate(currentDate); });
    root.querySelectorAll('.quick-add-link').forEach((btn) => btn.addEventListener('click', function (event) {
        event.preventDefault();
        redirectToCreate(this.dataset.hour || '09:00', this.dataset.date || formatIsoDate(currentDate));
    }));
    root.querySelectorAll('.timeline-slot.is-empty[data-create-url]').forEach((slot) => {
        const openQuickCreate = (event) => {
            if (event.target.closest('.quick-add-link')) return;
            window.location.href = slot.dataset.createUrl;
        };
        slot.addEventListener('click', openQuickCreate);
        slot.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openQuickCreate(event); }
        });
    });

    const closeDenseContextMenus = () => root.querySelectorAll('.dense-context-menu.is-open').forEach((menu) => {
        menu.classList.remove('is-open');
        menu.style.left = '';
        menu.style.top = '';
    });

    root.querySelectorAll('.dense-rdv-card[data-open-url]').forEach((card) => {
        const openUrl = card.dataset.openUrl;
        const menuId = card.dataset.contextTarget;
        const contextMenu = menuId ? document.getElementById(menuId) : null;
        card.addEventListener('click', (event) => {
            if (event.target.closest('[data-stop-open]') || event.target.closest('.dense-context-menu')) return;
            window.location.href = openUrl;
        });
        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location.href = openUrl; }
        });
        card.addEventListener('contextmenu', (event) => {
            if (!contextMenu) return;
            event.preventDefault();
            closeDenseContextMenus();
            contextMenu.classList.add('is-open');
            const maxLeft = window.innerWidth - contextMenu.offsetWidth - 12;
            const maxTop = window.innerHeight - contextMenu.offsetHeight - 12;
            contextMenu.style.left = Math.max(12, Math.min(event.clientX, maxLeft)) + 'px';
            contextMenu.style.top = Math.max(12, Math.min(event.clientY, maxTop)) + 'px';
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.dense-context-menu') && !event.target.closest('.dense-rdv-card')) closeDenseContextMenus();
    }, { signal });
    document.addEventListener('scroll', closeDenseContextMenus, { capture: true, signal });
    window.addEventListener('resize', closeDenseContextMenus, { signal });
    newRdvBtn?.addEventListener('click', (event) => { event.preventDefault(); redirectToCreate('09:00'); });

    updateDateDisplay();
    renderMiniCalendar();
    syncCreateLinks();
    if (window.bootstrap?.Tooltip) {
        root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => window.bootstrap.Tooltip.getOrCreateInstance(element));
    }
}
