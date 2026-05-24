<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'customers';
    
    // Primary key-nya (sesuai migrasi tadi)
    protected $primaryKey = 'idcustomer';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'nama', 
        'alamat', 
        'provinsi', 
        'kota', 
        'kecamatan', 
        'kodepos', 
        'foto_blob', // Untuk Studi Kasus 3: Tambah 1 
        'foto_path'  // Untuk Studi Kasus 3: Tambah 2 
    ];
}