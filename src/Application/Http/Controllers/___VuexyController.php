<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Koneko\VuexyAdmin\Application\UX\Navbar\{VuexyQuicklinksBuilderService,VuexySearchBuilderService};
use Koneko\VuexyAdmin\Application\UI\Avatar\AvatarInitialsService;
use Koneko\VuexyAdmin\Application\UX\ConfigBuilders\System\EnvironmentVarsTableConfigBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controlador para la gestión de funcionalidades administrativas de Vuexy
 */
class VuexyController extends Controller
{
    public function __construct(
        private readonly AvatarInitialsService $avatarService
    ) {}


    /**
     * Realiza búsqueda en la barra de navegación
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function searchNavbar(): JsonResponse
    {
        abort_if(!request()->expectsJson(), 403, __('errors.ajax_only'));

        return response()->json(app(VuexySearchBuilderService::class)->getForUser());
    }

    /**
     * Actualiza los enlaces rápidos del usuario
     *
     * @param Request $request Datos de la solicitud
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function quickLinksUpdate(Request $request): void
    {
        abort_if(!request()->expectsJson(), 403, __('errors.ajax_only'));

        $validated = $request->validate([
            'action' => 'required|in:update,remove',
            'route' => 'required|string',
        ]);

        $quickLinks = settings()->get('quicklinks', Auth::user()->id, []);

        if ($validated['action'] === 'update') {
            if (!in_array($validated['route'], $quickLinks)) {
                $quickLinks[] = $validated['route'];
            }
        } elseif ($validated['action'] === 'remove') {
            $quickLinks = array_values(array_filter(
                $quickLinks,
                fn($route) => $route !== $validated['route']
            ));
        }

        settings()->set('quicklinks', json_encode($quickLinks), Auth::user()->id, 'vuexy-admin');

        app(VuexyQuicklinksBuilderService::class)->clearCache(Auth::user());
    }


    /**
     * Muestra la vista de configuraciones generales
     *
     * @return \Illuminate\View\View
     */
    public function generalSettings(): View
    {
        return view('vuexy-admin::general-settings.index');
    }

    /**
     * Muestra la vista de configuraciones SMTP
     *
     * @return \Illuminate\View\View
     */
    public function smtpSettings(): View
    {
        return view('vuexy-admin::smtp-settings.index');
    }

    /**
     * Muestra la vista de configuraciones de interfaz
     *
     * @return \Illuminate\View\View
     */
    public function InterfaceSettings(): View
    {
        return view('vuexy-admin::interface-settings.index');
    }

    /**
     * Muestra el listado de accesos al sistema (Bootstrap Table AJAX or View).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function userLogs(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            $builder = app(UserLoginTableConfigBuilder::class)->getQueryBuilder($request);

            return $builder->getJson();
        }

        return view('vuexy-admin::user-logs.index');
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

        return view('vuexy-admin::security-events.index');
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

        return view('vuexy-admin::environment-vars.index');
    }

    public function generateAvatar(Request $request): BinaryFileResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'color' => 'nullable|string|regex:/^[0-9a-fA-F]{6}$/',
                'background' => 'nullable|string|regex:/^[0-9a-fA-F]{6}$/',
                'size' => 'nullable|integer|min:20|max:1024',
                'max_length' => 'nullable|integer|min:1|max:3'
            ]);

            $response = $this->avatarService->getAvatarImage(
                name: $validated['name'],
                forcedColor: $validated['color'] ?? null,
                forcedBackground: $validated['background'] ?? null,
                size: $validated['size'] ?? null,
                maxLength: $validated['max_length'] ?? null
            );

            $response->headers->set('Cache-Control', 'public, max-age=86400');

            return $response;

        } catch (\Throwable $e) {
            return  $this->avatarService->getAvatarImage(
                name: 'E R R',
                forcedColor: '#FF0000',
                maxLength: 3
            );
        }
    }

}
