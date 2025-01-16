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
        Schema::create('konversi_satuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahans');
            $table->string('satuan_awal');
            $table->string('satuan_tujuan');
            $table->integer('rasio');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['bahan_id', 'satuan_awal', 'satuan_tujuan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konversi_satuans');
    }
};
