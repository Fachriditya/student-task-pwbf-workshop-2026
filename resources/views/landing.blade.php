<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pujasera & Antrian</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .hover-card { transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; }
        .hover-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
        .icon-huge { font-size: 80px; line-height: 1; }
    </style>
</head>
<body class="bg-light">
    <audio id="suaraDingdong" src="{{ asset('audio/beep.mp3') }}" preload="auto"></audio>

    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 py-5">
        
        <div class="text-center mb-5">
            <h1 class="display-4 font-weight-bold text-primary mb-3">
                <i class="mdi mdi-food-fork-drink"></i> Sistem Pujasera Terpadu
            </h1>
            <p class="text-muted lead">Silakan pilih layanan yang ingin Anda akses</p>
        </div>

        <div class="row w-100 justify-content-center">
            
            <div class="col-md-4 mb-4 stretch-card">
                <div class="card hover-card border-0 shadow-sm w-100" onclick="window.location.href='{{ route('kantin.index') }}'"> <div class="card-body text-center p-5 d-flex flex-column justify-content-center align-items-center">
                        <i class="mdi mdi-silverware-variant icon-huge text-success mb-3"></i>
                        <h3 class="font-weight-bold text-dark">Pesan Makanan</h3>
                        <p class="text-muted">Lihat menu dan pesan makanan sebagai pengunjung.</p>
                        <span class="btn btn-outline-success rounded-pill mt-auto">Masuk Guest</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 stretch-card">
                <div class="card hover-card border-0 shadow-sm w-100" onclick="ambilAntrianCepat()">
                    <div class="card-body text-center p-5 d-flex flex-column justify-content-center align-items-center">
                        <i class="mdi mdi-ticket-account icon-huge text-info mb-3"></i>
                        <h3 class="font-weight-bold text-dark">Ambil Antrian</h3>
                        <p class="text-muted">Dapatkan nomor karcis digital untuk antrian pesanan.</p>
                        <span class="btn btn-outline-info rounded-pill mt-auto">Cetak Karcis</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 stretch-card">
                <div class="card hover-card border-0 shadow-sm w-100" onclick="window.location.href='{{ route('login') }}'">
                    <div class="card-body text-center p-5 d-flex flex-column justify-content-center align-items-center">
                        <i class="mdi mdi-shield-account icon-huge text-primary mb-3"></i>
                        <h3 class="font-weight-bold text-dark">Admin Panel</h3>
                        <p class="text-muted">Jalur masuk khusus untuk petugas loket dan dapur.</p>
                        <span class="btn btn-outline-primary rounded-pill mt-auto">Login Admin</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function ambilAntrianCepat() {
            document.getElementById('suaraDingdong').play().catch(e => {});

            Swal.fire({
                title: 'Mencetak Karcis...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('antrian.daftar.cepat') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'NOMOR ANTRIAN ANDA',
                            html: '<h1 style="font-size: 80px; font-weight: bold; color: #b66dff;">' + response.nomor + '</h1><p class="text-muted">ID: ' + response.nama + '</p><p>Silakan tunggu panggilan dari petugas loket.</p>',
                            confirmButtonText: 'Tutup & Tunggu',
                            confirmButtonColor: '#b66dff'
                        });
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil antrian', 'error');
                }
            });
        }
    </script>
</body>
</html>