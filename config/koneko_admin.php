<?php

return [
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
        'maxQuickLinks'       => 8, // options[Integer]: 8(default), 6, 8, 10
        'customizerControls'  => [
            'style',
            'headerType',
            'contentLayout',
            'layoutCollapsed',
            'layoutNavbarOptions',
            'themes',
        ], // To show/hide customizer options
    ],

    // HTTPS y proxies
    'security' => [
        'force_https'     => (bool) env('FORCE_HTTPS', false),
        'trust_proxy'     => (bool) env('TRUST_PROXY', false),
        'trust_proxy_ips' => env('TRUST_PROXY_IPS', '*'),

        // Key Vault & Encryption Management
        'key_vault' => [
            'driver'  => env('VUEXY_KEY_VAULT_DRIVER', 'laravel'), // Options: laravel, sqlite, mysql, mariadb, go_service
            'enabled' => (bool) env('VUEXY_KEY_VAULT_ENABLED', true),

            // Laravel Default Encryption (APP_KEY)
            'laravel' => [
                'key'       => env('APP_KEY'),
                'algorithm' => 'AES-256-CBC',
            ],

            // Second DB Configuration (Requires separate connection)
            'database' => [
                'connection' => env('VUEXY_KEY_VAULT_DB_CONNECTION', 'vault'),
                'table'      => env('VUEXY_KEY_VAULT_DB_TABLE', 'vault_keys'),
                'algorithm'  => env('VUEXY_KEY_VAULT_DB_ALGORITHM', 'AES-256-CBC'),
            ],

            // External Go Microservice
            'service' => [
                'base_url'  => env('VUEXY_KEY_VAULT_SERVICE_URL'),
                'api_token' => env('VUEXY_KEY_VAULT_SERVICE_TOKEN'),
                'timeout'   => (int) env('VUEXY_KEY_VAULT_SERVICE_TIMEOUT', 5),
            ],
        ],
    ],

    // Cache
    'cache' => [
        'enabled' => (bool) env('VUEXY_CACHE_ENABLED', true),
        'ttl'     => (int) env('VUEXY_CACHE_TTL', 20 * 24 * 60),
    ],

    // Avatar
    'avatar' => [
        'initials' => [
            'max_length' => (int) env('VUEXY_AVATAR_INITIALS_MAX_LENGTH', 2),
            'disk'       => env('VUEXY_AVATAR_INITIALS_DISK', 'public'),
            'directory'  => env('VUEXY_AVATAR_INITIALS_DIRECTORY', 'initial-avatars'),
            'size'       => (int) env('VUEXY_AVATAR_INITIALS_SIZE', 512),
            'background' => env('VUEXY_AVATAR_INITIALS_BACKGROUND', '#EBF4FF'),
            'colors'     => json_decode(env('VUEXY_AVATAR_INITIALS_COLORS', json_encode(['#3b82f6', '#808390', '#28c76f', '#ff4c51', '#ff9f43', '#00bad1', '#4b4b4b'])), true),
            'font_size_ratio' => (float) env('VUEXY_AVATAR_INITIALS_FONT_SIZE_RATIO', 0.4),
            'fallback_text' => env('VUEXY_AVATAR_INITIALS_FALLBACK_TEXT', 'NA'),
            'cache' => [
                'ttl'     => (int) env('VUEXY_AVATAR_INITIALS_CACHE_TTL', 30 * 24 * 60),
            ],
        ],
        'image' => [
            'disk'       => env('VUEXY_AVATAR_IMAGE_DISK', 'public'),
            'directory'  => env('VUEXY_AVATAR_IMAGE_DIRECTORY', 'profile-photos'),
            'width'      => (int) env('VUEXY_AVATAR_IMAGE_WIDTH', 512),
            'height'     => (int) env('VUEXY_AVATAR_IMAGE_HEIGHT', 512),
            'fit_method' => env('VUEXY_AVATAR_IMAGE_FIT_METHOD', 'cover'),
        ],
    ],

    // Menú
    'menu' => [
        'cache' => [
            'enabled' => (bool) env('VUEXY_MENU_CACHE_ENABLED', true),
            'ttl'     => (int) env('VUEXY_MENU_CACHE_TTL', 2 * 24 * 60),
        ],
        'debug' => [
            'show_broken_routers'   => (bool) env('VUEXY_MENU_DEBUG_SHOW_BROKEN_ROUTES', false),
            'show_disallowed_links' => (bool) env('VUEXY_MENU_DEBUG_SHOW_DISALLOWED_LINKS', false),
            'show_hidden_items'     => (bool) env('VUEXY_MENU_DEBUG_SHOW_HIDDEN_ITEMS', false),
        ],
    ],
];
