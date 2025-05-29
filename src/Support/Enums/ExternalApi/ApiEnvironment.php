<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Enums\ExternalApi;

enum ApiEnvironment: string
{
    case Development = 'dev';
    case Staging     = 'staging';
    case Production  = 'production';

    public function label(): string
    {
        return match ($this) {
            self::Development => 'Desarrollo',
            self::Staging     => 'Pruebas (Staging)',
            self::Production  => 'Producción',
        };
    }
}
