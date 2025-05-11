<?php

declare(strict_types=1);

namespace Koneko\VuexyApisAndIntegrations\Application\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Koneko\VuexyApisAndIntegrations\Models\ExternalApi;

/**
 * Servicio de consulta y agrupación de APIs externas del sistema.
 *
 * Este servicio centraliza el acceso a `ExternalApi`, útil para
 * dashboards, Livewire, comandos Artisan o UI administrativas.
 */
/**
 * Servicio principal para consulta y agrupación de APIs.
 */
class ExternalApiRegistryService implements ExternalApiRegistryInterface
{
    public function all(): Collection
    {
        return ExternalApi::all();
    }

    public function active(): Collection
    {
        return ExternalApi::where('is_active', true)->get();
    }

    public function groupByProvider(): Collection
    {
        return $this->all()->groupBy('provider');
    }

    public function groupByModule(): Collection
    {
        return $this->all()->groupBy('module');
    }

    public function forModule(string $module): Collection
    {
        return ExternalApi::where('module', $module)->get();
    }

    public function forProvider(string $provider): Collection
    {
        return ExternalApi::where('provider', $provider)->get();
    }

    public function summary(): array
    {
        return [
            'total'          => ExternalApi::count(),
            'active'         => ExternalApi::where('is_active', true)->count(),
            'inactive'       => ExternalApi::where('is_active', false)->count(),
            'by_provider'    => ExternalApi::select('provider')->distinct()->pluck('provider')->toArray(),
            'by_environment' => ExternalApi::select('environment')->distinct()->pluck('environment')->toArray(),
        ];
    }

    public function slugifyName(string $name): string
    {
        return Str::slug($name);
    }

    public function find(string $slug): ?ExternalApi
    {
        return ExternalApi::where('slug', $slug)->first();
    }
}