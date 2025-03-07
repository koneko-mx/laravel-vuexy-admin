<?php

namespace Koneko\VuexyAdmin\Http\Middleware;

use Closure;
use Koneko\VuexyAdmin\Services\AdminTemplateService;
use Illuminate\Support\Facades\View;
use Koneko\VuexyAdmin\Services\VuexyAdminService;

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
            $adminVars = app(AdminTemplateService::class)->getAdminVars();
            $vuexyAdminService = app(VuexyAdminService::class);

            View::share([
                '_admin'             => $adminVars,
                'vuexyMenu'          => $vuexyAdminService->getMenu(),
                'vuexySearch'        => $vuexyAdminService->getSearch(),
                'vuexyQuickLinks'    => $vuexyAdminService->getQuickLinks(),
                'vuexyNotifications' => $vuexyAdminService->getNotifications(),
                'vuexyBreadcrumbs'   => $vuexyAdminService->getBreadcrumbs(),
            ]);

        }

        return $next($request);
    }
}
