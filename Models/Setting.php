<?php

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    // ─────────────────────────────────────────────
    // Configuración del modelo
    // ─────────────────────────────────────────────

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'category',
        'user_id',
        'value_string',
        'value_integer',
        'value_boolean',
        'value_float',
        'value_text',
        'value_binary',
        'mime_type',
        'file_name',
        'updated_by',
    ];

    protected $casts = [
        'user_id'        => 'integer',
        'value_integer'  => 'integer',
        'value_boolean'  => 'boolean',
        'value_float'    => 'float',
        'updated_by'     => 'integer',
    ];

    // ─────────────────────────────────────────────
    // Metadatos personalizados para el generador de componentes
    // ─────────────────────────────────────────────

    public string $tagName = 'setting';
    public string $columnNameLabel = 'key';
    public string $singularName = 'Configuración';
    public string $pluralName = 'Configuraciones';

    // ─────────────────────────────────────────────
    // Relaciones
    // ─────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────

    /**
     * Configuraciones para un usuario específico.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Configuraciones globales (sin usuario).
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Incluir columna virtual `value` en la consulta.
     */
    public function scopeWithVirtualValue($query)
    {
        return $query->select(['key', 'value']);
    }

}
