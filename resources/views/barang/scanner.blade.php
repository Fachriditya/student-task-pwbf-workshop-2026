@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-barcode-scan"></i>
            </span> Scanner Barang UMKM
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span>Pindai Barcode Tag Harga</span>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="bg-light p-3 rounded mb-4 border text-center">
                        <h5 class="text-info mb-0"><i class="mdi mdi-camera"></i> Area Pemindai</h5>
                    </div>
                    
                    <p class="card-description text-center">Arahkan barcode / stiker ke kamera</p>
                    
                    <div id="reader" width="100%"></div>
                    
                    <audio id="beepSound" src="{{ asset('audio/beep.mp3') }}" preload="auto"></audio>
                    
                    <button class="btn btn-gradient-info w-100 mt-4 d-none" id="btnReset" onclick="location.reload()">
                        <i class="mdi mdi-refresh"></i> Scan Ulang
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="bg-light p-3 rounded mb-4 border">
                        <h5 class="text-primary mb-0"><i class="mdi mdi-clipboard-text"></i> Hasil Pemindaian</h5>
                    </div>

                    <div id="standbyCard" class="text-center p-5">
                        <i class="mdi mdi-barcode-scan text-muted" style="font-size: 80px;"></i>
                        <h4 class="text-muted mt-3">Menunggu Scan...</h4>
                    </div>

                    <div id="loadingData" class="d-none text-center p-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Mengambil data dari database...</p>
                    </div>

                    <div id="resultCard" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover border">
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-success text-center py-3">
                                            <h4><i class="mdi mdi-check-circle"></i> Barcode Teridentifikasi</h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th width="35%" class="bg-light">Kode / ID Barang</th>
                                        <td><label class="badge badge-gradient-primary" id="resId">-</label></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Nama Barang</th>
                                        <td><strong id="resNama">-</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Harga</th>
                                        <td><h3 class="text-success mb-0" id="resHarga">-</h3></td>
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
    $(document).ready(function() {
        
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false
        );

        function onScanSuccess(decodedText, decodedResult) {
            
            // 1. Mainkan Suara Beep
            let beep = document.getElementById('beepSound');
            if(beep) beep.play().catch(e => console.log('Autoplay audio ditahan browser'));

            // 2. Hentikan Scanner
            html5QrcodeScanner.clear().then(() => {
                $('#btnReset').removeClass('d-none');
            });

            // 3. Ubah UI ke mode Loading
            $('#standbyCard').addClass('d-none');
            $('#resultCard').addClass('d-none');
            $('#loadingData').removeClass('d-none');

            // 4. Proses AJAX
            $.ajax({
                url: "/barang/api/scan/" + decodedText,
                type: "GET",
                success: function(response) {
                    $('#loadingData').addClass('d-none');
                    
                    if(response.success) {
                        // Tampilkan hasil ke tabel
                        $('#resId').text(response.data.id_barang);
                        $('#resNama').text(response.data.nama);
                        
                        // Format angka ke Rupiah dengan Intl.NumberFormat (Sama dengan Kasir)
                        let hargaRupiah = new Intl.NumberFormat('id-ID').format(response.data.harga);
                        $('#resHarga').text("Rp " + hargaRupiah);
                        
                        $('#resultCard').removeClass('d-none');

                        // Notifikasi Toast dari SweetAlert
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Barang ditemukan',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        // Kembali ke tampilan Standby dan munculkan Error Swal
                        $('#standbyCard').removeClass('d-none');
                        Swal.fire('Tidak Ditemukan!', response.message, 'warning');
                    }
                },
                error: function() {
                    $('#loadingData').addClass('d-none');
                    $('#standbyCard').removeClass('d-none');
                    Swal.fire('Error!', 'Terjadi kesalahan saat menghubungi server.', 'error');
                }
            });
        }

        // Jalankan scanner
        html5QrcodeScanner.render(onScanSuccess);
    });
</script>
@endsection