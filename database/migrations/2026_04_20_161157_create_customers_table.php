<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('idcustomer');
            $table->string('nama');
            $table->string('alamat');
            $table->string('provinsi');
            $table->string('kota');
            $table->string('kecamatan');
            $table->string('kodepos');
            
            // Untuk Studi Kasus 3: Tambah Customer 1 (Simpan sebagai BLOB)
            $table->binary('foto_blob')->nullable(); // 
            
            // Untuk Studi Kasus 3: Tambah Customer 2 (Simpan sebagai File Path)
            $table->string('foto_path')->nullable(); // 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
