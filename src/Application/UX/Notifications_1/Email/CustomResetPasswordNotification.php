<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Notifications;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Koneko\VuexyAdmin\Models\Setting;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Crea una nueva instancia de notificación.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Configura el canal de la notificación.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Configura el mensaje de correo.
     */
    public function toMail($notifiable)
    {
        try {
            // Cargar configuración SMTP desde la base de datos
            $this->loadDynamicMailConfig();

            $resetUrl = url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset()
            ], false));

            $appTitle      = Setting::withVirtualValue()->where('key', 'website_title')->first()->value ?? Config::get('koneko.appTitle');
            $imageBase64   = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/img/logo/koneko-04.png')));
            $expireMinutes = Config::get('auth.passwords.' . Config::get('auth.defaults.passwords') . '.expire', 60);

            Config::set('app.name', $appTitle);

            return (new MailMessage)
                ->subject("Restablece tu contraseña - {$appTitle}")
                ->markdown('vuexy-admin::notifications.email', [ // Usar tu plantilla del módulo
                    'greeting' => "Hola {$notifiable->name}",
                    'introLines' => [
                        'Estás recibiendo este correo porque solicitaste restablecer tu contraseña.',
                    ],
                    'actionText' => 'Restablecer contraseña',
                    'actionUrl' => $resetUrl,
                    'outroLines' => [
                        "Este enlace expirará en {$expireMinutes} minutos.",
                        'Si no solicitaste este cambio, no se requiere realizar ninguna acción.',
                    ],
                    'displayableActionUrl' => $resetUrl, // Para el subcopy
                    'image' => $imageBase64, // Imagen del logo
                ]);

            /*
            */
        } catch (\Exception $e) {
            // Registrar el error
            Log::error('Error al enviar el correo de restablecimiento: ' . $e->getMessage());

            // Retornar un mensaje alternativo
            return (new MailMessage)
                ->subject('Restablece tu contraseña')
                ->line('Ocurrió un error al enviar el correo. Por favor, intenta de nuevo más tarde.');
        }
    }

    /**
     * Cargar configuración SMTP desde la base de datos.
     */
    protected function loadDynamicMailConfig()
    {
        try {
            $smtpConfig = Setting::where('key', 'LIKE', 'mail_%')
                ->withVirtualValue()
                ->pluck('value', 'key');

            if ($smtpConfig->isEmpty()) {
                throw new Exception('No SMTP configuration found in the database.');
            }

            Config::set('mail.mailers.smtp.host', $smtpConfig['mail_mailers_smtp_host'] ?? null);
            Config::set('mail.mailers.smtp.port', $smtpConfig['mail_mailers_smtp_port'] ?? null);
            Config::set('mail.mailers.smtp.username', $smtpConfig['mail_mailers_smtp_username'] ?? null);
            Config::set(
                'mail.mailers.smtp.password',
                isset($smtpConfig['mail_mailers_smtp_password'])
                    ? Crypt::decryptString($smtpConfig['mail_mailers_smtp_password'])
                    : null
            );
            Config::set('mail.mailers.smtp.encryption', $smtpConfig['mail_mailers_smtp_encryption'] ?? null);
            Config::set('mail.from.address', $smtpConfig['mail_from_address'] ?? null);
            Config::set('mail.from.name', $smtpConfig['mail_from_name'] ?? null);
        } catch (Exception $e) {
            Log::error('SMTP Configuration Error: ' . $e->getMessage());
            // Opcional: Puedes lanzar la excepción o manejarla de otra manera.
            throw new Exception('Error al cargar la configuración SMTP.');
        }
    }
}
