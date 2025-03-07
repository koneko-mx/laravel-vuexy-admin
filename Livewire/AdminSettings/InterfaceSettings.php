<?php

namespace Koneko\VuexyAdmin\Livewire\AdminSettings;

use Livewire\Component;
use Koneko\VuexyAdmin\Services\AdminTemplateService;
use Koneko\VuexyAdmin\Services\GlobalSettingsService;

class InterfaceSettings extends Component
{
    private $targetNotify = "#interface-settings-card .notification-container";

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
        $this->loadSettings();
    }


    public function loadSettings()
    {
        $adminTemplateService = app(AdminTemplateService::class);

        // Obtener los valores de las configuraciones de la base de datos
        $settings = $adminTemplateService->getVuexyCustomizerVars();

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

    public function save()
    {
        $this->validate([
            'vuexy_maxQuickLinks' => 'required|integer|min:2|max:20',
        ]);

        $globalSettingsService = app(GlobalSettingsService::class);

        // Guardar configuraciones
        $globalSettingsService->updateSetting('config.vuexy.custom.myLayout', $this->vuexy_myLayout);
        $globalSettingsService->updateSetting('config.vuexy.custom.myTheme', $this->vuexy_myTheme);
        $globalSettingsService->updateSetting('config.vuexy.custom.myStyle', $this->vuexy_myStyle);
        $globalSettingsService->updateSetting('config.vuexy.custom.hasCustomizer', $this->vuexy_hasCustomizer);
        $globalSettingsService->updateSetting('config.vuexy.custom.displayCustomizer', $this->vuexy_displayCustomizer);
        $globalSettingsService->updateSetting('config.vuexy.custom.contentLayout', $this->vuexy_contentLayout);
        $globalSettingsService->updateSetting('config.vuexy.custom.navbarType', $this->vuexy_navbarType);
        $globalSettingsService->updateSetting('config.vuexy.custom.footerFixed', $this->vuexy_footerFixed);
        $globalSettingsService->updateSetting('config.vuexy.custom.menuFixed', $this->vuexy_menuFixed);
        $globalSettingsService->updateSetting('config.vuexy.custom.menuCollapsed', $this->vuexy_menuCollapsed);
        $globalSettingsService->updateSetting('config.vuexy.custom.headerType', $this->vuexy_headerType);
        $globalSettingsService->updateSetting('config.vuexy.custom.showDropdownOnHover', $this->vuexy_showDropdownOnHover);
        $globalSettingsService->updateSetting('config.vuexy.custom.authViewMode', $this->vuexy_authViewMode);
        $globalSettingsService->updateSetting('config.vuexy.custom.maxQuickLinks', $this->vuexy_maxQuickLinks);

        $globalSettingsService->clearSystemConfigCache();

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

    public function clearCustomConfig()
    {
        $globalSettingsService = app(GlobalSettingsService::class);

        $globalSettingsService->clearVuexyConfig();

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


    public function render()
    {
        return view('vuexy-admin::livewire.admin-settings.interface-settings');
    }
}
