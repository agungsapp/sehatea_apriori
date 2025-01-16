<?php

namespace App\Livewire\Laporan\LabaRugi;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\Jasa;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\PenjualanJasa;
use Carbon\Carbon;
use Livewire\Component;

class LaporanLabaRugi extends Component
{

    public $pendapatanJasa, $pendapatanSparepart, $pendapatanLainya;
    public $bebanPengeluaran, $utangSudahDibayarkan;

    public $total;

    public $startDate = null;
    public $endDate = null;


    public function mount()
    {
        // Set default date range ke bulan ini
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->loadData();
    }




    public function loadData()
    {
        $this->pendapatanJasa = PenjualanJasa::whereHas('penjualan', function ($q) {
            $q->where('status_pembayaran', 'lunas');
        })->sum('harga_jasa');

        $this->pendapatanSparepart = PenjualanDetail::whereHas('penjualan', function ($q) {
            $q->where('status_pembayaran', 'lunas');
        })->sum('subtotal');

        // dd($this->pendapatanJasa, $this->pendapatanSparepart);

        $this->bebanPengeluaran = Pengeluaran::all()->sum('harga');
        $this->utangSudahDibayarkan = BarangMasuk::where('status_pembayaran', 'belum lunas')->sum('total_bayar');

        $this->total = 0;
        $this->total += $this->pendapatanJasa;
        $this->total += $this->pendapatanSparepart;
        $this->total -= $this->bebanPengeluaran;
        $this->total -= $this->utangSudahDibayarkan;
    }


    public function resetFilter()
    {
        $this->startDate = null;
        $this->endDate = null;
    }


    public function render()
    {
        return view('livewire.laporan.laba-rugi.laporan-laba-rugi');
    }
}
