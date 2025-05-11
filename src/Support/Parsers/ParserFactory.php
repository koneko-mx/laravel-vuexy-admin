<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Parsers;

use Koneko\VuexyAdmin\Application\Contracts\Files\ParsableFileInterface;

class ParserFactory
{
    public static function make(string $type, array $config = []): ParsableFileInterface
    {
        return match(strtolower($type)) {
            'csv' => new CsvParser($config),
            'json' => new JsonParser($config),
            'xlsx' => new XlsxParser($config), // Implementar después
            default => throw new \RuntimeException("Unsupported parser type: {$type}")
        };
    }

    public static function makeFromPath(string $path, array $config = []): ParsableFileInterface
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return self::make($extension, $config);
    }
}
