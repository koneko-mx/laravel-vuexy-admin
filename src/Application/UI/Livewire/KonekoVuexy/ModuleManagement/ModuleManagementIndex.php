<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\KonekoVuexy\ModuleManagement;

use Livewire\Component;

class ModuleManagementIndex extends Component
{
    /**
     * Vista Blade que debe renderizar este componente.
     */
    public function render()
    {
        return view('vuexy-admin::livewire.koneko-vuexy.module-management.index');
    }
}
