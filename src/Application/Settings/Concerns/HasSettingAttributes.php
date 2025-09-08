<?php

namespace Koneko\VuexyAdmin\Application\Settings\Concerns;

use Carbon\Carbon;

trait HasSettingAttributes
{
    protected array $attributes = [
        'is_file'          => false,
        'is_encrypted'     => false,
        'is_config'        => false,
        'is_track_usage'   => false,
        'is_should_cache'  => false,
        'is_active'        => true,
        'expires_at'       => null,
    ];

    public function markAsActive(bool $state = true): static
    {
        return $this->setFlag('is_active', $state);
    }

    public function expiresAt(\DateTimeInterface|string|null $date): static
    {
        $this->attributes['expires_at'] = $date instanceof Carbon
            ? $date
            : ($date ? Carbon::parse($date) : null);

        return $this;
    }

    public function trackUsage(bool $state = true): static
    {
        $this->attributes['is_track_usage'] = $state;
        return $this;
    }

    // ==================== Helpers ====================

    protected function setFlag(string $flag, bool $value = true): static
    {
        $this->attributes[$flag] = $value;
        return $this;
    }

    // Flag privado reservado
    public function setInternalConfigFlag(bool $state = true): static
    {
        return $this->setFlag('is_config', $state);
    }
}
