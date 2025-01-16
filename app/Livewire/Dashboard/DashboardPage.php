<?php

namespace App\Livewire\Dashboard;

use App\Models\BarangMasuk;
use App\Models\Pengeluaran;
use App\Models\PengeluaranLain;
use App\Models\Penjualan;
use App\Models\Stok;
use App\Models\Supplier;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Carbon\Carbon;

class DashboardPage extends Component
{
    public $utangs;
    public $stocks;

    public $omset;

    public $startDate, $endDate;
    public $totalTransaksi, $labaKotor, $labaBersih, $pengeluaran, $pengeluaranLain;

    public function mount()
    {
        // Set default dates to first and last day of current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

        $this->loadData();
        $this->loadStats();
    }

    public function loadData()
    {
        $this->loadStats();
    }

    private function loadStats()
    {

        $transaksiQuery = Transaksi::query();

        if ($this->startDate && $this->endDate) {
            $transaksiQuery->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }



        $this->omset = $transaksiQuery->sum('grand_total');
        $this->totalTransaksi = $transaksiQuery->count();

        // Pengeluaran query
        $pengeluaranQuery = Pengeluaran::query();
        // Apply date filter only if both dates are set
        if ($this->startDate && $this->endDate) {
            $pengeluaranQuery->whereBetween('tanggal', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }
        $pengeluaranLainQuery = PengeluaranLain::query();
        if ($this->startDate && $this->endDate) {
            $pengeluaranQuery->whereBetween('tanggal', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }

        $this->pengeluaran = $pengeluaranQuery->sum('subtotal');
        $this->pengeluaranLain =  $pengeluaranLainQuery->sum('harga');
    }

    public function resetFilter()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->loadData();
    }

    public function updatedStartDate()
    {
        $this->loadData();
    }

    public function updatedEndDate()
    {
        $this->loadData();
    }

    public function getFormattedDateRangeProperty()
    {
        if (!$this->startDate || !$this->endDate) {
            return 'Semua Waktu';
        }

        return Carbon::parse($this->startDate)->locale('id')->format('M d') .
            ' - ' .
            Carbon::parse($this->endDate)->locale('id')->format('M d');
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-page');
    }
}
