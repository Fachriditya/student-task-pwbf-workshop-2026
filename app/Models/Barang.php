<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    /**
     * Nama tabel
     */
    protected $table = 'barang';
    
    /**
     * Primary key
     */
    protected $primaryKey = 'id_barang';
    
    /**
     * Primary key bukan auto increment (karena pakai trigger atau custom logic)
     */
    public $incrementing = false;
    
    /**
     * Tipe primary key
     */
    protected $keyType = 'string';
    
    /**
     * Disable timestamps Laravel (kita pakai custom timestamp)
     */
    public $timestamps = false;
    
    /**
     * Kolom yang bisa diisi
     */
    protected $fillable = [
        'nama',
        'harga',
    ];
    
    /**
     * Casting tipe data
     */
    protected $casts = [
        'harga' => 'integer',
        'timestamp' => 'datetime',
    ];
    
    /**
     * Boot method - Generate ID otomatis jika trigger tidak aktif
     * 
     * SOLUSI 2: Jika trigger tidak bisa dibuat di MySQL,
     * bisa generate ID di Laravel dengan method ini
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($barang) {
            if (empty($barang->id_barang)) {
                $barang->id_barang = self::generateIdBarang();
            }
        });
    }
    
    /**
     * Generate ID Barang dengan format YYMMDDNN
     * 
     * @return string
     */
    public static function generateIdBarang()
    {
        $today = now();
        
        $count = self::whereDate('timestamp', $today->toDateString())->count() + 1;
        
        return $today->format('y') .
               $today->format('m') .
               $today->format('d') .
               str_pad($count, 2, '0', STR_PAD_LEFT);
    }
    
    /**
     * Format harga dengan rupiah
     */
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}