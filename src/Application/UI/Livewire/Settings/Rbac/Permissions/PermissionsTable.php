<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Rbac\Permissions;

use Koneko\VuexyAdmin\Application\UX\ConfigBuilders\Rbac\PermissionsTableConfigBuilder;
use Koneko\VuexyAdmin\Support\Livewire\Components\Table\AbstractTableComponent;
use Spatie\Permission\Models\Role;

class PermissionsTable extends AbstractTableComponent
{
    public $userRoleStyles = [];

    protected function configBuilderClass(): ?string
    {
        return PermissionsTableConfigBuilder::class;
    }

    /**
     * Montaje inicial del componente, incluyendo carga de rutas.
     */
    public function mount(): void
    {
        parent::mount();

        $this->userRoleStyles = json_encode(
            Role::all()->mapWithKeys(function ($role) {
                return [$role->name => $role->style];
            })
        );
    }

    /**
     * Vista Blade que debe renderizar este componente.
     */
    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.settings.rbac.permissions.table-index';
    }
}

