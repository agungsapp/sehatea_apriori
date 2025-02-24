<?php

namespace App\Livewire\Komposisi;

use App\Models\Produk;
use Livewire\Attributes\On;
use Livewire\Component;

class KomposisiCard extends Component
{

    public $produks;

    #[On('update-card')]
    public function mount()
    {
        $this->produks = Produk::with(['komposisi.bahan'])
            ->orderBy('nama', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.komposisi.komposisi-card');
    }
}
