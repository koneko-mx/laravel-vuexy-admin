<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Validation;

use Illuminate\Validation\Rule;
use Koneko\VuexyAdmin\Support\Validation\NotEmptyHtml;

class Rules
{
    public static function notEmptyHtml(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            new NotEmptyHtml(),
        ];
    }

    public static function existsUser(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'exists:users,id',
        ];
    }

    public static function erpCode(
        string $table,
        mixed $ignoreId = null,
        string $column = 'code',
        bool $required = true,
        int $min = 3,
        int $max = 16
    ): array {
        return array_filter([
            $required ? 'required' : 'nullable',
            'string',
            'alpha_dash',
            "min:$min",
            "max:$max",
            Rule::unique($table, $column)->ignore($ignoreId),
        ]);
    }

    public static function phoneMx(bool $required = false, int $max = 15): array
    {
        return [
            $required ? 'required' : 'nullable',
            'regex:/^(\+52)?[\s\-()]*(\d[\s\-()]*){10}$/',
            'max:' . $max,
        ];
    }

    public static function phoneIntl(bool $required = false, int $max = 20): array
    {
        return [
            $required ? 'required' : 'nullable',
            'regex:/^\+(?:[0-9\s\-().]){10,}$/',
            'max:' . $max,
        ];
    }

    public static function phoneMixed(bool $required = false, int $max = 20): array
    {
        return [
            $required ? 'required' : 'nullable',
            'regex:/^(\+[\d\s\-().]{10,}|[\d\s\-().]{10,})$/',
            'max:' . $max,
        ];
    }

    public static function latitude(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'numeric',
            'between:-90,90',
        ];
    }

    public static function longitude(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'numeric',
            'between:-180,180',
        ];
    }

}
