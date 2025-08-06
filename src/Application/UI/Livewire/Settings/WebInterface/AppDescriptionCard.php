<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\WebInterface;

use Koneko\VuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Livewire\Component;

class AppDescriptionCard extends Component
{
    private $targetNotify = "#app-description-card-card .notification-container";
    private $group   = 'layout';
    private $section = 'admin';

    public $app_name,
        $title,
        $description;

    private function settings(): KonekoSettingManager
    {
        return settings()->context($this->group, $this->section);
    }

    public function mount()
    {
        $this->loadForm();
    }

    public function save()
    {
        $this->validate([
            'app_name'    => 'required|string|max:32',
            'title'       => 'required|string|max:64',
            'description' => 'nullable|string|max:255',
        ]);

        // Guardar título del sitio en configuraciones
        $this->settings()->set(trim($this->app_name), 'app_name');
        $this->settings()->set(trim($this->title), 'title');
        $this->settings()->set(trim($this->description), 'description');

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
        $this->app_name    = $this->settings()->get('app_name')?? config('koneko.app_name');
        $this->title       = $this->settings()->get('title')?? config('koneko.title');
        $this->description = $this->settings()->get('description')?? config('koneko.description');
    }

    public function render()
    {
        return view('vuexy-admin::livewire.settings.web-interface.app-description-card');
    }
}
