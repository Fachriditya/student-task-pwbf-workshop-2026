<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiToko extends Model
{
    use HasFactory;

    protected $primaryKey = 'barcode';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'barcode', 
        'nama_toko', 
        'latitude', 
        'longitude', 
        'accuracy'
    ];
}