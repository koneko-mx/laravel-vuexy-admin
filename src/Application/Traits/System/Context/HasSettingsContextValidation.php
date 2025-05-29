<?php

namespace Koneko\VuexyAdmin\Application\Traits\System\Context;

trait HasSettingsContextValidation
{
    use HasBaseContextValidator;

    protected function validateBaseContext(): void
    {
        foreach (['namespace', 'environment', 'component', 'group', 'section', 'sub_group'] as $field) {
            if (empty($this->context[$field] ?? null)) {
                throw new \InvalidArgumentException("Falta definir '{$field}' en contexto settings().");
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
