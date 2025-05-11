<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\WebInterface;

use Livewire\Component;

class AppDescriptionCard extends Component
{
    private $targetNotify = "#app-description-card-card .notification-container";

    public $app_name,
        $title,
        $description;

    public function mount()
    {
        $this->loadForm();
    }

    public function save()
    {
        $this->validate([
            'app_name'    => 'required|string|max:255',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        // Guardar título del sitio en configuraciones
        settings()->self()->set('app_name', $this->app_name);
        settings()->self()->set('title', $this->title);
        settings()->self()->set('description', $this->description);

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
        $this->title       = settings()->self()->get('title')?? config('koneko.title');
        $this->description = settings()->self()->get('description')?? config('koneko.description');
        $this->app_name    = settings()->self()->get('app_name')?? config('koneko.app_name');
    }

    public function render()
    {
        return view('vuexy-admin::livewire.settings.web-interface.app-description-card');
    }
}
