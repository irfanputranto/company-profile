import './bootstrap';
import './page-loading-bar';
import '../vendor/flatpickr/flatpickr.min.js';
import Alpine from 'alpinejs';
import './flyonui-bootstrap';
import 'flyonui/flyonui';

window.Alpine = Alpine;

const initializeThemeSwitcher = () => {
    const root = document.documentElement;
    const defaultTheme = root.dataset.themeDefault || root.dataset.theme || 'valorant';
    const storageKey = root.dataset.themeStorageKey || 'laravel-skeleton-theme';
    const supportedThemes = new Set(
        String(root.dataset.themeOptions || defaultTheme)
            .split(',')
            .map(theme => theme.trim())
            .filter(Boolean),
    );

    let selectedTheme = defaultTheme;

    try {
        const storedPreference = JSON.parse(window.localStorage.getItem(storageKey));

        if (
            storedPreference?.defaultTheme === defaultTheme
            && supportedThemes.has(storedPreference.theme)
        ) {
            selectedTheme = storedPreference.theme;
        }
    } catch {
        selectedTheme = defaultTheme;
    }

    const applyTheme = theme => {
        const resolvedTheme = supportedThemes.has(theme) ? theme : defaultTheme;
        root.dataset.theme = resolvedTheme;

        document.querySelectorAll('[data-theme-value]').forEach(button => {
            const isActive = button.dataset.themeValue === resolvedTheme;
            button.classList.toggle('dropdown-active', isActive);
            button.setAttribute('aria-pressed', String(isActive));
        });

        const activeButton = document.querySelector(`[data-theme-value="${resolvedTheme}"]`);
        const currentLabel = activeButton?.dataset.themeLabel || resolvedTheme;

        document.querySelectorAll('[data-theme-current-label]').forEach(element => {
            element.textContent = currentLabel;
        });

        try {
            window.localStorage.setItem(storageKey, JSON.stringify({
                theme: resolvedTheme,
                defaultTheme,
            }));
        } catch {
            // The configured default remains active when browser storage is unavailable.
        }
    };

    applyTheme(selectedTheme);

    document.querySelectorAll('[data-theme-value]').forEach(button => {
        button.addEventListener('click', () => applyTheme(button.dataset.themeValue));
    });
};

initializeThemeSwitcher();

const initFlatpickr = (element, options = {}) => {
    if (!element || element.dataset?.flatpickrInitialized === '1') {
        return;
    }

    if (!window.flatpickr) {
        const attempts = Number(element.dataset.flatpickrRetry || 0);

        if (attempts < 40) {
            element.dataset.flatpickrRetry = String(attempts + 1);
            setTimeout(() => initFlatpickr(element, options), 120);
        }

        return;
    }

    window.flatpickr(element, {
        dateFormat: 'Y-m-d',
        monthSelectorType: 'static',
        disableMobile: true,
        ...options,
    });
    element.dataset.flatpickrInitialized = '1';
};

Alpine.data('dateInputPicker', (options = {}) => ({
    init() {
        this.$nextTick(() => initFlatpickr(this.$refs.input, options));
    },
}));

Alpine.data('rupiahInput', (maximumValue = null, initialValue = '') => ({
    rawValue: '',
    displayValue: '',
    maximumValue: Number(maximumValue) || null,
    init() {
        this.setValue(initialValue);
    },
    formatInput(event) {
        this.setValue(event.target.value);
        event.target.value = this.displayValue;
    },
    setValue(value) {
        const digits = String(value ?? '').replace(/\D/g, '');

        if (digits === '') {
            this.rawValue = '';
            this.displayValue = '';

            return;
        }

        const numericValue = Number(digits);
        const limitedValue = this.maximumValue !== null
            ? Math.min(numericValue, this.maximumValue)
            : numericValue;

        this.rawValue = String(limitedValue);
        this.displayValue = new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 0,
        }).format(limitedValue);
    },
}));

Alpine.data('confirmDelete', () => ({
    open: false,
    show() {
        this.open = true;
    },
    close() {
        this.open = false;
    },
}));

Alpine.data('dataTableFilters', () => ({
    applyFilters() {
        this.$refs.filterForm.requestSubmit();
    },
}));

Alpine.data('sidebarLayout', () => ({
    sidebarCollapsed: false,
    pageGuideOpen: false,
    storageKey: 'app-skeleton-sidebar-collapsed',
    init() {
        try {
            this.sidebarCollapsed = window.localStorage.getItem(this.storageKey) === 'true';
        } catch {
            this.sidebarCollapsed = false;
        }
    },
    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;

        try {
            window.localStorage.setItem(this.storageKey, String(this.sidebarCollapsed));
        } catch {
            // The sidebar still works when browser storage is unavailable.
        }
    },
}));

