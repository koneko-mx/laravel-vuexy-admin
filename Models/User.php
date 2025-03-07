<?php

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;
use Koneko\VuexyAdmin\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail, AuditableContract
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable,
        TwoFactorAuthenticatable, Auditable;

    // the list of status values that can be stored in table
    const STATUS_ENABLED  = 10;
    const STATUS_DISABLED = 1;
    const STATUS_REMOVED  = 0;

    const AVATAR_DISK        = 'public';
    const PROFILE_PHOTO_DIR  = 'profile-photos';
    const INITIAL_AVATAR_DIR = 'initial-avatars';
    const INITIAL_MAX_LENGTH = 4;

    const AVATAR_WIDTH  = 512;
    const AVATAR_HEIGHT = 512;
    const AVATAR_BACKGROUND = '#EBF4FF'; // Fondo por defecto
    const AVATAR_COLORS = [
        '#7367f0',
        '#808390',
        '#28c76f',
        '#ff4c51',
        '#ff9f43',
        '#00bad1',
        '#4b4b4b',
    ];

    /**
     * List of names for each status.
     * @var array
     */
    public static $statusList = [
        self::STATUS_ENABLED  => 'Habilitado',
        self::STATUS_DISABLED => 'Deshabilitado',
        self::STATUS_REMOVED  => 'Eliminado',
    ];

    /**
     * List of names for each status.
     * @var array
     */
    public static $statusListClass = [
        self::STATUS_ENABLED  => 'success',
        self::STATUS_DISABLED => 'warning',
        self::STATUS_REMOVED  => 'danger',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'profile_photo_path',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Nombre de la etiqueta para generar Componentes
     *
     * @var string
     */
    public $tagName = 'User';

    /**
     * Nombre de la columna que contiee el nombre del registro
     *
     * @var string
     */
    public $columnNameLabel = 'full_name';

    /**
     * Nombre singular del registro.
     *
     * @var string
     */
    public $singularName = 'usuario';

    /**
     * Nombre plural del registro.
     *
     * @var string
     */
    public $pluralName = 'usuarios';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Attributes to include in the Audit.
     *
     * @var array
     */
    protected $auditInclude = [
        'name',
        'email',
    ];

    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return trim($this->name . ' ' . $this->last_name);
    }

    /**
     * Get the initials of the user's full name.
     *
     * @return string
     */
    public function getInitialsAttribute()
    {
        return self::getInitials($this->fullname);
    }

    /**
     * Envía la notificación de restablecimiento de contraseña.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        // Usar la notificación personalizada
        $this->notify(new CustomResetPasswordNotification($token));
    }

    /**
     * Obtener usuarios activos con una excepción para incluir un usuario específico desactivado.
     *
     * @param array $filters Filtros opcionales como ['type' => 'user', 'status' => 1]
     * @param int|null $includeUserId ID de usuario específico a incluir aunque esté inactivo
     * @return array
     */
    public static function getUsersListWithInactive($includeUserId = null, array $filters = []): array
    {
        $query = self::query();

        // Filtro por tipo de usuario dinámico
        $tipoUsuarios = [
            'partner'   => 'is_partner',
            'employee'  => 'is_employee',
            'prospect'  => 'is_prospect',
            'customer'  => 'is_customer',
            'provider'  => 'is_provider',
            'user'      => 'is_user',
        ];

        if (isset($filters['type']) && isset($tipoUsuarios[$filters['type']])) {
            $query->where($tipoUsuarios[$filters['type']], 1);
        }

        // Filtrar por estado o incluir usuario inactivo
        $query->where(function ($q) use ($filters, $includeUserId) {
            if (isset($filters['status'])) {
                $q->where('status', $filters['status']);
            }

            if ($includeUserId) {
                $q->orWhere('id', $includeUserId);
            }
        });

        return $query->pluck(\DB::raw("CONCAT(name, ' ', IFNULL(last_name, ''))"), 'id')->toArray();
    }

    /**
     * User who created this user
     */
    public function creator()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    /**
     * Check if the user is active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ENABLED;
    }

}
