export const VuexyNotifyService = {
    flash({ message, type = 'info', target = 'body', delay = 3000 }) {
        const event = new CustomEvent('vuexy:notify', {
            detail: { type, message, target, delay }
        });

        window.dispatchEvent(event);
    },

    success(message, target = 'body', delay = 3000) {
        this.flash({ type: 'success', message, target, delay });
    },

    error(message, target = 'body', delay = 3000) {
        this.flash({ type: 'error', message, target, delay });
    },

    info(message, target = 'body', delay = 3000) {
        this.flash({ type: 'info', message, target, delay });
    },

    warning(message, target = 'body', delay = 3000) {
        this.flash({ type: 'warning', message, target, delay });
    },

    fromLocalStorage() {
        const data = localStorage.getItem('vuexy_notification');

        if (data) {
            try {
                this.flash(JSON.parse(data));

                localStorage.removeItem('vuexy_notification');

            } catch (e) {
                console.error('❌ Error parseando notificación de storage', e);
            }
        }
    },

    fromSession() {
        if (window.vuexySessionNotification) {
            this.flash(window.vuexySessionNotification);
            window.vuexySessionNotification = null;
        }
    },

    dispatch(eventName, payload = {}) {
        const event = new CustomEvent(eventName, { detail: payload });

        window.dispatchEvent(event);
    }
};

document.addEventListener('DOMContentLoaded', function () {
    window.addEventListener('vuexy:notify', function (event) {
        // Si viene como array, tomar el primer objeto
        const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
        const { type, message, target, delay } = detail || {};

        const defaultTarget = 'body .notification-container';

        let realTarget = target == 'body' ? defaultTarget : target;

        let targetElement = document.querySelector(realTarget);

        if (!targetElement) {
            console.warn('⚠️ Target para notificación no encontrado:', realTarget, ', usando ', defaultTarget);

            targetElement = document.querySelector(defaultTarget);
        }

        if(!targetElement) {
            console.error('🛑 Target por defecto no encontrado:', defaultTarget);
            return;
        }

        const alert = document.createElement('div');

        alert.className = `alert alert-${type} alert-dismissible fade show mt-2`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `.trim();

        targetElement.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, delay || 6000);
    });
});

