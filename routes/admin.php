<?php

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\Http\Controllers\{AuditoriaController, GeneralSettingsController, CacheController, KonekoModuleController};
use Koneko\VuexyAdmin\Support\Routing\RouteScope;

RouteScope::auto(__FILE__, function (RouteScope $r) {
    // Ajustes
    $r->route('ajustes-de-sistema', 'settings.', GeneralSettingsController::class, function () {
        Route::get('aplicacion/interfaz-web', 'webInterface')->name('web-interface.index');
        Route::get('aplicacion/interfaz-vuexy', 'vuexyInterface')->name('vuexy-interface.index');
        Route::get('correo-electronico/servidor-de-correo-smtp', 'smtpSettings')->name('smtp.index');
        Route::get('variables-de-entorno', 'environmentVars')->name('env.index');
    });

    // Herramietnas / Cache
    $r->route('herramientas/cache', 'cache.', CacheController::class, function () {
        Route::get('laravel', 'laravelIndex')->name('laravel.index');
        Route::get('redis', 'redisIndex')->name('redis.index');
        Route::get('memcache', 'memcacheIndex')->name('memcache.index');
        Route::get('sessions', 'sessionsIndex')->name('sessions.index');
        Route::get('carga-de-vite-assets', 'viteAssetsIndex')->name('vite-assets.index');
        Route::get('manejador-de-ttls', 'manualInvalidatorIndex')->name('ttls.index');
    });

    // Koneko Vuexy Admin
    $r->route('konek-vuexy-admin', 'modules.', KonekoModuleController::class, function () {
    //$r->route('konek-vuexy-admin', 'msodules.', KonekoModuleController::class, function () {
        Route::get('librerias-y-plugins', 'pluginsIndex')->name('plugins.index');
        Route::get('configuracion-de-modulos', 'modulesManagementIndex')->name('modules-management.index');
    });

    // Auditoria / Registros de sistema
    $r->route('registros-de-sistema', 'audit.', AuditoriaController::class, function () {
        Route::get('registros-de-accesos', 'usersAuthLogs')->name('users-auth-logs.index');
        Route::get('eventos-de-auditoria', 'securityEvents')->name('security-events.index');
        Route::get('registros-de-laravel', 'laravelLogs')->name('laravel-logs.index');
    });
});
