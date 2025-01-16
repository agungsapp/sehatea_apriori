<?php

namespace App\Livewire\ReturBarang;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\ReturBarang;
use App\Models\Stok;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ReturBarangPage extends Component
{

    use LivewireAlert;

    public $isTunai = true;

    public $selectedBarang, $selectedSupplier;
    public $hargaSatuan, $jumlah, $totalHarga, $metode, $catatan, $tanggalRetur;

    public $barangs, $suppliers, $returs;

    public function mount()
    {
        $this->barangs = Barang::whereHas('stok', function ($query) {
            $query->where('stok', '>', 0); // Pastikan stok lebih dari 0
        })
            ->orderBy('nama_barang', 'asc')
            ->get();
        $this->suppliers = Supplier::orderBy('nama', 'asc')->get();
        $this->returs = ReturBarang::with('user', 'cabang', 'supplier')->get();
        // dd($this->returs);
    }


    public function updatedJumlah($value)
    {
        $this->calculateTotalHarga();
    }
    public function updatedHargaSatuan($value)
    {
        $this->calculateTotalHarga();
    }
    public function calculateTotalHarga()
    {
        if ($this->jumlah != null && $this->hargaSatuan != null) {
            $this->totalHarga = $this->jumlah * $this->hargaSatuan;
        }
    }

    public function updatedSelectedBarang($value)
    {
        $this->getPrice();
    }
    public function updatedSelectedSupplier($value)
    {
        $this->getPrice();
    }

    public function getPrice()
    {
        if ($this->selectedBarang != null && $this->selectedSupplier != null) {
            // Ambil barang masuk berdasarkan supplier yang dipilih
            $barangMasuk = BarangMasuk::where('id_suplier', $this->selectedSupplier)
                ->whereHas('barangMasukDetail', function ($q) {
                    $q->where('id_barang', $this->selectedBarang);
                })
                ->orderBy('id', 'desc')
                ->first();

            // Pastikan barangMasuk dan barangMasukDetail ada
            if ($barangMasuk && $barangMasuk->barangMasukDetail->isNotEmpty()) {
                // Ambil harga satuan dari barangMasukDetail yang terakhir
                $this->hargaSatuan = $barangMasuk->barangMasukDetail->first()->harga_satuan;
            }
        }
    }

    public function save()
    {
        DB::beginTransaction();
        try {
            $retur = new ReturBarang();
            $retur->id_barang = $this->selectedBarang;
            $retur->id_supplier = $this->selectedSupplier;
            $retur->id_cabang = Session::get('selected_cabang') ?? 1;;
            $retur->id_user = Auth::id() ?? 1;
            $retur->harga_satuan = $this->hargaSatuan;
            $retur->jumlah = $this->jumlah;
            $retur->total_harga = $this->totalHarga;
            $retur->metode = $this->isTunai ? 'tunai' : 'tukar';
            $retur->catatan = $this->catatan;
            $retur->tanggal_retur = $this->tanggalRetur ?? now();
            $retur->save();

            if ($this->isTunai) {
                $stok = Stok::where('id_cabang', Session::get('selected_cabang') ?? 1)
                    ->where('id_barang', $this->selectedBarang)
                    ->first();
                if ($stok) {
                    $stok->stok -= $this->jumlah;
                    if ($stok->stok < 0) {
                        // throw new \Exception("Stok tidak cukup untuk retur.");
                        $this->showError("stok tidak cukup untuk retur dengan metode tunai !");
                    }
                    $stok->save();
                } else {
                    $this->showError('stok tidak ditemukan !');
                }
            }

            // dd($stok);

            DB::commit();
            $this->showSuccess("retur barang berhasil ! ");
        } catch (\Throwable $th) {
            DB::rollBack();
            //throw $th;
            $this->showError('Terjadi kesalahan saat menyimpan !');
        }
    }

    public function render()
    {
        return view('livewire.retur-barang.retur-barang-page');
    }

    private function showError($message)
    {
        return $this->alert('error', $message, [
            'showConfirmButton' => false,
            'timer' => 3000,
            'position' => 'center',
            'toast' => true
        ]);
    }

    private function showSuccess($message)
    {
        return $this->alert('success', $message, [
            'showConfirmButton' => false,
            'timer' => 2000,
            'position' => 'center',
            'toast' => false
        ]);
    }
}
