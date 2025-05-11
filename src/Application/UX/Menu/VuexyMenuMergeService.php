<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Menu;

class VuexyMenuMergeService
{
    /**
     * Realiza un merge recursivo entre dos menús jerárquicos,
     * respetando subniveles y sobrescribiendo claves compatibles.
     */
    public static function mergeMenus(array $base, array $overrides): array
    {
        foreach ($overrides as $key => $overrideItem) {
            // Si la clave ya existe en el menú base
            if (isset($base[$key])) {
                // Si ambos son arrays con submenu, hacer merge profundo
                if (is_array($overrideItem) && is_array($base[$key])) {
                    $merged = $base[$key];

                    // Merge especial de _meta si ambos lo tienen
                    if (isset($merged['_meta']) && isset($overrideItem['_meta'])) {
                        $merged['_meta'] = array_merge($merged['_meta'], $overrideItem['_meta']);
                        unset($overrideItem['_meta']);
                    }

                    // Si hay submenu en ambos, hacer merge recursivo
                    if (isset($merged['submenu']) && isset($overrideItem['submenu'])) {
                        $merged['submenu'] = static::mergeMenus($merged['submenu'], $overrideItem['submenu']);
                        unset($overrideItem['submenu']);
                    }

                    // Resto de propiedades se sobrescriben
                    $base[$key] = array_merge($merged, $overrideItem);

                } else {
                    // Si no son arrays compatibles, sobrescribe completo
                    $base[$key] = $overrideItem;
                }

            } else {
                // Clave nueva, simplemente agregar
                $base[$key] = $overrideItem;
            }
        }

        return $base;
    }

    /**
     * Valida la estructura general del menú para evitar errores comunes
     */
    public static function validate(array $menu, string $path = ''): array
    {
        $errors = [];

        foreach ($menu as $key => $item) {
            $label = $path . $key;

            if (!is_array($item)) {
                $errors[] = "[{$label}] no es un array válido";
                continue;
            }

            if (!isset($item['_meta'])) {
                $errors[] = "[{$label}] no contiene clave _meta";
            }

            if (isset($item['submenu'])) {
                if (!is_array($item['submenu'])) {
                    $errors[] = "[{$label}] submenu debe ser un array";
                } else {
                    $errors = array_merge($errors, static::validate($item['submenu'], $label . '/'));
                }
            }
        }

        return $errors;
    }
}
