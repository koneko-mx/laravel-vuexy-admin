<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Audit;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCreator
{
    /**
     * Relación con el usuario que creó el registro.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alias del nombre del usuario que creó el registro.
     *
     * @return string
     */
    public function getCreatorFullNameAttribute(): string
    {
        return $this->creator->full_name ?? 'Unknown';
    }
}
