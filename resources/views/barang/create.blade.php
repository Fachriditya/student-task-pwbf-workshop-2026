@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-tag-plus"></i>
            </span> Tambah Barang
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('barang.index') }}">Data Barang</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Baru</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Input Barang</h4>
                    <p class="card-description">Tambahkan barang baru untuk dicetak label harganya</p>
                    
                    <form action="{{ route('barang.store') }}" method="POST" class="forms-sample">
                        @csrf
                        
                        <div class="form-group">
                            <label for="nama">Nama Barang <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('nama') is-invalid @enderror" 
                                id="nama" 
                                name="nama" 
                                placeholder="Masukkan nama barang"
                                value="{{ old('nama') }}"
                                required
                                autofocus>
                            @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="harga">Harga Barang (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-gradient-primary text-white">Rp</span>
                                </div>
                                <input 
                                    type="number" 
                                    class="form-control @error('harga') is-invalid @enderror" 
                                    id="harga" 
                                    name="harga" 
                                    placeholder="Contoh: 150000"
                                    value="{{ old('harga') }}"
                                    min="0"
                                    required>
                                @error('harga')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Masukkan angka saja tanpa titik atau koma.</small>
                        </div>

                        <button type="submit" class="btn btn-gradient-primary me-2">
                            <i class="mdi mdi-content-save"></i> Simpan Barang
                        </button>
                        <a href="{{ route('barang.index') }}" class="btn btn-light">
                            <i class="mdi mdi-cancel"></i> Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="mdi mdi-information-outline"></i> Informasi
                    </h4>
                    <p class="card-description">Panduan Input Barang</p>
                    
                    <div class="alert alert-info" role="alert">
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 ps-3 mt-2">
                            <li><strong>ID Barang</strong> akan dibuat otomatis oleh sistem (Trigger Database) setelah disimpan.</li>
                            <li>Pastikan penulisan nama barang jelas untuk dicetak di stiker kecil.</li>
                            <li>Harga akan diformat ke Rupiah secara otomatis pada saat ditampilkan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection