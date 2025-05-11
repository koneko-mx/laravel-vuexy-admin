<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\User;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Koneko\VuexyAdmin\Models\UserLogin;

/**
 * Trait HasUserLogins
 *
 * Permite acceder a los logins del usuario.
 * Debe usarse típicamente en el modelo User.
 */
trait HasUserLogins
{
    /**
     * Relación con logins del usuario.
     */
    public function userLogins(): HasMany
    {
        return $this->hasMany(UserLogin::class, 'user_id');
    }

    /**
     * Logins recientes (por ejemplo, últimos 30 días)
     */
    public function recentUserLogins(): HasMany
    {
        return $this->userLogins()->where('created_at', '>=', now()->subDays(30));
    }

    /**
     * Último login del usuario
     */
    public function latestUserLogin()
    {
        return $this->userLogins()->latest('created_at')->first();
    }
}
