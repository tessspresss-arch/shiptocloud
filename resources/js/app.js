import './bootstrap';
import './layout';
import { initClientTelemetry } from './client-telemetry';
import { initPartialNavigation } from './partial-navigation';
import { initConsultationForms } from './pages/consultation-form';
import { initExamForms } from './pages/exam-form';
import { initAgendaPage } from './pages/agenda-page';
import { initOrdonnanceCreate } from './pages/ordonnance-create';
import { initOrdonnancesIndex } from './pages/ordonnances-index';
import { initPatientOrdonnanceModal } from './pages/patient-ordonnance-modal';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
initClientTelemetry();

const prefetchedDocumentUrls = new Set();
const MAX_IDLE_PREFETCH_LINKS = 8;
let resizeFrame = null;

function normalizeNavigationUrl(href) {
    if (!href) {
        return null;
    }

    try {
        return new URL(href, window.location.origin);
    } catch {
        return null;
    }
}

function isPrefetchableNavigation(link) {
    if (!link || link.target === '_blank' || link.hasAttribute('download')) {
        return false;
    }

    const href = link.getAttribute('href') || '';
    if (
        href === ''
        || href.startsWith('#')
        || href.startsWith('javascript:')
        || href.startsWith('mailto:')
        || href.startsWith('tel:')
    ) {
        return false;
    }

    const url = normalizeNavigationUrl(href);
    if (!url || url.origin !== window.location.origin) {
        return false;
    }

    return url.pathname !== window.location.pathname || url.search !== window.location.search;
}

function prefetchDocument(link) {
    if (!isPrefetchableNavigation(link)) {
        return;
    }

    const url = normalizeNavigationUrl(link.href);
    if (!url) {
        return;
    }

    const key = url.toString();
    if (prefetchedDocumentUrls.has(key)) {
        return;
    }

    const prefetch = document.createElement('link');
    prefetch.rel = 'prefetch';
    prefetch.as = 'document';
    prefetch.href = key;
    document.head.appendChild(prefetch);

    prefetchedDocumentUrls.add(key);
}

function scheduleIdleTask(callback) {
    if (typeof window.requestIdleCallback === 'function') {
        window.requestIdleCallback(callback, { timeout: 1200 });
        return;
    }

    window.setTimeout(callback, 180);
}

function prefetchPriorityLinks() {
    const priorityLinks = Array.from(document.querySelectorAll('a[data-nav-priority="module"][href]'))
        .filter((link) => isPrefetchableNavigation(link))
        .slice(0, MAX_IDLE_PREFETCH_LINKS);

    if (!priorityLinks.length) {
        return;
    }

    scheduleIdleTask(() => {
        priorityLinks.forEach((link) => {
            prefetchDocument(link);
        });
    });
}

