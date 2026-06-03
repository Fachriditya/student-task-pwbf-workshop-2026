@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-camera"></i>
            </span> Tambah Customer 1 (BLOB)
        </h3>
    </div>

    <form id="formCustomer1">
        @csrf
        <input type="hidden" name="type" value="blob">
        <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card border-left border-primary shadow">
                    <div class="card-body">
                        <h4 class="card-title text-primary"><i class="mdi mdi-account-card-details"></i> Data Diri</h4>
                        <hr>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control border-primary text-dark" placeholder="Masukkan nama" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control border-primary text-dark" rows="3" placeholder="Alamat lengkap" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <select name="provinsi" id="provinsi" class="form-control text-dark border-primary"><option value="">Pilih Provinsi</option></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kota</label>
                                    <select name="kota" id="kota" class="form-control text-dark" disabled><option value="">Pilih Kota</option></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan" class="form-control text-dark" disabled><option value="">Pilih Kecamatan</option></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kodepos - Kelurahan</label>
                                    <input type="text" name="kodepos" class="form-control border-primary text-dark" placeholder="Contoh: 60111 - Gubeng">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 grid-margin stretch-card">
                <div class="card border-left border-info shadow text-center">
                    <div class="card-body">
                        <h4 class="card-title text-info"><i class="mdi mdi-image-area"></i> Foto Customer</h4>
                        <hr>
                        <div class="border rounded d-flex align-items-center justify-content-center bg-light mb-3" style="height: 250px; overflow: hidden;">
                            <img id="preview_foto" src="" style="max-width: 100%; display:none;">
                            <div id="placeholder" class="text-muted">
                                <i class="mdi mdi-account-circle mdi-48px"></i><br>Pratinjau Foto
                            </div>
                        </div>
                        <input type="hidden" name="foto" id="foto_data">
                        
                        <button type="button" class="btn btn-gradient-info btn-block" data-bs-toggle="modal" data-bs-target="#modalKamera">
                            <i class="mdi mdi-camera-iris"></i> Ambil Foto
                        </button>
                        
                        <button type="submit" class="btn btn-gradient-primary btn-block mt-3" id="btnSimpan">
                            <i class="mdi mdi-content-save"></i> Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="modalKamera" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">Modal Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <label class="badge badge-primary mb-2">Video</label>
                        <video id="webcam" autoplay playsinline width="100%" class="rounded border shadow-sm"></video>
                        <button type="button" class="btn btn-inverse-secondary btn-sm mt-2">Pilihan Kamera</button>
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="badge badge-info mb-2">Snapshot</label>
                        <canvas id="canvas" style="display:none;"></canvas>
                        <img id="snap_result" src="" width="100%" class="rounded border shadow-sm">
                        <button type="button" id="btnCapture" class="btn btn-gradient-danger btn-sm mt-2">
                            <i class="mdi mdi-camera"></i> Ambil Foto
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnUsePhoto" class="btn btn-gradient-success" data-bs-dismiss="modal">Simpan Foto</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() { // Tunggu sampai struktur HTML (DOM) halaman selesai diload
    let video = document.getElementById('webcam'); // Ambil elemen video yang akan memutar tangkapan kamera langsung
    let canvas = document.getElementById('canvas'); // Ambil elemen canvas, ini berfungsi ibarat 'kanvas lukis' tersembunyi tempat foto ditempel sesaat
    let snapResult = document.getElementById('snap_result'); // Ambil elemen img (gambar) untuk menampilkan hasil jepretan di layar kanan
    let base64Foto = ""; // Siapkan variabel string kosong untuk menampung data gambar mentah (format base64)

    // 1. HIDUPKAN KAMERA VIA HTML5 API 
    navigator.mediaDevices.getUserMedia({ video: true }) // Minta izin ke browser untuk memakai kamera (hanya video, tanpa mic)
        .then(stream => { video.srcObject = stream; }) // Jika diizinkan, masukkan pancaran video (stream) ke dalam elemen <video> di layar agar gambarnya muncul
        .catch(err => Swal.fire('Error', 'Izin kamera ditolak!', 'error')); // Jika ditolak (atau gak ada kamera), munculkan alert error

    // 2. AMBIL SNAPSHOT 
    $('#btnCapture').on('click', function() { // Jika tombol "Ambil Foto" merah di klik
        canvas.width = video.videoWidth; // Set ukuran lebar kanvas lukis biar persis sama dengan ukuran video
        canvas.height = video.videoHeight; // Set ukuran tinggi kanvas lukis persis sama dengan video
        canvas.getContext('2d').drawImage(video, 0, 0); // "Cetak" atau gambar diam (pause) video saat ini ke atas kanvas dari kordinat 0,0
        base64Foto = canvas.toDataURL('image/png'); // Ubah gambar di kanvas tadi menjadi teks string super panjang (format base64 tipe PNG) lalu simpan ke variabel
        snapResult.src = base64Foto; // Tempelkan teks string panjang tadi ke elemen <img src="..."> supaya kelihatan sebagai gambar di layar
    });

    // 3. GUNAKAN FOTO KE FORM 
    $('#btnUsePhoto').on('click', function() { // Jika tombol "Simpan Foto" hijau di klik untuk menutup modal
        if(base64Foto) { // Pastikan variabelnya ada isinya (kasir udah nge-klik tombol ambil foto merah)
            $('#preview_foto').attr('src', base64Foto).show(); // Pasang string gambar ke area Pratinjau Foto di form utama, lalu tampilkan
            $('#placeholder').hide(); // Sembunyikan ikon abu-abu bulat default di area pratinjau form
            $('#foto_data').val(base64Foto); // KUNCI BLOB: Masukkan string gambar super panjang tersebut ke dalam tag <input type="hidden"> agar ikut terkirim ke Laravel pas disubmit!
        }
    });

    // 4. LOGIKA WILAYAH (SAMA DENGAN CODINGANMU TADI)
    function loadWilayah(url, targetId) { // Fungsi pembuat request AJAX untuk mengambil data daerah
        $.get(url, function(res) { // Minta data ke server (API Wilayah) menggunakan tipe request GET
            let dropdown = $(targetId); // Pilih elemen select (dropdown) yang dituju
            dropdown.prop('disabled', false).find('option:not(:first)').remove(); // Nyalakan kembali dropdown-nya, lalu hapus semua opsi lama (kecuali opsi pertama "Pilih...")
            res.data.forEach(item => dropdown.append(`<option value="${item.name}">${item.name}</option>`)); // Looping data wilayah dari server, lalu masukkan jadi opsi-opsi baru ke dropdown
        });
    }

    loadWilayah("{{ route('wilayah.provinsi') }}", "#provinsi"); // Panggil fungsi di atas secara otomatis saat halaman baru dibuka untuk mengisi dropdown provinsi

    $('#provinsi').on('change', function() { // Jika kasir merubah pilihan di dropdown provinsi
        let name = $(this).val(); // Ambil nama provinsi yang baru dipilih
        $('#kota, #kecamatan').prop('disabled', true).val(''); // Reset dan matikan dulu dropdown kota & kecamatan
        if(name) loadWilayah("/wilayah/kota-by-name/" + name, "#kota"); // Jika provinsi valid dipilih, panggil AJAX untuk cari daftar Kota yang ada di provinsi tersebut, lalu masukkan ke dropdown "#kota"
    });

    // 5. SIMPAN DATA (AJAX) 
    $('#formCustomer1').on('submit', function(e) { // Cegat aksi saat form disubmit
        e.preventDefault(); // Hentikan aksi refresh/reload bawaan HTML, kita bakal pakai AJAX!
        if(!$('#foto_data').val()) { // Cek input hidden foto, kalau string base64-nya kosong
            return Swal.fire('Opps', 'Ambil foto dulu dong!', 'warning'); // Tolak proses submit dan munculkan notif
        }

        $.ajax({ // Eksekusi pengiriman form menggunakan AJAX
            url: "{{ route('customer.store') }}", // Tuju route simpan data Laravel
            type: "POST", // Pakai mode POST untuk create data
            data: $(this).serialize(), // Ambil SEMUA data di dalam <form> (termasuk string gambar panjang di foto_data) lalu bungkus jadi format query string
            success: function(res) { // Jika server membalas dengan status sukses
                Swal.fire('Berhasil!', 'Data Customer (BLOB) tersimpan.', 'success').then(() => { // Tampilkan pop up centang
                    window.location.href = "{{ route('customer.index') }}"; // Setelah diklik OK, pindah otomatis ke halaman Daftar Customer
                });
            }
        });
    });
});
</script>
@endsection