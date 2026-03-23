const MODULE_LINK_SELECTOR = 'a[data-nav-priority="module"][href]';
const PAGE_STYLES_START_ID = 'medisys-page-styles-start';
const PAGE_STYLES_END_ID = 'medisys-page-styles-end';
const PAGE_SCRIPTS_START_ID = 'medisys-page-scripts-start';
const PAGE_SCRIPTS_END_ID = 'medisys-page-scripts-end';

const runtimeState = {
    controller: null,
    navigationId: 0,
    cleanupHandlers: [],
};

window.__medisysRegisterCleanup = function (callback) {
    if (typeof callback === 'function') {
        runtimeState.cleanupHandlers.push(callback);
    }
};

function normalizeUrl(href) {
    if (!href) {
        return null;
    }

    try {
        return new URL(href, window.location.origin);
    } catch {
        return null;
    }
}

function isEligibleLink(link) {
    if (!link || !link.matches(MODULE_LINK_SELECTOR) || link.target === '_blank' || link.hasAttribute('download')) {
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

    const url = normalizeUrl(href);
    if (!url || url.origin !== window.location.origin) {
        return false;
    }

    return url.pathname !== window.location.pathname || url.search !== window.location.search;
}

function setLoading(active) {
    document.body.classList.toggle('nav-loading', active);
}

function saveCurrentHistoryState() {
    const currentState = window.history.state || {};
    const nextState = {
        ...currentState,
        medisysPartialNav: true,
        url: window.location.href,
        scrollY: window.scrollY,
    };
    window.history.replaceState(nextState, '', window.location.href);
}

function collectBetween(doc, startId, endId) {
    const start = doc.getElementById(startId);
    const end = doc.getElementById(endId);
    const nodes = [];

    if (!start || !end) {
        return nodes;
    }

    let current = start.nextSibling;
    while (current && current !== end) {
        if (current.nodeType === Node.ELEMENT_NODE) {
            nodes.push(current);
        }
        current = current.nextSibling;
    }

    return nodes;
}

function syncHeadStyles(nextDocument) {
    const currentStart = document.getElementById(PAGE_STYLES_START_ID);
    const currentEnd = document.getElementById(PAGE_STYLES_END_ID);
    if (!currentStart || !currentEnd) {
        return;
    }

    let current = currentStart.nextSibling;
    while (current && current !== currentEnd) {
        const nextNode = current.nextSibling;
        if (current.nodeType === Node.ELEMENT_NODE) {
            current.remove();
        }
        current = nextNode;
    }

    const incomingNodes = collectBetween(nextDocument, PAGE_STYLES_START_ID, PAGE_STYLES_END_ID);
    incomingNodes.forEach((node) => {
        currentEnd.parentNode.insertBefore(node.cloneNode(true), currentEnd);
    });
}

function cleanupPageRuntime() {
    while (runtimeState.cleanupHandlers.length) {
        const handler = runtimeState.cleanupHandlers.pop();
        try {
            handler();
        } catch {
            // Ignore page cleanup errors and continue with navigation.
        }
    }

    document.querySelectorAll('[data-medisys-page-script="true"]').forEach((node) => {
        node.remove();
    });
}

function transformPageScript(code) {
    return String(code || '')
        .replace(/document\.addEventListener\(\s*['\"]DOMContentLoaded['\"]\s*,\s*function\s*\(/g, 'window.__medisysOnPageReady(function(')
        .replace(/document\.addEventListener\(\s*['\"]DOMContentLoaded['\"]\s*,\s*\(\)\s*=>\s*\{/g, 'window.__medisysOnPageReady(() => {')
        .replace(/window\.addEventListener\(\s*['\"]load['\"]\s*,\s*function\s*\(/g, 'window.__medisysOnPageReady(function(')
        .replace(/window\.addEventListener\(\s*['\"]load['\"]\s*,\s*\(\)\s*=>\s*\{/g, 'window.__medisysOnPageReady(() => {');
}

function installScriptRuntimeTracking() {
    const timeouts = new Set();
    const intervals = new Set();
    const animationFrames = new Set();
    const listeners = [];

    const originalSetTimeout = window.setTimeout;
    const originalClearTimeout = window.clearTimeout;
    const originalSetInterval = window.setInterval;
    const originalClearInterval = window.clearInterval;
    const originalRequestAnimationFrame = window.requestAnimationFrame ? window.requestAnimationFrame.bind(window) : null;
    const originalCancelAnimationFrame = window.cancelAnimationFrame ? window.cancelAnimationFrame.bind(window) : null;
    const originalWindowAddEventListener = window.addEventListener.bind(window);
    const originalWindowRemoveEventListener = window.removeEventListener.bind(window);
    const originalDocumentAddEventListener = document.addEventListener.bind(document);
    const originalDocumentRemoveEventListener = document.removeEventListener.bind(document);
    const originalOnPageReady = window.__medisysOnPageReady;

    window.setTimeout = function (handler, timeout, ...args) {
        const id = originalSetTimeout(handler, timeout, ...args);
        timeouts.add(id);
        return id;
    };

    window.clearTimeout = function (id) {
        timeouts.delete(id);
        return originalClearTimeout(id);
    };

    window.setInterval = function (handler, timeout, ...args) {
        const id = originalSetInterval(handler, timeout, ...args);
        intervals.add(id);
        return id;
    };

    window.clearInterval = function (id) {
        intervals.delete(id);
        return originalClearInterval(id);
    };

    if (originalRequestAnimationFrame && originalCancelAnimationFrame) {
        window.requestAnimationFrame = function (callback) {
            const id = originalRequestAnimationFrame(callback);
            animationFrames.add(id);
            return id;
        };

        window.cancelAnimationFrame = function (id) {
            animationFrames.delete(id);
            return originalCancelAnimationFrame(id);
        };
    }

    window.addEventListener = function (type, listener, options) {
        listeners.push({ target: window, type, listener, options });
        return originalWindowAddEventListener(type, listener, options);
    };

    window.removeEventListener = function (type, listener, options) {
        return originalWindowRemoveEventListener(type, listener, options);
    };

    document.addEventListener = function (type, listener, options) {
        listeners.push({ target: document, type, listener, options });
        return originalDocumentAddEventListener(type, listener, options);
    };

    document.removeEventListener = function (type, listener, options) {
        return originalDocumentRemoveEventListener(type, listener, options);
    };

    window.__medisysOnPageReady = function (callback) {
        if (typeof callback === 'function') {
            callback();
        }
    };

    const restore = function () {
        window.setTimeout = originalSetTimeout;
        window.clearTimeout = originalClearTimeout;
        window.setInterval = originalSetInterval;
        window.clearInterval = originalClearInterval;

        if (originalRequestAnimationFrame && originalCancelAnimationFrame) {
            window.requestAnimationFrame = originalRequestAnimationFrame;
            window.cancelAnimationFrame = originalCancelAnimationFrame;
        }

        window.addEventListener = originalWindowAddEventListener;
        window.removeEventListener = originalWindowRemoveEventListener;
        document.addEventListener = originalDocumentAddEventListener;
        document.removeEventListener = originalDocumentRemoveEventListener;
        window.__medisysOnPageReady = originalOnPageReady;
    };

    runtimeState.cleanupHandlers.push(function () {
        timeouts.forEach((id) => originalClearTimeout(id));
        intervals.forEach((id) => originalClearInterval(id));
        if (originalCancelAnimationFrame) {
            animationFrames.forEach((id) => originalCancelAnimationFrame(id));
        }
        listeners.forEach(({ target, type, listener, options }) => {
            target.removeEventListener(type, listener, options);
        });
    });

    return restore;
}

async function executePageScripts(nextDocument) {
    const incomingScripts = collectBetween(nextDocument, PAGE_SCRIPTS_START_ID, PAGE_SCRIPTS_END_ID)
        .filter((node) => node.tagName === 'SCRIPT');

    for (const scriptNode of incomingScripts) {
        if (scriptNode.src) {
            const existing = Array.from(document.scripts).find((script) => script.src === scriptNode.src);
            if (existing) {
                continue;
            }

            await new Promise((resolve, reject) => {
                const externalScript = document.createElement('script');
                Array.from(scriptNode.attributes).forEach((attribute) => {
                    externalScript.setAttribute(attribute.name, attribute.value);
                });
                externalScript.dataset.medisysPageScript = 'true';
                externalScript.addEventListener('load', resolve, { once: true });
                externalScript.addEventListener('error', reject, { once: true });
                document.body.appendChild(externalScript);
            });
            continue;
        }

        const restoreRuntime = installScriptRuntimeTracking();
        try {
            const inlineScript = document.createElement('script');
            inlineScript.dataset.medisysPageScript = 'true';
            inlineScript.textContent = transformPageScript(scriptNode.textContent || '');
            document.body.appendChild(inlineScript);
        } finally {
            restoreRuntime();
        }
    }
}

function reinitializePageChrome(url) {
    if (window.Alpine && typeof window.Alpine.initTree === 'function') {
        const mainContent = document.getElementById('mainContent');
        if (mainContent) {
            window.Alpine.initTree(mainContent);
        }
    }

    if (typeof window.initLayoutChrome === 'function') {
        window.initLayoutChrome();
    }

    if (typeof window.refreshSidebarState === 'function') {
        window.refreshSidebarState(url.pathname);
    }

    document.dispatchEvent(new CustomEvent('medisys:page-loaded', {
        detail: { url: url.toString() },
    }));
}

async function navigateTo(urlValue, options = {}) {
    const url = typeof urlValue === 'string' ? normalizeUrl(urlValue) : urlValue;
    if (!url) {
        return false;
    }

    const navigationId = ++runtimeState.navigationId;
    if (runtimeState.controller) {
        runtimeState.controller.abort();
    }

    const controller = new AbortController();
    runtimeState.controller = controller;
    setLoading(true);

    try {
        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-Medisys-Partial': '1',
                'Accept': 'text/html, application/xhtml+xml',
            },
            signal: controller.signal,
        });

        if (!response.ok || response.redirected) {
            window.location.href = url.toString();
            return false;
        }

        const html = await response.text();
        if (navigationId !== runtimeState.navigationId) {
            return false;
        }

        const parser = new DOMParser();
        const nextDocument = parser.parseFromString(html, 'text/html');
        const nextMain = nextDocument.getElementById('mainContent');
        const currentMain = document.getElementById('mainContent');

        if (!nextMain || !currentMain) {
            window.location.href = url.toString();
            return false;
        }

        cleanupPageRuntime();
        syncHeadStyles(nextDocument);
        currentMain.replaceWith(nextMain.cloneNode(true));
        document.title = nextDocument.title || document.title;

        const csrfToken = nextDocument.querySelector('meta[name="csrf-token"]');
        const currentCsrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken && currentCsrfToken) {
            currentCsrfToken.setAttribute('content', csrfToken.getAttribute('content') || '');
        }

        await executePageScripts(nextDocument);
        reinitializePageChrome(url);

        if (options.push !== false) {
            window.history.pushState({ medisysPartialNav: true, url: url.toString(), scrollY: 0 }, '', url.toString());
            window.scrollTo({ top: 0, behavior: 'instant' in window ? 'instant' : 'auto' });
        } else if (typeof options.scrollY === 'number') {
            window.requestAnimationFrame(() => {
                window.scrollTo(0, options.scrollY);
            });
        }

        return true;
    } catch (error) {
        if (error && error.name === 'AbortError') {
            return false;
        }

        window.location.href = url.toString();
        return false;
    } finally {
        if (navigationId === runtimeState.navigationId) {
            setLoading(false);
        }
    }
}

function bindHistoryPersistence() {
    let frameId = null;

    const persistState = function () {
        const currentState = window.history.state || {};
        if (!currentState.medisysPartialNav) {
            return;
        }

        window.history.replaceState({
            ...currentState,
            url: window.location.href,
            scrollY: window.scrollY,
        }, '', window.location.href);
    };

    window.addEventListener('scroll', function () {
        if (frameId !== null) {
            return;
        }

        frameId = window.requestAnimationFrame(function () {
            frameId = null;
            persistState();
        });
    }, { passive: true });

    window.addEventListener('pagehide', persistState);
}

function initPartialNavigation() {
    saveCurrentHistoryState();
    bindHistoryPersistence();

    document.addEventListener('click', async function (event) {
        const link = event.target.closest(MODULE_LINK_SELECTOR);
        if (!isEligibleLink(link)) {
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

        event.preventDefault();
        saveCurrentHistoryState();
        await navigateTo(link.href, { push: true });
    }, true);

    window.addEventListener('popstate', function (event) {
        const state = event.state;
        if (!state || !state.medisysPartialNav) {
            window.location.href = window.location.href;
            return;
        }

        navigateTo(window.location.href, {
            push: false,
            scrollY: typeof state.scrollY === 'number' ? state.scrollY : 0,
        });
    });
}

export { initPartialNavigation };
