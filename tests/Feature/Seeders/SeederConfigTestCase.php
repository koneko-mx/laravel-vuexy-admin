<?php

namespace Koneko\VuexyAdmin\Tests\Feature\Seeders;

use Koneko\VuexyAdmin\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\TestCase;

class SeederConfigTestCase extends AbstractTestCase
{
    protected array $config;

    protected function setUp(): void
    {
        parent::setUp();

        // Simula que la config está cargada como en Laravel
        $this->config = [
            'users' => [
                'enabled'  => true,
                'seeder'   => 'Koneko\\VuexyAdmin\\Database\\Seeders\\UserSeeder',
                'file'     => 'users.json',
                'fake'     => [
                    'min'    => 20,
                    'max'    => 3000,
                    'images' => [
                        'use_local'     => true,
                        'use_internal'  => true,
                        'image_percent' => 20,
                        'random_weight' => [
                            'local'    => 60,
                            'internal' => 40,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testUsersSeederConfigIsValid(): void
    {
        $user = $this->config['users'];

        $this->assertIsArray($user);
        $this->assertArrayHasKey('enabled', $user);
        $this->assertArrayHasKey('seeder', $user);
        $this->assertArrayHasKey('file', $user);
        $this->assertArrayHasKey('fake', $user);

        $this->assertIsBool($user['enabled']);
        $this->assertIsString($user['seeder']);
        $this->assertIsString($user['file']);

        $fake = $user['fake'];
        $this->assertArrayHasKey('min', $fake);
        $this->assertArrayHasKey('max', $fake);
        $this->assertArrayHasKey('images', $fake);

        $this->assertIsInt($fake['min']);
        $this->assertIsInt($fake['max']);
        $this->assertGreaterThan(0, $fake['min']);
        $this->assertGreaterThan($fake['min'], $fake['max']);

        $images = $fake['images'];
        $this->assertArrayHasKey('use_local', $images);
        $this->assertArrayHasKey('use_internal', $images);
        $this->assertArrayHasKey('image_percent', $images);
        $this->assertArrayHasKey('random_weight', $images);

        $this->assertIsBool($images['use_local']);
        $this->assertIsBool($images['use_internal']);
        $this->assertIsInt($images['image_percent']);
        $this->assertGreaterThanOrEqual(0, $images['image_percent']);
        $this->assertLessThanOrEqual(100, $images['image_percent']);

        $this->assertIsArray($images['random_weight']);
        $this->assertArrayHasKey('local', $images['random_weight']);
        $this->assertArrayHasKey('internal', $images['random_weight']);
        $this->assertIsInt($images['random_weight']['local']);
        $this->assertIsInt($images['random_weight']['internal']);
    }
}
