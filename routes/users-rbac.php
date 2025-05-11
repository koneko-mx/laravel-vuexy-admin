<?php

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\Http\Controllers\{UserController,RoleController,PermissionController};
use Koneko\VuexyAdmin\Support\Routing\RouteScope;

RouteScope::auto(__FILE__, 'ajustes-de-sistema', function (RouteScope $r) {
    // Ajustes de sistema / Usuarios
    $r->route('usuarios', 'users.', UserController::class, function () {
        Route::get('/', 'index')->name('users.index');
        Route::get('{user}', 'show')->name('users.show');
        Route::get('{user}/editar', 'edit')->name('users.edit');
        Route::get('{user}/eliminar', 'delete')->name('users.delete');
    });

    // Ajustes de sistema / Rbac / Roles
    $r->route('rbac/roles', 'rbac.', RoleController::class, function () {
        Route::get('/', 'index')->name('roles.index');
    });

    // Ajustes de sistema / Rbac / Permisos
    $r->route('rbac/permisos', 'rbac.', PermissionController::class, function () {
        Route::get('/', 'index')->name('permissions.index');
    });
});
