<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Vault Client Mode
    |--------------------------------------------------------------------------
    | Define si este proyecto se comporta como "client", "server", o "both".
    | Este valor se puede usar para omitir migraciones o inicializar módulos.
    */
    'mode' => env('KONEKO_KEY_VAULT_MODE', 'client'), // client | server | both

    /*
    |--------------------------------------------------------------------------
    | Cliente de Claves - Lectura local o remota
    |--------------------------------------------------------------------------
    */
    'client' => [
        'driver'     => env('KONEKO_KEY_VAULT_DRIVER', 'laravel'), // koneko_api | database | laravel
        'connection' => env('KONEKO_KEY_VAULT_DB_CONNECTION', 'vault'),
        'table'      => env('KONEKO_KEY_VAULT_DB_TABLE', 'vault_client_keys'),

        'project'    => env('KONEKO_PROJECT_CODE', 'erp'),
        'namespace'  => env('KONEKO_KEY_VAULT_NAMESPACE', 'default'),
        'client_id'  => env('KONEKO_CLIENT_ID'),

        // 🔐 Opción para lectura desde archivo plano en sistema
        'key_path'   => env('KONEKO_KEY_VAULT_CLIENT_KEY_PATH', '/etc/koneko/vault_value.key'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Servidor de Claves - Proyectos que gestionan claves de múltiples clientes
    |--------------------------------------------------------------------------
    */
    'server' => [
        'enabled'    => env('KONEKO_KEY_VAULT_SERVER_ENABLED', false),
        'connection' => env('KONEKO_KEY_VAULT_SERVER_CONNECTION', 'vault'),
        'table'      => env('KONEKO_KEY_VAULT_SERVER_TABLE', 'vault_client_keys'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Drivers disponibles (por ejemplo, API externa Koneko)
    |--------------------------------------------------------------------------
    */
    'drivers' => [
        'koneko_api' => [
            'base_url'  => env('KONEKO_VAULT_API_URL', 'https://vault.koneko.mx/api/v1/keys'),
            'api_token' => env('KONEKO_VAULT_API_TOKEN'),
            'timeout'   => (int) env('KONEKO_VAULT_API_TIMEOUT', 3),
        ],
    ],
];
