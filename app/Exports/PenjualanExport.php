<?php

namespace App\Exports;

use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PenjualanExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Mendapatkan tanggal awal dan akhir bulan kemarin
        $start = Carbon::now()->subMonth()->startOfMonth(); // awal bulan kemarin
        $end = Carbon::now()->subMonth()->endOfMonth(); // akhir bulan kemarin

        // Mengambil data DetailTransaksi antara tanggal yang ditentukan
        return DetailTransaksi::whereBetween('created_at', [$start, $end])->with('transaksi')->get(); // Mengambil relasi
    }

    /**
     * Mendapatkan header kolom untuk export
     * 
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Produk',
            'Jumlah',
            'Harga',
            'Total',
            'Tanggal Transaksi',
        ];
    }

    /**
     * Memetakan data untuk setiap baris yang akan diekspor
     * 
     * @param mixed $detailTransaksi
     * @return array
     */
    public function map($detailTransaksi): array
    {
        return [
            $detailTransaksi->id,
            $detailTransaksi->produk->nama,
            $detailTransaksi->qty,
            $detailTransaksi->harga,
            $detailTransaksi->subtotal,
            Carbon::parse($detailTransaksi->created_at)->format('d/m/Y'),
        ];
    }
}
