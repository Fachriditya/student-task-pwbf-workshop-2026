<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            text-align: center;
            border: 15px solid #2c3e50;
            padding: 60px;
        }

        h1 {
            font-size: 50px;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 36px;
            margin: 20px 0;
        }

        p {
            font-size: 18px;
        }

        .signature {
            margin-top: 80px;
        }
    </style>
</head>
<body>

    <h1>SERTIFIKAT</h1>

    <p>Diberikan kepada:</p>

    <h2>{{ $name }}</h2>

    <p>Atas partisipasinya dalam kegiatan</p>

    <h3>{{ $event }}</h3>

    <p>Pada tanggal {{ $date }}</p>

    <div class="signature">
        <p>Owner Toko Buku</p>
        <br><br>
        <strong>{{ $owner }}</strong>
    </div>

</body>
</html>