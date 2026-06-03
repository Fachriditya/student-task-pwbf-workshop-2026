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
$(document).ready(function() { // Pastikan DOM HTML beres diload dulu sebelum JS dieksekusi
    
    // --- FUNGSI HELPER JQUERY AJAX ---
    function loadJQuery(url, targetId) { // Bikin fungsi serbaguna jQuery dengan 2 syarat: URL API dan ID Dropdown tujuan
        $.ajax({ // Buka koneksi AJAX bawaan jQuery
            url: url, // Tembak ke URL yang diminta
            type: "GET", // Minta data (GET)
            success: function(res) { // Callback klasik: Jika sukses, jalankan block ini. Response server ada di variabel 'res'
                let dropdown = $(targetId); // Pilih dropdown berdasarkan ID yang dilempar
                dropdown.prop('disabled', false); // Nyalakan dropdownnya agar bisa diklik
                res.data.forEach(item => { // Looping langsung ke dalam 'res.data' (asumsi format server mereturn {data: [...]})
                    dropdown.append(`<option value="${item.id}">${item.name}</option>`); // Suntikkan elemen <option> baru ke dalam dropdown
                });
            }
        });
    }

    // --- FUNGSI HELPER AXIOS ---
    function loadAxios(url, targetId) { // Bikin fungsi serbaguna versi Axios dengan 2 syarat yang sama
        axios.get(url).then(function (res) { // Tembak URL dengan Axios (langsung GET). Menggunakan metode Promise (.then) jika sukses
            let dropdown = $(targetId); // Pilih dropdown tujuan
            dropdown.prop('disabled', false); // Nyalakan dropdownnya
            res.data.data.forEach(item => { // PERBEDAAN UTAMA: Axios membungkus response mentah di property '.data'. Jadi untuk akses JSON Laravel, wajib 'res.data.data'
                dropdown.append(`<option value="${item.id}">${item.name}</option>`); // Suntikkan ke HTML
            });
        }).catch(err => console.error(err)); // Rantai (chaining) penangkap error langsung di ujung fungsi
    }

    // 1. INITIAL LOAD (PROVINSI)
    loadJQuery("{{ route('wilayah.provinsi') }}", "#jq_provinsi"); // Langsung panggil list Provinsi pakai fungsi jQuery saat halaman web baru dibuka
    loadAxios("{{ route('wilayah.provinsi') }}", "#ax_provinsi"); // Lakukan hal yang sama untuk kolom sebelah kanan pakai fungsi Axios

    // ==========================================
    // LOGIKA JQUERY (KIRI)
    // ==========================================
    $('#jq_provinsi').on('change', function() { // Deteksi kalau dropdown Provinsi (Kiri) diganti pilihannya
        let id = $(this).val(); // Simpan ID provinsi yang dipilih
        $('#jq_kota, #jq_kecamatan, #jq_kelurahan').val('0').prop('disabled', true).find('option:not(:first)').remove(); // RESET TOTAL: Matikan 3 dropdown di bawahnya, kembalikan ke pilihan index 0, dan buang sisa option sebelumnya (kecuali option paling atas)
        if(id != '0') loadJQuery("/wilayah/kota/" + id, "#jq_kota"); // Kalau yang dipilih bukan index 0, panggil AJAX untuk isi dropdown Kota
    });

    $('#jq_kota').on('change', function() { // Deteksi dropdown Kota (Kiri)
        let id = $(this).val(); // Simpan ID kota
        $('#jq_kecamatan, #jq_kelurahan').val('0').prop('disabled', true).find('option: