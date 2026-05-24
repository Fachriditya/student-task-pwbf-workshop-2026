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
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id('idpenjualan_detail');
            
            $table->foreignId('id_penjualan')->constrained('penjualans', 'id_penjualan')->onDelete('cascade');
            
            $table->string('id_barang', 8);
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
            
            $table->integer('jumlah');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};
