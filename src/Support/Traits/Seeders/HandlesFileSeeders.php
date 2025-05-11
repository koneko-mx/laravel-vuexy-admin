<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Seeders;

use Illuminate\Support\Facades\File;
use Koneko\VuexyAdmin\Support\Parsers\ParserFactory;
use Throwable;

/**
 * 📂 Trait que encapsula la lógica de entrada de archivos en seeders.
 *
 * Permite:
 * - Resolver archivo desde múltiples rutas estándar
 * - Capturar el parámetro `--file` desde CLI
 * - Almacenar ruta seleccionada en `$targetFile`
 *
 * Ideal para seeders reutilizables que aceptan CSV o JSON.
 *
 * @package Koneko\VuexyAdmin\Support\Traits\Seeders
 */
trait HandlesFileSeeders
{
    /**
     * Punto de entrada principal para Artisan o SeederOrchestrator.
     *
     * @param array $options Parámetros de ejecución
     * @return void
     */
    public function run(array $options = []): void
    {
        $this->captureCommandLineArguments();
        $this->beforeRun($options);

        try {
            $fileToUse = $this->resolveFileToUse($options);

            if ($fileToUse) {
                $this->runFromFile($this->resolveFilePath($fileToUse), $options);

                return;
            }

            $this->log("❌ Error: No se proporcionó archivo para el seeder");

        } catch (Throwable $e) {
            $this->log("❌ Error inesperado: {$e->getMessage()}");
            throw $e;

        } finally {
            $this->afterRun($options);
        }
    }

    /**
     * Ejecuta el seeder desde un archivo estructurado.
     *
     * @param string $resolvedPath Ruta absoluta al archivo
     * @param array $options Opciones adicionales
     * @return void
     */
    protected function runFromFile(string $resolvedPath, array $options = []): void
    {
        $this->validateFile($resolvedPath);

        $parser = ParserFactory::makeFromPath($resolvedPath, $options);

        if (method_exists($parser, 'parseInChunks')) {
            $this->processInChunks($parser, $resolvedPath);

        } else {
            $rows = $parser->parse($resolvedPath);

            if (empty($rows)) {
                $this->log("⚠️ Archivo vacío: {$resolvedPath}");
                return;
            }

            $this->processRows($rows);
        }

        $this->log(" {$this->processedCount} registros procesados desde archivo");
    }

    /**
     * Captura el parámetro `--file` desde Artisan CLI.
     *
     * @return void
     */
    protected function captureCommandLineArguments(): void
    {
        global $argv;

        if (PHP_SAPI === 'cli' && isset($argv)) {
            foreach ($argv as $index => $arg) {
                if (str_starts_with($arg, '--file=')) {
                    $this->targetFile = substr($arg, 7);
                } elseif ($arg === '--file' && isset($argv[$index + 1]) && !str_starts_with($argv[$index + 1], '--')) {
                    $this->targetFile = $argv[$index + 1];
                }
            }
        }
    }

    /**
     * Determina la ruta de archivo a usar en el seeder.
     *
     * Prioriza:
     * - Línea de comandos
     * - `$options['file']`
     * - `$this->targetFile`
     *
     * @param array $options
     * @return string|null
     */
    protected function resolveFileToUse(array $options): null|string
    {
        return $this->targetFile ?? ($options['file'] ?? null);
    }

    /**
     * Resuelve la ruta física de un archivo a partir de ruta relativa.
     *
     * @param string $relativePath
     * @return string
     */
    protected function resolveFilePath(string $relativePath): string
    {
        // Si ya es un path absoluto y existe, lo regresamos directo
        if (File::exists($relativePath)) return $relativePath;

        $paths = [
            database_path("data/{$relativePath}"),
            database_path("seeders/data/{$relativePath}"),
            database_path("seeders/files/{$relativePath}"),
            storage_path("seeders/{$relativePath}"),
            base_path($relativePath),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) return $path;
        }

        throw new \RuntimeException("Archivo no encontrado: {$relativePath}");
    }

    /**
     * Valida que el archivo exista y no esté vacío.
     *
     * @param string $path
     * @return void
     *
     * @throws \RuntimeException
     */
    protected function validateFile(string $path): void
    {
        if (!file_exists($path) || !is_readable($path) || filesize($path) === 0) {
            throw new \RuntimeException("Archivo inválido o vacío: {$path}");
        }
    }
}

