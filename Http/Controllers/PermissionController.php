<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Koneko\VuexyAdmin\Queries\GenericQueryBuilder;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bootstrapTableIndexConfig = [
                'table' => 'permissions',
                'columns' => [
                    'permissions.id',
                    'permissions.name',
                    'permissions.group_name',
                    'permissions.sub_group_name',
                    'permissions.action',
                    'permissions.guard_name',
                    'permissions.created_at',
                    'permissions.updated_at',
                ],
                'filters' => [
                    'search' => ['permissions.name', 'permissions.group_name', 'permissions.sub_group_name', 'permissions.action'],
                ],
                'sort_column' => 'permissions.name',
                'default_sort_order' => 'asc',
            ];

            return (new GenericQueryBuilder($request, $bootstrapTableIndexConfig))->getJson();
        }

        return view('vuexy-admin::permissions.index');
    }
}
