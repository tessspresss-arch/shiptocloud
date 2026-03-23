document.addEventListener("DOMContentLoaded", () => {
    const app = document.getElementById("waiting-room-app");
    if (!app) return;

    const endpoint = app.dataset.endpoint || "/agenda/waiting-room-data";
    const statusEndpointBase = app.dataset.statusEndpointBase || "/rendezvous";
    const rendezvousBase = app.dataset.rendezvousBase || "/rendezvous";
    const patientsBase = app.dataset.patientsBase || "/patients";
    const smsCreateUrl = app.dataset.smsCreateUrl || "/sms/create";
    const consultationsCreateUrl = app.dataset.consultationsCreateUrl || "/consultations/create";
    const ordonnancesCreateUrl = app.dataset.ordonnancesCreateUrl || "/ordonnances/create";
    const documentsUrl = app.dataset.documentsUrl || "/documents/upload";
    const isTvMode = app.dataset.tvMode === "1";
    const liveIntervalMs = Number(app.dataset.liveIntervalMs || (isTvMode ? 4000 : 5000));
    const standardConsultationMinutes = Number(app.dataset.standardConsultationMinutes || 30);

    const refs = {
        medecin: document.getElementById("wr-medecin"),
        date: document.getElementById("wr-date"),
        status: document.getElementById("wr-status"),
        motif: document.getElementById("wr-motif"),
        search: document.getElementById("wr-search"),
        refresh: document.getElementById("wr-refresh"),
        clear: document.getElementById("wr-clear-filters"),
        toast: document.getElementById("wr-toast"),
        tvBody: document.getElementById("wr-tv-body"),
        tvNextName: document.getElementById("wr-tv-next-name"),
        tvNextMeta: document.getElementById("wr-tv-next-meta"),
        lastSync: document.getElementById("wr-last-sync"),
        nextCallCard: document.getElementById("wr-next-call-card"),
        nextCallAvatar: document.getElementById("wr-next-call-avatar"),
        nextCallName: document.getElementById("wr-next-call-name"),
        nextCallDetails: document.getElementById("wr-next-call-details"),
        nextCallStatus: document.getElementById("wr-next-call-status"),
        metricWaitingCount: document.getElementById("wr-metric-waiting-count"),
        metricAvgWait: document.getElementById("wr-metric-avg-wait"),
        metricFinished: document.getElementById("wr-metric-finished"),
        metricDelays: document.getElementById("wr-metric-delays"),
    };

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "";

    const statuses = {
        a_venir: { label: "A venir", empty: "Aucun patient a venir." },
        en_attente: { label: "En attente", empty: "Aucun patient en attente." },
        en_soins: { label: "En consultation", empty: "Aucun patient en consultation." },
        vu: { label: "Termine", empty: "Aucun patient vu pour le moment." },
        absent: { label: "Absent", empty: "Aucun patient absent." },
    };

    let fetchController = null;
    let pollHandle = null;
    let bc = null;

    const actionRules = {
        a_venir: [
            { key: "dossier", label: "Voir dossier patient", icon: "fa-folder-open", tone: "soft" },
            { key: "sms", label: "Envoyer SMS", icon: "fa-sms", tone: "soft" },
            { key: "call", label: "Marquer patient arrive", icon: "fa-check-circle", nextStatus: "en_attente", tone: "success" },
            { key: "consultation", label: "Creer consultation", icon: "fa-plus-square", tone: "primary" },
            { key: "ordonnance", label: "Ajouter ordonnance", icon: "fa-file-medical", tone: "soft" },
            { key: "documents", label: "Ajouter document", icon: "fa-file-upload", tone: "soft" },
            { key: "edit", label: "Reporter ou modifier", icon: "fa-edit", tone: "soft" },
            { key: "note", label: "Ajouter une note", icon: "fa-comment-alt", tone: "soft" },
            { key: "cancel", label: "Annuler le rendez-vous", icon: "fa-calendar-times", nextStatus: "annule", confirm: true, tone: "danger" },
        ],
        en_attente: [
            { key: "dossier", label: "Voir dossier patient", icon: "fa-folder-open", tone: "soft" },
            { key: "sms", label: "Envoyer SMS", icon: "fa-sms", tone: "soft" },
            { key: "start", label: "Commencer consultation", icon: "fa-play", nextStatus: "en_soins", tone: "primary" },
            { key: "consultation", label: "Creer consultation", icon: "fa-plus-square", tone: "success" },
            { key: "ordonnance", label: "Ajouter ordonnance", icon: "fa-file-medical", tone: "soft" },
            { key: "documents", label: "Ajouter document", icon: "fa-file-upload", tone: "soft" },
            { key: "edit", label: "Reporter ou modifier", icon: "fa-edit", tone: "soft" },
            { key: "note", label: "Ajouter une note", icon: "fa-comment-alt", tone: "soft" },
            { key: "absent", label: "Marquer absent", icon: "fa-user-times", nextStatus: "absent", confirm: true, tone: "warning" },
            { key: "cancel", label: "Annuler le rendez-vous", icon: "fa-calendar-times", nextStatus: "annule", confirm: true, tone: "danger" },
        ],
        en_soins: [
            { key: "dossier", label: "Voir dossier patient", icon: "fa-folder-open", tone: "soft" },
            { key: "sms", label: "Envoyer SMS", icon: "fa-sms", tone: "soft" },
            { key: "finish", label: "Terminer consultation", icon: "fa-check-double", nextStatus: "vu", tone: "success" },
            { key: "consultation", label: "Creer consultation", icon: "fa-plus-square", tone: "primary" },
            { key: "ordonnance", label: "Ajouter ordonnance", icon: "fa-file-medical", tone: "soft" },
            { key: "documents", label: "Ajouter document", icon: "fa-file-upload", tone: "soft" },
            { key: "edit", label: "Reporter ou modifier", icon: "fa-edit", tone: "soft" },
            { key: "note", label: "Ajouter une note", icon: "fa-comment-alt", tone: "soft" },
        ],
        vu: [
            { key: "dossier", label: "Voir dossier patient", icon: "fa-folder-open", tone: "soft" },
            { key: "sms", label: "Envoyer SMS", icon: "fa-sms", tone: "soft" },
            { key: "consultation", label: "Creer consultation", icon: "fa-plus-square", tone: "primary" },
            { key: "ordonnance", label: "Ajouter ordonnance", icon: "fa-file-medical", tone: "soft" },
            { key: "documents", label: "Ajouter document", icon: "fa-file-upload", tone: "soft" },
            { key: "edit", label: "Modifier le rendez-vous", icon: "fa-edit", tone: "soft" },
            { key: "note", label: "Ajouter une note", icon: "fa-comment-alt", tone: "soft" },
        ],
        absent: [
            { key: "dossier", label: "Voir dossier patient", icon: "fa-folder-open", tone: "soft" },
            { key: "sms", label: "Envoyer SMS", icon: "fa-sms", tone: "soft" },
            { key: "call", label: "Marquer arrive", icon: "fa-redo-alt", nextStatus: "en_attente", tone: "primary" },
            { key: "documents", label: "Ajouter document", icon: "fa-file-upload", tone: "soft" },
            { key: "edit", label: "Reporter ou modifier", icon: "fa-edit", tone: "soft" },
            { key: "note", label: "Ajouter une note", icon: "fa-comment-alt", tone: "soft" },
            { key: "cancel", label: "Annuler le rendez-vous", icon: "fa-calendar-times", nextStatus: "annule", confirm: true, tone: "danger" },
        ],
    };

    const allowedDragTransitions = {
        a_venir: ["en_attente", "absent"],
        en_attente: ["en_soins", "absent"],
        en_soins: ["vu"],
        vu: [],
        absent: ["en_attente"],
    };

    function debounce(fn, delay = 350) {
        let timer = null;
        return (...args) => {
            if (timer) window.clearTimeout(timer);
            timer = window.setTimeout(() => fn(...args), delay);
        };
    }

    function buildQueryParams() {
        const params = new URLSearchParams();
        if (refs.date?.value) params.set("date", refs.date.value);
        if (refs.medecin?.value) params.set("medecin_id", refs.medecin.value);
        if (refs.status?.value && refs.status.value !== "all") params.set("status", refs.status.value);
        if (refs.motif?.value.trim()) params.set("motif", refs.motif.value.trim());
        if (refs.search?.value.trim()) params.set("search", refs.search.value.trim());
        return params;
    }

    async function fetchData() {
        try {
            if (fetchController) fetchController.abort();
            fetchController = new AbortController();

            const response = await fetch(`${endpoint}?${buildQueryParams().toString()}`, {
                method: "GET",
                credentials: "same-origin",
                signal: fetchController.signal,
            });

            if (!response.ok) {
                throw new Error(`Impossible de charger les donnees (${response.status})`);
            }

            const payload = await response.json();
            render(payload);
        } catch (error) {
            if (error.name !== "AbortError") {
                showToast(error.message || "Erreur de synchronisation", "error");
            }
        }
    }

    function render(payload) {
        const lists = payload?.lists || {};

        Object.keys(statuses).forEach((status) => {
            const items = Array.isArray(lists[status]) ? lists[status] : [];

            const column = document.querySelector(`.wr-column[data-status="${status}"]`);
            if (!column) return;

            const countEl = column.querySelector(".wr-column-count");
            const listEl = column.querySelector(`.wr-list[data-status="${status}"]`);
            if (countEl) countEl.textContent = String(items.length);

            if (!listEl) return;
            listEl.innerHTML = "";

            if (items.length === 0) {
                const empty = document.createElement("div");
                empty.className = "wr-empty-state";
                empty.textContent = statuses[status].empty;
                listEl.appendChild(empty);
                return;
            }

            items.forEach((item, index) => listEl.appendChild(buildCard(item, status, index + 1)));
        });

        renderSummary(lists);
        bindDragAndDrop();
        renderTvBoard(lists);
        updateLastSync(payload?.meta?.refreshed_at);
    }

    function buildCard(item, status, queueNumber = null) {
        const card = document.createElement("article");
        card.className = `wr-patient-card status-${status}`;
        card.dataset.rdvId = item.id;
        card.dataset.currentStatus = status;
        if (!isTvMode) {
            card.setAttribute("draggable", "true");
        }

        const actions = actionRules[status] || [];
        const timeIndicators = buildTimeIndicators(item, status);
        const flags = [];
        if (item?.is_urgent) {
            flags.push('<span class="wr-flag wr-flag-urgent">Urgent</span>');
        }
        if (timeIndicators.waitAlert) {
            flags.push('<span class="wr-flag wr-flag-late">Attente > 20 min</span>');
        }
        if (timeIndicators.consultationAlert) {
            flags.push('<span class="wr-flag wr-flag-overrun">Consultation prolongee</span>');
        }
        if (timeIndicators.delayMinutes > 0) {
            flags.push(`<span class="wr-flag wr-flag-late">Retard ${escapeHtml(formatDuration(timeIndicators.delayMinutes))}</span>`);
        }
        const flagsMarkup = flags.length > 0 ? `<div class="wr-card-flags">${flags.join("")}</div>` : "";
        const motifText = item?.motif || item?.type || "Consultation";
        const doctorText = item?.medecin || "Non assigne";
        const avatarMarkup = getPatientAvatarMarkup(item);

        card.innerHTML = `
            <div class="wr-card-accent" aria-hidden="true"></div>
            <div class="wr-card-top">
                <div class="wr-card-identity">
                    ${avatarMarkup}
                    <div class="wr-patient-meta">
                        <p class="wr-patient-name">${escapeHtml(item.patient || "Patient")}</p>
                        <p class="wr-patient-id">${escapeHtml(item.patient_dossier || item.patient_cin || "Dossier patient")}</p>
                    </div>
                </div>
                <span class="wr-badge-status" data-status="${status}">${escapeHtml(statuses[status]?.label || status)}</span>
            </div>
            ${flagsMarkup}
            <div class="wr-card-primary">
                <div class="wr-primary-row">
                    <span class="wr-primary-label">Motif</span>
                    <span class="wr-primary-value">${escapeHtml(motifText)}</span>
                </div>
                <div class="wr-primary-row">
                    <span class="wr-primary-label">Heure du RDV</span>
                    <span class="wr-primary-value">${escapeHtml(item.heure || "--:--")}</span>
                </div>
            </div>
            <div class="wr-card-secondary">
                <div class="wr-secondary-chip">
                    <i class="fas fa-user-doctor"></i>
                    ${escapeHtml(doctorText)}
                </div>
                <div class="wr-secondary-chip">
                    <i class="fas fa-clock"></i>
                    ${escapeHtml(item.heure || "--:--")}
                </div>
            </div>
            <div class="wr-card-timing">${timeIndicators.markup}</div>
            <div class="wr-card-divider"></div>
            <div class="wr-card-actions" aria-label="Actions rapides"></div>
        `;

        const avatarImage = card.querySelector(".wr-avatar img");
        if (avatarImage) {
            avatarImage.addEventListener("error", () => {
                const avatar = avatarImage.closest(".wr-avatar");
                if (!avatar) return;
                avatar.classList.add("is-fallback");
                avatarImage.remove();
            }, { once: true });
        }

        const actionsContainer = card.querySelector(".wr-card-actions");
        actions.forEach((action) => {
            const button = document.createElement("button");
            button.type = "button";
            button.className = `wr-btn-action tone-${action.tone || "soft"}`;
            button.dataset.action = action.key;
            button.dataset.tooltip = action.label;
            button.setAttribute("title", action.label);
            button.setAttribute("aria-label", action.label);
            button.innerHTML = `
                <span class="wr-btn-icon"><i class="fas ${action.icon}"></i></span>
                <span class="wr-sr-only">${escapeHtml(action.label)}</span>
            `;
            button.addEventListener("click", () => handleAction(item, action));
            actionsContainer.appendChild(button);
        });

        if (!isTvMode) {
            card.addEventListener("dragstart", (event) => {
                card.classList.add("is-dragging");
                const payload = JSON.stringify({
                    id: item.id,
                    patient: item.patient || "Patient",
                    currentStatus: status,
                });
                event.dataTransfer.setData("application/json", payload);
                event.dataTransfer.effectAllowed = "move";
            });

            card.addEventListener("dragend", () => {
                card.classList.remove("is-dragging");
                document.querySelectorAll(".wr-list.is-drop-target").forEach((list) => {
                    list.classList.remove("is-drop-target");
                });
            });
        }

        return card;
    }

    function getPatientAvatarMarkup(item) {
        const photoUrl = typeof item?.patient_photo_url === "string" ? item.patient_photo_url.trim() : "";
        const initials = getPatientInitials(item);
        const fallbackClass = photoUrl ? "" : " is-fallback";

        return `
            <span class="wr-avatar${fallbackClass}" aria-hidden="true">
                <span class="wr-avatar-fallback"><i class="fas fa-user"></i><strong>${escapeHtml(initials)}</strong></span>
                ${photoUrl ? `<img src="${escapeHtml(photoUrl)}" alt="">` : ""}
            </span>
        `;
    }

    function getPatientInitials(item) {
        const explicit = String(item?.patient_initials || "").trim();
        if (explicit) {
            return explicit.slice(0, 2).toUpperCase();
        }

        const name = String(item?.patient || "").trim();
        if (!name) {
            return "PA";
        }

        return name
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map((part) => part.charAt(0).toUpperCase())
            .join("") || "PA";
    }

    async function handleAction(item, action) {
        if (action.key === "details") {
            window.location.href = `${rendezvousBase}/${item.id}`;
            return;
        }

        if (action.key === "dossier") {
            if (!item?.patient_id) {
                showToast("Patient indisponible pour ouvrir le dossier.", "error");
                return;
            }
            window.location.href = `${patientsBase}/${item.patient_id}`;
            return;
        }

        if (action.key === "edit") {
            window.location.href = `${rendezvousBase}/${item.id}/edit`;
            return;
        }

        if (action.key === "note") {
            window.location.href = `${rendezvousBase}/${item.id}/edit#notes`;
            return;
        }

        if (action.key === "consultation") {
            const params = new URLSearchParams();
            if (item?.patient_id) params.set("patient_id", String(item.patient_id));
            if (item?.medecin_id) params.set("medecin_id", String(item.medecin_id));
            if (item?.id) params.set("rendez_vous_id", String(item.id));
            const query = params.toString();
            window.location.href = `${consultationsCreateUrl}${query ? `?${query}` : ""}`;
            return;
        }

        if (action.key === "sms") {
            const params = new URLSearchParams();
            if (item?.patient_id) params.set("patient_id", String(item.patient_id));
            if (item?.id) params.set("rendezvous_id", String(item.id));
            const query = params.toString();
            window.location.href = `${smsCreateUrl}${query ? `?${query}` : ""}`;
            return;
        }

        if (action.key === "documents") {
            const params = new URLSearchParams();
            if (item?.patient_id) params.set("patient_id", String(item.patient_id));
            const query = params.toString();
            window.location.href = `${documentsUrl}${query ? `?${query}` : ""}`;
            return;
        }

        if (action.key === "ordonnance") {
            const params = new URLSearchParams();
            if (item?.patient_id) params.set("patient_id", String(item.patient_id));
            if (item?.medecin_id) params.set("medecin_id", String(item.medecin_id));
            if (item?.consultation_id) params.set("consultation_id", String(item.consultation_id));
            const query = params.toString();
            window.location.href = `${ordonnancesCreateUrl}${query ? `?${query}` : ""}`;
            return;
        }

        if (action.confirm) {
            const targetLabel = action.nextStatus === "annule"
                ? "annule"
                : (statuses[action.nextStatus]?.label || action.nextStatus);
            const accepted = await askConfirmation(
                "Confirmer l'action",
                `Voulez-vous passer "${item.patient}" en statut "${targetLabel}" ?`
            );
            if (!accepted) return;
        }

        await updateStatus(item.id, action.nextStatus, item.patient);
    }

    async function updateStatus(rdvId, nextStatus, patientName) {
        try {
            const response = await fetch(`${statusEndpointBase}/${rdvId}/status`, {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ statut: nextStatus }),
            });

            const payload = await response.json().catch(() => ({}));
            if (!response.ok || !payload?.success) {
                const message = payload?.error || "Mise a jour impossible.";
                throw new Error(message);
            }

            showToast(`Statut mis a jour pour ${patientName}.`, "success");
            if (bc) {
                bc.postMessage({ type: "waiting-room-updated", rdvId, nextStatus });
            }
            await fetchData();
        } catch (error) {
            showToast(error.message || "Erreur de mise a jour", "error");
        }
    }

    function renderTvBoard(lists) {
        if (!refs.tvBody) return;

        const orderedStatuses = ["en_attente", "en_soins", "a_venir", "vu", "absent"];
        const rows = [];
        orderedStatuses.forEach((status) => {
            const current = Array.isArray(lists?.[status]) ? lists[status] : [];
            current.forEach((item) => rows.push({ ...item, _status: status }));
        });

        const nextPatient = pickNextPatient(lists);
        if (refs.tvNextName) {
            refs.tvNextName.textContent = nextPatient ? (nextPatient.patient || "Patient") : "Aucun patient selectionne";
        }
        if (refs.tvNextMeta) {
            refs.tvNextMeta.textContent = nextPatient
                ? `${statuses[nextPatient._status]?.label || nextPatient._status} - ${nextPatient.heure || "--:--"} - ${nextPatient.medecin || "Medecin non assigne"}`
                : "Le prochain appel s'affichera ici.";
        }

        refs.tvBody.innerHTML = "";
        if (rows.length === 0) {
            const tr = document.createElement("tr");
            tr.innerHTML = `<td colspan="6">Aucun patient pour les filtres selectionnes.</td>`;
            refs.tvBody.appendChild(tr);
            return;
        }

        rows.forEach((item, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${escapeHtml(item.patient || "-")}</td>
                <td>${escapeHtml(item.heure || "--:--")}</td>
                <td>${escapeHtml(item.medecin || "-")}</td>
                <td>${escapeHtml(item.salle || "Consultation")}</td>
                <td><span class="wr-tv-status-pill wr-badge-status" data-status="${item._status}">${escapeHtml(statuses[item._status]?.label || item._status)}</span></td>
            `;
            refs.tvBody.appendChild(tr);
        });
    }

    function buildTimeIndicators(item, status) {
        const timeData = computeTimeData(item, status);
        const indicators = [];

        if (status === "a_venir") {
            indicators.push(renderTimeChip(
                "Avant RDV",
                timeData.remainingMinutes > 0 ? `Dans ${formatDuration(timeData.remainingMinutes)}` : "Maintenant",
                timeData.delayMinutes > 0 ? "time-warning" : "time-ok",
                "fa-calendar-day"
            ));
        }

        if (status === "en_attente" || status === "en_soins" || status === "vu") {
            indicators.push(renderTimeChip(
                "Attente",
                formatDuration(timeData.waitingMinutes),
                waitSeverityClass(timeData.waitingMinutes, 20),
                "fa-hourglass-half"
            ));
        }

        if (status === "en_soins") {
            indicators.push(renderTimeChip(
                "Consultation",
                timeData.consultationMinutes > 0 ? formatDuration(timeData.consultationMinutes) : "Debut recent",
                waitSeverityClass(timeData.consultationMinutes, standardConsultationMinutes),
                "fa-stethoscope"
            ));
        }

        if (status === "vu") {
            indicators.push(renderTimeChip(
                "Consultation",
                timeData.consultationMinutes > 0 ? formatDuration(timeData.consultationMinutes) : "Terminee",
                "time-ok",
                "fa-stethoscope"
            ));
        }

        if (timeData.delayMinutes > 0) {
            indicators.push(renderTimeChip(
                "Retard",
                formatDuration(timeData.delayMinutes),
                waitSeverityClass(timeData.delayMinutes, 20),
                "fa-triangle-exclamation"
            ));
        }

        return {
            ...timeData,
            markup: indicators.join(""),
            waitAlert: timeData.waitingMinutes > 20,
            consultationAlert: status === "en_soins" && timeData.consultationMinutes > standardConsultationMinutes,
        };
    }

    function renderTimeChip(label, value, className, icon) {
        return `
            <div class="wr-time-chip ${escapeHtml(className)}">
                <i class="fas ${escapeHtml(icon)}"></i>
                <span>${escapeHtml(label)}</span>
                <strong>${escapeHtml(value)}</strong>
            </div>
        `;
    }

    function computeTimeData(item, status) {
        const scheduledAt = parseDate(item?.date_heure);
        const arrivedAt = parseDate(item?.arrived_at);
        const consultationStartedAt = parseDate(item?.consultation_started_at);
        const consultationFinishedAt = parseDate(item?.consultation_finished_at);
        const now = new Date();

        const remainingMinutes = scheduledAt ? Math.max(0, Math.round((scheduledAt.getTime() - now.getTime()) / 60000)) : 0;
        const delayMinutes = scheduledAt ? Math.max(0, Math.round((now.getTime() - scheduledAt.getTime()) / 60000)) : 0;

        let waitingMinutes = 0;
        if (status === "en_attente") {
            waitingMinutes = arrivedAt
                ? Math.max(0, Math.round((now.getTime() - arrivedAt.getTime()) / 60000))
                : Math.max(0, Number(item?.waiting_minutes ?? delayMinutes));
        } else if (status === "en_soins" || status === "vu") {
            const waitEnd = consultationStartedAt || consultationFinishedAt || now;
            waitingMinutes = arrivedAt
                ? Math.max(0, Math.round((waitEnd.getTime() - arrivedAt.getTime()) / 60000))
                : Math.max(0, Number(item?.waiting_minutes ?? 0));
        }

        let consultationMinutes = 0;
        if (consultationStartedAt) {
            const consultationEnd = status === "vu"
                ? (consultationFinishedAt || consultationStartedAt)
                : (consultationFinishedAt || now);
            consultationMinutes = Math.max(0, Math.round((consultationEnd.getTime() - consultationStartedAt.getTime()) / 60000));
        }

        return {
            remainingMinutes,
            waitingMinutes,
            consultationMinutes,
            delayMinutes: status === "vu" ? 0 : delayMinutes,
        };
    }

    function parseDate(value) {
        if (!value) return null;
        const normalized = String(value).replace(" ", "T");
        const date = new Date(normalized);
        return Number.isNaN(date.getTime()) ? null : date;
    }

    function waitSeverityClass(minutes, warningThreshold = 20) {
        if (!Number.isFinite(minutes) || minutes <= Math.max(5, warningThreshold / 2)) return "time-ok";
        if (minutes <= warningThreshold) return "time-warning";
        return "time-danger";
    }

    function formatDuration(totalMinutes) {
        const safe = Math.max(0, Number(totalMinutes) || 0);
        if (safe < 60) {
            return `${safe} min`;
        }

        const hours = Math.floor(safe / 60);
        const mins = safe % 60;
        return `${hours} h ${mins} min`;
    }

    function renderSummary(lists) {
        const waitingItems = Array.isArray(lists?.en_attente) ? lists.en_attente : [];
        const doneItems = Array.isArray(lists?.vu) ? lists.vu : [];
        const delayedItems = []
            .concat(Array.isArray(lists?.a_venir) ? lists.a_venir : [])
            .concat(waitingItems)
            .concat(Array.isArray(lists?.en_soins) ? lists.en_soins : [])
            .filter((item) => computeTimeData(item, item?.statut || item?._status || "a_venir").delayMinutes > 0);

        const averageWait = waitingItems.length > 0
            ? Math.round(waitingItems.reduce((sum, item) => sum + computeTimeData(item, "en_attente").waitingMinutes, 0) / waitingItems.length)
            : 0;

        if (refs.metricWaitingCount) refs.metricWaitingCount.textContent = String(waitingItems.length);
        if (refs.metricAvgWait) refs.metricAvgWait.textContent = averageWait > 0 ? formatDuration(averageWait) : "0 min";
        if (refs.metricFinished) refs.metricFinished.textContent = String(doneItems.length);
        if (refs.metricDelays) refs.metricDelays.textContent = String(delayedItems.length);

        renderNextPatient(lists);
    }

    function pickNextPatient(lists) {
        const waiting = Array.isArray(lists?.en_attente) ? lists.en_attente : [];
        const consultation = Array.isArray(lists?.en_soins) ? lists.en_soins : [];
        const upcoming = Array.isArray(lists?.a_venir) ? lists.a_venir : [];

        if (waiting.length > 0) return { ...waiting[0], _status: "en_attente" };
        if (consultation.length > 0) return { ...consultation[0], _status: "en_soins" };
        if (upcoming.length > 0) return { ...upcoming[0], _status: "a_venir" };
        return null;
    }

    function renderNextPatient(lists) {
        const nextPatient = pickNextPatient(lists);
        if (!refs.nextCallName || !refs.nextCallDetails || !refs.nextCallStatus || !refs.nextCallAvatar) return;

        if (!nextPatient) {
            refs.nextCallName.textContent = "Aucun patient en file active";
            refs.nextCallDetails.textContent = "La salle d'attente est calme pour le moment.";
            refs.nextCallStatus.textContent = "Disponible";
            refs.nextCallAvatar.innerHTML = "<span>PA</span>";
            return;
        }

        const timeData = computeTimeData(nextPatient, nextPatient._status);
        refs.nextCallName.textContent = nextPatient.patient || "Patient";
        refs.nextCallDetails.textContent = `${nextPatient.heure || "--:--"} - ${nextPatient.medecin || "Medecin non assigne"} - ${nextPatient.motif || "Consultation"}`;
        refs.nextCallStatus.textContent = `${statuses[nextPatient._status]?.label || nextPatient._status}${timeData.delayMinutes > 0 ? ` - retard ${formatDuration(timeData.delayMinutes)}` : ""}`;
        refs.nextCallAvatar.innerHTML = getHeroAvatarMarkup(nextPatient);
        const heroAvatarImg = refs.nextCallAvatar.querySelector("img");
        if (heroAvatarImg) {
            heroAvatarImg.addEventListener("error", () => {
                refs.nextCallAvatar.innerHTML = `<i class="fas fa-user"></i><span>${escapeHtml(getPatientInitials(nextPatient))}</span>`;
            }, { once: true });
        }
        if (refs.nextCallCard) {
            refs.nextCallCard.dataset.status = nextPatient._status || "a_venir";
        }
    }

    function getHeroAvatarMarkup(item) {
        const photoUrl = typeof item?.patient_photo_url === "string" ? item.patient_photo_url.trim() : "";
        const initials = getPatientInitials(item);
        if (photoUrl) {
            return `<img src="${escapeHtml(photoUrl)}" alt="${escapeHtml(item?.patient || "Patient")}">`;
        }
        return `<i class="fas fa-user"></i><span>${escapeHtml(initials)}</span>`;
    }

    function isValidTransition(currentStatus, targetStatus) {
        if (!currentStatus || !targetStatus || currentStatus === targetStatus) {
            return false;
        }
        return (allowedDragTransitions[currentStatus] || []).includes(targetStatus);
    }

    function bindDragAndDrop() {
        if (isTvMode) return;
        document.querySelectorAll(".wr-list[data-status]").forEach((list) => {
            if (list.dataset.dndBound === "1") {
                return;
            }
            list.dataset.dndBound = "1";

            list.addEventListener("dragover", (event) => {
                event.preventDefault();
                list.classList.add("is-drop-target");
            });

            list.addEventListener("dragleave", () => {
                list.classList.remove("is-drop-target");
            });

            list.addEventListener("drop", async (event) => {
                event.preventDefault();
                list.classList.remove("is-drop-target");

                const raw = event.dataTransfer.getData("application/json");
                if (!raw) return;

                let payload = null;
                try {
                    payload = JSON.parse(raw);
                } catch (e) {
                    return;
                }

                const targetStatus = list.dataset.status;
                const currentStatus = payload?.currentStatus;
                if (!payload?.id || !targetStatus) return;

                if (!isValidTransition(currentStatus, targetStatus)) {
                    showToast("Transition non autorisee pour ce patient.", "error");
                    return;
                }

                await updateStatus(payload.id, targetStatus, payload.patient || "Patient");
            });
        });
    }

    function updateLastSync(isoString) {
        if (!refs.lastSync) return;
        if (!isoString) {
            refs.lastSync.textContent = "Synchronisation recente";
            return;
        }
        const date = new Date(isoString);
        if (Number.isNaN(date.getTime())) {
            refs.lastSync.textContent = "Synchronisation recente";
            return;
        }
        refs.lastSync.textContent = `Derniere synchro: ${date.toLocaleTimeString("fr-FR", {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
        })}`;
    }

    function showToast(message, type = "success") {
        if (!refs.toast) return;
        refs.toast.textContent = message;
        refs.toast.classList.remove("success", "error", "show");
        refs.toast.classList.add(type === "error" ? "error" : "success");

        window.clearTimeout(showToast._timer);
        window.requestAnimationFrame(() => refs.toast.classList.add("show"));
        showToast._timer = window.setTimeout(() => refs.toast.classList.remove("show"), 2800);
    }

    function askConfirmation(title, text) {
        if (window.Swal) {
            return window.Swal.fire({
                icon: "warning",
                title,
                text,
                showCancelButton: true,
                confirmButtonText: "Confirmer",
                cancelButtonText: "Annuler",
                reverseButtons: true,
            }).then((result) => result.isConfirmed);
        }
        return Promise.resolve(window.confirm(`${title}\n\n${text}`));
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return "";
        return String(value).replace(/[&<>"']/g, (char) => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        })[char]);
    }

    function clearFilters() {
        if (refs.medecin) refs.medecin.value = "all";
        if (refs.status) refs.status.value = "all";
        if (refs.motif) refs.motif.value = "";
        if (refs.search) refs.search.value = "";
        fetchData();
    }

    const debouncedFetch = debounce(fetchData, 350);
    refs.refresh?.addEventListener("click", fetchData);
    refs.clear?.addEventListener("click", clearFilters);
    refs.medecin?.addEventListener("change", fetchData);
    refs.date?.addEventListener("change", fetchData);
    refs.status?.addEventListener("change", fetchData);
    refs.motif?.addEventListener("input", debouncedFetch);
    refs.search?.addEventListener("input", debouncedFetch);

    if ("BroadcastChannel" in window) {
        bc = new BroadcastChannel("medisys-waiting-room");
        bc.addEventListener("message", (event) => {
            if (event?.data?.type === "waiting-room-updated") {
                fetchData();
            }
        });
    }

    fetchData();
    pollHandle = window.setInterval(() => {
        if (document.hidden && !isTvMode) return;
        fetchData();
    }, liveIntervalMs);

    document.addEventListener("visibilitychange", () => {
        if (!document.hidden) {
            fetchData();
        }
    });

    window.addEventListener("focus", fetchData);

    window.addEventListener("beforeunload", () => {
        if (pollHandle) window.clearInterval(pollHandle);
        if (bc) bc.close();
    });
});
