<?php

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\Http\Controllers\HomeController;
use Koneko\VuexyAdmin\Support\Routing\RouteScope;

RouteScope::auto(__FILE__, function (RouteScope $r) {
    $r->route('', 'pages.', HomeController::class, function () {
        Route::get('muy-pronto', 'comingsoon')->name('comingsoon.index');
        Route::get('bajo-mantenimiento', 'underMaintenance')->name('under-maintenance.index');
    });
});
