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
    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) { // (Sama persis dengan penjelasan di create.blade.php. Berfungsi mengunci sinyal GPS terbaik di bawah 50 meter dalam waktu 20 detik)
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

    $(document).ready(function() { // Tunggu HTML selesai dimuat
        let scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} }, false); // Inisialisasi kamera pembaca barcode

        async function onScanSuccess(decodedText, decodedResult) { // Berjalan otomatis saat kamera berhasil membaca sebuah barcode/QR
            document.getElementById('beepSound').play().catch(e => {}); // Putar bunyi 'beep'
            scanner.clear(); // Langsung matikan kamera HP sales agar tidak berat dan tidak terjadi scan dobel
            
            let statusBox = $('#statusProses'); // Ambil elemen kotak status abu-abu
            statusBox.html('<span class="text-warning"><i class="mdi mdi-loading mdi-spin"></i> Barcode terbaca! Sedang mengunci lokasi GPS Anda...</span>'); // Ubah instruksi menjadi pesan loading kuning bahwa GPS sedang dicari

            try { // Mulai operasi pengambilan GPS
                // 1. Ambil GPS Sales
                const pos = await getAccuratePosition(50); // Panggil fungsi pelacak satelit, kodenya akan "berhenti" di baris ini sampai HP menemukan lokasi sales saat itu juga
                
                statusBox.html('<span class="text-info"><i class="mdi mdi-loading mdi-spin"></i> GPS terkunci! Mengirim data ke server...</span>'); // Jika dapat sinyal, ubah teks jadi biru. Bersiap mengirim data ke Laravel

                // 2. Kirim data ke Server untuk dihitung jaraknya
                $.ajax({
                    url: "{{ route('kunjungan.proses') }}", // Arahkan ke endpoint Controller
                    type: "POST", // Pakai POST
                    data: {
                        _token: '{{ csrf_token() }}', // Tiket keamanan form
                        barcode: decodedText, // Kirim teks ID Toko hasil jepretan barcode tadi
                        lat_sales: pos.coords.latitude, // Kirim titik Latitude Sales saat ini
                        lng_sales: pos.coords.longitude, // Kirim titik Longitude Sales saat ini
                        acc_sales: pos.coords.accuracy // Kirim tingkat akurasi (sebagai bukti seberapa melenceng GPS HP si Sales)
                    },
                    success: function(res) { // Jika Laravel selesai menghitung rumus Haversine (Jarak Bumi) dan membalas
                        statusBox.addClass('d-none'); // Sembunyikan kotak status abu-abu
                        let resultCard = $('#resultCard'); // Panggil kartu hasil validasi (yang tadi tersembunyi)
                        resultCard.removeClass('d-none'); // Tampilkan ke layar

                        if(res.success) { // Jika barcode tokonya memang terdaftar di database
                            $('#resToko').text(res.toko); // Tampilkan Nama Toko
                            $('#resStatus').text(res.status); // Tampilkan teks status (DITERIMA / DITOLAK)
                            $('#resPesan').text(res.message); // Tampilkan pesan detail (misal: "Jarak Anda 10 Meter. Valid.")

                            if(res.is_valid) { // LOGIKA UTAMA: Apakah Controller menyatakan jaraknya masuk akal (Valid)?
                                resultCard.addClass('bg-success'); // DITERIMA (Ubah latar kotak jadi warna Hijau)
                                Swal.fire('Berhasil!', 'Kunjungan Sah!', 'success'); // Pop-up centang
                            } else { // Jika jaraknya kejauhan (Si sales curiga lagi nongkrong di warung kopi jauh dari toko)
                                resultCard.addClass('bg-danger'); // DITOLAK (Ubah latar kotak jadi warna Merah)
                                Swal.fire('Ditolak!', 'Jarak terlalu jauh!', 'error'); // Pop-up silang
                            }
                        } else { // Jika barcode tidak terdaftar
                            resultCard.addClass('bg-dark'); // Kotak jadi warna Hitam
                            $('#resStatus').text('ERROR'); // Tulis teks Error
                            $('#resPesan').text(res.message); // Tulis "Toko tidak ditemukan"
                        }
                    }
                });

            } catch (error) { // Jika proses pelacakan GPS di awal tadi gagal (misal GPS HP si sales dimatikan)
                statusBox.html('<span class="text-danger"><i class="mdi mdi-alert"></i> Gagal ambil GPS: ' + error.message + '</span>'); // Munculkan error di layar
                Swal.fire('Error GPS', 'Pastikan GPS menyala dan diizinkan.', 'error'); // Pop-up pengingat
            }
        }
        scanner.render(onScanSuccess); // Eksekusi penyalaan kamera dan ikatkan fungsi scan di atas
    });
</script>
@endsection