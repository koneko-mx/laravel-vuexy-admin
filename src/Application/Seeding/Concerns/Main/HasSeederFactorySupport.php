<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Seeding\Concerns\Main;

/**
 * Trait para generación de registros faker vía Factory con fallback.
 */
trait HasSeederFactorySupport
{
    public function runFake(int $total, array $config = []): void
    {
        // Este método se define completo en el seeder concreto.
        throw new \LogicException('El método runFake debe ser implementado por el Seeder que use este trait.');
    }
}
