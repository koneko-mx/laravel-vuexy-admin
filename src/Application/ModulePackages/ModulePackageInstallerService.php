<?php

namespace Koneko\VuexyAdmin\Application\Modules;

class ModulePackageInstallerService
{
    public function install(string $name): bool
    {
        $package = ModulePackage::where('name', $name)->firstOrFail();

        // 1. Descargar si es ZIP
        // 2. Ejecutar `composer require` si es URL
        // 3. Publicar assets, correr migraciones, registrar módulo

        // Esto sería más detallado en implementación real.
        return true;
    }

    public function update(string $name): bool
    {
        $package = ModulePackage::where('name', $name)->firstOrFail();
        // Reinstala o sincroniza desde URL actual
        return $this->install($name);
    }
}
