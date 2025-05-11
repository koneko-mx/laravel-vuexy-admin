<?php

namespace Koneko\VuexyAdmin\Application\Helpers;

class VuexyNotifyHelper
{
    public static function flash(string $message, string $type = 'info', string $target = 'body', int $delay = 5000): void
    {
        session()->flash('vuexy_notification', [
            'type'    => $type,      // primary, success, danger, warning, info, dark
            'message' => $message,
            'target'  => $target,
            'delay'   => $delay,
        ]);
    }

    public static function success(string $message, string $target = 'body', int $delay = 5000): void
    {
        static::flash($message, 'success', $target, $delay);
    }

    public static function error(string $message, string $target = 'body', int $delay = 5000): void
    {
        static::flash($message, 'danger', $target, $delay);
    }

    public static function warning(string $message, string $target = 'body', int $delay = 5000): void
    {
        static::flash($message, 'warning', $target, $delay);
    }

    public static function info(string $message, string $target = 'body', int $delay = 5000): void
    {
        static::flash($message, 'info', $target, $delay);
    }
}
