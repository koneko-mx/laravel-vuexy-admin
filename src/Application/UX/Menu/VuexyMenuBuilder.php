<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Menu;

/**
 * Clase encargada de construir el menú dinámico Vuexy
 * basado en permisos, configuración, y visibilidad.
 */
class VuexyMenuBuilder
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
