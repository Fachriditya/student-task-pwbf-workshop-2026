@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-store"></i>
            </span> Data Lokasi Toko
        </h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title m-0">Daftar Toko Klien</h4>
                        <a href="{{ route('toko.create') }}" class="btn btn-sm btn-gradient-info">+ Tambah Toko</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th>Barcode</th>
                                    <th>Nama Toko</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Akurasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tokos as $t)
                                <tr>
                                    <td><strong>{{ $t->barcode }}</strong></td>
                                    <td>{{ $t->nama_toko }}</td>
                                    <td>{{ $t->latitude }}</td>
                                    <td>{{ $t->longitude }}</td>
                                    <td><label class="badge badge-success">{{ $t->accuracy }} m</label></td>
                                    <td>
                                        <a href="{{ route('toko.barcode', $t->barcode) }}" target="_blank" class="btn btn-inverse-dark btn-sm">
                                            <i class="mdi mdi-printer"></i> Cetak Barcode
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data toko.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection