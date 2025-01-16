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
        Schema::create('stok_bahans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bahan_id');
            $table->integer('stok');
            $table->timestamps();
            $table->foreign('bahan_id')->references('id')->on('bahans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_bahans');
    }
};
