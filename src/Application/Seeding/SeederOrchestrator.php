<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Seeding;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Koneko\VuexyAdmin\Application\Seeding\SeederReportBuilder;
use Koneko\VuexyAdmin\Application\Traits\Seeders\Main\HasSeederLogger;
use Koneko\VuexyAdmin\Application\UI\Avatar\AvatarImageService;
use Koneko\VuexyAdmin\Application\UI\Avatar\AvatarInitialsService;
use Koneko\VuexyAdmin\Support\Seeders\AbstractDataSeeder;

class SeederOrchestrator
{
    use HasSeederLogger;

    protected ?Command $command = null;
    protected SeederReportBuilder $reporter;

    public function __construct(?Command $command = null)
    {
        $this->command = $command;
        $this->reporter = new SeederReportBuilder();
    }

    public function run(array|string|null $only = null, array $options = []): void
    {
        // Configuración del entorno
        if (isset($options['env'])) {
            config(['seeder.env' => $options['env']]);
        }

        // Determinar si se debe generar reporte
        $shouldGenerateReport = $options['report'] ?? config('seeder.defaults.report', false);

        // Ejecutar limpieza si está activada
        $this->clearAssetsIfNeeded($options);

        // Filtrar módulos y resolver dependencias
        $modules = $this->filterModules(config('seeder.modules', []), $only);
        $modules = $this->resolveDependencies($modules);

        foreach ($modules as $key => $config) {
            $this->log("Iniciando módulo: <info>{$key}</info>");

            $mergedConfig = array_merge(config('seeder.defaults', []), $config, $options);
            $this->processModule($key, $mergedConfig);
        }

        // Guardar reporte solo si está habilitado
        if ($shouldGenerateReport) {
            $this->reporter->saveReports();
        }
    }

    protected function processModule(string $key, array $config): void
    {
        try {
            $seederClass = $config['seeder'];
            $seeder = app($seederClass);

            // Inyectar comando si es posible
            if (method_exists($seeder, 'setCommand')) {
                $seeder->setCommand($this->command);

            } elseif (property_exists($seeder, 'command')) {
                $seeder->command = $this->command;
            }

            // Configuración general para seeders avanzados
            if ($seeder instanceof AbstractDataSeeder) {
                if ($this->command) {
                    $seeder->setCommand($this->command);
                }

                if (isset($config['file'])) {
                    $seeder->setTargetFile($config['file']);
                }

                if ($config['truncate'] ?? false) {
                    $this->log("🗑️ Truncando tabla para {$key}...");
                    $seeder->truncate();
                }
            }

            // Modo simulación
            if ($config['dry-run'] ?? false) {
                $this->logDryRun($key, $config);

                $this->reporter->addModule([
                    'name' => $key,
                    'status' => 'skipped',
                    'file' => $config['file'] ?? null,
                    'records' => null,
                    'time' => null,
                    'truncated' => $config['truncate'] ?? false,
                    'faker' => false,
                    'details' => 'Dry run',
                ]);
                return;
            }

            // Ejecución real
            $this->executeSeeder($seeder, $key, $config);

        } catch (\Throwable $e) {
            $this->log("❌ Error en módulo {$key}: {$e->getMessage()}");

            $this->reporter->addModule([
                'name' => $key,
                'status' => 'failed',
                'file' => $config['file'] ?? null,
                'records' => null,
                'time' => null,
                'truncated' => $config['truncate'] ?? false,
                'faker' => false,
                'details' => $e->getMessage(),
            ]);
        }
    }

    protected function executeSeeder($seeder, string $key, array $config): void
    {
        $records = null;

        if ($config['transactional'] ?? false) {
            $connection = $seeder->getModel()->getConnection();
            $connection->transaction(function () use ($seeder, $key, $config, &$records) {
                $records = $this->runSeederCore($seeder, $key, $config);
            });
        } else {
            $records = $this->runSeederCore($seeder, $key, $config);
        }

        $this->reporter->addModule([
            'name' => $key,
            'status' => 'completed',
            'records' => $records,
            'time' => round(microtime(true) - LARAVEL_START, 2) . 's',
            'truncated' => $config['truncate'] ?? false,
            'faker' => isset($config['fake']),
            'file' => $config['file'] ?? 'N/A',
            'note' => 'OK',
        ]);

        $this->log("✅ Módulo completado: <info>{$key}</info>\n");
    }

