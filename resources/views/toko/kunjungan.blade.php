@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-danger text-white me-2">
                <i class="mdi mdi-map-marker-distance"></i>
            </span> Absensi Kunjungan Sales
        </h3>
    </div>

    <div class="row">
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h5 class="text-danger mb-3"><i class="mdi mdi-camera"></i> Scan Barcode Toko</h5>
                    <div id="reader" width="100%"></div>
                    <audio id="beepSound" src="{{ asset('audio/beep.mp3') }}" preload="auto"></audio>
                </div>
            </div>
        </div>

        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-dark mb-3"><i class="mdi mdi-shield-check"></i> Proses Validasi GPS</h5>
                    
                    <div id="statusProses" class="text-muted p-3 border rounded mb-3 bg-light">
                        1. Arahkan kamera ke Barcode Toko.<br>
                        2. Sistem akan otomatis melacak GPS-mu.<br>
                        3. Jangan tutup halaman ini saat loading.
                    </div>

                    <div id="resultCard" class="d-none text-center p-4 rounded text-white mt-3">
                        <h2 id="resStatus" class="mb-2">-</h2>
                        <h4 id="resToko" class="mb-3">-</h4>
                        <p id="resPesan" class="m-0"></p>
                        <button class="btn btn-light btn-sm mt-4" onclick="location.reload()">Scan Toko Lain</button>
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
    // Fungsi Ambil GPS Akurat
    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
        return new Promise((resolve, reject) => {
            let bestResult = null;
            const startTime = Date.now();
            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const acc = position.coords.accuracy;
                    if (!bestResult || acc < bestResult.coords.accuracy) { bestResult = position; }
                    if (acc <= targetAccuracy) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                    }
                },
                (error) => reject(error),
                { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
            );

            setTimeout(() => {
                navigator.geolocation.clearWatch(watchId);
                if (bestResult) resolve(bestResult);
                else reject(new Error("Gagal mengunci sinyal GPS."));
            }, maxWait);
        });
    }

    $(document).ready(function() {
        let scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);

        async function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('beepSound').play().catch(e => {});
            scanner.clear(); // Hentikan kamera
            
            let statusBox = $('#statusProses');
            statusBox.html('<span class="text-warning"><i class="mdi mdi-loading mdi-spin"></i> Barcode terbaca! Sedang mengunci lokasi GPS Anda...</span>');

            try {
                // 1. Ambil GPS Sales
                const pos = await getAccuratePosition(50); 
                
                statusBox.html('<span class="text-info"><i class="mdi mdi-loading mdi-spin"></i> GPS terkunci! Mengirim data ke server...</span>');

                // 2. Kirim data ke Server untuk dihitung jaraknya
                $.ajax({
                    url: "{{ route('kunjungan.proses') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        barcode: decodedText,
                        lat_sales: pos.coords.latitude,
                        lng_sales: pos.coords.longitude,
                        acc_sales: pos.coords.accuracy
                    },
                    success: function(res) {
                        statusBox.addClass('d-none');
                        let resultCard = $('#resultCard');
                        resultCard.removeClass('d-none');

                        if(res.success) {
                            $('#resToko').text(res.toko);
                            $('#resStatus').text(res.status);
                            $('#resPesan').text(res.message);

                            if(res.is_valid) {
                                resultCard.addClass('bg-success'); // DITERIMA (Hijau)
                                Swal.fire('Berhasil!', 'Kunjungan Sah!', 'success');
                            } else {
                                resultCard.addClass('bg-danger'); // DITOLAK (Merah)
                                Swal.fire('Ditolak!', 'Jarak terlalu jauh!', 'error');
                            }
                        } else {
                            resultCard.addClass('bg-dark');
                            $('#resStatus').text('ERROR');
                            $('#resPesan').text(res.message);
                        }
                    }
                });

            } catch (error) {
                statusBox.html('<span class="text-danger"><i class="mdi mdi-alert"></i> Gagal ambil GPS: ' + error.message + '</span>');
                Swal.fire('Error GPS', 'Pastikan GPS menyala dan diizinkan.', 'error');
            }
        }
        scanner.render(onScanSuccess);
    });
</script>
@endsection