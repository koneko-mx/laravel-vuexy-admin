<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Parsers;

use Koneko\VuexyAdmin\Application\Contracts\Files\ParsableFileInterface;
use League\Csv\{CharsetConverter,Reader,Statement};

class CsvParser implements ParsableFileInterface
{
    private array $config = [
        'delimiter' => ',',
        'enclosure' => '"',
        'escape' => '\\',
        'header_offset' => 0,
        'encoding' => 'UTF-8',
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function parse(string $path): array
    {
        $this->validate($path);

        try {
            $stream = fopen($path, 'r');
            if ($stream === false) {
                throw new \RuntimeException("Could not open file: {$path}");
            }

            $csv = Reader::createFromStream($stream);
            $csv->setDelimiter($this->config['delimiter']);
            $csv->setEnclosure($this->config['enclosure']);
            $csv->setEscape($this->config['escape']);

            // Manejo de codificación
            if ($this->config['encoding'] !== 'UTF-8') {
                CharsetConverter::addTo($csv, $this->config['encoding'], 'UTF-8');
            }

            // Obtener headers
            $csv->setHeaderOffset($this->config['header_offset']);
            $headers = $csv->getHeader();

            // Procesar registros
            $stmt = Statement::create();
            $records = $stmt->process($csv);

            $result = [];
            foreach ($records as $record) {
                $result[] = array_combine($headers, array_values($record));
            }

            fclose($stream);

            return $result;
        } catch (\Exception $e) {
            throw new \RuntimeException("CSV parsing failed: " . $e->getMessage());
        }
    }

    public function validate(string $path): bool
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("CSV file not found: {$path}");
        }

        if (!is_readable($path)) {
            throw new \RuntimeException("CSV file is not readable: {$path}");
        }

        return true;
    }
}
