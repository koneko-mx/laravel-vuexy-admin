<?php

namespace Koneko\VuexyAdmin\Application\Settings\Concerns;

use Carbon\Carbon;

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
            // Garantiza valores por defecto razonables si el consumidor no los setea explícitamente
            $this->setEncryptionAlgorithm()
                 ->setEncryptionKey();
        }
        return $this;
    }

    public function setEncryptionAlgorithm(string $algorithm = 'AES-256-CBC'): static
    {
        $this->encryption['encryption_algorithm'] = $algorithm;
        return $this;
    }

    public function setEncryptionKey(?string $key = null): static
    {
        $this->encryption['encryption_key'] = $key ?? config('app.key');
        return $this;
    }

    public function setEncryptionRotatedAt(\DateTimeInterface|string|null $date): static
    {
        if (!($this->attributes['is_encrypted'] ?? false)) {
            throw new \InvalidArgumentException('Debe activar la encriptación antes de establecer la fecha de rotación');
        }

        $this->encryption['encryption_rotated_at'] =
            $date instanceof \DateTimeInterface ? Carbon::instance($date)
            : ($date !== null ? Carbon::parse($date) : null);

        return $this;
    }


    protected function validateEncryption(): void
    {
        if ($this->attributes['is_encrypted'] && empty($this->encryption['encryption_key'])) {
            throw new \InvalidArgumentException("Se requiere 'encryption_key' para valores cifrados.");
        }
    }
}
