<?php

namespace Koneko\VuexyAdmin\Support\Traits\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

trait HasResolvableUser
{
    /**
     * Resuelve el ID de usuario en 4 escenarios:
     *   null   → usuario autenticado (por defecto)
     *   false  → sin usuario (global)
     *   int    → user_id directo
     *   object → Authenticatable
     */
    protected function resolveUserId(Authenticatable|int|null|false $user): ?int
    {
        return match (true) {
            $user === false => null, // Forzar sin usuario
            $user instanceof Authenticatable => $user->getAuthIdentifier(),
            $user === null => Auth::check() ? Auth::id() : null,
            default => $user,
        };
    }

    protected function resolveUser(Authenticatable|int|null|false $user): ?Authenticatable
    {
        return match (true) {
            $user === false => null, // Forzar sin usuario
            $user instanceof Authenticatable => $user,
            $user === null => Auth::check() ? Auth::user() : null,
            default => Auth::findUserById($user),
        };
    }
}
