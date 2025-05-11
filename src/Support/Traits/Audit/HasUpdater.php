<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Audit;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUpdater
{
    /**
     * Relación con el usuario que actualizó el registro.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Alias del nombre del usuario que actualizó el registro.
     *
     * @return string
     */
    public function getUpdaterFullNameAttribute(): string
    {
        return $this->updater->full_name ?? 'Unknown';
    }
}
