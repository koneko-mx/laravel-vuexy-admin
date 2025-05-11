<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Koneko\VuexyAdmin\Application\Enums\User\UserStatus;
use Koneko\VuexyAdmin\Database\Factories\UserFactory;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasCreator,HasSecurityEvents};
use Koneko\VuexyAdmin\Support\Traits\Flags\{HandlesFlagMigrations,HasFlags};
use Koneko\VuexyAdmin\Support\Traits\Model\{HasVuexyModelMetadata,ExtendableModel};
use Koneko\VuexyAdmin\Application\Traits\User\{HasProfilePhoto,HasUserInitials,HasPasswordReset, HasUserLogins,HasUserFlags};
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, AuditableContract
{
    use HasFactory;
    use HasRoles, HasApiTokens, Notifiable,
        TwoFactorAuthenticatable;
    use HasVuexyModelMetadata,
        ExtendableModel;
    use HandlesFlagMigrations,
        HasFlags;
    use SoftDeletes;
    use Auditable;
    use HasProfilePhoto,
        HasUserInitials,
        HasUserLogins,
        HasSecurityEvents,
        HasPasswordReset,
        HasUserFlags,
        HasCreator;

    // the list of status values that can be stored in table
    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    /**
     * List of names for each status.
     * @var array
     */
    public static $statusList = [
        self::STATUS_ENABLED  => 'Activo',
        self::STATUS_DISABLED => 'Deshabilitado',
    ];

    /**
     * Guard name for Spatie Permissions
     * @var string
     */
    protected string $guard_name = 'web';

    // ===================== METADATOS =====================

    public string $sortColumn        = 'full_name';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'usuario';
    public string $focusColumnOnOpen = 'name';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'profile_photo_path',
        'flags',
        'status',
        'created_by',
    ];

    protected $auditInclude = [
        'name',
        'last_name',
        'email',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'flags',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
        'full_name',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'boolean',
            'flags'             => 'array',
        ];
    }

    // ===================== RELACIONES =====================

    public function systemNotifications(): HasMany
    {
        return $this->hasMany(SystemNotificationUser::class);
    }

    // ===================== GETTERS =====================

    /**
     * Devuelve el nombre completo del usuario.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Métodos para combinar atributos
     */
    public function getFillable(): array
    {
        return array_unique(array_merge(
            $this->fillable,
            $this->getExtendedAttributes('fillable')
        ));
    }

    public function getAuditInclude(): array
    {
        return array_unique(array_merge(
            $this->auditInclude,
            $this->getExtendedAttributes('auditInclude')
        ));
    }


    public function getAllPermissionMetas(): \Illuminate\Support\Collection
    {
        return PermissionMeta::whereIn('id', $this->getAllPermissions()->pluck('id'))->with('group')->get();
    }


    // ===================== ACCESSORS =====================

    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->name} {$this->last_name}");
    }

    /**
     * Devuelve el valor del estado del usuario.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return UserStatus::tryFrom((int) $this->status)?->label() ?? 'Sin estado';
    }

    // ===================== SCOPES =====================

    /**
     * Obtiene todos los usuarios, incluyendo los inactivos.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function scopeActive($query)
    {
        return $query->where('users.status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('users.status', false);
    }

    // ===================== FACTORY =====================

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
