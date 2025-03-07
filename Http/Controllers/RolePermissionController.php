<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        return response()->json([
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all()
        ]);
    }

    public function storeRole(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
        $role = Role::create(['name' => $request->name]);
        return response()->json(['message' => 'Rol creado con éxito', 'role' => $role]);
    }

    public function storePermission(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:permissions,name']);
        $permission = Permission::create(['name' => $request->name]);
        return response()->json(['message' => 'Permiso creado con éxito', 'permission' => $permission]);
    }

    public function assignPermissionToRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id'
        ]);

        $role = Role::findById($request->role_id);
        $permission = Permission::findById($request->permission_id);

        $role->givePermissionTo($permission->name);

        return response()->json(['message' => 'Permiso asignado con éxito']);
    }

    public function removePermissionFromRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id'
        ]);

        $role = Role::findById($request->role_id);
        $permission = Permission::findById($request->permission_id);

        $role->revokePermissionTo($permission->name);

        return response()->json(['message' => 'Permiso eliminado con éxito']);
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'Rol eliminado con éxito']);
    }

    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json(['message' => 'Permiso eliminado con éxito']);
    }
}
