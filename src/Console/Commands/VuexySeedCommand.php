<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Application\Seeding\SeederOrchestrator;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'vuexy:seed')]
class VuexySeedCommand extends Command
{
    protected $signature = 'vuexy:seed
        {--list : Mostrar lista de módulos disponibles}
        {--module=* : Módulos específicos a ejecutar (separados por comas)}
        {--class= : Ejecutar un seeder específico por su clase}
        {--file= : Sobrescribir archivo de datos}
        {--fake : Ejecutar en modo faker}
        {--fake-count= : Cantidad específica de registros falsos a generar}
        {--truncate : Vaciar tablas antes de sembrar}
        {--env= : Entorno de ejecución (local, demo, production)}
        {--report : Generar reporte detallado}
        {--dry-run : Simular sin ejecutar cambios}
        {--chunk-size= : Tamaño de chunk para procesamiento}';

    protected $description = 'Orquestador avanzado de seeders para Koneko Vuexy ERP';

    public function handle(SeederOrchestrator $orchestrator): int
    {
        $this->displayWelcomeMessage();

        $orchestrator->setCommand($this);

        if ($this->option('list')) {
            return $this->listAvailableModules();
        }

        if ($this->option('class')) {
            return $this->runSpecificSeeder($this->option('class'));
        }

        return $this->runFromModules($orchestrator);
    }

    protected function displayWelcomeMessage(): void
    {
        $this->newLine();
        $this->line('🌱 <fg=magenta;options=bold>Koneko Vuexy ERP Seeder Orchestrator</>');
        $this->line('📦 Versión: <info>1.0</info> | Entorno: <info>'.($this->option('env') ?: 'auto').'</info>');
        $this->newLine();
    }

    protected function listAvailableModules(): int
    {
        $modules = config('seeder.modules', []);

        $this->table(
            ['Módulo', 'Seeder', 'Archivo', 'Faker', 'Descripción'],
            collect($modules)->map(function ($config, $key) {
                return [
                    '<info>'.$key.'</info>',
                    $config['seeder'] ?? '-',
                    $config['file'] ?? 'Por defecto',
                    isset($config['fake']) ? '✅' : '❌',
                    $this->getModuleDescription($key)
                ];
            })->toArray()
        );

        return self::SUCCESS;
    }

    protected function runSpecificSeeder(string $fqcn): int
    {
        $seederClass = $this->qualifySeederClass($fqcn);

        if (!class_exists($seederClass)) {
            $this->error("❌ La clase especificada no existe: {$seederClass}");
            return self::FAILURE;
        }

        $seeder = app($seederClass);

        if (method_exists($seeder, 'setCommand')) {
            $seeder->setCommand($this);
        }

        if ($this->option('truncate') && method_exists($seeder, 'truncate')) {
            $this->info('🗑️ Truncando tabla...');
            $seeder->truncate();
        }

        if ($this->option('dry-run')) {
            $this->info("🔹 [DRY RUN] Simulación para: <comment>{$seederClass}</comment>");
            if ($this->option('file')) {
                $this->line("📄 Archivo: {$this->option('file')}");
            }
            if ($this->option('fake') || $this->option('fake-count')) {
                $count = $this->option('fake-count') ?: 'aleatorio';
                $this->line("👤 Faker: {$count} registros");
            }
            return self::SUCCESS;
        }

        $config = [
            'file' => $this->option('file'),
            'fake' => $this->option('fake') || $this->option('fake-count'),
            'fake-count' => (int) $this->option('fake-count'),
            'truncate' => $this->option('truncate'),
            'dry-run' => $this->option('dry-run'),
        ];

        if ($this->option('file') && method_exists($seeder, 'setTargetFile')) {
            $seeder->setTargetFile($this->option('file'));
        }

        $this->info("📦 Ejecutando seeder: <comment>{$seederClass}</comment>");

        if ($config['fake'] && method_exists($seeder, 'runFake')) {
            $count = $config['fake-count'] ?: rand(1, 10);
            $seeder->runFake($count);
            $this->info("✅ Faker: {$count} registros generados");
        } else {
            $seeder->run($config);
            $this->info("✅ Seeder ejecutado correctamente");
        }

        return self::SUCCESS;
    }

    protected function runFromModules(SeederOrchestrator $orchestrator): int
    {
        $modules = $this->option('module')
            ? explode(',', implode(',', $this->option('module')))
            : null;

        $options = [
            'env' => $this->option('env'),
            'file' => $this->option('file'),
            'fake' => $this->option('fake') || $this->option('fake-count'),
            'fake-count' => (int) $this->option('fake-count'),
            'truncate' => $this->option('truncate'),
            'dry-run' => $this->option('dry-run'),
        ];

        try {
            $orchestrator->run($modules, $options);

            if ($this->option('report')) {
                $this->newLine(2);
                $this->line('📊 <options=underscore>Reporte generado en:</> ' . $orchestrator->getReportPath());
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Error durante ejecución: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    protected function qualifySeederClass(string $class): string
    {
        if (str_starts_with($class, '\\')) {
            return ltrim($class, '\\');
        }

        if (str_contains($class, '\\')) {
            return $class;
        }

        $namespaces = [
            'Koneko\\VuexyAdmin\\Database\\Seeders\\',
            'Koneko\\VuexyPos\\Database\\Seeders\\',
            'Koneko\\VuexySatCatalogs\\Database\\Seeders\\',
            'Database\\Seeders\\',
        ];

        foreach ($namespaces as $namespace) {
            $fqcn = $namespace . $class;
            if (class_exists($fqcn)) return $fqcn;
        }

        return $class;
    }

    protected function getModuleDescription(string $module): string
    {
        return match ($module) {
            'settings' => 'Configuración global del sistema',
            'users' => 'Usuarios y permisos',
            'sat_catalogs' => 'Catálogos del SAT (México)',
            default => '--'
        };
    }
}
