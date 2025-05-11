// drivers/vuexy-driver.js

export const VuexyDriver = {
    notify({ message, type = 'info', target = 'body', delay = 3000 }) {
        const event = new CustomEvent('vuexy:notify', {
            detail: { type, message, target, delay }
        });

        window.dispatchEvent(event);
    },

    success(message, target = 'body', delay = 3000) {
        this.notify({ type: 'success', message, target, delay });
    },

    error(message, target = 'body', delay = 3000) {
        this.notify({ type: 'error', message, target, delay });
    },

    info(message, target = 'body', delay = 3000) {
        this.notify({ type: 'info', message, target, delay });
    },

    warning(message, target = 'body', delay = 3000) {
        this.notify({ type: 'warning', message, target, delay });
    },

    fromStorage() {
        const storageData = localStorage.getItem('vuexy_notification');

        if (storageData) {
            try {
                this.notify(JSON.parse(storageData));
                localStorage.removeItem('vuexy_notification');
            } catch (e) {
                console.error('[VuexyDriver] Error parseando notificación', e);
            }
        }
    },

    fromSession() {
        if (window.vuexySessionNotification) {
            this.notify(window.vuexySessionNotification);
            window.vuexySessionNotification = null;
        }
    }
};
