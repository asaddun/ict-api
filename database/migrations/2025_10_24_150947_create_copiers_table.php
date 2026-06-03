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
        Schema::create('copiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('limit');

            // Pemakaian harian (Hasil perhitungan selisih)
            $table->integer('bw_daily')->nullable();
            $table->integer('color_daily')->nullable();
            $table->integer('total_daily')->nullable();
            $table->date('usage_date'); // Tanggal pemakaian

            // Counter hingga hari ini
            $table->integer('bw_counter');
            $table->integer('color_counter');
            $table->integer('total_counter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copiers');
    }
};
