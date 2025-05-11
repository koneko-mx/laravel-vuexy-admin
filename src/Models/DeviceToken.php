<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Koneko\VuexyAdmin\Database\Factories\DeviceTokenFactory;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasEscalator,HasFlagler,HasReviewer,HasUser};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

class DeviceToken extends Model
{
    use HasFactory;
    use HasVuexyModelMetadata;
    use HasUser,
        HasReviewer,
        HasFlagler,
        HasEscalator;

    protected $table = 'device_tokens';

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'token de dispositivo';
    public string $focusColumnOnOpen = 'user_id';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'user_id',
        'token',
        'platform',
        'client',
        'device_info',
        'location',
        'last_used_at',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ===================== FACTORY =====================

    protected static function newFactory(): DeviceTokenFactory
    {
        return DeviceTokenFactory::new();
    }
}
