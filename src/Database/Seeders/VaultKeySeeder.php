<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Koneko\VuexyAdmin\Application\Security\VaultKeyService;

class VaultKeySeeder extends Seeder
{
    public function run(): void
    {
        $service = app(VaultKeyService::class);

        // Entorno Local
        $service->generateKey('sat_cert_dev', 'koneko_erp', 'AES-256-CBC', 32);
        $service->generateKey('api_token_dev', 'koneko_website', 'AES-256-CBC', 32);
    }
}