<?php

namespace Koneko\VuexyAdmin\Support\Logger;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Koneko\VuexyAdmin\Application\Enums\SecurityEvents\SecurityEventStatus;
use Koneko\VuexyAdmin\Application\Enums\SecurityEvents\SecurityEventType;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoComponentContextRegistrar;
use Koneko\VuexyAdmin\Models\SecurityEvent;
use Koneko\VuexyAdmin\Support\Geo\GeoLocationResolver;

class KonekoSecurityLogger
{
    public static function log(
        SecurityEventType|string $type,
        ?Request $request = null,
        ?int $userId = null,
        array $payload = [],
        bool $isProxy = false
    ): ?SecurityEvent {
        $request ??= request();

        $agent = new Agent(null, $request->userAgent());
        $geo = GeoLocationResolver::resolve($request->ip());

        return SecurityEvent::create([
            'module'          => KonekoComponentContextRegistrar::currentComponent() ?? 'unknown',
            'user_id'         => $userId ?? Auth::id(),
            'event_type'      => (string) $type,
            'status'          => SecurityEventStatus::NEW,
            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'device_type'     => $agent->device() ?: ($geo['device_type'] ?? null),
            'browser'         => $agent->browser() ?: ($geo['browser'] ?? null),
            'browser_version' => $agent->version($agent->browser()) ?: ($geo['browser_version'] ?? null),
            'os'              => $agent->platform() ?: ($geo['os'] ?? null),
            'os_version'      => $agent->version($agent->platform()) ?: ($geo['os_version'] ?? null),
            'country'         => $geo['country'] ?? null,
            'region'          => $geo['region'] ?? null,
            'city'            => $geo['city'] ?? null,
            'lat'             => $geo['lat'] ?? null,
            'lng'             => $geo['lng'] ?? null,
            'is_proxy'        => $isProxy,
            'url'             => $request->fullUrl(),
            'http_method'     => $request->method(),
            'payload'         => $payload,
        ]);
    }
}
