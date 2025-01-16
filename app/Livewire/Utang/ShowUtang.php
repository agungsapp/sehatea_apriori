<?php

namespace App\Livewire\Utang;

use App\Models\BarangMasuk;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowUtang extends Component
{
    public $isFill = false;
    public $supId = null;
    public $barangMasuks;

    #[On('load-utang-data')]
    public function loadBank($id)
    {
        $this->supId = $id;
        $isFill = true;
        $this->barangMasuks = BarangMasuk::where('id_suplier', $id)->where('status_pembayaran', '!=', 'lunas')->get();
    }

    public function render()
    {
        return view('livewire.utang.show-utang');
    }
}
