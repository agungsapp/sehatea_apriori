<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [

            [
                'nama' => 'whatsapp',
                'active' => true,

            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
