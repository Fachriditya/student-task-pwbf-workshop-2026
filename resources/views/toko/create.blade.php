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
    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
        return new Promise((resolve, reject) => {
            let bestResult = null;
            const startTime = Date.now();
            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const acc = position.coords.accuracy;
                    // Simpan hasil terbaik
                    if (!bestResult || acc < bestResult.coords.accuracy) {
                        bestResult = position;
                    }
                    // Kalau akurasi sudah mencapai target, berhenti
                    if (acc <= targetAccuracy) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                    }
                },
                (error) => reject(error),
                { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
            );

            // Timeout fallback
            setTimeout(() => {
                navigator.geolocation.clearWatch(watchId);
                if (bestResult) resolve(bestResult);
                else reject(new Error("Timeout, tidak dapat posisi dengan akurasi yang diminta."));
            }, maxWait);
        });
    }

    // Aksi ketika tombol diklik
    document.getElementById('btnAmbilLokasi').addEventListener('click', async function() {
        const statusText = document.getElementById('statusLokasi');
        statusText.innerHTML = '<span class="text-warning"><i class="mdi mdi-loading mdi-spin"></i> Sedang mencari sinyal GPS... (Izinkan akses lokasi di browser)</span>';
        
        try {
            // Target akurasi 50 meter sesuai modul
            const pos = await getAccuratePosition(50); 
            
            // Masukkan hasil ke dalam form
            document.getElementById('lat').value = pos.coords.latitude;
            document.getElementById('lng').value = pos.coords.longitude;
            document.getElementById('acc').value = pos.coords.accuracy;
            
            statusText.innerHTML = '<span class="text-success"><i class="mdi mdi-check-circle"></i> Lokasi berhasil dikunci!</span>';
        } catch (error) {
            statusText.innerHTML = '<span class="text-danger"><i class="mdi mdi-alert"></i> Gagal: ' + error.message + '</span>';
        }
    });
</script>
@endsection