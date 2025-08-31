<?php

namespace Koneko\VuexyAdmin\Application\Cache\Builders;

use Koneko\VuexyAdmin\Application\CoreModule;

/**
 * 🎛️ Builder de variables personalizadas para el layout de administración.
 * - Fuente primaria: settings globales (namespace 'koneko.core.layout.admin')
 * - Permite override explícito por usuario autenticado.
 */
class KonekoVuexyCustomizerVarsBuilder
{
    private $group     = 'layout';
    private $section   = 'admin';
    private $sub_group = 'vuexy';
    private $key_name  = 'customizer';

    private $config_path = CoreModule::COMPONENT;


    public function get(): array
    {
        return settings()
            ->context($this->group, $this->section, $this->sub_group)
            ->keyName($this->key_name)
            ->remember(fn () => $this->resolveCustomizerVars());
    }

    public function clear(): void
    {
        settings()
            ->context($this->group, $this->section, $this->sub_group)
            ->forgetCache($this->key_name);
    }

    protected function resolveCustomizerVars(): array
    {
        $vars_settings = settings()
            ->context($this->group, $this->section, $this->sub_group)
            ->asArray()
            ->all();
dump($vars_settings);

        $vars_config = config_m()->get('layout.vuexy', []);
dump($vars_config);

        $data = array_merge($vars_config, $vars_settings);

        return [
            'myLayout'            => $data['myLayout'],
            'myTheme'             => $data['myTheme'],
            'myStyle'             => $data['myStyle'],
            'hasCustomizer'       => $data['hasCustomizer'],
            'displayCustomizer'   => $data['displayCustomizer'],
            'contentLayout'       => $data['contentLayout'],
            'navbarType'          => $data['navbarType'],
            'footerFixed'         => $data['footerFixed'],
            'menuFixed'           => $data['menuFixed'],
            'menuCollapsed'       => $data['menuCollapsed'],
            'headerType'          => $data['headerType'],
            'showDropdownOnHover' => $data['showDropdownOnHover'],
            'authViewMode'        => $data['authViewMode'],
            'maxQuickLinks'       => $data['maxQuickLinks'],
        ];
    }
}
