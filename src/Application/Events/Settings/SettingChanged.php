<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Events\Settings;

use Illuminate\Foundation\Events\Dispatchable;

class SettingChanged
{
    use Dispatchable;

    /**
     * @param string $key       Clave completa del setting (ej. 'koneko.admin.site.logo')
     * @param string $namespace Namespace base (ej. 'koneko.admin.site.')
     * @param int|null $userId  Usuario relacionado si es setting scoped, null si es global
     */
    public function __construct(public string $key) {}
}
