const loadingBarId = 'app-loading-bar';
const progressId = 'app-loading-bar-progress';
const requestDelay = 180;
const maximumVisibleDuration = 30000;

let activeRequests = 0;
let progress = 0;
let delayTimer;
let finishTimer;
let safetyTimer;
let trickleTimer;

const ensureLoadingBar = () => {
    let loadingBar = document.getElementById(loadingBarId);

    if (loadingBar || !document.body) {
        return loadingBar;
    }

    loadingBar = document.createElement('div');
    loadingBar.id = loadingBarId;
    loadingBar.setAttribute('role', 'progressbar');
    loadingBar.setAttribute('aria-label', 'Memproses halaman');
    loadingBar.setAttribute('aria-valuemin', '0');
    loadingBar.setAttribute('aria-valuemax', '100');
    loadingBar.setAttribute('aria-valuenow', '0');
    loadingBar.setAttribute('aria-hidden', 'true');

    const progressElement = document.createElement('div');
    progressElement.id = progressId;
    loadingBar.append(progressElement);
    document.body.prepend(loadingBar);

    return loadingBar;
};

const renderProgress = (nextProgress) => {
    const loadingBar = ensureLoadingBar();

    if (!loadingBar) {
        return;
    }

    progress = Math.min(1, Math.max(0, nextProgress));
    loadingBar.style.setProperty('--loading-progress', String(progress));
    loadingBar.setAttribute('aria-valuenow', String(Math.round(progress * 100)));
};

const stopTimers = () => {
    window.clearTimeout(delayTimer);
    window.clearTimeout(finishTimer);
    window.clearTimeout(safetyTimer);
    window.clearInterval(trickleTimer);
};

const complete = () => {
    stopTimers();

    const loadingBar = ensureLoadingBar();

    if (!loadingBar || !loadingBar.classList.contains('is-visible')) {
        renderProgress(0);
        return;
    }

    renderProgress(1);
    finishTimer = window.setTimeout(() => {
        loadingBar.classList.remove('is-visible');
        loadingBar.setAttribute('aria-hidden', 'true');
        renderProgress(0);
    }, 220);
};

const activate = () => {
    const loadingBar = ensureLoadingBar();

    if (!loadingBar) {
        return;
    }

    window.clearTimeout(finishTimer);
    window.clearTimeout(safetyTimer);
    window.clearInterval(trickleTimer);

    if (progress >= 0.98) {
        renderProgress(0);
    }

    loadingBar.classList.add('is-visible');
    loadingBar.setAttribute('aria-hidden', 'false');
    renderProgress(Math.max(progress, 0.08));

    requestAnimationFrame(() => {
        if (loadingBar.classList.contains('is-visible')) {
            renderProgress(Math.max(progress, 0.28));
        }
    });

    trickleTimer = window.setInterval(() => {
        const remainingProgress = 0.92 - progress;

        if (remainingProgress > 0) {
            renderProgress(progress + Math.max(0.01, remainingProgress * 0.08));
        }
    }, 420);

    safetyTimer = window.setTimeout(() => {
        activeRequests = 0;
        complete();
    }, maximumVisibleDuration);
};

const beginRequest = () => {
    activeRequests += 1;

    if (activeRequests === 1) {
        window.clearTimeout(delayTimer);
        delayTimer = window.setTimeout(activate, requestDelay);
    }

    let isFinished = false;

    return () => {
        if (isFinished) {
            return;
        }

        isFinished = true;
        activeRequests = Math.max(0, activeRequests - 1);

        if (activeRequests === 0) {
            complete();
        }
    };
};

const beginNavigation = () => {
    activeRequests = Math.max(1, activeRequests);
    window.clearTimeout(delayTimer);
    activate();
};

const reset = () => {
    activeRequests = 0;
    complete();
};

const shouldShowForLink = (link, event) => {
    const href = link.getAttribute('href')?.trim();
    const target = link.getAttribute('target')?.toLowerCase();

    if (!href
        || href.startsWith('#')
        || href.startsWith('javascript:')
        || href.startsWith('mailto:')
        || href.startsWith('tel:')
        || link.hasAttribute('download')
        || link.hasAttribute('data-no-loading-bar')
        || (target && target !== '_self')
        || event.button !== 0
        || event.metaKey
        || event.ctrlKey
        || event.shiftKey
        || event.altKey) {
        return false;
    }

    return true;
};

const monitorNavigation = () => {
    document.addEventListener('click', (event) => {
        const link = event.target.closest?.('a[href]');

        if (!link || !shouldShowForLink(link, event)) {
            return;
        }

        window.setTimeout(() => {
            if (!event.defaultPrevented) {
                beginNavigation();
            }
        });
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)
            || form.method.toLowerCase() === 'dialog'
            || form.target.toLowerCase() === '_blank'
            || form.hasAttribute('data-no-loading-bar')) {
            return;
        }

        window.setTimeout(() => {
            if (!event.defaultPrevented) {
                beginNavigation();
            }
        });
    });

    window.addEventListener('beforeunload', beginNavigation);
    window.addEventListener('pageshow', reset);
    document.addEventListener('livewire:navigating', beginNavigation);
    document.addEventListener('livewire:navigated', reset);
};

const monitorFetch = () => {
    if (!window.fetch || window.fetch.__loadingBarWrapped) {
        return;
    }

    const nativeFetch = window.fetch.bind(window);
    const monitoredFetch = (...argumentsList) => {
        const finishRequest = beginRequest();

        return nativeFetch(...argumentsList).finally(finishRequest);
    };

    monitoredFetch.__loadingBarWrapped = true;
    window.fetch = monitoredFetch;
};

const monitorAxios = () => {
    if (!window.axios || window.axios.__loadingBarWrapped) {
        return;
    }

    window.axios.__loadingBarWrapped = true;
    window.axios.interceptors.request.use((config) => {
        config.__finishLoadingBar = beginRequest();

        return config;
    }, (error) => Promise.reject(error));

    window.axios.interceptors.response.use((response) => {
        response.config.__finishLoadingBar?.();

        return response;
    }, (error) => {
        error.config?.__finishLoadingBar?.();

        return Promise.reject(error);
    });
};

const initializeLoadingBar = () => {
    ensureLoadingBar();
    monitorNavigation();
    monitorFetch();
    monitorAxios();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLoadingBar, { once: true });
} else {
    initializeLoadingBar();
}

window.AppLoadingBar = {
    start: beginNavigation,
    finish: reset,
};
