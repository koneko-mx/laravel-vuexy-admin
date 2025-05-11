// resources/js/vuexy/notifications/vuexy-toastr.js

import toastr from 'toastr';

export const ToastrDriver = {
    defaultOptions: {
        closeButton: true,
        progressBar: true,
        tapToDismiss: true,
        newestOnTop: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        extendedTimeOut: 1000,
        showDuration: 300,
        hideDuration: 300,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    },

    /**
     * Notifica usando toastr con opciones personalizadas
     */
    notify({
        type = 'success',
        message = '',
        title = '',
        delay = 5000,
        position = 'toast-top-right',
        closeButton = true,
        progressBar = true,
        iconClass = null,
        extraOptions = {}
    } = {}) {
        const timeOut = delay;
        const extendedTimeOut = delay + 1000;

        toastr.options = {
            ...this.defaultOptions,
            closeButton,
            progressBar,
            timeOut,
            extendedTimeOut,
            positionClass: position,
            ...extraOptions
        };

        if (iconClass) {
            toastr.options.iconClass = iconClass;
        }

        if (toastr[type]) {
            toastr[type](message, title);
        } else {
            toastr.info(message || 'Sin mensaje');
        }

        if (import.meta.env.DEV) {
            console.debug(`[TOAST ${type.toUpperCase()}] ${title}: ${message}`);
        }
    },

    success(message, title = 'Éxito', delay = 4000, options = {}) {
        this.notify({ type: 'success', message, title, delay, ...options });
    },

    error(message, title = 'Error', delay = 6000, options = {}) {
        this.notify({ type: 'error', message, title, delay, ...options });
    },

    warning(message, title = 'Advertencia', delay = 5000, options = {}) {
        this.notify({ type: 'warning', message, title, delay, ...options });
    },

    info(message, title = 'Información', delay = 5000, options = {}) {
        this.notify({ type: 'info', message, title, delay, ...options });
    },

    /**
     * Inicializa listeners para eventos globales como vuexy:notify
     */
    listenToGlobalEvents() {
        window.addEventListener('vuexy:notify', (event) => {
            const detail = event.detail || {};
            this.notify(detail);
        });
    }
};
