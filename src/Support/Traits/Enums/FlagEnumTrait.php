<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Enums;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use RuntimeException;

trait FlagEnumTrait
{
    abstract public static function modelClass(): string;
    abstract public static function getDescription(self $case): string;

    public static function register(): void
    {
        if (!method_exists($modelClass = static::modelClass(), 'registerFlag')) {
            throw new RuntimeException(
                "{$modelClass} debe usar el trait HasFlags para registrar flags"
            );
        }

        foreach (self::cases() as $case) {
            $modelClass::registerFlag(
                $case->value,
                static::getDescription($case),
                static::getDefaultValue($case)
            );
        }
    }

    public static function toConfiguration(): array
    {
        return array_reduce(self::cases(), function (array $carry, self $case) {
            $carry[$case->value] = [
                'description' => static::getDescription($case),
                'default' => static::getDefaultValue($case),
                'column_definition' => $case->getColumnDefinition(),
                'migration_ready' => true
            ];
            return $carry;
        }, []);
    }

    public static function getDefaultValue(self $case): bool
    {
        return false;
    }

    public function getColumnDefinition(): string
    {
        $driver = config('database.default');
        $version = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);

        // Versión genérica que funciona en la mayoría de casos
        return sprintf(
            "TINYINT(1) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(flags, '$.%s'))) %s",
            $this->value,
            Str::contains($version, 'MariaDB') ? 'PERSISTENT' : 'STORED'
        );
    }
}
