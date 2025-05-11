<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Helpers;

trait HasLabeledEnumHelpers
{
    public static function options(): array
    {
        return array_column(self::cases(), null, 'value');
    }

    public static function labels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn(self $case) => $case->label(), self::cases())
        );
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

    public static function validationRules(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'in:' . implode(',', static::values()),
        ];
    }
}
