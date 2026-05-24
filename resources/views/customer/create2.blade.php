@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-folder-image"></i>
            </span> Tambah Customer 2 (File Path)
        </h3>
    </div>

    <form id="formCustomer2">
        @csrf
        <input type="hidden" name="type" value="path"> {{-- Pembeda utama di Controller --}}
        <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card border-left border-info shadow">
                    <div class="card-body">
                        <h4 class="card-title text-info"><i class="mdi mdi-account-box-outline"></i> Profil Customer</h4>
                        <hr>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control border-info text-dark" placeholder="Masukkan nama" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control border-info text-dark" rows="3" placeholder="Alamat lengkap" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <select name="provinsi" id="provinsi" class="form-control text-dark border-info"><option value="">Pilih Provinsi</option></select>
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
                                    <input type="text" name="kodepos" class="form-control border-info text-dark" placeholder="Contoh: 60111 - Gubeng">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 grid-margin stretch-card">
                <div class="card border-left border-primary shadow text-center">
                    <div class="card-body">
                        <h4 class="card-title text-primary"><i class="mdi mdi-camera"></i> Ambil Snapshot</h4>
                        <hr>
                        <div class="border rounded d-flex align-items-center justify-content-center bg-light mb-3" style="height: 250px; overflow: hidden;">
                            <img id="preview_foto" src="" style="max-width: 100%; display:none;">
                            <div id="placeholder" class="text-muted">
                                <i class="mdi mdi-image-filter-center-focus mdi-48px"></i><br>Belum Ada Foto
                            </div>
                        </div>
                        <input type="hidden" name="foto" id="foto_data">
                        
                        <button type="button" class="btn btn-gradient-primary btn-block" data-bs-toggle="modal" data-bs-target="#modalKamera">
                            <i class="mdi mdi-camera-plus"></i> Buka Kamera
                        </button>
                        
                        <button type="submit" class="btn btn-gradient-info btn-block mt-3" id="btnSimpan">
                            <i class="mdi mdi-cloud-upload"></i> Simpan ke Server
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
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title">Sistem Pengambilan Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <label class="badge badge-info mb-2">Live Video</label>
                        <video id="webcam" autoplay playsinline width="100%" class="rounded border shadow-sm"></video>
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="badge badge-primary mb-2">Hasil Snapshot</label>
                        <canvas id="canvas" style="display:none;"></canvas>
                        <img id="snap_result" src="" width="100%" class="rounded border shadow-sm">
                        <button type="button" id="btnCapture" class="btn btn-gradient-danger btn-sm mt-2">
                            <i class="mdi mdi-camera-iris"></i> Capture!
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnUsePhoto" class="btn btn-gradient-success" data-bs-dismiss="modal">Gunakan Foto Ini</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let video = document.getElementById('webcam');
    let canvas = document.getElementById('canvas');
    let base64Foto = "";

    // 1. AKSES KAMERA (API HTML5) 
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => { video.srcObject = stream; })
        .catch(err => Swal.fire('Error', 'Browser tidak diizinkan akses kamera!', 'error'));

    // 2. LOGIKA SNAPSHOT
    $('#btnCapture').on('click', function() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        base64Foto = canvas.toDataURL('image/png');
        $('#snap_result').attr('src', base64Foto);
    });

    // 3. PASANG KE FORM
    $('#btnUsePhoto').on('click', function() {
        if(base64Foto) {
            $('#preview_foto').attr('src', base64Foto).show();
            $('#placeholder').hide();
            $('#foto_data').val(base64Foto);
        }
    });

    // 4. WILAYAH DROPDOWN (LOGIKA AJAX KONSISTEN)
    function loadWilayah(url, targetId) {
        $.get(url, function(res) {
            let dropdown = $(targetId);
            dropdown.prop('disabled', false).find('option:not(:first)').remove();
            res.data.forEach(item => dropdown.append(`<option value="${item.name}">${item.name}</option>`));
        });
    }

    loadWilayah("{{ route('wilayah.provinsi') }}", "#provinsi");

    $('#provinsi').on('change', function() {
        let name = $(this).val();
        $('#kota, #kecamatan').prop('disabled', true).val('');
        if(name) loadWilayah("/wilayah/kota-by-name/" + name, "#kota");
    });

    // 5. PROSES SIMPAN SEBAGAI FILE PATH 
    $('#formCustomer2').on('submit', function(e) {
        e.preventDefault();
        if(!$('#foto_data').val()) return Swal.fire('Warning', 'Foto belum diambil!', 'warning');

        $.ajax({
            url: "{{ route('customer.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire('Sukses!', 'Customer disimpan dengan File Path.', 'success').then(() => {
                    window.location.href = "{{ route('customer.index') }}";
                });
            }
        });
    });
});
</script>
@endsection