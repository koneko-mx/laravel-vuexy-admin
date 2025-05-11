<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Seeders\Main;

trait SanitizeRowWithFillableAndCasts
{
    protected function sanitizeRowWithFillableAndCasts(array $row, string $modelClass): array
    {
        $model = new $modelClass;

        $fillable = $model->getFillable();
        $casts    = $model->getCasts();

        $sanitized = [];

        foreach ($row as $key => $value) {
            if (!in_array($key, $fillable)) {
                continue; // Solo campos fillable
            }

            //  Aplicar cast manualmente si es necesario
            if (isset($casts[$key])) {
                $castType = $casts[$key];

                $value = match ($castType) {
                    'boolean', 'bool'   => (bool) $value,
                    'int', 'integer'    => (int) $value,
                    'float', 'double'   => (float) $value,
                    'datetime', 'date'  => is_a($value, \DateTimeInterface::class) ? $value->format('Y-m-d H:i:s') : $value,
                    'array', 'json'     => is_string($value) ? json_decode($value, true) : $value,
                    default             => $value,
                };
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

}
