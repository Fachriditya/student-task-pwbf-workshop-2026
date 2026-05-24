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
    function loadDataAntrian() {
        $.ajax({
            url: "{{ route('antrian.api.data') }}",
            type: "GET",
            success: function(paketData) {
                // A. Update Tampilan Pesanan Aktif Saat Ini
                if (paketData.sekarang) {
                    $('#nomorSekarang').text(paketData.sekarang.nomor);
                    $('#namaSekarang').text(paketData.sekarang.nama.toUpperCase());
                } else {
                    $('#nomorSekarang').text('-');
                    $('#namaSekarang').text('Belum ada panggilan');
                }

                // B. Update Tabel Antrian Menunggu
                let htmlMenunggu = '';
                if (paketData.menunggu.length === 0) {
                    htmlMenunggu = '<tr><td colspan="3" class="text-center text-muted">Dapur kosong, tidak ada antrian.</td></tr>';
                } else {
                    paketData.menunggu.forEach(function(item) {
                        htmlMenunggu += `
                            <tr>
                                <td><label class="badge badge-primary fw-bold">#${item.nomor}</label></td>
                                <td>${item.nama}</td>
                                <td><span class="text-info"><i class="mdi mdi-clock-outline"></i> Menunggu</span></td>
                            </tr>
                        `;
                    });
                }
                $('#tabelMenunggu').html(htmlMenunggu);

                // C. Update Tabel Antrian Terlewat
                let htmlTerlewat = '';
                if (paketData.terlewat.length === 0) {
                    htmlTerlewat = '<tr><td colspan="3" class="text-center text-muted">Tidak ada data terlewat.</td></tr>';
                } else {
                    paketData.terlewat.forEach(function(item) {
                        htmlTerlewat += `
                            <tr>
                                <td><strong>#${item.nomor}</strong></td>
                                <td>${item.nama}</td>
                                <td>
                                    <button onclick="panggilUlangMundur('${item.nomor}')" class="btn btn-inverse-danger btn-xs py-2 px-3">
                                        <i class="mdi mdi-refresh"></i> Panggil Ulang
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#tabelTerlewat').html(htmlTerlewat);

                // D. Update Tabel Riwayat Selesai
                let htmlSelesai = '';
                if (!paketData.selesai || paketData.selesai.length === 0) {
                    htmlSelesai = '<tr><td colspan="3" class="text-center text-muted">Belum ada pesanan selesai.</td></tr>';
                } else {
                    paketData.selesai.forEach(function(item) {
                        htmlSelesai += `
                            <tr>
                                <td><label class="badge badge-success fw-bold">#${item.nomor}</label></td>
                                <td>${item.nama}</td>
                                <td><span class="text-success"><i class="mdi mdi-check"></i> Selesai</span></td>
                            </tr>
                        `;
                    });
                }
                $('#tabelSelesai').html(htmlSelesai);
            }
        });
    }

    // Refresh data setiap 1 detik agar tidak memblokir server
    setInterval(loadDataAntrian, 1000);
    // Panggil langsung saat halaman pertama dibuka
    loadDataAntrian();


    // -------------------------------------------------------------
    // FUNGSI TOMBOL: KASIR TRIGGER via AJAX (VERSI BARU)
    // -------------------------------------------------------------
    function panggilKarenaSelesai() {
        $.ajax({
            url: "{{ route('antrian.panggil.selesai') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                if(!res.success) {
                    Swal.fire('Info', res.message, 'info');
                }
            }
        });
    }

    function panggilKarenaTerlewat() {
        $.ajax({
            url: "{{ route('antrian.panggil.terlewat') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                if(!res.success) {
                    Swal.fire('Info', res.message, 'info');
                }
            }
        });
    }

    function panggilUlangMundur(nomor) {
        $.ajax({
            url: "{{ route('antrian.panggil.ulang') }}",
            type: "POST",
            data: { 
                _token: "{{ csrf_token() }}", 
                nomor: nomor 
            },
            success: function(res) {
                if(res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Memanggil Ulang...',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    }
</script>
@endsection