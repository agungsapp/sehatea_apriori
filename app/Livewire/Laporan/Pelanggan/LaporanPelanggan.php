<?php

namespace App\Livewire\Laporan\Pelanggan;

use App\LivewireAlertHelpers;
use Carbon\Carbon;
use Livewire\Component;


class LaporanPelanggan extends Component
{
    use LivewireAlertHelpers;

    public string $tableName = 'laporan-pelanggan-table';
    public $startDate = null;
    public $endDate = null;


    public function mount()
    {
        // Set default date range ke bulan ini
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function terapkan()
    {
        $this->dispatch('filterDate', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }

    public function resetFilter()
    {
        $this->startDate = null;
        $this->endDate = null;

        // Kirim event filter dengan nilai null
        $this->dispatch('filterDate', [
            'startDate' => null,
            'endDate' => null
        ]);
    }

    public function render()
    {
        return view('livewire.laporan.pelanggan.laporan-pelanggan');
    }
}
