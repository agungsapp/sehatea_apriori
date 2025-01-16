<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranLain extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_lains';

    protected $fillable = [
        'nama',
        'jenis_pengeluaran_id',
        'sumber_dana_id',
        'harga',
        'keterangan',
        'tanggal_pengeluaran',
    ];

    // protected $guarded = ['id'];

    public function jenisPengeluaran()
    {
        return $this->belongsTo(JenisPengeluaran::class, 'jenis_pengeluaran_id');
    }

    public function sumberDana()
    {
        return $this->belongsTo(SumberDana::class, 'sumber_dana_id');
    }
}
