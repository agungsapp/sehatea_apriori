<?php

namespace App\Livewire\Transaksi\Sub;

use App\Models\Penjualan;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class TransaksiSudahLunas extends Component
{
    use LivewireAlert;

    public $transaksi;


    #[On('handle-detail')]
    public function handleDetail($id)
    {
        $this->transaksi = Penjualan::with('penjualanDetail')->find($id);
        // dd($this->transaksi);
        $this->dispatch('show-detail-modal');
    }

    public function render()
    {
        return view('livewire.transaksi.sub.transaksi-sudah-lunas');
    }
}
