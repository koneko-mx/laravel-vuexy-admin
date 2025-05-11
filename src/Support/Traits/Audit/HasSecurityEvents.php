<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Audit;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Koneko\VuexyAdmin\Models\SecurityEvent;

/**
 * Trait HasSecurityEvents
 *
 * Permite acceder a los eventos de seguridad asociados al modelo.
 * Debe usarse típicamente en el modelo User.
 */
trait HasSecurityEvents
{
    /**
     * Relación con eventos de seguridad del usuario.
     */
    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class, 'user_id');
    }

    /**
     * Eventos recientes (por ejemplo, últimos 30 días)
     */
    public function recentSecurityEvents(): HasMany
    {
        return $this->securityEvents()->where('created_at', '>=', now()->subDays(30));
    }

    /**
     * Último evento de seguridad del usuario
     */
    public function latestSecurityEvent()
    {
        return $this->securityEvents()->latest('created_at')->first();
    }
}
