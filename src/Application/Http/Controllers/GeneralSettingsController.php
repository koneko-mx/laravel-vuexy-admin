<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{JsonResponse,Request};
use Illuminate\View\View;
use Koneko\VuexyAdmin\Application\UX\ConfigBuilders\System\EnvironmentVarsTableConfigBuilder;

class GeneralSettingsController extends Controller
{
    /**
     * Muestra la vista de configuraciones generales
     *
     * @return \Illuminate\View\View
     */
    public function webInterface(): View
    {
        return view('vuexy-admin::settings.web-interface.index');
    }

    /**
     * Muestra la vista de configuraciones de interfaz
     *
     * @return \Illuminate\View\View
     */
    public function vuexyInterface(): View
    {
        //dd(Livewire::class);

        return view('vuexy-admin::settings.vuexy-interface.index');
    }

    /**
     * Muestra la vista de configuraciones SMTP
     *
     * @return \Illuminate\View\View
     */
    public function smtpSettings(): View
    {
        return view('vuexy-admin::settings.smtp-settings.index');
    }

    /**
     * Display a listing of the resource (Bootstrap Table AJAX or View).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\View\View
     */
    public function environmentVars(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            $builder = app(EnvironmentVarsTableConfigBuilder::class)->getQueryBuilder($request);

            return $builder->getJson();
        }

        return view('vuexy-admin::settings.environment-vars.index');
    }
}
