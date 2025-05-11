<?php

namespace Koneko\VuexyAdmin\Console\Commands;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Application\Cache\KonekoCacheManager;

class KonekoCacheHelperCommand extends Command
{
    protected $signature = 'koneko:cache
        {--component= : Componente (ej: admin, website)}
        {--group= : Grupo (ej: menu, html, avatar)}
        {--flush : Limpia el cache del grupo indicado}
        {--show : Muestra la información del cache manager}
        {--ttl : Muestra el TTL efectivo}
        {--driver : Muestra el driver actual}
        {--enabled : Muestra si el cache está habilitado}
        {--tags : Muestra las etiquetas asociadas (si el driver lo permite)}
        ';

    protected $description = 'Gestor de Cache Ecosistema Koneko: TTL, driver, flush, debug, enabled, etiquetas';

    public function handle(): int
    {
        $component = $this->option('component') ?? 'admin';
        $group     = $this->option('group') ?? 'cache';

        $manager = new KonekoCacheManager($component, $group);

        $title = "\n🧠 Koneko Cache Manager - [{$manager->path()}]";
        $this->info(str_repeat('-', strlen($title)));
        $this->info($title);
        $this->info(str_repeat('-', strlen($title)));

        if ($this->option('flush')) {
            $manager->flush();
            $this->warn("\n🧹 Caché limpiada para '{$manager->path()}'");
            return self::SUCCESS;
        }

        if ($this->option('show')) {
            $info = $manager->info();
            $this->line("\n🔧 Componente : <info>{$info['component']}</info>");
            $this->line("🔸 Grupo     : <info>{$info['group']}</info>");
            $this->line("📦 Driver    : <info>{$info['driver']}</info>");
            $this->line("🕒 TTL       : <info>{$info['ttl']} seg</info>");
            $this->line("🚦 Enabled   : <info>" . ($info['enabled'] ? 'true' : 'false') . "</info>");
            $this->line("🐞 Debug     : <info>" . ($info['debug'] ? 'true' : 'false') . "</info>");
        }

        if ($this->option('enabled')) {
            $this->line("✅ Habilitado: <info>" . ($manager->enabled() ? 'true' : 'false') . "</info>");
        }

        if ($this->option('ttl')) {
            $this->line("🕒 TTL efectivo: <info>{$manager->ttl()} seg</info>");
        }

        if ($this->option('driver')) {
            $this->line("⚙️ Driver actual: <info>{$manager->driver()}</info>");
        }

        if ($this->option('tags')) {
            $tags = $manager->tags();
            $this->line("🏷 Etiquetas: <info>" . implode(', ', $tags) . "</info>");
        }

        if (! $this->option('show') &&
            ! $this->option('ttl') &&
            ! $this->option('enabled') &&
            ! $this->option('driver') &&
            ! $this->option('tags') &&
            ! $this->option('flush')) {

            $this->warn("⚠️ No se especificó ninguna acción. Usa --help para ver las opciones disponibles.");
        }

        return self::SUCCESS;
    }
}
