<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pesanan - Kantin Online</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body { background: #f2edf3; font-family: 'Poppins', sans-serif; }
        .receipt-card { border-radius: 15px; border-top: 5px solid #1bcfb4; }
        .qr-container { background: #fff; padding: 10px; border-radius: 10px; display: inline-block; border: 1px dashed #ccc; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm receipt-card">
                    <div class="card-body text-center p-5">
                        <i class="mdi mdi-check-circle text-success" style="font-size: 60px;"></i>
                        <h3 class="mt-2 text-success">Pembayaran Berhasil!</h3>
                        <p class="text-muted">Terima kasih, <strong>{{ $pesanan->nama }}</strong></p>

                        <div class="qr-container my-4 shadow-sm">
                            <img src="{{ $qrUri }}" alt="QR Code Pesanan">
                            <p class="mb-0 mt-2 text-muted" style="font-size: 12px;">Scan QR untuk cek status</p>
                        </div>

                        <h5 class="font-weight-bold">ID Pesanan: #{{ $pesanan->idpesanan }}</h5>
                        <p class="mb-4">Waktu: {{ \Carbon\Carbon::parse($pesanan->timestamp)->format('d M Y H:i') }}</p>

                        <table class="table table-borderless text-start mb-4">
                            <thead class="border-bottom">
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detail as $d)
                                <tr>
                                    <td>{{ $d->nama_menu }}</td>
                                    <td>x{{ $d->jumlah }}</td>
                                    <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <th colspan="2">TOTAL BAYAR</th>
                                    <th class="text-end text-primary" style="font-size: 1.2rem;">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                        <a href="{{ url('/kantin') }}" class="btn btn-gradient-primary w-100 btn-lg rounded-pill">
                            <i class="mdi mdi-home"></i> Kembali ke Menu Utama
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>