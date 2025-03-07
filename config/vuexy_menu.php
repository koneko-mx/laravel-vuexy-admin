<?php

return [
    'Inicio' => [
        'breadcrumbs' => false,
        'icon' => 'menu-icon tf-icons ti ti-home',
        'submenu' => [
            'Inicio' => [
                'route' => 'admin.core.home.index',
                'icon' => 'menu-icon tf-icons ti ti-home',
            ],
            'Sitio Web' => [
                'url' => env('APP_URL'),
                'icon' => 'menu-icon tf-icons ti ti-world-www',
            ],
            'Ajustes' => [
                'icon' => 'menu-icon tf-icons ti ti-settings-cog',
                'submenu' => [
                    'Aplicación' => [
                        'submenu' => [
                            'Ajustes generales' => [
                                'route' => 'admin.core.general-settings.index',
                                'can' => 'admin.core.general-settings.allow',
                            ],
                            'Ajustes de caché' => [
                                'route' => 'admin.core.cache-manager.index',
                                'can' => 'admin.core.cache-manager.view',
                            ],
                            'Servidor de correo SMTP' => [
                                'route' => 'admin.core.smtp-settings.index',
                                'can' => 'admin.core.smtp-settings.allow',
                            ],
                        ],
                    ],
                    'Empresa' => [
                        'submenu' => [
                            'Información general' => [
                                'route' => 'admin.store-manager.company.index',
                                'can' => 'admin.store-manager.company.view',
                            ],
                            'Sucursales' => [
                                'route' => 'admin.store-manager.stores.index',
                                'can' => 'admin.store-manager.stores.view',
                            ],
                            'Centros de trabajo' => [
                                'route' => 'admin.store-manager.work-centers.index',
                                'can' => 'admin.store-manager.stores.view',
                            ],
                        ]
                    ],
                    'BANXICO' => [
                        'route' => 'admin.finance.banxico.index',
                        'can' => 'admin.finance.banxico.allow',
                    ],
                    'Conectividad bancaria' => [
                        'route' => 'admin.finance.banking.index',
                        'can' => 'admin.finance.banking.allow',
                    ],
                    'Punto de venta' => [
                        'submenu' => [
                            'Ticket' => [
                                'route' => 'admin.sales.ticket-config.index',
                                'can' => 'admin.sales.ticket-config.allow',
                            ],
                        ]
                    ],
                    'Facturación' => [
                        'submenu' => [
                            'Certificados de Sello Digital' => [
                                'route' => 'admin.billing.csds-settings.index',
                                'can' => 'admin.billing.csds-settings.allow',
                            ],
                            'Paquete de timbrado' => [
                                'route' => 'admin.billing.stamping-package.index',
                                'can' => 'admin.billing.stamping-package.allow',
                            ],
                            'Servidor de correo SMTP' => [
                                'route' => 'admin.billing.smtp-settings.index',
                                'can' => 'admin.billing.smtp-settings.allow',
                            ],
                            'Descarga masiva de CFDI' => [
                                'route' => 'admin.billing.mass-cfdi-download.index',
                                'can' => 'admin.billing.mass-cfdi-download.allow',
                            ],
                        ]
                    ],
                ]
            ],
            'Sistema' => [
                'icon' => 'menu-icon tf-icons ti ti-user-cog',
                'submenu' => [
                    'Usuarios' => [
                        'route' => 'admin.core.users.index',
                        'can' => 'admin.core.users.view',
                    ],
                    'Roles' => [
                        'route' => 'admin.core.roles.index',
                        'can' => 'admin.core.roles.view',
                    ],
                    'Permisos' => [
                        'route' => 'admin.core.permissions.index',
                        'can' => 'admin.core.permissions.view',
                    ]
                ]
            ],
            'Catálogos' => [
                'icon' => 'menu-icon tf-icons ti ti-library',
                'submenu' => [
                    'Importar catálogos SAT' => [
                        'route' => 'admin.core.sat-catalogs.index',
                        'can' => 'admin.core.sat-catalogs.allow',
                    ],
                ]
            ],
            'Configuración de cuenta' => [
                'route' => 'admin.core.user-profile.index',
                'icon' => 'menu-icon tf-icons ti ti-user-cog',
            ],
            'Acerca de' => [
                'route' => 'admin.core.about.index',
                'icon' => 'menu-icon tf-icons ti ti-cat',
            ],
        ],
    ],
    'Herramientas Avanzadas' => [
        'icon' => 'menu-icon tf-icons ti ti-device-ipad-cog',
        'submenu' => [
            'Asistente AI' => [
                'icon' => 'menu-icon tf-icons ti ti-brain',
                'submenu' => [
                    'Panel de IA' => [
                        'route' => 'admin.ai.dashboard.index',
                        'can' => 'ai.dashboard.view',
                    ],
                    'Generación de Contenidos' => [
                        'route' => 'admin.ai.content.index',
                        'can' => 'ai.content.create',
                    ],
                    'Análisis de Datos' => [
                        'route' => 'admin.ai.analytics.index',
                        'can' => 'ai.analytics.view',
                    ],
                ],
            ],
            'Chatbot' => [
                'icon' => 'menu-icon tf-icons ti ti-message-chatbot',
                'submenu' => [
                    'Configuración' => [
                        'route' => 'admin.chatbot.config.index',
                        'can' => 'chatbot.config.view',
                    ],
                    'Flujos de Conversación' => [
                        'route' => 'admin.chatbot.flows.index',
                        'can' => 'chatbot.flows.manage',
                    ],
                    'Historial de Interacciones' => [
                        'route' => 'admin.chatbot.history.index',
                        'can' => 'chatbot.history.view',
                    ],
                ],
            ],
            'IoT Box' => [
                'icon' => 'menu-icon tf-icons ti ti-cpu',
                'submenu' => [
                    'Dispositivos Conectados' => [
                        'route' => 'admin.iot.devices.index',
                        'can' => 'iot.devices.view',
                    ],
                    'Sensores y Configuración' => [
                        'route' => 'admin.iot.sensors.index',
                        'can' => 'iot.sensors.manage',
                    ],
                    'Monitoreo en Tiempo Real' => [
                        'route' => 'admin.iot.monitoring.index',
                        'can' => 'iot.monitoring.view',
                    ],
                ],
            ],
            'Reconocimiento Facial' => [
                'icon' => 'menu-icon tf-icons ti ti-face-id',
                'submenu' => [
                    'Gestión de Perfiles' => [
                        'route' => 'admin.facial-recognition.profiles.index',
                        'can' => 'facial-recognition.profiles.manage',
                    ],
                    'Verificación en Vivo' => [
                        'route' => 'admin.facial-recognition.live.index',
                        'can' => 'facial-recognition.live.verify',
                    ],
                    'Historial de Accesos' => [
                        'route' => 'admin.facial-recognition.history.index',
                        'can' => 'facial-recognition.history.view',
                    ],
                ],
            ],
            'Servidor de Impresión' => [
                'icon' => 'menu-icon tf-icons ti ti-printer',
                'submenu' => [
                    'Cola de Impresión' => [
                        'route' => 'admin.print.queue.index',
                        'can' => 'print.queue.view',
                    ],
                    'Historial de Impresiones' => [
                        'route' => 'admin.print.history.index',
                        'can' => 'print.history.view',
                    ],
                    'Configuración de Impresoras' => [
                        'route' => 'admin.print.settings.index',
                        'can' => 'print.settings.manage',
                    ],
                ],
            ],
        ],
    ],
    'Sitio Web' => [
        'icon' => 'menu-icon tf-icons ti ti-tools',
        'submenu' => [
            'Ajustes generales' => [
                'icon' => 'menu-icon tf-icons ti ti-tools',
                'route' => 'admin.website.general-settings.index',
                'can' => 'website.general-settings.allow',
            ],
            'Avisos legales' => [
                'route' => 'admin.website.legal.index',
                'icon' => 'menu-icon tf-icons ti ti-writing-sign',
                'can' => 'website.legal.view',
            ],
            'Preguntas frecuentes' => [
                'route' => 'admin.website.faq.index',
                'icon' => 'menu-icon tf-icons ti ti-bubble-text',
                'can' => 'website.faq.view',
            ],
        ]
    ],
    'Blog' => [
        'icon' => 'menu-icon tf-icons ti ti-news',
        'submenu' => [
            'Categorias' => [
                'route' => 'admin.blog.categories.index',
                'icon' => 'menu-icon tf-icons ti ti-category',
                'can' => 'blog.categories.view',
            ],
            'Etiquetas' => [
                'route' => 'admin.blog.tags.index',
                'icon' => 'menu-icon tf-icons ti ti-tags',
                'can' => 'blog.tags.view',
            ],
            'Articulos' => [
                'route' => 'admin.blog.articles.index',
                'icon' => 'menu-icon tf-icons ti ti-news',
                'can' => 'blog.articles.view',
            ],
            'Comentarios' => [
                'route' => 'admin.blog.comments.index',
                'icon' => 'menu-icon tf-icons ti ti-message',
                'can' => 'blog.comments.view',
            ],
        ]
    ],
    'Contactos' => [
        'icon' => 'menu-icon tf-icons ti ti-users',
        'submenu' => [
            'Contactos' => [
                'route' => 'admin.crm.contacts.index',
                'icon' => 'menu-icon tf-icons ti ti-users',
                'can' => 'crm.contacts.view',
            ],
            'Campañas de marketing' => [
                'route' => 'admin.crm.marketing-campaigns.index',
                'icon' => 'menu-icon tf-icons ti ti-ad-2',
                'can' => 'crm.marketing-campaigns.view',
            ],
            'Oportunidades ' => [
                'route' => 'admin.crm.leads.index',
                'icon' => 'menu-icon tf-icons ti ti-target-arrow',
                'can' => 'crm.leads.view',
            ],
            'Newsletter' => [
                'route' => 'admin.crm.newsletter.index',
                'icon' => 'menu-icon tf-icons ti ti-notebook',
                'can' => 'crm.newsletter.view',
            ],
        ]
    ],
    'RRHH' => [
        'icon' => 'menu-icon tf-icons ti ti-users-group',
        'submenu' => [
            'Gestión de Empleados' => [
                'icon' => 'menu-icon tf-icons ti ti-id-badge-2',
                'submenu' => [
                    'Lista de Empleados' => [
                        'route' => 'admin.rrhh.employees.index',
                        'can' => 'rrhh.employees.view',
                    ],
                    'Agregar Nuevo Empleado' => [
                        'route' => 'admin.rrhh.employees.create',
                        'can' => 'rrhh.employees.create',
                    ],
                    'Puestos de trabajo' => [
                        'route' => 'admin.rrhh.jobs.index',
                        'can' => 'rrhh.jobs.view',
                    ],
                    'Estructura Organizacional' => [
                        'route' => 'admin.rrhh.organization.index',
                        'can' => 'rrhh.organization.view',
                    ],
                ],
            ],
            'Reclutamiento' => [
                'icon' => 'menu-icon tf-icons ti ti-user-search',
                'submenu' => [
                    'Vacantes Disponibles' => [
                        'route' => 'admin.recruitment.jobs.index',
                        'can' => 'recruitment.jobs.view',
                    ],
                    'Seguimiento de Candidatos' => [
                        'route' => 'admin.recruitment.candidates.index',
                        'can' => 'recruitment.candidates.view',
                    ],
                    'Entrevistas y Evaluaciones' => [
                        'route' => 'admin.recruitment.interviews.index',
                        'can' => 'recruitment.interviews.view',
                    ],
                ],
            ],
            'Nómina' => [
                'icon' => 'menu-icon tf-icons ti ti-cash',
                'submenu' => [
                    'Contratos' => [
                        'route' => 'admin.payroll.contracts.index',
                        'can' => 'payroll.contracts.view',
                    ],
                    'Procesar Nómina' => [
                        'route' => 'admin.payroll.process.index',
                        'can' => 'payroll.process.view',
                    ],
                    'Recibos de Nómina' => [
                        'route' => 'admin.payroll.receipts.index',
                        'can' => 'payroll.receipts.view',
                    ],
                    'Reportes Financieros' => [
                        'route' => 'admin.payroll.reports.index',
                        'can' => 'payroll.reports.view',
                    ],
                ],
            ],
            'Asistencia' => [
                'icon' => 'menu-icon tf-icons ti ti-calendar-exclamation',
                'submenu' => [
                    'Registro de Horarios' => [
                        'route' => 'admin.attendance.records.index',
                        'can' => 'attendance.records.view',
                    ],
                    'Asistencia con Biométricos' => [
                        'route' => 'admin.attendance.biometric.index',
                        'can' => 'attendance.biometric.view',
                    ],
                    'Justificación de Ausencias' => [
                        'route' => 'admin.attendance.absences.index',
                        'can' => 'attendance.absences.view',
                    ],
                ],
            ],
        ],
    ],
    'Productos y servicios' => [
        'icon' => 'menu-icon tf-icons ti ti-package',
        'submenu' => [
            'Categorias' => [
                'route' => 'admin.inventory.product-categories.index',
                'icon' => 'menu-icon tf-icons ti ti-category',
                'can' => 'admin.inventory.product-categories.view',
            ],
            'Catálogos' => [
                'route' => 'admin.inventory.product-catalogs.index',
                'icon' => 'menu-icon tf-icons ti ti-library',
                'can' => 'admin.inventory.product-catalogs.view',
            ],
            'Productos y servicios' => [
                'route' => 'admin.inventory.products.index',
                'icon' => 'menu-icon tf-icons ti ti-packages',
                'can' => 'admin.inventory.products.view',
            ],
            'Agregar producto o servicio' => [
                'route' => 'admin.inventory.products.create',
                'icon' => 'menu-icon tf-icons ti ti-package',
                'can' => 'admin.inventory.products.create',
            ],
        ]
    ],
    'Ventas' => [
        'icon' => 'menu-icon tf-icons ti ti-cash-register',
        'submenu' => [
            'Tablero' => [
                'route' => 'admin.sales.dashboard.index',
                'icon' => 'menu-icon tf-icons ti ti-chart-infographic',
                'can' => 'admin.sales.dashboard.allow',
            ],
            'Clientes' => [
                'route' => 'admin.sales.customers.index',
                'icon' => 'menu-icon tf-icons ti ti-users-group',
                'can' => 'admin.sales.customers.view',
            ],
            'Lista de precios' => [
                'route' => 'admin.sales.pricelist.index',
                'icon' => 'menu-icon tf-icons ti ti-report-search',
                'can' => 'admin.sales.sales.view',
            ],
            'Cotizaciones' => [
                'route' => 'admin.sales.quotes.index',
                'icon' => 'menu-icon tf-icons ti ti-file-dollar',
                'can' => 'admin.sales.quotes.view',
            ],
            'Ventas' => [
                'icon' => 'menu-icon tf-icons ti ti-cash-register',
                'submenu' => [
                    'Crear venta' => [
                        'route' => 'admin.sales.sales.create',
                        'can' => 'admin.sales.sales.create',
                    ],
                    'Ventas' => [
                        'route' => 'admin.sales.sales.index',
                        'can' => 'admin.sales.sales.view',
                    ],
                    'Ventas por producto o servicio' => [
                        'route' => 'admin.sales.sales-by-product.index',
                        'can' => 'admin.sales.sales.view',
                    ],
                ]
            ],
            'Remisiones' => [
                'icon' => 'menu-icon tf-icons ti ti-receipt',
                'submenu' => [
                    'Crear remisión' => [
                        'route' => 'admin.sales.remissions.create',
                        'can' => 'admin.sales.remissions.create',
                    ],
                    'Remisiones' => [
                        'route' => 'admin.sales.remissions.index',
                        'can' => 'admin.sales.remissions.view',
                    ],
                    'Remisiones por producto o servicio' => [
                        'route' => 'admin.sales.remissions-by-product.index',
                        'can' => 'admin.sales.remissions.view',
                    ],
                ]
            ],
            'Notas de crédito' => [
                'icon' => 'menu-icon tf-icons ti ti-receipt-refund',
                'submenu' => [
                    'Crear nota de crédito' => [
                        'route' => 'admin.sales.credit-notes.create',
                        'can' => 'admin.sales.credit-notes.create',
                    ],
                    'Notas de créditos' => [
                        'route' => 'admin.sales.credit-notes.index',
                        'can' => 'admin.sales.credit-notes.view',
                    ],
                    'Notas de crédito por producto o servicio' => [
                        'route' => 'admin.sales.credit-notes-by-product.index',
                        'can' => 'admin.sales.credit-notes.view',
                    ],
                ]
            ],
        ],
    ],
    'Finanzas' => [
        'icon' => 'menu-icon tf-icons ti ti-coins',
        'submenu' => [
            'Tablero Financiero' => [
                'route' => 'admin.accounting.dashboard.index',
                'icon' => 'menu-icon tf-icons ti ti-chart-infographic',
                'can' => 'accounting.dashboard.view',
            ],
            'Contabilidad' => [
                'icon' => 'menu-icon tf-icons ti ti-chart-pie',
                'submenu' => [
                    'Cuentas Contables' => [
                        'route' => 'admin.accounting.charts.index',
                        'can' => 'accounting.charts.view',
                    ],
                    'Cuentas por pagar' => [
                        'route' => 'admin.finance.accounts-payable.index',
                        'can' => 'finance.accounts-payable.view',
                    ],
                    'Cuentas por cobrar' => [
                        'route' => 'admin.finance.accounts-receivable.index',
                        'can' => 'finance.accounts-receivable.view',
                    ],
                    'Balance General' => [
                        'route' => 'admin.accounting.balance.index',
                        'can' => 'accounting.balance.view',
                    ],
                    'Estado de Resultados' => [
                        'route' => 'admin.accounting.income-statement.index',
                        'can' => 'accounting.income-statement.view',
                    ],
                    'Libro Mayor' => [
                        'route' => 'admin.accounting.ledger.index',
                        'can' => 'accounting.ledger.view',
                    ],
                    'Registros Contables' => [
                        'route' => 'admin.accounting.entries.index',
                        'can' => 'accounting.entries.view',
                    ],
                ],
            ],
            'Tablero de Gastos' => [
                'route' => 'admin.expenses.dashboard.index',
                'icon' => 'menu-icon tf-icons ti ti-chart-infographic',
                'can' => 'expenses.dashboard.view',
            ],
            'Gestión de Gastos' => [
                'icon' => 'menu-icon tf-icons ti ti-receipt-2',
                'submenu' => [
                    'Nuevo gasto' => [
                        'route' => 'admin.expenses.expenses.create',
                        'can' => 'expenses.expenses.create',
                    ],
                    'Gastos' => [
                        'route' => 'admin.expenses.expenses.index',
                        'can' => 'expenses.expenses.view',
                    ],
                    'Categorías de Gastos' => [
                        'route' => 'admin.expenses.categories.index',
                        'can' => 'expenses.categories.view',
                    ],
                    'Historial de Gastos' => [
                        'route' => 'admin.expenses.history.index',
                        'can' => 'expenses.history.view',
                    ],
                ],
            ],
        ],
    ],




    'Facturación' => [
        'icon' => 'menu-icon tf-icons ti ti-rubber-stamp',
        'submenu' => [
            'Tablero' => [
                'route' => 'admin.billing.dashboard.index',
                'icon' => 'menu-icon tf-icons ti ti-chart-infographic',
                'can' => 'admin.billing.dashboard.allow',
            ],
            'Ingresos' => [
                'icon' => 'menu-icon tf-icons ti ti-file-certificate',
                'submenu' => [
                    'Facturar ventas' => [
                        'route' => 'admin.billing.ingresos-stamp.index',
                        'can' => 'admin.billing.ingresos.create',
                    ],
                    'CFDI Ingresos' => [
                        'route' => 'admin.billing.ingresos.index',
                        'can' => 'admin.billing.ingresos.view',
                    ],
                    'CFDI Ingresos por producto o servicio' => [
                        'route' => 'admin.billing.ingresos-by-product.index',
                        'can' => 'admin.billing.ingresos.view',
                    ],
                ]
            ],
            'Egresos' => [
                'icon' => 'menu-icon tf-icons ti ti-file-certificate',
                'submenu' => [
                    'Facturar notas de crédito' => [
                        'route' => 'admin.billing.egresos-stamp.index',
                        'can' => 'admin.billing.egresos.create',
                    ],
                    'CFDI Engresos' => [
                        'route' => 'admin.billing.egresos.index',
                        'can' => 'admin.billing.egresos.view',
                    ],
                    'CFDI Engresos por producto o servicio' => [
                        'route' => 'admin.billing.egresos-by-product.index',
                        'can' => 'admin.billing.egresos.view',
                    ],
                ]
            ],
            'Pagos' => [
                'icon' => 'menu-icon tf-icons ti ti-file-certificate',
                'submenu' => [
                    'Facturar pagos' => [
                        'route' => 'admin.billing.pagos-stamp.index',
                        'can' => 'admin.billing.pagos.created',
                    ],
                    'CFDI Pagos' => [
                        'route' => 'admin.billing.pagos.index',
                        'can' => 'admin.billing.pagos.view',
                    ],
                ]
            ],
            'CFDI Nómina' => [
                'route' => 'admin.billing.nomina.index',
                'icon' => 'menu-icon tf-icons ti ti-file-certificate',
                'can' => 'admin.billing.nomina.view',
            ],
            'Verificador de CFDI 4.0' => [
                'route' => 'admin.billing.verify-cfdi.index',
                'icon' => 'menu-icon tf-icons ti ti-rosette-discount-check',
                'can' => 'admin.billing.verify-cfdi.allow',
            ],
        ]
    ],

    'Inventario y Logística' => [
        'icon' => 'menu-icon tf-icons ti ti-truck-delivery',
        'submenu' => [
            'Cadena de Suministro' => [
                'icon' => 'menu-icon tf-icons ti ti-chart-dots-3',
                'submenu' => [
                    'Proveedores' => [
                        'route' => 'admin.inventory.suppliers.index',
                        'can' => 'admin.inventory.suppliers.view',
                    ],
                    'Órdenes de Compra' => [
                        'route' => 'admin.inventory.orders.index',
                        'can' => 'admin.inventory.orders.view',
                    ],
                    'Recepción de Productos' => [
                        'route' => 'admin.inventory.reception.index',
                        'can' => 'admin.inventory.reception.view',
                    ],
                    'Gestión de Insumos' => [
                        'route' => 'admin.inventory.materials.index',
                        'can' => 'admin.inventory.materials.view',
                    ],
                ],
            ],
            'Gestión de Almacenes' => [
                'icon' => 'menu-icon tf-icons ti ti-building-warehouse',
                'submenu' => [
                    'Almacenes' => [
                        'route' => 'admin.inventory.warehouse.index',
                        'can' => 'admin.inventory.warehouse.view',
                    ],
                    'Stock de Inventario' => [
                        'route' => 'admin.inventory.stock.index',
                        'can' => 'admin.inventory.stock.view',
                    ],
                    'Movimientos de almacenes' => [
                        'route' => 'admin.inventory.movements.index',
                        'can' => 'admin.inventory.movements.view',
                    ],
                    'Transferencias entre Almacenes' => [
                        'route' => 'admin.inventory.transfers.index',
                        'can' => 'admin.inventory.transfers.view',
                    ],
                ],
            ],
            'Envíos y Logística' => [
                'icon' => 'menu-icon tf-icons ti ti-truck',
                'submenu' => [
                    'Órdenes de Envío' => [
                        'route' => 'admin.inventory.shipping-orders.index',
                        'can' => 'admin.inventory.shipping-orders.view',
                    ],
                    'Seguimiento de Envíos' => [
                        'route' => 'admin.inventory.shipping-tracking.index',
                        'can' => 'admin.inventory.shipping-tracking.view',
                    ],
                    'Transportistas' => [
                        'route' => 'admin.inventory.shipping-carriers.index',
                        'can' => 'admin.inventory.shipping-carriers.view',
                    ],
                    'Tarifas y Métodos de Envío' => [
                        'route' => 'admin.inventory.shipping-rates.index',
                        'can' => 'admin.inventory.shipping-rates.view',
                    ],
                ],
            ],
            'Gestión de Activos' => [
                'icon' => 'menu-icon tf-icons ti ti-tools-kitchen',
                'submenu' => [
                    'Activos Registrados' => [
                        'route' => 'admin.inventory.asset.index',
                        'can' => 'admin.inventory.asset.view',
                    ],
                    'Mantenimiento Preventivo' => [
                        'route' => 'admin.inventory.asset-maintenance.index',
                        'can' => 'admin.inventory.asset-maintenance.view',
                    ],
                    'Control de Vida Útil' => [
                        'route' => 'admin.inventory.asset-lifecycle.index',
                        'can' => 'admin.inventory.asset-lifecycle.view',
                    ],
                    'Asignación de Activos' => [
                        'route' => 'admin.inventory.asset-assignments.index',
                        'can' => 'admin.inventory.asset-assignments.view',
                    ],
                ],
            ],
        ],
    ],

    'Gestión Empresarial' => [
        'icon' => 'menu-icon tf-icons ti ti-briefcase',
        'submenu' => [
            'Gestión de Proyectos' => [
                'icon' => 'menu-icon tf-icons ti ti-layout-kanban',
                'submenu' => [
                    'Tablero de Proyectos' => [
                        'route' => 'admin.projects.dashboard.index',
                        'can' => 'projects.dashboard.view',
                    ],
                    'Proyectos Activos' => [
                        'route' => 'admin.projects.index',
                        'can' => 'projects.view',
                    ],
                    'Crear Proyecto' => [
                        'route' => 'admin.projects.create',
                        'can' => 'projects.create',
                    ],
                    'Gestión de Tareas' => [
                        'route' => 'admin.projects.tasks.index',
                        'can' => 'projects.tasks.view',
                    ],
                    'Historial de Proyectos' => [
                        'route' => 'admin.projects.history.index',
                        'can' => 'projects.history.view',
                    ],
                ],
            ],
            'Producción y Manufactura' => [
                'icon' => 'menu-icon tf-icons ti ti-building-factory',
                'submenu' => [
                    'Órdenes de Producción' => [
                        'route' => 'admin.production.orders.index',
                        'can' => 'production.orders.view',
                    ],
                    'Nueva Orden de Producción' => [
                        'route' => 'admin.production.orders.create',
                        'can' => 'production.orders.create',
                    ],
                    'Control de Procesos' => [
                        'route' => 'admin.production.process.index',
                        'can' => 'production.process.view',
                    ],
                    'Historial de Producción' => [
                        'route' => 'admin.production.history.index',
                        'can' => 'production.history.view',
                    ],
                ],
            ],
            'Control de Calidad' => [
                'icon' => 'menu-icon tf-icons ti ti-award',
                'submenu' => [
                    'Inspecciones de Calidad' => [
                        'route' => 'admin.quality.inspections.index',
                        'can' => 'quality.inspections.view',
                    ],
                    'Crear Inspección' => [
                        'route' => 'admin.quality.inspections.create',
                        'can' => 'quality.inspections.create',
                    ],
                    'Reportes de Calidad' => [
                        'route' => 'admin.quality.reports.index',
                        'can' => 'quality.reports.view',
                    ],
                    'Historial de Inspecciones' => [
                        'route' => 'admin.quality.history.index',
                        'can' => 'quality.history.view',
                    ],
                ],
            ],
            'Flujos de Trabajo y Automatización' => [
                'icon' => 'menu-icon tf-icons ti ti-chart-dots-3',
                'submenu' => [
                    'Gestión de Flujos de Trabajo' => [
                        'route' => 'admin.workflows.index',
                        'can' => 'workflows.view',
                    ],
                    'Crear Flujo de Trabajo' => [
                        'route' => 'admin.workflows.create',
                        'can' => 'workflows.create',
                    ],
                    'Automatizaciones' => [
                        'route' => 'admin.workflows.automations.index',
                        'can' => 'workflows.automations.view',
                    ],
                    'Historial de Flujos' => [
                        'route' => 'admin.workflows.history.index',
                        'can' => 'workflows.history.view',
                    ],
                ],
            ],
        ],
    ],


    'Contratos' => [
        'icon' => 'menu-icon tf-icons ti ti-writing-sign',
        'submenu' => [
            'Mis Contratos' => [
                'route' => 'admin.contracts.index',
                'icon' => 'menu-icon tf-icons ti ti-file-description',
                'can' => 'contracts.view',
            ],
            'Firmar Contrato' => [
                'route' => 'admin.contracts.sign',
                'icon' => 'menu-icon tf-icons ti ti-signature',
                'can' => 'contracts.sign',
            ],
            'Contratos Automatizados' => [
                'route' => 'admin.contracts.automated',
                'icon' => 'menu-icon tf-icons ti ti-robot',
                'can' => 'contracts.automated.view',
            ],
            'Historial de Contratos' => [
                'route' => 'admin.contracts.history',
                'icon' => 'menu-icon tf-icons ti ti-archive',
                'can' => 'contracts.history.view',
            ],
        ]
    ],
    'Atención al Cliente' => [
        'icon' => 'menu-icon tf-icons ti ti-messages',
        'submenu' => [
            'Tablero' => [
                'route' => 'admin.sales.dashboard.index',
                'icon' => 'menu-icon tf-icons ti ti-chart-infographic',
                'can' => 'ticketing.dashboard.view',
            ],
            'Mis Tickets' => [
                'route' => 'admin.ticketing.tickets.index',
                'icon' => 'menu-icon tf-icons ti ti-ticket',
                'can' => 'ticketing.tickets.view',
            ],
            'Crear Ticket' => [
                'route' => 'admin.ticketing.tickets.create',
                'icon' => 'menu-icon tf-icons ti ti-square-plus',
                'can' => 'ticketing.tickets.create',
            ],
            'Categorías de Tickets' => [
                'route' => 'admin.ticketing.categories.index',
                'icon' => 'menu-icon tf-icons ti ti-category',
                'can' => 'ticketing.categories.view',
            ],
            'Estadísticas de Atención' => [
                'route' => 'admin.ticketing.analytics.index',
                'icon' => 'menu-icon tf-icons ti ti-chart-bar',
                'can' => 'ticketing.analytics.view',
            ],
        ]
    ],
];
