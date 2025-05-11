<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Logger;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Koneko\VuexyAdmin\Application\Services\SystemLoggerService;

class LogMacrosServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Macro principal: log central
        Log::macro('system', function (): SystemLoggerService {
            return app(SystemLoggerService::class);
        });

        // 🔧 Ejemplo de uso inmediato (puedes remover o modificar):
        /*
        Log::system()->info(
            module: 'vuexy-admin',
            message: 'Sistema de macros de log inicializado correctamente',
            context: ['environment' => app()->environment()]
        );
        */
    }
}
