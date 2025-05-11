<?php

return [
    'channels' => [
        'vuexy' => [
            'driver' => 'daily',
            'path' => storage_path('logs/vuexy.log'),
            'level' => env('VUEXY_LOG_LEVEL', 'debug'),
            'days' => 14,
        ],
    ],
];