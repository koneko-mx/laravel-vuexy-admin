<?php

namespace Koneko\VuexyAdmin\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotEmptyHtml implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Eliminar etiquetas HTML y espacios en blanco
        $strippedContent = trim(strip_tags($value));

        // Considerar vacío si no queda contenido significativo
        if (empty($strippedContent)) {
            $fail('El contenido no puede estar vacío.');
        }
    }
}
