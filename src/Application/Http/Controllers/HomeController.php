<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('vuexy-admin::pages.home');
    }

    public function about(): View
    {
        return view('vuexy-admin::pages.about');
    }

    public function comingsoon(): View
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('vuexy-admin::pages.comingsoon', compact('pageConfigs'));
    }

    public function underMaintenance(): View
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('vuexy-admin::pages.under-maintenance', compact('pageConfigs'));
    }
}
