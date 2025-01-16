<?php

namespace App\Livewire\Pelanggan;

use App\LivewireAlertHelpers;
use Livewire\Component;

class PelangganPage extends Component
{
    use LivewireAlertHelpers;

    public $brandId, $nama;
    public $isEdit = false;


    public function render()
    {
        return view('livewire.pelanggan.pelanggan-page');
    }
}
