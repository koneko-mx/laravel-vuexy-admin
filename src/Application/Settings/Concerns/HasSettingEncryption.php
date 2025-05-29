<?php

namespace Koneko\VuexyAdmin\Application\Settings\Concerns;

use Carbon\Carbon;
use Koneko\VuexyAdmin\Application\Settings\SettingDefaults;

trait HasSettingEncryption
{
    protected array $encryption = [
        'encryption_algorithm'  => null,
        'encryption_key'        => null,
        'encryption_rotated_at' => null,
    ];

    public function enableEncryption(bool $state = true): static
    {
        $this->attributes['is_encrypted'] = $state;

        if ($state) {
            $this->setEncryption(
                $this->encryption['encryption_algorithm'],
                $this->encryption['encryption_key']
            );
        }

        return $this;
    }

    public function setEncryption(string $algorithm = SettingDefaults::DEFAULT_ALGORITHM, ?string $key = null): static
    {
        $this->attributes['is_encrypted'] = true;
        $this->encryption['encryption_algorithm'] = $algorithm;
        $this->encryption['encryption_key']       = $key ?? config('app.key');

        return $this;
    }

    public function setEncryptionAlgorithm(string $algorithm): static
    {
        $this->attributes['is_encrypted'] = true;
        $this->encryption['encryption_algorithm'] = $algorithm;

        return $this;
    }

    public function setEncryptionKey(string $key): static
    {
        $this->attributes['is_encrypted'] = true;
        $this->encryption['encryption_key'] = $key;

        if ($this->encryption['encryption_algorithm'] === null) {
            $this->encryption['encryption_algorithm'] = SettingDefaults::DEFAULT_ALGORITHM;
        }

        return $this;
    }

    public function setEncryptionRotatedAt(Carbon|string|false|null $date): static
    {
        if (!$this->attributes['is_encrypted']) {
            throw new \InvalidArgumentException('Debe activar la encriptación antes de establecer la fecha de rotación');
        }

        $this->encryption['encryption_rotated_at'] = $date instanceof Carbon
            ? $date
            : ($date ? Carbon::parse($date) : null);

        return $this;
    }


    // ==================== Validaciones ====================

    protected function validateEncryption(): void
    {
        if ($this->attributes['is_encrypted'] && empty($this->encryption['encryption_key'])) {
            throw new \InvalidArgumentException("Se requiere 'encryption_key' para valores cifrados.");
        }
    }
}
