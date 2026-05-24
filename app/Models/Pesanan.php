<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanans';
    protected $primaryKey = 'idpesanan';
    protected $fillable = ['nama', 'timestamp', 'total', 'metode_bayar', 'status_bayar', 'snap_token'];
    public $timestamps = false;
}
