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
        return settings('core')
            ->component('vuexy-admin')
            ->group('notifications')
            ->keyName("channel_driver.{$channel}")
            ->get() ?? "default.{$channel}";
    }

    /**
     * Aplica valores por defecto según configuración de canal.
     */
    protected function applyChannelDefaults(string $channel, NotifyPayload $payload): NotifyPayload
    {
        $configPrefix = "channel_config.{$channel}";

        $payload->type = $payload->type ?: settings('core')
            ->component('vuexy-admin')
            ->group('notifications')
            ->keyName("{$configPrefix}.type")
            ->get() ?? 'info';

        $payload->timeout = $payload->timeout ?? settings('core')
            ->component('vuexy-admin')
            ->group('notifications')
            ->keyName("{$configPrefix}.timeout")
            ->get() ?? 3000;

        return $payload;
    }
}
