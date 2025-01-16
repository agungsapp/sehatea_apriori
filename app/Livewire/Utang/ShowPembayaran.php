<?php

namespace App\Livewire\Utang;

use App\Models\Bank;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\PembayaranBarangMasuk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowPembayaran extends Component
{
    use LivewireAlert;

    public $bmId;
    public $isTunai = true;
    public $pemId = null;
    public $isShow = false;
    public $isLunas = false;


    // form property
    public $tanggal, $sisa, $bayar, $selectedBank, $catatan;


    // data load select
    public $banks, $pembayarans, $pembayaran, $barangMasuk;



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
        $this->pembayaran = PembayaranBarangMasuk::findOrFail($id);

        // dd("ID diterima: " . $id);
    }
    #[On('detail-pembayaran-close')]
    public function modalClose()
    {
        $this->pemId = null;
        $this->isShow = false;
        $this->pembayaran = null;
        $this->mount($this->bmId);

        // dd("ID diterima: " . $id);
    }

    public function updatedBayar($value)
    {
        $getSisa = PembayaranBarangMasuk::where('id_barang_masuk', $this->bmId)
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

    // #[On('delete-pembayaran')]
    public function delete($id)
    {
        // dd($id);
        $this->pemId = $id;
        // $cabang = PembayaranBarangMasuk::findOrFail($id);
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
            $bm = BarangMasuk::findOrFail($this->bmId);
            $pb = PembayaranBarangMasuk::findOrFail($this->pemId);

            $bm->total_bayar = $bm->total_bayar - $pb->jumlah_dibayar;
            $bm->sisa_tagihan = $bm->sisa_tagihan + $pb->jumlah_dibayar;
            $bm->status_pembayaran = $bm->sisa_tagihan > 0 ? 'belum lunas' : 'lunas';
            $bm->save();

            $pb->delete(); // Gunakan delete() bukan destroy()

            DB::commit();
            $this->alert('success', 'Berhasil menghapus data pembayaran !');
            $this->mount($this->bmId);
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
            $lastPayment = PembayaranBarangMasuk::where('id_barang_masuk', $this->bmId)
                ->orderBy('id', 'desc')
                ->first();

            // Create payment record
            $payment = PembayaranBarangMasuk::create([
                'id_barang_masuk' => $this->bmId,
                'periode' => $lastPayment ? $lastPayment->periode + 1 : 1,
                'tanggal_pembayaran' => $this->tanggal,
                'jumlah_dibayar' => $this->bayar,
                'sisa_hutang' => $lastPayment->sisa_hutang - $this->bayar,
                'metode_pembayaran' => $this->isTunai ? 'tunai' : 'transfer',
                'id_bank' => $this->selectedBank,
                'keterangan' => $this->catatan,
            ]);

            // Update barang masuk
            $barangMasuk = BarangMasuk::findOrFail($this->bmId);
            $newTotalBayar = $barangMasuk->total_bayar + $payment->jumlah_dibayar;
            $newSisaTagihan = $barangMasuk->sisa_tagihan - $payment->jumlah_dibayar;

            $barangMasuk->update([
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
        $this->mount($this->bmId);
    }

    public function mount($id)
    {
        $this->bmId = $id;
        $getSisa = PembayaranBarangMasuk::where('id_barang_masuk', $this->bmId)
            ->orderBy('id', 'desc')->first();
        $this->sisa =  $getSisa->sisa_hutang;

        $this->tanggal = Carbon::now()->format('Y-m-d\TH:i');
        $this->banks = Bank::orderBy('bank', 'asc')->get();
        $this->pembayarans = PembayaranBarangMasuk::where('id_barang_masuk', $this->bmId)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item, $key) {
                $item->max = $key === 0; // Hanya data pertama (id tertinggi) yang diberi properti max
                return $item;
            });
        $barangMasuk = BarangMasuk::findOrFail($this->bmId);
        $this->barangMasuk = $barangMasuk;
        $this->isLunas =  $barangMasuk->status_pembayaran == 'lunas' ? true : false;
    }

    public function render()
    {
        return view('livewire.utang.show-pembayaran');
    }
}
