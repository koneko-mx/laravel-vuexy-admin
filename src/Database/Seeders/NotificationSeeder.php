<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Seeders;

use Koneko\VuexyAdmin\Models\Notification;
use Koneko\VuexyAdmin\Support\Seeders\Base\AbstractDataSeeder;

/**
 * 🌱 NotificationSeeder
 *
 * Seeder de notificaciones base del ecosistema Koneko Vuexy ERP.
 *
 * - Soporta archivos CSV/JSON.
 * - Permite generación Faker en modo demo o testing.
 *
 * @extends AbstractDataSeeder
 */
class NotificationSeeder extends AbstractDataSeeder
{
    // Datos del Modelo
    protected string $model          = Notification::class;
    protected string|array $uniqueBy = 'id';
}
