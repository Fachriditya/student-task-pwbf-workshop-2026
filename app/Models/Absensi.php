<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = ['mahasiswa_id', 'waktu_scan'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}