<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Audit;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait HasUser
 *
 * Permite acceder al usuario.
 * Debe usarse típicamente en el modelo User.
 */
trait HasUser
{
    /**
     * Relación con el usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias del nombre del usuario.
     *
     * @return string
     */
    public function getUserFullNameAttribute(): string
    {
        return $this->user->full_name ?? 'Unknown';
    }
}
