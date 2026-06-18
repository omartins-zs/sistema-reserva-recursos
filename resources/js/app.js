import '@fortawesome/fontawesome-free/css/all.min.css';
import flatpickr from 'flatpickr';
import { Portuguese } from 'flatpickr/dist/l10n/pt.js';
import Swal from 'sweetalert2';

window.Swal = Swal;

const syncPickerValue = (element, value) => {
    element.value = value ?? '';
    element.dispatchEvent(new Event('input', { bubbles: true }));
    element.dispatchEvent(new Event('change', { bubbles: true }));
};

const applyAltInputStyling = (instance, placeholder) => {
    if (!instance.altInput) {
        return;
    }

    instance.altInput.classList.add('booking-input', 'booking-picker-alt');
    instance.altInput.placeholder = placeholder;
    instance.altInput.autocomplete = 'off';
};

window.initLocalizedPicker = (element, options = {}) => {
    if (!element) {
        return null;
    }

    const mode = options.mode ?? element.dataset.picker ?? 'date';
    const placeholder = options.placeholder
        ?? element.dataset.placeholder
        ?? element.getAttribute('placeholder')
        ?? '';
    const currentValue = element.value || element.dataset.defaultValue || '';

    if (element._flatpickr) {
        if (currentValue === '') {
            element._flatpickr.clear(false);
        } else if (element._flatpickr.input.value !== currentValue) {
            element._flatpickr.setDate(currentValue, false, element._flatpickr.config.dateFormat);
        }

        applyAltInputStyling(element._flatpickr, placeholder);

        return element._flatpickr;
    }

    const config = {
        locale: Portuguese,
        allowInput: false,
        disableMobile: true,
        altInput: true,
        monthSelectorType: 'static',
        prevArrow: '<span aria-hidden="true">&#8249;</span>',
        nextArrow: '<span aria-hidden="true">&#8250;</span>',
        ...(mode === 'time'
            ? {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                altFormat: 'H:i',
                time_24hr: true,
            }
            : {
                dateFormat: 'Y-m-d',
                altFormat: 'd/m/Y',
            }),
        ...options,
    };

    const originalOnReady = config.onReady ? (Array.isArray(config.onReady) ? config.onReady : [config.onReady]) : [];
    const originalOnChange = config.onChange ? (Array.isArray(config.onChange) ? config.onChange : [config.onChange]) : [];
    const originalOnClose = config.onClose ? (Array.isArray(config.onClose) ? config.onClose : [config.onClose]) : [];

    config.onReady = [
        (selectedDates, dateStr, instance) => {
            applyAltInputStyling(instance, placeholder);

            if (currentValue) {
                instance.setDate(currentValue, false, config.dateFormat);
            }
        },
        ...originalOnReady,
    ];

    config.onChange = [
        (selectedDates, dateStr) => {
            syncPickerValue(element, dateStr);
        },
        ...originalOnChange,
    ];

    config.onClose = [
        (selectedDates, dateStr) => {
            syncPickerValue(element, dateStr);
        },
        ...originalOnClose,
    ];

    return flatpickr(element, config);
};

const initLocalizedPickers = (root = document) => {
    root.querySelectorAll('[data-picker]').forEach((element) => {
        window.initLocalizedPicker(element);
    });
};

document.addEventListener('livewire:init', () => {
    initLocalizedPickers();

    Livewire.on('notify', ({ type = 'info', title = '', text = '' }) => {
        Swal.fire({
            icon: type,
            title,
            text,
            confirmButtonColor: '#0f4c81',
            background: '#ffffff',
            color: '#0f172a',
        });
    });

    Livewire.on('scroll-top', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    });

    Livewire.hook('morphed', ({ el }) => {
        initLocalizedPickers(el);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    initLocalizedPickers();
});

document.addEventListener('livewire:navigated', () => {
    initLocalizedPickers();
});
