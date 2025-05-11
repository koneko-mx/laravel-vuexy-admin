<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Enums;

trait HasLabelAndIcon
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function icons(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->icon()])
            ->toArray();
    }

    public static function options(): array
    {
        return static::labels();
    }

    public static function validationRules(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'in:' . implode(',', static::values()),
        ];
    }
}
