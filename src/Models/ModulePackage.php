<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Koneko\VuexyAdmin\Application\Enums\Modules\ModulePackageSourceType;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasCreator,HasDeleter,HasUpdater};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

class ModulePackage extends Model
{
    use HasVuexyModelMetadata;
    use HasCreator,
        HasUpdater,
        HasDeleter;

    protected $table = 'module_packages';

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'paquete de módulo';
    public string $focusColumnOnOpen = 'name';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'keywords',
        'author_name',
        'author_email',
        'source_url',
        'composer_url',
        'cover_image',
        'readme_path',
        'source_type',
        'zip_available',
        'composer',
        'repository_type',
        'active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'source_type'   => ModulePackageSourceType::class,
        'zip_available' => 'boolean',
        'active'        => 'boolean',
        'keywords'      => 'array',
    ];

    // ===================== RELATIONS =====================

    /**
     * Obtiene instalaciones relacionadas a este paquete.
     */
    public function installations(): HasMany
    {
        return $this->hasMany(InstalledModule::class);
    }

    // ===================== ACCESSORS =====================

    /**
     * Ruta completa hacia la imagen de portada.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? asset($this->cover_image) : null;
    }

    /**
     * Ruta completa hacia el README.md del módulo.
     */
    public function getReadmeFullPathAttribute(): ?string
    {
        return $this->readme_path ? base_path($this->readme_path) : null;
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOfficial($query)
    {
        return $query->where('official', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('repository_type', 'private');
    }
}
