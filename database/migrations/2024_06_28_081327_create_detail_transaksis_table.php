<?php

use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('produk_id');
            $table->string('kode', 20);
            $table->integer('harga');
            $table->integer('qty');
            $table->integer('subtotal');
            $table->timestamps();

            $table->foreign('transaksi_id')
                ->references('id')
                ->on('transaksis')
                ->onDelete('CASCADE');
            $table->foreign('produk_id')
                ->references('id')
                ->on('produks')
                ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksis');
    }
};
