<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonversiSatuan extends Model
{
    protected $fillable = [
        'bahan_id',
        'satuan_awal',
        'satuan_tujuan',
        'rasio',
        'catatan'
    ];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
