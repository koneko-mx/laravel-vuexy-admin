<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Services\VuexyAdminService;

class AdminController extends Controller
{
    public function searchNavbar()
    {
        abort_if(!request()->expectsJson(), 403, __('errors.ajax_only'));

        $VuexyAdminService = app(VuexyAdminService::class);

        return response()->json($VuexyAdminService->getVuexySearchData());
    }

    public function quickLinksUpdate(Request $request)
    {
        abort_if(!request()->expectsJson(), 403, __('errors.ajax_only'));

        $validated = $request->validate([
            'action' => 'required|in:update,remove',
            'route' => 'required|string',
        ]);

        $quickLinks = Setting::where('user_id', Auth::user()->id)
            ->where('key', 'quicklinks')
            ->first();

        $quickLinks = $quickLinks ? json_decode($quickLinks->value, true) : [];

        if ($validated['action'] === 'update') {
            // Verificar si ya existe
            if (!in_array($validated['route'], $quickLinks))
                $quickLinks[] = $validated['route'];
        } elseif ($validated['action'] === 'remove') {
            // Eliminar la ruta si existe
            $quickLinks = array_filter($quickLinks, function ($route) use ($validated) {
                return $route !== $validated['route'];
            });
        }

        Setting::updateOrCreate(['user_id' => Auth::user()->id, 'key' => 'quicklinks'], ['value' => json_encode($quickLinks)]);

        VuexyAdminService::clearQuickLinksCache();
    }

    public function generalSettings()
    {
        return view('vuexy-admin::admin-settings.webapp-general-settings');
    }

    public function smtpSettings()
    {
        return view('vuexy-admin::admin-settings.smtp-settings');
    }
}
