<?php

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\Http\Controllers\HomeController;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;
use Koneko\VuexyAdmin\Support\Routing\RouteScope;


RouteScope::auto(__FILE__, function (RouteScope $r) {
    $r->route('', 'pages.', HomeController::class, function () {
        Route::get('acerca-de', 'about')->name('about.index');
    });

    $r->route('/', 'pages.', HomeController::class, function () {
        Route::get('/', 'index')->name('home.index');
    });

    $r->route('', 'pages.', function () {
        Route::get('f/{slug}', function ($slug) {

            $menuEntry = app(VuexyMenuFormatter::class)->getMenuBySlug($slug);

            if (!$menuEntry) {
                return redirect()->route('admin.core.pages.home.index')
                    ->with('warning', 'Acceso no permitido o sección inexistente.');
            }

            $title = $menuEntry
                ? last($menuEntry[array_key_first($menuEntry)]['_trail'])['label'] ?? 'Dashboard'
                : 'Dashboard';

            return view('vuexy-admin::pages.menu-access-cards', [
                'slug' => $slug,
                'title' => $title
            ]);
        })->name('folder.view');
    });

});
