<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Contracts\Catalogs;

interface CatalogServiceInterface
{
    public function catalogs(): array;
    public function exists(string $catalog): bool;
    public function getCatalog(string $catalog, string $searchTerm = '', array $options = []): array;
    public function getCatalogMeta(string $catalog): array;
}
