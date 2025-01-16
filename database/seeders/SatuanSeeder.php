<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $satuan = [
            ['nama' => 'ml'],
            ['nama' => 'gr'],
            ['nama' => 'kg'],
            ['nama' => 'ltr'],
            ['nama' => 'pcs'],
            ['nama' => 'pack'],
        ];

        foreach ($satuan as $item) {
            \App\Models\Satuan::create($item);
        }
    }
}
