<?php

namespace Koneko\VuexyAdmin\Livewire\VuexyAdmin;

use Livewire\Component;
use Koneko\VuexyAdmin\Services\AdminTemplateService;
use Koneko\VuexyAdmin\Services\GlobalSettingsService;
use Koneko\VuexyAdmin\Services\SettingsService;

class VuexyInterfaceSettings extends Component
{
    private $targetNotify = "#interface-settings-card .notification-container";

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

        $this->resetForm();
    }


    public function save()
    {
        $this->validate([
            'vuexy_maxQuickLinks' => 'required|integer|min:2|max:20',
        ]);

        // Guardar configuraciones utilizando SettingsService
        $SettingsService = app(SettingsService::class);

        $SettingsService->set('config.vuexy.custom.myLayout', $this->vuexy_myLayout, null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.myTheme', $this->vuexy_myTheme, null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.myStyle', $this->vuexy_myStyle, null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.hasCustomizer', $this->vuexy_hasCustomizer, null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.displayCustomizer', ($this->vuexy_hasCustomizer? $this->vuexy_displayCustomizer: false), null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.contentLayout', $this->vuexy_contentLayout, null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.navbarType', ($this->vuexy_myLayout == 'vertical' ? $this->vuexy_navbarType: null), null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.footerFixed', $this->vuexy_footerFixed, null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.menuFixed', ($this->vuexy_myLayout == 'vertical' ? $this->vuexy_menuFixed: null), null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.menuCollapsed', ($this->vuexy_myLayout == 'vertical' ? $this->vuexy_menuCollapsed: null), null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.headerType', ($this->vuexy_myLayout == 'horizontal' ? $this->vuexy_headerType: null), null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.showDropdownOnHover', ($this->vuexy_myLayout == 'horizontal' ? $this->vuexy_showDropdownOnHover: null), null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.authViewMode', $this->vuexy_authViewMode, null, 'vuexy-admin');
        $SettingsService->set('config.vuexy.custom.maxQuickLinks', $this->vuexy_maxQuickLinks, null, 'vuexy-admin');

        // Elimina la Cache de Configuraciones
        app(GlobalSettingsService::class)->clearSystemConfigCache();

        // Refrescar el componente actual
        $this->dispatch('clearLocalStoregeTemplateCustomizer');

        // Notificación de éxito
        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.',
            deferReload: true
        );
    }

    public function clearCustomConfig()
    {
        // Elimina las claves config.vuexy.* para cargar los valores por defecto
        app(GlobalSettingsService::class)->clearVuexyConfig();

        // Refrescar el componente actual
        $this->dispatch('clearLocalStoregeTemplateCustomizer');

        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.',
            deferReload: true
        );
    }

    public function resetForm()
    {
        // Obtener los valores de las configuraciones de la base de datos
        $settings = app(AdminTemplateService::class)->getVuexyCustomizerVars();

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
        return view('vuexy-admin::livewire.vuexy.interface-settings');
    }
}
