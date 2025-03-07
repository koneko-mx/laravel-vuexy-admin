<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('vuexy-admin::pages.home');
    }

    public function about()
    {
        return view('vuexy-admin::pages.about');
    }

    public function comingsoon()
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('vuexy-admin::pages.comingsoon', compact('pageConfigs'));
    }

    public function underMaintenance()
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('vuexy-admin::pages.under-maintenance', compact('pageConfigs'));
    }
}
