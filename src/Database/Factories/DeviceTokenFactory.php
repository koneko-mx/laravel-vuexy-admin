<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Factories;

use Koneko\VuexyAdmin\Models\DeviceToken;
use Koneko\VuexyAdmin\Support\Factories\AbstractModelFactory;
use Koneko\VuexyAdmin\Support\Traits\Factories\HasFactorySupport;

/**
 * 🧲 DeviceTokenFactory
 *
 * Factory oficial para el modelo `DeviceToken` dentro del ecosistema Koneko Vuexy ERP.
 *
 * @extends AbstractModelFactory<DeviceToken>
 */
class DeviceTokenFactory extends AbstractModelFactory
{
    use HasFactorySupport;

    protected $model = DeviceToken::class;

    protected function baseDefinition(): array
    {
        return [
            'user_id'      => null, // Puedes asignarlo luego
            'token'        => $this->faker->uuid(),
            'platform'     => $this->randomFrom(['ios', 'android', 'web', 'desktop']),
            'client'       => $this->maybe(70, $this->faker->userAgent),
            'device_info'  => $this->maybe(80, fn() => $this->faker->word . ' ' . $this->faker->numerify('###')),
            'location'     => $this->maybe(50, fn() => $this->faker->country),
            'last_used_at' => $this->maybe(70, $this->faker->dateTimeBetween('-30 days', 'now')),
            'is_active'    => true,
        ];
    }

    public function definition(): array
    {
        return $this->baseDefinition();
    }
}
