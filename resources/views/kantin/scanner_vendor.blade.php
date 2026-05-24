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
    $(document).ready(function() {
        
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false
        );

        function onScanSuccess(decodedText, decodedResult) {
            
            // 1. Dikeluarkan bunyi "beep" pendek
            let beep = document.getElementById('beepSound');
            if(beep) beep.play().catch(e => console.log('Autoplay ditahan'));

            // 2. Scanner berhenti scan
            html5QrcodeScanner.clear().then(() => {
                $('#btnReset').removeClass('d-none');
            });

            $('#standbyCard').addClass('d-none');
            $('#resultCard').addClass('d-none');
            $('#loadingData').removeClass('d-none');

            // 3. Menampilkan menu yang dipesan dan status bayarnya lewat AJAX
            $.ajax({
                url: "/kantin/api/scan-pesanan/" + decodedText,
                type: "GET",
                success: function(response) {
                    $('#loadingData').addClass('d-none');
                    
                    if(response.success) {
                        $('#resId').text("#" + response.data.id_pesanan);
                        $('#resMenu').text(response.data.menu);
                        
                        // Cek Visual Status Bayar
                        let statusHtml = response.data.status_bayar === 'LUNAS' 
                            ? `<span class="text-success"><i class="mdi mdi-check-decagram"></i> LUNAS</span>`
                            : `<span class="text-danger"><i class="mdi mdi-close-circle"></i> ${response.data.status_bayar}</span>`;
                        
                        $('#resStatus').html(statusHtml);
                        $('#resultCard').removeClass('d-none');

                        Swal.fire({ title: 'Berhasil!', text: 'Pesanan ditemukan', icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    } else {
                        $('#standbyCard').removeClass('d-none');
                        Swal.fire('Tidak Ditemukan!', response.message, 'warning');
                    }
                },
                error: function() {
                    $('#loadingData').addClass('d-none');
                    $('#standbyCard').removeClass('d-none');
                    Swal.fire('Error!', 'Terjadi kesalahan saat menghubungi server Kantin.', 'error');
                }
            });
        }

        html5QrcodeScanner.render(onScanSuccess);
    });
</script>
@endsection