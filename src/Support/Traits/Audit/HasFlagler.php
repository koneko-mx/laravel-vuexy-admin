<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Audit;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait HasFlagler
 *
 * Permite acceder al usuario señalizador.
 * Debe usarse típicamente en el modelo User.
 */
trait HasFlagler
{
    /**
     * Relación con el usuario señalizador.
     */
    public function flager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    /**
     * Alias del nombre del usuario señalizador.
     *
     * @return string
     */
    public function getFlaggerFullNameAttribute(): string
    {
        return $this->flager->full_name ?? 'Unknown';
    }
}
