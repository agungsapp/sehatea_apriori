<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSeederCSV extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->produks();
        $this->bahans();
        $this->konversi();
        $this->transaksi();
        $this->detailTransaksi();
    }


    public function detailTransaksi()
    {
        $csvFilePath = public_path('seeder/detail_transaksis.csv');

        // Membaca file CSV menggunakan fgetcsv
        if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
            // Skip baris header jika ada
            $header = fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== FALSE) {
                DB::table('detail_transaksis')->insert([
                    'id' => $data[0],
                    'transaksi_id' => $data[1],
                    'produk_id' => $data[2],
                    'kode' => $data[3],
                    'harga' => $data[4],
                    'qty' => $data[5],
                    'subtotal' => $data[6],
                    'created_at' => $data[7],
                    'updated_at' => $data[8],
                ]);
            }

            fclose($handle);
        }
    }
    public function transaksi()
    {
        $csvFilePath = public_path('seeder/transaksis.csv');

        // Membaca file CSV menggunakan fgetcsv
        if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
            // Skip baris header jika ada
            $header = fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== FALSE) {
                DB::table('transaksis')->insert([
                    'id' => $data[0],
                    'kode' => $data[1],
                    'grand_total' => $data[2],
                    'metode_pembayaran_id' => $data[3],
                    'metode_pembelian_id' => $data[4],
                    'created_at' => $data[5],
                    'updated_at' => $data[6],
                ]);
            }

            fclose($handle);
        }
    }



    public function konversi()
    {
        $csvFilePath = public_path('seeder/konversi_satuans.csv');

        // Membaca file CSV menggunakan fgetcsv
        if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
            // Skip baris header jika ada
            $header = fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== FALSE) {
                DB::table('konversi_satuans')->insert([
                    'id' => $data[0],
                    'bahan_id' => $data[1],
                    'satuan_awal' => $data[2],
                    'satuan_tujuan' => $data[3],
                    'rasio' => $data[4],
                    'catatan' => $data[5],
                    'created_at' => $data[6],
                    'updated_at' => $data[7],
                ]);
            }

            fclose($handle);
        }
    }
    public function bahans()
    {
        $csvFilePath = public_path('seeder/bahans.csv');

        // Membaca file CSV menggunakan fgetcsv
        if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
            // Skip baris header jika ada
            $header = fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== FALSE) {
                DB::table('bahans')->insert([
                    'id' => $data[0],
                    'nama' => $data[1],
                    'satuan' => $data[2],
                    'stok' => $data[3],
                    'catatan' => $data[4],
                    'active' => $data[5],
                    'created_at' => $data[6],
                    'updated_at' => $data[7],
                ]);
            }

            fclose($handle);
        }
    }
    public function produks()
    {
        $csvFilePath = public_path('seeder/produks.csv');

        // Membaca file CSV menggunakan fgetcsv
        if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
            // Skip baris header jika ada
            $header = fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== FALSE) {
                DB::table('produks')->insert([
                    'id' => $data[0],
                    'nama' => $data[1],
                    'hpp' => $data[2],
                    'harga' => $data[3],
                    'active' => $data[4],
                    'created_at' => $data[5],
                    'updated_at' => $data[6],
                ]);
            }

            fclose($handle);
        }
    }
}
