<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Seeding;

use Illuminate\Support\Facades\File;

class SeederReportBuilder
{
    protected array $modules = [];
    protected array $metadata = [];

    /**
     * Setea los metadatos globales del reporte.
     */
    public function setMetadata(array $data): static
    {
        $this->metadata = $data;
        return $this;
    }

    /**
     * Agrega información de un módulo procesado.
     */
    public function addModule(array $data): static
    {
        $this->modules[] = $data;
        return $this;
    }

    /**
     * Finaliza la metadata antes de guardar.
     */
    public function finalize(): void
    {
        $this->metadata['executed_at'] = now()->toDateTimeString();
        $this->metadata['duration'] = round(microtime(true) - LARAVEL_START, 2) . 's';
        $this->metadata['status'] = collect($this->modules)->contains(fn($mod) => ($mod['status'] ?? '') === 'failed')
            ? '❌ With errors'
            : '✅ Success';
    }

    /**
     * Guarda el reporte Markdown.
     */
    public function saveReports(): void
    {
        $this->finalize();
        $this->saveMarkdown();
        $this->saveJson();
    }

    /**
     * Genera y guarda el reporte en Markdown.
     */
    public function saveMarkdown(?string $path = null): string
    {
        $env = $this->metadata['env'] ?? 'local';
        $timestamp = now()->format('Y-m-d-H-i-s');
        $dir = database_path("seeders/reports/{$env}");

        File::ensureDirectoryExists($dir);
        $filePath = $path ?? "{$dir}/seed-report-{$env}-{$timestamp}.md";

        File::put($filePath, $this->generateMarkdown());

        return $filePath;
    }

    /**
     * Genera y guarda el reporte en JSON.
     */
    public function saveJson(?string $path = null): string
    {
        $env = $this->metadata['env'] ?? 'local';
        $timestamp = now()->format('Y-m-d-H-i-s');
        $dir = database_path("seeders/reports/{$env}");

        File::ensureDirectoryExists($dir);
        $filePath = $path ?? "{$dir}/seed-report-{$env}-{$timestamp}.json";

        File::put($filePath, json_encode([
            'metadata' => $this->metadata,
            'modules' => $this->modules,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filePath;
    }

    /**
     * Genera la tabla Markdown completa.
     */
    public function generateMarkdown(): string
    {
        $lines = [];

        $lines[] = '# 📊 Koneko ERP - Seeders Report';
        $lines[] = '';
        $lines[] = '## 🛠️ Metadata';
        $lines[] = '| Metadata        | Value                     |';
        $lines[] = '|-----------------|---------------------------|';
        $lines[] = '| **Project**     | ' . ($this->metadata['project'] ?? 'N/A') . ' |';
        $lines[] = '| **Version**     | ' . ($this->metadata['version'] ?? 'N/A') . ' |';
        $lines[] = '| **Environment** | `' . ($this->metadata['env'] ?? 'N/A') . '` |';
        $lines[] = '| **Executed at** | ' . ($this->metadata['executed_at'] ?? now()) . ' |';
        $lines[] = '| **Duration**    | ' . ($this->metadata['duration'] ?? 'N/A') . ' |';
        $lines[] = '| **Status**      | ' . ($this->metadata['status'] ?? 'N/A') . ' |';
        $lines[] = '';
        $lines[] = '## 📦 Modules Summary';
        $lines[] = '';
        $lines[] = '| Module          | Status | Records | Time   | Truncated | Faker | File        | Note |';
        $lines[] = '|-----------------|--------|---------|--------|-----------|-------|-------------|------|';

        foreach ($this->modules as $mod) {
            $lines[] = sprintf(
                '| %-15s | %-6s | %-7s | %-6s | %-9s | %-5s | %-11s | %-4s |',
                $mod['name'] ?? '-',
                $mod['status'] ?? '-',
                (string) ($mod['records'] ?? '-'),
                (string) ($mod['time'] ?? '-'),
                ($mod['truncated'] ?? false) ? '✔️' : '✖️',
                ($mod['faker'] ?? false) ? '✔️' : '✖️',
                $mod['file'] ?? 'N/A',
                $mod['note'] ?? '-'
            );
        }

        $lines[] = '';
        $lines[] = '## 📂 Archivos utilizados';
        $lines[] = '';

        $files = collect($this->modules)->pluck('file')->filter()->unique();

        foreach ($files as $file) {
            $lines[] = '- `' . $file . '`';
        }

        $lines[] = '';

        return implode("\n", $lines);
    }

    /**
     * Ruta del reporte Markdown generado (opcional en runtime).
     */
    public function getReportPathMarkdown(): string
    {
        $env = $this->metadata['env'] ?? 'local';
        return database_path("seeders/reports/{$env}/seed-report-{$env}.md");
    }

    /**
     * Obtiene los datos del reporte como array.
     */
    public function getReportData(): array
    {
        return [
            'metadata' => $this->metadata,
            'modules'  => $this->modules,
        ];
    }
}
