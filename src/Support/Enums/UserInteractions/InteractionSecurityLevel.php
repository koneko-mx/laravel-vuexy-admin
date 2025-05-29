<?php

namespace Koneko\VuexyAdmin\Support\Enums\UserInteractions;

enum InteractionSecurityLevel: string
{
    case Normal   = 'normal';
    case Sensible = 'sensible';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Normal   => 'Normal',
            self::Sensible => 'Sensible',
            self::Critical => 'Crítico',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Normal   => 'badge bg-light text-dark',
            self::Sensible => 'badge bg-warning text-dark',
            self::Critical => 'badge bg-danger text-white',
        };
    }
}
