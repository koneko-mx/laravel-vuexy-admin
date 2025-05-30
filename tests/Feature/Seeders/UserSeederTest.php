<?php

namespace Koneko\VuexyAdmin\Tests\Feature\Seeders;

use Koneko\VuexyAdmin\Tests\AbstractTestCase;


class UserSeederTest extends AbstractTestCase
{
    /** @test */
    public function se_ejecuta_user_seeder_y_se_crea_el_usuario_admin()
    {
        $this->seed(\Koneko\VuexyAdmin\Database\Seeders\UserSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'sadmin@koneko.mx',
        ]);
    }
}
