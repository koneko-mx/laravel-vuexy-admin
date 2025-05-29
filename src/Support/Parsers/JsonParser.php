<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Parsers;

use Koneko\VuexyAdmin\Support\Contracts\Files\ParsableFileInterface;

class JsonParser implements ParsableFileInterface
{
    private array $config = [
        'associative' => true,
        'depth' => 512,
        'flags' => JSON_THROW_ON_ERROR,
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function parse(string $path): array
    {
        $this->validate($path);

        try {
            $jsonString = file_get_contents($path);
            $data = json_decode($jsonString, $this->config['associative'], $this->config['depth'], $this->config['flags']);

            if (!is_array($data)) {
                throw new \RuntimeException("JSON file must contain an array of objects");
            }

            return $data;
        } catch (\JsonException $e) {
            throw new \RuntimeException("JSON parsing failed: " . $e->getMessage());
        }
    }

    public function validate(string $path): bool
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("JSON file not found: {$path}");
        }

        if (!is_readable($path)) {
            throw new \RuntimeException("JSON file is not readable: {$path}");
        }

        // Validación básica de contenido JSON
        $jsonString = file_get_contents($path);
        if (!json_validate($jsonString)) {
            throw new \RuntimeException("Invalid JSON format in file: {$path}");
        }

        return true;
    }
}
