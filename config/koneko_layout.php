<?php

return [
// ================== 🌐 LAYOUT ==================
    // Personalización de interfaz
    'vuexy' => [
        'myLayout'            => 'horizontal', // Options[String]: vertical(default), horizontal
        'myTheme'             => 'theme-semi-dark', // Options[String]: theme-default(default), theme-bordered, theme-semi-dark
        'myStyle'             => 'light', // Options[String]: light(default), dark & system mode
        'myRTLSupport'        => false, // options[Boolean]: true(default), false // To provide RTLSupport or not
        'myRTLMode'           => false, // options[Boolean]: false(default), true // To set layout to RTL layout  (myRTLSupport must be true for rtl mode)
        'hasCustomizer'       => true, // options[Boolean]: true(default), false // Display customizer or not THIS WILL REMOVE INCLUDED JS FILE. SO LOCAL STORAGE WON'T WORK
        'displayCustomizer'   => true, // options[Boolean]: true(default), false // Display customizer UI or not, THIS WON'T REMOVE INCLUDED JS FILE. SO LOCAL STORAGE WILL WORK
        'contentLayout'       => 'compact', // options[String]: 'compact', 'wide' (compact=container-xxl, wide=container-fluid)
        'navbarType'          => 'static', // options[String]: 'sticky', 'static', 'hidden' (Only for vertical Layout)
        'footerFixed'         => false, // options[Boolean]: false(default), true // Footer Fixed
        'menuFixed'           => false, // options[Boolean]: true(default), false // Layout(menu) Fixed (Only for vertical Layout)
        'menuCollapsed'       => true, // options[Boolean]: false(default), true // Show menu collapsed, (Only for vertical Layout)
        'headerType'          => 'static', // options[String]: 'static', 'fixed' (for horizontal layout only)
        'showDropdownOnHover' => false, // true, false (for horizontal layout only)
        'authViewMode'        => 'cover', // Options[String]: cover(default), basic
        'maxQuickLinks'       => 12, // options[Integer]: 8(default),  8, 10, 12
        'customizerControls'  => [
            'style',
            'headerType',
            'contentLayout',
            'layoutCollapsed',
            'layoutNavbarOptions',
            'themes',
        ], // To show/hide customizer options
    ],
    // 📋 Menú de Navegación
    'menu' => [
        'debug' => [
            'show_broken_routes'   => (bool) env('VUEXY_MENU_DEBUG_SHOW_BROKEN_ROUTES', false),
            'show_disallowed_links'=> (bool) env('VUEXY_MENU_DEBUG_SHOW_DISALLOWED_LINKS', false),
            'show_hidden_items'    => (bool) env('VUEXY_MENU_DEBUG_SHOW_HIDDEN_ITEMS', false),
        ],
        'cache' => [
            'enabled' => (bool) env('VUEXY_MENU_CACHE_ENABLED', true),
            'ttl'     => (int) env('VUEXY_MENU_CACHE_TTL', 2 * 24 * 60),
        ],
    ],
    'cache' => [
        'enabled' => (bool) env('VUEXY_CACHE_ENABLED', true),
        'ttl'     => (int) env('VUEXY_CACHE_TTL', 2 * 24 * 60),
    ],
];
