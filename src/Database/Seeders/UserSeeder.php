<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Seeders;

use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Application\Enums\User\UserBaseFlags;
use Koneko\VuexyAdmin\Application\Seeding\Concerns\Main\HasSeederFactorySupport;
use Koneko\VuexyAdmin\Application\Traits\Seeders\User\{HandlesSeederAvatars,HandlesSeederRoles};
use Koneko\VuexyAdmin\Support\Seeders\Base\AbstractDataSeeder;
use Koneko\VuexyAdmin\Support\Traits\Seeders\HandlesFileSeeders;

/**
 * 🌱 Seeder del modelo User
 *
 * Soporta:
 * - Inserción desde archivo JSON/CSV
 * - Inserción Faker por lotes
 * - Asignación de roles desde archivo
 * - Asignación de avatar desde archivo
 *
 * @package Koneko\VuexyAdmin\Database\Seeders
 */
class UserSeeder extends AbstractDataSeeder
{
    use HasSeederFactorySupport,
        HandlesFileSeeders;
    use HandlesSeederRoles,
        HandlesSeederAvatars;

    // Datos del Modelo
    protected string $model          = User::class;
    protected string|array $uniqueBy = 'email';

    // Ruta del archivo de datos y chunk size para inserción por lotes
    protected string $targetFile = 'users.json';
    protected int $chunkSize     = 100;

    /**
     * Genera usuarios con Faker
     *
     * @param int $total
     * @param array $config
     * @return void
     */
    public function runFake(int $total, array $config = []): void
    {
        $this->log(" 👤 Generando {$total} usuarios con Faker...");
        $this->startProgress($total);

        $createdUsers = collect();

        try {
            for ($i = 0; $i < $total; $i++) {
                $user = User::factory()
                    ->withRoles(rand(1, 2))
                    ->withNotifications(0, 10) // de 0 a 10 Notificaciones
                    ->withAvatar(40) // 40% de usuarios con avatar
                    ->create();

                $createdUsers->push($user);
                $this->advanceProgress();
            }

        } catch (\Throwable $e) {
            $this->log("❌ Error durante generación fake: {$e->getMessage()}");
            throw $e;
        }

        $this->finishProgress();
        $this->log(" Faker finalizado: {$createdUsers->count()} usuarios generados\n");
    }

    /**
     * Sanitiza una fila de datos para el modelo User
     *
     * @param array $row
     * @param string $modelClass
     * @return array
     */
    protected function sanitizeRowWithFillableAndCasts(array $row, string $modelClass): array
    {
        $row = parent::sanitizeRowWithFillableAndCasts($row, $modelClass);

        $row['flags'] = [UserBaseFlags::IS_USER->value => 1];

        return $row;
    }

    /**
     * Asigna roles y avatares a los usuarios
     *
     * @param array $options
     * @return void
     */
    protected function afterRun(array $options): void
    {
        foreach ($this->originalData as $row) {
            $email      = $row['email'] ?? null;
            $avatarPath = $row['avatar_path'] ?? '';
            $roles      = $this->normalizeRoles($row['roles'] ?? []);

            if (!empty($roles)) {
                $this->assignRolesToUser($email, $roles);
            }

            if (!empty($avatarPath)) {
                $this->assignAvatarToUser($email, $avatarPath);
            }
        }
    }
}
