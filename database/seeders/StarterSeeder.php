<?php

namespace Database\Seeders;

use App\Models\MetodePembayaran;
use App\Models\MetodePembelian;
use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StarterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pembayarans = [
            [
                'nama' => 'qris',
            ],
            [
                'nama' => 'tunai',
            ],
            [
                'nama' => 'transfer',
            ],
        ];

        foreach ($pembayarans as $pembayaran) {
            MetodePembayaran::create($pembayaran);
        }
        $pembelians = [
            [
                'nama' => 'toko',
            ],
            [
                'nama' => 'pesanan',
            ],
            [
                'nama' => 'gojek',
            ],
            [
                'nama' => 'grab',
            ],
            [
                'nama' => 'shopee',
            ],
        ];

        foreach ($pembelians as $pembelian) {
            MetodePembelian::create($pembelian);
        }
    }
}
