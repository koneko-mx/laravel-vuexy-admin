<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Enums\Settings\{SettingEnvironment, SettingScope, SettingValueType};
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasCreator,HasDeleter,HasUpdater,HasUser};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Setting extends Model implements AuditableContract
{
    use HasVuexyModelMetadata;
    use Auditable;
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
        'environment',  // Entorno de aplicación (prod, dev, test, staging), permite sobrescribir valores según ambiente.
        'scope',        // Define el alcance: global, tenant, branch, user, etc. Útil en arquitecturas multicliente.

        'component',    // Nombre de Componente o proyecto
        'module',       // composerName de módulo Autocalculado
        'group',        // Grupo de configuraciones
        'sub_group',    // Sub grupo de configuraciones
        'key_name',     // Nombre de la clave de configuraciones
        'user_id',      // Usuario (null para globales)

        'is_system',    // Indica si es un setting de sistema
        'is_encrypted', // Si el valor está cifrado (para secretos, tokens, passwords).
        'is_sensitive', // Marca datos sensibles (ej. datos personales, claves API). Puede ocultarse en UI o logs.
        'is_editable',  // Permite o bloquea edición desde la UI (útil para settings de solo lectura).
        'is_active',    // Permite activar/desactivar la aplicación de un setting sin eliminarlo.

        'encryption_key',
        'encryption_algorithm',
        'encryption_rotated_at',

        'description',  // Descripción legible para el setting (ayuda en la UI).
        'hint',         // Breve consejo o ayuda contextual (tooltip en la UI).
        'last_used_at', // Última vez que este setting fue consultado/aplicado.
        'usage_count',  // Contador de veces usado (útil para limpieza de settings obsoletos).

        'value_string',
        'value_integer',
        'value_boolean',
        'value_float',
        'value_text',
        'value_binary',
        'mime_type',
        'file_name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $appends = ['value', 'decrypted_value'];

    protected $auditInclude = [
        'is_system',
        'is_encrypted',
        'is_sensitive',
        'is_editable',
        'is_active',
        'scope',
        'description',
        'hint',
        'last_used_at',
        'usage_count',
        'value_string',
        'value_integer',
        'value_boolean',
        'value_float',
        'value_text',
        'mime_type',
        'file_name',
    ];

    protected $casts = [
        'environment'    => SettingEnvironment::class,
        'scope'          => SettingScope::class,
        'user_id'        => 'integer',
        'is_system'      => 'boolean',
        'is_encrypted'   => 'boolean',
        'is_sensitive'   => 'boolean',
        'is_editable'    => 'boolean',
        'is_active'      => 'boolean',
        'last_used_at'   => 'datetime',
        'usage_count'    => 'integer',
        'value_integer'  => 'integer',
        'value_boolean'  => 'boolean',
        'value_float'    => 'float',
        'created_by'     => 'integer',
        'deleted_by'     => 'integer',
        'updated_by'     => 'integer',
        'encryption_rotated_at' => 'datetime',
    ];

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return collect([
            $this->key,
            $this->module ? "Module: {$this->module}" : null,
            $this->user_id ? "User: {$this->user_id}" : null,
        ])->filter()->implode(' | ');
    }

    public function getValueAttribute(): mixed
    {
        foreach (SettingValueType::cases() as $type) {
            $field = "value_{$type->value}";

            if (!is_null($this->$field)) {
                return $this->decode($this->$field);
            }
        }

        return null;
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

        $encoded = $this->value_text ?? $this->value_string;
        $key = $this->getEncryptionKey();
        $algorithm = $this->encryption_algorithm ?? 'AES-256-CBC';

        try {
            $raw = base64_decode($encoded, true);
            $ivLength = openssl_cipher_iv_length($algorithm);
            $iv = substr($raw, 0, $ivLength);
            $cipher = substr($raw, $ivLength);

            $decrypted = openssl_decrypt($cipher, $algorithm, $key, 0, $iv);

            if ($decrypted === false) {
                throw new \RuntimeException("Error al descifrar el valor para '{$this->key}'.");
            }
        } catch (\Throwable $e) {
            logger()->error('❌ Error al descifrar valor de setting.', [
                'key' => $this->key,
                'error' => $e->getMessage(),
            ]);
            return null;
        }

        return $this->decode($decrypted, $asArray);
    }


    // ===================== SETTERS =====================

    public function setValueAttribute($value): void
    {
        foreach (SettingValueType::cases() as $type) {
            $field = "value_{$type->value}";
            $this->$field = null;
        }

        if ($this->is_encrypted) {
            $key = $this->getEncryptionKey();
            $algorithm = $this->encryption_algorithm ?? 'AES-256-CBC';

            $ivLength = openssl_cipher_iv_length($algorithm);
            $iv = random_bytes($ivLength);
            $cipher = openssl_encrypt($value, $algorithm, $key, 0, $iv);

            if ($cipher === false) {
                throw new \RuntimeException("Error al cifrar el valor para '{$this->key}'.");
            }

            // Guardamos el IV junto con el valor en base64
            $this->value_text = base64_encode($iv . $cipher);
            return;
        }

        match (true) {
            is_string($value) => $this->{strlen($value) > 250 ? 'value_text' : 'value_string'} = $value,
            is_int($value)    => $this->value_integer = $value,
            is_bool($value)   => $this->value_boolean = $value,
            is_float($value)  => $this->value_float = $value,
            is_array($value), is_object($value) => $this->value_text = json_encode($value, JSON_UNESCAPED_UNICODE),
            default           => null
        };
    }

    protected function isJson(mixed $value): bool
    {
        if (!is_string($value)) return false;
        $value = trim($value);
        return Str::startsWith($value, ['{', '[']) && json_validate($value);
    }

    // ===================== SCOPES =====================

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }

    // ===================== HELPERS =====================

    /**
     * Decodifica un valor almacenado en un setting.
     */
    protected function decode(mixed $value, bool $asArray = true): mixed
    {
        return $this->isJson($value) ? json_decode(trim($value), $asArray) : $value;
    }

    /**
     * Obtiene la clave de encriptación.
     */
    protected function getEncryptionKey(): string
    {
        $key = $this->encryption_key ?? config('app.key');

        if (empty($key)) {
            throw new \LogicException("No se ha definido una clave de encriptación para '{$this->key}'.");
        }

        $key = Str::startsWith($key, 'base64:') ? base64_decode(substr($key, 7)) : $key;

        // Validación adicional por seguridad
        if (strlen($key) < 16) {
            throw new \RuntimeException("La clave de encriptación es demasiado corta.");
        }

        return $key;
    }


    /**
     * Incrementa el contador de uso y actualiza la fecha de última utilización.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Marca el setting para rotación de clave.
     */
    public function markForKeyRotation(): void
    {
        $this->encryption_rotated_at = now();
        $this->save();
    }
}
