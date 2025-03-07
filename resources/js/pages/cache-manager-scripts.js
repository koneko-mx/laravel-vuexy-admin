import '../../assets/js/notifications/LivewireNotification.js';
import FormCustomListener from '../../assets/js/forms/formCustomListener';

// Inicializar listener para estadísticas de cache
new FormCustomListener({
    buttonSelectors: ['.btn-clear-cache', '.btn-reload-cache-stats']
});

// Inicializar listener para funciones de cache
new FormCustomListener({
    formSelector: '#cache-functions-card',
    buttonSelectors: ['.btn', '.btn-config-cache', '.btn-cache-routes'],
    callbacks: [
        null, // Callback por defecto para .btn
        (form, button) => {
            // Emitir notificación de carga
            notification.emitNotification({
                target: '#cache-functions-card .notification-container',
                message: 'Generando cache de configuraciones de Laravel...',
                type: 'warning'
            });

            // Generar cache de configuraciones mediante una petición AJAX
            fetch('/admin/cache/config/cache', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al generar el cache de configuraciones');
                    }
                    return response.json();
                })
                .then(() => {
                    // Emitir notificación de éxito con recarga diferida
                    notification.emitNotification({
                        target: '#cache-functions-card .notification-container',
                        message: 'Se ha cacheado la configuración de Laravel...',
                        type: 'success',
                        deferReload: true
                    });
                })
                .catch(error => {
                    // Emitir notificación de error
                    notification.emitNotification({
                        target: '#cache-functions-card .notification-container',
                        message: `Error: ${error.message}`,
                        type: 'danger'
                    });
                    console.error('Error al generar el cache:', error);
                });
        },
        (form, button) => {
            // Emitir notificación de carga
            notification.emitNotification({
                target: '#cache-functions-card .notification-container',
                message: 'Generando cache de rutas de Laravel...',
                type: 'warning'
            });

            // Recargar estadísticas de cache mediante una petición AJAX
            fetch('/admin/cache/route/cache', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al recargar las estadísticas de cache');
                    }
                    return response.json();
                })
                .then(() => {
                    // Emitir notificación de éxito con recarga diferida
                    notification.emitNotification({
                        target: '#cache-functions-card .notification-container',
                        message: 'Se han cacheado las rutas de Laravel...',
                        type: 'success',
                        deferReload: true
                    });
                })
                .catch(error => {
                    // Emitir notificación de error
                    notification.emitNotification({
                        target: '#cache-functions-card .notification-container',
                        message: `Error: ${error.message}`,
                        type: 'danger'
                    });
                    console.error('Error al recargar las estadísticas:', error);
                });
        }
    ]
});
