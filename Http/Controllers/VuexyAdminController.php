<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Services\SettingsService;
use Koneko\VuexyAdmin\Services\VuexyAdminService;

/**
 * Controlador para la gestión de funcionalidades administrativas de Vuexy
 */
class VuexyAdminController extends Controller
{
    /**
     * Muestra la vista de configuraciones generales
     *
     * @return \Illuminate\View\View
     */
    public function GeneralSettings()
    {
        return view('vuexy-admin::general-settings.index');
    }

    /**
     * Muestra la vista de configuraciones SMTP
     *
     * @return \Illuminate\View\View
     */
    public function smtpSettings()
    {
        return view('vuexy-admin::sendmail-settings.index');
    }

    /**
     * Muestra la vista de configuraciones de interfaz
     *
     * @return \Illuminate\View\View
     */
    public function VuexyInterfaceSettings()
    {
        return view('vuexy-admin::interface-settings.index');
    }

    /**
     * Realiza búsqueda en la barra de navegación
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function searchNavbar()
    {
        abort_if(!request()->expectsJson(), 403, __('errors.ajax_only'));

        return response()->json(app(VuexyAdminService::class)->getVuexySearchData());
    }

    /**
     * Actualiza los enlaces rápidos del usuario
     *
     * @param Request $request Datos de la solicitud
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function quickLinksUpdate(Request $request)
    {
        abort_if(!request()->expectsJson(), 403, __('errors.ajax_only'));

        $validated = $request->validate([
            'action' => 'required|in:update,remove',
            'route' => 'required|string',
        ]);

        $quickLinks = Setting::where('key', 'quicklinks')
            ->where('user_id', Auth::user()->id)
            ->first();

        $quickLinks = $quickLinks ? json_decode($quickLinks->value, true) : [];

        if ($validated['action'] === 'update') {
            // Verificar si ya existe
            if (!in_array($validated['route'], $quickLinks)) {
                $quickLinks[] = $validated['route'];
            }

        } elseif ($validated['action'] === 'remove') {
            // Eliminar la ruta si existe
            $quickLinks = array_filter($quickLinks, function ($route) use ($validated) {
                return $route !== $validated['route'];
            });
        }

        app(SettingsService::class)->set('quicklinks', json_encode($quickLinks), Auth::user()->id, 'vuexy-admin');

        VuexyAdminService::clearQuickLinksCache();
    }
}
