<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Seeding\Concerns\Main;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait HandlesSeederLoggingAndReporting
{
    protected array $reportData = [];
    protected float $startTime;

    /**
     * Registra métricas clave por módulo
     */
    protected function logModuleMetrics(
        string $module,
        string $status,
        ?string $file = null,
        ?int $records = null,
        ?string $details = null,
        bool $truncated = false,
        bool $isFake = false,
        ?int $fakeCount = null
    ): void {
        $this->reportData['modules'][$module] = [
            'status'    => $status,
            'file'      => $file ? basename($file) : null,
            'path'      => $file ? dirname($file) : null,
            'records'   => $records,
            'time'      => round(microtime(true) - $this->startTime, 2) . 's',
            'truncated' => $truncated,
            'is_fake'   => $isFake,
            'fake_qty' => $fakeCount,
            'details'   => $details,
        ];

        $this->consoleOutput($module, $status, $details);
    }

    /**
     * Genera reportes en múltiples formatos
     */
    protected function generateReports(): void
    {
        $this->reportData['meta'] = [
            'env'       => config('seeder.env', 'local'),
            'date'      => now()->toDateTimeString(),
            'duration'  => round(microtime(true) - $this->startTime, 2) . 's',
            'has_errors'=> collect($this->reportData['modules'])->contains('status', 'failed')
        ];

        $this->generateMarkdownReport();
        $this->generateJsonReport();
    }

    /**
     * Reporte Markdown Mejorado (Tabla + Resumen)
     */
    protected function generateMarkdownReport(): void
    {
        $env = $this->reportData['meta']['env'];
        $path = database_path("seeders/reports/{$env}/seed-report-{$env}-" . now()->format('Y-m-d') . ".md");

        $content = [
            "# 📊 Koneko ERP - Seeders Report",
            "| Metadata         | Value              |",
            "|------------------|--------------------|",
            "| **Environment**  | `{$env}`           |",
            "| **Execution**    | {$this->reportData['meta']['date']} |",
            "| **Duration**     | {$this->reportData['meta']['duration']} |",
            "| **Status**       | " . ($this->reportData['meta']['has_errors'] ? '❌ With errors' : '✅ Success') . " |",
            "",
            "## 📦 Modules Summary",
            "",
            "| Module | Status | Records | Time | Truncated | Faker | File | Details |",
            "|--------|--------|---------|------|-----------|-------|------|---------|",
        ];

        foreach ($this->reportData['modules'] as $module => $data) {
            $content[] = sprintf(
                "| %s | %s | %s | %s | %s | %s | %s | %s |",
                Str::headline($module),
                $this->formatStatus($data['status']),
                $data['records'] ?? ($data['is_fake'] ? "~{$data['fake_qty']}" : 'N/A'),
                $data['time'],
                $data['truncated'] ? '✓' : '✗',
                $data['is_fake'] ? '✓' : '✗',
                $data['file'] ? "`{$data['file']}`" : 'N/A',
                $data['details'] ?? ''
            );
        }

        // Sección de archivos usados
        $content[] = "";
        $content[] = "## 📂 Files Location";
        foreach (array_unique(array_column($this->reportData['modules'], 'path')) as $filePath) {
            if ($filePath) $content[] = "- `{$filePath}/`";
        }

        File::put($path, implode("\n", $content));
    }

    /**
     * Helpers para formato visual
     */
    private function formatStatus(string $status): string
    {
        return match(strtolower($status)) {
            'completed' => '✅',
            'failed'    => '❌',
            'skipped'   => '⏭️',
            default     => 'ℹ️'
        };
    }

    private function consoleOutput(string $module, string $status, ?string $details): void
    {
        $emoji = $this->formatStatus($status);
        $this->command?->line("  {$emoji} <fg=white>{$module}</>" . ($details ? " → <fg=yellow>{$details}</>" : ""));
    }
}
