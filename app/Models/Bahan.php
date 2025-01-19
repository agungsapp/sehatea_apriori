<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;
    // protected $guarded = ['id'];
    protected $fillable = [
        'nama',
        'satuan',
        'harga_satuan',
        'stok',
        'catatan',
        'active'
    ];

    public function getLatestHargaSatuan()
    {
        $lastPurchase = Pengeluaran::where('bahan_id', $this->id)
            ->orderBy('tanggal', 'desc')
            ->first();

        if (!$lastPurchase) {
            return 0;
        }

        // Jika satuan pembelian berbeda dengan satuan bahan
        if ($lastPurchase->satuan !== $this->satuan) {
            $konversi = KonversiSatuan::where('bahan_id', $this->id)
                ->where('satuan_awal', $lastPurchase->satuan)
                ->where('satuan_tujuan', $this->satuan)
                ->first();

            if (!$konversi) {
                return 0;
            }

            return $lastPurchase->harga_satuan / $konversi->rasio;
        }

        return $lastPurchase->harga_satuan;
    }

    // Tambahkan juga relasi ke Pengeluaran jika belum ada
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
