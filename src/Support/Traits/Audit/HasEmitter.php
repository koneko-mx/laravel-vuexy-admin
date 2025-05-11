<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Audit;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait HasEmitter
 *
 * Permite acceder al emisor.
 * Debe usarse típicamente en el modelo User.
 */
trait HasEmitter
{
    /**
     * Relación con el emisor.
     */
    public function emitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitted_by');
    }

    /**
     * Alias del nombre del emisor.
     *
     * @return string
     */
    public function getEmitterFullNameAttribute(): string
    {
        return $this->emitter->full_name ?? 'Unknown';
    }
}
