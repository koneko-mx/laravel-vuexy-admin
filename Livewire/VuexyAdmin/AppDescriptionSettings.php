<?php

namespace Koneko\VuexyAdmin\Livewire\VuexyAdmin;

use Livewire\Component;
use Koneko\VuexyAdmin\Services\SettingsService;
use Koneko\VuexyAdmin\Services\AdminTemplateService;

class AppDescriptionSettings extends Component
{
    private $targetNotify = "#app-description-settings-card .notification-container";

    public $app_name,
        $title,
        $description;

    public function mount()
    {
        $this->resetForm();
    }

    public function save()
    {
        $this->validate([
            'app_name'    => 'required|string|max:255',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        // Guardar título del sitio en configuraciones
        $SettingsService = app(SettingsService::class);

        $SettingsService->set('admin.app_name', $this->app_name, null, 'vuexy-admin');
        $SettingsService->set('admin.title', $this->title, null, 'vuexy-admin');
        $SettingsService->set('admin.description', $this->description, null, 'vuexy-admin');

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

        $this->app_name    = $settings['app_name'];
        $this->title       = $settings['title'];
        $this->description = $settings['description'];
    }

    public function render()
    {
        return view('vuexy-admin::livewire.vuexy.app-description-settings');
    }
}
