<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\{Request,JsonResponse};
use Illuminate\View\View;
use Koneko\VuexyAdmin\Application\UX\ConfigBuilders\Rbac\PermissionsTableConfigBuilder;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            $builder = app(PermissionsTableConfigBuilder::class)->getQueryBuilder($request);

            return $builder->getJson();
        }

        return view('vuexy-admin::settings.rbac.permissions.index');
    }
}
