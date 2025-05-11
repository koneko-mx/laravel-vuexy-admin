<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Contracts\Factories;

interface ProbabilisticAttributesFactoryInterface
{
    public function maybe(int $percentage, mixed $value): mixed;
    public function maybeDefault(mixed $value): mixed;
}
