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
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Galon',
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Susu 3 Sapi',
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Es Batu',
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Air Teh',
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Gelas Cup 18oz Slim',
                'satuan' => 'pcs',
            ],
            // [
            //     'nama' => 'Gelas Cup 18oz Polos',
            //     'satuan' => 'pcs',
            // ],
            // [
            //     'nama' => 'Gelas Cup 18oz Sablon',
            //     'satuan' => 'pcs',
            // ],
            // powder series
            [
                'nama' => 'Powder Coklat',
                'satuan' => 'gr',
            ],
            [
                'nama' => 'Powder Taro',
                'satuan' => 'gr',
            ],
            [
                'nama' => 'Powder GreenTea',
                'satuan' => 'gr',
            ],
            [
                'nama' => 'Powder RedVelvet',
                'satuan' => 'gr',
            ],
            [
                'nama' => 'Powder Strawberry',
                'satuan' => 'gr',
            ],
            // sirup series
            [
                'nama' => 'Sirup ABC Lychee',
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Sirup Marjan Markisa',
                'satuan' => 'ml',
            ],
            [
                'nama' => 'Sirup Marjan Lemon',
                'satuan' => 'ml',
            ],

        ];

        foreach ($data as $bh) {
            $bh['catatan'] = 'by sistem';
            $bh['stok'] = 1000;
            $bahan = Bahan::create($bh);
        }
    }
}
