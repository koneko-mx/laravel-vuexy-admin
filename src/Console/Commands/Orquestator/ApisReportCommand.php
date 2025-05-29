<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands\Orquestator;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Koneko\VuexyApisAndIntegrations\Application\Services\ExternalApiRegistryService;
use Illuminate\Support\Collection;

/**
 * Comando que muestra un resumen detallado de las APIs externas registradas.
 *
 * Permite filtrar, agrupar y exportar en diferentes formatos (tabla, JSON, CSV).
 */
class ApisReportCommand extends Command
{
    protected $signature = 'apis:report
                            {--format=table : Formato de salida (table, json, csv)}
                            {--only-errors : Mostrar solo APIs con errores}
                            {--group-by= : Agrupar por (module, provider, auth_type)}
                            {--provider= : Filtrar por proveedor (ej. google)}';

    protected $description = '📡 Genera un informe detallado de las APIs externas registradas en el ERP';

    public function __construct(protected ExternalApiRegistryService $apis)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $entries = $this->apis->all();

        if ($provider = $this->option('provider')) {
            $entries = $entries->where('provider', $provider);
        }

        if ($this->option('only-errors')) {
            $entries = $entries->filter(fn($api) =>
                empty($api->auth_type) || empty($api->scopes)
            );
        }

        if ($groupBy = $this->option('group-by')) {
            $entries = $entries->groupBy($groupBy)->map->values();
        }

        if ($entries->isEmpty()) {
            $this->warn("⚠️  No se encontraron APIs registradas con los filtros aplicados.");
            return;
        }

        match ($this->option('format')) {
            'json' => $this->outputJson($entries),
            'csv'  => $this->outputCsv($entries),
            default => $this->outputTable($entries)
        };
    }

    /**
     * Muestra las APIs en formato tabla.
     */
    protected function outputTable(Collection $apis): void
    {
        $table = new Table($this->output);
        $table->setHeaders(['Módulo', 'Proveedor', 'API', 'Auth', 'Scopes', 'Entorno']);

        foreach ($apis as $entry) {
            $table->addRow([
                $entry->module,
                $entry->provider->value ?? '—',
                $entry->name,
                $entry->auth_type->value ?? '❌',
                $entry->scopes ? implode(', ', $entry->scopes) : '❌',
                $entry->environment->value ?? 'default',
            ]);
        }

        $table->render();
    }

    /**
     * Muestra las APIs en formato JSON.
     */
    protected function outputJson(Collection $apis): void
    {
        $this->line(
            json_encode($apis->map(fn($api) => $api->toArray())->values(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Muestra las APIs en formato CSV.
     */
    protected function outputCsv(Collection $apis): void
    {
        $headers = ['module', 'provider', 'name', 'auth_type', 'scopes', 'environment'];
        $this->line(implode(',', $headers));

        foreach ($apis as $api) {
            $this->line(implode(',', [
                $api->module,
                $api->provider->value ?? '-',
                $api->name,
                $api->auth_type->value ?? '-',
                is_array($api->scopes) ? implode('|', $api->scopes) : '-',
                $api->environment->value ?? '-',
            ]));
        }
    }
}
