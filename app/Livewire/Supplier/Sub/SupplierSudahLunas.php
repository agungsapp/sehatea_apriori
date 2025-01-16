<?php

namespace App\Livewire\Supplier\Sub;

use App\Models\BarangMasuk;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class SupplierSudahLunas extends Component
{
    use LivewireAlert;

    public $detailBarangMasuk;


    #[On('handle-detail')]
    public function handleDetail($id)
    {
        $this->detailBarangMasuk = BarangMasuk::with('barangMasukDetail')->find($id);
        // dd($this->detailBarangMasuk);
        $this->dispatch('show-detail-modal');
    }

    public function render()
    {
        return view('livewire.supplier.sub.supplier-sudah-lunas');
    }
}
