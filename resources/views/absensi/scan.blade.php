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
    async function startScan() { // Mendeklarasikan fungsi asinkron bernama startScan agar bisa menggunakan perintah 'await'
        const statusText = document.getElementById('status'); // Mengambil elemen HTML ber-ID 'status' untuk kita ubah teksnya nanti
        const nfcIcon = document.getElementById('nfcIcon'); // Mengambil elemen ikon gambar NFC untuk mengubah warnanya
        
        // Cek dukungan Web NFC API
        if (!('NDEFReader' in window)) { // Mengecek apakah browser HP yang dipakai mendukung fitur pembaca NFC
            Swal.fire('Gagal', 'Browser HP ini tidak mendukung Web NFC. Pastikan pakai Android Chrome versi terbaru!', 'error'); // Jika tidak dukung, munculkan popup error
            statusText.textContent = 'Fitur NFC tidak didukung.'; // Ubah tulisan di layar menjadi tidak didukung
            return; // Hentikan fungsi di sini, jangan lanjutkan ke kode bawah
        }

        try { // Blok percobaan: Coba jalankan kode di bawah ini, kalau error lempar ke blok 'catch'
            // Aktifkan Sensor
            const ndef = new NDEFReader(); // Membentuk objek pembaca NFC (NDEFReader)
            await ndef.scan(); // Meminta izin ke HP dan menyalakan sensor NFC, sistem akan menunggu sampai sensor aktif
            
            // Animasi UI saat NFC Standby
            statusText.innerHTML = '<span class="text-info fw-bold">Sensor Aktif! Tempelkan kartu di belakang HP...</span>'; // Memberi tahu user bahwa sensor siap
            nfcIcon.classList.remove('text-muted'); // Menghapus class warna abu-abu pada gambar ikon
            nfcIcon.classList.add('text-primary'); // Menambahkan class warna biru pada gambar ikon

            // Event Listener: Dijalankan otomatis setiap kali ada kartu nempel
            ndef.addEventListener('reading', async ({ serialNumber }) => { // Menunggu kartu ditempel, lalu menangkap nomor serinya
                // UI Feedback: Getar (jika diizinkan HP)
                if ("vibrate" in navigator) navigator.vibrate(200); // Memerintahkan HP untuk bergetar selama 200 milidetik sebagai tanda terbaca

                statusText.innerHTML = `<span class="text-warning">Memproses Kartu: ${serialNumber}</span>`; // Mengubah teks di layar untuk menampilkan nomor seri kartu
                
                // Lempar serial number ke fungsi AJAX Laravel
                kirimDataKeServer(serialNumber); // Memanggil fungsi kedua di bawah dengan membawa nomor seri kartu tadi
            });

        } catch (error) { // Blok penangkap error: Berjalan jika user menolak izin NFC atau hardware bermasalah
            console.error("NFC Error:", error); // Mencetak error asli ke console browser untuk keperluan debugging
            Swal.fire('Error', 'Gagal menyalakan NFC. Pastikan fitur NFC di Settings HP menyala.', 'error'); // Menampilkan pesan peringatan ke layar user
            statusText.textContent = 'Gagal mengakses sensor NFC.'; // Mengubah status teks menjadi gagal
        }
    }

    // -------------------------------------------------------------
    // FUNGSI 2: KIRIM DATA KE DATABASE LARAVEL
    // -------------------------------------------------------------
    function kirimDataKeServer(serial) { // Mendeklarasikan fungsi untuk mengirim data ke Laravel, membutuhkan 1 variabel (serial)
        $.ajax({ // Memulai pengiriman data menggunakan AJAX (tanpa perlu reload halaman web)
            url: "{{ route('absensi.proses') }}", // Menentukan tujuan pengiriman data, yaitu ke route Laravel bernama 'absensi.proses'
            type: "POST", // Menggunakan metode POST karena kita mengirim data untuk diproses/disimpan
            data: { // Kumpulan data yang dipaketkan untuk dikirim ke controller Laravel
                _token: $('meta[name="csrf-token"]').attr('content'), // Menyertakan tiket keamanan (CSRF) agar Laravel tidak menolak kiriman ini
                serialNumber: serial // Mengirim nomor seri kartu ke server dengan nama kolom 'serialNumber'
            },
            success: function(res) { // Fungsi ini otomatis jalan jika Laravel berhasil merespon/membalas
                if (res.success) { // Mengecek apakah balasan dari Laravel (res.success) bernilai true
                    // Jika Serial Number cocok dengan data Mahasiswa
                    Swal.fire('Hadir!', 'Berhasil absen untuk: ' + res.nama, 'success'); // Menampilkan notifikasi centang hijau nama mahasiswa
                    document.getElementById('hasil').innerHTML = ` // Menambahkan kotak hijau riwayat absen ke layar
                        <div class="alert alert-success shadow-sm">
                            <i class="mdi mdi-check-circle"></i> <strong>${res.nama}</strong> berhasil absen.
                        </div>
                    ` + document.getElementById('hasil').innerHTML; // Menumpuk data yang baru di atas daftar riwayat yang lama
                } else {
                    // Jika Serial Number tidak ada di database
                    Swal.fire('Tidak Dikenali', res.message, 'warning'); // Menampilkan notifikasi silang merah karena kartu tidak dikenali
                    document.getElementById('hasil').innerHTML = ` // Menambahkan kotak merah error ke layar
                        <div class="alert alert-danger shadow-sm">
                            <i class="mdi mdi-close-circle"></i> Kartu (<strong>${serial}</strong>) belum didaftarkan.
                        </div>
                    ` + document.getElementById('hasil').innerHTML; // Menumpuk data yang baru di atas daftar riwayat yang lama
                }
            },
            error: function(xhr) { // Fungsi ini otomatis jalan jika server mati, ngrok terputus, atau ada error kode 500
                Swal.fire('Error', 'Terjadi kesalahan saat menghubungi server.', 'error'); // Menampilkan notifikasi koneksi gagal
            }
        });
    }
</script>
@endsection