function bindConsultationForm(root, config) {
    const form = root.querySelector(config.formSelector);
    if (!form || form.dataset.medisysBound === '1') {
        return;
    }

    form.dataset.medisysBound = '1';

    const poidsInput = root.querySelector(config.poidsSelector);
    const tailleInput = root.querySelector(config.tailleSelector);
    const bmiBox = root.querySelector(config.bmiBoxSelector);
    const bmiValue = root.querySelector(config.bmiValueSelector);
    const systolicInput = root.querySelector(config.systolicSelector);
    const diastolicInput = root.querySelector(config.diastolicSelector);

    const calculateBMI = () => {
        const poids = parseFloat(poidsInput?.value || '');
        const taille = parseFloat(tailleInput?.value || '');

        if (poids > 0 && taille > 0 && bmiBox && bmiValue) {
            const tailleMetres = taille / 100;
            const bmi = (poids / (tailleMetres * tailleMetres)).toFixed(1);
            bmiValue.textContent = bmi;
            bmiBox.style.display = 'flex';
            return;
        }

        if (bmiValue) {
            bmiValue.textContent = '--';
        }

        if (bmiBox) {
            bmiBox.style.display = 'none';
        }
    };

    poidsInput?.addEventListener('input', calculateBMI);
    tailleInput?.addEventListener('input', calculateBMI);
    calculateBMI();

    form.addEventListener('submit', (event) => {
        const systolic = parseInt(systolicInput?.value || '', 10);
        const diastolic = parseInt(diastolicInput?.value || '', 10);

        if (!Number.isNaN(systolic) && !Number.isNaN(diastolic) && systolic <= diastolic) {
            event.preventDefault();
            window.alert('La tension systolique doit etre superieure a la diastolique.');
        }
    });
}

export function initConsultationForms(root = document) {
    bindConsultationForm(root, {
        formSelector: '#consultationCreateForm',
        poidsSelector: '#poids',
        tailleSelector: '#taille',
        bmiBoxSelector: '#bmiBox',
        bmiValueSelector: '#bmiValue',
        systolicSelector: '#tas',
        diastolicSelector: '#tad',
    });

    bindConsultationForm(root, {
        formSelector: '#consultationForm',
        poidsSelector: '#poids',
        tailleSelector: '#taille',
        bmiBoxSelector: '#bmiCalculator',
        bmiValueSelector: '#bmiResult',
        systolicSelector: '#tension_systolique',
        diastolicSelector: '#tension_diastolique',
    });
}