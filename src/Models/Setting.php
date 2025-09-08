<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Enums\Settings\SettingValueType;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasCreator,HasUpdater,HasUser};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

class Setting extends Model
{
    use HasVuexyModelMetadata;
    use HasUser, HasCreator, HasUpdater;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'key';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'configuración';
    public string $focusColumnOnOpen = 'key';

    // ===================== ATRIBUTOS BASE =====================

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'namespace',
        'environment',
        'component',
        'scope',
        'scope_id',
        'group',
        'section',
        'sub_group',
        'key_name',
        'is_file',
        'is_encrypted',
        'is_config',
        'is_track_usage',
        'is_should_cache',
        'is_active',
        'mime_type',
        'file_name',
        'encryption_algorithm',
        'encryption_key',
        'encryption_rotated_at',
        'expires_at',
        'usage_count',
        'last_used_at',
        'cache_ttl',
        'cache_expires_at',
        'description',
        'hint',
        'value_string',
        'value_integer',
        'value_boolean',
        'value_float',
        'value_text',
        'value_binary',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scope_id'              => 'integer',
        'is_file'               => 'boolean',
        'is_encrypted'          => 'boolean',
        'is_config'             => 'boolean',
        'is_track_usage'        => 'boolean',
        'is_should_cache'       => 'boolean',
        'is_active'             => 'boolean',
        'expires_at'            => 'datetime',
        'encryption_rotated_at' => 'datetime',
        'cache_ttl'             => 'integer',
        'cache_expires_at'      => 'datetime',
        'value_integer'         => 'integer',
        'value_boolean'         => 'boolean',
        'value_float'           => 'float',
        'created_by'            => 'integer',
    ];

    // ===================== BOOT =====================

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->created_by ??= Auth::id());
        static::saving(fn($m)   => $m->updated_by = Auth::id());
        static::updating(function (self $model) {
            $original = $model->getOriginal();

            foreach ([
                'key','namespace','environment',
                'scope','scope_id',
                'component','group','section','sub_group',
                'key_name',
            ] as $locked) {
                if ($model->$locked !== $original[$locked]) {
                    throw new \RuntimeException("El campo '{$locked}' no puede ser modificado una vez creado.");
                }
            }
        });
    }

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return collect([
            $this->key,
            $this->component ? "Componente: {$this->component}" : null,
            $this->scope_id ? "Scope: {$this->scope_id}" : null,
        ])->filter()->implode(' | ');
    }

    public function getValueAttribute(): mixed
    {
        if (!app()->runningInConsole() && !Schema::hasTable($this->getTable())) {
            return null;
        }

        return $this->resolveValueAndTrackUsage();
    }

    public function getDecryptedValueAttribute(): mixed
    {
        return $this->getDecryptedValue();
    }

    public function getDecryptedValue(bool $asArray = true): mixed
    {
        if (!$this->is_encrypted) {
            return $this->decode($this->value, $asArray);
        }

        return $this->decryptValue($asArray);
    }

    // ===================== SETTERS =====================

    public function setValueAttribute($value): void
    {
        foreach ($this->encodeValue($value) as $key => $val) {
            $this->$key = $val;
        }
    }

    protected function isJson(mixed $value): bool
    {
        if (!is_string($value)) return false;
        $value = trim($value);
        return Str::startsWith($value, ['{', '[']) && json_validate($value);
    }

    // ===================== HELPERS =====================

    protected function decode(mixed $value, bool $asArray = true): mixed
    {
        return $this->isJson($value)
            ? json_decode(trim($value), $asArray)
            : $value;
    }

    protected function encodeValue(mixed $value): array
    {
        foreach (SettingValueType::cases() as $type) {
            $field = "value_{$type->value}";
            $this->$field = null;
        }

        if ($this->is_encrypted) {
            $key       = $this->getEncryptionKey();
            $algorithm = $this->encryption_algorithm ?? 'AES-256-CBC';
            $ivLength  = openssl_cipher_iv_length($algorithm);
            $iv        = random_bytes($ivLength);
            $payload   = (is_scalar($value) || $value === null)
                ? (string) $value
                : json_encode($value, JSON_UNESCAPED_UNICODE);
            $cipher    = openssl_encrypt($payload, $algorithm, $key, 0, $iv);
            if ($cipher === false) {
                throw new \RuntimeException("Error al cifrar el valor para '{$this->key}'.");
            }
            return ['value_text' => base64_encode($iv . $cipher)];
        }

        return match (true) {
            is_string($value)                     => [strlen($value) > 250 ? 'value_text' : 'value_string' => $value],
            is_int($value)                        => ['value_integer' => $value],
            is_bool($value)                       => ['value_boolean' => $value],
            is_float($value)                      => ['value_float'   => $value],
            is_array($value), is_object($value)   => ['value_text' => json_encode($value, JSON_UNESCAPED_UNICODE)],
            default                               => []
        };
    }

    protected function getEncryptionKey(): string
    {
        $key = $this->encryption_key ?? config('app.key');
        if (empty($key)) {
            throw new \LogicException("No se ha definido una clave de encriptación para el setting '{$this->key}'.");
        }

        $decoded = Str::startsWith($key, 'base64:')
            ? base64_decode(substr($key, 7), true)
            : $key;

        if (!$decoded || !is_string($decoded)) {
            throw new \RuntimeException("La clave de encriptación es inválida o no puede ser decodificada.");
        }

        if (strlen($decoded) < 16) {
            throw new \RuntimeException("La clave de encriptación es demasiado corta (mínimo 16 bytes).");
        }

        if (!Str::startsWith($key, 'base64:') && !ctype_print($key)) {
            throw new \RuntimeException("La clave de encriptación contiene caracteres no válidos.");
        }

        return substr(hash('sha256', $decoded, true), 0, 32); // 32 bytes para AES-256
    }

    protected function decryptValue(bool $asArray = true): mixed
    {
        $encoded   = $this->value_text ?? $this->value_string;
        $key       = $this->getEncryptionKey();
        $algorithm = $this->encryption_algorithm ?? 'AES-256-CBC';

        try {
            $raw      = base64_decode($encoded, true);
            $ivLength = openssl_cipher_iv_length($algorithm);
            $iv       = substr($raw, 0, $ivLength);
            $cipher   = substr($raw, $ivLength);

            $decrypted = openssl_decrypt($cipher, $algorithm, $key, 0, $iv);
            if ($decrypted === false) {
                throw new \RuntimeException("Error al descifrar el valor para '{$this->key}'.");
            }

            return $this->decode($decrypted, $asArray);
        } catch (\Throwable $e) {
            logger()->error('❌ Error al descifrar valor de setting.', [
                'key' => $this->key,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function markForKeyRotation(): void
    {
        $this->encryption_rotated_at = now();
        $this->save();
    }

    public function getResolvedValue(bool $decrypt = false, bool $asArray = true): mixed
    {
        return $decrypt ? $this->getDecryptedValue($asArray) : $this->resolveValueAndTrackUsage();
    }

    protected function resolveValueAndTrackUsage(): mixed
    {
        foreach (SettingValueType::cases() as $type) {
            $field = "value_{$type->value}";
            $raw   = $this->$field;

            if (!is_null($raw)) {
                if ($this->is_track_usage) {
                    $this->incrementUsage();
                }
                return $this->decode($raw);
            }
        }
        return null;
    }

    public function incrementUsage(): void
    {
        $this->update([
            'usage_count'  => DB::raw('COALESCE(usage_count,0)+1'),
            'last_used_at' => now(),
        ]);
    }
}
