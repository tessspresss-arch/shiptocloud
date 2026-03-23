export function initExamForms(root = document) {
    const forms = root.querySelectorAll('#examCreateForm, #examEditForm');

    forms.forEach((form) => {
        if (form.dataset.medisysBound === '1') {
            return;
        }

        form.dataset.medisysBound = '1';

        const errorField = form.querySelector('.exam-record-input.error, .exam-record-select.error, .exam-record-textarea.error');
        if (errorField) {
            errorField.focus();
            errorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
}