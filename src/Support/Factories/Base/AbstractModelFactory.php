<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Factories\Base;

use Illuminate\Database\Eloquent\Factories\Factory;
use Koneko\VuexyAdmin\Support\Traits\Factories\HasFactorySupport;

/**
 * 🏭 AbstractModelFactory
 *
 * Clase base minimalista para todas las Model Factories del ecosistema Koneko Vuexy ERP.
 *
 * - Inyecta automáticamente la instancia de `Faker`
 * - No incluye lógica adicional por defecto
 * - Requiere que los factories concretos compongan Traits según su necesidad
 *
 * Trait de extensión:
 * - `HasDynamicFactoryExtenders`: `getDefinitionXyz()` collector
 *
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @package Koneko\VuexyAdmin\Support\Factories
 */
abstract class AbstractModelFactory extends Factory
{
    use HasFactorySupport;
}
