<?php

namespace Koneko\VuexyAdmin\Livewire\VuexyAdmin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Koneko\VuexyAdmin\Services\AdminSettingsService;
use Koneko\VuexyAdmin\Services\AdminTemplateService;

class LogoOnDarkBgSettings extends Component
{
    use WithFileUploads;

    private $targetNotify = "#logo-on-dark-bg-settings-card .notification-container";

    public $admin_image_logo_dark,
        $upload_image_logo_dark;

    public function mount()
    {
        $this->resetForm();
    }

    public function save()
    {
        $this->validate([
            'upload_image_logo_dark' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:20480',
        ]);

        // Procesar favicon si se ha cargado una imagen
        app(AdminSettingsService::class)->processAndSaveImageLogo($this->upload_image_logo_dark, 'dark');

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

        $this->upload_image_logo_dark = null;
        $this->admin_image_logo_dark  = $settings['image_logo']['large_dark'];
    }

    public function render()
    {
        return view('vuexy-admin::livewire.vuexy.logo-on-dark-bg-settings');
    }
}
