<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Livewire;

trait DispatchesVuexyNotifications
{
    /**
     * Target por defecto para notificaciones.
     */
    protected string $targetNotify = '#vuexy-interface-index-card .notification-container';

    /**
     * Emite una notificación genérica.
     *
     * @param string $message Mensaje de notificación
     * @param string $type Tipo de alerta ('info', 'success', 'warning', 'danger')
     * @param int    $delay Tiempo de vida de la notificación en milisegundos
     */
    public function vuexyNotify(string $message, string $type = 'info', int $delay = 3000): void
    {
        $this->dispatch('vuexy:notify', [
            'type'    => $type,
            'message' => $message,
            'target'  => $this->targetNotify,
            'delay'   => $delay,
        ]);
    }

    /**
     * Notificación tipo success.
     *
     * @param string $message
     * @param int    $delay
     */
    public function vuexyNotifySuccess(string $message, int $delay = 3000): void
    {
        $this->vuexyNotify($message, 'success', $delay);
    }

    /**
     * Notificación tipo error (danger).
     *
     * @param string $message
     * @param int    $delay
     */
    public function vuexyNotifyError(string $message, int $delay = 3000): void
    {
        $this->vuexyNotify($message, 'danger', $delay);
    }

    /**
     * Notificación tipo warning.
     *
     * @param string $message
     * @param int    $delay
     */
    public function vuexyNotifyWarning(string $message, int $delay = 3000): void
    {
        $this->vuexyNotify($message, 'warning', $delay);
    }

    /**
     * Notificación tipo info.
     *
     * @param string $message
     * @param int    $delay
     */
    public function vuexyNotifyInfo(string $message, int $delay = 3000): void
    {
        $this->vuexyNotify($message, 'info', $delay);
    }
}
