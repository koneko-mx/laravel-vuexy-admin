<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\SecurityEvents;

enum SecurityEventType: string
{
    case LOGIN_FAILED  = 'failed_login_attempt';
    case LOGIN_SUCCESS = 'login_success';


    public function label(): string
    {
        return match ($this) {
            self::LOGIN_FAILED  => 'Inicio fallido',
            self::LOGIN_SUCCESS => 'Inicio exitoso',
        };
    }

    public static function options(): array
    {
        return array_column(self::cases(), 'label', 'value');
    }
}
