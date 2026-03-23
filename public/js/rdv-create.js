document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('rdvCreateForm');
    if (!form) {
        return;
    }

    const inputDate = document.getElementById('inputDate');
    const inputTime = document.getElementById('inputTime');
    const displayDate = document.getElementById('displayDate');
    const displayTime = document.getElementById('displayTime');
    const patientSelect = document.getElementById('patientSelect');
    const patientSearch = document.getElementById('patientSearch');
    const patientMiniCard = document.getElementById('patientMiniCard');
    const submitBtn = document.getElementById('submitBtn');
    const slotSearch = document.getElementById('slotSearch');

    const summaryFields = {
        date: document.querySelectorAll('[data-summary="date"]'),
        time: document.querySelectorAll('[data-summary="time"]'),
        patient: document.querySelectorAll('[data-summary="patient"]'),
        doctor: document.querySelectorAll('[data-summary="doctor"]'),
        motif: document.querySelectorAll('[data-summary="motif"]'),
    };

    function updateSummaryField(field, value) {
        summaryFields[field].forEach(function (item) {
            item.textContent = value || '-';
        });
    }

    function formatDateFR(isoDate) {
        const parts = (isoDate || '').split('-');
        if (parts.length !== 3) {
            return '-';
        }
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function updatePatientMiniCard() {
        if (!patientSelect) {
            return;
        }

        const option = patientSelect.options[patientSelect.selectedIndex];
        const hasValue = !!(option && option.value);
        if (!patientMiniCard) {
            return;
        }

        if (!hasValue) {
            patientMiniCard.classList.add('is-hidden');
            updateSummaryField('patient', '-');
            return;
        }

        const name = option.dataset.fullName || option.textContent || '-';
        const phone = option.dataset.phone || '-';
        const cin = option.dataset.cin || '-';
        const age = option.dataset.age ? option.dataset.age + ' ans' : '-';

        patientMiniCard.classList.remove('is-hidden');
        const nameEl = patientMiniCard.querySelector('[data-patient-field="name"]');
        const phoneEl = patientMiniCard.querySelector('[data-patient-field="phone"]');
        const cinEl = patientMiniCard.querySelector('[data-patient-field="cin"]');
        const ageEl = patientMiniCard.querySelector('[data-patient-field="age"]');

        if (nameEl) nameEl.textContent = name;
        if (phoneEl) phoneEl.textContent = phone;
        if (cinEl) cinEl.textContent = cin;
        if (ageEl) ageEl.textContent = age;

        updateSummaryField('patient', name);
    }

    function updateDoctorCards() {
        const checked = form.querySelector('input[name="medecin_id"]:checked');
        form.querySelectorAll('[data-doctor-card]').forEach(function (card) {
            const radio = card.querySelector('input[name="medecin_id"]');
            card.classList.toggle('selected', !!(checked && radio && checked.value === radio.value));
        });
        updateSummaryField('doctor', checked ? checked.dataset.doctorName : '-');
    }

    function updateMotifCards() {
        const checked = form.querySelector('input[name="motif"]:checked');
        form.querySelectorAll('[data-motif-card]').forEach(function (card) {
            const radio = card.querySelector('input[name="motif"]');
            card.classList.toggle('selected', !!(checked && radio && checked.value === radio.value));
        });
        updateSummaryField('motif', checked ? checked.value : '-');
    }

    function updateDateDisplays() {
        const fr = formatDateFR(inputDate ? inputDate.value : '');
        if (displayDate) {
            displayDate.value = fr;
        }
        updateSummaryField('date', fr);
    }

    function updateTimeDisplays() {
        const time = inputTime ? inputTime.value : '';
        if (displayTime) {
            displayTime.value = time || '-';
        }
        updateSummaryField('time', time || '-');
    }

    function checkFormValidity() {
        const hasPatient = !!(patientSelect && patientSelect.value);
        const hasDoctor = !!form.querySelector('input[name="medecin_id"]:checked');
        const hasMotif = !!form.querySelector('input[name="motif"]:checked');
        const hasDate = !!(inputDate && inputDate.value);
        const hasTime = !!(inputTime && inputTime.value);

        if (submitBtn) {
            submitBtn.disabled = !(hasPatient && hasDoctor && hasMotif && hasDate && hasTime);
        }
    }

    function updateSlotVisibility() {
        const activeFilter = document.querySelector('[data-slot-filter].is-active')?.dataset.slotFilter || 'all';
        const search = (slotSearch?.value || '').trim().toLowerCase();

        document.querySelectorAll('[data-slot-time]').forEach(function (slotBtn) {
            const time = (slotBtn.dataset.slotTime || '').toLowerCase();
            const period = slotBtn.dataset.period || 'all';

            const periodMatches = activeFilter === 'all' || activeFilter === period;
            const searchMatches = !search || time.includes(search);
            slotBtn.style.display = periodMatches && searchMatches ? '' : 'none';
        });
    }

    // Gestion de la navigation de mois dans le calendrier.
    document.querySelectorAll('[data-month-nav]').forEach(function (button) {
        button.addEventListener('click', function () {
            const offset = Number(button.dataset.monthNav || '0');
            const base = inputDate && inputDate.value ? new Date(inputDate.value + 'T00:00:00') : new Date();
            if (Number.isNaN(base.getTime())) {
                return;
            }

            base.setMonth(base.getMonth() + offset);
            base.setDate(1);

            const next = base.getFullYear() + '-' + String(base.getMonth() + 1).padStart(2, '0') + '-01';
            const url = new URL(window.location.href);
            url.searchParams.set('date', next);
            if (inputTime && inputTime.value) {
                url.searchParams.set('heure', inputTime.value);
            }
            if (patientSelect && patientSelect.value) {
                url.searchParams.set('patient_id', patientSelect.value);
            }
            window.location.href = url.toString();
        });
    });

    // Gestion du choix de date et d'heure.
    document.querySelectorAll('[data-day]').forEach(function (dayBtn) {
        dayBtn.addEventListener('click', function () {
            if (dayBtn.disabled || !inputDate) {
                return;
            }
            inputDate.value = dayBtn.dataset.day || '';
            document.querySelectorAll('[data-day]').forEach(function (item) {
                item.classList.remove('selected');
            });
            dayBtn.classList.add('selected');
            updateDateDisplays();
            checkFormValidity();
        });
    });

    document.querySelectorAll('[data-slot-time]').forEach(function (slotBtn) {
        slotBtn.addEventListener('click', function () {
            if (slotBtn.disabled || !inputTime) {
                return;
            }
            inputTime.value = slotBtn.dataset.slotTime || '';
            document.querySelectorAll('[data-slot-time]').forEach(function (item) {
                item.classList.remove('selected');
            });
            slotBtn.classList.add('selected');
            updateTimeDisplays();
            checkFormValidity();
        });
    });

    document.querySelectorAll('[data-slot-filter]').forEach(function (filterBtn) {
        filterBtn.addEventListener('click', function () {
            document.querySelectorAll('[data-slot-filter]').forEach(function (item) {
                item.classList.remove('is-active');
            });
            filterBtn.classList.add('is-active');
            updateSlotVisibility();
        });
    });

    if (slotSearch) {
        slotSearch.addEventListener('input', updateSlotVisibility);
    }

    if (patientSearch && patientSelect) {
        patientSearch.addEventListener('input', function () {
            const query = patientSearch.value.trim().toLowerCase();
            Array.from(patientSelect.options).forEach(function (option, index) {
                if (index === 0) {
                    return;
                }
                const haystack = (option.textContent + ' ' + (option.dataset.cin || '')).toLowerCase();
                option.hidden = !!query && !haystack.includes(query);
            });
        });
    }

    if (patientSelect) {
        patientSelect.addEventListener('change', function () {
            updatePatientMiniCard();
            checkFormValidity();
        });
    }

    form.querySelectorAll('input[name="medecin_id"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            updateDoctorCards();
            checkFormValidity();
        });
    });

    form.querySelectorAll('input[name="motif"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            updateMotifCards();
            checkFormValidity();
        });
    });

    // Accordéon mobile pour les sections du workflow.
    const isMobile = window.matchMedia('(max-width: 767.98px)').matches;
    const steps = Array.from(document.querySelectorAll('[data-step]'));
    steps.forEach(function (step, index) {
        step.dataset.collapsed = isMobile && index > 0 ? 'true' : 'false';
    });

    document.querySelectorAll('[data-accordion-toggle]').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            if (!window.matchMedia('(max-width: 767.98px)').matches) {
                return;
            }
            const step = toggle.closest('[data-step]');
            if (!step) {
                return;
            }
            const isCollapsed = step.dataset.collapsed === 'true';
            step.dataset.collapsed = isCollapsed ? 'false' : 'true';
        });
    });

    // Focus sur le premier champ invalide après validation Laravel.
    const firstInvalid = form.querySelector('.is-invalid');
    if (firstInvalid) {
        firstInvalid.focus();
        const containingStep = firstInvalid.closest('[data-step]');
        if (containingStep) {
            containingStep.dataset.collapsed = 'false';
        }
    }

    // Protection double soumission + état loading.
    form.addEventListener('submit', function () {
        if (!submitBtn || submitBtn.disabled || submitBtn.classList.contains('is-loading')) {
            return;
        }
        submitBtn.classList.add('is-loading');
        submitBtn.disabled = true;
    });

    updateDateDisplays();
    updateTimeDisplays();
    updatePatientMiniCard();
    updateDoctorCards();
    updateMotifCards();
    updateSlotVisibility();
    checkFormValidity();
});
