<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Koneko\VuexyAdmin\Queries\GenericQueryBuilder;

class GlobalSettingsController extends Controller
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
                'table' => 'settings',
                'columns' => [
                    'settings.id',
                    'settings.key',
                    'settings.category',
                    'settings.user_id',
                    DB::raw("CONCAT_WS(' ', users.name, users.last_name) AS user_fullname"),
                    'settings.value_string',
                    'settings.value_integer',
                    'settings.value_boolean',
                    'settings.value_float',
                    DB::raw("IF(LENGTH(settings.value_text) > 60, CONCAT(LEFT(settings.value_text, 60), '..'), settings.value_text) AS value_text"),
                    DB::raw("IF(settings.value_binary, '-BINARY-', '') AS value_binary"),
                    'settings.mime_type',
                    'settings.file_name',
                    'settings.created_at',
                    'settings.updated_at',
                    'settings.updated_by',
                    DB::raw("CONCAT_WS(' ', creator.name, creator.last_name) AS creator_name"),
                ],
                'joins' => [
                    [
                        'table'  => 'users',
                        'first'  => 'settings.user_id',
                        'second' => 'users.id',
                        'type'   => 'leftJoin',
                    ],
                    [
                        'table'  => 'users',
                        'first'  => 'settings.updated_by',
                        'second' => 'creator.id',
                        'type'   => 'leftJoin',
                        'alias'  => 'creator',
                    ],
                ],
                'filters' => [
                    'search' => [
                        'settings.key',
                        'settings.category',
                        'users.name',
                        'users.last_name',
                        'creator.name',
                        'creator.last_name',
                    ],
                ],
                'sort_column' => 'settings.key',
                'default_sort_order' => 'asc',
            ];

            return (new GenericQueryBuilder($request, $bootstrapTableIndexConfig))->getJson();
        }

        return view('vuexy-admin::global-settings.index');
    }
}
