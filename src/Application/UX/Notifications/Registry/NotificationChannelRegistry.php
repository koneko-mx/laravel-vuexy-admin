<?php

namespace Koneko\VuexyAdmin\Application\UX\Notifications\Registry;

use Koneko\VuexyAdmin\Application\UX\Notifications\Contracts\NotificationChannelDriverInterface;

class NotificationChannelRegistry
{
    /**
     * @var array<string, NotificationChannelDriverInterface>
     */
    protected static array $drivers = [];

    /**
     * Registra un driver.
     */
    public static function register(NotificationChannelDriverInterface $driver): void
    {
        static::$drivers[$driver->name()] = $driver;
    }

    /**
     * Recupera un driver registrado.
     */
    public static function get(string $name): NotificationChannelDriverInterface
    {
        if (! isset(static::$drivers[$name])) {
            throw new \RuntimeException("El driver de notificación '{$name}' no está registrado.");
        }

        return static::$drivers[$name];
    }

    /**
     * Devuelve todos los drivers registrados.
     */
    public static function all(): array
    {
        return static::$drivers;
    }

    /**
     * Limpia el registro (útil en tests).
     */
    public static function flush(): void
    {
        static::$drivers = [];
    }
}
