<?php

namespace App\Livewire\Component;

use App\Models\Cabang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class NavbarComponent extends Component
{

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    public function mount()
    {
        // Ambil semua cabang

        // Prioritaskan mengambil dari session, jika tidak ada gunakan default 1

        // $this->selectedCabang = Session::get('selected_cabang') ?? 1;
        // dd(Session::get('selected_cabang'));
    }



    public function render()
    {
        return view('livewire.component.navbar-component');
    }
}
