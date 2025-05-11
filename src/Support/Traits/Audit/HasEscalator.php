<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Audit;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait HasEscalator
 *
 * Permite acceder al usuario escalador.
 * Debe usarse típicamente en el modelo User.
 */
trait HasEscalator
{
    /**
     * Relación con el usuario escalador.
     */
    public function escalator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_by');
    }

    /**
     * Alias del nombre del usuario escalador.
     *
     * @return string
     */
    public function getEscalatorFullNameAttribute(): string
    {
        return $this->escalator->full_name ?? 'Unknown';
    }
}
