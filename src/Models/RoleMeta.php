<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use Spatie\Permission\Models\Role;

class RoleMeta extends Role
{
    use HasVuexyModelMetadata;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'name';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'rol';
    public string $focusColumnOnOpen = 'name';

    // ===================== ATRIBUTOS BASE =====================

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'ui_metadata',
        'guard_name',
    ];

    protected $casts = [
        'ui_metadata' => 'array',
    ];
}