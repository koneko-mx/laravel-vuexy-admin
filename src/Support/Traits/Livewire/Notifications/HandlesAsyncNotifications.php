<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Livewire\Notifications;

trait HandlesAsyncNotifications
{
    public function notify(string $message, string $type = 'info', int $delay = 5000): void
    {
        $this->dispatch('notify', [
            'message' => $message,
            'type'    => $type,     // info, success, warning, error
            'target'  => isset($this->targetNotify) ? $this->targetNotify : 'body',
            'delay'   => $delay,
            'channel' => 'toastify'
        ]);
    }
}
