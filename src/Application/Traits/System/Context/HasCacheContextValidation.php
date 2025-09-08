<?php

namespace Koneko\VuexyAdmin\Application\Traits\System\Context;

trait HasCacheContextValidation
{
    use HasBaseContextValidator;

    /**
     * Verifica que el contexto mínimo para cache esté presente.
     */
    protected function validateBaseContext(): void
    {
        foreach (['namespace', 'environment', 'component', 'group', 'section', 'sub_group'] as $field) {
            if (empty($this->context[$field] ?? null)) {
                throw new \InvalidArgumentException("Falta definir '{$field}' en el contexto de caché.");
            }
        }
    }

    /**
     * Valida base, key_name y coherencia de scope.
     */
    public function validateContextWithScope(): void
    {
        $this->validateBaseContext();
        $this->requireKeyName();
        $this->validateScopeContext($this->context['scope'], $this->context['scope_id']);
    }
}
