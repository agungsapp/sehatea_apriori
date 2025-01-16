<?php

use App\Models\MetodePembayaran;
use App\Models\MetodePembelian;
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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20);
            $table->integer('grand_total');
            $table->unsignedBigInteger('metode_pembayaran_id');
            $table->unsignedBigInteger('metode_pembelian_id');
            $table->timestamps();

            $table->foreign('metode_pembayaran_id')
                ->references('id')
                ->on('metode_pembayarans')
                ->onDelete('RESTRICT');
            $table->foreign('metode_pembelian_id')
                ->references('id')
                ->on('metode_pembelians')
                ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
