<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands\Orquestator;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Attribute\AsCommand;


class KonekoModuleInstallCommand extends Command
{
    protected $description = 'Instala módulos desde Composer';

    public function handle()
    {
        $package = $this->argument('package');

        $this->info("Instalando paquete: {$package}...");

        $process = new Process(['composer', 'require', $package]);
        $process->setTimeout(300);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            return $this->error('La instalación falló.');
        }

        $this->call('migrate');
        $this->call('vuexy:menu:build');

        $this->info("Módulo instalado con éxito: {$package}");
    }
}
