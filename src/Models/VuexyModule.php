<?php

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasCreator,HasDeleter,HasUpdater};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

class KonekoModule extends Model
{
    use HasVuexyModelMetadata;
    use HasCreator,
        HasUpdater,
        HasDeleter;

    protected $table = 'vuexy_modules';

    protected $primaryKey = 'slug';
    public $incrementing  = false;
    protected $keyType    = 'string';

    // ===================== METADATOS =====================

    public string $sortColumn        = 'name';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'módulo';
    public string $focusColumnOnOpen = 'name';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'slug',
        'name',
        'vendor',
        'installed_module_id',
        'version',
        'build_version',
        'type',
        'provider',
        'tags',
        'metadata',
        'is_enabled',
        'is_installed',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_enabled'    => 'boolean',
        'is_installed'  => 'boolean',
        'tags'          => 'array',
        'metadata'      => 'array',
    ];

    // ===================== RELATIONS =====================

    public function runtime(): HasOne
    {
        return $this->hasOne(KonekoModule::class, 'installed_module_id');
    }


    // ===================== ACCESSORS =====================

    public function getDisplayNameAttribute(): string
    {
        return $this->name . ($this->version ? " ({$this->version})" : '');
    }

    public function buildVersionFormatted(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? \Carbon\Carbon::createFromFormat('YmdHis', $value)->toDateTimeString() : null,
        );
    }

    // ===================== SCOPES =====================

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeInstalled($query)
    {
        return $query->where('is_installed', true);
    }
}
