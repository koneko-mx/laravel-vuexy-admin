<?php

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\Http\Controllers\{CacheController, CatalogAjaxController, UserController, VuexyNavbarController};
use Koneko\VuexyAdmin\Support\Routing\RouteScope;


RouteScope::auto(__FILE__, function (RouteScope $r) {
    // menu
    $r->route('navbar', 'menu.', VuexyNavbarController::class, function () {
        Route::get('search-navbar', 'searchNavbar')->name('search-navbar.ajax');
    });

    // cache
    $r->route('cache', 'cache.', CacheController::class, function () {
        Route::post('config/cache', 'generateConfigCache')->name('generate-config-cache');
        Route::post('route/cache', 'generateRouteCache')->name('generate-route-cache');
    });

    // user
    $r->route('usuario', 'user.', UserController::class, function () {
        Route::get('avatar', 'generateAvatar')->name('avatar.image');
    });

    // catalogos
    $r->route('ajax', 'ajax.', CatalogAjaxController::class, function () {
        Route::get('/catalogs/{component}/{catalog}', 'fetch')->name('catalogs.fetch');
    });
});
