<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Contracts\ApiRegistry;

use Illuminate\Support\Collection;
use Koneko\VuexyAdmin\Models\ExternalApi;

/**
 * Contrato para servicios de registro de APIs externas.
 */
interface ExternalApiRegistryInterface
{
    public function all(): Collection;

    public function active(): Collection;

    public function groupByProvider(): Collection;

    public function groupByModule(): Collection;

    public function forModule(string $module): Collection;

    public function forProvider(string $provider): Collection;

    public function summary(): array;

    public function slugifyName(string $name): string;

    public function find(string $slug): ?ExternalApi;
}