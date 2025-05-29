<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Koneko\VuexyAdmin\Application\CoreModule;
use Koneko\VuexyAdmin\Application\UX\Navbar\VuexySearchBarBuilder;

class VuexyNavbarController extends Controller
{
    /**
     * Realiza búsqueda en la barra de navegación
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function searchNavbar(): JsonResponse
    {
        abort_if(!request()->expectsJson(), 403, __('errors.ajax_only'));

        return response()->json(app(VuexySearchBarBuilder::class)->getSearchData());
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

        /** @var string Settings Context */
        $group     = 'website-admin';
        $section   = 'layout';
        $sub_group = 'navbar';

        /** @var string Cache keyName */
        $key_name = 'quicklinks';


        $validated = $request->validate([
            'action' => 'required|in:update,remove',
            'route'  => 'required|string',
        ]);

        //$userId = Auth::user()->id;

        $quickLinks = settings(CoreModule::COMPONENT)
            ->context($group, $section, $sub_group)
            ->setScope($request->user())
            ->get($key_name)?? [];

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

        settings(CoreModule::COMPONENT)
            ->context($group, $section, $sub_group)
            ->setScope($request->user())
            ->set($key_name, json_encode($quickLinks));

        //VuexyQuicklinksBuilder::forgetCacheForUser();
    }
}
