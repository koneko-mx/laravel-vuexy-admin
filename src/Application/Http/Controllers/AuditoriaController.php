<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Koneko\VuexyAdmin\Application\UX\ConfigBuilders\System\SecurityEventsTableConfigBuilder;
use Koneko\VuexyAdmin\Application\UX\ConfigBuilders\Users\UserLoginTableConfigBuilder;

/**
 * Controlador para la gestión de funcionalidades administrativas de Vuexy
 */
class AuditoriaController extends Controller
{
    /**
     * Muestra el listado de accesos al sistema (Bootstrap Table AJAX or View).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function usersAuthLogs(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            $builder = app(UserLoginTableConfigBuilder::class)->getQueryBuilder($request);

            return $builder->getJson();
        }

        return view('vuexy-admin::audit.users-auth-logs.index');
    }

    /**
     * Muestra el listado de eventos de auditoría (Bootstrap Table AJAX or View).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function securityEvents(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            $builder = app(SecurityEventsTableConfigBuilder::class)->getQueryBuilder($request);

            return $builder->getJson();
        }

        return view('vuexy-admin::audit.security-events.index');
    }

    public function laravelLogs(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            $builder = app(laravelLogsTableConfigBuilder::class)->getQueryBuilder($request);

            return $builder->getJson();
        }

        return view('vuexy-admin::audit.laravel-logs.index');
    }

}
