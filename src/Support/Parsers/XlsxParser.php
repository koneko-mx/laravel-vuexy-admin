<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Parsers;

use Koneko\VuexyAdmin\Support\Contracts\Files\ParsableFileInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class XlsxParser implements ParsableFileInterface
{
    private array $config = [
        'header_row' => 1,      // Fila que contiene los headers (1-based)
        'data_start_row' => 2,  // Fila donde inician los datos (1-based)
        'sheet_index' => 0,     // Índice de la hoja a leer
        'max_memory' => '512MB',// Límite de memoria para cálculo
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->configureMemory();
    }

    public function parse(string $path): array
    {
        $this->validate($path);

        try {
            $spreadsheet = IOFactory::load($path);
            $worksheet = $spreadsheet->getSheet($this->config['sheet_index']);
            $headers = $this->getHeaders($worksheet);

            return $this->getData($worksheet, $headers);
        } catch (\Exception $e) {
            throw new \RuntimeException("XLSX parsing failed: " . $e->getMessage());
        }
    }

    public function validate(string $path): bool
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("XLSX file not found: {$path}");
        }

        if (!is_readable($path)) {
            throw new \RuntimeException("XLSX file is not readable: {$path}");
        }

        return true;
    }

    private function configureMemory(): void
    {
        if (isset($this->config['max_memory'])) {
            ini_set('memory_limit', $this->config['max_memory']);
        }
    }

    private function getHeaders(Worksheet $worksheet): array
    {
        $headers = [];
        $highestColumn = $worksheet->getHighestDataColumn();

        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $cellValue = $worksheet->getCell($col . $this->config['header_row'])
                ->getValue();
            $headers[] = $this->sanitizeHeader($cellValue);
        }

        return $headers;
    }

    private function sanitizeHeader(?string $value): string
    {
        return trim($value ?? '');
    }

    private function getData(Worksheet $worksheet, array $headers): array
    {
        $data = [];
        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();

        for ($row = $this->config['data_start_row']; $row <= $highestRow; $row++) {
            $rowData = [];

            $colIndex = 0;
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();
                $rowData[$headers[$colIndex] ?? $col] = $cellValue;
                $colIndex++;
            }

            // Filtramos filas completamente vacías
            if (!empty(array_filter($rowData, fn($v) => $v !== null))) {
                $data[] = $rowData;
            }
        }

        return $data;
    }
}
