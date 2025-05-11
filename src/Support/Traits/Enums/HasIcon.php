<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Enums;

trait HasIcon
{
    public function icon(): string
    {
        return static::ICONS[$this->value] ?? 'ti ti-alert-circle';
    }

    public static function iconList(): array
    {
        return static::ICONS;
    }

    public static function iconOptions(): array
    {
        return array_combine(array_keys(static::ICONS), array_keys(static::ICONS));
    }
}
