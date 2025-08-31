<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Enums\Settings\SettingValueType;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasCreator,HasDeleter,HasUpdater,HasUser};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

class Setting extends Model
{
    use HasVuexyModelMetadata;
    use HasUser,
        HasCreator,
        HasUpdater,
        HasDeleter;

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
        'component',    // Nombre de Componente o proyecto
        'module',       // composerName de módulo Autocalculado
        'scope',
        'scope_id',
        'group',        // Grupo de configuraciones
        'section',
        'sub_group',    // Sub grupo de configuraciones
        'key_name',     // Nombre de la clave de configuraciones

        'is_system',    // Indica si es un setting de sistema
        'is_sensitive', // Marca datos sensibles (ej. datos personales, claves API). Puede ocultarse en UI o logs.
        'is_file',      // Indica si el setting es un archivo
        'is_encrypted', // Si el valor está cifrado (para secretos, tokens, passwords).
        'is_config',    // Indica si el setting es un archivo de configuración
        'is_track_usage', // Indica si el contador de uso está habilitado.
        'is_should_cache', // Indica si el setting debe ser cacheado.
        'is_editable',  // Permite o bloquea edición desde la UI (útil para settings de solo lectura).
        'is_active',    // Permite activar/desactivar la aplicación de un setting sin eliminarlo.

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

        'description',      // Descripción legible para el setting (ayuda en la UI).
        'hint',             // Breve consejo o ayuda contextual (tooltip en la UI).

        'value_string',
        'value_integer',
        'value_boolean',
        'value_float',
        'value_text',
        'value_binary',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'scope_id'         => 'integer',
        'is_system'        => 'boolean',
        'is_encrypted'     => 'boolean',
        'is_config'        => 'boolean',
        'is_sensitive'     => 'boolean',
        'is_editable'      => 'boolean',
        'is_active'        => 'boolean',
        'expires_at'       => 'datetime',
        'encryption_rotated_at' => 'datetime',
        'should_cache'     => 'boolean',
        'cache_ttl'        => 'integer',
        'cache_expires_at' => 'datetime',
        'value_integer'    => 'integer',
        'value_boolean'    => 'boolean',
        'value_float'      => 'float',
        'created_by'       => 'integer',
        'deleted_by'       => 'integer',
        'updated_by'       => 'integer',
    ];


    // ===================== BOOT =====================

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->created_by ??= Auth::id());
        static::saving(fn($m)   => $m->updated_by = Auth::id());
        static::updating(function (self $model) {
            $original = $model->getOriginal();

            foreach ([
                'key', 'namespace',
                'environment',
                'scope', 'scope_id',
                'component', 'module', 'group', 'sub_group',
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
            $this->module ? "Module: {$this->module}" : null,
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


    // ===================== SCOPES =====================




    // ===================== HELPERS =====================

    /**
     * Decodifica un valor almacenado en un setting.
     */
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
            $cipher    = openssl_encrypt($value, $algorithm, $key, 0, $iv);

            if ($cipher === false) {
                throw new \RuntimeException("Error al cifrar el valor para '{$this->key}'.");
            }

            return ['value_text' => base64_encode($iv . $cipher)];
        }

        return match (true) {
            is_string($value) => [strlen($value) > 250 ? 'value_text' : 'value_string' => $value],
            is_int($value)    => ['value_integer' => $value],
            is_bool($value)   => ['value_boolean' => $value],
            is_float($value)  => ['value_float'   => $value],
            is_array($value), is_object($value) => ['value_text' => json_encode($value, JSON_UNESCAPED_UNICODE)],
            default           => []
        };
    }

    /**
     * Obtiene y valida la clave de encriptación para este setting.
     *
     * @throws \LogicException Si no hay clave definida.
     * @throws \RuntimeException Si la clave es inválida o insegura.
     */
    protected function getEncryptionKey(): string
    {
        // Obtener la clave del modelo o fallback de configuración
        $key = $this->encryption_key ?? config('app.key');

        if (empty($key)) {
            throw new \LogicException("No se ha definido una clave de encriptación para el setting '{$this->key}'.");
        }

        // Decodificar si está en formato base64
        $decoded = Str::startsWith($key, 'base64:') ? base64_decode(substr($key, 7), true) : $key;

        if (!$decoded || !is_string($decoded)) {
            throw new \RuntimeException("La clave de encriptación es inválida o no puede ser decodificada.");
        }

        // Validar longitud mínima por seguridad
        if (strlen($decoded) < 16) {
            throw new \RuntimeException("La clave de encriptación es demasiado corta (mínimo 16 bytes).");
        }

        // Validar que no contenga caracteres no imprimibles si no es base64
        if (!Str::startsWith($key, 'base64:') && !ctype_print($key)) {
            throw new \RuntimeException("La clave de encriptación contiene caracteres no válidos.");
        }

        return $decoded;
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

    /**
     * Marca el setting para rotación de clave.
     */
    public function markForKeyRotation(): void
    {
        $this->encryption_rotated_at = now();
        $this->save();
    }

    /**
     * Obtiene el valor resuelto del setting.
     */
    public function getResolvedValue(bool $decrypt = false, bool $asArray = true): mixed
    {
        return $decrypt ? $this->getDecryptedValue($asArray) : $this->resolveValueAndTrackUsage();
    }

    /**
     * Resuelve el valor del setting y actualiza el uso.
     */
    protected function resolveValueAndTrackUsage(): mixed
    {
        foreach (SettingValueType::cases() as $type) {
            $field = "value_{$type->value}";
            $raw   = $this->$field;

            if (!is_null($raw)) {
                if ($this->track_usage) {
                    $this->incrementUsage();
                }

                return $this->decode($raw);
            }
        }

        return null;
    }

    /**
     * Incrementa el contador de uso y actualiza la fecha de última utilización.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }
}
