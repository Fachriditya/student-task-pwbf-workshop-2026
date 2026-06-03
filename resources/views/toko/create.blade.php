@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-map-marker-plus"></i>
            </span> Tambah Lokasi Toko Baru
        </h3>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Data Toko</h4>
                    <p class="card-description"> Masukkan data toko dan ambil titik koordinat saat ini </p>
                    
                    <form action="{{ route('toko.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Barcode / Kode Toko (Maks 8 Karakter)</label>
                            <input type="text" name="barcode" class="form-control" required placeholder="Contoh: TK-001" maxlength="8">
                        </div>
                        <div class="form-group">
                            <label>Nama Toko</label>
                            <input type="text" name="nama_toko" class="form-control" required placeholder="Contoh: Toko Makmur Jaya">
                        </div>

                        <hr>
                        <h5 class="text-info">Titik Koordinat (GPS)</h5>
                        <button type="button" class="btn btn-gradient-info btn-sm mb-3" id="btnAmbilLokasi">
                            <i class="mdi mdi-crosshairs-gps"></i> Ambil Titik Lokasi Saat Ini
                        </button>
                        
                        <div id="statusLokasi" class="text-muted mb-3"></div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Latitude</label>
                                    <input type="text" name="latitude" id="lat" class="form-control" required readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Longitude</label>
                                    <input type="text" name="longitude" id="lng" class="form-control" required readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Akurasi (Meter)</label>
                                    <input type="text" name="accuracy" id="acc" class="form-control" required readonly>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gradient-primary me-2">Simpan Data Toko</button>
                        <a href="{{ route('toko.index') }}" class="btn btn-light">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi GPS dari Lampiran 1 Modul 9
    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) { // Membuat fungsi dengan target akurasi 50 meter dan batas waktu tunggu maksimal 20 detik (20000ms)
        return new Promise((resolve, reject) => { // Membungkus fungsi dalam Promise agar bisa ditunggu (await) hasilnya
            let bestResult = null; // Variabel untuk menyimpan titik GPS dengan akurasi paling bagus selama masa pencarian
            const startTime = Date.now(); // Mencatat waktu mulai pencarian
            
            const watchId = navigator.geolocation.watchPosition( // Memerintahkan browser untuk menyalakan GPS dan 'memantau' (watch) pergerakan sinyal terus-menerus
                (position) => { // Jika browser berhasil menangkap sinyal satelit
                    const acc = position.coords.accuracy; // Ambil nilai radius akurasi (dalam satuan meter). Semakin kecil, semakin akurat posisinya
                    
                    // Simpan hasil terbaik
                    if (!bestResult || acc < bestResult.coords.accuracy) { // Jika ini adalah tangkapan pertama, ATAU akurasinya lebih bagus (lebih kecil) dari tangkapan sebelumnya
                        bestResult = position; // Ganti memori bestResult dengan tangkapan sinyal terbaru ini
                    }
                    
                    // Kalau akurasi sudah mencapai target, berhenti
                    if (acc <= targetAccuracy) { // Jika sinyal GPS sudah tembus di bawah 50 meter (sangat akurat)
                        navigator.geolocation.clearWatch(watchId); // Matikan pemantauan GPS agar tidak menguras baterai HP
                        resolve(bestResult); // Selesaikan Promise dan kembalikan data lokasi terbaik tersebut ke tombol pemanggil
                    }
                },
                (error) => reject(error), // Jika user menolak izin lokasi atau GPS mati, lemparkan error
                { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait } // Konfigurasi paksaan: minta akurasi tertinggi, jangan pakai cache lokasi lama, dan tunggu maksimal 20 detik
            );

            // Timeout fallback
            setTimeout(() => { // Timer mundur berjalan paralel selama 20 detik
                navigator.geolocation.clearWatch(watchId); // Jika sudah 20 detik, paksa matikan pemantauan GPS
                if (bestResult) resolve(bestResult); // Kalau selama 20 detik tadi sempat dapat sinyal (walau akurasinya misal 100 meter), kembalikan saja hasil lumayan itu
                else reject(new Error("Timeout, tidak dapat posisi dengan akurasi yang diminta.")); // Kalau 20 detik sama sekali buta sinyal, gagalkan (reject)
            }, maxWait);
        });
    }

    // Aksi ketika tombol diklik
    document.getElementById('btnAmbilLokasi').addEventListener('click', async function() { // Saat tombol "Ambil Titik Lokasi" diklik (pakai async agar bisa menunggu GPS)
        const statusText = document.getElementById('statusLokasi'); // Ambil elemen HTML tempat kita menaruh pesan
        statusText.innerHTML = '<span class="text-warning"><i class="mdi mdi-loading mdi-spin"></i> Sedang mencari sinyal GPS... (Izinkan akses lokasi di browser)</span>'; // Tampilkan pesan loading muter
        
        try { // Coba blok kode ini
            // Target akurasi 50 meter sesuai modul
            const pos = await getAccuratePosition(50); // Minta sistem mencari GPS, dan TUNGGU (await) di baris ini sampai fungsi di atas selesai membalas
            
            // Masukkan hasil ke dalam form
            document.getElementById('lat').value = pos.coords.latitude; // Isi kolom input Latitude dengan titik koordinat lintang
            document.getElementById('lng').value = pos.coords.longitude; // Isi kolom input Longitude dengan titik koordinat bujur
            document.getElementById('acc').value = pos.coords.accuracy; // Isi kolom akurasi agar admin tahu seberapa meleset titik ini (dalam meter)
            
            statusText.innerHTML = '<span class="text-success"><i class="mdi mdi-check-circle"></i> Lokasi berhasil dikunci!</span>'; // Ubah pesan loading menjadi pesan sukses hijau
        } catch (error) { // Jika dalam proses pencarian tadi kena reject (ditolak/timeout)
            statusText.innerHTML = '<span class="text-danger"><i class="mdi mdi-alert"></i> Gagal: ' + error.message + '</span>'; // Tampilkan error warna merah
        }
    });
</script>
@endsection