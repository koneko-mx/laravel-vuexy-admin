<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Seeding\Concerns\Main;

/**
 * ⚡ Trait para soporte de procesamiento por lotes (chunks) en seeders.
 *
 * Permite:
 * - Cargar archivos grandes sin agotar memoria
 * - Delegar la lógica al parser correspondiente (si la soporta)
 * - Integrarse con la barra de progreso
 *
 * Requiere que el parser implemente `parseInChunks()`
 *
 * @package Koneko\VuexyAdmin\Support\Traits\Seeders
 */
trait HasSeederChunkSupport
{
    /**
     * Procesa un archivo usando lotes si el parser lo soporta.
     *
     * @param object $parser Parser con método `parseInChunks()`
     * @param string $path Ruta absoluta del archivo
     * @return void
     */

    protected function processInChunks(object $parser, string $path): void
    {
        $this->log("⚡ Procesando por lotes...");
        $this->startProgress();

        foreach ($parser->parseInChunks($path) as $chunk) {
            foreach ($chunk as $row) {
                $this->processSingleRow($row);
                $this->advanceProgress();
            }
        }

        $this->finishProgress();
    }

    /**
     * Procesa una sola fila dentro de un chunk.
     *
     * @param array $row
     * @return void
     */
    protected function processSingleRow(array $row): void
    {
        try {
            $sanitized = $this->sanitizeRow($row);
            $modelClass = $this->getModelClass();
            $uniqueBy = $this->getUniqueBy();

            $modelClass::updateOrCreate(
                is_array($uniqueBy)
                    ? array_intersect_key($sanitized, array_flip($uniqueBy))
                    : [$uniqueBy => $sanitized[$uniqueBy] ?? null],
                $sanitized
            );

            $this->processedCount++;
        } catch (\Exception $e) {
            $this->log("⚠️  Error en fila: " . json_encode($row) . " - " . $e->getMessage());
        }
    }
}
