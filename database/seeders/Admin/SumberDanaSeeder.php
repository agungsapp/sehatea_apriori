<?php

namespace Database\Seeders\Admin;

use App\Models\SumberDana;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SumberDanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            [
                'user_id' => 1,
                'nama' => 'sehatea',
            ],
            [
                'user_id' => 2,
                'nama' => 'agung',
            ],
            [
                'user_id' => 3,
                'nama' => 'intan',
            ],
            [
                'user_id' => 4,
                'nama' => 'ajis',
            ],
        ];

        foreach ($datas as $data) {
            SumberDana::create($data);
        }
    }
}
