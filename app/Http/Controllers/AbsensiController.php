<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    // 1. Menampilkan Halaman Scan di HP
    public function scan()
    {
        return view('absensi.scan');
    }

    // 2. Menerima data Serial Number dari NFC HP dan memprosesnya
    public function prosesAbsensi(Request $request)
    {
        $serial = $request->serialNumber;

        // Cari apakah ada mahasiswa yang punya serial number NFC ini
        $mahasiswa = Mahasiswa::where('nfc_serial', $serial)->first();

        if ($mahasiswa) {
            // Jika ada, catat absensinya hari ini
            Absensi::create([
                'mahasiswa_id' => $mahasiswa->id
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Berhasil absen!',
                'nama' => $mahasiswa->nama
            ]);
        } else {
            // Jika kartu tidak dikenali
            return response()->json([
                'success' => false, 
                'message' => 'Kartu NFC tidak terdaftar!'
            ]);
        }
    }
}