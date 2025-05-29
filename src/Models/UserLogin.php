<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Support\Traits\Audit\HasUser;
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use Koneko\VuexyAdmin\Support\Traits\Geolocation\HasGeolocation;

class UserLogin extends Model
{
    use HasVuexyModelMetadata;
    use HasUser;
    use HasGeolocation;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'acceso de usuario';
    public string $focusColumnOnOpen = 'user_id';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'user_id',
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
        'login_success',
        'logout_at',
        'logout_reason',
        'additional_info',
    ];

    protected $casts = [
        'is_proxy'       => 'boolean',
        'login_success'  => 'boolean',
        'additional_info'=> 'array',
        'logout_at'      => 'datetime',
    ];

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return "{$this->user?->getDisplayName()} - {$this->ip_address}";
    }

    // ===================== ACCESSORS =====================

    public function getSessionDurationAttribute(): ?int
    {
        if ($this->logout_at && $this->created_at) {
            return $this->logout_at->diffInSeconds($this->created_at);
        }

        return null;
    }

    public function getSessionDurationFormattedAttribute(): ?string
    {
        $seconds = $this->session_duration;
        if ($seconds === null) return null;

        return gmdate('H:i:s', $seconds);
    }

    // ===================== HELPERS =====================

    /**
     * Cierra el último inicio de sesión activo para un usuario.
     *
     * @param int $userId ID del usuario
     * @param string $reason Razón del cierre (por defecto: 'manual_logout')
     * @return bool True si se cerró el inicio de sesión, false si no se encontró un inicio de sesión activo
     */
    public static function closeLastActiveLoginForUser(int $userId, string $reason = 'manual_logout'): bool
    {
        $userLogin = static::where('user_id', $userId)
            ->whereNull('logout_at')
            ->latest()
            ->first();

        if (!$userLogin) {
            return false;
        }

        return $userLogin->update([
            'logout_at' => now(),
            'logout_reason' => $reason,
        ]);
    }
}
