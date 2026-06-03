<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Midtrans\Config;
use Midtrans\Snap;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KantinController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();

        $lastGuest = Pesanan::where('nama', 'LIKE', 'Guest_%')
            ->orderBy('idpesanan', 'desc')
            ->first();

        if (!$lastGuest) {
            $nextGuest = "Guest_0000001";
        } else {
            $lastNumber = (int) substr($lastGuest->nama, 6);
            $nextGuest = "Guest_" . str_pad($lastNumber + 1, 7, '0', STR_PAD_LEFT);
        }

        return view('kantin.index', compact('vendors', 'nextGuest'));
    }

    public function getMenuByVendor($id)
    {
        $menu = Menu::where('idvendor', $id)->get();
        return response()->json($menu);
    }

    public function bayar(Request $request)
    {
        try {
            \Log::info('Request data:', $request->all());

            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $pesanan = Pesanan::create([
                'nama' => $request->nama_guest ?? 'Guest_Unknown',
                'timestamp' => now(),
                'total' => (int) $request->total,
                'status_bayar' => 1,
            ]);

            $items = $request->input('items', []);
            
            if (empty($items)) {
                return response()->json([
                    'error' => 'Keranjang kosong! Tambahkan menu terlebih dahulu.'
                ], 400);
            }

            foreach ($items as $item) {
                if (!isset($item['id']) || !isset($item['qty']) || !isset($item['harga'])) {
                    continue;
                }

                DetailPesanan::create([
                    'idpesanan' => $pesanan->idpesanan,
                    'idmenu'    => $item['id'],
                    'jumlah'    => (int)$item['qty'],
                    'harga'     => (int)$item['harga'],
                    'subtotal'  => (int)$item['subtotal']
                ]);
            }

            $params = [
                'transaction_details' => [
                    'order_id' => 'KANTIN-' . uniqid() . '-' . $pesanan->idpesanan, 
                    'gross_amount' => (int) $request->total,
                ],
            ];
            $snapToken = Snap::getSnapToken($params);
            
            $pesanan->update(['snap_token' => $snapToken]);

            return response()->json([
                'snap_token' => $snapToken,
                'idpesanan'  => $pesanan->idpesanan
            ]);

        } catch (\Exception $e) {
            \Log::error('Error bayar:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'error' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function nota($id)
    {
        $pesanan = Pesanan::find($id);
        if (!$pesanan) {
            return "Error: Pesanan ID $id tidak ditemukan di database.";
        }

        $detail = DetailPesanan::where('idpesanan', $id)
                    ->join('menus', 'detail_pesanans.idmenu', '=', 'menus.idmenu')
                    ->get();

        try {
            $qrCode = new \Endroid\QrCode\QrCode((string)$pesanan->idpesanan);
            
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            
            $qrUri = $result->getDataUri();

            return view('kantin.nota', compact('pesanan', 'detail', 'qrUri'));

        } catch (\Exception $e) {
            return "Gagal membuat QR Code: " . $e->getMessage();
        }
    }

    public function scannerVendor()
    {
        return view('kantin.scanner_vendor');
    }

   public function getPesananData($id)
    {
        $pesanan = DB::table('pesanans')->where('idpesanan', $id)->first();

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan dengan ID #' . $id . ' tidak ditemukan di database Kantin!'
            ]);
        }

        $detailMenu = DB::table('detail_pesanans')
            ->join('menus', 'detail_pesanans.idmenu', '=', 'menus.idmenu')
            ->where('detail_pesanans.idpesanan', $id)
            ->select('menus.nama_menu', 'detail_pesanans.jumlah') 
            ->get();

        $listMenu = [];
        foreach($detailMenu as $dm) {
            $listMenu[] = $dm->nama_menu . ' (x' . $dm->jumlah . ')';
        }
        $stringMenu = implode(', ', $listMenu);
        
        if (empty($stringMenu)) {
            $stringMenu = 'Tidak ada rincian menu';
        }

        $statusText = ($pesanan->status_bayar == 1) ? 'LUNAS' : 'BELUM BAYAR';

        return response()->json([
            'success' => true,
            'data' => [
                'id_pesanan' => $pesanan->idpesanan,
                'menu' => $stringMenu,
                'status_bayar' => $statusText
            ]
        ]);
    }

    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $orderParts = explode('-', $request->order_id);
                $id = $orderParts[0];
                
                $pesanan = Pesanan::find($id);
                if ($pesanan) {
                    $pesanan->update(['status_bayar' => 2]);
                }
            }
        }
    }
}