<?php
return [
    'default_disk' => env('KONEKO_MEDIA_DISK', 'public'),
    'paths' => [
        'share'   => 'media/{module}/{scope}/{owner}/share',
        'logo'    => 'media/{module}/{scope}/{owner}/logo',
        'favicon' => 'media/{module}/{scope}/{owner}/favicon',
    ],
    'share' => [
        'default_area'    => 756000,
        'default_aspect'  => 1.91,
        'default_fit'     => 'cover',
        'default_format'  => 'auto',
        'default_quality' => 80,
        'default_bg'      => '#ffffff',
    ],
];
