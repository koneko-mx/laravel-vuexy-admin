<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuRegistry;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleRegistry;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'vuexy:menu:list-modules')]
class VuexyMenuListModulesCommand extends Command
{
    protected $signature = 'vuexy:menu:list-modules
        {--flat : Mostrar claves en formato plano}
        {--json : Mostrar salida como JSON}';

    protected $description = 'Lista los archivos de menú registrados por cada módulo de Vuexy';

    public function handle(): int
    {
        $registry = new VuexyMenuRegistry();
        $modules = KonekoModuleRegistry::enabled();

        $results = [];

        foreach ($modules as $module) {
            $menuPath = $module->extensions['menu']['path'] ?? null;
            $basePath = $module->basePath;
            $name     = $module->composerName;

            if ($menuPath && file_exists($basePath . DIRECTORY_SEPARATOR . $menuPath)) {
                $fullPath = realpath($basePath . DIRECTORY_SEPARATOR . $menuPath);
                $menuData = require $fullPath;
                $keys     = $this->extractKeys($menuData);

                $results[$name] = [
                    'file'  => $fullPath,
                    'count' => count($keys),
                    'keys'  => $keys,
                ];
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return self::SUCCESS;
        }

        foreach ($results as $module => $data) {
            $this->info("📦 $module");
            $this->line("     📁 Archivo: {$data['file']}");
            $this->line("     🔑 Claves: {$data['count']}");

            if ($this->option('flat')) {
                foreach ($data['keys'] as $key) {
                    $this->line("       └ $key");
                }
            } else {
                $this->displayTree($data['keys']);
            }

            $this->newLine();
        }

        return self::SUCCESS;
    }

    protected function extractKeys(array $menu, string $prefix = ''): array
    {
        $keys = [];
        foreach ($menu as $key => $item) {
            $full = $prefix ? "$prefix / $key" : $key;
            $keys[] = $full;
            if (isset($item['submenus']) && is_array($item['submenus'])) {
                $keys = array_merge($keys, $this->extractKeys($item['submenus'], $full));
            }
        }
        return $keys;
    }

    protected function displayTree(array $keys): void
    {
        foreach ($keys as $key) {
            $depth  = substr_count($key, '/');
            $indent = str_repeat('  ', $depth);
            $last   = strrchr($key, '/');
            $label  = $last !== false ? trim($last, '/') : $key;

            $this->line("       {$indent}└ {$label}");
        }
    }
}
