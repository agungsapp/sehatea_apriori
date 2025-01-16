<?php

namespace App\Livewire\Utang;

use Livewire\Attributes\On;
use Livewire\Component;

class UtangPage extends Component
{

    #[On('show-utang')]
    public function loadDataForEdit($id)
    {
        // dd("oke");
        $this->dispatch('load-utang-data', $id);
        $this->dispatch('show-modal-utang-data');
    }

    public function render()
    {
        return view('livewire.utang.utang-page');
    }
}
