<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Flags;

use Illuminate\Database\Schema\Blueprint;

trait HandlesFlagMigrations
{
    protected static array $flagExtensions = [];

    public static function registerFlagMigration(callable $callback): void
    {
        static::$flagExtensions[] = $callback;
    }

    public static function applyFlagExtensions(Blueprint $table): void
    {
        foreach (static::$flagExtensions as $callback) {
            $callback($table);
        }
    }
}
