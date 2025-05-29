<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Seeders;

use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Support\Seeders\Base\AbstractDataSeeder;
use Koneko\VuexyAdmin\Support\Traits\Seeders\HandlesFileSeeders;

class SettingSeeder extends AbstractDataSeeder
{
    use HandlesFileSeeders;

    // Datos del Modelo
    protected string $model          = Setting::class;
    protected string|array $uniqueBy = 'key';

    // Ruta del archivo de datos
    protected string $targetFile = 'settings.json';
}
