<?php

namespace Koneko\VuexyAdmin\Application\UX\Notifications\Builder;

use Koneko\VuexyAdmin\Application\UX\Notifications\Manager\KonekoNotifyManager;
use Koneko\VuexyAdmin\Application\UX\Notifications\Support\NotifyPayload;

class NotifyBuilder
{
    protected array $data = [];

    public function scope(string $scope): static
    {
        $this->data['scope'] = $scope;
        return $this;
    }

    public function to(mixed $user): static
    {
        $this->data['data']['to'] = $user;
        return $this;
    }

    public function channel(string $channel): static
    {
        $this->data['channel'] = $channel;
        return $this;
    }

    public function type(string $type): static
    {
        $this->data['type'] = $type;
        return $this;
    }

    public function title(string $title): static
    {
        $this->data['title'] = $title;
        return $this;
    }

    public function body(string $body): static
    {
        $this->data['body'] = $body;
        return $this;
    }

    public function target(string $target): static
    {
        $this->data['target'] = $target;
        return $this;
    }

    public function timeout(int $ms): static
    {
        $this->data['timeout'] = $ms;
        return $this;
    }

    public function persist(bool $value = true): static
    {
        $this->data['persist'] = $value;
        return $this;
    }

    public function requiresConfirmation(bool $value = true): static
    {
        $this->data['requiresConfirmation'] = $value;
        return $this;
    }

    public function withData(array $extra): static
    {
        $this->data['data'] = array_merge($this->data['data'] ?? [], $extra);
        return $this;
    }

    public function send(): void
    {
        $manager = app(KonekoNotifyManager::class);

        $payload = NotifyPayload::make($this->data);

        $manager->send($payload);
    }
}
