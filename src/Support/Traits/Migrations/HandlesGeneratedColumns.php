<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Migrations;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait HandlesGeneratedColumns
{
    /**
     * Añade columnas generadas usando la sintaxis comprobada
     */
    protected function addGeneratedColumns(string $tableName, array $columns, string $jsonColumn = 'flags'): void
    {
        $alterStatements = [];
        $indexStatements = [];

        foreach ($columns as $column) {
            $columnName = $column->value;

            if (!Schema::hasColumn($tableName, $columnName)) {
                // Sintaxis idéntica a tu versión que funcionaba
                $alterStatements[] = sprintf(
                    "ADD COLUMN %s TINYINT(1) GENERATED ALWAYS AS (CAST(JSON_UNQUOTE(JSON_EXTRACT(%s, '$.%s')) AS UNSIGNED)) PERSISTENT AFTER %s",
                    $columnName,
                    $jsonColumn,
                    $columnName,
                    $jsonColumn
                );

                $indexStatements[] = "ADD INDEX idx_{$columnName} ({$columnName})";
            }
        }

        if (!empty($alterStatements)) {
            $this->executeAlterTable($tableName, array_merge($alterStatements, $indexStatements));
        }
    }

    /**
     * Elimina columnas generadas
     */
    protected function dropGeneratedColumns(string $tableName, array $columns): void
    {
        $dropStatements = [];

        foreach ($columns as $column) {
            $columnName = $column->value;

            if (Schema::hasColumn($tableName, $columnName)) {
                $dropStatements[] = "DROP COLUMN {$columnName}";
                $dropStatements[] = "DROP INDEX idx_{$columnName}";
            }
        }

        if (!empty($dropStatements)) {
            $this->executeAlterTable($tableName, $dropStatements);
        }
    }

    /**
     * Ejecuta ALTER TABLE con manejo seguro de errores
     */
    protected function executeAlterTable(string $tableName, array $statements): void
    {
        $sql = "ALTER TABLE {$tableName} " . implode(", ", $statements);

        try {
            DB::statement($sql);
        } catch (\Exception $e) {
            // Log detallado para diagnóstico
            logger()->error("Error en ALTER TABLE: " . $e->getMessage(), [
                'sql' => $sql,
                'table' => $tableName
            ]);
            throw $e;
        }
    }
}
