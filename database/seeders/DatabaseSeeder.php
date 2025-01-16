<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Admin\BahanSeeder;
use Database\Seeders\Admin\JenisPengeluaranSeeder;
use Database\Seeders\Admin\ProdukSeeder;
use Database\Seeders\Admin\SumberDanaSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // pondasi
        $this->call(ProdukSeeder::class);
        $this->call(StarterSeeder::class);
        // $this->call(BahanSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(SumberDanaSeeder::class);
        $this->call(JenisPengeluaranSeeder::class);
        $this->call(SatuanSeeder::class);
    }
}
