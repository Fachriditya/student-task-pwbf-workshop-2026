@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-success text-white me-2">
                <i class="mdi mdi-qrcode-scan"></i>
            </span> Scanner Vendor Kantin
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span>Scan QR Code dari Customer</span>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card border-success">
                <div class="card-body">
                    <div class="bg-light p-3 rounded mb-4 border text-center">
                        <h5 class="text-success mb-0"><i class="mdi mdi-camera"></i> Area Pemindai Kantin</h5>
                    </div>
                    
                    <p class="card-description text-center">Arahkan QR Code dari HP Customer ke kamera</p>
                    
                    <div id="reader" width="100%"></div>
                    
                    <audio id="beepSound" src="{{ asset('audio/beep.mp3') }}" preload="auto"></audio>
                    
                    <button class="btn btn-gradient-success w-100 mt-4 d-none" id="btnReset" onclick="location.reload()">
                        <i class="mdi mdi-refresh"></i> Scan Pelanggan Berikutnya
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="bg-light p-3 rounded mb-4 border">
                        <h5 class="text-success mb-0"><i class="mdi mdi-receipt"></i> Detail Pesanan</h5>
                    </div>

                    <div id="standbyCard" class="text-center p-5">
                        <i class="mdi mdi-qrcode-scan text-muted" style="font-size: 80px;"></i>
                        <h4 class="text-muted mt-3">Menunggu Scan QR Code...</h4>
                    </div>

                    <div id="loadingData" class="d-none text-center p-5">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="mt-3 text-muted">Mengecek data pesanan...</p>
                    </div>

                    <div id="resultCard" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover border">
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-success text-center py-3">
                                            <h4><i class="mdi mdi-check-circle"></i> Pesanan Teridentifikasi</h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th width="35%" class="bg-light">ID Pesanan</th>
                                        <td><label class="badge badge-gradient-dark" id="resId">-</label></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Menu Dipesan</th>
                                        <td><strong id="resMenu" class="text-primary">-</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Status Bayar</th>
                                        <td><h3 class="mb-0" id="resStatus">-</h3></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() { // Tunggu DOM beres dimuat
        
        // Panggil fungsi Library eksternal (html5-qrcode) untuk mengontrol kamera HP/Laptop
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false // Konfigurasi: fps (frame per detik) 10, kotak panduan scan 250x250
        );

        // Fungsi ini akan otomatis dipicu oleh Library jika kamera mendeteksi ada QR Code
        function onScanSuccess(decodedText, decodedResult) { 
            // Variabel 'decodedText' berisi tulisan murni dari dalam QR Code (dalam kasusmu: ID Pesanan pelanggan)
            
            // 1. Dikeluarkan bunyi "beep" pendek
            let beep = document.getElementById('beepSound'); // Ambil elemen MP3 di HTML
            if(beep) beep.play().catch(e => console.log('Autoplay ditahan')); // Putar suara. Catch untuk mencegah layar error (merah) kalau browser kebetulan memblokir fitur suara otomatis

            // 2. Scanner berhenti scan
            html5QrcodeScanner.clear().then(() => { // Matikan akses kamera sejenak agar QR tidak terscan dobel
                $('#btnReset').removeClass('d-none'); // Tampilkan tombol "Scan Pelanggan Berikutnya" dengan menghapus class d-none (display: none)
            });

            // 3. Ubah Tampilan UI ke Mode Loading
            $('#standbyCard').addClass('d-none'); // Hilangkan gambar ikon gede "Menunggu Scan"
            $('#resultCard').addClass('d-none'); // Sembunyikan tabel hasil untuk berjaga-jaga
            $('#loadingData').removeClass('d-none'); // Munculkan lingkaran muter (Loading) karena butuh waktu kontak ke server

            // 4. Menampilkan menu yang dipesan dan status bayarnya lewat AJAX
            $.ajax({ // Kirim ID Pesanan hasil scan tadi ke server Laravel
                url: "/kantin/api/scan-pesanan/" + decodedText, // Alamat API ditambah ID Pesanan (decodedText)
                type: "GET", // Ambil data
                success: function(response) { // Jika server selesai mencari dan membalas
                    $('#loadingData').addClass('d-none'); // Matikan lingkaran Loading muter
                    
                    if(response.success) { // Jika pesanannya DITEMUKAN di database
                        $('#resId').text("#" + response.data.id_pesanan); // Tulis nomor pesanan ke layar
                        $('#resMenu').text(response.data.menu); // Tulis nama menu yang dipesan
                        
                        // Cek Visual Status Bayar (Ini untuk keamanan Vendor)
                        let statusHtml = response.data.status_bayar === 'LUNAS' // Mengecek kata di database
                            ? `<span class="text-success"><i class="mdi mdi-check-decagram"></i> LUNAS</span>` // Kalau lunas, cetak teks hijau
                            : `<span class="text-danger"><i class="mdi mdi-close-circle"></i> ${response.data.status_bayar}</span>`; // Kalau belum/gagal, cetak teks merah dan nama errornya
                        
                        $('#resStatus').html(statusHtml); // Suntikkan HTML status lunas/merah tadi ke layar
                        $('#resultCard').removeClass('d-none'); // Munculkan tabel hasilnya agar bisa dibaca vendor

                        // Munculkan notif kecil (toast) sukses di kanan atas tanpa mengganggu layar
                        Swal.fire({ title: 'Berhasil!', text: 'Pesanan ditemukan', icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    } else { // Jika pesanannya TIDAK DITEMUKAN (mungkin pelanggan nunjukin QR palsu/lama)
                        $('#standbyCard').removeClass('d-none'); // Balikin UI ke mode Standby awal
                        Swal.fire('Tidak Ditemukan!', response.message, 'warning'); // Tampilkan pop-up peringatan gede
                    }
                },
                error: function() { // Jika server Laravel mati atau internet vendor putus
                    $('#loadingData').addClass('d-none'); // Matikan loading
                    $('#standbyCard').removeClass('d-none'); // Kembali ke Standby
                    Swal.fire('Error!', 'Terjadi kesalahan saat menghubungi server Kantin.', 'error'); // Pop-up silang merah
                }
            });
        }

        // Perintah pamungkas untuk menyalakan modul scanner ke dalam kotak "reader" dan merajut fungsi "onScanSuccess" di atas sebagai respon hasilnya
        html5QrcodeScanner.render(onScanSuccess); 
    });
</script>
@endsection