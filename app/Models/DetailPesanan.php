<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanans';
    protected $primaryKey = 'iddetail_pesanan';
    
    public $timestamps = true;

    protected $fillable = [
        'idmenu',     
        'idpesanan',  
        'jumlah',     
        'harga',      
        'subtotal',  
        'catatan'     
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'idpesanan');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'idmenu');
    }
}