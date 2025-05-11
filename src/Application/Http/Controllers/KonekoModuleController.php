<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;

class KonekoModuleController extends Controller
{
    /**
     * Muestra la vista de configuraciones generales
     *
     * @return \Illuminate\View\View
     */
    public function pluginsIndex(): View
    {
        return view('vuexy-admin::koneko-vuexy.plugins.index');
    }

    public function modulesManagementIndex(): View
    {
        return view('vuexy-admin::koneko-vuexy.module-management.index');
    }
}
