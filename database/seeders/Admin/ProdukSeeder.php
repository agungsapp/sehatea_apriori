<?php

namespace Database\Seeders\Admin;

use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Iced Tea Original',
                'hpp' => 0,
                'harga' => 5000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Lemon Tea',
                'hpp' => 0,
                'harga' => 6000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Milk Tea',
                'hpp' => 0,
                'harga' => 6000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Green Tea',
                'hpp' => 0,
                'harga' => 8000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Red Velvet Tea',
                'hpp' => 0,
                'harga' => 8000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Lychee Tea',
                'hpp' => 0,
                'harga' => 8000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Markisa Tea',
                'hpp' => 0,
                'harga' => 8000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Coklat Tea',
                'hpp' => 0,
                'harga' => 8000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Taro Tea',
                'hpp' => 0,
                'harga' => 8000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Strawberry Tea',
                'hpp' => 0,
                'harga' => 8000,
                'active' => true,
            ],
            [
                'nama' => 'Iced Yakult Tea',
                'hpp' => 0,
                'harga' => 9000,
                'active' => true,
            ],
            [
                'nama' => 'Es Tawar',
                'hpp' => 0,
                'harga' => 2000,
                'active' => true,
            ],
        ];

        foreach ($data as $produk) {
            Produk::create($produk);
        }
    }
}
