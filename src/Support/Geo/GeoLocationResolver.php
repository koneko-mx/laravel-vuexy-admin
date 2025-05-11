<?php

namespace Koneko\VuexyAdmin\Support\Geo;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;

class GeoLocationResolver
{
    public static function resolve(string $ip): array
    {
        try {
            $reader = new Reader(public_path('vendor/geoip/GeoLite2-City.mmdb'));
            $record = $reader->city($ip);

            return [
                'country'         => $record->country->name ?? null,
                'region'          => $record->mostSpecificSubdivision->name ?? null,
                'city'            => $record->city->name ?? null,
                'lat'             => $record->location->latitude ?? null,
                'lng'             => $record->location->longitude ?? null,
                'device_type'     => null,
                'browser'         => null,
                'browser_version' => null,
                'os'              => null,
                'os_version'      => null,
            ];
        } catch (\Throwable $e) {
            Log::warning("[GeoLocationResolver] Falló la geolocalización para IP {$ip}: {$e->getMessage()}");
            return [];
        }
    }
}
