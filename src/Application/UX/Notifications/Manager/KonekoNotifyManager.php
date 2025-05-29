<?php

namespace Koneko\VuexyAdmin\Application\UX\Notifications\Manager;

use Koneko\VuexyAdmin\Application\UX\Notifications\Registry\NotificationChannelRegistry;
use Koneko\VuexyAdmin\Application\UX\Notifications\Contracts\NotificationChannelDriverInterface;
use Koneko\VuexyAdmin\Application\UX\Notifications\Support\NotifyPayload;
use Koneko\VuexyAdmin\Application\UX\Notifications\Concerns\ResolvesNotifySettings;

final class KonekoNotifyManager
{
    use ResolvesNotifySettings;

    public function __construct(
        protected string $defaultChannel = 'toast'
    ) {}

    /**
     * Envía una notificación.
     */
    public function send(NotifyPayload $payload): void
    {
        // Determina canal
        $channel = $payload->channel ?? $this->defaultChannel;

        // Resuelve driver activo vía settings
        $driverName = $this->resolveDriverForChannel($channel);
        $driver = NotificationChannelRegistry::get($driverName);

        // Aplica valores por defecto si están ausentes
        $payload = $this->applyChannelDefaults($channel, $payload);

        // Ejecuta el envío
        $driver->send($payload);
    }
}
