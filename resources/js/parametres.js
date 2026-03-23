// Paramètres JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeSettings();
});

function initializeSettings() {
    // Navigation handling
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.getAttribute('data-category');
            switchCategory(category);
        });
    });

    // Form handling
    const settingsForm = document.getElementById('settingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveAllSettings();
        });
    }

    // Auto-save on input change (debounced)
    const inputs = document.querySelectorAll('.setting-input, .setting-select, .setting-textarea');
    inputs.forEach(input => {
        input.addEventListener('change', debounce(saveAllSettings, 1000));
    });

    // Switch handling
    const switches = document.querySelectorAll('.setting-switch input[type="checkbox"]');
    switches.forEach(switchEl => {
        switchEl.addEventListener('change', function() {
            // Update visual state immediately
            const slider = this.nextElementSibling;
            if (this.checked) {
                slider.classList.add('checked');
            } else {
                slider.classList.remove('checked');
            }
            // Save settings
            debounce(saveAllSettings, 500)();
        });
    });
}

function switchCategory(category) {
    // Update navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-category="${category}"]`).classList.add('active');

    // Update content
    document.querySelectorAll('.settings-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(category).classList.add('active');

    // Update URL hash
    window.location.hash = category;
}

function saveAllSettings() {
    const formData = new FormData(document.getElementById('settingsForm'));
    const settings = {};

    // Collect all form data
    for (let [key, value] of formData.entries()) {
        if (key === '_token') continue;

        // Determine category and type from the key
        const category = getCategoryFromKey(key);
        const type = getTypeFromKey(key);

        if (!settings[key]) {
            settings[key] = {
                key: key,
                value: value,
                category: category,
                type: type
            };
        }
    }

    // Send to server
    fetch('/parametres', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
        },
        body: JSON.stringify({
            settings: settings,
            _token: formData.get('_token')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Paramètres sauvegardés avec succès', 'success');
        } else {
            showToast('Erreur lors de la sauvegarde: ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Erreur de connexion', 'error');
    });
}

function resetSettings() {
    if (!confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres aux valeurs par défaut ?')) {
        return;
    }

    fetch('/parametres/reset', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            _token: document.querySelector('input[name="_token"]').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Paramètres réinitialisés avec succès', 'success');
            // Reload page to show default values
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('Erreur lors de la réinitialisation: ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Erreur de connexion', 'error');
    });
}

function getCategoryFromKey(key) {
    const categories = {
        'app_name': 'general',
        'timezone': 'general',
        'language': 'general',
        'cabinet_name': 'cabinet',
        'cabinet_address': 'cabinet',
        'cabinet_phone': 'cabinet',
        'cabinet_email': 'cabinet',
        'default_user_role': 'users',
        'password_min_length': 'users',
        'default_consultation_duration': 'medecins',
        'working_hours_start': 'medecins',
        'working_hours_end': 'medecins',
        'email_reminders': 'notifications',
        'sms_reminders': 'notifications',
        'session_timeout': 'security',
        'max_login_attempts': 'security'
    };

    return categories[key] || 'general';
}

function getTypeFromKey(key) {
    const types = {
        'password_min_length': 'integer',
        'default_consultation_duration': 'integer',
        'session_timeout': 'integer',
        'max_login_attempts': 'integer',
        'email_reminders': 'boolean',
        'sms_reminders': 'boolean'
    };

    return types[key] || 'string';
}

function showToast(message, type = 'info') {
    const toast = document.getElementById('settingsToast');
    const toastBody = document.getElementById('toastMessage');

    // Update message
    toastBody.textContent = message;

    // Update styling based on type
    const toastHeader = toast.querySelector('.toast-header');
    const icon = toastHeader.querySelector('i');

    toast.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
    icon.className = '';

    switch (type) {
        case 'success':
            toast.classList.add('bg-success');
            icon.className = 'fas fa-check-circle me-2';
            break;
        case 'error':
            toast.classList.add('bg-danger');
            icon.className = 'fas fa-exclamation-triangle me-2';
            break;
        case 'warning':
            toast.classList.add('bg-warning');
            icon.className = 'fas fa-exclamation-circle me-2';
            break;
        default:
            toast.classList.add('bg-info');
            icon.className = 'fas fa-info-circle me-2';
    }

    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle URL hash on page load
window.addEventListener('load', function() {
    const hash = window.location.hash.substring(1);
    if (hash && document.getElementById(hash)) {
        switchCategory(hash);
    }
});

// Handle browser back/forward
window.addEventListener('hashchange', function() {
    const hash = window.location.hash.substring(1);
    if (hash && document.getElementById(hash)) {
        switchCategory(hash);
    }
});
