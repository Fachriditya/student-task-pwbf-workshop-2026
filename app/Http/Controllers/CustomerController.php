<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    // Menampilkan Tabel Data Customer [cite: 26]
    public function index() {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }

    // Form Tambah 1 (Simpan BLOB) 
    public function create1() { return view('customer.create1'); }

    // Form Tambah 2 (Simpan File Path) 
    public function create2() { return view('customer.create2'); }

    public function store(Request $request)
    {
        $type = $request->type; // 'blob' atau 'path'
        $img = $request->foto; // Data Base64 dari Kamera
        
        $data = $request->except(['foto', 'type']);
        
        if ($type == 'blob') {
            // Ubah Base64 ke Binary untuk masuk ke kolom binary/blob 
            $image_parts = explode(";base64,", $img);
            $data['foto_blob'] = base64_decode($image_parts[1]);
        } else {
            // Simpan sebagai file fisik dan catat alamatnya di database 
            $image_parts = explode(";base64,", $img);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = 'cust_' . time() . '.png';
            Storage::put('public/customers/' . $fileName, $image_base64);
            $data['foto_path'] = 'customers/' . $fileName;
        }

        Customer::create($data);
        return response()->json(['success' => 'Data Berhasil Disimpan!']);
    }
}
