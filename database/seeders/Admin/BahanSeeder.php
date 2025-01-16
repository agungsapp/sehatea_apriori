<?php

namespace Database\Seeders\Admin;

use App\Models\Bahan;
use App\Models\StokBahan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Gula Cair',
                'harga' => 17000,
                'harga_satuan' => 0,
                'bobot' => 2000,
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Galon',
                'harga' => 5000,
                'harga_satuan' => 0,
                'bobot' => 18000,
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Susu 3 Sapi',
                'harga' => 12500,
                'harga_satuan' => 0,
                'bobot' => 490,
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Es Batu',
                'harga' => 13000,
                'harga_satuan' => 0,
                'bobot' => 10000,
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Air Teh',
                'harga' => 5000,
                'harga_satuan' => 0,
                'bobot' => 4000,
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Gelas Cup 17oz Polos',
                'harga' => 283000,
                'harga_satuan' => 0,
                'bobot' => 500,
                'satuan' => 'pcs',
            ],
            [
                'nama' => 'Gelas Cup 17oz Sablon',
                'harga' => 566,
                'harga_satuan' => 0,
                'bobot' => 1,
                'satuan' => 'pcs',
            ],
            // powder series
            [
                'nama' => 'Powder Coklat',
                'harga' => 75000,
                'harga_satuan' => 0,
                'bobot' => 1000,
                'satuan' => 'gr',
            ],
            [
                'nama' => 'Powder Taro',
                'harga' => 27500,
                'harga_satuan' => 0,
                'bobot' => 500,
                'satuan' => 'gr',
            ],
            [
                'nama' => 'Powder GreenTea',
                'harga' => 27500,
                'harga_satuan' => 0,
                'bobot' => 500,
                'satuan' => 'gr',
            ],
            [
                'nama' => 'Powder RedVelvet',
                'harga' => 27500,
                'harga_satuan' => 0,
                'bobot' => 500,
                'satuan' => 'gr',
            ],
            [
                'nama' => 'Powder Strawberry',
                'harga' => 27500,
                'harga_satuan' => 0,
                'bobot' => 500,
                'satuan' => 'gr',
            ],
            // sirup series
            [
                'nama' => 'Marjan Lychee',
                'harga' => 27200,
                'harga_satuan' => 0,
                'bobot' => 460,
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Marjan Markisa',
                'harga' => 27200,
                'harga_satuan' => 0,
                'bobot' => 460,
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Marjan Lemon',
                'harga' => 27200,
                'harga_satuan' => 0,
                'bobot' => 460,
                'satuan' => 'ml',
            ],

        ];

        foreach ($data as $bh) {
            $bh['harga_satuan'] = $bh['harga'] / $bh['bobot'];
            $bahan = Bahan::create($bh);
            StokBahan::create([
                'bahan_id' => $bahan->id,
                'stok' => 0
            ]);
        }
    }
}
