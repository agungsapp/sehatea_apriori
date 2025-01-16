<?php

use App\Models\Bahan;
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
        Schema::create('bahan_hilangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bahan_id');
            $table->integer('harga');
            $table->integer('qty');
            $table->enum('satuan', ['ml', 'gr', 'kg', 'pcs']);
            $table->enum('status', ['hilang', 'mencair', 'basi']);
            $table->timestamps();

            $table->foreign('bahan_id')
                ->references('id')
                ->on('bahans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_hilangs');
    }
};
