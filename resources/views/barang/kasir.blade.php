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
    function hitungTotal() { // Mendeklarasikan fungsi untuk menjumlahkan semua subtotal belanjaan
        let total = 0; // Siapkan wadah variabel untuk menampung total nilai, mulai dari 0
        $('.subtotal-item').each(function() { // Loop (ulangi) ke setiap elemen HTML yang memiliki class 'subtotal-item' (ada di setiap baris tabel)
            total += parseInt($(this).data('raw')); // Ambil nilai asli (tanpa titik pemisah ribuan) dari atribut 'data-raw' dan tambahkan ke variabel 'total'
        });
        $('#totalBayar').text(new Intl.NumberFormat('id-ID').format(total)); // Format angka total jadi format Rupiah (contoh: 10000 -> 10.000) lalu tampilkan di layar
    }

    // 2. Pencarian Barang (ENTER)
    $('#kode_barang').on('keypress', function(e) { // Pasang pendengar event (listener): jika ada tombol keyboard ditekan di kolom input 'kode_barang'
        if (e.which == 13) { // Cek apakah tombol yang ditekan adalah tombol "Enter" (kode ASCII 13)
            let id = $(this).val().trim(); // Tambah trim biar aman. Ambil isi inputan, lalu hilangkan spasi tidak sengaja di awal/akhir teks
            if(id == "") return; // Jika inputan kosong tapi dienter, batalkan proses (return)

            $.ajax({ // Kirim permintaan (request) ke server Laravel tanpa refresh halaman
                url: "{{ route('barang.cari') }}", // Alamat tujuan untuk mencari data barang
                type: "POST", // Gunakan metode POST
                data: {
                    _token: "{{ csrf_token() }}", // Tiket keamanan wajib dari Laravel
                    id_barang: id // Kirimkan ID/Kode barang yang diketik tadi ke server
                },
                success: function(response) { // Jika server berhasil menemukan barang dan membalas
                    $('#nama_barang').val(response.data.nama); // Isi kolom input 'nama_barang' otomatis dengan data dari server
                    $('#harga_barang').val(response.data.harga); // Isi kolom input 'harga_barang' otomatis dengan harga dari server
                    $('#btnTambah').prop('disabled', false); // Nyalakan (enable) tombol "Tambah" agar bisa diklik kasir
                },
                error: function() { // Jika server membalas dengan error (misal: barang tidak ditemukan di database)
                    Swal.fire('Error!', 'Kode barang tidak terdaftar', 'error'); // Munculkan pop-up error merah
                    $('#nama_barang').val(''); // Kosongkan kembali kolom nama
                    $('#harga_barang').val(''); // Kosongkan kembali kolom harga
                    $('#btnTambah').prop('disabled', true); // Matikan lagi (disable) tombol "Tambah"
                }
            });
        }
    });

    // 3. Tombol Tambah ke Keranjang
    $('#btnTambah').on('click', function() { // Jika tombol "Tambah" diklik oleh kasir
        let kode = $('#kode_barang').val().trim(); // Ambil data kode dari kolom input pencarian
        let nama = $('#nama_barang').val(); // Ambil nama barang
        let harga = parseInt($('#harga_barang').val()); // Ambil harga barang, pastikan diubah jadi angka murni (Integer)
        let qty = 1; // Setel jumlah beli bawaan (default) adalah 1
        
        let ada = false; // Bikin variabel penanda: apakah barang ini sudah ada di keranjang tabel bawah atau belum
        $('#tabelBelanja tbody tr').each(function() { // Loop/cek satu-satu setiap baris di tabel keranjang belanja
            // Pastikan perbandingan string-nya bersih
            if ($(this).find('.td-kode-text').text().trim() == kode) { // Jika ternyata kode barang di baris tabel ini SAMA dengan barang yang baru mau ditambah
                let inputQty = $(this).find('.input-qty'); // Cari kolom input jumlah (qty) di baris tabel tersebut
                let newQty = parseInt(inputQty.val()) + 1; // Ambil jumlah lamanya, lalu tambah 1
                inputQty.val(newQty); // Tulis ulang jumlah baru itu ke kolom input tabel
                
                let newSubtotal = newQty * harga; // Hitung ulang harga subtotal baris itu (jumlah baru * harga satuan)
                $(this).find('.subtotal-item').text(new Intl.NumberFormat('id-ID').format(newSubtotal)); // Ganti tulisan subtotal di layar dengan format Rupiah
                $(this).find('.subtotal-item').data('raw', newSubtotal); // Update juga data tersembunyinya ('data-raw') dengan nilai yang belum diformat
                ada = true; // Tandai bahwa barangnya SUDAH ADA (biar gak dibikin baris tabel baru)
            }
        });

        if (!ada) { // Kalau barang ini BELUM ADA di keranjang tabel bawah
            let row = ` // Buatkan struktur HTML baris tabel baru
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
            $('#tabelBelanja tbody').append(row); // Suntikkan (tambahkan) baris tabel baru tadi ke baris paling bawah tabel keranjang
        }

        hitungTotal(); // Panggil fungsi penghitung total (yang di atas tadi) untuk memperbarui harga "Total Pembayaran"
        $('#kode_barang').val('').focus(); // Bersihkan kolom pencarian, lalu arahkan kursor ngetik kembali ke sana otomatis
        $('#nama_barang').val(''); // Bersihkan kolom nama
        $('#harga_barang').val(''); // Bersihkan kolom harga
        $('#btnTambah').prop('disabled', true); // Matikan lagi tombol "Tambah"
    });

    // 4. Hapus Item
    $('#tabelBelanja').on('click', '.btn-hapus', function() { // Jika kasir klik icon Tempat Sampah (Hapus) pada suatu baris
        $(this).closest('tr').remove(); // Cari baris tabel (<tr>) terdekat dari tombol itu, lalu hapus seluruh barisnya dari layar!
        hitungTotal(); // Karena ada barang dihapus, hitung ulang Total Pembayaran akhirnya
    });

    // 5. Update Quantity (INI YANG TADI KURANG KURUNG TUTUP)
    $('#tabelBelanja').on('change keyup', '.input-qty', function() { // Jika kasir mengubah angka jumlah (qty) langsung dari dalam tabel (diketik atau diklik panah naik-turun)
        let qty = parseInt($(this).val()); // Ambil angka jumlah yang baru
        if(isNaN(qty) || qty < 1) qty = 1; // Keamanan: kalau kasir hapus isinya (kosong) atau ketik angka minus, paksa kembali jadi angka 1

        let hargaText = $(this).closest('tr').find('td:nth-child(3)').text().replace(/[^\d]/g, ''); // Cek ke kolom ke-3 (kolom Harga), ambil teksnya, lalu hilangkan huruf "Rp" dan titiknya biar sisa angka doang
        let harga = parseInt(hargaText); // Ubah teks angka tadi jadi Integer murni
        let subtotal = qty * harga; // Kalikan jumlah baru dengan harga satuan
        
        $(this).closest('tr').find('.subtotal-item').text(new Intl.NumberFormat('id-ID').format(subtotal)); // Update tulisan angka subtotal di layar baris tersebut
        $(this).closest('tr').find('.subtotal-item').data('raw', subtotal); // Update juga data tersembunyinya ('data-raw')
        hitungTotal(); // Karena subtotal satu baris berubah, hitung ulang "Total Pembayaran" keseluruhannya
    }); // <--- Tadi kurang ini

    // 6. Proses Bayar
    $('#btnBayar').on('click', function() { // Jika kasir mengeklik tombol hijau "Proses Pembayaran" di paling bawah
        let items = []; // Siapkan keranjang kosong untuk menampung rincian barang yang akan dikirim ke server
        
        $('#tabelBelanja tbody tr').each(function() { // Loop/cek setiap baris barang yang ada di tabel keranjang
            items.push({ // Masukkan data baris ini ke dalam array 'items'
                kode: $(this).find('.td-kode-text').text().trim(), // Ambil kode barangnya
                qty: $(this).find('.input-qty').val(), // Ambil jumlah belinya
                subtotal: $(this).find('.subtotal-item').data('raw') // Ambil total harga khusus barang ini
            });
        });

        if (items.length === 0) { // Validasi: kalau array 'items' panjangnya 0 (artinya tabelnya kosong gak ada barang)
            Swal.fire('Peringatan', 'Belum ada barang di keranjang!', 'warning'); // Cegah kasir, munculkan pop-up peringatan kuning
            return; // Batalkan proses bayar
        }

        let btn = $(this); // Simpan elemen tombol bayar ke dalam variabel 'btn'
        let originalContent = btn.html(); // Simpan teks asli tombol (misal: "Proses Pembayaran")
        btn.html('<span class="spinner-border spinner-border-sm"></span> Memproses...').prop('disabled', true); // Ubah teks tombol jadi ada animasi muternya, lalu matikan tombolnya biar kasir gak ngeklik 2 kali

        $.ajax({ // Kirim semua data belanja ke server untuk disimpan ke database
            url: "{{ route('barang.bayar') }}", // Tuju alamat route checkout
            type: "POST", // Pakai metode POST untuk simpan data
            data: { // Data paket yang dikirim
                _token: "{{ csrf_token() }}", // Tiket keamanan
                total: $('#totalBayar').text().replace(/[^\d]/g, ''), // Kirim angka total bayarnya (tanpa Rp dan titik)
                items: items // Kirim isi keranjang yang udah dirakit di awal tadi
            },
            success: function(response) { // Jika server berhasil menyimpan datanya
                Swal.fire('Berhasil!', response.message, 'success').then(() => { // Munculkan pop-up berhasil (centang hijau)
                    location.reload(); // Kalau pop-up nya di-oke-in (ditutup) kasir, langsung refresh (reload) halaman agar kembali kosong untuk pelanggan berikutnya
                });
            },
            error: function(xhr) { // Jika ada masalah di server (misal database mati atau stok habis)
                Swal.fire('Error!', xhr.responseJSON.message || 'Gagal menyimpan transaksi', 'error'); // Munculkan pop-up gagal
                btn.html(originalContent).prop('disabled', false); // Kembalikan bentuk tombol bayar seperti semula dan nyalakan lagi
            }
        });
    });
</script>
@endsection