<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\User;

trait HasUserFlags
{
    // ===================== SCOPES =====================

    public function scopeIsUser($query)
    {
        return $query->where('users.is_user', 1);
    }

    public function scopeIsNotUser($query)
    {
        return $query->whereNot('users.is_user');
    }
}
