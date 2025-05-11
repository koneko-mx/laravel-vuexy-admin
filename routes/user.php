<?php

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\Http\Controllers\{UserController,UserProfileController};
use Koneko\VuexyAdmin\Support\Routing\RouteScope;

RouteScope::auto(__FILE__, function (RouteScope $r) {
    // Usuario
    $r->route('usuario', 'user.', UserProfileController::class, function () {
        Route::get('perfil-de-usuario', 'index')->name('profile.index');
    });

    // Visor de usuario
    $r->route('usuario', 'user.', UserController::class, function () {
        Route::get('visor-de-usuario/{user}', 'viewerIndex')->name('viewer.index');
    });
});