    protected function runSeederCore($seeder, string $key, array $config): int
    {
        $records = 0;

        $runFake = $config['fake'] ?? false;
        $runNormal = !$runFake || !($config['faker_only'] ?? false); // si no está en modo faker_only, corre run()

        // Ejecutar inserción desde archivo solo si se permite
        if ($runNormal && method_exists($seeder, 'run')) {
            $seeder->run($config);
            $records = method_exists($seeder, 'getProcessedCount') ? $seeder->getProcessedCount() : 0;
        }

        // Ejecutar faker si se permite
        if ($runFake && method_exists($seeder, 'runFake')) {
            $min = (int) ($config['fake-count'] ?? $config['fake']['min'] ?? 1);
            $max = (int) ($config['fake-count'] ?? $config['fake']['max'] ?? $min);
            $total = $min === $max ? $min : rand($min, $max);

            $seeder->runFake($total, $config['fake']);
            $records += method_exists($seeder, 'getProcessedCount') ? $seeder->getProcessedCount() : $total;
        }

        return $records;
    }

    protected function resolveDependencies(array $modules): array
    {
        $resolved = [];
        $unresolved = $modules;

        while (!empty($unresolved)) {
            $resolvedCount = count($resolved);

            foreach ($unresolved as $key => $module) {
                $dependencies = $module['depends_on'] ?? [];

                if (collect($dependencies)->every(fn($dep) => isset($resolved[$dep]))) {
                    $resolved[$key] = $module;
                    unset($unresolved[$key]);
                }
            }

            if ($resolvedCount === count($resolved)) {
                $keys = implode(', ', array_keys($unresolved));
                throw new \RuntimeException("Dependencias circulares o no resueltas detectadas en: {$keys}");
            }
        }

        return $resolved;
    }

    protected function filterModules(array $modules, $only): array
    {
        $filtered = [];

        foreach ($modules as $key => $module) {
            // 1. Si está deshabilitado, lo ignoramos completamente
            if (!($module['enabled'] ?? true)) {
                continue;
            }

            // 2. Si hay filtro por "only", respetarlo
            if ($only !== null) {
                $onlyList = is_array($only) ? $only : [$only];
                if (!in_array($key, $onlyList)) {
                    continue;
                }
            }

            $filtered[$key] = $module;
        }

        return $filtered;
    }

    protected function clearAssetsIfNeeded(array $options = []): void
    {
        if ($options['dry-run'] ?? false) {
            $this->log("🔹 [DRY RUN] Simularía limpieza de assets");
            return;
        }

        $config = config('seeder.clear_assets', []);

        foreach ($config as $assetType => $enabled) {
            if (!$enabled) continue;

            match ($assetType) {
                'avatars' => app(AvatarImageService::class)->clearAllProfilePhotos(),
                'initials' => app(AvatarInitialsService::class)->clearAllInitials(),
                'attachments' => Storage::disk('attachments')->deleteDirectory('all'),
                default => null,
            };
        }
    }

    protected function logDryRun(string $key, array $config): void
    {
        $this->log("🔹 [DRY RUN] Simulación para módulo: <info>{$key}</info>");
        $this->log("├─ Seeder: <comment>{$config['seeder']}</comment>");
        if (isset($config['file'])) {
            $this->log("├─ Archivo: <comment>{$config['file']}</comment>");
        }
        if (isset($config['fake'])) {
            $count = $config['fake-count'] ?? ($config['fake']['max'] ?? 'aleatorio');
            $this->log("├─ Datos falsos: <comment>{$count} registros</comment>");
        }
        if ($config['truncate'] ?? false) {
            $this->log("└─ <fg=red>TRUNCATE activado</>");
        }
    }

    public function getAvailableModules(): array
    {
        return config('seeder.modules', []);
    }

    public function getSeederClass(string $module): ?string
    {
        return config("seeder.modules.$module.seeder");
    }

    public function getReportPath(): string
    {
        return $this->reporter->getReportPathMarkdown();
    }

    public function getReport(): array
    {
        return $this->reporter->getReportData();
    }

    public function setCommand(Command $command): static
    {
        $this->command = $command;
        return $this;
    }
}
