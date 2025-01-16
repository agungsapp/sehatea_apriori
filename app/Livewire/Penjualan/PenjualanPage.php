<?php

namespace App\Livewire\Penjualan;

use App\Http\Controllers\Api\ApiTransaksiController;
use App\LivewireAlertHelpers;
use App\Models\Bahan;
use App\Models\DetailTransaksi;
use App\Models\JenisPengeluaran;
use App\Models\MetodePembayaran;
use App\Models\MetodePembelian;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\SumberDana;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class PenjualanPage extends Component
{

    use LivewireAlertHelpers;

    public $produk;
    public $metode_pembelian;
    public $metode_pembayaran;
    public $cart = [];
    public $jumlah = [];
    public $metode_pembayaran_selected = [];
    public $metode_pembelian_selected = [];
    public $total = 0;

    public $transaksiHariIni;
    public $totalTransaksiHariIni;
    public $totalCash;
    public $totalNonCash;
    public $selectedTransaksi;

    public $editingTransaksiId;
    public $editMetodePembayaranId;
    public $editMetodePembelianId;
    public $transaksiId;

    public function mount()
    {

        $this->produk = Produk::where('active', true)->get();
        $this->metode_pembelian = MetodePembelian::all();
        $this->metode_pembayaran = MetodePembayaran::all();

        // Set nilai default
        $defaultPembayaran = MetodePembayaran::where('nama', 'Tunai')->first();
        $defaultPembelian = MetodePembelian::where('nama', 'Toko')->first();

        if ($defaultPembayaran) {
            $this->metode_pembayaran_selected[] = $defaultPembayaran->id;
        }
        if ($defaultPembelian) {
            $this->metode_pembelian_selected[] = $defaultPembelian->id;
        }


        $this->loadTransaksiHariIni();
    }

    public function deleteTransaksi($transaksiId)
    {
        $this->transaksiId = $transaksiId; // Simpan ID ke property
        $data = Transaksi::findOrFail($transaksiId);
        $this->showCon(
            "Apakah anda yakin ingin menghapus data <strong>{$data->nama}</strong> ?",
            'deleteItemConfirmed'
        );
    }

    #[On('deleteItemConfirmed')]
    public function deleteItemConfirmed()
    {
        try {
            $data = Transaksi::findOrFail($this->transaksiId);
            $data->delete();
            $this->transaksiId = null;
            $this->showSuccess("Berhasil menghapus data !");
            $this->dispatch('refresh-data');
            $this->loadTransaksiHariIni();
        } catch (\Exception $e) {
            logger()->error('Error deleting transaction: ' . $e->getMessage());
            $this->showError("Terjadi kesalahan pada server!");
        }
    }

    public function editTransaksi($transaksiId)
    {
        $this->editingTransaksiId = $transaksiId;
        $transaksi = Transaksi::find($transaksiId);
        $this->editMetodePembayaranId = $transaksi->metode_pembayaran_id;
        $this->editMetodePembelianId = $transaksi->metode_pembelian_id;
    }

    public function updateTransaksi()
    {
        $transaksi = Transaksi::find($this->editingTransaksiId);
        if ($transaksi) {
            $transaksi->update([
                'metode_pembayaran_id' => $this->editMetodePembayaranId,
                'metode_pembelian_id' => $this->editMetodePembelianId,
            ]);
            $this->editingTransaksiId = null;
            $this->loadTransaksiHariIni();
            session()->flash('message', 'Transaksi berhasil diperbarui.');
        }
    }

    public function cancelEdit()
    {
        $this->editingTransaksiId = null;
    }

    public function loadTransaksiHariIni()
    {
        $this->transaksiHariIni = Transaksi::whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->get();
        $this->totalTransaksiHariIni = $this->transaksiHariIni->sum('grand_total');

        $metodePembayaranCash = MetodePembayaran::where('nama', 'Tunai')->first();
        $this->totalCash = $this->transaksiHariIni->where('metode_pembayaran_id', $metodePembayaranCash->id)->sum('grand_total');
        $this->totalNonCash = $this->totalTransaksiHariIni - $this->totalCash;
    }

    public function showDetail($transaksiId)
    {
        $this->selectedTransaksi = Transaksi::with('detailTransaksi.produk', 'metodePembayaran', 'metodePembelian')->find($transaksiId);
    }

    public function closeModal()
    {
        $this->selectedTransaksi = null;
    }

    public function tambahKeKeranjang($id)
    {
        if (!isset($this->cart[$id])) {
            $this->cart[$id] = Produk::find($id);
            $this->jumlah[$id] = 1;
        } else {
            $this->jumlah[$id]++;
        }
        $this->hitungTotal();
    }

    public function kurangiDariKeranjang($id)
    {
        if (isset($this->jumlah[$id]) && $this->jumlah[$id] > 1) {
            $this->jumlah[$id]--;
        } else {
            unset($this->cart[$id]);
            unset($this->jumlah[$id]);
        }
        $this->hitungTotal();
    }

    public function hitungTotal()
    {
        $this->total = array_sum(array_map(function ($id) {
            return $this->cart[$id]->harga * $this->jumlah[$id];
        }, array_keys($this->cart)));
    }

    public function checkout()
    {
        // Validasi
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong. Tidak dapat melakukan checkout.');
            return;
        }
        if (empty($this->metode_pembayaran_selected) || empty($this->metode_pembelian_selected)) {
            session()->flash('error', 'Pilih metode pembayaran dan pembelian.');
            return;
        }

        // Simpan transaksi
        $kode_transaksi = 'SHT-' . Str::random(8);
        $transaksi = Transaksi::create([
            'kode' => $kode_transaksi,
            'grand_total' => $this->total,
            'metode_pembayaran_id' => $this->metode_pembayaran_selected[0], // Ambil metode pembayaran pertama
            'metode_pembelian_id' => $this->metode_pembelian_selected[0], // Ambil metode pembelian pertama
        ]);

        // Simpan detail transaksi
        foreach ($this->cart as $id => $item) {
            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $id,
                'kode' => 'DTX-' . Str::random(8),
                'harga' => $item->harga,
                'qty' => $this->jumlah[$id],
                'subtotal' => $item->harga * $this->jumlah[$id],
            ]);
        }

        // Reset keranjang
        $this->cart = [];
        $this->jumlah = [];
        $this->total = 0;
        $this->loadTransaksiHariIni();


        if (env('APP_MODE') == 'production') {
            $apiTransaksi = new ApiTransaksiController();
            $apiTransaksi->sendDailySalesReport();
        }

        session()->flash('message', 'Transaksi berhasil disimpan.');
    }

    public function confirmDelete()
    {
        $this->dispatch('oke:aja');
        // $this->dispatch('swal:confirm', message: 'Are you sure?', text: 'If deleted, you will not be able to recover this imaginary file!');
    }

    public function render()
    {
        return view('livewire.penjualan.penjualan-page');
    }
}
