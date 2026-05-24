<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode Toko - {{ $toko->nama_toko }}</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .card { border: 2px dashed #000; display: inline-block; padding: 30px; border-radius: 10px; }
        h2 { margin: 0 0 10px 0; color: #333; }
        .barcode-container { margin: 20px 0; }
        .info { font-size: 14px; color: #666; }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>{{ $toko->nama_toko }}</h2>
        <p class="info">Kode Toko: <strong>{{ $toko->barcode }}</strong></p>
        
        <div class="barcode-container">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $toko->barcode }}" alt="Barcode Toko">
        </div>
        
        <p class="info">Titik Koordinat: {{ $toko->latitude }}, {{ $toko->longitude }}</p>
    </div>

    <br><br>
    <button class="btn-print" onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Cetak Barcode</button>
</body>
</html>