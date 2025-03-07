<?php

namespace Koneko\VuexyAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanInitialAvatars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avatars:clean-initial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina avatares generados automáticamente en el directorio initial-avatars';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = 'initial-avatars';
        $files = Storage::disk('public')->files($directory);

        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);

            // Elimina archivos no accedidos en los últimos 30 días
            if (now()->timestamp - $lastModified > 30 * 24 * 60 * 60) {
                Storage::disk('public')->delete($file);
            }
        }

        $this->info('Avatares iniciales antiguos eliminados.');
    }
}
