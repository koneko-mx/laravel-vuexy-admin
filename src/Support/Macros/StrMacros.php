<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Macros;

use Illuminate\Support\Str;

class StrMacros
{
    public static function register(): void
    {
        // Macro: pluralEs → Pluraliza una sola palabra en español
        if (! Str::hasMacro('pluralEs')) {
            Str::macro('pluralEs', function (string $word): string {
                $word = trim(Str::lower($word));

                return match (true) {
                    Str::endsWith($word, ['í', 'ú'])      => $word . 'es',
                    Str::endsWith($word, 'z')             => Str::replaceLast('z', 'ces', $word),
                    Str::endsWith($word, ['s', 'x'])      => $word,
                    Str::endsWith($word, ['á', 'é', 'ó']) => $word . 'es',
                    Str::endsWith($word, ['a', 'e', 'o']) => $word . 's',
                    default                               => $word . 'es',
                };
            });
        }

        // Macro: pluralEsPhrase → Pluraliza solo la primera palabra de una frase
        if (! Str::hasMacro('pluralEsPhrase')) {
            Str::macro('pluralEsPhrase', function (string $phrase): string {
                $phrase = trim($phrase);

                if (empty($phrase)) return $phrase;

                $exceptions = [
                    'admin', 'catalogo', 'cbb', 'cce', 'cfdi', 'clave', 'crm', 'csv', 'curp', 'diot', 'domicilio',
                    'email', 'erp', 'folio', 'gps', 'gsm', 'hardware', 'ine', 'internet', 'iep', 'isr', 'iva',
                    'json', 'licencia', 'log', 'modulo', 'nss', 'pdf', 'poliza', 'registro', 'retencion', 'rol',
                    'root', 'rfc', 'sat', 'saldo', 'sku', 'software', 'stock', 'subsidio', 'timbrado', 'token',
                    'traslado', 'uuid', 'version', 'xml'
                ];

                $words = preg_split('/\s+/', $phrase);
                $first = $words[0];
                $rest  = array_slice($words, 1);

                $isCapitalized = ctype_upper(Str::substr($first, 0, 1));
                $isUppercase   = strtoupper($first) === $first;

                $wordLower = Str::lower($first);

                // Excepciones que no se pluralizan
                if (in_array($wordLower, $exceptions)) {
                    $plural = $first;
                } else {
                    $pluralBase = Str::pluralEs($wordLower);

                    $plural = match (true) {
                        $isUppercase   => strtoupper($pluralBase),
                        $isCapitalized => Str::of($pluralBase)->ucfirst()->value(),
                        default        => $pluralBase,
                    };
                }

                return implode(' ', array_merge([$plural], $rest));
            });
        }
    }
}
