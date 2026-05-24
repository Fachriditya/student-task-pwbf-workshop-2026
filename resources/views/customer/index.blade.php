@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-account-group"></i>
            </span> Customers
        </h3>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Customer List</h4>
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th width="100">ID</th>
                                    <th>Customer Info</th>
                                    <th>Address Details</th>
                                    <th width="150">Photo Preview</th>
                                    <th width="150">Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $index => $c)
                                <tr>
                                    <td>{{ $customers->firstItem() + $index }}</td>
                                    <td>
                                        <label class="badge badge-gradient-primary">#{{ $c->idcustomer }}</label>
                                    </td>
                                    <td>
                                        <strong>{{ $c->nama }}</strong>
                                    </td>
                                    <td class="text-muted" style="font-size: 0.85rem;">
                                        {{ $c->alamat }} <br>
                                        <small>{{ $c->kecamatan }}, {{ $c->kota }}, {{ $c->provinsi }} ({{ $c->kodepos }})</small>
                                    </td>
                                    <td>
                                        {{-- Logika Menampilkan Foto berdasarkan Jenis Penyimpanan --}}
                                        @if($c->foto_blob)
                                            {{-- Menampilkan data biner BLOB  --}}
                                            <img src="data:image/png;base64,{{ base64_encode($c->foto_blob) }}" 
                                                 class="rounded" style="width: 60px; height: 60px; object-fit: cover;" 
                                                 title="Storage: BLOB">
                                        @elseif($c->foto_path)
                                            {{-- Menampilkan dari file path  --}}
                                            <img src="{{ asset('storage/' . $c->foto_path) }}" 
                                                 class="rounded" style="width: 60px; height: 60px; object-fit: cover;" 
                                                 title="Storage: File Path">
                                        @else
                                            <label class="badge badge-light text-muted">No Photo</label>
                                        @endif
                                    </td>
                                    <td>{{ $c->created_at->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <p class="mt-3 text-muted">No customers found. 
                                            <a href="{{ route('customer.create1') }}">Capture your first customer</a>
                                        </p>
                                    </td>
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