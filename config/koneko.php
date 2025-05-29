<?php

return [
    "title"       => "Koneko Soluciones Tecnológicas",
    "description" => "Koneko Soluciones Tecnológicas ofrece desarrollo de sistemas empresariales, sitios web profesionales, inteligencia artificial, infraestructura y soluciones digitales avanzadas para negocios en México.",
    "author"      => "arturo@koneko.mx",
    "app_name"    => "koneko.mx",
    "app_logo"    => "../vendor/vuexy-admin/img/logo/koneko-04.png",
    "favicon"     => "../vendor/vuexy-admin/img/logo/koneko-04.png",

    // ================== 📦 CACHE GENERAL ==================
    'cache' => [
        'enabled' => (bool) env('KONEKO_CACHE_ENABLED', true),
        'ttl'     => (int) env('KONEKO_CACHE_TTL', 20 * 24 * 60),  // 20 días
    ],

    // ================== 📦 CACHE DE COMPONENTE ==================
    'core' => [
        'cache' => [
            'enabled' => (bool) env('KONEKO_CORE_CACHE_ENABLED', true),
            'ttl'     => (int) env('KONEKO_CORE_CACHE_TTL', 20 * 24 * 60),
        ],
    ],
];