Alpine.data('imageUpload', (currentUrl = null) => ({
    preview: currentUrl,
    temporaryPreview: null,
    error: null,
    dragging: false,
    choose(file) {
        this.error = null;

        if (!file) {
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            this.error = 'Gunakan gambar JPG, PNG, atau WebP.';
            this.$refs.file.value = '';

            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            this.error = 'Ukuran gambar maksimal 2 MB.';
            this.$refs.file.value = '';

            return;
        }

        if (this.temporaryPreview) {
            URL.revokeObjectURL(this.temporaryPreview);
        }

        this.temporaryPreview = URL.createObjectURL(file);
        this.preview = this.temporaryPreview;
    },
    drop(event) {
        this.dragging = false;
        const file = event.dataTransfer.files[0];

        if (!file) {
            return;
        }

        const transfer = new DataTransfer();
        transfer.items.add(file);
        this.$refs.file.files = transfer.files;
        this.choose(file);
    },
}));

Alpine.data('modalState', (initiallyOpen = false) => ({
    open: Boolean(initiallyOpen),
    show() {
        this.open = true;
    },
    close() {
        this.open = false;
    },
}));

Alpine.data('bigspringCarousel', (slideCount = 1, interval = 5000) => ({
    active: 0,
    timer: null,
    init() {
        if (slideCount <= 1 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        this.timer = window.setInterval(() => {
            this.active = (this.active + 1) % slideCount;
        }, interval);
    },
    goTo(index) {
        this.active = index;
    },
    destroy() {
        if (this.timer) {
            window.clearInterval(this.timer);
        }
    },
}));

const trackPublicAnalytics = (scopeType, eventName) => {
    const endpoint = document.querySelector('meta[name="analytics-endpoint"]')?.content;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!endpoint || !csrfToken || !scopeType || !eventName) {
        return;
    }

    const payload = new FormData();
    payload.append('_token', csrfToken);
    payload.append('scope_type', scopeType);
    payload.append('event', eventName);

    if (navigator.sendBeacon?.(endpoint, payload)) {
        return;
    }

    fetch(endpoint, {
        method: 'POST',
        body: payload,
        credentials: 'same-origin',
        keepalive: true,
        headers: {
            Accept: 'application/json',
        },
    }).catch(() => {
        // Analytics must never interrupt public navigation.
    });
};

document.addEventListener('click', event => {
    const analyticsTarget = event.target.closest('[data-analytics-scope][data-analytics-event]');

    if (!analyticsTarget) {
        return;
    }

    trackPublicAnalytics(
        analyticsTarget.dataset.analyticsScope,
        analyticsTarget.dataset.analyticsEvent,
    );
});

Alpine.data('publicNavigation', (initialPage = 'home') => ({
    active: initialPage,
    open: false,
    observer: null,
    trackedSections: new Set(),
    init() {
        if (initialPage !== 'home' || !('IntersectionObserver' in window)) {
            return;
        }

        this.$nextTick(() => {
            const sections = document.querySelectorAll('[data-nav-section]');

            this.observer = new IntersectionObserver(entries => {
                const visibleSection = entries
                    .filter(entry => entry.isIntersecting)
                    .sort((first, second) => second.intersectionRatio - first.intersectionRatio)[0];

                if (visibleSection) {
                    this.active = visibleSection.target.dataset.navSection;
                    this.trackSection(this.active);
                }
            }, {
                rootMargin: '-20% 0px -60% 0px',
                threshold: [0, 0.2, 0.5],
            });

            sections.forEach(section => this.observer.observe(section));
        });
    },
    activate(page) {
        this.active = page;
    },
    trackSection(section) {
        if (this.trackedSections.has(section)) {
            return;
        }

        this.trackedSections.add(section);
        trackPublicAnalytics('section', section);
    },
    destroy() {
        this.observer?.disconnect();
    },
}));

const initializeBigspringReveals = () => {
    const elements = document.querySelectorAll('.bigspring-home [data-reveal]');

    if (elements.length === 0) {
        return;
    }

    document.body.classList.add('reveal-ready');

    if (
        !('IntersectionObserver' in window)
        || window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        elements.forEach(element => element.classList.add('is-visible'));

        return;
    }

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px -8% 0px',
        threshold: 0.12,
    });

    elements.forEach(element => observer.observe(element));
};

initializeBigspringReveals();

Alpine.start();
