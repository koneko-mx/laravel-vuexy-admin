<?php

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Support\Facades\Crypt;

class VaultKey extends Model
{
    use SoftDeletes;

    protected $connection = 'vault';
    protected $table = 'vault_keys';

    protected $fillable = [
        'alias',
        'owner_project',
        'environment',
        'namespace',
        'scope',
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

    // ==================== ACCESSORS ====================

    protected function getEncryptionKey(): string
    {
        return vault_value_key();
    }

    public function getKeyMaterialDecryptedAttribute(): ?string
    {
        try {
            return Crypt::decryptString($this->attributes['key_material']);

        } catch (\Throwable $e) {
            logger()->error('❌ Error al desencriptar key_material.', [
                'alias' => $this->alias,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function getDecodedKeyMaterial(): ?string
    {
        return $this->getKeyMaterialDecryptedAttribute();
    }

    // ==================== MUTATORS ====================

    /*
    public function setKeyMaterialAttribute(string $value): void
    {
        if (empty($value)) {
            throw new \InvalidArgumentException("El valor de la clave no puede ser vacío.");
        }

        $this->attributes['key_material'] = Crypt::encryptString($value);
    }
    */

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProject($query, string $project)
    {
        return $query->where('owner_project', $project);
    }

    public function scopeForEnvironment($query, string $env)
    {
        return $query->where('environment', $env);
    }

    public function scopeWithNamespace($query, string $namespace)
    {
        return $query->where('namespace', $namespace);
    }

    public function scopeWithScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    // ==================== HELPERS ====================

    public function rotateKey(string $newMaterial): void
    {
        $this->key_material = $newMaterial;
        $this->rotated_at = now();
        $this->increment('rotation_count');
    }

    public static function generateRandomKey(int $length = 32): string
    {
        return base64_encode(random_bytes($length));
    }
}