function initializePageModules(root = document) {
    initConsultationForms(root);
    initExamForms(root);
    initAgendaPage(root);
    initOrdonnanceCreate(root);
    initOrdonnancesIndex(root);
    initPatientOrdonnanceModal(root);
}
function enableNavigationFeedback() {
    const body = document.body;

    const activate = () => body.classList.add('nav-loading');
    const deactivate = () => body.classList.remove('nav-loading');

    document.addEventListener('mouseover', (event) => {
        const link = event.target.closest('a[href]');
        const previousLink = event.relatedTarget && typeof event.relatedTarget.closest === 'function'
            ? event.relatedTarget.closest('a[href]')
            : null;
        if (link && previousLink !== link) {
            prefetchDocument(link);
        }
    }, { passive: true });

    document.addEventListener('touchstart', (event) => {
        const link = event.target.closest('a[href]');
        if (link) {
            prefetchDocument(link);
        }
    }, { passive: true });

    document.addEventListener('focusin', (event) => {
        const link = event.target.closest('a[href]');
        if (link) {
            prefetchDocument(link);
        }
    }, { passive: true });

    document.addEventListener('pointerdown', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || !isPrefetchableNavigation(link)) {
            return;
        }

        if (
            event.button !== 0
            || event.metaKey
            || event.ctrlKey
            || event.shiftKey
            || event.altKey
        ) {
            return;
        }

        prefetchDocument(link);
        activate();
    }, true);

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || !isPrefetchableNavigation(link)) {
            return;
        }

        if (
            event.defaultPrevented
            || event.button !== 0
            || event.metaKey
            || event.ctrlKey
            || event.shiftKey
            || event.altKey
        ) {
            return;
        }

        activate();
    }, true);

    window.addEventListener('pageshow', deactivate);
    window.addEventListener('pagehide', activate);
}
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    enableNavigationFeedback();
    prefetchPriorityLinks();
    initPartialNavigation();
    initializePageModules(document);

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarIcon = document.getElementById('sidebarIcon');

    function setAria(expanded) {
        if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            // Toggle sur mobile
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.toggle('mobile-open');
                }
                // Icon mobile: bars <-> times
                if (sidebarIcon) {
                    if (sidebar.classList.contains('mobile-open')) {
                        sidebarIcon.className = 'fas fa-times';
                        sidebarToggle.setAttribute('aria-label', 'Fermer le menu');
                    } else {
                        sidebarIcon.className = 'fas fa-bars';
                        sidebarToggle.setAttribute('aria-label', 'Ouvrir le menu');
                    }
                }
            } else {
                // Collapse sur desktop
                const collapsed = document.body.classList.toggle('sidebar-collapsed');
                if (collapsed) {
                    // Fermer visuellement tous les sous-menus quand la sidebar passe en mode rÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â©duit
                    document.querySelectorAll('.has-submenu.expanded').forEach((item) => {
                        item.classList.remove('expanded');

                        const submenu = item.querySelector('.nav-submenu');
                        if (submenu) {
                            submenu.style.maxHeight = '0';
                            submenu.style.opacity = '0';
                            submenu.style.overflow = 'hidden';
                            submenu.style.display = 'none';
                        }

                        const arrow = item.querySelector('.nav-arrow i');
                        if (arrow) {
                            arrow.style.transform = 'rotate(0deg)';
                        }
                    });
                } else {
                    // Re-ouvrir le sous-menu actif apres retour en mode normal
                    const activeSubmenu = document.querySelector('.has-submenu.active');
                    const activeMain = activeSubmenu ? activeSubmenu.querySelector('.nav-item-main') : null;
                    if (activeMain && typeof window.toggleSubmenu === 'function' && !activeSubmenu.classList.contains('expanded')) {
                        window.toggleSubmenu(activeMain);
                    }
                }
                setAria(!collapsed);
                if (sidebarIcon) {
                    sidebarIcon.className = collapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
                }
                sidebarToggle.setAttribute('aria-label', collapsed ? 'Ouvrir le menu' : 'Réduire le menu');
            }
        });
    }

    // Fermer sidebar overlay au clic
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
            }
            sidebarOverlay.classList.remove('mobile-open');
            if (sidebarIcon) {
                sidebarIcon.className = 'fas fa-bars';
            }
            if (sidebarToggle) {
                sidebarToggle.setAttribute('aria-label', 'Ouvrir le menu');
            }
        });
    }

    // GÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â©rer le resize
    window.addEventListener('resize', function() {
        if (resizeFrame !== null) {
            return;
        }

        resizeFrame = window.requestAnimationFrame(function () {
            resizeFrame = null;

            if (window.innerWidth > 768) {
                if (sidebar) {
                    sidebar.classList.remove('mobile-open');
                }
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('mobile-open');
                }
            }
        });
    });

    // Initialisation de l'ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â©tat du bouton
    const collapsedInit = document.body.classList.contains('sidebar-collapsed');
    setAria(!collapsedInit);
    if (sidebarIcon) {
        sidebarIcon.className = collapsedInit ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
    }
    if (sidebarToggle) {
        sidebarToggle.setAttribute('aria-label', collapsedInit ? 'Ouvrir le menu' : 'Réduire le menu');
    }
});


document.addEventListener('medisys:page-loaded', function (event) {
    initializePageModules(event.detail?.container || document);
});
