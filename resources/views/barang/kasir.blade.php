@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-cart"></i>
            </span> Kasir (POS)
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span>Transaksi Baru</span>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="bg-light p-4 rounded mb-4 border">
                        <h5 class="text-primary mb-3"><i class="mdi mdi-magnify"></i> Pencarian Barang</h5>
                        <div class="row align-items-end">
                            <div class="col-md-3 form-group mb-0">
                                <label>Kode Barang (Enter)</label>
                                <input type="text" id="kode_barang" class="form-control" placeholder="Contoh: B001" autofocus>
                            </div>
                            <div class="col-md-4 form-group mb-0">
                                <label>Nama Barang</label>
                                <input type="text" id="nama_barang" class="form-control" readonly placeholder="-">
                            </div>
                            <div class="col-md-3 form-group mb-0">
                                <label>Harga (Rp)</label>
                                <input type="number" id="harga_barang" class="form-control" readonly placeholder="0">
                            </div>
                            <div class="col-md-2 form-group mb-0 text-end">
                                <button type="button" id="btnTambah" class="btn btn-gradient-primary w-100" disabled>
                                    <i class="mdi mdi-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelBelanja">
                            <thead>
                                <tr class="bg-light">
                                    <th width="120">Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th width="100">Jumlah</th>
                                    <th>Subtotal</th>
                                    <th width="80" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="4" class="text-end text-primary"><h4>Total Pembayaran :</h4></th>
                                    <th colspan="2"><h4 class="text-primary">Rp <span id="totalBayar">0</span></h4></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" id="btnBayar" class="btn btn-gradient-success btn-lg">
                            <i class="mdi mdi-cash-multiple"></i> Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Fungsi Hitung Total
    function hitungTotal() {
        let total = 0;
        $('.subtotal-item').each(function() {
            total += parseInt($(this).data('raw'));
        });
        $('#totalBayar').text(new Intl.NumberFormat('id-ID').format(total));
    }

    // 2. Pencarian Barang (ENTER)
    $('#kode_barang').on('keypress', function(e) {
        if (e.which == 13) {
            let id = $(this).val().trim(); // Tambah trim biar aman
            if(id == "") return;

            $.ajax({
                url: "{{ route('barang.cari') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id_barang: id
                },
                success: function(response) {
                    $('#nama_barang').val(response.data.nama);
                    $('#harga_barang').val(response.data.harga);
                    $('#btnTambah').prop('disabled', false);
                },
                error: function() {
                    Swal.fire('Error!', 'Kode barang tidak terdaftar', 'error');
                    $('#nama_barang').val('');
                    $('#harga_barang').val('');
                    $('#btnTambah').prop('disabled', true);
                }
            });
        }
    });

    // 3. Tombol Tambah ke Keranjang
    $('#btnTambah').on('click', function() {
        let kode = $('#kode_barang').val().trim();
        let nama = $('#nama_barang').val();
        let harga = parseInt($('#harga_barang').val());
        let qty = 1;
        
        let ada = false;
        $('#tabelBelanja tbody tr').each(function() {
            // Pastikan perbandingan string-nya bersih
            if ($(this).find('.td-kode-text').text().trim() == kode) {
                let inputQty = $(this).find('.input-qty');
                let newQty = parseInt(inputQty.val()) + 1;
                inputQty.val(newQty);
                
                let newSubtotal = newQty * harga;
                $(this).find('.subtotal-item').text(new Intl.NumberFormat('id-ID').format(newSubtotal));
                $(this).find('.subtotal-item').data('raw', newSubtotal);
                ada = true;
            }
        });

        if (!ada) {
            let row = `
                <tr>
                    <td class="td-kode"><label class="badge badge-gradient-primary td-kode-text">${kode}</label></td>
                    <td><strong>${nama}</strong></td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(harga)}</td>
                    <td><input type="number" class="form-control input-qty" value="${qty}" min="1" style="width: 80px;"></td>
                    <td>Rp <span class="subtotal-item" data-raw="${harga * qty}">${new Intl.NumberFormat('id-ID').format(harga * qty)}</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-inverse-danger btn-hapus"><i class="mdi mdi-delete"></i></button>
                    </td>
                </tr>`;
            $('#tabelBelanja tbody').append(row);
        }

        hitungTotal();
        $('#kode_barang').val('').focus();
        $('#nama_barang').val('');
        $('#harga_barang').val('');
        $('#btnTambah').prop('disabled', true);
    });

    // 4. Hapus Item
    $('#tabelBelanja').on('click', '.btn-hapus', function() {
        $(this).closest('tr').remove();
        hitungTotal();
    });

    // 5. Update Quantity (INI YANG TADI KURANG KURUNG TUTUP)
    $('#tabelBelanja').on('change keyup', '.input-qty', function() {
        let qty = parseInt($(this).val());
        if(isNaN(qty) || qty < 1) qty = 1;

        let hargaText = $(this).closest('tr').find('td:nth-child(3)').text().replace(/[^\d]/g, '');
        let harga = parseInt(hargaText);
        let subtotal = qty * harga;
        
        $(this).closest('tr').find('.subtotal-item').text(new Intl.NumberFormat('id-ID').format(subtotal));
        $(this).closest('tr').find('.subtotal-item').data('raw', subtotal);
        hitungTotal();
    }); // <--- Tadi kurang ini

    // 6. Proses Bayar
    $('#btnBayar').on('click', function() {
        let items = [];
        
        $('#tabelBelanja tbody tr').each(function() {
            items.push({
                kode: $(this).find('.td-kode-text').text().trim(),
                qty: $(this).find('.input-qty').val(),
                subtotal: $(this).find('.subtotal-item').data('raw')
            });
        });

        if (items.length === 0) {
            Swal.fire('Peringatan', 'Belum ada barang di keranjang!', 'warning');
            return;
        }

        let btn = $(this);
        let originalContent = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm"></span> Memproses...').prop('disabled', true);

        $.ajax({
            url: "{{ route('barang.bayar') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                total: $('#totalBayar').text().replace(/[^\d]/g, ''),
                items: items
            },
            success: function(response) {
                Swal.fire('Berhasil!', response.message, 'success').then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON.message || 'Gagal menyimpan transaksi', 'error');
                btn.html(originalContent).prop('disabled', false);
            }
        });
    });
</script>
@endsection