<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\User\Viewer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Livewire\Component;
use Koneko\VuexyAdmin\Models\User;

class UserDetailsViewerIndex extends Component
{
    public User|Model $user;

    public array $tabs = [];

    public function mount(User $user): void
    {
        $this->user = $user->loadMissing([
            'creator',
            'userLogins',
            'securityEvents',
            'systemNotifications',
            'roles',
            'permissions',
        ]);

        $this->prepareTabs();
    }

    protected function prepareTabs(): void
    {
        $this->tabs = [
            'general'    => 'Información General',
            'flags'      => 'Banderas',
            'roles'      => 'Roles y Permisos',
            'security'   => 'Seguridad',
            'contact'    => 'Contacto',
            'audit'      => 'Auditoría',
        ];
    }

    public function render(): View
    {
        return view('vuexy-admin::livewire.user.viewer.index');
    }

    // Properties helpers
    public function getActiveFlagsProperty(): array
    {
        return method_exists($this->user, 'activeFlags')
            ? $this->user->activeFlags()
            : [];
    }

    public function getAllPermissionsProperty(): array
    {
        return method_exists($this->user, 'getAllPermissions')
            ? $this->user->getAllPermissions()->pluck('name')->toArray()
            : [];
    }

    public function getRolesProperty(): array
    {
        return method_exists($this->user, 'roles')
            ? $this->user->roles->pluck('name')->toArray()
            : [];
    }
}
