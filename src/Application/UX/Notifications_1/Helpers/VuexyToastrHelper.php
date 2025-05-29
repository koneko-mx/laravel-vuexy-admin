<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Helpers;

class VuexyToastrHelper
{
    public static function flash(string $message, string $type = 'info', int $delay = 5000): void
    {
        session()->flash('vuexy_toastr', [
            'type'    => $type,     // success, info, warning, error
            'message' => $message,
            'delay'   => $delay,
        ]);
    }

    public static function success(string $message, int $delay = 5000): void
    {
        static::flash($message, 'success', $delay);
    }

    public static function error(string $message, int $delay = 5000): void
    {
        static::flash($message, 'error', $delay);
    }

    public static function warning(string $message, int $delay = 5000): void
    {
        static::flash($message, 'warning', $delay);
    }

    public static function info(string $message, int $delay = 5000): void
    {
        static::flash($message, 'info', $delay);
    }
}
