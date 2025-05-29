<?php

return [
// ================== 👤 UI / AVATAR ==================
    'avatar' => [
        // 🔠 Avatares por Iniciales
        'initials' => [
            'max_length'       => (int) env('VUEXY_AVATAR_INITIALS_MAX_LENGTH', 2),
            'disk'             => env('VUEXY_AVATAR_INITIALS_DISK', 'public'),
            'directory'        => env('VUEXY_AVATAR_INITIALS_DIRECTORY', 'initial-avatars'),
            'size'             => (int) env('VUEXY_AVATAR_INITIALS_SIZE', 512),
            'background'       => env('VUEXY_AVATAR_INITIALS_BACKGROUND', '#EBF4FF'),
            'colors'           => json_decode(env('VUEXY_AVATAR_INITIALS_COLORS', json_encode([
                                    '#3b82f6', '#808390', '#28c76f', '#ff4c51',
                                    '#ff9f43', '#00bad1', '#4b4b4b'
                                ])), true),
            'font_size_ratio'  => (float) env('VUEXY_AVATAR_INITIALS_FONT_SIZE_RATIO', 0.4),
            'fallback_text'    => env('VUEXY_AVATAR_INITIALS_FALLBACK_TEXT', 'NA'),

            // 🧹 Mantenimiento y Depuración de Avatares
            'maintenance' => [
                'enabled'        => (bool) env('VUEXY_AVATAR_CLEANUP_ENABLED', true),
                'ttl_days'       => (int) env('VUEXY_AVATAR_CLEANUP_TTL_DAYS', 30), // Días antes de considerar obsoletos los avatares
                'cleanup_cron'   => env('VUEXY_AVATAR_CLEANUP_CRON', '0 3 * * *'),  // Hora de ejecución del Job de limpieza (por defecto a las 3 AM)
                'max_batch_size' => (int) env('VUEXY_AVATAR_CLEANUP_MAX_BATCH', 500), // Máximo de archivos a procesar por ejecución
                'log_deletions'  => (bool) env('VUEXY_AVATAR_CLEANUP_LOG', true),    // Registrar o no las eliminaciones
            ],
        ],

        // 📸 Avatares por Imagen
        'image' => [
            'disk'       => env('VUEXY_AVATAR_IMAGE_DISK', 'public'),
            'directory'  => env('VUEXY_AVATAR_IMAGE_DIRECTORY', 'profile-photos'),
            'width'      => (int) env('VUEXY_AVATAR_IMAGE_WIDTH', 512),
            'height'     => (int) env('VUEXY_AVATAR_IMAGE_HEIGHT', 512),
            'fit_method' => env('VUEXY_AVATAR_IMAGE_FIT_METHOD', 'cover'),
        ],
    ],
];
