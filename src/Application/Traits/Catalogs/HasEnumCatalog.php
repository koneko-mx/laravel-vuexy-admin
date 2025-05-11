<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Catalogs;

trait HasEnumCatalog
{
    public function getEnumCatalog(string $enumClass, array $options = []): array
    {
        return collect($enumClass::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
