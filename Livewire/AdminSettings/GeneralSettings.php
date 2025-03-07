<?php

namespace Koneko\VuexyAdmin\Livewire\AdminSettings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Koneko\VuexyAdmin\Services\AdminSettingsService;
use Koneko\VuexyAdmin\Services\AdminTemplateService;

class GeneralSettings extends Component
{
    use WithFileUploads;

    private $targetNotify = "#general-settings-card .notification-container";

    public $admin_title;
    public $admin_favicon_16x16,
        $admin_favicon_76x76,
        $admin_favicon_120x120,
        $admin_favicon_152x152,
        $admin_favicon_180x180,
        $admin_favicon_192x192;

    public $upload_image_favicon;

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings($clearcache = false)
    {
        $this->upload_image_favicon = null;

        $adminTemplateService = app(AdminTemplateService::class);

        if ($clearcache) {
            $adminTemplateService->clearAdminVarsCache();
        }

        // Obtener los valores de las configuraciones de la base de datos
        $settings = $adminTemplateService->getAdminVars();

        $this->admin_title           = $settings['title'];
        $this->admin_favicon_16x16   = $settings['favicon']['16x16'];
        $this->admin_favicon_76x76   = $settings['favicon']['76x76'];
        $this->admin_favicon_120x120 = $settings['favicon']['120x120'];
        $this->admin_favicon_152x152 = $settings['favicon']['152x152'];
        $this->admin_favicon_180x180 = $settings['favicon']['180x180'];
        $this->admin_favicon_192x192 = $settings['favicon']['192x192'];
    }

    public function save()
    {
        $this->validate([
            'admin_title' => 'required|string|max:255',
            'upload_image_favicon' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:20480',
        ]);

        $adminSettingsService = app(AdminSettingsService::class);

        // Guardar título del sitio en configuraciones
        $adminSettingsService->updateSetting('admin_title', $this->admin_title);

        // Procesar favicon si se ha cargado una imagen
        if ($this->upload_image_favicon) {
            $adminSettingsService->processAndSaveFavicon($this->upload_image_favicon);
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
        return view('vuexy-admin::livewire.admin-settings.general-settings');
    }
}
