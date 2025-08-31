<?php

namespace Koneko\VuexyAdmin\Support\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DomainRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim(strtolower($value));

        // Rechazar si empieza con protocolo o con www.
        if (preg_match('/^(https?:\/\/)/i', $value)) {
            $fail('El :attribute no debe incluir http:// o https://.');
            return;
        }

        if (preg_match('/^www\./i', $value)) {
            $fail('El :attribute no debe incluir www.');
            return;
        }

        // Validar formato básico de dominio
        if (!preg_match('/^(?!:\/\/)(?=.{1,255}$)(([a-z0-9-]{1,63}\.)+[a-z]{2,})$/i', $value)) {
            $fail('El :attribute debe ser un dominio válido.');
        }
    }
}
