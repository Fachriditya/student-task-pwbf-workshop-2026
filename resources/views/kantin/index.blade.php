<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantin Online - Pesan Mudah</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body { background: #f2edf3; font-family: 'Poppins', sans-serif; }
        .navbar-custom { background: linear-gradient(to right, #da8cff, #9a55ff); padding: 15px; color: white; }
        .card { border-radius: 15px; border: none; transition: transform 0.2s; }
        .card:hover { transform: scale(1.02); } /* Efek pop-up saat disentuh */
        .btn-lg { border-radius: 10px; font-weight: bold; }
        /* Scrollbar keranjang agar lebih rapi */
        .table-responsive { max-height: 350px; overflow-y: auto; }
    </style>
</head>
<body>

<nav class="navbar-custom shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="mdi mdi-food"></i> Kantin Online</h4>
        <div class="text-end">
            <small class="d-block">ID Pelanggan:</small>
            <strong class="badge badge-light text-dark" id="guest_id">{{ $nextGuest }}</strong>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="mdi mdi-store"></i> Pilih Kantin</h5>
                    <div class="form-group mb-0">
                        <select id="vendor_select" class="form-control form-control-lg border-primary">
                            <option value="">-- Sentuh untuk Pilih Kantin --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <h5 class="text-primary mb-3"><i class="mdi mdi-silverware-fork-knife"></i> Menu Tersedia</h5>
            <div class="row" id="menu_gallery">
                <div class="col-12 text-center text-muted mt-4">
                    <i class="mdi mdi-food-variant mdi-48px"></i>
                    <p>Silakan pilih kantin terlebih dahulu.</p>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm position-sticky" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="mdi mdi-basket"></i> Keranjang Belanja</h5>
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelKeranjang">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th width="80px">Qty</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Total :</h4>
                        <h3 class="text-primary mb-0 font-weight-bold">Rp <span id="totalBayar">0</span></h3>
                    </div>
                    <button id="btnBayar" class="btn btn-gradient-success btn-lg w-100" disabled>
                        <i class="mdi mdi-cash-multiple"></i> Bayar Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        
        // 1. KETIKA KANTIN DIPILIH (Menampilkan Card Gallery)
        $('#vendor_select').on('change', function() {
            let id = $(this).val();
            let gallery = $('#menu_gallery');
            
            gallery.empty(); // Kosongkan etalase sebelumnya
            
            if(id) {
                // Munculkan efek loading
                gallery.html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div></div>');
                
                $.ajax({
                    url: "{{ url('api/menu') }}/" + id,
                    type: "GET",
                    success: function(res) {
                        gallery.empty(); // Bersihkan loading
                        
                        if(res.length === 0) {
                            gallery.append('<div class="col-12 text-center text-muted">Menu belum tersedia.</div>');
                            return;
                        }

                        // Looping pembuatan Card untuk setiap menu
                        res.forEach(m => {
                            // LOGIKA GAMBAR: Jika path_gambar kosong di database, pakai gambar dummy dari nama_menu
                            let imgUrl = m.path_gambar 
                                        ? `{{ asset('storage') }}/${m.path_gambar}` 
                                        : `https://ui-avatars.com/api/?name=${encodeURIComponent(m.nama_menu)}&background=random&color=fff&size=200`;
                            
                            let card = `
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 shadow-sm border-0">
                                        <img src="${imgUrl}" class="card-img-top" style="height: 140px; object-fit: cover; border-radius: 15px 15px 0 0;">
                                        <div class="card-body p-3 text-center">
                                            <h6 class="font-weight-bold mb-1">${m.nama_menu}</h6>
                                            <p class="text-primary mb-2">Rp ${new Intl.NumberFormat('id-ID').format(m.harga)}</p>
                                            
                                            <button class="btn btn-sm btn-gradient-primary w-100 btn-tambah-card" 
                                                data-id="${m.idmenu}" 
                                                data-nama="${m.nama_menu}" 
                                                data-harga="${m.harga}">
                                                <i class="mdi mdi-cart-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>`;
                            gallery.append(card);
                        });
                    }
                });
            } else {
                gallery.html('<div class="col-12 text-center text-muted"><i class="mdi mdi-food-variant mdi-48px"></i><p>Silakan pilih kantin terlebih dahulu.</p></div>');
            }
        });

        // 2. KETIKA TOMBOL "TAMBAH" DI CARD DIKLIK
        $(document).on('click', '.btn-tambah-card', function() {
            let idMenu = $(this).data('id');
            let namaMenu = $(this).data('nama');
            let harga = parseInt($(this).data('harga'));
            
            let existingRow = $(`#tabelKeranjang tbody tr[data-id="${idMenu}"]`);
            
            if (existingRow.length > 0) {
                let inputQty = existingRow.find('.qty-input');
                let newQty = parseInt(inputQty.val()) + 1;
                inputQty.val(newQty);
                let newSubtotal = newQty * harga;
                existingRow.find('.subtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(newSubtotal));
            } else {
                let row = `<tr data-id="${idMenu}" data-harga="${harga}">
                            <td>${namaMenu}</td>
                            <td><input type="number" class="form-control qty-input p-1 text-center" value="1" min="1"></td>
                            <td class="subtotal">Rp ${new Intl.NumberFormat('id-ID').format(harga)}</td>
                            <td><button class="btn btn-link text-danger p-0 btn-hapus"><i class="mdi mdi-close-circle mdi-24px"></i></button></td>
                          </tr>`;
                $('#tabelKeranjang tbody').append(row);
            }
            hitungTotalSemua();
            $('#btnBayar').prop('disabled', false);
        });

        // 3. UPDATE QTY MANUAL & HAPUS (Tetap Sama)
        $(document).on('change', '.qty-input', function() {
            let row = $(this).closest('tr');
            let harga = parseInt(row.data('harga'));
            let qty = parseInt($(this).val());
            if(qty < 1) { $(this).val(1); qty = 1; }
            row.find('.subtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(qty * harga));
            hitungTotalSemua();
        });

        $(document).on('click', '.btn-hapus', function() { 
            $(this).closest('tr').remove(); 
            hitungTotalSemua(); 
            if ($('#tabelKeranjang tbody tr').length === 0) $('#btnBayar').prop('disabled', true);
        });

        function hitungTotalSemua() {
            let total = 0;
            $('.subtotal').each(function() { total += parseInt($(this).text().replace(/[^\d]/g, '')); });
            $('#totalBayar').text(new Intl.NumberFormat('id-ID').format(total));
        }

        // 4. PROSES BAYAR MIDTRANS (Tetap Sama)
        $('#btnBayar').on('click', function() {
            let items = [];
            $('#tabelKeranjang tbody tr').each(function() {
                items.push({
                    id: $(this).data('id'),
                    qty: $(this).find('.qty-input').val(),
                    harga: $(this).data('harga'),
                    subtotal: $(this).find('.subtotal').text().replace(/[^\d]/g, '')
                });
            });

            let btn = $(this);
            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Processing...');

            $.ajax({
                url: "{{ url('/api/proses-bayar') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    nama_guest: $('#guest_id').text().trim(),
                    total: $('#totalBayar').text().replace(/[^\d]/g, ''),
                    items: items
                },
                success: function(res) {
                    if(res.snap_token) {
                        setTimeout(function() {
                            window.snap.pay(res.snap_token, {
                                onSuccess: function() { 
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Pembayaran lunas, mengalihkan ke nota...',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500 // Tunggu 1,5 detik biar user baca pesannya
                                    }).then(() => {
                                        // Arahkan ke halaman nota membawa ID Pesanan dari server
                                        window.location.href = "{{ url('/kantin/nota') }}/" + res.idpesanan;
                                    }); 
                                },
                                onPending: function() { Swal.fire('Pending', 'Selesaikan pembayaran', 'info'); btn.prop('disabled', false).html('Bayar Sekarang'); },
                                onError: function() { Swal.fire('Gagal', 'Pembayaran gagal', 'error'); btn.prop('disabled', false).html('Bayar Sekarang'); }
                            });
                        }, 1000); 
                    }
                },
                error: function(xhr) {
                    let err = xhr.responseJSON ? xhr.responseJSON.error : "Error 500: Cek Console";
                    Swal.fire('Error', err, 'error');
                    btn.prop('disabled', false).html('<i class="mdi mdi-cash-multiple"></i> Bayar Sekarang');
                }
            });
        });
    });
</script>
</body>
</html>