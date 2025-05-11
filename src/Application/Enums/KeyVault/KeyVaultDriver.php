<?php

namespace Koneko\VuexyAdmin\Application\Enums\KeyVault;

enum KeyVaultDriver: string {
    case LARAVEL = 'laravel';
    case DATABASE = 'database';
    case SERVICE = 'service';
}