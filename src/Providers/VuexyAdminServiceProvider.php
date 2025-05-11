<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleBootManager;
use Koneko\VuexyAdmin\Providers\Concerns\RegistersTrustedProxies;
use Koneko\VuexyAdmin\Support\Traits\Modules\KonekoModuleBoots;

class VuexyAdminServiceProvider extends ServiceProvider
{
    use KonekoModuleBoots;
    use RegistersTrustedProxies;

    public function register(): void
    {
        $this->registerKonekoModule(dirname(__DIR__));
    }

    public function boot(): void
    {
        $this->registersTrustedProxies();

        $this->registerAllKonekoModules();

        KonekoModuleBootManager::bootAll();
    }
}
