<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\KonekoVuexy\Plugins;

use Livewire\Component;

class PluginsIndex extends Component
{
    /**
     * Vista Blade que debe renderizar este componente.
     */
    public function render()
    {
        return view('vuexy-admin::livewire.koneko-vuexy.plugins.index');
    }
}
