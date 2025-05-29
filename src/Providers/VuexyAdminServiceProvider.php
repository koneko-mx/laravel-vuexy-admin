<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Koneko\VuexyAdmin\Application\Bootstrap\Manager\KonekoModuleBootManager;
use Koneko\VuexyAdmin\Providers\Concerns\{EnforcesHttps, RegistersTrustedProxies};
use Koneko\VuexyAdmin\Support\Traits\Modules\KonekoModuleBoots;

class VuexyAdminServiceProvider extends ServiceProvider
{
    use KonekoModuleBoots;
    use EnforcesHttps,
        RegistersTrustedProxies;

    public function register(): void
    {
        $this->registerKonekoModule(dirname(__DIR__));
    }

    public function boot(): void
    {
        $this->enforceHttps();
        $this->registerTrustedProxies();

        $this->registerAllKonekoModules();

        KonekoModuleBootManager::bootAll();
    }
}
