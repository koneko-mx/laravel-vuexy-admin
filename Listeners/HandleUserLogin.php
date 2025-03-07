<?php

namespace Koneko\VuexyAdmin\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Mail;
use Koneko\VuexyAdmin\Models\UserLogin;

class HandleUserLogin
{
    public function handle(Login $event)
    {
        // Guardar log en base de datos
        UserLogin::create([
            'user_id' => $event->user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        // Actualizar el último login
        $event->user->update(['last_login_at' => now(), 'last_login_ip' => request()->ip()]);

        // Enviar notificación de inicio de sesión
        //Mail::to($event->user->email)->send(new LoginNotification($event->user));
    }
}
