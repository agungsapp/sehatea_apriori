<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // protected $guarded = ['id'];
    protected $fillable = ['nama', 'hpp', 'harga'];

    public function komposisi()
    {
        return $this->hasMany(Komposisi::class, 'produk_id');
    }
}
