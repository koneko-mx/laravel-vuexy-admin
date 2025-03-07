<?php

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;
use Koneko\VuexyAdmin\Notifications\CustomResetPasswordNotification;

if (trait_exists(\Koneko\VuexyContacts\Traits\HasContactsAttributes::class)) {
    trait DynamicContactsAttributes {
        use \Koneko\VuexyContacts\Traits\HasContactsAttributes;
    }
} else {
    trait DynamicContactsAttributes {}
}

class User extends Authenticatable implements MustVerifyEmail, AuditableContract
{
    use HasRoles,
        HasApiTokens,
        HasFactory,
        Notifiable,
        TwoFactorAuthenticatable,
        Auditable,
        DynamicContactsAttributes;

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

    public function updateProfilePhoto(UploadedFile $image_avatar)
    {
        try {
            // Verificar si el archivo existe
            if (!file_exists($image_avatar->getRealPath()))
                throw new \Exception('El archivo no existe en la ruta especificada.');

            if (!in_array($image_avatar->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                throw new \Exception('El formato del archivo debe ser JPG o PNG.');

            // Directorio donde se guardarán los avatares
            $avatarDisk = self::AVATAR_DISK;
            $avatarPath = self::PROFILE_PHOTO_DIR;
            $avatarName = uniqid('avatar_') . '.png'; // Nombre único para el avatar

            // Crear la instancia de ImageManager
            $driver = config('image.driver', 'gd');
            $manager = new ImageManager($driver);

            // Crear el directorio si no existe
            if (!Storage::disk($avatarDisk)->exists($avatarPath))
                Storage::disk($avatarDisk)->makeDirectory($avatarPath);

            // Leer la imagen
            $image = $manager->read($image_avatar->getRealPath());

            // crop the best fitting 5:3 (600x360) ratio and resize to 600x360 pixel
            $image->cover(self::AVATAR_WIDTH, self::AVATAR_HEIGHT);

            // Guardar la imagen en el disco de almacenamiento gestionado por Laravel
            Storage::disk($avatarDisk)->put($avatarPath . '/' . $avatarName, $image->toPng(indexed: true));

            // Elimina el avatar existente si hay uno
            $this->deleteProfilePhoto();

            // Update the user's profile photo path
            $this->forceFill([
                'profile_photo_path' => $avatarName,
            ])->save();
        } catch (\Exception $e) {
            throw new \Exception('Ocurrió un error al actualizar el avatar. ' . $e->getMessage());
        }
    }

    public function deleteProfilePhoto()
    {
        if (!empty($this->profile_photo_path)) {
            $avatarDisk = self::AVATAR_DISK;

            Storage::disk($avatarDisk)->delete($this->profile_photo_path);

            $this->forceFill([
                'profile_photo_path' => null,
            ])->save();
        }
    }

    public function getAvatarColor()
    {
        // Selecciona un color basado en el id del usuario
        return self::AVATAR_COLORS[$this->id % count(self::AVATAR_COLORS)];
    }

    public static function getAvatarImage($name, $color, $background, $size)
    {
        $avatarDisk = self::AVATAR_DISK;
        $directory  = self::INITIAL_AVATAR_DIR;
        $initials   = self::getInitials($name);

        $cacheKey    = "avatar-{$initials}-{$color}-{$background}-{$size}";
        $path        = "{$directory}/{$cacheKey}.png";
        $storagePath = storage_path("app/public/{$path}");

        // Verificar si el avatar ya está en caché
        if (Storage::disk($avatarDisk)->exists($path))
            return response()->file($storagePath);

        // Crear el avatar
        $image = self::createAvatarImage($name, $color, $background, $size);

        // Guardar en el directorio de iniciales
        Storage::disk($avatarDisk)->put($path, $image->toPng(indexed: true));

        // Retornar la imagen directamente
        return response()->file($storagePath);
    }

    private static function createAvatarImage($name, $color, $background, $size)
    {
        // Usar la configuración del driver de imagen
        $driver = config('image.driver', 'gd');
        $manager = new ImageManager($driver);

        $initials = self::getInitials($name);

        // Obtener la ruta correcta de la fuente dentro del paquete
        $fontPath = __DIR__ . '/../storage/fonts/OpenSans-Bold.ttf';

        // Crear la imagen con fondo
        $image = $manager->create($size, $size)
            ->fill($background);

        // Escribir texto en la imagen
        $image->text(
            $initials,
            $size / 2,  // Centrar horizontalmente
            $size / 2,  // Centrar verticalmente
            function (FontFactory $font) use ($color, $size, $fontPath) {
                $font->file($fontPath);
                $font->size($size * 0.4);
                $font->color($color);
                $font->align('center');
                $font->valign('middle');
            }
        );

        return $image;
    }

    public static function getInitials($name)
    {
        // Manejar casos de nombres vacíos o nulos
        if (empty($name))
            return 'NA';

        // Usar array_map para mayor eficiencia
        $initials = implode('', array_map(function ($word) {
            return mb_substr($word, 0, 1);
        }, explode(' ', $name)));

        $initials = substr($initials, 0, self::INITIAL_MAX_LENGTH);

        return strtoupper($initials);
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path)
            return Storage::url(self::PROFILE_PHOTO_DIR . '/' . $this->profile_photo_path);

        // Generar URL del avatar por iniciales
        $name       = urlencode($this->fullname);
        $color      = ltrim($this->getAvatarColor(), '#');
        $background = ltrim(self::AVATAR_BACKGROUND, '#');
        $size       = (self::AVATAR_WIDTH + self::AVATAR_HEIGHT) / 2;

        return url("/admin/usuario/avatar?name={$name}&color={$color}&background={$background}&size={$size}");
    }

    public function getFullnameAttribute()
    {
        return trim($this->name . ' ' . $this->last_name);
    }

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
    public static function getUsersListWithInactive(int $includeUserId = null, array $filters = []): array
    {
        $query = self::query();

        // Filtro por tipo de usuario
        if (isset($filters['type'])) {
            switch ($filters['type']) {
                case 'partner':
                    $query->where('is_partner', 1);
                    break;
                case 'employee':
                    $query->where('is_employee', 1);
                    break;
                case 'prospect':
                    $query->where('is_prospect', 1);
                    break;
                case 'customer':
                    $query->where('is_customer', 1);
                    break;
                case 'provider':
                    $query->where('is_provider', 1);
                    break;
                case 'user':
                    $query->where('is_user', 1);
                    break;
            }
        }

        // Incluir usuarios activos o el usuario desactivado seleccionado
        $query->where(function ($q) use ($filters, $includeUserId) {
            if (isset($filters['status'])) {
                $q->where('status', $filters['status']);
            }

            if ($includeUserId) {
                $q->orWhere('id', $includeUserId);
            }
        });

        // Formatear los datos como id => "Nombre Apellido"
        return $query->pluck(\DB::raw("CONCAT(name, ' ', IFNULL(last_name, ''))"), 'id')->toArray();
    }


    /**
     * Relations
     */

    // User who created this user
    public function creator()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ENABLED;
    }

}
