<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Notifications\Manager;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Koneko\VuexyAdmin\Models\Notification as NotificationModel;
use Koneko\VuexyAdmin\Application\Events\Notifications\NotificationEmitted;

class NotifyChannelManager
{
    protected string $channel = 'toast';
    protected ?string $driver = null;
    protected ?int $userId = null;
    protected array $payload = [];

    public static function make(array $data): self
    {
        $instance = new self();
        $instance->channel = $data['channel'] ?? 'toast';
        $instance->driver = $data['driver'] ?? null;
        $instance->userId = $data['user_id'] ?? Auth::id();
        $instance->payload = $data;

        return $instance;
    }

    public function send(): void
    {
        match ($this->channel) {
            'toast'     => $this->handleToast(),
            'push'      => $this->handlePush(),
            'banner'    => $this->handleBanner(),
            'websocket' => $this->handleWebSocket(),
            default     => $this->handleCustom(),
        };
    }

    protected function handleToast(): void
    {
        $driver = $this->driver ?? settings('core')->get('notifications.toast.driver', 'toastr');

        Event::dispatch('notify.toast', array_merge($this->payload, [
            'driver' => $driver,
        ]));
    }

    protected function handlePush(): void
    {
        $notification = NotificationModel::create([
            'user_id'    => $this->userId,
            'channel'    => 'push',
            'type'       => $this->payload['type'] ?? 'info',
            'title'      => $this->payload['title'] ?? 'Mensaje',
            'body'       => $this->payload['message'] ?? null,
            'action_url' => $this->payload['url'] ?? null,
            'data'       => json_encode($this->payload),
        ]);

        Event::dispatch(new NotificationEmitted($notification));
    }

    protected function handleBanner(): void
    {
        // A futuro podrías registrar directamente en `system_notifications`
        Event::dispatch('notify.banner', $this->payload);
    }

    protected function handleWebSocket(): void
    {
        broadcast(new NotificationEmitted((object) $this->payload))->toOthers();
    }

    protected function handleCustom(): void
    {
        Event::dispatch('notify.custom', array_merge($this->payload, [
            'channel' => $this->channel,
            'driver'  => $this->driver,
        ]));
    }
}
