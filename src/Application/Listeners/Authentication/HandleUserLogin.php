<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Listeners\Authentication;

use Illuminate\Auth\Events\Login;
use Jenssegers\Agent\Agent;
use GeoIp2\Database\Reader;
use Koneko\VuexyAdmin\Models\UserLogin;
use Illuminate\Support\Facades\Log;

class HandleUserLogin
{
    public function handle(Login $event): void
    {
        $request = request();

        // Extraer información avanzada
        $loginData = $this->collectLoginData($event, $request);

        // Registrar Login
        UserLogin::create($loginData);

        // Actualizar información de último login en usuario
        $event->user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Opcional: Enviar notificación por email
        //$this->notifyUser($event->user);
    }

    /**
     * Extrae y prepara datos para el registro de login.
     */
    protected function collectLoginData(Login $event, $request): array
    {
        $agent = new Agent();
        $ip = $request->ip();

        // Información básica
        $data = [
            'user_id'     => $event->user->id,
            'ip_address'  => $ip,
            'user_agent'  => $request->userAgent(),
            'login_success' => true, // Asumimos que al llegar aquí es exitoso
        ];

        // Información del dispositivo
        $data['device_type'] = $agent->device();
        $data['browser'] = $agent->browser();
        $data['browser_version'] = $agent->version($agent->browser());
        $data['os'] = $agent->platform();
        $data['os_version'] = $agent->version($agent->platform());

        // Información de ubicación
        try {
            $geoData = $this->getGeoIpData($ip);
            $data = array_merge($data, $geoData);

        } catch (\Exception $e) {
            Log::warning("No se pudo obtener GeoIP para IP: {$ip} - {$e->getMessage()}");
        }

        // Información adicional (headers, etc.)
        $data['additional_info'] = json_encode([
            'headers' => $request->headers->all(),
        ]);

        return $data;
    }

    /**
     * Obtiene datos de geolocalización usando MaxMind GeoIP2.
     */
    protected function getGeoIpData(string $ip): array
    {
        $reader = new Reader(public_path('vendor/geoip/GeoLite2-City.mmdb'));
        $record = $reader->city($ip);

        return [
            'country'  => $record->country->name ?? null,
            'region'   => $record->mostSpecificSubdivision->name ?? null,
            'city'     => $record->city->name ?? null,
            'lat'      => $record->location->latitude ?? null,
            'lng'      => $record->location->longitude ?? null,
            'is_proxy' => $this->detectProxy($ip),
        ];
    }

    /**
     * Detecta si una IP es Proxy/VPN mediante integración externa (opcional).
     */
    protected function detectProxy(string $ip): bool
    {
        // TODO: Integrar API externa para detección de Proxy/VPN.
        return false; // Por defecto, asumimos no proxy.
    }

    /**
     * Enviar notificación por correo al usuario (opcional).
     */
    protected function notifyUser($user): void
    {
        //Mail::to($user->email)->send(new LoginNotification($user));
    }
}
