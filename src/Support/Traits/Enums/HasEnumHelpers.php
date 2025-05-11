<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Enums;

trait HasEnumHelpers
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }

    public static function fromOrFail(string $value): self
    {
        if (!self::isValid($value)) {
            throw new \InvalidArgumentException("El valor '{$value}' no es válido para " . static::class);
        }
        return self::from($value);
    }
}
