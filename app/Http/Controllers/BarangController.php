<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::orderBy('timestamp', 'desc')->get();
        
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        Barang::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil diupdate!');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus!');
    }

    public function print(Request $request)
    {
        $selected = $request->ids;
        $x = (int)($request->x ?? 1);
        $y = (int)($request->y ?? 1);

        if (!$selected) {
            return redirect()->back()->with('error', 'Pilih barang terlebih dahulu!');
        }

        $barang = Barang::whereIn('id_barang', $selected)->get();

        $startIndex = (($y - 1) * 5) + ($x - 1);

        $labels = array_fill(0, 40, null);
        
        foreach ($barang as $i => $b) {
            $posisi = $startIndex + $i;
            if ($posisi < 40) {
                $labels[$posisi] = $b;
            }
        }

        $pdf = Pdf::loadView('barang.pdf', compact('labels'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('label_barang.pdf');
    }
}