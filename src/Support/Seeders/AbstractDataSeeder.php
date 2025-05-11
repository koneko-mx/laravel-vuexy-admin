<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Seeders;

use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Koneko\VuexyAdmin\Application\Traits\Seeders\Main\{HasSeederLogger,HasSeederProgressBar,HasSeederChunkSupport,SanitizeRowWithFillableAndCasts};
use Throwable;

/**
 * 🌱 Seeder base para CSV/JSON con compatibilidad avanzada para CLI y procesamiento modular.
 *
 * Esta clase proporciona:
 * - Procesamiento directo desde archivos estructurados (CSV, JSON, NDJSON)
 * - Sanitización automática de filas mediante fillables y casts
 * - Progreso visual con Artisan
 * - Resolución automática de paths
 * - Extensibilidad mediante Traits especializados
 *
 * ✅ Ideal para:
 * - Seeders simples de catálogo
 * - Seeders complejos con relaciones y validaciones
 *
 * Opcionalmente puedes extender:
 * - `beforeRun(array $options)`
 * - `afterRun(array $options)`
 * - `sanitizeRow(array $row)`
 *
 * @package Koneko\VuexyAdmin\Support\Seeders
 * @author arturo@koneko.mx
 */
abstract class AbstractDataSeeder extends Seeder
{
    use HasSeederChunkSupport,
        HasSeederLogger,
        HasSeederProgressBar,
        SanitizeRowWithFillableAndCasts;

    /**
     * Entorno actual de ejecución del seeder (e.g., local, demo).
     *
     * @var string
     */
    protected string $seederEnv = 'local';

    /**
     * Total de registros procesados exitosamente.
     *
     * @var int
     */
    protected int $processedCount = 0;

    /**
     * Total de registros generados por Faker.
     *
     * @var int
     */
    protected int $fakeCreatedCount = 0;

    /**
     * Datos originales cargados desde el archivo.
     *
     * @var array
     */
    protected array $originalData = [];

    /**
     * Silencia salida de consola (útil para pruebas).
     *
     * @var bool
     */
    protected bool $quietMode = false;

    /**
     * Clase del modelo Eloquent que se está procesando.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected string $model;

    /**
     * Campo único o combinación de campos usados en updateOrCreate.
     *
     * @var string|array
     */
    protected string|array $uniqueBy;

    /**
     * Ruta del archivo de datos.
     *
     * @var string|null
     */
    protected string $targetFile = '';

    /**
     * Constructor base.
     *
     * @param string|null $file Archivo objetivo opcional
     */
    public function __construct(?string $file = null)
    {
        // Sobrescribe targetFile si se proporciona un archivo
        $this->targetFile = $file ?? $this->targetFile;
    }

    public function runFake(int $count, array $options = []): void
    {
        if (!method_exists($this->model, 'factory')) {
            throw new \RuntimeException("El modelo {$this->model} no soporta factories");
        }

        $this->log(" 👤 Generando $count registros con Factory personalizada...");

        $factory = $this->runFakeInstance()->count($count);
        $created = $factory->create();

        $this->fakeCreatedCount = is_countable($created) ? $created->count() : $count;

        $this->log(" Faker finalizado: {$this->fakeCreatedCount} registros generados");
    }

    protected function runFakeInstance(): mixed
    {
        return $this->model::factory();
    }

    /**
     * Procesa múltiples filas de datos con sanitización automática.
     *
     * @param array $rows
     * @return void
     */
    protected function processRows(array $rows): void
    {
        $this->originalData = $rows;
        $this->startProgress(count($rows));

        foreach ($rows as $row) {
            $this->processSingleRowWithSanitization($row);
            $this->advanceProgress();
        }

        $this->finishProgress();
    }

    /**
     * Procesa una fila de datos y realiza updateOrCreate.
     *
     * @param array $row
     * @return void
     */
    protected function processSingleRowWithSanitization(array $row): void
    {
        try {
            // aplicar sanitizeRow personalizado
            $row = $this->sanitizeRow($row);

            // sanitizar contra fillables + casts
            $sanitized  = $this->sanitizeRowWithFillableAndCasts($row, $this->model);
            $modelClass = $this->getModel();

            $modelClass::updateOrCreate(
                is_array($this->uniqueBy)
                    ? array_intersect_key($sanitized, array_flip($this->uniqueBy))
                    : [$this->uniqueBy => $sanitized[$this->uniqueBy] ?? null],
                $sanitized
            );

            $this->processedCount++;

        } catch (Throwable $e) {
            $this->log("⚠️ Error al procesar fila: " . json_encode($row) . " → {$e->getMessage()}");
        }
    }

    /**
     * Obtiene la clase del modelo Eloquent.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected function getModel(): string
    {
        return $this->model;
    }

    /**
     * Permite sanitización personalizada por fila (opcional en subclases).
     *
     * @param array $row
     * @return array
     */
    protected function sanitizeRow(array $row): array
    {
        return $row;
    }

    /**
     * Hook opcional: se ejecuta antes de comenzar la carga.
     *
     * @param array $options
     * @return void
     */
    protected function beforeRun(array $options): void {}

    /**
     * Hook opcional: se ejecuta después de finalizar la carga.
     *
     * @param array $options
     * @return void
     */
    protected function afterRun(array $options): void {}

    /**
     * Asocia el seeder con un comando Artisan (para logs y barra de progreso).
     *
     * @param Command $command
     * @return $this
     */
    public function setCommand(Command $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Obtiene el total de registros generados por Faker.
     *
     * @return int
     */
    public function getProcessedCount(): int
    {
        return $this->fakeCreatedCount;
    }

    /**
     * Define el entorno actual del seeder (`local`, `demo`, etc.).
     *
     * @param string $env
     * @return $this
     */
    public function setSeederEnv(string $env): self
    {
        $this->seederEnv = $env;

        return $this;
    }

    /**
     * Elimina todos los registros de la tabla destino.
     *
     * @return void
     */
    public function truncate(): void
    {
        $modelClass = $this->getModel();
        $model = new $modelClass();

        $modelClass::query()->delete(); // elimina filas sin romper FK

        $table = $model->getTable();

        $connection = $model->getConnection();
        $connection->statement("ALTER TABLE `$table` AUTO_INCREMENT = 1");

        $this->log("🗑️ Registros eliminados (sin truncar tabla por FK)");
    }

    public function setTargetFile(string $file): self
    {
        $this->targetFile = $file;
        return $this;
    }
}
