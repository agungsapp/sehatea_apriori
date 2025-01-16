<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komposisi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
