<?php

namespace Koneko\VuexyAdmin\Application\UX\Notifications\Drivers;

use Illuminate\Support\Facades\Blade;
use Koneko\VuexyAdmin\Application\UX\Notifications\Contracts\NotificationChannelDriverInterface;
use Koneko\VuexyAdmin\Application\UX\Notifications\Support\NotifyPayload;

final class ToastToastrDriver implements NotificationChannelDriverInterface
{
    public function name(): string
    {
        return 'toast.toastr';
    }

    public function send(NotifyPayload $payload): void
    {
        // Livewire-safe JS dispatch
        $event = [
            'type'    => $payload->type,
            'title'   => $payload->title,
            'message' => $payload->body,
            'timeout' => $payload->timeout,
            'target'  => $payload->target,
            'scope'   => $payload->scope,
        ];

        if (method_exists($this, 'dispatchBrowserEvent')) {
            $this->dispatch('notify', $event);
        } else {
            // fallback para contexto no Livewire
            echo Blade::render("<script>window.dispatchEvent(new CustomEvent('notify', { detail: " . json_encode($event) . " }));</script>");
        }
    }

    public function supports(): array
    {
        return ['web', 'livewire'];
    }
}
