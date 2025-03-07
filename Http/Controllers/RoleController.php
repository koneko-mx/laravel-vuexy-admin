<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('vuexy-admin::roles.index');
    }

    public function checkUniqueRoleName(Request $request)
    {
        $id   = $request->input('id');
        $name = $request->input('name');

        // Verificar si el nombre ya existe en la base de datos
        $existingRole = Role::where('name', $name)
            ->whereNot('id', $id)
            ->first();

        if ($existingRole) {
            return response()->json(['valid' => false]);
        }

        return response()->json(['valid' => true]);
    }
}
