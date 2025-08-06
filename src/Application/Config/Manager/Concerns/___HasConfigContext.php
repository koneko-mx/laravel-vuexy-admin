<?php

namespace Koneko\VuexyAdmin\Application\Config\Manager\Concerns;

use Koneko\VuexyAdmin\Application\Traits\System\Context\HasBaseContext;
use Koneko\VuexyAdmin\Application\Traits\System\Context\HasConfigContextValidation;

trait __HasConfigContext
{
    use HasBaseContext;
    use HasConfigContextValidation;

    protected function validateKeyName(string $keyName): string
    {
        if (!preg_match('/^[a-zA-Z0-9-._]+$/', $keyName)) {
            throw new \InvalidArgumentException("El valor '{$keyName}' de 'keyName' debe ser un slug válido.");
        }

        if (strlen($keyName) > 64) {
            throw new \InvalidArgumentException("El valor de 'keyName' excede 64 caracteres.");
        }

        return $keyName;
    }

    // ======================= GETTERS =========================

    public function qualifiedKey(?string $key = null): string
    {
        $parts = [
            $this->context['namespace'],
            $this->context['component'],
            $this->context['group'],
            $this->context['section'],
            $this->context['sub_group'],
            $key ?? $this->context['key_name'],
        ];

        return collect($parts)
            ->filter()
            ->implode('.');
    }

    public function qualifiedKeyPrefix(): string
    {
        $parts = [
            $this->context['namespace'],
            $this->context['component'],
        ];

        return collect($parts)->filter()->implode('.');
    }

    // ======================= HELPERS =========================

    public function ensureQualifiedKey(): void
    {
        if (!$this->hasBaseContext()) {
            throw new \InvalidArgumentException("Falta definir el contexto base y 'key_name' en config().");
        }
    }
}
