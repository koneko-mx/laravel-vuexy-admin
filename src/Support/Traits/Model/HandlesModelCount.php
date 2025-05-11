<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Model;

trait HandlesModelCount
{
    protected function countModel(string $modelClass): int
    {
        return $modelClass::count();
    }
}
