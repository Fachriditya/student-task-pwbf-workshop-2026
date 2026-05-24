<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'idmenu';
    protected $fillable = ['nama_menu', 'harga', 'path_gambar', 'idvendor'];

    public function vendor() {
        return $this->belongsTo(Vendor::class, 'idvendor');
}
}
