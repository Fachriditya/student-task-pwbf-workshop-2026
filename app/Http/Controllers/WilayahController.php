<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function index() 
    {
        return view('wilayah.index');
    }

    public function getProvinsi() 
    {
        $data = DB::table('reg_provinces')->get(); 
        return response()->json([
            'status' => 'success', 
            'data'   => $data
        ]); 
    }

    public function getKota($id) 
    {
        $data = DB::table('reg_regencies')->where('province_id', $id)->get();
        return response()->json([
            'status' => 'success', 
            'data'   => $data
        ]);
    }

    public function getKecamatan($id) 
    {
        $data = DB::table('reg_districts')->where('regency_id', $id)->get();
        return response()->json([
            'status' => 'success', 
            'data'   => $data
        ]);
    }

    public function getKelurahan($id) 
    {
        $data = DB::table('reg_villages')->where('district_id', $id)->get();
        return response()->json([
            'status' => 'success', 
            'data'   => $data
        ]);
    }
}