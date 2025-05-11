<?php

namespace Koneko\VuexyAdmin\Application\Enums\Modules;

enum ModulePackageSourceType: string
{
    case Official = 'official';     // Curado por Koneko Marketplace
    case External = 'external';     // URL externa (ej. GitHub)
    case Custom   = 'custom';       // Módulo propio/local
    case Zip      = 'zip';          // Instalado desde archivo ZIP

    /**
     * Devuelve la etiqueta legible para UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Official => 'Marketplace Oficial',
            self::External => 'Repositorio Externo',
            self::Custom   => 'Componente Interno',
            self::Zip      => 'Instalado desde ZIP',
        };
    }

    /**
     * Devuelve un ícono representativo.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Official => 'ti ti-shield-check',
            self::External => 'ti ti-world-www',
            self::Custom   => 'ti ti-tools',
            self::Zip      => 'ti ti-file-zip',
        };
    }
}
