function initLayoutChrome() {
    const body = document.body;
    const root = document.documentElement;
    const storedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const dark = storedTheme
        ? storedTheme === 'dark'
        : (localStorage.getItem('darkMode') === 'true' || body.classList.contains('theme-dark') || body.classList.contains('dark-mode') || prefersDark);

    const applyDarkMode = function (enabled) {
        root.classList.toggle('dark', enabled);
        body.classList.toggle('theme-dark', enabled);
        body.classList.toggle('dark-mode', enabled);
        localStorage.setItem('theme', enabled ? 'dark' : 'light');
        localStorage.setItem('darkMode', enabled ? 'true' : 'false');
    };

    applyDarkMode(dark);

    const darkToggle = document.getElementById('topDarkModeToggle');
    if (darkToggle) {
        darkToggle.checked = dark;
        if (!darkToggle.dataset.layoutBound) {
            darkToggle.addEventListener('change', function (e) {
                applyDarkMode(Boolean(e.target.checked));
            });
            darkToggle.dataset.layoutBound = 'true';
        }
    }

    const globalSearch = document.getElementById('globalTopbarSearch');
    if (globalSearch && !globalSearch.dataset.layoutBound) {
        globalSearch.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') {
                return;
            }

            const value = (globalSearch.value || '').trim();
            if (!value || typeof window.find !== 'function') {
                return;
            }

            e.preventDefault();
            window.find(value, false, false, true, false, false, false);
        });
        globalSearch.dataset.layoutBound = 'true';
    }

    const dedupeModuleHeading = function () {
        const mainContent = document.getElementById('mainContent');
        if (!mainContent) {
            return;
        }

        mainContent.querySelectorAll('.module-title-deduped').forEach(function (node) {
            node.classList.remove('module-title-deduped');
        });
        mainContent.querySelectorAll('.module-header-deduped').forEach(function (node) {
            node.classList.remove('module-header-deduped');
        });
        mainContent.querySelectorAll('.module-header-context[data-generated="true"]').forEach(function (node) {
            node.remove();
        });

        const topbarTitleElement = document.querySelector('.app-page-header .topbar-heading-title');
        const topbarSubtitleElement = document.querySelector('.app-page-header .topbar-heading-subtitle');
        const moduleTitle = (topbarTitleElement ? topbarTitleElement.textContent : '').trim();
        const moduleSubtitle = (topbarSubtitleElement ? topbarSubtitleElement.textContent : '').trim();
        if (!moduleTitle) {
            return;
        }

        const normalizeText = function (value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/&/g, ' et ')
                .replace(/[^a-z0-9\s]/g, ' ')
                .replace(/\b(du|de|des|la|le|les|d|l|un|une|et|centre|gestion|module|espace|detail|details|fiche|vue|page|terminee|termine|planifiee|planifie|en|cours|attente)\b/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
        };

        const isVisible = function (element) {
            if (!element || element.closest('.app-page-header, .topbar-dropdown, .modal, [aria-hidden="true"]')) {
                return false;
            }

            const styles = window.getComputedStyle(element);
            if (styles.display === 'none' || styles.visibility === 'hidden') {
                return false;
            }

            return element.getClientRects().length > 0;
        };

        const areDuplicateTitles = function (topbarValue, contentValue) {
            const normalizedTopbar = normalizeText(topbarValue);
            const normalizedContent = normalizeText(contentValue);
            if (!normalizedTopbar || !normalizedContent) {
                return false;
            }

            if (normalizedTopbar === normalizedContent) {
                return true;
            }

            if (normalizedTopbar.includes(normalizedContent) || normalizedContent.includes(normalizedTopbar)) {
                return true;
            }

            const topbarTokens = normalizedTopbar.split(' ').filter(Boolean);
            const contentTokens = normalizedContent.split(' ').filter(Boolean);
            if (!topbarTokens.length || !contentTokens.length) {
                return false;
            }

            const overlap = topbarTokens.filter(function (token) {
                return contentTokens.includes(token);
            }).length;

            return overlap / Math.min(topbarTokens.length, contentTokens.length) >= 0.75;
        };

        const candidates = Array.from(mainContent.querySelectorAll('h1, h2')).slice(0, 12);
        const duplicateHeading = candidates.find(function (heading) {
            return isVisible(heading) && areDuplicateTitles(moduleTitle, heading.textContent || '');
        });

        if (!duplicateHeading) {
            return;
        }

        duplicateHeading.classList.add('module-title-deduped');

        const headerContainer = duplicateHeading.closest(
            '.page-header, .page-title, .page-header-content, .cc-hero-title-row, .patient-hero-copy, .rdv-show-head, .rdv-edit-head, .dossier-edit-main, .reports-hero, .stats-report-hero'
        ) || duplicateHeading.parentElement;

        if (!headerContainer) {
            return;
        }

        headerContainer.classList.add('module-header-deduped');

        const hasVisibleSupportText = Array.from(headerContainer.children).some(function (child) {
            if (child === duplicateHeading) {
                return false;
            }

            if (child.classList && child.classList.contains('module-header-context')) {
                return true;
            }

            const tagName = child.tagName || '';
            if (!['P', 'SMALL', 'DIV', 'SPAN'].includes(tagName)) {
                return false;
            }

            return isVisible(child) && normalizeText(child.textContent || '') !== '';
        });

        if (!hasVisibleSupportText && moduleSubtitle !== '') {
            const context = document.createElement('p');
            context.className = 'module-header-context';
            context.dataset.generated = 'true';
            context.textContent = moduleSubtitle;
            duplicateHeading.insertAdjacentElement('afterend', context);
        }
    };

    const scheduleAfterPaint = window.requestAnimationFrame
        ? window.requestAnimationFrame.bind(window)
        : function (callback) { window.setTimeout(callback, 16); };
    scheduleAfterPaint(dedupeModuleHeading);

    const userMenu = document.getElementById('topUserMenu');
    const userMenuBtn = document.getElementById('topUserMenuBtn');
    const userMenuDropdown = document.getElementById('topUserDropdown');
    if (userMenu && userMenuBtn && !userMenuBtn.dataset.layoutBound) {
        userMenuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = userMenu.classList.toggle('open');
            userMenuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        userMenuBtn.dataset.layoutBound = 'true';
    }

    if (userMenuDropdown && !userMenuDropdown.dataset.layoutBound) {
        userMenuDropdown.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const liveMenu = document.getElementById('topUserMenu');
                const liveButton = document.getElementById('topUserMenuBtn');
                if (liveMenu) {
                    liveMenu.classList.remove('open');
                }
                if (liveButton) {
                    liveButton.setAttribute('aria-expanded', 'false');
                    liveButton.focus();
                }
            }
        });
        userMenuDropdown.dataset.layoutBound = 'true';
    }

    const topbarLogoutForm = document.getElementById('topbarLogoutForm');
    if (topbarLogoutForm && !topbarLogoutForm.dataset.layoutBound) {
        topbarLogoutForm.addEventListener('submit', function (e) {
            if (!window.confirm('Confirmer la deconnexion ?')) {
                e.preventDefault();
            }
        });
        topbarLogoutForm.dataset.layoutBound = 'true';
    }

    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    if (mobileMenuBtn && !mobileMenuBtn.dataset.layoutBound) {
        mobileMenuBtn.addEventListener('click', function () {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggleBtn = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (!sidebar || window.innerWidth > 768) {
                return;
            }

            if (sidebarToggleBtn) {
                sidebarToggleBtn.click();
                return;
            }

            const isOpen = !sidebar.classList.contains('mobile-open');
            sidebar.classList.toggle('mobile-open', isOpen);
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('mobile-open', isOpen);
            }
        });
        mobileMenuBtn.dataset.layoutBound = 'true';
    }
}

if (!window.__medisysLayoutGlobalsBound) {
    document.addEventListener('click', function (e) {
        const userMenu = document.getElementById('topUserMenu');
        const userMenuBtn = document.getElementById('topUserMenuBtn');
        if (!userMenu || !userMenuBtn || userMenu.contains(e.target)) {
            return;
        }

        userMenu.classList.remove('open');
        userMenuBtn.setAttribute('aria-expanded', 'false');
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') {
            return;
        }

        const userMenu = document.getElementById('topUserMenu');
        const userMenuBtn = document.getElementById('topUserMenuBtn');
        if (!userMenu || !userMenuBtn) {
            return;
        }

        userMenu.classList.remove('open');
        userMenuBtn.setAttribute('aria-expanded', 'false');
        userMenuBtn.focus();
    });

    window.__medisysLayoutGlobalsBound = true;
}

window.initLayoutChrome = initLayoutChrome;

document.addEventListener('DOMContentLoaded', function () {
    initLayoutChrome();
});