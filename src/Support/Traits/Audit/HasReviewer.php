<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Audit;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait HasReviewer
 *
 * Permite acceder al revisor.
 * Debe usarse típicamente en el modelo User.
 */
trait HasReviewer
{
    /**
     * Relación con el revisor.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Alias del nombre del revisor.
     *
     * @return string
     */
    public function getReviewFullNameAttribute(): string
    {
        return $this->reviewer->full_name ?? 'Unknown';
    }
}
