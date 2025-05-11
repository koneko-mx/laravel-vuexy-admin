<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Audit;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasDeleter
{
    /**
     * Relación con el usuario que eliminó el registro.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Alias del nombre del usuario que eliminó el registro.
     *
     * @return string
     */
    public function getDeleterFullNameAttribute(): string
    {
        return $this->deleter->full_name ?? 'Unknown';
    }
}
