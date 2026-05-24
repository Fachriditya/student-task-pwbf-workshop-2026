<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // ==============================================
    // 1. AREA GUEST (PENDAFTARAN CEPAT VIA AJAX)
    // ==============================================
    public function daftarCepat(Request $request)
    {
        $nomorTerakhir = Cache::get('antrian_nomor_terakhir', 0);
        $nomorBaru = $nomorTerakhir + 1;
        Cache::put('antrian_nomor_terakhir', $nomorBaru);

        $namaOtomatis = 'Guest-' . str_pad($nomorBaru, 3, '0', STR_PAD_LEFT);

        $menunggu = Cache::get('antrian_menunggu', []);
        $menunggu[] = [
            'nomor' => $nomorBaru,
            'nama' => $namaOtomatis,
            'status' => 'menunggu'
        ];
        Cache::put('antrian_menunggu', $menunggu);

        return response()->json([
            'success' => true, 
            'nomor' => $nomorBaru,
            'nama' => $namaOtomatis
        ]);
    }

    // Fungsi khusus untuk menyuplai data ke Dashboard Admin tanpa memblokir server
    public function apiData()
    {
        return response()->json([
            'sekarang' => Cache::get('antrian_sekarang'),
            'menunggu' => Cache::get('antrian_menunggu', []),
            'terlewat' => Cache::get('antrian_terlewat', []),
            'selesai'  => Cache::get('antrian_selesai', [])
        ]);
    }

    // ==============================================
    // 2. AREA ADMIN (LOKET KASIR)
    // ==============================================
    public function admin()
    {
        return view('antrian.admin');
    }

    // --- FUNGSI GABUNGAN: SELESAIKAN YANG LAMA, PANGGIL YANG BARU ---
    public function panggilSelesai(Request $request)
    {
        $sekarang = Cache::get('antrian_sekarang');
        $diproses = false;

        // 1. Amankan dulu pesanan SEKARANG ke tabel Selesai
        if ($sekarang) {
            $sekarang['status'] = 'selesai';
            $selesai = Cache::get('antrian_selesai', []);
            $selesai[] = $sekarang;
            Cache::put('antrian_selesai', $selesai);
            
            Cache::forget('antrian_sekarang'); // Kosongkan layar utama
            $diproses = true;
        }

        // 2. Cek apakah masih ada antrean di dapur
        $menunggu = Cache::get('antrian_menunggu', []);
        if (!empty($menunggu)) {
            $dipanggil = array_shift($menunggu);
            Cache::put('antrian_menunggu', $menunggu);
            Cache::put('antrian_sekarang', $dipanggil);
            return response()->json(['success' => true]);
        }

        // 3. Jika dapur kosong tapi kita baru saja menyelesaikan pesanan (Kasus Panggil Ulang)
        if ($diproses) {
            return response()->json(['success' => true, 'message' => 'Pesanan selesai. Antrian habis.']);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada antrian aktif!']);
    }

    public function panggilTerlewat(Request $request)
    {
        $sekarang = Cache::get('antrian_sekarang');
        $diproses = false;

        // 1. Amankan pesanan SEKARANG ke tabel Terlewat
        if ($sekarang) {
            $sekarang['status'] = 'terlewat';
            $terlewat = Cache::get('antrian_terlewat', []);
            $terlewat[] = $sekarang;
            Cache::put('antrian_terlewat', $terlewat);
            
            Cache::forget('antrian_sekarang');
            $diproses = true;
        }

        // 2. Cek apakah masih ada antrean di dapur
        $menunggu = Cache::get('antrian_menunggu', []);
        if (!empty($menunggu)) {
            $dipanggil = array_shift($menunggu);
            Cache::put('antrian_menunggu', $menunggu);
            Cache::put('antrian_sekarang', $dipanggil);
            return response()->json(['success' => true]);
        }

        // 3. Jika dapur kosong tapi kita baru saja melewati pesanan
        if ($diproses) {
            return response()->json(['success' => true, 'message' => 'Pesanan dilewati. Antrian habis.']);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada antrian aktif!']);
    }

    public function panggilUlang(Request $request)
    {
        $nomor = $request->nomor;
        $terlewat = Cache::get('antrian_terlewat', []);

        $kandidat = collect($terlewat)->firstWhere('nomor', (int)$nomor);

        if ($kandidat) {
            $terlewatBaru = collect($terlewat)->reject(function ($item) use ($nomor) {
                return $item['nomor'] == (int)$nomor;
            })->values()->all();
            Cache::put('antrian_terlewat', $terlewatBaru);

            Cache::put('antrian_sekarang', $kandidat);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    }

    // ==============================================
    // 3. AREA PAPAN TV (RUANG MAKAN / PUBLIK)
    // ==============================================
    public function papan()
    {
        return view('antrian.papan');
    }

    // ==============================================
    // 4. MESIN RADIO SSE (STREAMING REAL-TIME)
    // ==============================================
    public function stream(Request $request)
    {
        // Mencegah PHP timeout pada koneksi yang panjang
        set_time_limit(0); 

        // KUNCI RAHASIA: Lepaskan sesi agar tombol AJAX tidak nge-lag/ngantre!
        session_write_close();

        return response()->stream(function () {
            while (true) {
                if (connection_aborted()) break;

                $data = [
                    'sekarang' => Cache::get('antrian_sekarang'),
                    'menunggu' => Cache::get('antrian_menunggu', []),
                    'terlewat' => Cache::get('antrian_terlewat', []),
                    'selesai'  => Cache::get('antrian_selesai', [])
                ];

                // Aturan format SSE
                echo 'event: update-antrian' . PHP_EOL;
                echo 'data: ' . json_encode($data) . PHP_EOL;
                echo PHP_EOL; 

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no' 
        ]);
    }
}