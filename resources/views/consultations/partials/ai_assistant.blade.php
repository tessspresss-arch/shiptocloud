@php
    $assistantEnabled = isset($consultation) && $consultation;
    $historyItems = $aiGenerations ?? collect();
@endphp

@once
    @push('styles')
    <style>
        .cai-panel { background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%); border: 1px solid #dbe5f1; border-radius: 16px; box-shadow: 0 14px 28px -24px rgba(15, 23, 42, 0.45); overflow: hidden; }
        .cai-head { padding: 1rem 1.1rem; border-bottom: 1px solid #e2e8f0; background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%); display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .cai-head-copy { min-width: 0; display: grid; gap: .35rem; }
        .cai-head-copy h3 { margin: 0; color: #1e3a8a; font-size: 1.15rem; font-weight: 800; display: inline-flex; align-items: center; gap: .55rem; }
        .cai-head-copy p { margin: 0; color: #5f7896; font-size: .92rem; font-weight: 600; }
        .cai-badge { display: inline-flex; align-items: center; gap: .45rem; border-radius: 999px; padding: .45rem .8rem; font-size: .78rem; font-weight: 800; white-space: nowrap; border: 1px solid #bfdbfe; background: #dbeafe; color: #1d4ed8; }
        .cai-body { padding: 1rem 1.1rem 1.1rem; display: grid; gap: 1rem; }
        .cai-grid { display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr); gap: 1rem; }
        .cai-stack { display: grid; gap: 1rem; }
        .cai-card { border: 1px solid #dbe5f1; border-radius: 14px; background: #ffffff; padding: .95rem; display: grid; gap: .85rem; }
        .cai-card-head { display: flex; align-items: center; justify-content: space-between; gap: .75rem; flex-wrap: wrap; }
        .cai-card-head strong { color: #1e3a8a; font-size: .98rem; font-weight: 800; }
        .cai-actions-row, .cai-toolbar-left, .cai-toolbar-right { display: flex; gap: .6rem; flex-wrap: wrap; align-items: center; }
        .cai-toolbar { display: flex; gap: .65rem; align-items: center; justify-content: space-between; flex-wrap: wrap; }
        .cai-button { min-height: 42px; border-radius: 10px; border: 1px solid #cbd5e1; background: #f8fafc; color: #334155; font-weight: 800; font-size: .9rem; padding: 0 .9rem; display: inline-flex; align-items: center; justify-content: center; gap: .5rem; cursor: pointer; transition: all .2s ease; text-decoration: none; }
        .cai-button:hover:not(:disabled) { transform: translateY(-1px); background: #eef4fb; border-color: #b8c9dd; color: #1f3d5e; }
        .cai-button:disabled { opacity: .6; cursor: not-allowed; }
        .cai-button.primary { background: linear-gradient(135deg, #1d4ed8, #0284c7); border-color: transparent; color: #fff; }
        .cai-button.report { background: linear-gradient(135deg, #0f766e, #0ea5a4); border-color: transparent; color: #fff; }
        .cai-button.success { background: #eafbf3; border-color: #bfe8d3; color: #0f7a58; }
        .cai-button.info { background: #ecf5ff; border-color: #c4ddf8; color: #155b9a; }
        .cai-button.warning { background: #fff5e4; border-color: #f3d2a0; color: #9a5e00; }
        .cai-button.pdf { background: #fff1f2; border-color: #fecdd3; color: #be123c; }
        .cai-button.muted { background: #f8fafc; border-color: #d6e0eb; color: #475569; }
        .cai-textarea, .cai-select { width: 100%; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #0f172a; padding: .8rem .9rem; min-height: 48px; transition: border-color .2s ease, box-shadow .2s ease; }
        .cai-textarea { min-height: 170px; resize: vertical; line-height: 1.5; }
        .cai-textarea.report { min-height: 230px; }
        .cai-textarea:focus, .cai-select:focus { outline: none; border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(96, 165, 250, .15); }
        .cai-hint { color: #64748b; font-size: .84rem; line-height: 1.45; font-weight: 600; }
        .cai-status { border-radius: 12px; padding: .72rem .85rem; font-size: .88rem; font-weight: 700; border: 1px solid #dbe5f1; background: #f8fbff; color: #44627f; }
        .cai-status.success { background: #ecfdf5; border-color: #bbf7d0; color: #166534; }
        .cai-status.error { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
        .cai-status.info { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
        .cai-status.warning { background: #fff7ed; border-color: #fed7aa; color: #c2410c; }
        .cai-dictation-bar { display: flex; align-items: center; justify-content: space-between; gap: .75rem; flex-wrap: wrap; padding: .8rem .9rem; border-radius: 12px; border: 1px solid #dbe5f1; background: linear-gradient(180deg, #fbfdff 0%, #f4f8ff 100%); }
        .cai-dictation-meta { display: grid; gap: .25rem; min-width: 0; }
        .cai-dictation-meta strong { color: #1e3a8a; font-size: .92rem; font-weight: 800; }
        .cai-dictation-meta span { color: #64748b; font-size: .82rem; line-height: 1.45; }
        .cai-live-chip { display: inline-flex; align-items: center; gap: .45rem; padding: .4rem .72rem; border-radius: 999px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-size: .77rem; font-weight: 800; }
        .cai-live-chip.recording { color: #b91c1c; border-color: #fecaca; background: #fef2f2; }
        .cai-live-chip.recording::before { content: ""; width: 8px; height: 8px; border-radius: 999px; background: #ef4444; box-shadow: 0 0 0 0 rgba(239, 68, 68, .45); animation: caiPulse 1.5s infinite; }
        .cai-live-preview { border: 1px dashed #dbe5f1; border-radius: 12px; padding: .8rem .9rem; background: #fcfdff; color: #475569; font-size: .86rem; line-height: 1.55; min-height: 58px; }
        .cai-live-preview:empty::before { content: attr(data-placeholder); color: #94a3b8; }
        @keyframes caiPulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, .42); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .cai-history { display: grid; gap: .75rem; }
        .cai-history-item { border: 1px solid #dbe5f1; border-radius: 12px; background: #fbfdff; padding: .85rem; display: grid; gap: .55rem; }
        .cai-history-head { display: flex; align-items: center; justify-content: space-between; gap: .75rem; flex-wrap: wrap; }
        .cai-history-meta { display: flex; gap: .45rem; flex-wrap: wrap; align-items: center; color: #64748b; font-size: .78rem; font-weight: 700; }
        .cai-history-type { display: inline-flex; align-items: center; gap: .4rem; padding: .28rem .58rem; border-radius: 999px; background: #eaf4ff; border: 1px solid #c7ddf6; color: #19528c; font-size: .75rem; font-weight: 800; }
        .cai-history-preview { color: #31465d; font-size: .88rem; line-height: 1.5; }
        .cai-empty, .cai-locked { border: 1px dashed #cbd5e1; border-radius: 14px; padding: 1rem; color: #64748b; background: #f8fafc; font-weight: 600; }
        .cai-locked strong { color: #1e3a8a; }
        @media (max-width: 1100px) { .cai-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .cai-head, .cai-body { padding: .9rem; }
            .cai-toolbar { flex-direction: column; align-items: stretch; }
            .cai-toolbar-left, .cai-toolbar-right, .cai-actions-row { width: 100%; }
            .cai-actions-row .cai-button, .cai-toolbar-right .cai-button, .cai-toolbar-left .cai-select, .cai-toolbar-right .cai-select { width: 100%; }
            .cai-dictation-bar { align-items: stretch; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-consultation-ai]').forEach(function (root) {
                const generateUrl = root.dataset.generateUrl || '';
                const exportMedicalReportUrl = root.dataset.exportMedicalReportUrl || '';
                const consultationId = root.dataset.consultationId || '';
                const assistantReady = Boolean(generateUrl && consultationId);
                const sourceTextarea = root.querySelector('[data-ai-source]');
                const summaryTextarea = root.querySelector('[data-ai-summary-result]');
                const reportTextarea = root.querySelector('[data-ai-report-result]');
                const statusBox = root.querySelector('[data-ai-status]');
                const summaryTargetSelect = root.querySelector('[data-ai-summary-target]');
                const reportTargetSelect = root.querySelector('[data-ai-report-target]');
                const historyList = root.querySelector('[data-ai-history]');
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const fields = ['symptomes', 'examen_clinique', 'diagnostic', 'traitement_prescrit', 'recommandations'];
                const labels = { symptomes: 'Symptomes', examen_clinique: 'Examen clinique', diagnostic: 'Diagnostic', traitement_prescrit: 'Traitement prescrit', recommandations: 'Recommandations' };
                const buttons = root.querySelectorAll('[data-ai-action]');
                const collectButton = root.querySelector('[data-ai-collect]');
                const clearSourceButton = root.querySelector('[data-ai-clear-source]');
                const summaryCopyButton = root.querySelector('[data-ai-copy-summary]');
                const summaryClearButton = root.querySelector('[data-ai-clear-summary]');
                const summaryInsertButton = root.querySelector('[data-ai-insert-summary]');
                const reportCopyButton = root.querySelector('[data-ai-copy-report]');
                const reportClearButton = root.querySelector('[data-ai-clear-report]');
                const reportInsertButton = root.querySelector('[data-ai-insert-report]');
                const reportFocusButton = root.querySelector('[data-ai-focus-report]');
                const reportExportButton = root.querySelector('[data-ai-export-report]');
                const dictationStartButton = root.querySelector('[data-ai-dictation-start]');
                const dictationStopButton = root.querySelector('[data-ai-dictation-stop]');
                const dictationChip = root.querySelector('[data-ai-dictation-chip]');
                const dictationPreview = root.querySelector('[data-ai-dictation-preview]');

                function setStatus(message, type) {
                    if (!statusBox) { return; }
                    statusBox.textContent = message;
                    statusBox.classList.remove('success', 'error', 'info', 'warning');
                    if (type) { statusBox.classList.add(type); }
                }

                function collectFieldValues() {
                    const values = {};
                    fields.forEach(function (field) {
                        const element = document.getElementById(field);
                        if (element && String(element.value || '').trim() !== '') {
                            values[field] = String(element.value).trim();
                        }
                    });
                    return values;
                }

                function buildSourceFromFields() {
                    const values = collectFieldValues();
                    const parts = [];
                    Object.keys(values).forEach(function (field) { parts.push(labels[field] + ':\n' + values[field]); });
                    return parts.join('\n\n').trim();
                }

                function toggleLoading(loading) {
                    buttons.forEach(function (button) { button.disabled = loading || !assistantReady; });
                    [collectButton, clearSourceButton, summaryCopyButton, summaryClearButton, summaryInsertButton, reportCopyButton, reportClearButton, reportInsertButton, reportFocusButton, reportExportButton].forEach(function (button) {
                        if (button) { button.disabled = loading; }
                    });
                }

                function targetTextareaForAction(action) {
                    return action === 'medical_report' ? reportTextarea : summaryTextarea;
                }

                function labelForAction(action) {
                    return action === 'medical_report' ? 'compte rendu IA' : 'resume IA';
                }

                function prependHistoryItem(item) {
                    if (!historyList || !item) { return; }
                    const empty = historyList.querySelector('.cai-empty');
                    if (empty) { empty.remove(); }
                    const wrapper = document.createElement('div');
                    wrapper.className = 'cai-history-item';
                    wrapper.innerHTML = `
                        <div class="cai-history-head">
                            <span class="cai-history-type"><i class="fas fa-wand-magic-sparkles"></i>${item.action_label}</span>
                            <button type="button" class="cai-button info" data-ai-load-history data-action-type="${item.action_type}" data-generated-text="${encodeURIComponent(item.generated_text)}">
                                <i class="fas fa-arrow-down"></i><span>Charger</span>
                            </button>
                        </div>
                        <div class="cai-history-meta">
                            <span>${item.created_at}</span>
                            <span>&bull;</span>
                            <span>${item.user_name}</span>
                            <span>&bull;</span>
                            <span>${item.provider || 'assistant'}</span>
                        </div>
                        <div class="cai-history-preview">${item.preview}</div>`;
                    historyList.prepend(wrapper);
                }

                function copyText(text, successMessage) {
                    return navigator.clipboard.writeText(text).then(function () { setStatus(successMessage, 'success'); }).catch(function () { setStatus('Impossible de copier automatiquement le texte. Selectionnez-le manuellement.', 'error'); });
                }

                function insertIntoConsultation(targetSelect, textarea, successMessage) {
                    const target = targetSelect ? targetSelect.value : '';
                    const targetField = target ? document.getElementById(target) : null;
                    const generated = String(textarea?.value || '').trim();
                    if (!targetField) { setStatus('Selectionnez un champ cible valide pour inserer le texte.', 'error'); return; }
                    if (!generated) { setStatus('Aucun contenu IA a inserer dans la consultation.', 'error'); return; }
                    const existing = String(targetField.value || '').trim();
                    targetField.value = existing ? (existing + '\n\n' + generated) : generated;
                    targetField.dispatchEvent(new Event('input', { bubbles: true }));
                    targetField.focus();
                    setStatus(successMessage, 'success');
                }

                const SpeechRecognitionCtor = window.SpeechRecognition || window.webkitSpeechRecognition || null;
                let recognition = null;
                let recognitionSeed = '';
                let finalTranscript = '';
                let keepDictating = false;
                let isRecording = false;

                function setDictationState(message, recording) {
                    if (dictationChip) {
                        dictationChip.textContent = message;
                        dictationChip.classList.toggle('recording', Boolean(recording));
                    }
                    if (dictationStartButton) { dictationStartButton.disabled = Boolean(recording) || !SpeechRecognitionCtor; }
                    if (dictationStopButton) { dictationStopButton.disabled = !recording || !SpeechRecognitionCtor; }
                }

                function setDictationPreview(text) {
                    if (!dictationPreview) { return; }
                    dictationPreview.textContent = text || '';
                }

                function composeDictationText(seed, finalText, interimText) {
                    const blocks = [];
                    if (String(seed || '').trim() !== '') { blocks.push(String(seed).trim()); }
                    const liveText = [String(finalText || '').trim(), String(interimText || '').trim()].filter(Boolean).join(' ');
                    if (liveText) { blocks.push(liveText.trim()); }
                    return blocks.join('\n\n').trim();
                }

                if (SpeechRecognitionCtor && sourceTextarea) {
                    recognition = new SpeechRecognitionCtor();
                    recognition.lang = 'fr-FR';
                    recognition.continuous = true;
                    recognition.interimResults = true;

                    recognition.onstart = function () {
                        isRecording = true;
                        setDictationState('Dictee en cours', true);
                        setStatus('Dictee medicale active. Parlez librement, la transcription apparait en temps reel.', 'info');
                    };

                    recognition.onresult = function (event) {
                        let interimTranscript = '';
                        for (let index = event.resultIndex; index < event.results.length; index += 1) {
                            const transcript = String(event.results[index][0]?.transcript || '').trim();
                            if (!transcript) { continue; }
                            if (event.results[index].isFinal) {
                                finalTranscript = [finalTranscript, transcript].filter(Boolean).join(' ').trim();
                            } else {
                                interimTranscript = [interimTranscript, transcript].filter(Boolean).join(' ').trim();
                            }
                        }
                        sourceTextarea.value = composeDictationText(recognitionSeed, finalTranscript, interimTranscript);
                        sourceTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                        setDictationPreview(interimTranscript || finalTranscript || '');
                    };

                    recognition.onerror = function (event) {
                        const errorMessages = {
                            'not-allowed': 'Microphone refuse. Autorisez l acces au microphone pour utiliser la dictee.',
                            'service-not-allowed': 'Le service de reconnaissance vocale du navigateur est indisponible.',
                            'no-speech': 'Aucune voix detectee. Reessayez en parlant plus pres du microphone.',
                            'audio-capture': 'Aucun microphone detecte pour la dictee medicale.',
                            'network': 'Erreur reseau pendant la dictee medicale. Reessayez.',
                            'aborted': 'Dictee arretee.'
                        };
                        if (event.error !== 'aborted') {
                            keepDictating = false;
                            setStatus(errorMessages[event.error] || 'Erreur lors de la transcription vocale.', 'error');
                        }
                    };

                    recognition.onend = function () {
                        const shouldRestart = keepDictating;
                        isRecording = false;
                        setDictationState('Dictee arretee', false);
                        if (String(finalTranscript || '').trim() !== '') {
                            setDictationPreview(finalTranscript);
                        }
                        if (shouldRestart) {
                            try {
                                recognition.start();
                                return;
                            } catch (error) {
                                keepDictating = false;
                            }
                        }
                    };
                } else {
                    setDictationState('Dictee non disponible', false);
                    setDictationPreview('Votre navigateur ne prend pas en charge la transcription vocale temps reel.');
                }

                if (dictationStartButton && sourceTextarea) {
                    dictationStartButton.addEventListener('click', function () {
                        if (!recognition) {
                            setStatus('La reconnaissance vocale n est pas disponible sur ce navigateur. Utilisez Chrome, Edge ou un navigateur compatible.', 'warning');
                            return;
                        }
                        recognitionSeed = String(sourceTextarea.value || '').trim();
                        finalTranscript = '';
                        keepDictating = true;
                        setDictationPreview('Ecoute en cours...');
                        try {
                            recognition.start();
                        } catch (error) {
                            setStatus('Impossible de demarrer la dictee pour le moment. Verifiez le microphone puis reessayez.', 'error');
                        }
                    });
                }

                if (dictationStopButton) {
                    dictationStopButton.addEventListener('click', function () {
                        keepDictating = false;
                        if (recognition && isRecording) {
                            recognition.stop();
                        }
                        setStatus('Dictee arretee. Relisez et corrigez le texte avant utilisation.', 'info');
                    });
                }

                if (collectButton && sourceTextarea) {
                    collectButton.addEventListener('click', function () {
                        const text = buildSourceFromFields();
                        if (!text) { setStatus('Aucun contenu clinique disponible dans les champs de consultation.', 'error'); return; }
                        sourceTextarea.value = text;
                        setStatus('Les notes de consultation ont ete chargees dans l assistant IA.', 'success');
                    });
                }

                if (clearSourceButton && sourceTextarea) {
                    clearSourceButton.addEventListener('click', function () { sourceTextarea.value = ''; recognitionSeed = ''; finalTranscript = ''; setDictationPreview(''); setStatus('La zone des notes du medecin a ete videe.', 'info'); });
                }

                buttons.forEach(function (button) {
                    button.addEventListener('click', async function () {
                        const action = button.dataset.aiAction;
                        const targetTextarea = targetTextareaForAction(action);
                        const notes = String(sourceTextarea?.value || '').trim();
                        const summaryText = String(summaryTextarea?.value || '').trim();
                        const fieldValues = collectFieldValues();
                        if (!targetTextarea) { return; }
                        if (!notes && !summaryText && Object.keys(fieldValues).length === 0) {
                            setStatus('Ajoutez des notes, un resume IA ou chargez les champs de consultation avant de generer un texte IA.', 'error');
                            return;
                        }
                        toggleLoading(true);
                        setStatus('Generation du ' + labelForAction(action) + ' en cours...', null);
                        try {
                            const response = await fetch(generateUrl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                                body: JSON.stringify({
                                    action: action,
                                    notes: notes,
                                    summary_text: summaryText,
                                    preferred_target: action === 'medical_report' ? (reportTargetSelect ? reportTargetSelect.value : '') : (summaryTargetSelect ? summaryTargetSelect.value : ''),
                                    field_values: fieldValues,
                                }),
                            });
                            const payload = await response.json();
                            if (!response.ok) { throw new Error(payload.message || 'La generation IA a echoue.'); }
                            targetTextarea.value = payload.generation.generated_text || '';
                            if (action === 'summary' && summaryTargetSelect && payload.generation.suggested_target) { summaryTargetSelect.value = payload.generation.suggested_target; }
                            if (action === 'medical_report' && reportTargetSelect && payload.generation.suggested_target) { reportTargetSelect.value = payload.generation.suggested_target; }
                            prependHistoryItem(payload.generation);
                            setStatus(payload.message || ('Le ' + labelForAction(action) + ' a ete genere. Relisez puis inserez manuellement le texte si besoin.'), 'success');
                        } catch (error) {
                            setStatus(error.message || 'Erreur lors de la generation IA.', 'error');
                        } finally {
                            toggleLoading(false);
                        }
                    });
                });

                if (summaryCopyButton && summaryTextarea) {
                    summaryCopyButton.addEventListener('click', function () {
                        const generated = String(summaryTextarea.value || '').trim();
                        if (!generated) { setStatus('Aucun resume IA a copier.', 'error'); return; }
                        copyText(generated, 'Le resume IA a ete copie dans le presse-papiers.');
                    });
                }

                if (summaryClearButton && summaryTextarea) {
                    summaryClearButton.addEventListener('click', function () { summaryTextarea.value = ''; setStatus('La zone de resume IA a ete effacee.'); });
                }

                if (summaryInsertButton && summaryTextarea) {
                    summaryInsertButton.addEventListener('click', function () {
                        insertIntoConsultation(summaryTargetSelect, summaryTextarea, 'Le resume IA a ete insere dans la consultation. Pensez a relire avant sauvegarde.');
                    });
                }
                if (reportCopyButton && reportTextarea) {
                    reportCopyButton.addEventListener('click', function () {
                        const generated = String(reportTextarea.value || '').trim();
                        if (!generated) { setStatus('Aucun compte rendu IA a copier.', 'error'); return; }
                        copyText(generated, 'Le compte rendu IA a ete copie dans le presse-papiers.');
                    });
                }

                if (reportClearButton && reportTextarea) {
                    reportClearButton.addEventListener('click', function () { reportTextarea.value = ''; setStatus('La zone de compte rendu IA a ete effacee.'); });
                }

                if (reportFocusButton && reportTextarea) {
                    reportFocusButton.addEventListener('click', function () { reportTextarea.focus(); setStatus('Le compte rendu IA est editable. Vous pouvez le modifier librement.'); });
                }

                if (reportInsertButton && reportTextarea) {
                    reportInsertButton.addEventListener('click', function () {
                        insertIntoConsultation(reportTargetSelect, reportTextarea, 'Le compte rendu IA a ete insere dans la consultation. Pensez a relire avant sauvegarde.');
                    });
                }

                if (reportExportButton && reportTextarea) {
                    reportExportButton.addEventListener('click', function () {
                        const content = String(reportTextarea.value || '').trim();
                        if (!exportMedicalReportUrl) { setStatus('La route d export PDF du compte rendu IA est indisponible.', 'error'); return; }
                        if (!content) { setStatus('Aucun compte rendu IA a exporter en PDF.', 'error'); return; }
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = exportMedicalReportUrl;
                        form.target = '_blank';
                        form.style.display = 'none';
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrf;
                        form.appendChild(csrfInput);
                        const contentInput = document.createElement('input');
                        contentInput.type = 'hidden';
                        contentInput.name = 'content';
                        contentInput.value = content;
                        form.appendChild(contentInput);
                        document.body.appendChild(form);
                        form.submit();
                        document.body.removeChild(form);
                        setStatus('Export PDF du compte rendu IA lance. Verifiez le telechargement.', 'success');
                    });
                }

                if (historyList) {
                    historyList.addEventListener('click', function (event) {
                        const trigger = event.target.closest('[data-ai-load-history]');
                        if (!trigger) { return; }
                        const actionType = trigger.dataset.actionType || 'summary';
                        const targetTextarea = targetTextareaForAction(actionType);
                        if (!targetTextarea) { return; }
                        targetTextarea.value = decodeURIComponent(trigger.dataset.generatedText || '');
                        targetTextarea.focus();
                        setStatus('Une generation precedente a ete rechargee dans la zone editable.', 'success');
                    });
                }

                if (!SpeechRecognitionCtor) {
                    setStatus('Assistant pret. La dictee temps reel n est pas supportee sur ce navigateur. Utilisez Chrome ou Edge pour la reconnaissance vocale.', 'warning');
                }
            });
        });
    </script>
    @endpush
@endonce

<section class="cai-panel"
         data-consultation-ai
         data-consultation-id="{{ $assistantEnabled ? $consultation->id : '' }}"
         data-generate-url="{{ $assistantEnabled ? route('consultations.ai.generate', $consultation) : '' }}"
         data-export-medical-report-url="{{ $assistantEnabled ? route('consultations.ai.export-medical-report', $consultation) : '' }}">
    <div class="cai-head">
        <div class="cai-head-copy">
            <h3><i class="fas fa-wand-magic-sparkles"></i> Assistant IA medical</h3>
            <p>Generation assistee, editable et jamais enregistree automatiquement sans validation du medecin.</p>
        </div>
        <span class="cai-badge">
            <i class="fas fa-shield-halved"></i>
            {{ $assistantEnabled ? 'Historisation activee' : 'Active apres enregistrement' }}
        </span>
    </div>

    <div class="cai-body">
        <div class="cai-grid">
                <div class="cai-card">
                    <div class="cai-card-head">
                        <strong>Notes du medecin</strong>
                        <div class="cai-actions-row">
                            <button type="button" class="cai-button info" data-ai-collect>
                                <i class="fas fa-file-circle-plus"></i>
                                <span>Recuperer les champs</span>
                            </button>
                            <button type="button" class="cai-button muted" data-ai-clear-source>
                                <i class="fas fa-eraser"></i>
                                <span>Effacer</span>
                            </button>
                        </div>
                    </div>

                    <div class="cai-dictation-bar">
                        <div class="cai-dictation-meta">
                            <strong>Dictee medicale intelligente</strong>
                            <span>Parlez librement pendant la consultation pour transcrire vos observations dans les notes du medecin.</span>
                        </div>
                        <div class="cai-actions-row">
                            <span class="cai-live-chip" data-ai-dictation-chip>Dictee prete</span>
                            <button type="button" class="cai-button dictation-start" data-ai-dictation-start>
                                <i class="fas fa-microphone"></i>
                                <span>Demarrer dictee</span>
                            </button>
                            <button type="button" class="cai-button dictation-stop" data-ai-dictation-stop disabled>
                                <i class="fas fa-stop"></i>
                                <span>Arreter dictee</span>
                            </button>
                        </div>
                    </div>

                    <div class="cai-live-preview" data-ai-dictation-preview data-placeholder="Le texte transcrit en temps reel apparaitra ici pendant la dictee."></div>

                    <textarea class="cai-textarea" data-ai-source placeholder="Collez ici vos notes libres, dictez vos observations ou utilisez le bouton pour recuperer automatiquement les champs de la consultation."></textarea>

                    <div class="cai-actions-row">
                        <button type="button" class="cai-button primary" data-ai-action="summary" {{ $assistantEnabled ? '' : 'disabled title="Enregistrez d abord la consultation"' }}>
                            <i class="fas fa-align-left"></i>
                            <span>Generer resume IA</span>
                        </button>
                        <button type="button" class="cai-button report" data-ai-action="medical_report" {{ $assistantEnabled ? '' : 'disabled title="Enregistrez d abord la consultation"' }}>
                            <i class="fas fa-file-medical"></i>
                            <span>Generer compte rendu IA</span>
                        </button>
                        <button type="button" class="cai-button muted" disabled title="Disponible dans une prochaine version">
                            <i class="fas fa-pen-fancy"></i>
                            <span>Reformulation bientot</span>
                        </button>
                    </div>

                    <div class="cai-hint">Le resume IA reste concis et structure. Le compte rendu IA produit un document plus complet a partir des notes, des champs de consultation et du resume deja genere si disponible.</div>
                </div>

                <div class="cai-stack">
                    @if($assistantEnabled)
                        <div class="cai-card">
                            <div class="cai-card-head"><strong>Resume IA editable</strong></div>
                            <textarea class="cai-textarea" data-ai-summary-result placeholder="Le resume IA apparaitra ici. Vous pouvez le modifier librement avant toute insertion dans la consultation."></textarea>
                            <div class="cai-toolbar">
                                <div class="cai-toolbar-left">
                                    <select class="cai-select" data-ai-summary-target>
                                        <option value="recommandations">Inserer dans Recommandations</option>
                                        <option value="diagnostic">Inserer dans Diagnostic</option>
                                        <option value="examen_clinique">Inserer dans Examen clinique</option>
                                        <option value="traitement_prescrit">Inserer dans Traitement prescrit</option>
                                        <option value="symptomes">Inserer dans Symptomes</option>
                                    </select>
                                </div>
                                <div class="cai-toolbar-right">
                                    <button type="button" class="cai-button info" data-ai-copy-summary><i class="fas fa-copy"></i><span>Copier</span></button>
                                    <button type="button" class="cai-button warning" data-ai-clear-summary><i class="fas fa-trash-alt"></i><span>Effacer</span></button>
                                    <button type="button" class="cai-button success" data-ai-insert-summary><i class="fas fa-file-import"></i><span>Inserer dans la consultation</span></button>
                                </div>
                            </div>
                        </div>

                        <div class="cai-card">
                            <div class="cai-card-head"><strong>Compte rendu IA editable</strong></div>
                            <textarea class="cai-textarea report" data-ai-report-result placeholder="Le compte rendu IA apparaitra ici. Vous pouvez le modifier librement avant insertion ou export PDF."></textarea>
                            <div class="cai-toolbar">
                                <div class="cai-toolbar-left">
                                    <select class="cai-select" data-ai-report-target>
                                        <option value="diagnostic">Inserer dans Diagnostic</option>
                                        <option value="recommandations">Inserer dans Recommandations</option>
                                        <option value="examen_clinique">Inserer dans Examen clinique</option>
                                        <option value="traitement_prescrit">Inserer dans Traitement prescrit</option>
                                    </select>
                                </div>
                                <div class="cai-toolbar-right">
                                    <button type="button" class="cai-button info" data-ai-copy-report><i class="fas fa-copy"></i><span>Copier</span></button>
                                    <button type="button" class="cai-button muted" data-ai-focus-report><i class="fas fa-pen"></i><span>Modifier</span></button>
                                    <button type="button" class="cai-button warning" data-ai-clear-report><i class="fas fa-trash-alt"></i><span>Effacer</span></button>
                                    <button type="button" class="cai-button success" data-ai-insert-report><i class="fas fa-file-import"></i><span>Inserer dans la consultation</span></button>
                                    <button type="button" class="cai-button pdf" data-ai-export-report><i class="fas fa-file-pdf"></i><span>Exporter en PDF</span></button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="cai-card">
                            <div class="cai-card-head"><strong>Generation IA</strong></div>
                            <div class="cai-locked">
                                <strong>Resume IA et compte rendu disponibles apres enregistrement</strong>
                                <span>Vous pouvez deja dicter et preparer les notes du medecin. Des que la consultation est enregistree, les actions IA deviennent actives et les generations sont historisees.</span>
                            </div>
                        </div>
                    @endif

                    <div class="cai-status" data-ai-status>Assistant pret. Chargez les notes cliniques puis lancez un resume IA ou un compte rendu IA.</div>
                </div>
            </div>

            <div class="cai-card">
                <div class="cai-card-head">
                    <strong>Historique des generations IA</strong>
                    <span class="cai-hint">Chaque generation est liee a cette consultation et a l utilisateur connecte.</span>
                </div>
                @if($assistantEnabled)
                    <div class="cai-history" data-ai-history>
                        @forelse($historyItems as $history)
                            <div class="cai-history-item">
                                <div class="cai-history-head">
                                    <span class="cai-history-type"><i class="fas fa-wand-magic-sparkles"></i>{{ $history->action_label }}</span>
                                    <button type="button" class="cai-button info" data-ai-load-history data-action-type="{{ $history->action_type }}" data-generated-text="{{ rawurlencode($history->generated_text) }}">
                                        <i class="fas fa-arrow-down"></i>
                                        <span>Charger</span>
                                    </button>
                                </div>
                                <div class="cai-history-meta">
                                    <span>{{ optional($history->created_at)->format('d/m/Y H:i') }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $history->user?->name ?? 'Utilisateur' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ data_get($history->context_payload, 'provider', 'assistant') }}</span>
                                </div>
                                <div class="cai-history-preview">{{ \Illuminate\Support\Str::limit($history->generated_text, 220) }}</div>
                            </div>
                        @empty
                            <div class="cai-empty">Aucune generation IA pour cette consultation. Les prochaines generations apparaitront ici avec leur historique.</div>
                        @endforelse
                    </div>
                @else
                    <div class="cai-locked">
                        <strong>Historique active apres creation</strong>
                        <span>La dictee medicale est deja disponible sur cette page, mais l historique IA n est cree qu une fois la consultation enregistree.</span>
                    </div>
                @endif
            </div>
        </div>
    </div></section>

















