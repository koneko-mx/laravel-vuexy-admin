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

    const INITIAL_MAX_LENGTH = 3;

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
     * Get the URL for the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/profile-photos/' . $this->profile_photo_path);
        }

        return $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        return route('admin.core.user-profile.avatar', ['name' => $this->fullname]);
    }

    /**
     * Calcula las iniciales a partir del nombre.
     *
     * @param string $name Nombre completo.
     *
     * @return string Iniciales en mayúsculas.
     */
    public static function getInitials($name)
    {
        if (empty($name)) {
            return 'NA';
        }

        $initials = implode('', array_map(function ($word) {
            return mb_substr($word, 0, 1);
        }, explode(' ', $name)));

        return strtoupper(substr($initials, 0, self::INITIAL_MAX_LENGTH));
    }

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
