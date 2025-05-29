<?php

declare(strict_types=1);

return [
    'driver' => 'mysql',
    'host' => env('KEYVAULT_DB_HOST', '127.0.0.1'),
    'database' => env('KEYVAULT_DB_DATABASE', 'key_vault'),
    'username' => env('KEYVAULT_DB_USERNAME', 'vault_user'),
    'password' => env('KEYVAULT_DB_PASSWORD', 'secret'),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => env('KEYVAULT_DB_PREFIX', ''),
    'strict' => true,
    'engine' => null,
];
