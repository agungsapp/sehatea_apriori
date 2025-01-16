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
        Schema::create('pengeluaran_lains', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->unsignedBigInteger('jenis_pengeluaran_id');
            $table->unsignedBigInteger('sumber_dana_id');
            $table->decimal('harga', 15, 2);
            $table->text('keterangan')->nullable();
            $table->dateTime('tanggal_pengeluaran');
            $table->timestamps();

            $table->foreign('jenis_pengeluaran_id')->references('id')->on('jenis_pengeluarans')->onDelete('cascade');
            $table->foreign('sumber_dana_id')->references('id')->on('sumber_danas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_lains');
    }
};
