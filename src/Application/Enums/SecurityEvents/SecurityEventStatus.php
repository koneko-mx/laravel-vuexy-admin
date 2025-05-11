<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\SecurityEvents;

enum SecurityEventStatus: string
{
    case NEW      = 'new';
    case RESOLVED = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::NEW      => 'Nuevo',
            self::RESOLVED => 'Resuelto',
        };
    }

    public static function options(): array
    {
        return array_column(self::cases(), 'label', 'value');
    }
}
