<?php

namespace App\Livewire\Laporan\Utang;

use App\LivewireAlertHelpers;
use Carbon\Carbon;
use Livewire\Component;

class LaporanUtangSupplier extends Component
{


    use LivewireAlertHelpers;

    public string $tableName = 'laporan-stok-table';
    public $startDate = null;
    public $endDate = null;

    public $statusPembayaran = 'semua'; // Default filter adalah 'semua'


    public function mount()
    {
        // Set default date range ke bulan ini
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function terapkan()
    {
        $this->dispatch('filterData', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'statusPembayaran' => $this->statusPembayaran,
        ]);
    }

    public function resetFilter()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->statusPembayaran = 'semua';

        $this->dispatch('filterData', [
            'startDate' => null,
            'endDate' => null,
            'statusPembayaran' => 'semua',
        ]);
    }


    public function render()
    {
        return view('livewire.laporan.utang.laporan-utang-supplier');
    }
}
