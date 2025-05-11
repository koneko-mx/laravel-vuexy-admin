<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotEmptyHtml implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $strippedContent = trim(strip_tags($value));
        if (empty($strippedContent)) {
            $fail('El campo :attribute no puede estar vacío.');
        }
    }
}
