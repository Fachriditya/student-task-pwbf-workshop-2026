@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    /* Mengubah kursor menjadi pointer saat di-hover pada baris tabel */
    .row-clickable:hover {
        cursor: pointer;
        background-color: #f8f9fa !important;
    }
</style>

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
                    <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="mdi mdi-plus"></i> Tambah Barang
                    </button>
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

                    <form action="{{ route('barang.print') }}" method="POST" target="_blank" id="formPrint">
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
                                    <button type="button" id="btnPrint" onclick="submitAman('btnPrint', 'formPrint', 'Mencetak...')" class="btn btn-gradient-success">
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
                                            <button type="button" class="btn btn-sm btn-inverse-info btn-edit" 
                                                    data-id="{{ $barang->id_barang }}" 
                                                    data-nama="{{ $barang->nama }}" 
                                                    data-harga="{{ $barang->harga }}">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </button>
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

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('barang.store') }}" method="POST" id="formTambah">
                    @csrf
                    <div class="form-group">
                        <label>Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required placeholder="Masukkan nama barang">
                    </div>
                    <div class="form-group">
                        <label>Harga Barang (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="harga" min="0" required placeholder="Contoh: 150000">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-gradient-primary" id="btnTambah" onclick="submitAman('btnTambah', 'formTambah', 'Menyimpan...')">
                    <i class="mdi mdi-content-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="formEdit">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>ID Barang</label>
                        <input type="text" class="form-control" id="edit_id" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" id="edit_nama" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Barang (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="harga" id="edit_harga" min="0" required>
                    </div>
                </form>

                <form method="POST" id="formHapus" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" id="btnHapus" onclick="if(confirm('Yakin ingin menghapus?')) submitAman('btnHapus', 'formHapus', 'Menghapus...')">
                    <i class="mdi mdi-delete"></i> Hapus
                </button>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info" id="btnEdit" onclick="submitAman('btnEdit', 'formEdit', 'Mengubah...')">
                        <i class="mdi mdi-pencil"></i> Ubah
                    </button>
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
            "columnDefs": [{
                "orderable": false,
                "targets": [0, 5]
            }]
        });

        $('#tableBarang').on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let harga = $(this).data('harga');

            $('#edit_id').val(id);
            $('#edit_nama').val(nama);
            $('#edit_harga').val(harga);

            $('#formEdit').attr('action', '/barang/' + id);
            $('#formHapus').attr('action', '/barang/' + id);

            $('#modalEdit').modal('show');
        });
    });

    function submitAman(btnId, formId, loadingText) {
        let form = document.getElementById(formId);
        let btn = document.getElementById(btnId);

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${loadingText}`;
        btn.classList.add('disabled');
        btn.setAttribute('disabled', 'true');

        form.submit();
    }
</script>
@endsection