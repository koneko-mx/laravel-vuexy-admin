<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Menu;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\{Auth,Route};
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Support\Traits\Cache\InteractsWithKonekoVarsCache;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

/**
 * Clase encargada de construir el menú dinámico Vuexy
 * basado en permisos, configuración, y visibilidad.
 */
class VuexyMenuBuilderService
{
    /**
     * Obtiene el menú procesado para un usuario específico, visitante o el autenticado.
     * Respeta la configuración de caché desde config/vuexy.php (VUEXY_CACHE_MENU).
     *
     * @param Authenticatable|null $user Usuario explícito o null para visitante.
     * @return array Menú final procesado y autorizado para el usuario.
     */
    public static function getForUser(): array
    {
        return app(VuexyMenuFormatter::class)->getMenu();
    }
}
