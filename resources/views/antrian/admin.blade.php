@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-monitor-dashboard"></i>
            </span> Dashboard Kendali Antrian Kasir
        </h3>
    </div>

    <div class="row">
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card bg-gradient-primary card-img-holder text-white">
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">PESANAN YANG DIPANGGIL SAAT INI <i class="mdi mdi-volume-high mdi-24px float-right"></i></h4>
                    
                    <h1 class="display-1 font-weight-bold my-4" id="nomorSekarang">-</h1>
                    <h3 class="mb-4" id="namaSekarang">Belum ada panggilan</h3>

                    <div class="w-100">
                        <button onclick="panggilKarenaSelesai()" class="btn btn-success btn-lg font-weight-bold w-100 mb-2 shadow-sm">
                            <i class="mdi mdi-check-all"></i> PANGGIL BERIKUTNYA (SELESAI)
                        </button>
                        <button onclick="panggilKarenaTerlewat()" class="btn btn-warning btn-lg font-weight-bold text-dark w-100 shadow-sm">
                            <i class="mdi mdi-debug-step-over"></i> PANGGIL BERIKUTNYA (TERLEWATI)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-primary"><i class="mdi mdi-format-list-bulleted"></i> Antrian Menunggu (Dapur)</h4>
                    <p class="card-description">Daftar ini akan terupdate otomatis saat ada pesanan masuk</p>
                    
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th>No. Antrian</th>
                                    <th>ID / Nama Guest</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="tabelMenunggu">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Menghubungkan ke server...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-danger"><i class="mdi mdi-account-alert"></i> Antrian Terlewat / Terlambat</h4>
                    <p class="card-description">Daftar pengunjung yang tidak hadir saat dipanggil. Kasir bisa memanggil mereka kembali.</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th width="15%">No. Antrian</th>
                                    <th>ID / Nama Guest</th>
                                    <th width="20%">Aksi Kasir</th>
                                </tr>
                            </thead>
                            <tbody id="tabelTerlewat">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada antrian terlewat.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-success"><i class="mdi mdi-checkbox-marked-circle-outline"></i> Riwayat Pesanan Selesai</h4>
                    <p class="card-description">Daftar tamu yang pesanannya sudah berhasil diserahkan.</p>
                    
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th width="15%">No. Antrian</th>
                                    <th>ID / Nama Guest</th>
                                    <th width="20%">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tabelSelesai">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada pesanan selesai.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // -------------------------------------------------------------
    // FUNGSI UTAMA: AJAX POLLING UNTUK DASHBOARD ADMIN (ANTI MACET)
    // -------------------------------------------------------------
    function loadDataAntrian() { // Mendeklarasikan fungsi untuk mengambil data antrian terbaru dari server
        $.ajax({ // Menggunakan jQuery AJAX untuk request data secara background
            url: "{{ route('antrian.api.data') }}", // Meminta data ke URL endpoint API antrian
            type: "GET", // Menggunakan metode GET karena hanya untuk mengambil data (read-only)
            success: function(paketData) { // Fungsi ini dieksekusi jika server berhasil merespons dengan data
                
                // A. Update Tampilan Pesanan Aktif Saat Ini
                if (paketData.sekarang) { // Cek apakah ada data 'sekarang' (pesanan yang sedang dipanggil)
                    $('#nomorSekarang').text(paketData.sekarang.nomor); // Ubah teks nomor antrian di layar utama
                    $('#namaSekarang').text(paketData.sekarang.nama.toUpperCase()); // Ubah teks nama menjadi huruf besar (kapital)
                } else { // Jika tidak ada pesanan yang sedang dipanggil
                    $('#nomorSekarang').text('-'); // Tampilkan tanda strip untuk nomor
                    $('#namaSekarang').text('Belum ada panggilan'); // Tampilkan teks default
                }

                // B. Update Tabel Antrian Menunggu
                let htmlMenunggu = ''; // Siapkan variabel string kosong untuk menampung elemen HTML baru
                if (paketData.menunggu.length === 0) { // Jika panjang array antrian menunggu adalah 0 (kosong)
                    htmlMenunggu = '<tr><td colspan="3" class="text-center text-muted">Dapur kosong, tidak ada antrian.</td></tr>'; // Tampilkan baris informasi kosong
                } else { // Jika ada data antrian menunggu
                    paketData.menunggu.forEach(function(item) { // Lakukan perulangan (loop) untuk setiap data menunggu
                        htmlMenunggu += ` // Tambahkan baris tabel (<tr>) ke variabel string
                            <tr>
                                <td><label class="badge badge-primary fw-bold">#${item.nomor}</label></td> // Menampilkan nomor antrian dengan gaya badge
                                <td>${item.nama}</td> // Menampilkan nama pengantri
                                <td><span class="text-info"><i class="mdi mdi-clock-outline"></i> Menunggu</span></td> // Menampilkan status
                            </tr>
                        `;
                    });
                }
                $('#tabelMenunggu').html(htmlMenunggu); // Ganti isi tbody '#tabelMenunggu' dengan HTML yang baru dibuat

                // C. Update Tabel Antrian Terlewat
                let htmlTerlewat = ''; // Siapkan variabel penampung HTML
                if (paketData.terlewat.length === 0) { // Jika tidak ada data terlewat
                    htmlTerlewat = '<tr><td colspan="3" class="text-center text-muted">Tidak ada data terlewat.</td></tr>'; // Baris kosong
                } else { // Jika ada data terlewat
                    paketData.terlewat.forEach(function(item) { // Loop data terlewat
                        htmlTerlewat += ` // Susun baris HTML
                            <tr>
                                <td><strong>#${item.nomor}</strong></td> // Menampilkan nomor
                                <td>${item.nama}</td> // Menampilkan nama
                                <td>
                                    <button onclick="panggilUlangMundur('${item.nomor}')" class="btn btn-inverse-danger btn-xs py-2 px-3"> // Tombol panggil ulang yang memicu fungsi lain
                                        <i class="mdi mdi-refresh"></i> Panggil Ulang
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#tabelTerlewat').html(htmlTerlewat); // Render HTML ke tabel terlewat

                // D. Update Tabel Riwayat Selesai
                let htmlSelesai = ''; // Variabel penampung HTML
                if (!paketData.selesai || paketData.selesai.length === 0) { // Cek apakah property 'selesai' ada DAN kosong
                    htmlSelesai = '<tr><td colspan="3" class="text-center text-muted">Belum ada pesanan selesai.</td></tr>'; // Baris kosong
                } else { // Jika ada riwayat selesai
                    paketData.selesai.forEach(function(item) { // Loop data selesai
                        htmlSelesai += ` // Susun baris HTML
                            <tr>
                                <td><label class="badge badge-success fw-bold">#${item.nomor}</label></td> // Menampilkan nomor selesai
                                <td>${item.nama}</td> // Menampilkan nama
                                <td><span class="text-success"><i class="mdi mdi-check"></i> Selesai</span></td> // Status sukses
                            </tr>
                        `;
                    });
                }
                $('#tabelSelesai').html(htmlSelesai); // Render HTML ke tabel selesai
            }
        });
    }

    // Refresh data setiap 1 detik agar tidak memblokir server
    setInterval(loadDataAntrian, 1000); // Memerintahkan browser untuk menjalankan fungsi loadDataAntrian berulang kali setiap 1000 milidetik (1 detik)
    
    // Panggil langsung saat halaman pertama dibuka
    loadDataAntrian(); // Eksekusi fungsi pertama kali saat halaman baru saja di-load


    // -------------------------------------------------------------
    // FUNGSI TOMBOL: KASIR TRIGGER via AJAX (VERSI BARU)
    // -------------------------------------------------------------
    function panggilKarenaSelesai() { // Deklarasi fungsi saat kasir menekan tombol "PANGGIL BERIKUTNYA (SELESAI)"
        $.ajax({ // Kirim request ke server
            url: "{{ route('antrian.panggil.selesai') }}", // Tuju route pemrosesan selesai
            type: "POST", // Pakai POST karena akan mengubah data di server
            data: { _token: "{{ csrf_token() }}" }, // Kirim token CSRF keamanan
            success: function(res) { // Saat ada respons dari server
                if(!res.success) { // Jika respons gagal (misal tidak ada lagi antrian)
                    Swal.fire('Info', res.message, 'info'); // Munculkan pop-up info
                }
            }
        });
    }

    function panggilKarenaTerlewat() { // Deklarasi fungsi saat kasir menekan tombol "PANGGIL BERIKUTNYA (TERLEWATI)"
        $.ajax({
            url: "{{ route('antrian.panggil.terlewat') }}", // Tuju route pemrosesan terlewat
            type: "POST", // Metode POST
            data: { _token: "{{ csrf_token() }}" }, // Kirim token keamanan
            success: function(res) {
                if(!res.success) { // Jika gagal
                    Swal.fire('Info', res.message, 'info'); // Munculkan peringatan
                }
            }
        });
    }

    function panggilUlangMundur(nomor) { // Fungsi untuk memanggil ulang antrian yang spesifik, menerima 1 argumen: nomor
        $.ajax({
            url: "{{ route('antrian.panggil.ulang') }}", // Tuju route pemanggilan ulang
            type: "POST",
            data: { 
                _token: "{{ csrf_token() }}", // Token keamanan
                nomor: nomor  // Mengirim nomor antrian yang spesifik agar server tahu mana yang dipanggil ulang
            },
            success: function(res) { // Saat direspons server
                if(res.success) { // Jika berhasil
                    Swal.fire({ // Tampilkan notifikasi "Toast" (kecil di sudut kanan atas)
                        icon: 'success',
                        title: 'Memanggil Ulang...',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500 // Hilang sendiri setelah 1.5 detik
                    });
                } else { // Jika server menolak
                    Swal.fire('Gagal', res.message, 'error'); // Tampilkan pop-up error
                }
            }
        });
    }
</script>
@endsection