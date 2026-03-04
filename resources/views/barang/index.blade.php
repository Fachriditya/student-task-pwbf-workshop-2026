@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-tag-multiple"></i>
            </span> Data Barang UMKM
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <a href="{{ route('barang.create') }}" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Barang
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('barang.print') }}" method="POST" target="_blank">
                        @csrf
                        
                        <div class="bg-light p-3 rounded mb-4 border">
                            <h5 class="text-primary mb-3"><i class="mdi mdi-printer"></i> Pengaturan Cetak Label (TnJ 108)</h5>
                            <div class="row align-items-end">
                                <div class="col-md-3 form-group mb-0">
                                    <label>Koordinat X (Kolom 1-5) <span class="text-danger">*</span></label>
                                    <input type="number" name="x" class="form-control" min="1" max="5" required placeholder="Contoh: 3">
                                </div>
                                <div class="col-md-3 form-group mb-0">
                                    <label>Koordinat Y (Baris 1-8) <span class="text-danger">*</span></label>
                                    <input type="number" name="y" class="form-control" min="1" max="8" required placeholder="Contoh: 2">
                                </div>
                                <div class="col-md-6 form-group mb-0 text-end">
                                    <button type="submit" class="btn btn-gradient-success">
                                        <i class="mdi mdi-file-pdf"></i> Cetak Label Terpilih
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" id="tableBarang">
                                <thead>
                                    <tr>
                                        <th width="50" class="text-center">
                                            <input type="checkbox" id="checkAll">
                                        </th>
                                        <th width="120">ID Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th width="150">Waktu Input</th>
                                        <th width="100">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangs as $barang)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="ids[]" class="checkItem" value="{{ $barang->id_barang }}">
                                        </td>
                                        <td>
                                            <label class="badge badge-gradient-primary">{{ $barang->id_barang }}</label>
                                        </td>
                                        <td>
                                            <strong>{{ $barang->nama }}</strong>
                                        </td>
                                        <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($barang->timestamp)->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('barang.edit', $barang->id_barang) }}" class="btn btn-sm btn-inverse-info">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('barang.destroy', $barang->id_barang) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-inverse-danger">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableBarang').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 5] }
            ]
        });

        $('#checkAll').click(function() {
            $('.checkItem').prop('checked', this.checked);
        });
    });
</script>
@endsection