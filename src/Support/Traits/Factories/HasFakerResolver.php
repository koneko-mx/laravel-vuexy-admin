<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Factories;

use Faker\Generator;

trait HasFakerResolver
{
    protected function resolveFaker(): Generator
    {
        return method_exists($this, 'faker') && $this->faker instanceof Generator
            ? $this->faker
            : \Faker\Factory::create(app()->getLocale());
    }
}
