<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Application\Enums\SecurityEvents\{SecurityEventStatus,SecurityEventType};
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasDeleter,HasUser};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use Koneko\VuexyAdmin\Support\Traits\Helpers\HasGeolocation;

class SecurityEvent extends Model
{
    use HasVuexyModelMetadata;
    use HasUser,
        HasDeleter;
    use HasGeolocation;

    public const EVENT_LOGIN_FAILED  = 'login_failed';
    public const EVENT_LOGIN_SUCCESS = 'login_success';

    public const STATUS_NEW      = 'new';
    public const STATUS_RESOLVED = 'resolved';

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'evento de sistema';
    public string $focusColumnOnOpen = 'user_id';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'module',
        'user_id',
        'event_type',
        'status',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'browser_version',
        'os',
        'os_version',
        'country',
        'region',
        'city',
        'lat',
        'lng',
        'is_proxy',
        'url',
        'http_method',
        'payload',
        'deleted_by',
    ];

    protected $casts = [
        'is_proxy'   => 'boolean',
        'payload'    => 'array',
        'status'     => SecurityEventStatus::class,
        'event_type' => SecurityEventType::class,
    ];

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return "{$this->event_type} - {$this->ip_address} - {$this->status}";
    }

    // ===================== SCOPES =====================

    public function scopeProxy($query)
    {
        return $query->where('is_proxy', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }
}
