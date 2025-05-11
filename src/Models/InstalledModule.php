<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasCreator,HasDeleter,HasUpdater};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

class InstalledModule extends Model
{
    use HasVuexyModelMetadata;
    use HasCreator,
        HasUpdater,
        HasDeleter;

    protected $table = 'installed_modules';

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'módulo instalado';
    public string $focusColumnOnOpen = 'module_package_id';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'module_package_id',
        'slug',
        'name',
        'version',
        'install_path',
        'enabled',
        'installed_at',
        'last_checked_at',
        'install_options',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'enabled'          => 'boolean',
        'installed_at'     => 'datetime',
        'last_checked_at'  => 'datetime',
        'install_options'  => 'array',
    ];

    // ===================== RELATIONS =====================

    public function package(): BelongsTo
    {
        return $this->belongsTo(ModulePackage::class, 'module_package_id');
    }

    // ===================== SCOPES =====================

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
