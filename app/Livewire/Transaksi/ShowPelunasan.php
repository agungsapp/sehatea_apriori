<?php

namespace App\Livewire\Transaksi;

use App\Models\Bank;
use App\Models\PembayaranPenjualan;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowPelunasan extends Component
{
    use LivewireAlert;

    public $pnId;
    public $isTunai = true;
    public $pemId = null;
    public $isShow = false;
    public $isLunas = false;
    public $penjualan;

    // form property
    public $tanggal, $sisa, $bayar, $selectedBank, $catatan;

    // data load select
    public $banks, $pembayarans, $pembayaran;

    protected function rules()
    {
        return [
            'tanggal' => 'required',
            'sisa' => 'required',
            'bayar' => 'required',
            'selectedBank' => 'nullable',
            'catatan' => 'nullable',
        ];
    }

    protected function messages()
    {
        return [
            'tanggal.required' => 'tanggal wajib diisi !',
            'sisa.required' => 'sisa wajib diisi !',
            'bayar.required' => 'bayar wajib diisi !',
        ];
    }

    #[On('load-data')]
    public function loadDataForEdit($id)
    {
        $this->pemId = $id;
        $this->isShow = true;
        $this->pembayaran = PembayaranPenjualan::findOrFail($id);
    }

    #[On('detail-pembayaran-close')]
    public function modalClose()
    {
        $this->pemId = null;
        $this->isShow = false;
        $this->pembayaran = null;
        $this->mount($this->pnId);
    }

    public function updatedBayar($value)
    {
        $getSisa = PembayaranPenjualan::where('id_penjualan', $this->pnId)
            ->orderBy('id', 'desc')->first();

        $this->sisa = $getSisa->sisa_hutang - (int)$value;
        // Jika hasil sisa menjadi negatif
        if ($this->sisa < 0) {
            $this->alert('error', 'Jumlah yang Anda bayarkan melebihi sisa hutang!', [
                'toast' => false,
                'position' => 'center'
            ]);
            $this->bayar = $getSisa->sisa_hutang;
            $this->sisa = 0;
        }
    }

    public function delete($id)
    {
        $this->pemId = $id;
        $this->alert('warning', "Apakah anda yakin ingin menghapus pembayaran ini ?", [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Ya, Hapus!',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'toast' => false,
            'position' => 'center',
            'onConfirmed' => 'deleteConfirmed',
        ]);
    }

    #[On('deleteConfirmed')]
    public function deleteConfirmed()
    {
        DB::beginTransaction();
        try {
            $penjualan = Penjualan::findOrFail($this->pnId);
            $pembayaran = PembayaranPenjualan::findOrFail($this->pemId);

            $penjualan->total_bayar = $penjualan->total_bayar - $pembayaran->jumlah_dibayar;
            $penjualan->sisa_tagihan = $penjualan->sisa_tagihan + $pembayaran->jumlah_dibayar;
            $penjualan->status_pembayaran = $penjualan->sisa_tagihan > 0 ? 'belum lunas' : 'lunas';
            $penjualan->save();

            $pembayaran->delete();

            DB::commit();
            $this->alert('success', 'Berhasil menghapus data pembayaran !');
            $this->mount($this->pnId);
            $this->pemId = null;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->alert('error', 'Terjadi kesalahan pada server !');
        }
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $lastPayment = PembayaranPenjualan::where('id_penjualan', $this->pnId)
                ->orderBy('id', 'desc')
                ->first();

            // Create payment record
            $payment = PembayaranPenjualan::create([
                'id_penjualan' => $this->pnId,
                'periode' => $lastPayment ? $lastPayment->periode + 1 : 1,
                'tanggal_pembayaran' => $this->tanggal,
                'jumlah_dibayar' => $this->bayar,
                'sisa_hutang' => $lastPayment->sisa_hutang - $this->bayar,
                'metode_pembayaran' => $this->isTunai ? 'tunai' : 'transfer',
                'id_bank' => $this->selectedBank,
                'keterangan' => $this->catatan,
            ]);

            // Update penjualan
            $penjualan = Penjualan::findOrFail($this->pnId);
            $newTotalBayar = $penjualan->total_bayar + $payment->jumlah_dibayar;
            $newSisaTagihan = $penjualan->sisa_tagihan - $payment->jumlah_dibayar;

            $penjualan->update([
                'total_bayar' => $newTotalBayar,
                'sisa_tagihan' => $newSisaTagihan,
                'status_pembayaran' => $newSisaTagihan <= 0 ? 'lunas' : 'belum lunas'
            ]);

            DB::commit();
            $this->alert('success', 'berhasil menyimpan data !', [
                'toast' => false,
                'position' => 'center'
            ]);
            $this->resetForm();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->alert('error', 'Terjadi kesalahan saat menyimpan data !');
            throw $th;
        }
    }

    public function resetForm()
    {
        $this->reset(['tanggal', 'selectedBank', 'isTunai', 'bayar', 'sisa', 'catatan']);
        $this->mount($this->pnId);
    }

    public function mount($id)
    {
        $this->pnId = $id;
        $this->penjualan = Penjualan::find($this->pnId);

        $getSisa = PembayaranPenjualan::where('id_penjualan', $this->pnId)
            ->orderBy('id', 'desc')->first();
        $this->sisa = $getSisa->sisa_hutang;

        $this->tanggal = Carbon::now()->format('Y-m-d\TH:i');
        $this->banks = Bank::orderBy('bank', 'asc')->get();
        $this->pembayarans = PembayaranPenjualan::where('id_penjualan', $this->pnId)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item, $key) {
                $item->max = $key === 0; // Hanya data pertama (id tertinggi) yang diberi properti max
                return $item;
            });

        $penjualan = Penjualan::findOrFail($this->pnId);
        $this->isLunas = $penjualan->status_pembayaran == 'lunas';
    }

    public function render()
    {
        return view('livewire.transaksi.show-pelunasan');
    }
}
