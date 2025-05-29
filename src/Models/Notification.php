<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Koneko\VuexyAdmin\Database\Factories\NotificationFactory;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasEmitter,HasUpdater,HasUser};

/**
 * Modelo de notificaciones internas para VuexyAdmin y Website.
 *
 * @property int $id
 * @property string $type
 * @property string $title
 * @property string|null $body
 * @property array|null $data
 * @property string|null $action_url
 * @property bool $is_read
 * @property bool $is_dismissed
 * @property bool $is_deleted
 * @property int|null $user_id
 * @property int|null $emitted_by
 * @property int|null $updated_by
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Notification extends Model
{
    use HasFactory;
    use HasUser,
        HasEmitter,
        HasUpdater;

    protected $table = 'notifications';

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'notificación';
    public string $focusColumnOnOpen = 'module';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'module',
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'action_url',
        'is_read',
        'read_at',
        'is_dismissed',
        'is_deleted',
        'emitted_by',
        'updated_by',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_dismissed' => 'boolean',
        'is_deleted' => 'boolean',
        'read_at' => 'datetime',
    ];


    // ===================== SCOPES =====================

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false)->where('is_dismissed', false);
    }

    // ===================== FACTORY =====================

    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }
}
