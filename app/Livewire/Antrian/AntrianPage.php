<?php

namespace App\Livewire\Antrian;

use App\LivewireAlertHelpers;
use App\Models\Antrian;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Penjualan;
use App\Models\Supplier;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class AntrianPage extends Component
{
    use LivewireAlertHelpers;

    public $transaksi;
    public $antrianId;



    #[On('chekout')]
    public function chekout($id)
    {
        $this->antrianId = $id;
        $antrian = Antrian::findOrFail($id);
        $namaPelanggan = $antrian->pelangganKendaraan->pelanggan->tipe == 'instansi' ? $antrian->pelangganKendaraan->pelanggan->instansi->nama_instansi : $antrian->pelangganKendaraan->pelanggan->nama;
        $this->showCon("proses data antrian pelanggan atas nama <strong>$namaPelanggan</strong> menuju halaman transaksi ?", 'chekoutConfirmed', [
            'confirmButtonText' => 'Ya, Chekout!',
        ]);
    }
    #[On('chekoutConfirmed')]
    public function chekoutConfirmed()
    {
        return redirect()->to(route('create-transaksi.checkout', ['id' => $this->antrianId]));
    }



    #[On('handle-detail')]
    public function handleDetail($id)
    {
        $this->transaksi = Penjualan::with(['penjualanDetail', 'penjualanJasa'])->find($id);
        // dd($this->transaksi);
        $this->dispatch('show-detail-modal');
    }

    public function render()
    {
        return view('livewire.antrian.antrian-page');
    }
}
