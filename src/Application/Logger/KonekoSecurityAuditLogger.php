<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Logger;

use GeoIp2\Database\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Koneko\VuexyAdmin\Models\SecurityEvent;

class KonekoSecurityAuditLogger
{
    /**
     * Registra un nuevo evento de seguridad.
     */
    public function logEvent(string $type, Request $request, ?int $userId = null, array $payload = [], bool $isProxy = false)
    {
        $agent = new Agent();
        $ip = $request->ip();

        $geoData = $this->getGeoData($ip);

        SecurityEvent::create([
            'user_id'         => $userId,
            'event_type'      => $type,
            'status'          => 'new',
            'ip_address'      => $ip,
            'user_agent'      => $request->userAgent(),
            'device_type'     => $agent->device(),
            'browser'         => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()),
            'os'              => $agent->platform(),
            'os_version'      => $agent->version($agent->platform()),
            'country'         => $geoData['country'] ?? null,
            'region'          => $geoData['region'] ?? null,
            'city'            => $geoData['city'] ?? null,
            'lat'             => $geoData['latitude'] ?? null,
            'lng'             => $geoData['longitude'] ?? null,
            'is_proxy'        => $isProxy,
            'url'             => $request->fullUrl(),
            'http_method'     => $request->method(),
            'payload'         => json_encode($payload),
        ]);
    }

    /**
     * Obtiene datos de geolocalización usando MaxMind GeoIP2.
     */
    private function getGeoData(string $ip): array
    {
        try {
            $reader = new Reader(public_path('vendor/geoip/GeoLite2-City.mmdb'));
            $record = $reader->city($ip);

            return [
                'country'   => $record->country->name,
                'region'    => $record->mostSpecificSubdivision->name,
                'city'      => $record->city->name,
                'latitude'  => $record->location->latitude,
                'longitude' => $record->location->longitude,
            ];

        } catch (\Exception $e) {
            Log::warning("GeoIP falló para IP: {$ip} - {$e->getMessage()}");
            return [];
        }
    }
}
