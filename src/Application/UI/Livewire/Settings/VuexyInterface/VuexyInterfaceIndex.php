<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\VuexyInterface;

use Koneko\VuexyAdmin\Application\Events\Settings\VuexyCustomizerSettingsUpdated;
use Koneko\VuexyAdmin\Application\Cache\Builders\KonekoAdminVarsBuilder;
use Livewire\Component;

class VuexyInterfaceIndex extends Component
{
    public $uniqueId;

    public $vuexy_myLayout,
        $vuexy_myTheme,
        $vuexy_myStyle,
        $vuexy_hasCustomizer,
        $vuexy_displayCustomizer,
        $vuexy_contentLayout,
        $vuexy_navbarType,
        $vuexy_footerFixed,
        $vuexy_menuFixed,
        $vuexy_menuCollapsed,
        $vuexy_headerType,
        $vuexy_showDropdownOnHover,
        $vuexy_authViewMode,
        $vuexy_maxQuickLinks;

    public function mount()
    {
        $this->uniqueId = uniqid();

        $this->loadForm();
    }

    public function save()
    {
        $this->validate([
            'vuexy_maxQuickLinks' => 'required|integer|min:2|max:20',
        ]);

        event(new VuexyCustomizerSettingsUpdated([
            'myLayout'            => $this->vuexy_myLayout,
            'myTheme'             => $this->vuexy_myTheme,
            'myStyle'             => $this->vuexy_myStyle,
            'hasCustomizer'       => $this->vuexy_hasCustomizer,
            'displayCustomizer'   => $this->vuexy_displayCustomizer,
            'contentLayout'       => $this->vuexy_contentLayout,
            'navbarType'          => $this->vuexy_navbarType,
            'footerFixed'         => $this->vuexy_footerFixed,
            'menuFixed'           => $this->vuexy_menuFixed,
            'menuCollapsed'       => $this->vuexy_menuCollapsed,
            'headerType'          => $this->vuexy_headerType,
            'showDropdownOnHover' => $this->vuexy_showDropdownOnHover,
            'authViewMode'        => $this->vuexy_authViewMode,
            'maxQuickLinks'       => $this->vuexy_maxQuickLinks,
        ]));

        $this->dispatch('refreshAndNotify');
    }


    public function clearCustomConfig()
    {
        // Elimina las claves koneko.admin.vuexy.* para cargar los valores por defecto
        KonekoAdminVarsBuilder::deleteVuexyCustomizerVars();

        // Refrescar el componente actual
        $this->dispatch('refreshAndNotify');
    }

    public function loadForm()
    {
        // Obtener los valores de las configuraciones de la base de datos
        $settings = app(KonekoAdminVarsBuilder::class)->getVuexyCustomizerVars();

        $this->vuexy_myLayout            = $settings['myLayout'];
        $this->vuexy_myTheme             = $settings['myTheme'];
        $this->vuexy_myStyle             = $settings['myStyle'];
        $this->vuexy_hasCustomizer       = $settings['hasCustomizer'];
        $this->vuexy_displayCustomizer   = $settings['displayCustomizer'];
        $this->vuexy_contentLayout       = $settings['contentLayout'];
        $this->vuexy_navbarType          = $settings['navbarType'];
        $this->vuexy_footerFixed         = $settings['footerFixed'];
        $this->vuexy_menuFixed           = $settings['menuFixed'];
        $this->vuexy_menuCollapsed       = $settings['menuCollapsed'];
        $this->vuexy_headerType          = $settings['headerType'];
        $this->vuexy_showDropdownOnHover = $settings['showDropdownOnHover'];
        $this->vuexy_authViewMode        = $settings['authViewMode'];
        $this->vuexy_maxQuickLinks       = $settings['maxQuickLinks'];
    }

    public function render()
    {
        return view('vuexy-admin::livewire.settings.vuexy-interface.index');
    }
}
