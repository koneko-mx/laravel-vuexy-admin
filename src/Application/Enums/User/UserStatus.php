<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\User;

enum UserStatus: int
{
    case ENABLED  = 1;
    case DISABLED = 0;

    public function label(): string
    {
        return match ($this) {
            self::ENABLED  => 'Activo',
            self::DISABLED => 'Deshabilitado',
        };
    }
}
