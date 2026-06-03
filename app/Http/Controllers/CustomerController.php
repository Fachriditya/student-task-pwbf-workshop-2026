<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index() {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }

    public function create1() { return view('customer.create1'); }

    public function create2() { return view('customer.create2'); }

    public function store(Request $request)
    {
        $type = $request->type;
        $img = $request->foto;
        
        $data = $request->except(['foto', 'type']);
        
        if ($type == 'blob') {
            $image_parts = explode(";base64,", $img);
            $data['foto_blob'] = base64_decode($image_parts[1]);
        } else {
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
