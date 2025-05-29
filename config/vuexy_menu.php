<?php

declare(strict_types=1);

// Este archivo **NO se registra como config**, es usado por VuexyMenuRegistry

return [
    'Inicio' => [
        '_meta' => [
            'icon' => 'ti ti-home',
            'description' => 'Accede rápidamente a las funciones principales y configuraciones del sistema.',
            'priority' => 'first',
        ],
        'submenu' => [
            'Ajustes de sistema' => [
                '_meta' => [
                    'icon' => 'ti ti-adjustments-alt',
                    'description' => 'Configura los parámetros generales de la aplicación.',
                    'home_at_root' => true,
                    'priority'     => 200,
                ],
                'submenu' => [
                    'Usuarios y permisos' => [
                        '_meta' => [
                            'icon' => 'ti ti-user-cog',
                            'description' => 'Gestiona usuarios, roles y permisos de acceso dentro del sistema.',
                            'home_at_root' => true,
                            'priority'     => 100,
                        ],
                        'submenu' => [
                            'Usuarios' => [
                                'icon' => 'ti ti-users',
                                'description' => 'Administración de usuarios del sistema.',
                                'route' => 'admin.core.users.users.index',
                                'can' => 'admin.core.users.users.view',
                                'priority' => 100,
                            ],
                            'Roles' => [
                                'icon' => 'ti ti-lock-access',
                                'description' => 'Configuración de roles y niveles de acceso.',
                                'route' => 'admin.core.rbac.roles.index',
                                'can' => 'admin.core.rbac.roles.view',
                                'priority' => 200,
                            ],
                            'Permisos' => [
                                'icon' => 'ti ti-key',
                                'description' => 'Gestión avanzada de permisos de usuario.',
                                'route' => 'admin.core.rbac.permissions.index',
                                'can' => 'admin.core.rbac.permissions.view',
                                'priority' => 300,
                            ]
                        ]
                    ],
                    'Aplicación' => [
                        '_meta' => [
                            'icon' => 'ti ti-device-desktop-cog',
                            'description' => 'Configura los parámetros generales de la aplicación.',
                            'widget_label' => 'Configuracione de la aplicación',
                            'home_at_root' => true,
                            'priority'     => 200,
                        ],
                        'submenu' => [
                            'Interfaz Web Admin' => [
                                'icon' => 'ti ti-device-desktop-cog',
                                'description' => 'Ajustes de entorno Web.',
                                'route' => 'admin.core.settings.web-interface.index',
                                'can' => 'admin.core.settings.web-interface.view',
                                'priority' => 100,
                            ],
                            'Inteface Vuexy' => [
                                'icon' => 'ti ti-template',
                                'description' => 'Configuración de la apariencia y comportamiento de la interfaz Koneko Vuexy Admin.',
                                'route' => 'admin.core.settings.vuexy-interface.index',
                                'can' => 'admin.core.settings.vuexy-interface.view',
                                'priority' => 200,
                            ],
                        ]
                    ],
                    'Correo electrónico' => [
                        '_meta' => [
                            'icon' => 'ti ti-mail-cog',
                            'description' => 'Configuración de servidor SMTP para correo electrónico.',
                            'widget_label' => 'Configuración de correo electrónico',
                            'home_at_root' => true,
                            'priority'     => 300,
                        ],
                        'submenu' => [
                            'Servidor de correo saliente' => [
                                'icon' => 'ti ti-mail-cog',
                                'description' => 'Configuración de los servidores de correo para notificaciones.',
                                'route' => 'admin.core.settings.smtp.index',
                                'can' => 'admin.core.settings.smtp.view',
                                'priority' => 100,
                            ],
                        ]
                    ],
                    'Variables de entorno' => [
                        'icon' => 'ti ti-settings-code',
                        'description' => 'Visualiza y modifica variables de entorno.',
                        'route' => 'admin.core.settings.env.index',
                        'can' => 'admin.core.settings.env.view',
                        'priority' => 'last',
                    ],
                ]
            ],
            'Herramientas' => [
                '_meta' => [
                    'icon' => 'ti ti-tool',
                    'description' => 'Conjunto de herramientas para el mantenimiento del sistema.',
                    'widget_label' => 'Herramientas de sistema',
                    'home_at_root' => true,
                    'priority'     => 300,
                ],
                'submenu' => [
                    'Tareas programadas' => [
                        '_meta' => [
                            'icon' => 'ti ti-clock',
                            'description' => 'Supervisa y gestiona tareas periódicas, trabajos en cola y estado del scheduler.',
                            'widget_label' => 'Planificación de tareas',
                            'home_at_root' => true,
                            'priority'     => 100,
                        ],
                        'submenu' => [
                            'Panel general' => [
                                'icon' => 'ti ti-dashboard',
                                'description' => 'Resumen del estado del planificador, workers, colas activas y errores recientes.',
                                'route' => 'admin.core.scheduler.dashboard',
                                'can' => 'admin.core.scheduler.dashboard.view',
                                'priority' => 100,
                            ],
                            'Tareas programadas' => [
                                'icon' => 'ti ti-clock-hour-4',
                                'description' => 'Lista detallada de tareas programadas por Cron o Laravel Scheduler.',
                                'route' => 'admin.core.scheduler.cron.index',
                                'can' => 'admin.core.scheduler.cron.view',
                                'priority' => 200,
                            ],
                            'Jobs en cola' => [
                                'icon' => 'ti ti-list-check',
                                'description' => 'Supervisa los trabajos en cola, sus estados y tiempos de ejecución.',
                                'route' => 'admin.core.scheduler.queued-jobs.index',
                                'can' => 'admin.core.scheduler.queued-jobs.view',
                                'priority' => 300,
                            ],
                            'Historial de ejecución' => [
                                'icon' => 'ti ti-history',
                                'description' => 'Revisa el historial de tareas y jobs ejecutados, con resultados y tiempos.',
                                'route' => 'admin.core.scheduler.history.index',
                                'can' => 'admin.core.scheduler.history.view',
                                'priority' => 400,
                            ],
                            'Configuración de scheduler' => [
                                'icon' => 'ti ti-settings-cog',
                                'description' => 'Configura comportamiento del scheduler, fallback y workers.',
                                'route' => 'admin.core.scheduler.settings.index',
                                'can' => 'admin.core.scheduler.settings.view',
                                'priority' => 500,
                            ],
                        ],
                    ],
                    'Caché' => [
                        '_meta' => [
                            'icon' => 'ti ti-cpu',
                            'description' => 'Administración y limpieza de caché para mejorar el rendimiento.',
                            'widget_label' => 'Caché de sistema',
                            'home_at_root' => true,
                            'priority' => 200,
                        ],
                        'submenu' => [
                            'Controladores' => [
                                '_meta' => [
                                    'icon' => 'ti ti-cpu',
                                    'description' => 'Administración y limpieza de caché para mejorar el rendimiento.',
                                    'home_at_root' => true,
                                    'priority' => 100,
                                ],
                                'submenu' => [
                                    'Controlador de sesiones' => [
                                        'icon' => 'ti ti-user-check',
                                        'description' => 'Muestra configuraciones de sesiones de usuarios y permite su limpieza.',
                                        'route' => 'admin.core.cache.sessions.index',
                                        'can' => 'admin.core.cache.sessions.view',
                                        'priority' => 100,
                                    ],
                                    'Redis' => [
                                        'icon' => 'ti ti-cpu',
                                        'description' => 'Administración y limpieza de caché Redis para mejorar el rendimiento.',
                                        'route' => 'admin.core.cache.redis.index',
                                        'can' => 'admin.core.cache.redis.view',
                                        'priority' => 200,
                                    ],
                                    'Memcache' => [
                                        'icon' => 'ti ti-cpu',
                                        'description' => 'Administración y limpieza de caché Memcache para mejorar el rendimiento.',
                                        'route' => 'admin.core.cache.memcache.index',
                                        'can' => 'admin.core.cache.memcache.view',
                                        'priority' => 300,
                                    ],
                                ]
                            ],
                            'Gestores' => [
                                '_meta' => [
                                    'icon' => 'ti ti-cpu',
                                    'description' => 'Administración y limpieza de caché para mejorar el rendimiento.',
                                    'widget_label' => 'Gestores de caché',
                                    'home_at_root' => true,
                                    'priority' => 200,
                                ],
                                'submenu' => [
                                    'Caché Laravel' => [
                                        'icon' => 'ti ti-cpu',
                                        'description' => 'Administración y limpieza de caché de Laravel.',
                                        'route' => 'admin.core.cache.laravel.index',
                                        'can' => 'admin.core.cache.laravel.view',
                                        'priority' => 100,
                                    ],
                                    'Caché Koneko Vuexy' => [
                                        'icon' => 'ti ti-cpu-2',
                                        'description' => 'Configura y optimiza la caché del sistema vuexy admin.',
                                        'route' => 'admin.core.cache.vuexy.index',
                                        'can' => 'admin.core.cache.vuexy.view',
                                        'priority' => 200,
                                    ],
                                    'Carga de Vite / Assets' => [
                                        'icon' => 'ti ti-brand-vite',
                                        'description' => 'Estado de compilación y assets generados por Vite.',
                                        'route' => 'admin.core.cache.vite-assets.index',
                                        'can' => 'admin.core.cache.vite-assets.view',
                                        'priority' => 300,
                                    ],
                                    'Manejador de TTLs' => [
                                        'icon' => 'ti ti-clock-edit',
                                        'description' => 'Ajuste manual de TTLs e invalidación selectiva por tag.',
                                        'route' => 'admin.core.cache.ttls.index',
                                        'can' => 'admin.core.cache.ttls.view',
                                        'priority' => 700,
                                    ],
                                ]
                            ],
                        ]
                    ],
                    'Notificaciones del sistema' => [
                        '_meta' => [
                            'icon' => 'ti ti-bell',
                            'description' => 'Configura y monitorea notificaciones persistentes del sistema y mensajes push por usuario.',
                            'widget_label' => 'Centro de notificaciones',
                            'home_at_root' => true,
                            'priority'     => 400,
                        ],
                        'submenu' => [
                            'Notificaciones globales' => [
                                'icon' => 'ti ti-broadcast',
                                'description' => 'Mensajes visibles por roles, flags o alcance global. Incluye confirmaciones y banners.',
                                'route' => 'admin.core.notifications.system.index',
                                'can' => 'admin.core.notifications.system.view',
                                'priority' => 100,
                            ],
                            'Notificaciones individuales' => [
                                'icon' => 'ti ti-bell-ringing',
                                'description' => 'Mensajes enviados a usuarios individuales por actividad o eventos.',
                                'route' => 'admin.core.notifications.personal.index',
                                'can' => 'admin.core.notifications.personal.view',
                                'priority' => 200,
                            ],
                            'Configuración del centro de alertas' => [
                                'icon' => 'ti ti-settings-2',
                                'description' => 'Define estilos, comportamiento y zonas del sistema para las notificaciones.',
                                'route' => 'admin.core.notifications.settings.index',
                                'can' => 'admin.core.notifications.settings.view',
                                'priority' => 300,
                            ],
                        ],
                    ],
                    'WebSockets' => [
                        '_meta' => [
                            'icon' => 'ti ti-plug-connected',
                            'description' => 'Control y monitoreo de canales WebSocket, usuarios conectados, métricas y eventos en tiempo real.',
                            'home_at_root' => true,
                            'priority' => 400,
                        ],
                        'submenu' => [
                            'Canales activos' => [
                                'icon' => 'ti ti-plug-connected',
                                'route' => 'admin.websockets.channels.index',
                                'description' => 'Visualiza todos los canales activos, públicos, privados y presence, con detalles en vivo.',
                                'can' => 'admin.websockets.channels.view',
                            ],

                            'Eventos transmitidos' => [
                                'icon' => 'ti ti-broadcast',
                                'route' => 'admin.websockets.events.index',
                                'description' => 'Explora el historial y escucha en tiempo real los eventos emitidos desde el ERP.',
                                'can' => 'admin.websockets.events.view',
                            ],

                            'Usuarios conectados' => [
                                'icon' => 'ti ti-users',
                                'route' => 'admin.websockets.users.index',
                                'description' => 'Lista de usuarios activos en tiempo real, con canales, IP, roles y detalles de sesión.',
                                'can' => 'admin.websockets.users.view',
                            ],

                            'Métricas en tiempo real' => [
                                'icon' => 'ti ti-activity',
                                'route' => 'admin.websockets.metrics.index',
                                'description' => 'Gráficos de rendimiento por minuto: usuarios conectados, eventos enviados y canales usados.',
                                'can' => 'admin.websockets.metrics.view',
                            ],

                            'Simulador de eventos' => [
                                'icon' => 'ti ti-terminal-2',
                                'route' => 'admin.websockets.simulator.index',
                                'description' => 'Prueba la emisión de eventos a canales y usuarios para depuración o integración avanzada.',
                                'can' => 'admin.websockets.simulator.use',
                            ],

                            'Estado del servidor' => [
                                'icon' => 'ti ti-heartbeat',
                                'route' => 'admin.websockets.status.index',
                                'description' => 'Estado del backend WebSocket (Echo Server, Pusher, Laravel WebSockets).',
                                'can' => 'admin.websockets.status.view',
                            ],

                            'Auditoría de conexiones' => [
                                'icon' => 'ti ti-shield-lock',
                                'route' => 'admin.websockets.audit.index',
                                'description' => 'Registro detallado de conexiones, errores, reconexiones, autenticaciones y desconexiones.',
                                'can' => 'admin.websockets.audit.view',
                            ],

                            'Configuración WebSocket' => [
                                'icon' => 'ti ti-settings',
                                'route' => 'admin.websockets.settings.index',
                                'description' => 'Ajusta parámetros como claves Pusher, reconexión, canalización y drivers activos.',
                                'can' => 'admin.websockets.settings.edit',
                            ],

                            'Integraciones en tiempo real' => [
                                'icon' => 'ti ti-link',
                                'route' => 'admin.websockets.integrations.index',
                                'description' => 'Controla qué módulos del ERP están conectados a eventos WebSocket: notificaciones, chat, banners.',
                                'can' => 'admin.websockets.integrations.manage',
                            ],

                            'Probar canal personalizado' => [
                                'icon' => 'ti ti-test-pipe',
                                'route' => 'admin.websockets.tester.index',
                                'description' => 'Suscríbete y escucha manualmente cualquier canal para fines de desarrollo o debugging.',
                                'can' => 'admin.websockets.tester.access',
                            ],
                        ]
                    ],
                    'Monitoreo' => [
                        '_meta' => [
                            'icon' => 'ti ti-heart-rate-monitor',
                            'description' => 'Monitoreo profundo del rendimiento y salud del sistema.',
                            'home_at_root' => true,
                            'priority' => 800,
                        ],
                        'submenu' => [
                            'Sesiones activas' => [
                                'icon' => 'ti ti-devices',
                                'description' => 'Control avanzado sobre sesiones activas y accesos concurrentes.',
                                'route' => 'admin.core.monitor.sessions.index',
                                'can' => 'admin.core.monitor.sessions.view',
                                'priority' => 900
                            ],
                        ],
                    ],
                ],
            ],
            'Koneko Vuexy Admin' => [
                '_meta' => [
                    'icon' => 'ti ti-cloud-computing',
                    'description' => 'Administrador de paquetes del ecosistema Koneko Vuexy Admin.',
                    'home_at_root' => true,
                    'priority'     => 'last',
                ],
                'submenu' => [
                    'Librerías y plugins' => [
                        'icon' => 'ti ti-plug',
                        'description' => 'Gestiona las librerías y plugins del módulo Vuexy Admin.',
                        'route' => 'admin.core.modules.plugins.index',
                        'can' => 'admin.core.modules.plugins.view',
                        'priority' => 100,
                    ],
                    'Configuración de módulos' => [
                        'icon' => 'ti ti-puzzle',
                        'description' => 'Administra la configuración avanzada de módulos y paquetes instalados.',
                        'route' => 'admin.core.modules.config.index',
                        'can' => 'admin.core.modules.config.view',
                        'priority' => 200,
                    ],
                ]
            ],
            'Auditoría' => [
                '_meta' => [
                    'icon' => 'ti ti-lock',
                    'description' => 'Supervisión avanzada de eventos, accesos, acciones de usuario y logs del sistema.',
                    'home_at_root' => true,
                    'priority'     => 800,
                ],
                'submenu' => [
                    'Eventos del Sistema' => [
                        '_meta' => [
                            'icon' => 'ti ti-shield-lock',
                            'description' => 'Eventos internos generados por el uso del ERP Koneko Vuexy.',
                            'home_at_root' => true,
                            'priority'     => 100,
                        ],
                        'submenu' => [
                            'Logs de Acceso' => [
                                'icon' => 'ti ti-user-shield',
                                'description' => 'Historial de inicios de sesión y cierres por usuario.',
                                'route' => 'admin.core.audit.access.index',
                                'can' => 'admin.core.audit.access.view',
                                'priority' => 100
                            ],
                            'Eventos de Seguridad' => [
                                'icon' => 'ti ti-shield',
                                'description' => 'Registros enriquecidos con geolocalización, IP, dispositivos y actividad sospechosa.',
                                'route' => 'admin.core.audit.security-events.index',
                                'can' => 'admin.core.audit.security-events.view',
                                'priority' => 200
                            ],
                            'Interacciones de Usuario' => [
                                'icon' => 'ti ti-user-check',
                                'description' => 'Registro detallado de acciones ejecutadas por usuarios en la interfaz.',
                                'route' => 'admin.core.audit.user-interactions.index',
                                'can' => 'admin.core.audit.user-interactions.view',
                                'priority' => 300
                            ],
                        ],
                    ],
                    'Registros de Log' => [
                        '_meta' => [
                            'icon' => 'ti ti-archive',
                            'description' => 'Registros técnicos del sistema almacenados en archivos o base de datos.',
                            'widget_label' => 'Registros de Log de auditoría',
                            'home_at_root' => true,
                            'priority'     => 200,
                        ],
                        'submenu' => [
                            'Logs del Sistema' => [
                                'icon' => 'ti ti-file-text',
                                'description' => 'Visualiza logs generados por Laravel u otros sistemas locales.',
                                'route' => 'admin.core.audit.file-logs.index',
                                'can' => 'admin.core.audit.file-logs.view',
                                'priority' => 100
                            ],
                            'Logs de Auditoría' => [
                                'icon' => 'ti ti-database-search',
                                'description' => 'Consulta los logs persistidos en la base de datos estructurados por tipo y nivel.',
                                'route' => 'admin.core.audit.db-logs.index',
                                'can' => 'admin.core.audit.db-logs.view',
                                'priority' => 200
                            ],
                        ],
                    ],
                    'Alertas y Reportes' => [
                        'icon' => 'ti ti-bell',
                        'description' => 'Configura alertas automáticas, condiciones críticas y reportes periódicos.',
                        'route' => 'admin.core.audit.alerts.index',
                        'can' => 'admin.core.audit.alerts.view',
                        'priority' => 300
                    ],
                    'Configuración de Logging' => [
                        'icon' => 'ti ti-settings',
                        'description' => 'Configuración avanzada del sistema de logging y auditoría.',
                        'route' => 'admin.core.audit.logging-settings.index',
                        'can' => 'admin.core.audit.logging-settings.view',
                        'priority' => 400
                    ],
                ]
            ],
            'Acerca de' => [
                'icon' => 'ti ti-cat',
                'description' => 'Información sobre la versión y desarrolladores del sistema.',
                'route' => 'admin.core.pages.about.index',
                'priority' => 'last',
            ],
        ],
    ],
    '_extra' => [
        '_quicklinks' => [
            'Inicio' => [
                'icon' => 'ti ti-home',
                'route' => 'admin.core.pages.home.index',
            ],
            'Mi perfil' => [
                'icon' => 'ti ti-user-circle',
                'route' => 'admin.users.profile',
            ],
        ],
    ],
];
