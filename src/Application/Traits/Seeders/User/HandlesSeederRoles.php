<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Seeders\User;

use Koneko\VuexyAdmin\Models\User;

/**
 * 🎭 HandlesSeederRoles
 *
 * Encapsula la lógica de asignación de roles a usuarios en seeders.
 *
 * Requiere que el modelo tenga relación con Spatie Roles.
 * Puede utilizarse en múltiples seeders que trabajen con modelos que implementen roles.
 */
trait HandlesSeederRoles
{
    /**
     * Asigna roles a un usuario a partir del correo.
     */
    protected function assignRolesToUser(string $email, string|array $roles): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) return;

        // Normaliza roles
        if (is_string($roles)) {
            $decoded = json_decode($roles, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $roles = $decoded;
            } else {
                $roles = array_map('trim', explode(',', $roles));
            }
        }

        $roles = array_filter($roles); // Evita vacíos o nulos

        try {
            $user->syncRoles($roles);
            $this->log(" 🔑 Roles asignados a {$email}: " . implode(', ', $roles));

        } catch (\Throwable $e) {
            $this->log(" ❌ Error al asignar roles a {$email}: " . $e->getMessage());
        }
    }

    /**
     * Normaliza la entrada de roles desde CSV o JSON.
     */
    protected function normalizeRoles(string|array|null $input): array
    {
        if (is_array($input)) {
            return array_filter($input);
        }

        if (is_string($input)) {
            $cleaned = trim($input, "\"'");

            $decoded = json_decode($cleaned, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_filter($decoded);
            }

            return array_filter(array_map('trim', explode(',', $cleaned)));
        }

        return [];
    }

}
