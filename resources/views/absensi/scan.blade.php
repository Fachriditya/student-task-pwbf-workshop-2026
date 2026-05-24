@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card text-center shadow-sm">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 65vh;">
                    <h4 class="card-title text-primary mb-4">
                        <i class="mdi mdi-cellphone-nfc"></i> Scanner Absensi NFC
                    </h4>
                    
                    <div class="mb-4">
                        <i class="mdi mdi-contactless-payment text-muted" style="font-size: 120px;" id="nfcIcon"></i>
                    </div>

                    <button onclick="startScan()" id="btnScan" class="btn btn-gradient-primary btn-lg rounded-pill mb-3 px-5 shadow">
                        <i class="mdi mdi-nfc"></i> Aktifkan Sensor NFC
                    </button>

                    <p id="status" class="text-muted mt-2">Klik tombol di atas untuk mulai memindai kartu.</p>
                    
                    <div id="hasil" class="mt-4 w-100 text-start"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // -------------------------------------------------------------
    // FUNGSI 1: MENGHIDUPKAN HARDWARE NFC DI HP
    // -------------------------------------------------------------
    async function startScan() {
        const statusText = document.getElementById('status');
        const nfcIcon = document.getElementById('nfcIcon');
        
        // Cek dukungan Web NFC API
        if (!('NDEFReader' in window)) {
            Swal.fire('Gagal', 'Browser HP ini tidak mendukung Web NFC. Pastikan pakai Android Chrome versi terbaru!', 'error');
            statusText.textContent = 'Fitur NFC tidak didukung.';
            return;
        }

        try {
            // Aktifkan Sensor
            const ndef = new NDEFReader();
            await ndef.scan();
            
            // Animasi UI saat NFC Standby
            statusText.innerHTML = '<span class="text-info fw-bold">Sensor Aktif! Tempelkan kartu di belakang HP...</span>';
            nfcIcon.classList.remove('text-muted');
            nfcIcon.classList.add('text-primary');

            // Event Listener: Dijalankan otomatis setiap kali ada kartu nempel
            ndef.addEventListener('reading', async ({ serialNumber }) => {
                // UI Feedback: Getar (jika diizinkan HP)
                if ("vibrate" in navigator) navigator.vibrate(200);

                statusText.innerHTML = `<span class="text-warning">Memproses Kartu: ${serialNumber}</span>`;
                
                // Lempar serial number ke fungsi AJAX Laravel
                kirimDataKeServer(serialNumber);
            });

        } catch (error) {
            console.error("NFC Error:", error);
            Swal.fire('Error', 'Gagal menyalakan NFC. Pastikan fitur NFC di Settings HP menyala.', 'error');
            statusText.textContent = 'Gagal mengakses sensor NFC.';
        }
    }

    // -------------------------------------------------------------
    // FUNGSI 2: KIRIM DATA KE DATABASE LARAVEL
    // -------------------------------------------------------------
    function kirimDataKeServer(serial) {
        $.ajax({
            url: "{{ route('absensi.proses') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                serialNumber: serial
            },
            success: function(res) {
                if (res.success) {
                    // Jika Serial Number cocok dengan data Mahasiswa
                    Swal.fire('Hadir!', 'Berhasil absen untuk: ' + res.nama, 'success');
                    document.getElementById('hasil').innerHTML = `
                        <div class="alert alert-success shadow-sm">
                            <i class="mdi mdi-check-circle"></i> <strong>${res.nama}</strong> berhasil absen.
                        </div>
                    ` + document.getElementById('hasil').innerHTML; // Tumpuk ke bawah
                } else {
                    // Jika Serial Number tidak ada di database
                    Swal.fire('Tidak Dikenali', res.message, 'warning');
                    document.getElementById('hasil').innerHTML = `
                        <div class="alert alert-danger shadow-sm">
                            <i class="mdi mdi-close-circle"></i> Kartu (<strong>${serial}</strong>) belum didaftarkan.
                        </div>
                    ` + document.getElementById('hasil').innerHTML;
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Terjadi kesalahan saat menghubungi server.', 'error');
            }
        });
    }
</script>
@endsection