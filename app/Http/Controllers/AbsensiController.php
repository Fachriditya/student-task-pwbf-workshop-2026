<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    public function scan()
    {
        return view('absensi.scan');
    }

    public function prosesAbsensi(Request $request)
    {
        $serial = $request->serialNumber;

        $mahasiswa = Mahasiswa::where('nfc_serial', $serial)->first();

        if ($mahasiswa) {
            Absensi::create([
                'mahasiswa_id' => $mahasiswa->id
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Berhasil absen!',
                'nama' => $mahasiswa->nama
            ]);
        } else {
            return response()->json([
                'success' => false, 
                'message' => 'Kartu NFC tidak terdaftar!'
            ]);
        }
    }
}