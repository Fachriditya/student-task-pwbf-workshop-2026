<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\KantinController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LokasiTokoController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\AbsensiController;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/kantin', [App\Http\Controllers\KantinController::class, 'index'])->name('kantin.index');
Route::get('/api/menu/{id}', [App\Http\Controllers\KantinController::class, 'getMenuByVendor']);
Route::post('/api/proses-bayar', [App\Http\Controllers\KantinController::class, 'bayar']);
Route::get('/kantin/nota/{id}', [App\Http\Controllers\KantinController::class, 'nota'])->name('kantin.nota');

Route::post('/guest/daftar-cepat', [App\Http\Controllers\AntrianController::class, 'daftarCepat'])->name('antrian.daftar.cepat');
Route::get('/sse/antrian', [App\Http\Controllers\AntrianController::class, 'stream'])->name('antrian.stream');
Route::get('/papan', [App\Http\Controllers\AntrianController::class, 'papan'])->name('antrian.papan');

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware('guest')->group(function () {
    Route::get('/otp/verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('/otp/verify', [OtpController::class, 'verify']);
    Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/book', [BookController::class, 'index'])->name('book.index');
    Route::get('/book/create', [BookController::class, 'create'])->name('book.create');
    Route::post('/book', [BookController::class, 'store'])->name('book.store');

    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');

    Route::get('/pdf/sertifikat', [PdfController::class, 'certificate'])->name('pdf.sertifikat');
    Route::get('/pdf/undangan', [PdfController::class, 'invitation'])->name('pdf.undangan');

    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::post('/barang/print', [BarangController::class, 'print'])->name('barang.print');
    Route::get('/barang/api/scan/{id}', [BarangController::class, 'getBarangData']);

    Route::get('/kasir', [BarangController::class, 'kasir'])->name('barang.kasir');
    Route::post('/kasir/bayar', [BarangController::class, 'simpanTransaksi'])->name('barang.bayar');

    Route::post('/barang/cari', [BarangController::class, 'cariBarang'])->name('barang.cari');

    Route::get('/scan-barcode', [BarangController::class, 'scanner'])->name('barang.scanner');

    Route::get('/kantin-scanner', [KantinController::class, 'scannerVendor'])->name('kantin.scanner');
    Route::get('/kantin/api/scan-pesanan/{id}', [KantinController::class, 'getPesananData']);

    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');

    Route::get('/wilayah/provinsi', [WilayahController::class, 'getProvinsi'])->name('wilayah.provinsi');
    Route::get('/wilayah/kota/{id}', [WilayahController::class, 'getKota'])->name('wilayah.kota');
    Route::get('/wilayah/kecamatan/{id}', [WilayahController::class, 'getKecamatan'])->name('wilayah.kecamatan');
    Route::get('/wilayah/kelurahan/{id}', [WilayahController::class, 'getKelurahan'])->name('wilayah.kelurahan');

    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customer/create1', [CustomerController::class, 'create1'])->name('customer.create1');
    Route::get('/customer/create2', [CustomerController::class, 'create2'])->name('customer.create2');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');

    Route::get('/toko', [LokasiTokoController::class, 'index'])->name('toko.index');
    Route::get('/toko/create', [LokasiTokoController::class, 'create'])->name('toko.create');
    Route::post('/toko', [LokasiTokoController::class, 'store'])->name('toko.store');
    Route::get('/toko/barcode/{barcode}', [LokasiTokoController::class, 'cetakBarcode'])->name('toko.barcode');

    Route::get('/kunjungan', [LokasiTokoController::class, 'kunjungan'])->name('kunjungan.index');
    Route::post('/kunjungan/proses', [LokasiTokoController::class, 'prosesKunjungan'])->name('kunjungan.proses');

    Route::get('/admin-antrian/api-data', [AntrianController::class, 'apiData'])->name('antrian.api.data');
    Route::get('/admin-antrian', [AntrianController::class, 'admin'])->name('antrian.admin');
    Route::post('/admin-antrian/panggil', [AntrianController::class, 'panggil'])->name('antrian.panggil');
    Route::post('/admin-antrian/panggil-selesai', [AntrianController::class, 'panggilSelesai'])->name('antrian.panggil.selesai');
    Route::post('/admin-antrian/panggil-terlewat', [AntrianController::class, 'panggilTerlewat'])->name('antrian.panggil.terlewat');
    Route::post('/admin-antrian/panggil-ulang', [AntrianController::class, 'panggilUlang'])->name('antrian.panggil.ulang');

    Route::get('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');
    Route::post('/absensi/proses', [AbsensiController::class, 'prosesAbsensi'])->name('absensi.proses');

});
Auth::routes();


