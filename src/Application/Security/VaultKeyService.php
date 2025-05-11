<?php

namespace Koneko\VuexyAdmin\Application\Security;

use Illuminate\Support\Facades\Crypt;
use Koneko\VuexyAdmin\Models\VaultKey;

class VaultKeyService
{
    public function generateKey(
        string $alias,
        string $ownerProject = 'default_project',
        string $algorithm = 'AES-256-CBC',
        bool $isSensitive = true
    ): VaultKey {
        if (!in_array($algorithm, openssl_get_cipher_methods())) {
            throw new \InvalidArgumentException("Algoritmo no soportado: $algorithm");
        }

        $keyMaterial = random_bytes(openssl_cipher_iv_length($algorithm) * 2);
        $encryptedKey = Crypt::encrypt($keyMaterial);

        return VaultKey::create([
            'environment'    => app()->environment(),
            'namespace'      => config('app.name'),
            'scope'          => 'global',
            'alias'          => $alias,
            'owner_project'  => $ownerProject,
            'algorithm'      => $algorithm,
            'key_material'   => $encryptedKey,
            'is_active'      => true,
            'is_sensitive'   => $isSensitive,
            'rotated_at'     => now(),
            'rotation_count' => 0,
        ]);
    }

    public function retrieveKey(string $alias): string
    {
        $keyEntry = VaultKey::where('alias', $alias)->where('is_active', true)->firstOrFail();

        return Crypt::decrypt($keyEntry->key_material);
    }

    public function rotateKey(string $alias): VaultKey
    {
        $keyEntry = VaultKey::where('alias', $alias)->firstOrFail();

        $newKeyMaterial = random_bytes(openssl_cipher_iv_length($keyEntry->algorithm) * 2);

        $keyEntry->update([
            'key_material'   => Crypt::encrypt($newKeyMaterial),
            'rotated_at'     => now(),
            'rotation_count' => $keyEntry->rotation_count + 1,
        ]);

        return $keyEntry;
    }

    public function deactivateKey(string $alias): bool
    {
        return VaultKey::where('alias', $alias)->update(['is_active' => false]);
    }
}
