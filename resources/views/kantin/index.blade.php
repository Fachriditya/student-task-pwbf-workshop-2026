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
    $(document).ready(function() { // Tunggu sampai DOM HTML selesai dibuat oleh browser
        
        // 1. KETIKA KANTIN DIPILIH (Menampilkan Card Gallery)
        $('#vendor_select').on('change', function() { // Deteksi event saat pembeli memilih nama kantin dari dropdown
            let id = $(this).val(); // Ambil ID Kantin yang dipilih
            let gallery = $('#menu_gallery'); // Pilih area/wadah untuk menampung daftar menu
            
            gallery.empty(); // Kosongkan etalase (buang menu kantin sebelumnya jika ada)
            
            if(id) { // Jika ID kantin valid (bukan pilihan kosong "-- Sentuh --")
                // Munculkan efek loading muter (spinner) sambil menunggu data dari server
                gallery.html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div></div>');
                
                $.ajax({ // Minta daftar menu kantin tersebut ke server
                    url: "{{ url('api/menu') }}/" + id, // Alamat API ditambah ID kantin
                    type: "GET", // Ambil data
                    success: function(res) { // Saat server berhasil mengirim data menu (array 'res')
                        gallery.empty(); // Bersihkan loading spinner tadi
                        
                        if(res.length === 0) { // Kalau ternyata array dari server kosong (kantin belum punya menu)
                            gallery.append('<div class="col-12 text-center text-muted">Menu belum tersedia.</div>');
                            return; // Hentikan proses di sini
                        }

                        // Looping pembuatan Card untuk setiap menu yang dikirim server
                        res.forEach(m => { 
                            // LOGIKA GAMBAR: Jika path_gambar kosong di database, pakai gambar dummy dari nama_menu
                            let imgUrl = m.path_gambar 
                                        ? `{{ asset('storage') }}/${m.path_gambar}` // Pakai foto asli kalau ada
                                        : `https://ui-avatars.com/api/?name=${encodeURIComponent(m.nama_menu)}&background=random&color=fff&size=200`; // Bikin foto dummy dari inisial huruf nama menu
                            
                            // Rakit struktur HTML (Card Bootstrap) untuk satu menu
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
                            gallery.append(card); // Suntikkan (tambahkan) card HTML yang sudah dirakit ini ke etalase layar
                        });
                    }
                });
            } else {
                // Jika user mengembalikan pilihan ke default "-- Pilih Kantin --", tampilkan peringatan
                gallery.html('<div class="col-12 text-center text-muted"><i class="mdi mdi-food-variant mdi-48px"></i><p>Silakan pilih kantin terlebih dahulu.</p></div>');
            }
        });

        // 2. KETIKA TOMBOL "TAMBAH" DI CARD DIKLIK
        // Pakai $(document).on(...) karena tombol ini dibuat secara dinamis oleh AJAX, bukan dari HTML bawaan
        $(document).on('click', '.btn-tambah-card', function() { 
            let idMenu = $(this).data('id'); // Tarik data tersembunyi ID Menu dari tombol yang ditekan
            let namaMenu = $(this).data('nama'); // Tarik Nama
            let harga = parseInt($(this).data('harga')); // Tarik Harga, pastikan jadi angka murni (Integer)
            
            // Cek apakah menu ini sudah ada di dalam tabel keranjang belanja
            let existingRow = $(`#tabelKeranjang tbody tr[data-id="${idMenu}"]`); 
            
            if (existingRow.length > 0) { // JIKA SUDAH ADA
                let inputQty = existingRow.find('.qty-input'); // Cari kolom input jumlah (qty) di baris tersebut
                let newQty = parseInt(inputQty.val()) + 1; // Tambahkan jumlah lamanya dengan 1
                inputQty.val(newQty); // Masukkan jumlah baru ke input
                let newSubtotal = newQty * harga; // Hitung subtotal baru (jumlah baru * harga asli)
                existingRow.find('.subtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(newSubtotal)); // Update angka subtotal di layar
            } else { // JIKA BELUM ADA (BARANG BARU)
                let row = `<tr data-id="${idMenu}" data-harga="${harga}"> // Rakit baris tabel (tr) baru
                            <td>${namaMenu}</td>
                            <td><input type="number" class="form-control qty-input p-1 text-center" value="1" min="1"></td>
                            <td class="subtotal">Rp ${new Intl.NumberFormat('id-ID').format(harga)}</td>
                            <td><button class="btn btn-link text-danger p-0 btn-hapus"><i class="mdi mdi-close-circle mdi-24px"></i></button></td>
                          </tr>`;
                $('#tabelKeranjang tbody').append(row); // Tambahkan ke tabel keranjang
            }
            hitungTotalSemua(); // Panggil fungsi rekap total
            $('#btnBayar').prop('disabled', false); // Nyalakan tombol "Bayar Sekarang" karena keranjang sudah terisi
        });

        // 3. UPDATE QTY MANUAL & HAPUS (Tetap Sama)
        $(document).on('change', '.qty-input', function() { // Saat pembeli ngetik ganti jumlah (qty) manual di tabel keranjang
            let row = $(this).closest('tr'); // Tangkap baris yang angkanya lagi diubah
            let harga = parseInt(row.data('harga')); // Ambil memori harga asli dari baris tersebut
            let qty = parseInt($(this).val()); // Ambil angka qty yang baru diketik
            if(qty < 1) { $(this).val(1); qty = 1; } // Pencegahan: Kalau pembeli ketik minus atau 0, paksa balik ke angka 1
            row.find('.subtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(qty * harga)); // Update subtotal
            hitungTotalSemua(); // Update Grand Total
        });

        $(document).on('click', '.btn-hapus', function() {  // Saat klik tombol x (Hapus)
            $(this).closest('tr').remove();  // Hapus baris dari layar
            hitungTotalSemua();  // Hitung ulang totalnya
            if ($('#tabelKeranjang tbody tr').length === 0) $('#btnBayar').prop('disabled', true); // Kalau tabel jadi kosong melompong, matikan lagi tombol Bayar
        });

        function hitungTotalSemua() { // Fungsi tukang rekap angka keranjang
            let total = 0; // Mulai dari 0
            $('.subtotal').each(function() { total += parseInt($(this).text().replace(/[^\d]/g, '')); }); // Loop semua tulisan subtotal, hilangkan huruf "Rp" dan titiknya, lalu tambahkan ke 'total'
            $('#totalBayar').text(new Intl.NumberFormat('id-ID').format(total)); // Cetak angka hasil akhirnya ke layar dengan format titik Rupiah
        }

        // 4. PROSES BAYAR MIDTRANS (Tetap Sama)
        $('#btnBayar').on('click', function() { // Saat tombol "Bayar Sekarang" ditekan
            let items = []; // Siapkan kotak kosong
            $('#tabelKeranjang tbody tr').each(function() { // Loop isi keranjang dan masukkan satu-satu ke kotak
                items.push({
                    id: $(this).data('id'),
                    qty: $(this).find('.qty-input').val(),
                    harga: $(this).data('harga'),
                    subtotal: $(this).find('.subtotal').text().replace(/[^\d]/g, '')
                });
            });

            let btn = $(this); // Simpan elemen tombol bayar
            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Processing...'); // Kunci tombol agar tidak di-klik 2x (double submit), ubah teks jadi tulisan Processing

            $.ajax({ // Kirim keranjang belanja ke server Laravel untuk dicatat ke database dan meminta tiket (token) dari Midtrans
                url: "{{ url('/api/proses-bayar') }}",
                type: "POST",
                data: { // Paket yang dikirim ke server
                    _token: "{{ csrf_token() }}",
                    nama_guest: $('#guest_id').text().trim(), // ID Tamu
                    total: $('#totalBayar').text().replace(/[^\d]/g, ''), // Total bayar
                    items: items // Rincian menu
                },
                success: function(res) { // Jika server dan Midtrans merespon dengan sukses
                    if(res.snap_token) { // Kalau tiket (token) dari Midtrans dikirim
                        setTimeout(function() {
                            window.snap.pay(res.snap_token, { // Panggil *library* bawaan Midtrans untuk membuka pop-up QRIS/Transfer di layar pembeli!
                                onSuccess: function() { // Jika pembeli sudah bayar (misal nge-scan QRIS) dan lunas
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Pembayaran lunas, mengalihkan ke nota...',
                                        icon: 'success',
                                        showConfirmButton: false, // Hilangkan tombol OK
                                        timer: 1500 // Tunggu 1,5 detik biar user baca pesannya
                                    }).then(() => {
                                        // Arahkan otomatis pembeli ke halaman cetak/lihat nota yang berisi Barcode untuk discan ke kantin
                                        window.location.href = "{{ url('/kantin/nota') }}/" + res.idpesanan;
                                    }); 
                                },
                                onPending: function() { Swal.fire('Pending', 'Selesaikan pembayaran', 'info'); btn.prop('disabled', false).html('Bayar Sekarang'); }, // Jika pembeli menutup popup tapi berniat bayar nanti
                                onError: function() { Swal.fire('Gagal', 'Pembayaran gagal', 'error'); btn.prop('disabled', false).html('Bayar Sekarang'); } // Jika error dari sisi bank/Midtrans
                            });
                        }, 1000); // Beri jeda 1 detik agar UI terasa lebih mulus sebelum popup midtrans keluar
                    }
                },
                error: function(xhr) { // Jika gagal di server kita (Laravel error)
                    let err = xhr.responseJSON ? xhr.responseJSON.error : "Error 500: Cek Console";
                    Swal.fire('Error', err, 'error'); // Munculkan popup merah
                    btn.prop('disabled', false).html('<i class="mdi mdi-cash-multiple"></i> Bayar Sekarang'); // Nyalakan lagi tombol bayar
                }
            });
        });
    });
</script>
</body>
</html>