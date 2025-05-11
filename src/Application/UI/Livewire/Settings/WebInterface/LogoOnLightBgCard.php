<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\WebInterface;

use Koneko\VuexyAdmin\Application\Cache\VuexyVarsBuilderService;
use Koneko\VuexyAdmin\Application\System\VuexyAdminImageHandlerService;
use Livewire\Component;
use Livewire\WithFileUploads;

class LogoOnLightBgCard extends Component
{
    use WithFileUploads;

    private $targetNotify = "#logo-on-light-bg-card-card .notification-container";

    public $admin_image_logo,
        $upload_image_logo;

    public function mount()
    {
        $this->loadForm();
    }

    public function save()
    {
        $this->validate([
            'upload_image_logo' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:20480',
        ]);

        // Procesar favicon si se ha cargado una imagen
        app(VuexyAdminImageHandlerService::class)->processAndSaveImageLogo($this->upload_image_logo);

        // Limpiamos la cache
        app(VuexyVarsBuilderService::class)->clearCache();

        // Recargamos el formulario
        $this->loadForm();

        // Notificación de éxito
        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.'
        );
    }

    public function loadForm()
    {
        // Obtener los valores de las configuraciones de la base de datos
        $settings = app(VuexyVarsBuilderService::class)->getAdminVars();

        $this->upload_image_logo = null;
        $this->admin_image_logo  = $settings['image_logo']['large'];
    }

    public function render()
    {
        return view('vuexy-admin::livewire.settings.web-interface.logo-on-light-bg-card');
    }
}
