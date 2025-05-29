<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Enums\Permissions;

use Illuminate\Support\Facades\App;

enum PermissionAction: string
{
    case View      = 'view';
    case Create    = 'create';
    case Update    = 'update';
    case Delete    = 'delete';
    case Approve   = 'approve';
    case Cancel    = 'cancel';
    case Close     = 'close';
    case Reopen    = 'reopen';
    case Duplicate = 'duplicate';
    case Import    = 'import';
    case Export    = 'export';
    case Print     = 'print';
    case Email     = 'email';
    case Sync      = 'sync';
    case Configure = 'configure';
    case Allow     = 'allow';
    case Assign    = 'assign';
    case Stamp     = 'stamp';
    case Install   = 'install';
    case Clean     = 'clean';
    case Publish   = 'publish';
    case Archive   = 'archive';
    case Feature   = 'feature';

    public function label(?string $locale = null): ?string
    {
        $locale ??= App::getLocale();

        $labels = match ($locale) {
            'es' => [
                self::View->value      => 'Ver',
                self::Create->value    => 'Crear',
                self::Update->value    => 'Editar',
                self::Delete->value    => 'Eliminar',
                self::Approve->value   => 'Aprobar',
                self::Cancel->value    => 'Cancelar',
                self::Close->value     => 'Cerrar',
                self::Reopen->value    => 'Reabrir',
                self::Duplicate->value => 'Duplicar',
                self::Import->value    => 'Importar',
                self::Export->value    => 'Exportar',
                self::Print->value     => 'Imprimir',
                self::Email->value     => 'Correo',
                self::Sync->value      => 'Sincronizar',
                self::Configure->value => 'Configurar',
                self::Allow->value     => 'Autorizar',
                self::Assign->value    => 'Asignar',
                self::Stamp->value     => 'Timbrar',
                self::Install->value   => 'Instalar',
                self::Clean->value     => 'Limpiar',
                self::Publish->value   => 'Publicar',
                self::Archive->value   => 'Archivar',
                self::Feature->value   => 'Destacar',
            ],
            'en' => [
                self::View->value      => 'View',
                self::Create->value    => 'Create',
                self::Update->value    => 'Update',
                self::Delete->value    => 'Delete',
                self::Approve->value   => 'Approve',
                self::Cancel->value    => 'Cancel',
                self::Close->value     => 'Close',
                self::Reopen->value    => 'Reopen',
                self::Duplicate->value => 'Duplicate',
                self::Import->value    => 'Import',
                self::Export->value    => 'Export',
                self::Print->value     => 'Print',
                self::Email->value     => 'Email',
                self::Sync->value      => 'Sync',
                self::Configure->value => 'Configure',
                self::Allow->value     => 'Authorize',
                self::Assign->value    => 'Assign',
                self::Stamp->value     => 'Stamp',
                self::Install->value   => 'Install',
                self::Clean->value     => 'Clean',
                self::Publish->value   => 'Publish',
                self::Archive->value   => 'Archive',
                self::Feature->value   => 'Feature',
            ],
            default => [] // fallback vacío
        };

        return $labels[$this->value] ?? ucfirst($this->value);
    }
}
