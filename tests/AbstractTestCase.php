<?php

namespace Koneko\VuexyAdmin\Tests;

use Livewire\LivewireServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Testbench;

abstract class AbstractTestCase extends Testbench
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [
            \Koneko\VuexyAdmin\Providers\VuexyAdminServiceProvider::class,
            LivewireServiceProvider::class, // 👈 agrega esto
            // otros providers necesarios...
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // Si tienes factories en PSR-4, esto se puede omitir
        // $this->withFactories(__DIR__.'/../database/factories');
    }
}
