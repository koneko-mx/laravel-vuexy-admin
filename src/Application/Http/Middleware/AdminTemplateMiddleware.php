<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Koneko\VuexyAdmin\Application\Cache\Builders\KonekoAdminVarsBuilder;
use Koneko\VuexyAdmin\Application\UX\Breadcrumbs\VuexyBreadcrumbsBuilder;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;
use Koneko\VuexyAdmin\Application\UX\Notifications\Builder\VuexyNotificationsBuilder;

class AdminTemplateMiddleware
{
    public function handle($request, Closure $next)
    {
        // Aplicar configuración de layout antes de que la vista se cargue
        if (str_contains($request->header('Accept'), 'text/html')) {
            config_m('core')->syncFromRegistry('koneko.core.layout.vuexy');

            View::share([
                '_admin'             => app(KonekoAdminVarsBuilder::class)->get(),
                'vuexyMenu'          => app(VuexyMenuFormatter::class)->getMenu(),
                'vuexyNotifications' => app(VuexyNotificationsBuilder::class)->getNotifications(),
                'vuexyBreadcrumbs'   => app(VuexyBreadcrumbsBuilder::class)->getBreadcrumbs(),
            ]);
        }

        return $next($request);
    }
}
