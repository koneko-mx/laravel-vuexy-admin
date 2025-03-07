<?php

namespace Koneko\VuexyAdmin\Livewire\AdminSettings;

use Livewire\Component;
use Koneko\VuexyAdmin\Services\GlobalSettingsService;

class MailSenderResponseSettings extends Component
{
    private $targetNotify = "#mail-sender-response-settings-card .notification-container";

    public $from_address,
        $from_name,
        $reply_to_method,
        $reply_to_email,
        $reply_to_name;

    protected $listeners = ['saveMailSenderResponseSettings' => 'save'];

    const REPLY_EMAIL_CREATOR = 1;
    const REPLY_EMAIL_SENDER  = 2;
    const REPLY_EMAIL_CUSTOM  = 3;

    public $reply_email_options = [
        self::REPLY_EMAIL_CREATOR => 'Responder al creador del documento',
        self::REPLY_EMAIL_SENDER  => 'Responder a quien envía el documento',
        self::REPLY_EMAIL_CUSTOM  => 'Definir dirección de correo electrónico',
    ];


    public function mount()
    {
        $this->loadSettings();
    }


    public function loadSettings()
    {
        $globalSettingsService = app(GlobalSettingsService::class);

        // Obtener los valores de las configuraciones de la base de datos
        $settings = $globalSettingsService->getMailSystemConfig();

        $this->from_address = $settings['from']['address'];
        $this->from_name  = $settings['from']['name'];
        $this->reply_to_method = $settings['reply_to']['method'];
        $this->reply_to_email  = $settings['reply_to']['email'];
        $this->reply_to_name   = $settings['reply_to']['name'];
    }

    public function save()
    {
        $this->validate([
            'from_address' => 'required|email',
            'from_name'  => 'required|string|max:255',
            'reply_to_method' => 'required|string|max:255',
        ], [
            'from_address.required' => 'El campo de correo electrónico es obligatorio.',
            'from_address.email' => 'El formato del correo electrónico no es válido.',
            'from_name.required' => 'El nombre es obligatorio.',
            'from_name.string' => 'El nombre debe ser una cadena de texto.',
            'from_name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'reply_to_method.required' => 'El método de respuesta es obligatorio.',
            'reply_to_method.string' => 'El método de respuesta debe ser una cadena de texto.',
            'reply_to_method.max' => 'El método de respuesta no puede tener más de 255 caracteres.',
        ]);

        if ($this->reply_to_method == self::REPLY_EMAIL_CUSTOM) {
            $this->validate([
                'reply_to_email' => ['required', 'email'],
                'reply_to_name'  => ['required', 'string', 'max:255'],
            ], [
                'reply_to_email.required' => 'El correo de respuesta es obligatorio.',
                'reply_to_email.email' => 'El formato del correo de respuesta no es válido.',
                'reply_to_name.required' => 'El nombre de respuesta es obligatorio.',
                'reply_to_name.string' => 'El nombre de respuesta debe ser una cadena de texto.',
                'reply_to_name.max' => 'El nombre de respuesta no puede tener más de 255 caracteres.',
            ]);
        }

        $globalSettingsService = app(GlobalSettingsService::class);

        // Guardar título del App en configuraciones
        $globalSettingsService->updateSetting('mail.from.address', $this->from_address);
        $globalSettingsService->updateSetting('mail.from.name', $this->from_name);
        $globalSettingsService->updateSetting('mail.reply_to.method', $this->reply_to_method);
        $globalSettingsService->updateSetting('mail.reply_to.email', $this->reply_to_method == self::REPLY_EMAIL_CUSTOM ? $this->reply_to_email : '');
        $globalSettingsService->updateSetting('mail.reply_to.name', $this->reply_to_method == self::REPLY_EMAIL_CUSTOM ? $this->reply_to_name : '');

        $globalSettingsService->clearMailSystemConfigCache();

        $this->loadSettings();

        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.',
        );
    }

    public function render()
    {
        return view('vuexy-admin::livewire.admin-settings.mail-sender-response-settings');
    }
}
