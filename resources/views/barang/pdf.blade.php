<!DOCTYPE html>
<html>
<head>
    <style>
        @page { 
            margin: 0.5cm; 
        }
        body { 
            font-family: sans-serif; 
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0.3cm;
            table-layout: fixed;
        }
        td {
            width: 32mm;
            height: 18mm;
            border: 0.5px solid #000;
            text-align: center;
            vertical-align: middle;
            box-sizing: border-box;
            padding: 2px;
            overflow: hidden;
        }
        .nama-barang {
            font-size: 9px;
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .barcode-img {
            width: 95px;
            height: 18px;
            display: block;
            margin: 0 auto;
        }
        .id-barang {
            font-size: 8px;
            display: block;
            margin-top: 1px;
            margin-bottom: 2px;
        }
        .harga-barang {
            font-size: 9px;
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>
    @php
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    @endphp

    <table>
        @for ($row = 0; $row < 8; $row++)
            <tr>
                @for ($col = 0; $col < 5; $col++)
                    @php
                        $index = ($row * 5) + $col;
                        $b = $labels[$index] ?? null;
                    @endphp
                    
                    <td>
                        @if($b)
                            @php
                                $barcodeData = $generator->getBarcode($b->id_barang, $generator::TYPE_CODE_128);
                                $barcodeBase64 = base64_encode($barcodeData);
                            @endphp
                            
                            <span class="nama-barang">{{ substr($b->nama, 0, 18) }}</span>

                            <div style="margin-top: 4px;" >
                                <img src="data:image/png;base64,{{ $barcodeBase64 }}" class="barcode-img">
                            </div>

                            <span class="id-barang">{{ $b->id_barang }}</span>

                            <span class="harga-barang">Rp {{ number_format($b->harga, 0, ',', '.') }}</span>
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
    </table>
</body>
</html>