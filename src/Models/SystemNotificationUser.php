<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Koneko\VuexyAdmin\Models\SystemNotification;
use Koneko\VuexyAdmin\Support\Traits\Audit\HasUser;
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

class SystemNotificationUser extends Model
{
    use HasVuexyModelMetadata;
    use HasUser;

    protected $table = 'system_notification_user';

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'notificación de sistema';
    public string $focusColumnOnOpen = 'system_notification_id';


    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'system_notification_id',
        'user_id',
        'is_read',
        'read_at',
        'is_dismissed',
        'dismissed_at',
        'is_confirmed',
        'confirmed_at',
        'confirmation_notes',
    ];

    protected $casts = [
        'is_read'      => 'boolean',
        'is_dismissed' => 'boolean',
        'is_confirmed' => 'boolean',
        'read_at'      => 'datetime',
        'dismissed_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    // ===================== RELATIONS =====================

    public function notification(): BelongsTo
    {
        return $this->belongsTo(SystemNotification::class);
    }
}
