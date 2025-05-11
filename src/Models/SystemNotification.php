<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\{Model,Builder};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Koneko\VuexyAdmin\Application\Enums\SystemNotifications\{SystemNotificationPriority, SystemNotificationType,SystemNotificationScope, SystemNotificationStyle};
use Koneko\VuexyAdmin\Database\Factories\SystemNotificationFactory;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasCreator,HasDeleter,HasUpdater};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class SystemNotification extends Model implements AuditableContract
{
    use HasFactory;
    use HasVuexyModelMetadata;
    use Auditable;
    use HasCreator,
        HasUpdater,
        HasDeleter;

    protected $table = 'system_notifications';

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'notificación de sistema';
    public string $focusColumnOnOpen = 'title';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'scope',
        'type',
        'title',
        'message',
        'style',
        'priority',
        'requires_confirmation',
        'target_area',
        'tags',
        'roles',
        'user_flags',
        'is_active',
        'starts_at',
        'ends_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'scope'       => SystemNotificationScope::class,
        'type'        => SystemNotificationType::class,
        'style'       => SystemNotificationStyle::class,
        'priority'    => SystemNotificationPriority::class,
        'tags'        => 'array',
        'roles'       => 'array',
        'user_flags'  => 'array',
        'is_active'   => 'boolean',
        'requires_confirmation' => 'boolean',
        'starts_at'   => 'datetime',
        'ends_at'     => 'datetime',
    ];

    protected $auditInclude = [
        'scope',
        'style',
        'priority',
        'type',
        'title',
        'message',
        'target_area',
        'tags',
        'roles',
        'user_flags',
        'is_active',
        'requires_confirmation',
        'starts_at',
        'ends_at',
    ];

    // ===================== RELACIONES =====================

    public function userStatuses(): HasMany
    {
        return $this->hasMany(SystemNotificationUser::class, 'system_notification_id');
    }

    // ===================== SCOPES =====================

    public function scopeVisibleNow(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    public function scopeForScope(Builder $query, SystemNotificationScope|string $scope): Builder
    {
        $scope = is_string($scope) ? SystemNotificationScope::from($scope) : $scope;

        return $query->where(function ($q) use ($scope) {
            $q->where('scope', $scope->value)->orWhere('scope', SystemNotificationScope::Both->value);
        });
    }

    public function scopeVisibleToUser(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            $q->whereNull('roles')->orWhereJsonContains('roles', $user->getRoleNames()->toArray());
        })->where(function ($q) use ($user) {
            $q->whereNull('user_flags')->orWhere(function ($sub) use ($user) {
                foreach ($user->flags ?? [] as $flag => $val) {
                    if ($val) {
                        $sub->orWhereJsonContains('user_flags', $flag);
                    }
                }
            });
        });
    }

    // ===================== FACTORY =====================

    protected static function newFactory(): SystemNotificationFactory
    {
        return SystemNotificationFactory::new();
    }
}
