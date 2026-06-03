<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    
    protected $primaryKey = 'idcustomer';

    protected $fillable = [
        'nama', 
        'alamat', 
        'provinsi', 
        'kota', 
        'kecamatan', 
        'kodepos', 
        'foto_blob',
        'foto_path'
    ];
}