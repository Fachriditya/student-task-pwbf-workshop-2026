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
$(document).ready(function() {
    let video = document.getElementById('webcam');
    let canvas = document.getElementById('canvas');
    let snapResult = document.getElementById('snap_result');
    let base64Foto = "";

    // 1. HIDUPKAN KAMERA VIA HTML5 API 
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => { video.srcObject = stream; })
        .catch(err => Swal.fire('Error', 'Izin kamera ditolak!', 'error'));

    // 2. AMBIL SNAPSHOT 
    $('#btnCapture').on('click', function() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        base64Foto = canvas.toDataURL('image/png');
        snapResult.src = base64Foto;
    });

    // 3. GUNAKAN FOTO KE FORM 
    $('#btnUsePhoto').on('click', function() {
        if(base64Foto) {
            $('#preview_foto').attr('src', base64Foto).show();
            $('#placeholder').hide();
            $('#foto_data').val(base64Foto);
        }
    });

    // 4. LOGIKA WILAYAH (SAMA DENGAN CODINGANMU TADI)
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

    // 5. SIMPAN DATA (AJAX) 
    $('#formCustomer1').on('submit', function(e) {
        e.preventDefault();
        if(!$('#foto_data').val()) {
            return Swal.fire('Opps', 'Ambil foto dulu dong!', 'warning');
        }

        $.ajax({
            url: "{{ route('customer.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire('Berhasil!', 'Data Customer (BLOB) tersimpan.', 'success').then(() => {
                    window.location.href = "{{ route('customer.index') }}";
                });
            }
        });
    });
});
</script>
@endsection