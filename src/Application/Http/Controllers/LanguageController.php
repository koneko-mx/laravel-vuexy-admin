<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{Request,RedirectResponse};
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function swap(Request $request, $locale): RedirectResponse
    {
        if (!in_array($locale, ['es', 'co', 'en', 'fr', 'ar', 'de'])) {
            abort(400);

        } else {
            $request->session()->put('locale', $locale);
        }

        App::setLocale($locale);

        return redirect()->back();
    }
}
