<?php

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
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bahan_id');
            $table->unsignedBigInteger('jenis_pengeluaran_id');
            $table->unsignedBigInteger('sumber_dana_id')->default(1);
            $table->string('satuan');
            $table->integer('qty');
            $table->integer('harga_satuan');
            $table->integer('subtotal');
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('bahan_id')->references('id')->on('bahans');
            $table->foreign('jenis_pengeluaran_id')->references('id')->on('jenis_pengeluarans');
            $table->foreign('sumber_dana_id')->references('id')->on('sumber_danas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
