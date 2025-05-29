<?php

namespace Koneko\VuexyAdmin\Application\UX\Notifications\Contracts;

use Koneko\VuexyAdmin\Application\UX\Notifications\Support\NotifyPayload;

interface NotificationChannelDriverInterface
{
    /**
     * Devuelve el nombre único del driver (ej: 'toast.toastr').
     */
    public function name(): string;

    /**
     * Envía/renderiza la notificación.
     */
    public function send(NotifyPayload $payload): void;

    /**
     * Define en qué contextos se puede usar este driver (web, livewire, mobile, etc).
     */
    public function supports(): array;
}
