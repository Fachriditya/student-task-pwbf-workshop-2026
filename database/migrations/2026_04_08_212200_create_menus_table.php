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
        Schema::create('menus', function (Blueprint $table) {
            $table->id('idmenu'); // Primary Key [cite: 16]
            $table->string('nama_menu'); // [cite: 17]
            $table->integer('harga'); // [cite: 18]
            $table->string('path_gambar')->nullable(); // [cite: 19]
            $table->foreignId('idvendor')->constrained('vendors', 'idvendor')->onDelete('cascade'); // [cite: 19]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
