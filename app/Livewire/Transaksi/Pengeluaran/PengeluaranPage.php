<?php

namespace App\Livewire\Transaksi\Pengeluaran;

use App\LivewireAlertHelpers;
use Livewire\Component;

class PengeluaranPage extends Component
{

    use LivewireAlertHelpers;

    public $isEdit = false;

    public $noInvoice, $nama, $harga, $tanggal, $catatan;



    public function mount()
    {
        $this->noInvoice = \App\Models\Pengeluaran::generateKodeInvoice();
    }


    public function resetForm()
    {
        $this->reset(['nama', 'harga', 'tanggal', 'catatan']);
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required',
            'harga' => 'required',
            'tanggal' => 'required',
        ]);

        $data = [
            'no_invoice' => $this->noInvoice,
            'nama' => $this->nama,
            'harga' => $this->harga,
            'tanggal' => $this->tanggal,
            'catatan' => $this->catatan,
        ];

        \App\Models\Pengeluaran::create($data);

        $this->alert('success', 'Data berhasil disimpan');
        $this->resetForm();
    }


    public function render()
    {
        return view('livewire.transaksi.pengeluaran.pengeluaran-page');
    }
}
