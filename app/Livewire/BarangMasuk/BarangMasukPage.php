<?php

namespace App\Livewire\BarangMasuk;


use App\Models\BarangMasuk;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class BarangMasukPage extends Component
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
        $this->dispatch('detail-barang-masuk');

        return view('livewire.barang-masuk.barang-masuk-page');
    }
}
