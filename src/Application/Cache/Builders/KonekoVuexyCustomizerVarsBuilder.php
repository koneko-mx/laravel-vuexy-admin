<?php

namespace Koneko\VuexyAdmin\Application\Cache\Builders;

use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Application\CoreModule;
use Koneko\VuexyAdmin\Application\Cache\Services\KonekoVarsService;

/**
 * 🎛️ Builder de variables personalizadas para el layout de administración.
 * - Fuente primaria: settings globales (namespace 'koneko.core.layout.admin')
 * - Permite override explícito por usuario autenticado.
 */
class KonekoVuexyCustomizerVarsBuilder extends KonekoAdminVarsBuilder
{
    public function build(): array
    {
        $this->setContext([
            'component' => CoreModule::COMPONENT,
            'group'     => 'layout',
            'sub_group' => 'vuexy',
        ]);

        $this->setScope('customizer');

        return parent::build();
    }
}
