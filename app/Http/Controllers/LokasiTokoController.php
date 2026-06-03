<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LokasiToko;

class LokasiTokoController extends Controller
{
    
    public function index()
    {
        $tokos = LokasiToko::all();
        return view('toko.index', compact('tokos'));
    }

    public function create()
    {
        return view('toko.create');
    }

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

    public function cetakBarcode($barcode)
    {
        $toko = LokasiToko::findOrFail($barcode);
        return view('toko.barcode', compact('toko'));
    }

    public function kunjungan()
    {
        return view('toko.kunjungan');
    }

    private function haversine($lat1, $lng1, $lat2, $lng2) 
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return round($R * $c, 2);
    }

    public function prosesKunjungan(Request $request)
    {
        $barcode_scan = $request->barcode;
        $lat_sales = $request->lat_sales;
        $lng_sales = $request->lng_sales;
        $acc_sales = $request->acc_sales;

        $toko = LokasiToko::where('barcode', $barcode_scan)->first();

        if (!$toko) {
            return response()->json(['success' => false, 'message' => 'Barcode Toko Tidak Dikenal!']);
        }

        $jarak_aktual = $this->haversine($toko->latitude, $toko->longitude, $lat_sales, $lng_sales);

        $threshold_dasar = 300;
        $threshold_efektif = $threshold_dasar + $toko->accuracy + $acc_sales;

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