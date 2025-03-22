<?php

namespace Koneko\VuexyAdmin\Livewire\VuexyAdmin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Koneko\VuexyAdmin\Services\AdminSettingsService;
use Koneko\VuexyAdmin\Services\AdminTemplateService;

class AppFaviconSettings extends Component
{
    use WithFileUploads;

    private $targetNotify = "#app-favicon-settings-card .notification-container";

    public $admin_favicon_16x16,
        $admin_favicon_76x76,
        $admin_favicon_120x120,
        $admin_favicon_152x152,
        $admin_favicon_180x180,
        $admin_favicon_192x192;

    public $upload_image_favicon;

    public function mount()
    {
        $this->resetForm();
    }

    public function save()
    {
        $this->validate([
            'upload_image_favicon' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:20480',
        ]);

        // Procesar favicon
        app(AdminSettingsService::class)->processAndSaveFavicon($this->upload_image_favicon);

        // Limpiar cache de plantilla
        app(AdminTemplateService::class)->clearAdminVarsCache();

        // Recargamos el formulario
        $this->resetForm();

        // Notificación de éxito
        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.'
        );
    }

    public function resetForm()
    {
        // Obtener los valores de las configuraciones de la base de datos
        $settings = app(AdminTemplateService::class)->getAdminVars();

        $this->upload_image_favicon  = null;
        $this->admin_favicon_16x16   = $settings['favicon']['16x16'];
        $this->admin_favicon_76x76   = $settings['favicon']['76x76'];
        $this->admin_favicon_120x120 = $settings['favicon']['120x120'];
        $this->admin_favicon_152x152 = $settings['favicon']['152x152'];
        $this->admin_favicon_180x180 = $settings['favicon']['180x180'];
        $this->admin_favicon_192x192 = $settings['favicon']['192x192'];
    }

    public function render()
    {
        return view('vuexy-admin::livewire.vuexy.app-favicon-settings');
    }
}
