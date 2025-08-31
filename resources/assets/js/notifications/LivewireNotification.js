export default class LivewireNotification {
    constructor(config = {}) {
        const defaultConfig = {
            delay: 9000,        // Tiempo predeterminado para las notificaciones
            prepend: true,      // ← insertar arriba
            max: 6,             // ← máximo visible en el contenedor
            pauseOnHover: true, // ← pausa el autocierre al pasar el mouse
            onNotificationShown: null,    // Callback al mostrar una notificación
            onNotificationRemoved: null,  // Callback al eliminar una notificación
            onNotificationClosed: null    // Callback al cerrar una notificación mediante botón
        };

        this.config = { ...defaultConfig, ...config };
        this.initLivewireNotification();
        this._timers = new WeakMap();
    }

    /**
     * Inicializa la escucha de notificaciones desde Livewire.
     */
    initLivewireNotification() {
        // Mostrar notificación almacenada después de la recarga
        const storedNotification = localStorage.getItem('pendingNotification');

        if (storedNotification) {
            const event = JSON.parse(storedNotification);
            this.showStoredNotification(event);
            localStorage.removeItem('pendingNotification'); // Limpiar después de mostrar
        }

        // Escuchar nuevas notificaciones desde Livewire
        if (!window.__LW_NOTIFICATION_ON__) {
            window.__LW_NOTIFICATION_ON__ = true;

            Livewire.on('notification', event => {
                if (event.deferReload) {
                    // Guardar la notificación en localStorage para mostrar después de la recarga
                    localStorage.setItem('pendingNotification', JSON.stringify(event));

                    window.location.reload();
                } else {
                    // Mostrar la notificación inmediatamente
                    this.showNotification(event);
                }
            });
        }

        // Escuchar evento personalizado para almacenar la notificación en localStorage
        document.addEventListener('store-notification', (event) => {
            const notification = {
                type: event.detail.type || 'info',
                message: event.detail.message || 'Notificación',
                delay: event.detail.delay || 5000,
                target: event.detail.target || 'body'
            };
            localStorage.setItem('pendingNotification', JSON.stringify(notification));
        });
    }

    /**
     * Método para emitir notificaciones desde JavaScript.
     * @param {Object} options - Opciones de la notificación.
     * @param {Function} callback - Callback opcional que se ejecutará después de mostrar la notificación.
     * @param {number} customTimeout - Timeout personalizado (opcional).
     */
    emitNotification(options, callback, customTimeout) {
        const event = {
            target: options.target || 'body',
            message: options.message || 'Notificación',
            type: options.type || 'info',
            deferReload: options.deferReload || false,
            delay: customTimeout || options.delay || this.config.delay // Usar el timeout personalizado o el predeterminado
        };

        // Mostrar la notificación
        this.showNotification(event);

        // Ejecutar callback si está definido
        if (typeof callback === 'function') {
            callback(event);
        }
    }

    /**
     * Muestra una notificación almacenada.
     * @param {Object} event - Datos del evento de notificación.
     */
    showStoredNotification(event) {
        this.showNotification(event);
    }

    /**
     * Muestra una notificación.
     * @param {Object} event - Datos del evento de notificación.
     */
    showNotification(event) {
        setTimeout(() => {
            const targetElement = document.querySelector(event.target);

            if (!targetElement) {
                console.error(`Target ${event.target} no encontrado. Mostrando en el contenedor global.`);

                this.showInGlobalContainer(event);
                return;
            }

            // Si el target YA es el contenedor, úsalo tal cual.
            const notificationContainer = targetElement.matches('.notification-container')
              ? targetElement
              : (targetElement.querySelector('.notification-container') ||
                 (() => {
                   const c = document.createElement('div');
                   c.className = 'notification-container';
                   targetElement.appendChild(c);
                   return c;
                 })());

            const notificationElement = this.renderNotification(notificationContainer, event);

            // Callback opcional al mostrar la notificación
            if (typeof this.config.onNotificationShown === 'function') {
                this.config.onNotificationShown(notificationElement, event);
            }

            // Configurar el timeout para eliminar la notificación
            this.setdelay(notificationElement, event);

            // Configurar el evento para el botón de cierre
            this.setupCloseButton(notificationElement, event);
        }, 5);
    }

    /**
     * Renderiza una notificación en el contenedor global (body).
     * @param {Object} event - Datos del evento de notificación.
     */
    showInGlobalContainer(event) {
        const globalContainer = document.body;
        if (!globalContainer.querySelector('.notification-container')) {
            const container = document.createElement('div');

            container.className = 'notification-container';
            globalContainer.appendChild(container);
        }

        const notificationContainer = globalContainer.querySelector('.notification-container');
        const notificationElement = this.renderNotification(notificationContainer, event);

        if (typeof this.config.onNotificationShown === 'function') {
            this.config.onNotificationShown(notificationElement, event);
        }

        this.setdelay(notificationElement, event);
        this.setupCloseButton(notificationElement, event);
    }

    /**
     * Renderiza una notificación en el contenedor.
     * @param {HTMLElement} container - Contenedor de notificaciones.
     * @param {Object} event - Evento de notificación con tipo y mensaje.
     * @returns {HTMLElement} - Elemento de la notificación recién creada.
     */
    renderNotification(container, event) {
      const el = document.createElement('div');

      el.className = `alert alert-${event.type} alert-dismissible fade show`;
      el.role = 'status';
      el.setAttribute('aria-live', 'polite');
      el.innerHTML = `${event.message} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;

      // Nuevo primero (arriba) o al final según config
      if (this.config.prepend) {
        container.insertBefore(el, container.firstChild);
      } else {
        container.appendChild(el);
      }

      // Límite de notificaciones visibles
      if (this.config.max) {
        const alerts = container.querySelectorAll('.alert');
        if (alerts.length > this.config.max) {
          const victim = this.config.prepend ? container.lastElementChild : container.firstElementChild;
          victim?.remove();
        }
      }

      // Pausa/resume del autocierre al pasar el mouse
      if (this.config.pauseOnHover) {
        el.addEventListener('mouseenter', () => this._clearTimer(el));
        el.addEventListener('mouseleave', () => this.setdelay(el, event));
      }

      return el;
    }


    _clearTimer(el) {
        const t = this._timers.get(el);
        if (t) {
          clearTimeout(t);
          this._timers.delete(el);
        }
    }


    /**
     * Configura un timeout para limpiar una notificación específica.
     * @param {HTMLElement} notificationElement - Elemento de la notificación.
     * @param {Object} event - Evento asociado a la notificación.
     */
    setdelay(notificationElement, event) {
        const timeout = event.delay ?? this.config.delay;
        // delay === 0 o sticky => no autocierre
        if (event.sticky || timeout === 0) return;

        this._clearTimer(notificationElement);
        const id = setTimeout(() => {
          if (notificationElement && notificationElement.parentElement) {
            notificationElement.remove();
            if (typeof this.config.onNotificationRemoved === 'function') {
              this.config.onNotificationRemoved(notificationElement, event);
            }
          }
          this._timers.delete(notificationElement);
        }, timeout);
        this._timers.set(notificationElement, id);
    }

    /**
     * Configura el cierre manual de una notificación mediante el botón "Cerrar".
     * @param {HTMLElement} notificationElement - Elemento de la notificación.
     * @param {Object} event - Evento asociado a la notificación.
     */
    setupCloseButton(notificationElement, event) {
        const closeButton = notificationElement.querySelector('.btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                this._clearTimer(notificationElement);

                notificationElement.remove();

                // Callback opcional al cerrar la notificación manualmente
                if (typeof this.config.onNotificationClosed === 'function') {
                    this.config.onNotificationClosed(notificationElement, event);
                }
            });
        }
    }
}

if(typeof window.livewireNotification === 'undefined') {
    window.livewireNotification = new LivewireNotification();

    const storedNotification = localStorage.getItem('vuexy_notification');

    if (storedNotification) {
        const notification = JSON.parse(storedNotification);

        window.livewireNotification.emitNotification(notification);

        localStorage.removeItem('vuexy_notification');
    }
}
