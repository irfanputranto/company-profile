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

Alpine.start();
