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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id('idpesanan'); // Primary Key [cite: 29]
            $table->string('nama'); // Guest_0000001 [cite: 4, 30]
            $table->timestamp('timestamp'); // [cite: 31]
            $table->integer('total'); // [cite: 32]
            $table->integer('metode_bayar')->nullable(); // [cite: 33]
            $table->smallInteger('status_bayar')->default(1); // 1: Pending [cite: 33]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
