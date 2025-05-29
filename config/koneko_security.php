<?php

return [
// ================== 🔒 SEGURIDAD ==================
    // 🔐 HTTPS y Proxies
    'https' => [
        'force'       => (bool) env('FORCE_HTTPS', false),
    ],
    'proxies' => [
        'enabled'     => (bool) env('TRUST_PROXY', false),
        'ips'         => env('TRUST_PROXY_IPS', '*'),
    ],

    // 🗝️ Key Vault & Gestión de Claves
    'key_vault' => [
        // Namespace por defecto y cliente global (si aplica)
        'default_namespace' => env('KONEKO_KEY_VAULT_NAMESPACE', 'default'),
        'default_project'   => env('KONEKO_PROJECT_CODE', 'erp'),
        'default_client_id' => env('KONEKO_CLIENT_ID'),


        // 🔑 Cliente que accede a claves de un servidor remoto o local
        'client' => [
            'driver'     => env('KONEKO_KEY_VAULT_DRIVER', 'database'), // koneko_api, database, laravel
            'connection' => env('KONEKO_KEY_VAULT_DB_CONNECTION', 'vault'),
            'table'      => env('KONEKO_KEY_VAULT_DB_TABLE', 'vault_client_keys'),
            'project'    => env('KONEKO_PROJECT_CODE', 'erp'),
            'namespace'  => env('KONEKO_KEY_VAULT_NAMESPACE', 'default'),
            'client_id'  => env('KONEKO_CLIENT_ID'),
        ],

        // 🗃️ Conexión y configuración del servidor de claves (solo si actúa como vault)
        'server' => [
            'connection' => 'vault', // conexión que administra la tabla físicamente
            'table'      => 'vault_client_keys',
        ],


        'drivers' => [
            // Laravel Default Encryption (APP_KEY)
            'laravel' => [
                'key'       => env('APP_KEY'),
                'algorithm' => 'AES-256-CBC',
            ],
            // Second DB Configuration (Requires separate connection)
            'database' => [
                'connection' => env('KONEKO_KEY_VAULT_DB_CONNECTION', 'vault'),
                'table'      => env('KONEKO_KEY_VAULT_DB_TABLE', 'vault_keys'),
                'algorithm'  => env('KONEKO_KEY_VAULT_DB_ALGORITHM', 'AES-256-CBC'),
            ],
            // External Go Microservice
            'koneko_api' => [
                'base_url'  => env('KONEKO_KEY_VAULT_SERVICE_URL'),
                'api_token' => env('KONEKO_KEY_VAULT_SERVICE_TOKEN'),
                'timeout'   => (int) env('KONEKO_KEY_VAULT_SERVICE_TIMEOUT', 5),
            ],
        ],
    ],
];
