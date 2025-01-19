<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // protected $guarded = ['id'];
    protected $fillable = ['nama', 'hpp', 'harga'];


    public function getHppAttribute()
    {
        return $this->calculateHpp();
    }

    // Method helper untuk menghitung HPP
    public function calculateHpp()
    {
        return $this->komposisi->sum(function ($komposisi) {
            return $komposisi->takaran * $komposisi->bahan->getLatestHargaSatuan();
        });
    }

    // Definisi relasi dengan komposisi
    public function komposisi()
    {
        return $this->hasMany(Komposisi::class, 'produk_id');
    }
}
