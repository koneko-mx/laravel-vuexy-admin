<?php

namespace Koneko\VuexyAdmin\Application\Traits\System\Context;

trait HasSettingsContextValidation
{
    use HasBaseContextValidator;

    /**
     * Valida que exista el contexto base requerido para operaciones de Settings.
     * Requisitos: namespace, environment, component, group, section, sub_group
     */
    protected function validateBaseContext(): void
    {
        foreach (['namespace', 'environment', 'component', 'group', 'section', 'sub_group'] as $field) {
            if (empty($this->context[$field] ?? null)) {
                throw new \InvalidArgumentException("Falta definir '{$field}' en contexto settings().");
            }
        }
    }

    /**
     * Valida contexto + key_name + coherencia de scope/scope_id.
     */
    public function validateContextWithScope(): void
    {
        $this->validateBaseContext();
        $this->requireKeyName();
        $this->validateScopeContext($this->context['scope'], $this->context['scope_id']);
    }
}
