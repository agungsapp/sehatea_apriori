<?php

namespace Database\Seeders\Admin;

use App\Models\JenisPengeluaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'belanja harian',
            ],
            [
                'nama' => 'belanja ',
            ],
            [
                'nama' => 'beban gaji ',
            ],
            [
                'nama' => 'beban sewa ',
            ],
            [
                'nama' => 'beban sampah ',
            ],
            [
                'nama' => 'perlengkapan ',
            ],
            [
                'nama' => 'perbaikan',
            ],
            [
                'nama' => 'upah lembur',
            ],
        ];


        foreach ($data as $produk) {
            JenisPengeluaran::create($produk);
        }
    }
}
