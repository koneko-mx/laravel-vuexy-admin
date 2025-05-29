<?php

namespace Koneko\VuexyAdmin\Application\UX\Notifications\Concerns;

use Koneko\VuexyAdmin\Application\UX\Notifications\Support\NotifyPayload;

trait ResolvesNotifySettings
{
    /**
     * Devuelve el nombre del driver activo para un canal.
     */
    protected function resolveDriverForChannel(string $channel): string
    {
        return settings()
            ->setComponent('vuexy-admin')
            ->setGroup('notifications')
            ->setKeyName("channel_driver.{$channel}")
            ->get() ?? "default.{$channel}";
    }

    /**
     * Aplica valores por defecto según configuración de canal.
     */
    protected function applyChannelDefaults(string $channel, NotifyPayload $payload): NotifyPayload
    {
        $configPrefix = "channel_config.{$channel}";

        $payload->type = $payload->type ?: settings()
            ->setComponent('vuexy-admin')
            ->setGroup('notifications')
            ->setKeyName("{$configPrefix}.type")
            ->get() ?? 'info';

        $payload->timeout = $payload->timeout ?? settings()
            ->setComponent('vuexy-admin')
            ->setGroup('notifications')
            ->setKeyName("{$configPrefix}.timeout")
            ->get() ?? 3000;

        return $payload;
    }
}
