<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan library ini sudah diinstall

class BarangController extends Controller
{
    /**
     * Menampilkan halaman DataTables (Read)
     */
    public function index()
    {
        // Mengambil semua data barang, diurutkan dari yang terbaru
        $barangs = Barang::orderBy('timestamp', 'desc')->get();
        return view('barang.index', compact('barangs'));
    }

    /**
     * Menampilkan form tambah barang (Create)
     */
    public function create()
    {
        return view('barang.create');
    }

    /**
     * Menyimpan barang ke database (Store)
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        // Simpan data. ID Barang di-handle oleh Model/Trigger, 
        // Timestamp otomatis dari database.
        Barang::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil ditambahkan!');
    }

    /**
     * Logika Utama: Cetak Label Harga ke PDF
     */
    public function print(Request $request)
    {
        // 1. Validasi request
        $request->validate([
            'ids' => 'required|array',       // Minimal 1 checkbox dipilih
            'x' => 'required|integer|min:1|max:5', // Kertas 108 punya 5 kolom
            'y' => 'required|integer|min:1|max:8', // Kertas 108 punya 8 baris
        ]);

        // 2. Ambil data barang yang dicentang user
        $selectedBarangs = Barang::whereIn('id_barang', $request->ids)->get();

        // 3. Logika Menghitung Slot Kosong (Skip)
        // Sesuai rumus: (Y - 1) * Jumlah Kolom + (X - 1)
        $x = $request->x;
        $y = $request->y;
        
        $skipSlots = (($y - 1) * 5) + ($x - 1); // 5 adalah jumlah kolom per baris kertas TnJ 108

        // 4. Siapkan Array Data Final
        $labelData = [];

        // Masukkan slot kosong (berupa null) agar posisi mulai cetak bergeser
        for ($i = 0; $i < $skipSlots; $i++) {
            $labelData[] = null;
        }

        // Masukkan data barang asli setelah slot kosong
        foreach ($selectedBarangs as $barang) {
            $labelData[] = $barang;
        }

        // 5. Generate PDF
        // Kita akan buat file view 'barang.pdf' setelah ini
        $pdf = Pdf::loadView('barang.pdf', compact('labelData'))
                  ->setPaper('a4', 'portrait'); // Bisa disesuaikan nanti

        return $pdf->stream('Cetak_Label_Harga_TnJ_108.pdf');
    }
}