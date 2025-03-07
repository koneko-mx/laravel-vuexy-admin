<?php

namespace Koneko\VuexyAdmin\Livewire\AdminSettings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Koneko\VuexyAdmin\Services\AdminSettingsService;
use Koneko\VuexyAdmin\Services\AdminTemplateService;

class ApplicationSettings extends Component
{
    use WithFileUploads;

    private $targetNotify = "#application-settings-card .notification-container";

    public $admin_app_name,
        $admin_image_logo,
        $admin_image_logo_dark;

    public $upload_image_logo,
        $upload_image_logo_dark;

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings($clearcache = false)
    {
        $this->upload_image_logo = null;
        $this->upload_image_logo_dark = null;

        $adminTemplateService = app(AdminTemplateService::class);

        if ($clearcache) {
            $adminTemplateService->clearAdminVarsCache();
        }

        // Obtener los valores de las configuraciones de la base de datos
        $settings = $adminTemplateService->getAdminVars();

        $this->admin_app_name        = $settings['app_name'];
        $this->admin_image_logo      = $settings['image_logo']['large'];
        $this->admin_image_logo_dark = $settings['image_logo']['large_dark'];
    }

    public function save()
    {
        $this->validate([
            'admin_app_name' => 'required|string|max:255',
            'upload_image_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:20480',
            'upload_image_logo_dark' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:20480',
        ]);

        $adminSettingsService = app(AdminSettingsService::class);

        // Guardar título del App en configuraciones
        $adminSettingsService->updateSetting('admin_app_name', $this->admin_app_name);

        // Procesar favicon si se ha cargado una imagen
        if ($this->upload_image_logo) {
            $adminSettingsService->processAndSaveImageLogo($this->upload_image_logo);
        }

        if ($this->upload_image_logo_dark) {
            $adminSettingsService->processAndSaveImageLogo($this->upload_image_logo_dark, 'dark');
        }

        $this->loadSettings(true);

        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.'
        );
    }

    public function render()
    {
        return view('vuexy-admin::livewire.admin-settings.application-settings');
    }
}
