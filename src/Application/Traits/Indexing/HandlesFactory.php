<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Indexing;

use Illuminate\Database\Eloquent\Factories\Factory;

trait HandlesFactory
{
    /**
     * Devuelve una instancia de la Factory relacionada con el modelo.
     * Puede ser sobrescrito para casos extendidos.
     */
     public function getIndexFactory(): ?Factory { return null; }
}
