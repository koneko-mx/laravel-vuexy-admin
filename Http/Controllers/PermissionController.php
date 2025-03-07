<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

use App\Http\Controllers\Controller;

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
            $permissions = Permission::latest()->get();

            return DataTables::of($permissions)
                ->addIndexColumn()
                ->addColumn('assigned_to', function ($row) {
                    return (Arr::pluck($row->roles, ['name']));
                })
                ->editColumn('created_at', function ($request) {
                    return $request->created_at->format('Y-m-d h:i:s a');
                })
                ->make(true);
        }

        return view('vuexy-admin::permissions.index');
    }
}
