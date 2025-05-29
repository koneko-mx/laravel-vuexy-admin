<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Seeders;

use Koneko\VuexyAdmin\Models\SystemNotification;
use Koneko\VuexyAdmin\Support\Seeders\Base\AbstractDataSeeder;

/**
 * 🌱 SystemNotificationSeeder
 *
 * Seeder de notificaciones base del ecosistema Koneko Vuexy ERP.
 *
 * - Soporta archivos CSV/JSON.
 * - Permite generación Faker en modo demo o testing.
 *
 * @extends AbstractDataSeeder
 */
class SystemNotificationSeeder extends AbstractDataSeeder
{
    // Datos del Modelo
    protected string $model          = SystemNotification::class;
    protected string|array $uniqueBy = 'id';

    // ================== FACKER ==================

    /**
     * Genera Notificaciones de sistema con Faker
     *
     * @param int $total
     * @param array $config
     * @return void
     */
    public function runFake(int $total, array $config = []): void
    {
        $this->log(" 👤 Generando {$total} Notificaciones de sistema con Faker...");
        $this->startProgress($total);

        try {
            for ($i = 0; $i < $total; $i++) {
                SystemNotification::factory()
                    ->withUsers(3, true, true) // 3 usuarios, 100% lectura, 100% confirmación
                    ->create();
                $this->advanceProgress();
            }
        } catch (\Throwable $e) {
            $this->log("❌ Error durante generación fake: {$e->getMessage()}");
            throw $e;
        }

        $this->finishProgress();
        $this->log(" Faker finalizado: {$total} Notificaciones de sistema generadas\n");
    }
}
