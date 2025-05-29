<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\System\Context;

trait HasConfigContextValidation
{
    use HasBaseContextValidator;

    protected function validateBaseContext(): void
    {
        foreach (['namespace', 'environment', 'component'] as $field) {
            if (empty($this->context[$field] ?? null)) {
                throw new \InvalidArgumentException("Falta definir '{$field}' en contexto config_m().");
            }
        }
    }

    public function validateContextWithScope(): void
    {
        $this->validateBaseContext();
        $this->requireKeyName();
        $this->validateScopeContext($this->context['scope'], $this->context['scope_id']);
    }
}
