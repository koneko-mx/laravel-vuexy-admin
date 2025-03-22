<?php

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Http\Controllers\{HomeController,VuexyAdminController,CacheController,GlobalSettingsController};
use Koneko\VuexyAdmin\Http\Controllers\{UserController,UserProfileController,RoleController,PermissionController};
use Koneko\VuexyAdmin\Http\Controllers\LanguageController;
use Koneko\VuexyAdmin\Http\Controllers\{DocumentController};

// Grupo raíz para admin con middleware y prefijos comunes
Route::prefix('admin')->name('admin.core.')->middleware(['web', 'auth', 'admin'])->group(function () {
    // Páginas
    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('home.index');
        Route::get('acerca-de', 'about')->name('about.index');
        Route::get('muy-pronto', 'comingsoon')->name('comingsoon.index');
        Route::get('bajo-mantenimiento', 'underMaintenance')->name('under-maintenance.index');
    });

    // Perfil del usuario
    Route::controller(UserProfileController::class)->prefix('usuario')->name('user-profile.')->group(function () {
        Route::get('perfil', 'index')->name('index');
        Route::patch('perfil', 'update')->name('update');
        Route::delete('perfil', 'destroy')->name('destroy');
        Route::get('avatar', 'generateAvatar')->name('avatar');
    });

    // Usuarios
    Route::controller(UserController::class)->prefix('sistema/usuarios')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');  // Listar usuarios
        Route::get('{user}', 'show')->name('show');
        Route::get('{user}/delete', 'delete')->name('delete');
        Route::get('{user}/edit', 'edit')->name('edit');

        Route::post('/', 'store')->name('store');  // Guardar usero
        Route::put('{user}', 'update')->name('update');  // Actualizar usero
    });

    // Roles
    Route::controller(RoleController::class)->prefix('sistema/rbac/roles')->name('roles.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('ajax/check-unique-name', 'checkUniqueRoleName')->name('check-unique-name');
    });

    // Permisos
    Route::controller(PermissionController::class)->prefix('sistema/rbac/permisos')->name('permissions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/ajax/check-unique-name', 'checkUniquePermissionName')->name('check-unique-name');
    });

    // Ajustes de la aplicación
    Route::controller(VuexyAdminController::class)->prefix('ajustes-generales')->group(function () {
        Route::get('ajustes-generales', 'generalSettings')->name('general-settings.index');
    });

    // Ajustes de Cache
    Route::controller(CacheController::class)->prefix('ajustes-generales')->name('cache-manager.')->group(function () {
        Route::get('ajustes-de-cache', 'index')->name('index');
    });

    // Ajustes de Cache Actions
    Route::controller(CacheController::class)->name('cache-manager.')->group(function () {
        Route::post('config/cache', 'generateConfigCache')->name('config-cache');
        Route::post('route/cache', 'generateRouteCache')->name('route-cache');
    });

    // Ajustes de interface
    Route::controller(VuexyAdminController::class)->prefix('ajustes-generales')->group(function () {
        Route::get('ajustes-de-interfaz', 'VuexyInterfaceSettings')->name('interface-settings.index');
    });

    // Servidor de correo Saliente
    Route::controller(VuexyAdminController::class)->prefix('ajustes-generales')->group(function () {
        Route::get('servidor-de-correo-smtp', 'smtpSettings')->name('smtp-settings.index');
    });

    // Configuraciones globales
    Route::controller(GlobalSettingsController::class)->prefix('ajustes-generales')->group(function () {
        Route::get('configuraciones-globales', 'index')->name('global-settings.index');
    });

    // Documentos
    Route::controller(DocumentController::class)->prefix('documentos')->name('documents.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('crear', 'create')->name('create');
        Route::post('crear', 'store')->name('store');
        Route::get('editar/{document}', 'edit')->name('edit');
        Route::post('editar/{document}', 'update')->name('update');
        Route::delete('editar/{document}', 'destroy')->name('destroy');
    });

    // Accesos rápidos de la barra de menú
    Route::controller(VuexyAdminController::class)->group(function () {
        Route::post('quicklinks-update', 'quickLinksUpdate')->name('quicklinks-navbar.update');
    });

    // Buscador de la barra de menú
    Route::get('search-navbar', [VuexyAdminController::class, 'searchNavbar'])->name('search-navbar.index');

    // Cambio de idioma
    Route::get('language/{locale}', [LanguageController::class, 'swap'])->name('language.swap');

});
