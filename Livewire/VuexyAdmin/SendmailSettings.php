<?php

namespace Koneko\VuexyAdmin\Livewire\VuexyAdmin;

use Livewire\Component;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Koneko\VuexyAdmin\Services\GlobalSettingsService;


class SendmailSettings extends Component
{
    private $targetNotify = "#sendmail-settings-card .notification-container";

    public $change_smtp_settings,
        $host,
        $port,
        $encryption,
        $username,
        $password;

    public $save_button_disabled;

    protected $listeners = [
        'loadSettings',
        'testSmtpConnection',
    ];

    // the list of smtp_encryption values that can be stored in table
    const SMTP_ENCRYPTION_SSL  = 'SSL';
    const SMTP_ENCRYPTION_TLS  = 'TLS';
    const SMTP_ENCRYPTION_NONE = 'none';

    public $encryption_options = [
        self::SMTP_ENCRYPTION_SSL  => 'SSL (Secure Sockets Layer)',
        self::SMTP_ENCRYPTION_TLS  => 'TLS (Transport Layer Security)',
        self::SMTP_ENCRYPTION_NONE => 'Sin encriptación (No recomendado)',
    ];

    public $rules = [
        [
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer',
            'encryption' => 'nullable|string',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ],
        [
            'host.string' => 'El servidor SMTP debe ser una cadena de texto.',
            'host.max' => 'El servidor SMTP no puede exceder los 255 caracteres.',
            'port.integer' => 'El puerto SMTP debe ser un número entero.',
            'encryption.string' => 'El tipo de encriptación SMTP debe ser una cadena de texto.',
            'username.string' => 'El nombre de usuario SMTP debe ser una cadena de texto.',
            'username.max' => 'El nombre de usuario SMTP no puede exceder los 255 caracteres.',
            'password.string' => 'La contraseña SMTP debe ser una cadena de texto.',
            'password.max' => 'La contraseña SMTP no puede exceder los 255 caracteres.',
        ]
    ];


    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $settings = app(GlobalSettingsService::class)->getMailSystemConfig();

        $this->change_smtp_settings = false;
        $this->save_button_disabled = true;

        $this->host       = $settings['mailers']['smtp']['host'];
        $this->port       = $settings['mailers']['smtp']['port'];
        $this->encryption = $settings['mailers']['smtp']['encryption'];
        $this->username   = $settings['mailers']['smtp']['username'];
        $this->password   = null;
    }

    public function save()
    {
        $this->validate($this->rules[0]);

        $globalSettingsService = app(GlobalSettingsService::class);

        // Guardar título del App en configuraciones
        $globalSettingsService->updateSetting('mail.mailers.smtp.host', $this->host);
        $globalSettingsService->updateSetting('mail.mailers.smtp.port', $this->port);
        $globalSettingsService->updateSetting('mail.mailers.smtp.encryption', $this->encryption);
        $globalSettingsService->updateSetting('mail.mailers.smtp.username', $this->username);
        $globalSettingsService->updateSetting('mail.mailers.smtp.password', Crypt::encryptString($this->password));

        $globalSettingsService->clearMailSystemConfigCache();

        $this->loadSettings();

        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.'
        );
    }

    public function testSmtpConnection()
    {
        // Validar los datos del formulario
        $this->validate($this->rules[0]);

        try {
            // Verificar la conexión SMTP
            if ($this->validateSMTPConnection()) {
                $this->save_button_disabled = false;

                $this->dispatch(
                    'notification',
                    target: $this->targetNotify,
                    type: 'success',
                    message: 'Conexión SMTP exitosa, se guardó los cambios exitosamente.',
                );
            }
        } catch (\Exception $e) {
            // Captura y maneja errores de conexión SMTP
            $this->dispatch(
                'notification',
                target: $this->targetNotify,
                type: 'danger',
                message: 'Error en la conexión SMTP: ' . $e->getMessage(),
                delay: 15000  // Timeout personalizado
            );
        }
    }

    private function validateSMTPConnection()
    {
        $dsn = sprintf(
            'smtp://%s:%s@%s:%s?encryption=%s',
            urlencode($this->username),    // Codificar nombre de usuario
            urlencode($this->password),    // Codificar contraseña
            $this->host,        // Host SMTP
            $this->port,        // Puerto SMTP
            $this->encryption   // Encriptación (tls o ssl)
        );

        // Crear el transportador usando el DSN
        $transport = Transport::fromDsn($dsn);

        // Crear el mailer con el transportador personalizado
        $mailer = new Mailer($transport);

        // Enviar un correo de prueba
        $email = (new Email())
            ->from($this->username)  // Dirección de correo del remitente
            ->to(env('MAIL_SANDBOX')) // Dirección de correo de destino
            ->subject(Config::get('app.name') . ' - Correo de prueba')
            ->text('Este es un correo de prueba para verificar la conexión SMTP.');

        // Enviar el correo
        $mailer->send($email);

        return true;
    }


    public function render()
    {
        return view('vuexy-admin::livewire.vuexy.sendmail-settings');
    }
}
