<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands\UI;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Koneko\VuexyAdmin\Application\UI\Avatar\AvatarInitialsService;

#[AsCommand(name: 'avatars:clean-initial')]
class VuexyAvatarInitialsCommand extends Command
{
    protected $signature = 'avatars:clean-initial
                            {--days=30 : Número de días antes de eliminar}
                            {--dry    : Simular la eliminación sin borrar nada}';

    protected $description = 'Elimina avatares generados automáticamente en el directorio initial-avatars';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dry  = $this->option('dry') ?? false;

        $this->info("🔍 Escaneando avatares anteriores a $days días...");
        $deleted = app(AvatarInitialsService::class)->cleanupOldAvatars($days, $dry);

        if ($dry) {
            $this->line("🧪 DRY RUN: Se encontraron {$deleted} archivos que serían eliminados.");
        } else {
            $this->info("🗑️ {$deleted} avatares eliminados.");
        }

        return self::SUCCESS;
    }
}
