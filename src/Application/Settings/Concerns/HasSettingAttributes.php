<?php

namespace Koneko\VuexyAdmin\Application\Settings\Concerns;

use Carbon\Carbon;
use Koneko\VuexyAdmin\Application\Settings\SettingDefaults;

trait HasSettingAttributes
{
    protected array $attributes = [
        'is_system'        => false,
        'is_sensitive'     => false,
        'is_file'          => false,
        'is_encrypted'     => false,
        'is_config'        => false,
        'is_editable'      => true,
        'is_track_usage'   => SettingDefaults::DEFAULT_TRACK_USAGE,
        'is_should_cache'  => SettingDefaults::DEFAULT_SHOULD_CACHE,
        'is_active'        => true,
        'expires_at'       => null,
    ];

    public function markAsSystem(bool $state = true): static { return $this->setFlag('is_system', $state); }
    public function markAsSensitive(bool $state = true): static { return $this->setFlag('is_sensitive', $state); }
    public function markAsEditable(bool $state = true): static { return $this->setFlag('is_editable', $state); }
    public function markAsActive(bool $state = true): static { return $this->setFlag('is_active', $state); }

    public function expiresAt(Carbon|string|false|null $date): static
    {
        $this->attributes['expires_at'] = $date instanceof Carbon
            ? $date
            : ($date ? Carbon::parse($date) : null);

        return $this;
    }

    public function trackUsage(bool $state = true): static
    {
        $this->attributes['track_usage'] = $state;
        return $this;
    }

    // ==================== Helpers ====================

    protected function setFlag(string $flag, bool $value = true): static
    {
        $this->attributes[$flag] = $value;
        return $this;
    }

    // ======================= 🔐 FLAG PRIVADO =========================

    public function setInternalConfigFlag(bool $state = true): static { return $this->setFlag('is_config', $state); }
}
