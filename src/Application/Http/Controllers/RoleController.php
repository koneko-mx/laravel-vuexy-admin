<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        return view('vuexy-admin::settings.rbac.roles.index');
    }

    public function checkUniqueRoleName(Request $request): JsonResponse
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
