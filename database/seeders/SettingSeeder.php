<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Koneko\VuexyAdmin\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings_array = [
            //
        ];

        foreach ($settings_array as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value,
            ]);
        };
    }
}
