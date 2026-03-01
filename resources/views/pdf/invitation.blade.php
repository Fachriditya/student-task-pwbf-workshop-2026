<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Undangan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid black;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .content {
            margin-top: 30px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>TOKO BUKU CERDAS</h2>
        <p>Jl. Ilmu Pengetahuan No. 10, Surabaya</p>
        <p>Email: tokobuku@cerdas.com</p>
    </div>

    <h3>{{ $title }}</h3>

    <div class="content">
        <p>{{ $content }}</p>
    </div>

    <br><br>

    <p>Surabaya, {{ $date }}</p>

    <br><br>

    <p>Hormat kami,</p>
    <br><br>
    <strong>Manajemen Toko Buku</strong>

</body>
</html>