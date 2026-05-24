<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LokasiToko; // Memanggil model tabel toko kita

class LokasiTokoController extends Controller
{
    // Menampilkan daftar toko
    public function index()
    {
        $tokos = LokasiToko::all();
        return view('toko.index', compact('tokos'));
    }

    // Menampilkan form tambah toko baru
    public function create()
    {
        return view('toko.create');
    }

    // Menyimpan data toko ke database
    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string|max:8|unique:lokasi_tokos,barcode',
            'nama_toko' => 'required|string|max:50',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
        ]);

        LokasiToko::create($request->all());

        return redirect()->route('toko.index')->with('success', 'Data Toko berhasil ditambahkan!');
    }

    // Halaman khusus untuk print barcode toko
    public function cetakBarcode($barcode)
    {
        $toko = LokasiToko::findOrFail($barcode);
        return view('toko.barcode', compact('toko'));
    }
    // ==============================================
    // FITUR KUNJUNGAN SALES (MODUL 9)
    // ==============================================

    // Menampilkan halaman alat tempur sales
    public function kunjungan()
    {
        return view('toko.kunjungan');
    }

    // Fungsi Rahasia: Menghitung jarak bumi dalam satuan METER (Sesuai Lampiran 2)
    private function haversine($lat1, $lng1, $lat2, $lng2) 
    {
        $R = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return round($R * $c, 2); // Bulatkan 2 angka desimal
    }

    // Memproses data absen sales
    public function prosesKunjungan(Request $request)
    {
        $barcode_scan = $request->barcode;
        $lat_sales = $request->lat_sales;
        $lng_sales = $request->lng_sales;
        $acc_sales = $request->acc_sales;

        // 1. Cari data toko di database
        $toko = LokasiToko::where('barcode', $barcode_scan)->first();

        if (!$toko) {
            return response()->json(['success' => false, 'message' => 'Barcode Toko Tidak Dikenal!']);
        }

        // 2. Hitung Jarak Aktual (Sales vs Toko)
        $jarak_aktual = $this->haversine($toko->latitude, $toko->longitude, $lat_sales, $lng_sales);

        // 3. Hitung Batas Toleransi (Threshold Efektif sesuai Lampiran 3)
        $threshold_dasar = 300; // Bos minta toleransi dasar 300 meter
        $threshold_efektif = $threshold_dasar + $toko->accuracy + $acc_sales;

        // 4. Pengambilan Keputusan
        if ($jarak_aktual <= $threshold_efektif) {
            $status = 'DITERIMA';
            $pesan = "Kunjungan Sah! Jarak: {$jarak_aktual}m (Maks: {$threshold_efektif}m)";
            $is_valid = true;
        } else {
            $status = 'DITOLAK';
            $pesan = "Anda terlalu jauh! Jarak: {$jarak_aktual}m (Maks: {$threshold_efektif}m)";
            $is_valid = false;
        }

        return response()->json([
            'success' => true,
            'is_valid' => $is_valid,
            'status' => $status,
            'message' => $pesan,
            'toko' => $toko->nama_toko
        ]);
    }
}