const CLIENT_TELEMETRY_ENDPOINT = '/telemetry/client-errors';
const HTTP_STATUS_REPORTS = new Set([403, 404, 419, 422, 500]);
const reportedEvents = new Map();
const MAX_DUPLICATES_PER_SIGNATURE = 3;

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function buildUrl(input) {
    try {
        if (typeof input === 'string') {
            return new URL(input, window.location.origin);
        }

        if (input instanceof URL) {
            return input;
        }

        if (input && typeof input.url === 'string') {
            return new URL(input.url, window.location.origin);
        }
    } catch {
        return null;
    }

    return null;
}

function normalizeMessage(value) {
    if (value instanceof Error) {
        return value.message || value.name || 'Unknown error';
    }

    if (typeof value === 'string') {
        return value;
    }

    try {
        return JSON.stringify(value);
    } catch {
        return String(value);
    }
}

function normalizeStack(value) {
    if (value instanceof Error) {
        return value.stack || null;
    }

    if (value && typeof value === 'object' && typeof value.stack === 'string') {
        return value.stack;
    }

    return null;
}

function shouldIgnoreUrl(url) {
    return !!url && url.pathname === CLIENT_TELEMETRY_ENDPOINT;
}

function shouldReport(signature) {
    const current = reportedEvents.get(signature) || 0;
    if (current >= MAX_DUPLICATES_PER_SIGNATURE) {
        return false;
    }

    reportedEvents.set(signature, current + 1);
    return true;
}

function reportClientIssue(payload) {
    const message = normalizeMessage(payload.message).slice(0, 2000);
    const signature = [
        payload.type || 'unknown',
        payload.status || '',
        payload.method || '',
        payload.source || '',
        message,
    ].join('|');

    if (!shouldReport(signature)) {
        return;
    }

    const body = {
        type: payload.type || 'unknown',
        level: payload.level || 'warning',
        message,
        url: payload.url || window.location.href,
        source: payload.source || null,
        stack: payload.stack ? String(payload.stack).slice(0, 12000) : null,
        status: payload.status || null,
        method: payload.method || null,
        context: payload.context || null,
    };

    fetch(CLIENT_TELEMETRY_ENDPOINT, {
        method: 'POST',
        credentials: 'same-origin',
        keepalive: true,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(body),
    }).catch(() => {
        // Do not recurse into telemetry failures.
    });
}

function patchWindowErrors() {
    window.addEventListener('error', (event) => {
        reportClientIssue({
            type: 'js_error',
            level: 'error',
            message: event.message || 'Unhandled window error',
            source: event.filename ? `${event.filename}:${event.lineno || 0}:${event.colno || 0}` : null,
            stack: normalizeStack(event.error),
        });
    });

    window.addEventListener('unhandledrejection', (event) => {
        reportClientIssue({
            type: 'unhandled_rejection',
            level: 'error',
            message: normalizeMessage(event.reason),
            stack: normalizeStack(event.reason),
        });
    });
}

function patchConsoleErrors() {
    if (!window.console || typeof window.console.error !== 'function' || window.__medisysConsoleTelemetryPatched) {
        return;
    }

    const originalConsoleError = window.console.error.bind(window.console);
    window.console.error = (...args) => {
        const message = args.map((arg) => normalizeMessage(arg)).join(' | ');

        if (!message.includes('client.telemetry')) {
            reportClientIssue({
                type: 'console_error',
                level: 'warning',
                message,
                context: {
                    arg_count: args.length,
                },
            });
        }

        originalConsoleError(...args);
    };

    window.__medisysConsoleTelemetryPatched = true;
}

function patchFetch() {
    if (typeof window.fetch !== 'function' || window.__medisysFetchTelemetryPatched) {
        return;
    }

    const originalFetch = window.fetch.bind(window);

    window.fetch = async (...args) => {
        const requestUrl = buildUrl(args[0]);
        const method = args[1]?.method || (args[0] && typeof args[0] === 'object' && 'method' in args[0] ? args[0].method : 'GET');

        try {
            const response = await originalFetch(...args);

            if (
                requestUrl
                && requestUrl.origin === window.location.origin
                && !shouldIgnoreUrl(requestUrl)
                && HTTP_STATUS_REPORTS.has(response.status)
            ) {
                reportClientIssue({
                    type: 'http_error',
                    level: response.status >= 500 ? 'error' : 'warning',
                    message: `HTTP ${response.status} on ${requestUrl.pathname}`,
                    url: requestUrl.toString(),
                    status: response.status,
                    method: String(method).toUpperCase(),
                });
            }

            return response;
        } catch (error) {
            if (requestUrl && requestUrl.origin === window.location.origin && !shouldIgnoreUrl(requestUrl)) {
                reportClientIssue({
                    type: 'fetch_exception',
                    level: 'error',
                    message: normalizeMessage(error),
                    url: requestUrl.toString(),
                    method: String(method).toUpperCase(),
                    stack: normalizeStack(error),
                });
            }

            throw error;
        }
    };

    window.__medisysFetchTelemetryPatched = true;
}

function patchAxios() {
    if (!window.axios || window.__medisysAxiosTelemetryPatched) {
        return;
    }

    window.axios.interceptors.response.use(
        (response) => response,
        (error) => {
            const response = error?.response;
            const config = error?.config || {};
            const requestUrl = buildUrl(config.url);

            if (response && requestUrl && requestUrl.origin === window.location.origin && !shouldIgnoreUrl(requestUrl) && HTTP_STATUS_REPORTS.has(response.status)) {
                reportClientIssue({
                    type: 'http_error',
                    level: response.status >= 500 ? 'error' : 'warning',
                    message: `HTTP ${response.status} on ${requestUrl.pathname}`,
                    url: requestUrl.toString(),
                    status: response.status,
                    method: String(config.method || 'GET').toUpperCase(),
                });
            } else if (requestUrl && requestUrl.origin === window.location.origin && !shouldIgnoreUrl(requestUrl)) {
                reportClientIssue({
                    type: 'axios_exception',
                    level: 'error',
                    message: normalizeMessage(error),
                    url: requestUrl.toString(),
                    method: String(config.method || 'GET').toUpperCase(),
                    stack: normalizeStack(error),
                });
            }

            return Promise.reject(error);
        }
    );

    window.__medisysAxiosTelemetryPatched = true;
}

export function initClientTelemetry() {
    if (window.__medisysClientTelemetryInitialized) {
        return;
    }

    patchWindowErrors();
    patchConsoleErrors();
    patchFetch();
    patchAxios();

    window.__medisysClientTelemetryInitialized = true;
}
