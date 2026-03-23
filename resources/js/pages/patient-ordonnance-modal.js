function parsePayload(root) {
    const node = root.querySelector('#patientOrdonnanceModalPayload');
    if (!node) return null;

    try {
        return JSON.parse(node.textContent || '{}');
    } catch {
        return null;
    }
}

export function initPatientOrdonnanceModal(root = document) {
    const payload = parsePayload(root);
    const modal = root.querySelector('#modal-ordonnance');
    const form = root.querySelector('#patientOrdonnanceModalForm');

    if (!payload || !modal || !form || form.dataset.medisysBound === '1') {
        return;
    }

    form.dataset.medisysBound = '1';

    const rowTemplate = root.querySelector('#ordonnanceQuickRowTemplate');
    const rowsContainer = root.querySelector('#ordonnanceQuickRows');
    const addRowButton = root.querySelector('#ordonnanceQuickAddRow');
    const medecinSelect = root.querySelector('#ordonnanceQuickMedecin');
    const instructionsInput = root.querySelector('#ordonnanceQuickInstructions');
    const errorBox = root.querySelector('#ordonnanceQuickErrors');
    const successBox = root.querySelector('#ordonnanceQuickSuccess');
    const submitButton = root.querySelector('#ordonnanceQuickSubmit');
    const submitLabel = root.querySelector('#ordonnanceQuickSubmitLabel');
    const medicationCatalog = Array.isArray(payload.medicamentCatalog) ? payload.medicamentCatalog : [];
    const defaultMedecinId = payload.defaultMedecinId ? String(payload.defaultMedecinId) : '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    let rowIndex = 0;

    const normalize = (value) => (value || '')
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();

    const escapeHtml = (value) => (value || '')
        .toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const getMedicationById = (id) => medicationCatalog.find((item) => String(item.id) === String(id)) || null;
    const getMedicationSearch = (item) => item.search || normalize([
        item.label,
        item.nom_commercial,
        item.dci,
        item.presentation,
        item.classe_therapeutique,
    ].filter(Boolean).join(' '));

    function inputNameFromErrorKey(key) {
        return String(key || '')
            .split('.')
            .reduce((name, segment, index) => (index === 0 ? segment : `${name}[${segment}]`), '');
    }

    function clearFeedback() {
        errorBox?.classList.add('hidden');
        successBox?.classList.add('hidden');

        if (errorBox) {
            errorBox.innerHTML = '';
        }

        if (successBox) {
            successBox.innerHTML = '';
        }

        form.querySelectorAll('.is-invalid').forEach((field) => {
            field.classList.remove('is-invalid');
        });
    }

    function showGenericError(message) {
        if (!errorBox) {
            return;
        }

        errorBox.innerHTML = `<strong>Enregistrement impossible.</strong><div>${escapeHtml(message)}</div>`;
        errorBox.classList.remove('hidden');
    }

    function showValidationErrors(errors) {
        clearFeedback();

        const messages = [];
        const fields = Array.from(form.querySelectorAll('input, select, textarea'));

        Object.entries(errors || {}).forEach(([key, value]) => {
            const name = inputNameFromErrorKey(key);
            const list = Array.isArray(value) ? value : [value];

            list.filter(Boolean).forEach((message) => {
                messages.push(message);
            });

            fields
                .filter((field) => field.name === name || field.dataset.errorProxy === name)
                .forEach((field) => field.classList.add('is-invalid'));
        });

        if (!errorBox) {
            return;
        }

        const uniqueMessages = [...new Set(messages)];
        errorBox.innerHTML = `<strong>Merci de corriger les champs suivants.</strong>${uniqueMessages.map((message) => `<div>${escapeHtml(message)}</div>`).join('')}`;
        errorBox.classList.remove('hidden');
    }

    function setSubmitting(isSubmitting) {
        if (submitButton) {
            submitButton.disabled = isSubmitting;
        }

        if (submitLabel) {
            submitLabel.textContent = isSubmitting ? 'Enregistrement...' : 'Enregistrer';
        }
    }

    function refreshRowNumbers() {
        Array.from(rowsContainer?.querySelectorAll('.ord-quick-row') || []).forEach((row, index) => {
            const label = row.querySelector('[data-role="row-number"]');
            if (label) {
                label.textContent = `#${index + 1}`;
            }
        });
    }

    function syncDerivedFields(row) {
        const dosageInput = row.querySelector('[data-role="dosage"]');
        const frequencyInput = row.querySelector('[data-role="frequency"]');
        const posologieInput = row.querySelector('[data-role="posologie"]');
        const dosageHiddenInput = row.querySelector('[data-role="dosage-hidden"]');
        const frequencyHiddenInput = row.querySelector('[data-role="frequency-hidden"]');
        const dosage = dosageInput?.value.trim() || '';
        const frequency = frequencyInput?.value.trim() || '';

        if (posologieInput) {
            posologieInput.value = [dosage, frequency].filter(Boolean).join(' - ');
        }

        if (dosageHiddenInput) {
            dosageHiddenInput.value = dosage;
        }

        if (frequencyHiddenInput) {
            frequencyHiddenInput.value = frequency;
        }
    }

    function renderMedicationMeta(row) {
        const meta = row.querySelector('[data-role="medication-meta"]');
        const medicationId = row.querySelector('[data-role="medication-id"]')?.value || '';
        const medication = getMedicationById(medicationId);

        if (!meta) {
            return;
        }

        if (!medication) {
            meta.textContent = 'Saisissez un libelle libre ou selectionnez un medicament du catalogue.';
            return;
        }

        const details = [
            medication.presentation ? `Presentation : ${medication.presentation}` : null,
            medication.posologie ? `Posologie type : ${medication.posologie}` : null,
            medication.voie_administration ? `Voie : ${medication.voie_administration}` : null,
            medication.classe_therapeutique ? `Classe : ${medication.classe_therapeutique}` : null,
        ].filter(Boolean);

        meta.textContent = details.join(' | ') || 'Medicament selectionne.';
    }

    function renderMedicationResults(row, query) {
        const results = row.querySelector('[data-role="medication-results"]');
        if (!results) {
            return;
        }

        const normalizedQuery = normalize(query);
        const matches = normalizedQuery === ''
            ? medicationCatalog.slice(0, 8)
            : medicationCatalog.filter((item) => getMedicationSearch(item).includes(normalizedQuery)).slice(0, 8);

        if (!matches.length) {
            results.innerHTML = '<div class="ord-quick-empty-search">Aucun medicament trouve.</div>';
            results.classList.add('is-open');
            return;
        }

        results.innerHTML = matches.map((item) => `
            <button type="button" class="ord-quick-result" data-medication-id="${escapeHtml(item.id)}">
                <strong>${escapeHtml(item.nom_commercial || item.label || 'Medicament')}</strong>
                <small>${escapeHtml([item.dci || null, item.presentation || null].filter(Boolean).join(' | ') || 'Libelle catalogue')}</small>
            </button>
        `).join('');
        results.classList.add('is-open');
    }

    function selectMedication(row, medicationId) {
        const medication = getMedicationById(medicationId);
        const medicationIdInput = row.querySelector('[data-role="medication-id"]');
        const medicationLabelInput = row.querySelector('[data-role="medication-label"]');
        const results = row.querySelector('[data-role="medication-results"]');

        if (!medication) {
            if (medicationIdInput) {
                medicationIdInput.value = '';
            }

            renderMedicationMeta(row);
            return;
        }

        if (medicationIdInput) {
            medicationIdInput.value = medication.id;
        }

        if (medicationLabelInput) {
            medicationLabelInput.value = medication.label || medication.nom_commercial || '';
            medicationLabelInput.classList.remove('is-invalid');
        }

        results?.classList.remove('is-open');
        renderMedicationMeta(row);
    }

    function ensureAtLeastOneRow() {
        if (!rowsContainer || rowsContainer.children.length > 0) {
            return;
        }

        appendMedicationRow();
    }

    function bindMedicationRow(row) {
        const medicationLabelInput = row.querySelector('[data-role="medication-label"]');
        const medicationIdInput = row.querySelector('[data-role="medication-id"]');
        const dosageInput = row.querySelector('[data-role="dosage"]');
        const frequencyInput = row.querySelector('[data-role="frequency"]');
        const durationInput = row.querySelector('[data-role="duration"]');
        const results = row.querySelector('[data-role="medication-results"]');
        const removeButton = row.querySelector('[data-remove-row]');

        medicationLabelInput?.addEventListener('focus', () => {
            renderMedicationResults(row, medicationLabelInput.value);
        });

        medicationLabelInput?.addEventListener('input', () => {
            if (medicationIdInput) {
                medicationIdInput.value = '';
            }

            medicationLabelInput.classList.remove('is-invalid');
            renderMedicationResults(row, medicationLabelInput.value);
            renderMedicationMeta(row);
            clearFeedback();
        });

        medicationLabelInput?.addEventListener('blur', () => {
            window.setTimeout(() => results?.classList.remove('is-open'), 150);
        });

        results?.addEventListener('click', (event) => {
            const option = event.target.closest('[data-medication-id]');
            if (!option) {
                return;
            }

            event.preventDefault();
            selectMedication(row, option.dataset.medicationId);
            clearFeedback();
        });

        [dosageInput, frequencyInput].forEach((input) => {
            input?.addEventListener('input', () => {
                syncDerivedFields(row);
                input.classList.remove('is-invalid');
                clearFeedback();
            });
        });

        durationInput?.addEventListener('input', () => {
            durationInput.classList.remove('is-invalid');
            clearFeedback();
        });

        removeButton?.addEventListener('click', () => {
            row.remove();
            ensureAtLeastOneRow();
            refreshRowNumbers();
            clearFeedback();
        });

        renderMedicationMeta(row);
        syncDerivedFields(row);
    }

    function appendMedicationRow() {
        if (!rowTemplate || !rowsContainer) {
            return null;
        }

        const index = rowIndex++;
        const html = rowTemplate.innerHTML
            .replaceAll('__INDEX__', String(index))
            .replaceAll('__NUMBER__', String(rowsContainer.children.length + 1));

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const row = wrapper.firstElementChild;

        if (!row) {
            return null;
        }

        rowsContainer.appendChild(row);
        bindMedicationRow(row);
        refreshRowNumbers();
        return row;
    }

    function syncAllRows() {
        rowsContainer?.querySelectorAll('.ord-quick-row').forEach((row) => {
            syncDerivedFields(row);
        });
    }

    function resetFormState() {
        clearFeedback();
        form.reset();

        if (medecinSelect) {
            medecinSelect.value = defaultMedecinId;
        }

        if (instructionsInput) {
            instructionsInput.value = '';
        }

        if (rowsContainer) {
            rowsContainer.innerHTML = '';
        }

        rowIndex = 0;
        appendMedicationRow();
    }

    function updatePrescriptionCounters(count) {
        root.querySelectorAll('[data-prescriptions-count]').forEach((node) => {
            node.textContent = String(count);
        });
    }

    async function submitForm(event) {
        event.preventDefault();
        clearFeedback();
        syncAllRows();

        if (!form.reportValidity()) {
            return;
        }

        setSubmitting(true);

        try {
            const response = await fetch(payload.storeUrl || form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: new FormData(form),
            });

            const contentType = response.headers.get('content-type') || '';
            const data = contentType.includes('application/json') ? await response.json() : null;

            if (!response.ok) {
                if (response.status === 422 && data?.errors) {
                    showValidationErrors(data.errors);
                    return;
                }

                showGenericError(data?.message || 'Une erreur est survenue lors de la creation de l ordonnance.');
                return;
            }

            const prescriptionsCount = Number(data?.ordonnances_count ?? data?.prescriptions_count);
            if (Number.isFinite(prescriptionsCount)) {
                updatePrescriptionCounters(prescriptionsCount);
            }

            resetFormState();
            window.dispatchEvent(new CustomEvent('close-modal', { detail: payload.modalName || 'modal-ordonnance' }));
        } catch {
            showGenericError('Le formulaire n a pas pu etre envoye. Verifiez la connexion et reessayez.');
        } finally {
            setSubmitting(false);
        }
    }

    addRowButton?.addEventListener('click', () => {
        const row = appendMedicationRow();
        clearFeedback();
        row?.querySelector('[data-role="medication-label"]')?.focus();
    });

    medecinSelect?.addEventListener('change', () => {
        medecinSelect.classList.remove('is-invalid');
        clearFeedback();
    });

    instructionsInput?.addEventListener('input', clearFeedback);
    form.addEventListener('submit', submitForm);
    modal.addEventListener('ordonnance-quick:opened', () => {
        clearFeedback();
        ensureAtLeastOneRow();
    });
    modal.addEventListener('ordonnance-quick:closed', () => {
        clearFeedback();
    });
    modal.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            clearFeedback();
        }
    });

    appendMedicationRow();
}
