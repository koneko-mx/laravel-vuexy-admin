<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VaultClientKey extends Model
{
    use SoftDeletes;

    protected $connection = 'vault';
    protected $table = 'vault_client_keys';

    protected $fillable = [
        'project_code',
        'client_id',
        'alias',
        'namespace',
        'algorithm',
        'key_material',
        'is_active',
        'is_sensitive',
        'rotated_at',
        'rotation_count',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_sensitive'   => 'boolean',
        'rotated_at'     => 'datetime',
        'rotation_count' => 'integer',
    ];

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProject($query, string $project)
    {
        return $query->where('project_code', $project);
    }

    public function scopeWithNamespace($query, string $namespace)
    {
        return $query->where('namespace', $namespace);
    }

    public function scopeByAlias($query, string $alias)
    {
        return $query->where('alias', $alias);
    }

    // ===================== MÉTODOS =====================

    public function getKeyMaterialDecryptedAttribute(): ?string
    {
        try {
            return decrypt($this->attributes['key_material']);
        } catch (\Throwable $e) {
            logger()->error('[Vault] Falló decrypt key_material.', [
                'alias' => $this->alias,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function rotateKey(string $newMaterial): void
    {
        $this->key_material = encrypt($newMaterial);
        $this->rotated_at = now();
        $this->rotation_count++;
        $this->save();
    }

    public static function generateRandomKey(int $length = 32): string
    {
        return base64_encode(random_bytes($length));
    }
}
