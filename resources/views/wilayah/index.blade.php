@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-map-marker-multiple"></i>
            </span> Full Comparison: AJAX vs Axios (4 Level)
        </h3>
    </div>

    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card border-left border-primary shadow">
                <div class="card-body">
                    <h4 class="card-title text-primary"><i class="mdi mdi-jquery"></i> Versi JQuery AJAX</h4>
                    <p class="text-muted small">Alur: Success Callback</p>
                    <hr>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <select id="jq_provinsi" class="form-control text-dark border-primary"><option value="0">Pilih Provinsi</option></select>
                    </div>
                    <div class="form-group">
                        <label>Kota</label>
                        <select id="jq_kota" class="form-control text-dark" disabled><option value="0">Pilih Kota</option></select>
                    </div>
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <select id="jq_kecamatan" class="form-control text-dark" disabled><option value="0">Pilih Kecamatan</option></select>
                    </div>
                    <div class="form-group">
                        <label>Kelurahan</label>
                        <select id="jq_kelurahan" class="form-control text-dark" disabled><option value="0">Pilih Kelurahan</option></select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card border-left border-info shadow">
                <div class="card-body">
                    <h4 class="card-title text-info"><i class="mdi mdi-rocket"></i> Versi Axios</h4>
                    <p class="text-muted small">Alur: Promise (.then)</p>
                    <hr>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <select id="ax_provinsi" class="form-control text-dark border-info"><option value="0">Pilih Provinsi</option></select>
                    </div>
                    <div class="form-group">
                        <label>Kota</label>
                        <select id="ax_kota" class="form-control text-dark" disabled><option value="0">Pilih Kota</option></select>
                    </div>
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <select id="ax_kecamatan" class="form-control text-dark" disabled><option value="0">Pilih Kecamatan</option></select>
                    </div>
                    <div class="form-group">
                        <label>Kelurahan</label>
                        <select id="ax_kelurahan" class="form-control text-dark" disabled><option value="0">Pilih Kelurahan</option></select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
$(document).ready(function() {
    // --- FUNGSI HELPER JQUERY AJAX ---
    function loadJQuery(url, targetId) {
        $.ajax({
            url: url,
            type: "GET",
            success: function(res) {
                let dropdown = $(targetId);
                dropdown.prop('disabled', false);
                res.data.forEach(item => {
                    dropdown.append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    }

    // --- FUNGSI HELPER AXIOS ---
    function loadAxios(url, targetId) {
        axios.get(url).then(function (res) {
            let dropdown = $(targetId);
            dropdown.prop('disabled', false);
            res.data.data.forEach(item => {
                dropdown.append(`<option value="${item.id}">${item.name}</option>`);
            });
        }).catch(err => console.error(err));
    }

    // 1. INITIAL LOAD (PROVINSI)
    loadJQuery("{{ route('wilayah.provinsi') }}", "#jq_provinsi");
    loadAxios("{{ route('wilayah.provinsi') }}", "#ax_provinsi");

    // ==========================================
    // LOGIKA JQUERY (KIRI)
    // ==========================================
    $('#jq_provinsi').on('change', function() {
        let id = $(this).val();
        $('#jq_kota, #jq_kecamatan, #jq_kelurahan').val('0').prop('disabled', true).find('option:not(:first)').remove();
        if(id != '0') loadJQuery("/wilayah/kota/" + id, "#jq_kota");
    });

    $('#jq_kota').on('change', function() {
        let id = $(this).val();
        $('#jq_kecamatan, #jq_kelurahan').val('0').prop('disabled', true).find('option:not(:first)').remove();
        if(id != '0') loadJQuery("/wilayah/kecamatan/" + id, "#jq_kecamatan");
    });

    $('#jq_kecamatan').on('change', function() {
        let id = $(this).val();
        $('#jq_kelurahan').val('0').prop('disabled', true).find('option:not(:first)').remove();
        if(id != '0') loadJQuery("/wilayah/kelurahan/" + id, "#jq_kelurahan");
    });

    // ==========================================
    // LOGIKA AXIOS (KANAN)
    // ==========================================
    $('#ax_provinsi').on('change', function() {
        let id = $(this).val();
        $('#ax_kota, #ax_kecamatan, #ax_kelurahan').val('0').prop('disabled', true).find('option:not(:first)').remove();
        if(id != '0') loadAxios("/wilayah/kota/" + id, "#ax_kota");
    });

    $('#ax_kota').on('change', function() {
        let id = $(this).val();
        $('#ax_kecamatan, #ax_kelurahan').val('0').prop('disabled', true).find('option:not(:first)').remove();
        if(id != '0') loadAxios("/wilayah/kecamatan/" + id, "#ax_kecamatan");
    });

    $('#ax_kecamatan').on('change', function() {
        let id = $(this).val();
        $('#ax_kelurahan').val('0').prop('disabled', true).find('option:not(:first)').remove();
        if(id != '0') loadAxios("/wilayah/kelurahan/" + id, "#ax_kelurahan");
    });
});
</script>
@endsection