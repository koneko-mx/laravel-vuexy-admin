<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Catalog\CatalogModuleRegistry;
use Symfony\Component\Console\Helper\Table;

class VuexyListCatalogsCommand extends Command
{
    /** @var string */
    protected $signature = 'vuexy:catalogs:list {--details : Muestra metadatos de cada catálogo} {--component= : Filtra por un componente en particular}';

    /** @var string */
    protected $description = 'Lista todos los catálogos registrados por componente en el sistema';

    public function handle(): int
    {
        $filterComponent = $this->option('component');
        $components = CatalogModuleRegistry::all();

        if (empty($components)) {
            $this->warn('No hay componentes registrados en el CatalogModuleRegistry.');
            return Command::SUCCESS;
        }

        foreach ($components as $component) {
            if ($filterComponent && $filterComponent !== $component) {
                continue;
            }

            $service = CatalogModuleRegistry::get($component);

            $this->info("\n📦 Componente: <fg=cyan>{$component}</>");

            $catalogs = $service->catalogs();

            if (empty($catalogs)) {
                $this->line("  └ No tiene catálogos registrados.");
                continue;
            }

            if ($this->option('details')) {
                $table = new Table($this->output);
                $table->setHeaders(['Catálogo', 'Tipo', 'Tabla/Enum', 'Llave', 'Label', 'Buscable', 'Filtros']);

                foreach ($catalogs as $cat) {
                    $meta = $service->getCatalogMeta($cat);
                    $table->addRow([
                        $cat,
                        $meta['enum'] ? 'Enum' : 'DB',
                        $meta['enum'] ?? ($meta['table'] ?? '-'),
                        $meta['key'] ?? '-',
                        $meta['label'] ?? '-',
                        implode(', ', $meta['searchable'] ?? []),
                        implode(', ', $meta['filters'] ?? []),
                    ]);
                }

                $table->render();
            } else {
                foreach ($catalogs as $cat) {
                    $this->line("  └ 📚 $cat");
                }
            }
        }

        return Command::SUCCESS;
    }
}
