<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = "pengeluarans";
    // protected $guarded = ['id'];
    protected $fillable = [
        'bahan_id',
        'jenis_pengeluaran_id',
        'sumber_dana_id',
        'satuan',
        'qty',
        'harga_satuan',
        'subtotal',
        'tanggal',
        'catatan',
    ];

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    public function jenisPengeluaran()
    {
        return $this->belongsTo(JenisPengeluaran::class, 'jenis_pengeluaran_id');
    }

    public function sumberDana()
    {
        return $this->belongsTo(SumberDana::class, 'sumber_dana_id');
    }
}
