<?php

namespace App\Livewire\Transaksi;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Penjualan;
use App\Models\Supplier;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class TransaksiPage extends Component
{
    use LivewireAlert;

    public $transaksi;


    #[On('handle-detail')]
    public function handleDetail($id)
    {
        $this->transaksi = Penjualan::with(['penjualanDetail', 'penjualanJasa'])->find($id);
        // dd($this->transaksi);
        $this->dispatch('show-detail-modal');
    }

    public function render()
    {
        return view('livewire.transaksi.transaksi-page');
    }
}
