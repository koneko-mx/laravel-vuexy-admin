<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Koneko\VuexyAdmin\Application\Enums\PermissionGroup\PermissionGroupType;
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use Illuminate\Support\Facades\App;

class PermissionGroup extends Model
{
    use HasVuexyModelMetadata;

    protected $table = 'permission_groups';

    protected $fillable = [
        'module_register',
        'parent_id',
        'type',
        'module',
        'grupo',
        'sub_grupo',
        'name',
        'ui_metadata',
        'priority',
    ];

    protected $casts = [
        'type'        => PermissionGroupType::class,
        'name'        => 'array',
        'ui_metadata' => 'array',
    ];

    // ===================== RELACIONES =====================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(PermissionMeta::class, 'group_id');
    }

    // ===================== METADATOS =====================

    public string $sortColumn        = 'display_order';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'grupo de permiso';
    public string $focusColumnOnOpen = 'group_name';

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        $locale = App::getLocale();
        $name = $this->name;

        if (is_array($name)) {
            return $name[$locale] ?? $name['es'] ?? ($this->sub_grupo ?: $this->grupo);
        }

        return $name ?? ($this->sub_grupo ?: $this->grupo);
    }
}
