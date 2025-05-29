<?php

namespace Koneko\VuexyAdmin\Application\Cache\Manager\Concerns;

use Koneko\VuexyAdmin\Application\Traits\System\Context\HasBaseContext;
use Koneko\VuexyAdmin\Application\Traits\System\Context\HasCacheContextValidation;

trait ___HasCacheContext
{
    use HasBaseContext;
    use HasCacheContextValidation;

    // ======================= HELPERS =========================

    public function reset(): static
    {


        return $this;
    }
}
