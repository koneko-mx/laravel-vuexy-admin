<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\WebInterface;

use Koneko\VuexyAdmin\Application\Cache\VuexyVarsBuilderService;
use Koneko\VuexyAdmin\Application\System\VuexyAdminImageHandlerService;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppFaviconCard extends Component
{
    use WithFileUploads;

    private $targetNotify = "#app-favicon-card-card .notification-container";

    public $admin_favicon_16x16,
        $admin_favicon_76x76,
        $admin_favicon_120x120,
        $admin_favicon_152x152,
        $admin_favicon_180x180,
        $admin_favicon_192x192;

    public $upload_image_favicon;

    public function mount()
    {
        $this->loadForm();
    }

    public function save()
    {
        $this->validate([
            'upload_image_favicon' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:20480',
        ]);

        // Procesar favicon
        app(VuexyAdminImageHandlerService::class)->processAndSaveFavicon($this->upload_image_favicon);

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
        return view('vuexy-admin::livewire.settings.web-interface.app-favicon-card');
    }
}
