<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Koneko\VuexyAdmin\Application\Cache\VuexyVarsBuilderService;
use Koneko\VuexyAdmin\Application\UX\Content\VuexyBreadcrumbsBuilderService;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;
use Koneko\VuexyAdmin\Application\UX\Notifications\VuexyNotificationsBuilderService;
use Koneko\VuexyAdmin\Application\UX\Template\{VuexyConfigSynchronizer};

class AdminTemplateMiddleware
{
    public function __construct()
    {
        //
    }

    public function handle($request, Closure $next)
    {
        // Aplicar configuración de layout antes de que la vista se cargue
        if (str_contains($request->header('Accept'), 'text/html')) {
            app(VuexyConfigSynchronizer::class)->sync();

            View::share([
                '_admin'             => app(VuexyVarsBuilderService::class)->getAdminVars(),
                'vuexyMenu'          => app(VuexyMenuFormatter::class)->getMenu(),
                'vuexyNotifications' => app(VuexyNotificationsBuilderService::class)->getForUser(),
                'vuexyBreadcrumbs'   => app(VuexyBreadcrumbsBuilderService::class)->getBreadcrumbs(),
            ]);
        }

        return $next($request);
    }
}
