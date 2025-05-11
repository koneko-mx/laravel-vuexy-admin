<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Contracts\Modules;

use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModule;

interface KonekoModuleServiceInterface
{
    /**
     * Lista los módulos instalados en el sistema.
     *
     * @return KonekoModule[]
     */
    public function listInstalled(): array;

    /**
     * Lista los módulos disponibles en el marketplace oficial.
     *
     * @return array Módulos con metadatos
     */
    public function listAvailable(): array;

    /**
     * Obtiene un módulo instalado por su slug.
     */
    public function get(string $slug): ?KonekoModule;

    /**
     * Instala un módulo oficial desde el marketplace.
     */
    public function installFromMarketplace(string $slug): bool;

    /**
     * Instala un módulo desde una URL externa (ej. GitHub, repositorio privado).
     */
    public function installFromUrl(string $url): bool;

    /**
     * Activa un módulo previamente instalado.
     */
    public function enable(string $slug): bool;

    /**
     * Desactiva un módulo instalado (sin eliminarlo físicamente).
     */
    public function disable(string $slug): bool;

    /**
     * Desinstala un módulo: elimina archivos y entrada en base de datos.
     */
    public function uninstall(string $slug): bool;

    /**
     * Ejecuta migraciones de un módulo.
     */
    public function migrate(string $slug): bool;

    /**
     * Reversión de migraciones del módulo (rollback).
     */
    public function rollback(string $slug): bool;

    /**
     * Publica los assets del módulo.
     */
    public function publishAssets(string $slug): bool;

    /**
     * Elimina los assets previamente publicados.
     */
    public function removeAssets(string $slug): bool;

    /**
     * Sincroniza los módulos detectados en el filesystem con los registrados en la DB.
     */
    public function syncFromModules(): bool;

    /**
     * Lista todos los paquetes composer (instalados) que podrían ser módulos.
     */
    public function detectAllComposerModules(): array;

    /**
     * Obtiene metadatos extendidos de un módulo (instalado o no).
     */
    public function getExtendedDetails(string $slug): array;
}
