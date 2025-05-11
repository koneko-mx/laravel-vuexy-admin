<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Koneko\VuexyAdmin\Application\Enums\SystemLog\{LogTriggerType,LogLevel};
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasDeleter,HasUpdater,HasUser};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

class SystemLog extends Model
{
    use HasVuexyModelMetadata;
    use HasUser,
        HasUpdater,
        HasDeleter;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'registro de sistema';
    public string $focusColumnOnOpen = 'module';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'module',
        'user_id',
        'level',
        'message',
        'context',
        'trigger_type',
        'trigger_id',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'context'       => 'array',
        'level'         => LogLevel::class,
        'trigger_type'  => LogTriggerType::class,
    ];

    // ===================== RELACIONES =====================

    public function related_model(): MorphTo
    {
        return $this->morphTo();
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    // ===================== SCOPES =====================

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByTrigger($query, LogTriggerType|string $type)
    {
        return $query->where('trigger_type', (string)$type);
    }

    public function scopeByLevel($query, LogLevel|string $level)
    {
        return $query->where('level', (string)$level);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ===================== MÉTODOS AUXILIARES =====================

    public function getDisplayName(): string
    {
        return "[{$this->module}] {$this->level->value} - {$this->message}";
    }

    public function getShortMessageAttribute(): string
    {
        return str($this->message)->limit(100)->toString();
    }

    public function getContextJsonAttribute(): string
    {
        return json_encode($this->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
