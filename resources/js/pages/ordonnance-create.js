function parsePayload(root) {
    const node = root.querySelector('#ordonnanceCreatePayload');
    if (!node) return null;
    try { return JSON.parse(node.textContent || '{}'); } catch { return null; }
}

export function initOrdonnanceCreate(root = document) {
    const payload = parsePayload(root);
    const form = root.querySelector('#ordonnanceForm');
    if (!payload || !form || form.dataset.medisysBound === '1') return;
    form.dataset.medisysBound = '1';

    const patientCatalog = payload.patientCatalog || [];
    const medicamentCatalog = payload.medicamentCatalog || [];
    const consultationCatalog = payload.consultationCatalog || [];
    const templateCatalog = payload.templateCatalog || [];
    const doctorLocked = Boolean(payload.doctorLocked);
    const initialDoctor = payload.initialDoctor || null;
    const previewPdfUrl = payload.previewPdfUrl || '';
    let medicationIndex = Number(payload.medicationIndex || 0);

    const patientInput = root.querySelector('#patient_search');
    const patientIdInput = root.querySelector('#patient_id');
    const patientResults = root.querySelector('#patientResults');
    const consultationSelect = root.querySelector('#consultation_id');
    const medecinInput = root.querySelector('#medecin_id');
    const dateInput = root.querySelector('#date_prescription');
    const diagnosticInput = root.querySelector('#diagnostic');
    const instructionsInput = root.querySelector('#instructions');
    const templateSelect = root.querySelector('#ordonnance_template_id');
    const templatePreviewBox = root.querySelector('#templatePreviewBox');
    const applyTemplateBtn = root.querySelector('#applyTemplateBtn');
    const medicationRows = root.querySelector('#medicationRows');
    const addMedicationBtn = root.querySelector('#addMedicationBtn');
    const sendPatientBtn = root.querySelector('#sendPatientBtn');
    const previewPrintBtn = root.querySelector('#previewPrintBtn');
    const previewPdfBtn = root.querySelector('#previewPdfBtn');
    const quickPrintBtn = root.querySelector('#quickPrintBtn');
    const quickPdfBtn = root.querySelector('#quickPdfBtn');
    const patientInfoName = root.querySelector('#patientInfoName');
    const patientInfoSubtitle = root.querySelector('#patientInfoSubtitle');
    const patientInfoAge = root.querySelector('#patientInfoAge');
    const patientInfoEmail = root.querySelector('#patientInfoEmail');
    const patientInfoAllergies = root.querySelector('#patientInfoAllergies');
    const patientInfoTreatments = root.querySelector('#patientInfoTreatments');
    const patientInfoNotes = root.querySelector('#patientInfoNotes');
    const previewPatientCard = root.querySelector('#previewPatientCard');
    const previewDoctorCard = root.querySelector('#previewDoctorCard');
    const previewDateCard = root.querySelector('#previewDateCard');
    const previewAttentionCard = root.querySelector('#previewAttentionCard');
    const previewDiagnostic = root.querySelector('#previewDiagnostic');
    const previewInstructions = root.querySelector('#previewInstructions');
    const previewMedicationList = root.querySelector('#previewMedicationList');

    const normalize = (value) => (value || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    const escapeHtml = (value) => (value || '').toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    const nl2br = (value) => escapeHtml(value).replace(/\n/g, '<br>');
    const formatDate = (dateValue) => {
        if (!dateValue) return 'Date non renseignee';
        const [year, month, day] = dateValue.split('-');
        return year && month && day ? [day, month, year].join('/') : dateValue;
    };
    const getPatientById = (id) => patientCatalog.find((patient) => String(patient.id) === String(id)) || null;
    const getMedicationById = (id) => medicamentCatalog.find((medication) => String(medication.id) === String(id)) || null;
    const getConsultationById = (id) => consultationCatalog.find((consultation) => String(consultation.id) === String(id)) || null;
    const getTemplateById = (id) => templateCatalog.find((template) => String(template.id) === String(id)) || null;
    const getCurrentDoctor = () => {
        if (doctorLocked && initialDoctor) return initialDoctor;
        if (!medecinInput?.options) return null;
        const option = medecinInput.options[medecinInput.selectedIndex];
        if (!option?.value) return null;
        const parts = option.textContent.trim().split('-').map((part) => part.trim());
        return { id: option.value, name: parts[0] || option.textContent.trim(), specialite: parts[1] || 'Medecin generaliste' };
    };

    function getMedicationRowsData() {
        return Array.from(medicationRows?.querySelectorAll('.rx-med-row') || []).map((row) => ({
            medicationId: row.querySelector('.js-medication-id')?.value || '',
            medication: getMedicationById(row.querySelector('.js-medication-id')?.value || ''),
            posologie: row.querySelector('input[name*="[posologie]"]')?.value || '',
            duree: row.querySelector('input[name*="[duree]"]')?.value || '',
            quantite: row.querySelector('input[name*="[quantite]"]')?.value || '',
            instructions: row.querySelector('input[name*="[instructions]"]')?.value || '',
        })).filter((row) => row.medicationId || row.posologie || row.duree || row.quantite || row.instructions);
    }

    function updatePreview() {
        const patient = getPatientById(patientIdInput?.value || '');
        const doctor = getCurrentDoctor();
        const medications = getMedicationRowsData();
        if (previewPatientCard) previewPatientCard.textContent = patient ? `${patient.label}\n${patient.numero_dossier || ''}` : 'Aucun patient selectionne';
        if (previewDoctorCard) previewDoctorCard.textContent = doctor ? `${doctor.name}\n${doctor.specialite || ''}` : 'Medecin a confirmer';
        if (previewDateCard) previewDateCard.textContent = formatDate(dateInput?.value || '');
        if (previewAttentionCard) previewAttentionCard.textContent = patient?.allergies || 'Aucune allergie documentee.';
        if (previewDiagnostic) previewDiagnostic.textContent = diagnosticInput?.value.trim() || 'Aucun diagnostic saisi pour le moment.';
        if (previewInstructions) previewInstructions.textContent = instructionsInput?.value.trim() || 'Aucune instruction generale saisie.';
        if (!previewMedicationList) return;
        previewMedicationList.innerHTML = '';
        if (!medications.length) {
            previewMedicationList.innerHTML = '<li class="rx-preview-empty">Aucun medicament ajoute pour le moment.</li>';
            return;
        }
        medications.forEach((row) => {
            const item = document.createElement('li');
            item.className = 'rx-preview-item';
            const title = row.medication ? row.medication.nom_commercial : 'Medicament a confirmer';
            const details = [row.medication?.dci ? `DCI : ${row.medication.dci}` : null, row.posologie ? `Posologie : ${row.posologie}` : null, row.duree ? `Duree : ${row.duree}` : null, row.quantite ? `Quantite : ${row.quantite}` : null, row.instructions ? `Instructions : ${row.instructions}` : null].filter(Boolean).join('\n');
            item.innerHTML = `<strong>${escapeHtml(title)}</strong><span>${escapeHtml(details || 'Ligne a completer.')}</span>`;
            previewMedicationList.appendChild(item);
        });
    }

    function renderPatientResults(query) {
        if (!patientResults) return;
        const normalizedQuery = normalize(query);
        const matches = normalizedQuery === '' ? patientCatalog.slice(0, 6) : patientCatalog.filter((patient) => patient.search.includes(normalizedQuery)).slice(0, 8);
        if (!matches.length) {
            patientResults.innerHTML = '<div class="rx-empty-search">Aucun patient correspondant.</div>';
            patientResults.classList.add('is-open');
            return;
        }
        patientResults.innerHTML = matches.map((patient) => `<button type="button" class="rx-search-option" data-patient-id="${patient.id}"><strong>${escapeHtml(patient.label)}</strong><small>${escapeHtml(patient.numero_dossier)}${patient.age !== null && patient.age !== undefined ? ' | ' + patient.age + ' ans' : ''}</small></button>`).join('');
        patientResults.classList.add('is-open');
    }

    function selectPatient(patientId, syncInput = true) {
        const patient = getPatientById(patientId);
        if (!patient) {
            if (patientIdInput) patientIdInput.value = '';
            if (syncInput && patientInput) patientInput.value = '';
            if (patientInfoName) patientInfoName.textContent = 'Aucun patient selectionne';
            if (patientInfoSubtitle) patientInfoSubtitle.textContent = 'Selectionnez un patient pour afficher son contexte medical.';
            if (patientInfoAge) patientInfoAge.textContent = 'Age inconnu';
            if (patientInfoEmail) patientInfoEmail.textContent = 'Email non renseigne';
            if (patientInfoAllergies) patientInfoAllergies.textContent = 'Aucune allergie documentee.';
            if (patientInfoTreatments) patientInfoTreatments.textContent = 'Aucun traitement actif connu.';
            if (patientInfoNotes) patientInfoNotes.textContent = 'Aucun point d attention complementaire.';
            updatePreview();
            return;
        }
        if (patientIdInput) patientIdInput.value = patient.id;
        if (syncInput && patientInput) patientInput.value = patient.label;
        if (patientInfoName) patientInfoName.textContent = patient.label;
        if (patientInfoSubtitle) patientInfoSubtitle.textContent = patient.numero_dossier || 'Sans numero de dossier';
        if (patientInfoAge) patientInfoAge.textContent = patient.age !== null && patient.age !== undefined ? `${patient.age} ans` : 'Age inconnu';
        if (patientInfoEmail) patientInfoEmail.textContent = patient.email || 'Email non renseigne';
        if (patientInfoAllergies) patientInfoAllergies.textContent = patient.allergies || 'Aucune allergie documentee.';
        if (patientInfoTreatments) patientInfoTreatments.textContent = patient.traitements || 'Aucun traitement actif connu.';
        if (patientInfoNotes) patientInfoNotes.textContent = patient.notes || 'Aucun point d attention complementaire.';
        patientResults?.classList.remove('is-open');
        updatePreview();
    }

    function renderTemplatePreview() {
        if (!templateSelect || !templatePreviewBox) return;
        const template = getTemplateById(templateSelect.value);
        if (!template) {
            templatePreviewBox.textContent = 'Selectionnez un modele pour voir le contexte, les consignes et les medicaments proposes avant chargement.';
            return;
        }
        const medicationCount = Array.isArray(template.medications) ? template.medications.length : 0;
        templatePreviewBox.textContent = [template.category ? `Categorie : ${template.category}` : null, template.diagnostic ? `Diagnostic : ${template.diagnostic}` : null, template.instructions ? `Instructions : ${template.instructions}` : null, medicationCount > 0 ? `${medicationCount} medicament(s) preconfigure(s)` : 'Aucun medicament type preconfigure.'].filter(Boolean).join('\n\n');
    }

    function bindMedicationRow(row) {
        const hiddenId = row.querySelector('.js-medication-id');
        const hiddenLabel = row.querySelector('.js-medication-label');
        const searchInput = row.querySelector('.js-medication-search');
        const results = row.querySelector('.js-medication-results');
        const meta = row.querySelector('.js-medication-meta');
        const removeButton = row.querySelector('.js-remove-medication');
        const allFields = row.querySelectorAll('.js-medication-field');

        const renderMedicationMeta = () => {
            const medication = getMedicationById(hiddenId?.value || '');
            if (!meta) return;
            if (!medication) {
                meta.innerHTML = '<span class="rx-chip rx-chip-muted"><i class="fas fa-lightbulb"></i> Recherchez un medicament pour voir ses suggestions de dosage.</span>';
                return;
            }
            const chips = [];
            if (medication.posologie) chips.push(`<span class="rx-chip"><i class="fas fa-syringe"></i> ${escapeHtml(medication.posologie)}</span>`);
            if (medication.presentation) chips.push(`<span class="rx-chip rx-chip-muted"><i class="fas fa-box-open"></i> ${escapeHtml(medication.presentation)}</span>`);
            if (medication.voie_administration) chips.push(`<span class="rx-chip rx-chip-muted"><i class="fas fa-route"></i> ${escapeHtml(medication.voie_administration)}</span>`);
            if (medication.classe_therapeutique) chips.push(`<span class="rx-chip rx-chip-muted"><i class="fas fa-stethoscope"></i> ${escapeHtml(medication.classe_therapeutique)}</span>`);
            meta.innerHTML = chips.join('');
        };

        const selectMedication = (medicationId, syncLabel = true) => {
            const medication = getMedicationById(medicationId);
            if (!medication) {
                if (hiddenId) hiddenId.value = '';
                if (hiddenLabel) hiddenLabel.value = searchInput?.value.trim() || '';
                if (syncLabel && searchInput) { searchInput.value = ''; if (hiddenLabel) hiddenLabel.value = ''; }
                renderMedicationMeta();
                updatePreview();
                return;
            }
            if (hiddenId) hiddenId.value = medication.id;
            if (syncLabel && searchInput) searchInput.value = medication.label;
            if (hiddenLabel) hiddenLabel.value = medication.label;
            const posologyField = row.querySelector('input[name*="[posologie]"]');
            if (posologyField && !posologyField.value && medication.posologie) posologyField.value = medication.posologie;
            renderMedicationMeta();
            results?.classList.remove('is-open');
            updatePreview();
        };

        const renderMedicationResults = (query) => {
            if (!results) return;
            const normalizedQuery = normalize(query);
            const matches = normalizedQuery === '' ? medicamentCatalog.slice(0, 8) : medicamentCatalog.filter((medication) => medication.search.includes(normalizedQuery)).slice(0, 8);
            if (!matches.length) {
                results.innerHTML = '<div class="rx-empty-search">Aucun medicament trouve.</div>';
                results.classList.add('is-open');
                return;
            }
            results.innerHTML = matches.map((medication) => `<button type="button" class="rx-search-option" data-medication-id="${medication.id}"><strong>${escapeHtml(medication.nom_commercial)}</strong><small>${escapeHtml(medication.dci || 'DCI non renseignee')}${medication.presentation ? ' | ' + escapeHtml(medication.presentation) : ''}</small></button>`).join('');
            results.classList.add('is-open');
        };

        searchInput?.addEventListener('focus', () => renderMedicationResults(searchInput.value));
        searchInput?.addEventListener('input', () => {
            if (hiddenId) hiddenId.value = '';
            if (hiddenLabel) hiddenLabel.value = searchInput.value.trim();
            renderMedicationResults(searchInput.value);
            updatePreview();
        });
        searchInput?.addEventListener('blur', () => window.setTimeout(() => results?.classList.remove('is-open'), 140));
        results?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-medication-id]');
            if (button) selectMedication(button.dataset.medicationId);
        });
        allFields.forEach((field) => { field.addEventListener('input', updatePreview); field.addEventListener('change', updatePreview); });
        removeButton?.addEventListener('click', () => { row.remove(); refreshMedicationTitles(); updatePreview(); });
        renderMedicationMeta();
    }

    function buildMedicationRow(index, rowData = {}) {
        const wrapper = document.createElement('article');
        wrapper.className = 'rx-med-row';
        wrapper.dataset.index = index;
        wrapper.innerHTML = `<div class="rx-med-row-top"><div class="rx-med-row-title">Ligne medicament #${index + 1}</div><button type="button" class="rx-button rx-button-danger js-remove-medication"><i class="fas fa-trash"></i> Supprimer</button></div><div class="rx-med-grid"><div class="rx-field"><label class="rx-label"><span>Medicament</span><span class="rx-label-note">Recherche avec suggestions</span></label><div class="rx-search-shell"><input type="hidden" name="medicaments[${index}][medicament_id]" class="js-medication-id" value="${escapeHtml(rowData.medicament_id || '')}"><input type="hidden" name="medicaments[${index}][medicament_label]" class="js-medication-label" value="${escapeHtml(rowData.medicament_label || '')}"><input type="text" class="rx-input js-medication-search" placeholder="Nom commercial, DCI, classe therapeutique..." autocomplete="off" value="${escapeHtml(rowData.medicament_label || '')}"><i class="fas fa-capsules"></i><div class="rx-search-results js-medication-results"></div></div><div class="rx-med-meta js-medication-meta"></div></div><div class="rx-field"><label class="rx-label">Posologie</label><input type="text" name="medicaments[${index}][posologie]" class="rx-input js-medication-field" placeholder="Ex: 1 comprime matin et soir" value="${escapeHtml(rowData.posologie || '')}"></div><div class="rx-field"><label class="rx-label">Duree</label><input type="text" name="medicaments[${index}][duree]" class="rx-input js-medication-field" placeholder="Ex: 7 jours" value="${escapeHtml(rowData.duree || '')}"></div><div class="rx-field"><label class="rx-label">Quantite</label><input type="text" name="medicaments[${index}][quantite]" class="rx-input js-medication-field" placeholder="Ex: 14" value="${escapeHtml(rowData.quantite || '')}"></div><div class="rx-field"><label class="rx-label">Instruction specifique</label><input type="text" name="medicaments[${index}][instructions]" class="rx-input js-medication-field" placeholder="Avant repas, soir, surveiller..." value="${escapeHtml(rowData.instructions || '')}"></div></div>`;
        return wrapper;
    }

    function refreshMedicationTitles() {
        medicationRows?.querySelectorAll('.rx-med-row').forEach((row, index) => {
            const title = row.querySelector('.rx-med-row-title');
            if (title) title.textContent = `Ligne medicament #${index + 1}`;
        });
    }

    function setMedicationRowsFromTemplate(rows) {
        if (!medicationRows) return;
        medicationRows.innerHTML = '';
        medicationIndex = 0;
        (Array.isArray(rows) && rows.length ? rows : [{}]).forEach((rowData) => {
            const row = buildMedicationRow(medicationIndex++, rowData || {});
            medicationRows.appendChild(row);
            bindMedicationRow(row);
        });
        refreshMedicationTitles();
    }

    function submitPreviewPdf() {
        const originalAction = form.getAttribute('action');
        const originalTarget = form.getAttribute('target');
        const methodField = form.querySelector('input[name="_method"]');
        const originalMethodOverride = methodField ? methodField.value : null;
        form.setAttribute('action', previewPdfUrl);
        form.setAttribute('target', '_blank');
        if (methodField) methodField.value = 'POST';
        form.submit();
        form.setAttribute('action', originalAction || '');
        if (originalTarget === null) form.removeAttribute('target'); else form.setAttribute('target', originalTarget);
        if (methodField && originalMethodOverride !== null) methodField.value = originalMethodOverride;
    }

    function buildPrintableMarkup() {
        const patient = getPatientById(patientIdInput?.value || '');
        const doctor = getCurrentDoctor();
        const medications = getMedicationRowsData();
        const dateLabel = formatDate(dateInput?.value || '');
        const diagnosis = diagnosticInput?.value.trim() || 'Aucun diagnostic saisi.';
        const instructions = instructionsInput?.value.trim() || 'Aucune instruction generale.';
        const allergies = patient?.allergies || 'Aucune allergie documentee.';
        const patientLabel = patient?.label || 'Patient non selectionne';
        const patientDossier = patient?.numero_dossier || 'Sans numero de dossier';
        const doctorLabel = doctor?.name || 'Medecin a confirmer';
        const doctorSpecialite = doctor?.specialite || 'Specialite non renseignee';

        const medicationMarkup = medications.length
            ? medications.map((row, index) => {
                const medicationName = row.medication?.nom_commercial || row.medication?.label || row.medication?.dci || 'Medicament a confirmer';
                const details = [
                    row.medication?.dci ? `DCI : ${row.medication.dci}` : null,
                    row.posologie ? `Posologie : ${row.posologie}` : null,
                    row.duree ? `Duree : ${row.duree}` : null,
                    row.quantite ? `Quantite : ${row.quantite}` : null,
                    row.instructions ? `Instructions : ${row.instructions}` : null,
                ].filter(Boolean);
                return `<li><strong>${index + 1}. ${escapeHtml(medicationName)}</strong>${details.length ? `<span>${nl2br(details.join('\n'))}</span>` : ''}</li>`;
            }).join('')
            : '<li><strong>Aucun medicament saisi</strong><span>Ajoutez au moins une ligne pour completer l ordonnance.</span></li>';

        return `<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ordonnance - ${escapeHtml(patientLabel)}</title>
    <style>
        :root {
            color-scheme: light;
            --text: #0f172a;
            --muted: #64748b;
            --line: #dbe4ee;
            --brand: #2563eb;
            --soft: #f8fafc;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px;
            font-family: "Plus Jakarta Sans", "Segoe UI", Roboto, Arial, sans-serif;
            color: var(--text);
            background: #ffffff;
        }
        .sheet {
            max-width: 880px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            align-items: flex-start;
            padding-bottom: 18px;
            border-bottom: 2px solid var(--line);
        }
        .kicker {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--brand);
        }
        h1 {
            margin: 0;
            font-size: 32px;
            line-height: 1.08;
            font-weight: 700;
        }
        .date-badge {
            min-width: 160px;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--soft);
            text-align: right;
        }
        .date-badge strong {
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .date-badge span {
            font-size: 16px;
            font-weight: 600;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 22px;
        }
        .card {
            padding: 16px 18px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #fff;
        }
        .card strong, .section h2 {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .card p {
            margin: 0;
            font-size: 15px;
            line-height: 1.55;
            font-weight: 500;
        }
        .card p + p { margin-top: 6px; color: var(--muted); font-weight: 500; }
        .section {
            margin-top: 24px;
            padding: 18px 20px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: #fff;
        }
        .section h2 {
            margin: 0 0 10px;
        }
        .section p {
            margin: 0;
            font-size: 15px;
            line-height: 1.65;
            white-space: pre-wrap;
        }
        .medication-list {
            margin: 0;
            padding-left: 20px;
            display: grid;
            gap: 12px;
        }
        .medication-list li {
            font-size: 15px;
            line-height: 1.6;
        }
        .medication-list li strong {
            display: block;
            margin-bottom: 4px;
            font-size: 15px;
            color: var(--text);
            text-transform: none;
            letter-spacing: 0;
        }
        .medication-list li span {
            color: var(--muted);
            font-weight: 500;
        }
        @media print {
            body { padding: 0; }
            .sheet { max-width: none; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="header">
            <div>
                <p class="kicker">MEDISYS Pro</p>
                <h1>Ordonnance medicale</h1>
            </div>
            <div class="date-badge">
                <strong>Date</strong>
                <span>${escapeHtml(dateLabel)}</span>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <strong>Patient</strong>
                <p>${escapeHtml(patientLabel)}</p>
                <p>${escapeHtml(patientDossier)}</p>
            </div>
            <div class="card">
                <strong>Prescripteur</strong>
                <p>${escapeHtml(doctorLabel)}</p>
                <p>${escapeHtml(doctorSpecialite)}</p>
            </div>
            <div class="card">
                <strong>Points d attention</strong>
                <p>${escapeHtml(allergies)}</p>
            </div>
            <div class="card">
                <strong>Coordonnees patient</strong>
                <p>${escapeHtml(patient?.email || 'Email non renseigne')}</p>
                <p>${escapeHtml(patient?.telephone || 'Telephone non renseigne')}</p>
            </div>
        </div>

        <section class="section">
            <h2>Diagnostic / contexte</h2>
            <p>${nl2br(diagnosis)}</p>
        </section>

        <section class="section">
            <h2>Instructions generales</h2>
            <p>${nl2br(instructions)}</p>
        </section>

        <section class="section">
            <h2>Traitement prescrit</h2>
            <ol class="medication-list">${medicationMarkup}</ol>
        </section>
    </div>
</body>
</html>`;
    }

    function openPrintableWindow() {
        const printWindow = window.open('', '_blank', 'width=980,height=780');
        if (!printWindow) {
            window.alert('Le navigateur a bloque la fenetre d impression. Autorisez les popups pour continuer.');
            return;
        }

        printWindow.document.open();
        printWindow.document.write(buildPrintableMarkup());
        printWindow.document.close();
        printWindow.focus();
        printWindow.onload = () => {
            printWindow.print();
        };
    }

    function sendPreviewByEmail() {
        const patient = getPatientById(patientIdInput?.value || '');
        if (!patient || !patient.email) { window.alert('Aucune adresse email patient n est renseignee.'); return; }
        const bodyLines = [`Patient : ${patient.label}`, `Date : ${formatDate(dateInput?.value || '')}`, '', 'Diagnostic :', diagnosticInput?.value.trim() || 'Aucun diagnostic saisi.', '', 'Instructions :', instructionsInput?.value.trim() || 'Aucune instruction generale.', '', 'Traitement :', ...getMedicationRowsData().map((row) => `- ${[row.medication?.nom_commercial || 'Medicament', row.posologie || 'posologie a confirmer', row.duree || 'duree a confirmer', row.quantite ? `quantite ${row.quantite}` : null, row.instructions ? `instructions ${row.instructions}` : null].filter(Boolean).join(' | ')}`)];
        const subject = `Ordonnance pour ${patient.label}`;
        window.location.href = `mailto:${encodeURIComponent(patient.email)}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(bodyLines.join('\n'))}`;
    }

    patientInput?.addEventListener('focus', () => renderPatientResults(patientInput.value));
    patientInput?.addEventListener('input', () => renderPatientResults(patientInput.value));
    patientInput?.addEventListener('blur', () => window.setTimeout(() => patientResults?.classList.remove('is-open'), 140));
    patientResults?.addEventListener('click', (event) => { const button = event.target.closest('[data-patient-id]'); if (button) selectPatient(button.dataset.patientId); });
    consultationSelect?.addEventListener('change', () => {
        const consultation = getConsultationById(consultationSelect.value);
        if (!consultation) return;
        if (consultation.patient_id) selectPatient(consultation.patient_id);
        if (!doctorLocked && medecinInput && consultation.medecin_id) medecinInput.value = consultation.medecin_id;
        updatePreview();
    });
    medecinInput?.addEventListener('change', updatePreview);
    dateInput?.addEventListener('change', updatePreview);
    diagnosticInput?.addEventListener('input', updatePreview);
    instructionsInput?.addEventListener('input', updatePreview);
    templateSelect?.addEventListener('change', renderTemplatePreview);
    applyTemplateBtn?.addEventListener('click', () => {
        const template = getTemplateById(templateSelect.value);
        if (!template) return;
        if (diagnosticInput) diagnosticInput.value = template.diagnostic || template.name || '';
        if (instructionsInput) instructionsInput.value = template.instructions || template.content || '';
        if (Array.isArray(template.medications)) setMedicationRowsFromTemplate(template.medications);
        updatePreview();
        renderTemplatePreview();
    });
    addMedicationBtn?.addEventListener('click', () => {
        if (!medicationRows) return;
        const row = buildMedicationRow(medicationIndex++);
        medicationRows.appendChild(row);
        bindMedicationRow(row);
        refreshMedicationTitles();
        updatePreview();
    });
    [previewPrintBtn, quickPrintBtn].forEach((button) => button?.addEventListener('click', openPrintableWindow));
    [previewPdfBtn, quickPdfBtn].forEach((button) => button?.addEventListener('click', submitPreviewPdf));
    sendPatientBtn?.addEventListener('click', sendPreviewByEmail);
    root.querySelectorAll('.rx-med-row').forEach(bindMedicationRow);
    refreshMedicationTitles();
    renderTemplatePreview();
    selectPatient(patientIdInput?.value || '', !patientInput?.value);
    updatePreview();
}
