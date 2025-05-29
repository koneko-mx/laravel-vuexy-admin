<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Koneko\VuexyAdmin\Support\Enums\Permissions\PermissionAction;
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

class PermissionMeta extends Permission
{
    use HasVuexyModelMetadata;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'name';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'permiso';
    public string $focusColumnOnOpen = 'name';

    // ===================== ATRIBUTOS BASE =====================

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'group_id',
        'label',
        'ui_metadata',
        'action',
        'guard_name',
    ];

    protected $casts = [
        'label'       => 'array',
        'ui_metadata' => 'array',
        'action'      => PermissionAction::class,
    ];

    // ===================== RELACIONES =====================

    public function group(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class, 'group_id');
    }

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        $locale = App::getLocale();

        // Si la etiqueta viene en múltiples idiomas
        if (is_array($this->label)) {
            return $this->label[$locale] ?? $this->label['es'] ?? $this->name;
        }

        return $this->label ?? $this->name;
    }

    public function getActionLabel(): string
    {
        return $this->action?->label(App::getLocale()) ?? '-';
    }

    public function getGroupDisplayName(): string
    {
        return $this->group?->getDisplayName() ?? 'Sin grupo';
    }

    // ===================== ACCESOR OPCIONAL =====================

    public function actionLabel(): Attribute
    {
        return Attribute::get(fn () => $this->getActionLabel());
    }
}
