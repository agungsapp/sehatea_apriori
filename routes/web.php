<?php

use App\Http\Controllers\DebugController;
use App\Livewire\Antrian\AntrianPage;
use App\Livewire\Antrian\CreateAntrian;
use App\Livewire\Antrian\EditAntrian;
use App\Livewire\Bahan\BahanPage;
use App\Livewire\Bank\BankPage;
use App\Livewire\Barang\BarangPage;
use App\Livewire\BarangMasuk\BarangMasukPage;
use App\Livewire\BarangMasuk\CreateBarangMasuk;
use App\Livewire\BarangMasuk\EditBarangMasuk;
use App\Livewire\Brand\BrandPage;
use App\Livewire\Cabang\CabangPage;
use App\Livewire\DaftarHarga\DaftarHargaPage;
use App\Livewire\Dashboard\DashboardPage;
use App\Livewire\Instansi\InstansiPage;
use App\Livewire\Jasa\JasaPage;
use App\Livewire\JenisPengeluaran\JenisPengeluaranPage;
use App\Livewire\Kategori\KategoriPage;
use App\Livewire\KonversiSatuan\KonversiSatuanPage;
use App\Livewire\Laporan\LabaRugi\LaporanLabaRugi;
use App\Livewire\Laporan\Pelanggan\LaporanPelanggan;
use App\Livewire\Laporan\Piutang\LaporanPiutang;
use App\Livewire\Laporan\Stok\Stok\LaporanStok;
use App\Livewire\Laporan\Utang\LaporanUtangSupplier;
use App\Livewire\Merk\MerkPage;
use App\Livewire\Pelanggan\PelangganPage;
use App\Livewire\PembelianBahan\PembelianBahanPage;
use App\Livewire\Pengguna\PenggunaPage;
use App\Livewire\Penjualan\PenjualanPage;
use App\Livewire\Penjualan\PenjualanShow;
use App\Livewire\Produk\ProdukPage;
use App\Livewire\ReturBarang\ReturBarangPage;
use App\Livewire\Stok\StokPage;
use App\Livewire\SumberDana\SumberDanaPage;
use App\Livewire\Supplier\Sub\SupplierBelumLunas;
use App\Livewire\Supplier\Sub\SupplierSudahLunas;
use App\Livewire\Supplier\SupplierPage;
use App\Livewire\Transaksi\CreateTransaksi;
use App\Livewire\Transaksi\Pengeluaran\PengeluaranPage;
use App\Livewire\Transaksi\ShowPelunasan;
use App\Livewire\Transaksi\Sub\TransaksiBelumLunas;
use App\Livewire\Transaksi\Sub\TransaksiSudahLunas;
use App\Livewire\Transaksi\TransaksiPage;
use App\Livewire\Utang\ShowPembayaran;
use App\Livewire\Utang\UtangPage;
use App\Models\DaftarHarga;
use App\Models\JenisPengeluaran;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to(route('login'));
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';

// Route::prefix('admin')->name('admin')->middleware('auth')->group(function () {
// });


Route::middleware('auth')->group(function () {

    // sehatea area
    // master data
    Route::get('jenis-pengeluaran', JenisPengeluaranPage::class)->name('jenis-pengeluaran');
    Route::get('sumber-dana', SumberDanaPage::class)->name('sumber-dana');
    // master data end

    Route::get('produk', ProdukPage::class)->name('produk');
    Route::get('bahan', BahanPage::class)->name('bahan');
    Route::get('konversi-satuan', KonversiSatuanPage::class)->name('konversi-satuan');
    Route::get('pembelian-bahan', PembelianBahanPage::class)->name('pembelian-bahan');

    Route::get('mode-kasir', PenjualanPage::class)->name('mode-kasir');
    Route::get('mode-kasir-show/{id}', PenjualanShow::class)->name('mode-kasir-show');
    // sehatea area


    Route::get('dashboard', DashboardPage::class)->name('dashboard');
    Route::get('pengguna', PenggunaPage::class)->name('pengguna');
    // master data
    Route::get('kategori', KategoriPage::class)->name('kategori');
    Route::get('brand', BrandPage::class)->name('brand');
    Route::get('supplier', SupplierPage::class)->name('supplier');
    Route::get('cabang', CabangPage::class)->name('cabang');
    Route::get('barang', BarangPage::class)->name('barang');
    Route::get('instansi', InstansiPage::class)->name('instansi');
    Route::get('jasa', JasaPage::class)->name('jasa');
    // master data end
    Route::get('stok', StokPage::class)->name('stok');
    Route::get('barang-masuk', BarangMasukPage::class)->name('barang-masuk');
    Route::get('edit-barang-masuk/{id}', EditBarangMasuk::class)->name('edit-barang-masuk');
    Route::get('retur-barang', ReturBarangPage::class)->name('retur-barang');
    // drop supplier start
    Route::get('utang', UtangPage::class)->name('utang');
    Route::get('supplier-belum-lunas', SupplierBelumLunas::class)->name('supplier-belum-lunas');
    Route::get('supplier-sudah-lunas', SupplierSudahLunas::class)->name('supplier-sudah-lunas');
    Route::get('bayar-utang/{id}', ShowPembayaran::class)->name('bayar-utang');
    // drop supplier end
    // drop transaction start
    Route::get('create-barang-masuk', CreateBarangMasuk::class)->name('create-barang-masuk');
    Route::get('pelunasan-transaksi/{id}', ShowPelunasan::class)->name('pelunasan-transaksi');
    Route::get('transaksi', TransaksiPage::class)->name('transaksi');
    Route::get('pengeluaran', PengeluaranPage::class)->name('pengeluaran');
    Route::get('transaksi-sudah-lunas', TransaksiSudahLunas::class)->name('transaksi-sudah-lunas');
    Route::get('transaksi-belum-lunas', TransaksiBelumLunas::class)->name('transaksi-belum-lunas');
    Route::get('create-transaksi', CreateTransaksi::class)->name('create-transaksi');
    Route::get('create-transaksi/{id?}', CreateTransaksi::class)->name('create-transaksi.checkout');
    // drop transaction end 

    // drop pelanggan start
    Route::get('pelanggan', PelangganPage::class)->name('pelanggan');

    // drop pelanggan end
    Route::get('daftar-harga', DaftarHargaPage::class)->name('daftar-harga');
    Route::get('antrian', AntrianPage::class)->name('antrian');
    Route::get('create-antrian', CreateAntrian::class)->name('create-antrian');
    Route::get('edit-antrian/{id}', EditAntrian::class)->name('edit-antrian');



    // route grooup for laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('pelanggan', LaporanPelanggan::class)->name('pelanggan');
        Route::get('stok', LaporanStok::class)->name('stok');
        Route::get('utang', LaporanUtangSupplier::class)->name('utang');
        Route::get('piutang', LaporanPiutang::class)->name('piutang');
        Route::get('laba-rugi', LaporanLabaRugi::class)->name('laba-rugi');
    });

    // route grooup for laporan end


});


// Route::get('debug', [DebugController::class, 'lastPrice']);
