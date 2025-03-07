<?php

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Http\Controllers\AdminController;
use Koneko\VuexyAdmin\Http\Controllers\CacheController;
use Koneko\VuexyAdmin\Http\Controllers\HomeController;
use Koneko\VuexyAdmin\Http\Controllers\UserController;
use Koneko\VuexyAdmin\Http\Controllers\UserProfileController;
use Koneko\VuexyAdmin\Http\Controllers\RoleController;
use Koneko\VuexyAdmin\Http\Controllers\PermissionController;

// Grupo raíz para admin con middleware y prefijos comunes
Route::prefix('admin')->name('admin.core.')->middleware(['web', 'auth', 'admin'])->group(function () {
    // Rutas de HomeController
    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('home.index');
        Route::get('acerca-de', 'about')->name('about.index');
        Route::get('muy-pronto', 'comingsoon')->name('comingsoon.index');
        Route::get('bajo-mantenimiento', 'underMaintenance')->name('under-maintenance.index');
    });

    Route::controller(UserController::class)->prefix('sistema/usuarios')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');  // Listar usuarios
        Route::get('{user}', 'show')->name('show');
        Route::get('{user}/delete', 'delete')->name('delete');
        Route::get('{user}/edit', 'edit')->name('edit');



        Route::post('/', 'store')->name('store');  // Guardar usero
        Route::put('{user}', 'update')->name('update');  // Actualizar usero
    });

    // Rutas de UserController
    Route::controller(UserProfileController::class)->prefix('usuario')->name('user-profile.')->group(function () {
        Route::get('perfil', 'index')->name('index');
        Route::patch('perfil', 'update')->name('update');
        Route::delete('perfil', 'destroy')->name('destroy');
        Route::get('avatar', 'generateAvatar')->name('avatar');
    });

    // Rutas de RoleController
    Route::controller(RoleController::class)->prefix('sistema/rbac/roles')->name('roles.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('ajax/check-unique-name', 'checkUniqueRoleName')->name('check-unique-name');
    });

    // Rutas de PermissionController
    Route::controller(PermissionController::class)->prefix('sistema/rbac/permisos')->name('permissions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/ajax/check-unique-name', 'checkUniquePermissionName')->name('check-unique-name');
    });


    // Rutas de AdminController
    Route::controller(AdminController::class)->prefix('ajustes/aplicacion')->group(function () {
        Route::get('ajustes-generales', 'generalSettings')->name('general-settings.index');
        Route::get('servidor-de-correo-smtp', 'smtpSettings')->name('smtp-settings.index');
    });

    Route::controller(AdminController::class)->group(function () {
        Route::post('quicklinks-update', 'quickLinksUpdate')->name('quicklinks-navbar.update');
    });

    Route::controller(CacheController::class)->prefix('ajustes/aplicacion')->name('cache-manager.')->group(function () {
        Route::get('ajustes-de-cache', 'cacheManager')->name('index');
    });

    Route::controller(CacheController::class)->name('cache-manager.')->group(function () {
        Route::post('config/cache', 'generateConfigCache')->name('config-cache');
        Route::post('route/cache', 'generateRouteCache')->name('route-cache');
    });
});

// Rutas públicas sin autenticación
Route::prefix('admin')->name('admin.core.')->middleware(['web', 'auth'])->group(function () {
    Route::get('search-navbar', [AdminController::class, 'searchNavbar'])->name('search-navbar.index');
});

