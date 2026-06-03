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
    $(document).ready(function() { // Menunggu seluruh elemen HTML di halaman selesai dimuat agar tidak ada error elemen tidak ditemukan
        
        let html5QrcodeScanner = new Html5QrcodeScanner( // Membuat objek scanner baru dari library Html5QrcodeScanner
            "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false // Konfigurasi: tempelkan ke elemen id="reader", baca dengan kecepatan 10 Frame/detik, ukuran kotak panduan scan 250x250px
        );

        function onScanSuccess(decodedText, decodedResult) { // Mendeklarasikan fungsi yang akan otomatis berjalan SAAT kamera berhasil membaca barcode
            
            // 1. Mainkan Suara Beep
            let beep = document.getElementById('beepSound'); // Cari elemen audio MP3 'beepSound' di HTML
            if(beep) beep.play().catch(e => console.log('Autoplay audio ditahan browser')); // Putar suaranya. Perintah .catch dipakai untuk mencegah error di console jika browser memblokir audio otomatis

            // 2. Hentikan Scanner
            html5QrcodeScanner.clear().then(() => { // Matikan kamera scanner seketika agar tidak terus-terusan men-scan barcode yang sama
                $('#btnReset').removeClass('d-none'); // Munculkan tombol "Scan Ulang" dengan membuang class 'd-none' (display: none)
            });

            // 3. Ubah UI ke mode Loading
            $('#standbyCard').addClass('d-none'); // Sembunyikan ikon raksasa "Menunggu Scan..."
            $('#resultCard').addClass('d-none'); // Sembunyikan tabel hasil (berjaga-jaga jika sebelumnya ada hasil pencarian lain)
            $('#loadingData').removeClass('d-none'); // Munculkan animasi bulat muter (spinner) loading

            // 4. Proses AJAX
            $.ajax({ // Kirim data hasil scan kamera ke server Laravel
                url: "/barang/api/scan/" + decodedText, // Alamat tujuan URL-nya, langsung disambung dengan teks barcode hasil scan (decodedText)
                type: "GET", // Pakai metode GET karena kita hanya meminta informasi dari server
                success: function(response) { // Jika server merespons data dengan sukses
                    $('#loadingData').addClass('d-none'); // Matikan (sembunyikan) animasi loading
                    
                    if(response.success) { // Jika data barang yang dicari ADA di database
                        // Tampilkan hasil ke tabel
                        $('#resId').text(response.data.id_barang); // Ganti teks '-' pada kolom ID menjadi kode barang asli dari server
                        $('#resNama').text(response.data.nama); // Ganti teks kolom nama
                        
                        // Format angka ke Rupiah dengan Intl.NumberFormat (Sama dengan Kasir)
                        let hargaRupiah = new Intl.NumberFormat('id-ID').format(response.data.harga); // Format angka ribuan, contoh: 50000 -> 50.000
                        $('#resHarga').text("Rp " + hargaRupiah); // Tampilkan ke layar dengan tambahan 'Rp' di depannya
                        
                        $('#resultCard').removeClass('d-none'); // Munculkan kerangka tabel hasil yang berisi data di atas

                        // Notifikasi Toast dari SweetAlert
                        Swal.fire({ // Panggil pop-up notifikasi
                            title: 'Berhasil!',
                            text: 'Barang ditemukan',
                            icon: 'success', // Ikon centang
                            toast: true, // Aktifkan mode toast (bentuknya kecil memanjang, bukan pop-up kotak di tengah layar)
                            position: 'top-end', // Posisikan toast di sudut kanan atas
                            showConfirmButton: false, // Hilangkan tombol OK
                            timer: 2000 // Otomatis hilang sendiri setelah 2 detik
                        });
                    } else {
                        // Kembali ke tampilan Standby dan munculkan Error Swal
                        $('#standbyCard').removeClass('d-none'); // Jika barang TIDAK ADA, kembalikan layar ke mode Standby
                        Swal.fire('Tidak Ditemukan!', response.message, 'warning'); // Tampilkan pop-up peringatan kuning
                    }
                },
                error: function() { // Jika server mati atau ada error koneksi internet
                    $('#loadingData').addClass('d-none'); // Sembunyikan loading
                    $('#standbyCard').removeClass('d-none'); // Kembali ke mode Standby
                    Swal.fire('Error!', 'Terjadi kesalahan saat menghubungi server.', 'error'); // Tampilkan pop-up silang merah
                }
            });
        }

        // Jalankan scanner
        html5QrcodeScanner.render(onScanSuccess); // Perintah pamungkas untuk menyalakan modul kamera ke layar, dan mengikat fungsi "onScanSuccess" sebagai penerima hasilnya
    });
</script>
@endsection